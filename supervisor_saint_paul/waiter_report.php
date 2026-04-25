<?php
error_reporting(0);
echo '<div class="container">';
$where = [];
$params = [];

// Determine if user provided a date range
$date_from = $_GET['date_from'] ?? date('Y-m-d');
$date_too  = $_GET['date_too'] ?? date('Y-m-d');
$hasRange = (isset($_GET['date_from']) || isset($_GET['date_too']));

// Normalize range if present
if ($hasRange) {
    $date_from = $date_from !== '' ? $date_from : $date_too;
    $date_too  = $date_too  !== '' ? $date_too  : $date_from;
}
// Always include users with specific roles
$where[] = "tbl_users.role_id IN (6,5,10,12)";

// Optional month filter (applied to item timestamp)
if (!empty($_GET['month'])) {
    $where[] = "MONTH(tbl_cmd_qty.created_at) = :month";
    $params[':month'] = $_GET['month'];
}

// Build SQL depending on whether a date range is provided
if ($hasRange) {
    // Get business days that match the date range - use DATE() to compare just the date part
    $sqlgetday = $db->prepare("SELECT opened_at, closed_at  
                         FROM days 
                         WHERE DATE(opened_at) BETWEEN :date_from AND :date_too 
                         ORDER BY opened_at");
    $sqlgetday->execute([
        ':date_from' => $date_from,
        ':date_too' => $date_too
    ]);
    $days = $sqlgetday->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($days) > 0) {
        // Build conditions for each day in the range
        $dayConditions = [];
        $dayIndex = 0;
        foreach ($days as $day) {
            $dayConditions[] = "(d.opened_at <= :toEnd$dayIndex AND COALESCE(d.closed_at, NOW()) >= :fromStart$dayIndex)";
            $params[':fromStart' . $dayIndex] = $day['opened_at'];
            $params[':toEnd' . $dayIndex] = $day['closed_at'] ?? date('Y-m-d H:i:s');
            $dayIndex++;
        }
        $where[] = "(" . implode(' OR ', $dayConditions) . ")";
    } else {
        // No business days found for the selected date range - return empty result
        $where[] = "1 = 0";
    }

    $whereClause = $where ? "WHERE " . implode(' AND ', $where) : "";

    $sql = "SELECT tbl_vsdc_sales.has_refund, tbl_vsdc_sales.transaction_id, tbl_cmd_qty.cmd_code, 
            concat(f_name, ' ', l_name) as server, tbl_cmd_qty.created_at, tbl_tables.table_no,
            tbl_cmd_qty.cmd_qty, menu.menu_price, menu.menu_name, tbl_cmd_qty.cmd_item, tbl_cmd_qty.Serv_id, 
            tbl_cmd.client, tbl_cmd_qty.cmd_table_id
            FROM `tbl_cmd_qty`
            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
            INNER JOIN category ON menu.cat_id=category.cat_id
            INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
            INNER JOIN tbl_users ON tbl_users.user_id = tbl_cmd_qty.Serv_id
            LEFT JOIN tbl_cmd ON tbl_cmd.OrderCode = tbl_cmd_qty.cmd_code
            JOIN days d
              ON tbl_cmd_qty.created_at >= d.opened_at
             AND tbl_cmd_qty.created_at <  COALESCE(d.closed_at, NOW())
            lEFT JOIN tbl_vsdc_sales on tbl_cmd_qty.cmd_code = tbl_vsdc_sales.transaction_id
            $whereClause
            AND (tbl_vsdc_sales.has_refund IS NULL OR tbl_vsdc_sales.has_refund != '1')
            GROUP BY tbl_cmd_qty.cmd_qty_id
            ORDER BY serv_id, tbl_cmd_qty.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
} else {
    // Default: use the latest business-day window
    $qDay = $db->prepare("SELECT opened_at, COALESCE(closed_at, NOW()) AS closed_at FROM days ORDER BY opened_at DESC LIMIT 1");
    $qDay->execute();
    $currentDay = $qDay->fetch(PDO::FETCH_ASSOC);

    if ($currentDay && $currentDay['opened_at']) {
        $params[':opened'] = $currentDay['opened_at'];
        $params[':closed'] = $currentDay['closed_at'];

        $whereClause = $where ? "WHERE " . implode(' AND ', $where) : "";

        $sql = "SELECT *, concat(f_name, ' ', l_name) as server, tbl_cmd_qty.created_at, tbl_tables.table_no,
                tbl_cmd_qty.cmd_qty, menu.menu_price FROM `tbl_cmd_qty`
                INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                INNER JOIN category ON menu.cat_id=category.cat_id
                INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                INNER JOIN tbl_users ON tbl_users.user_id = tbl_cmd_qty.Serv_id
                LEFT JOIN tbl_vsdc_sales on tbl_cmd_qty.cmd_code = tbl_vsdc_sales.transaction_id
                $whereClause
                AND (tbl_vsdc_sales.has_refund IS NULL OR tbl_vsdc_sales.has_refund != '1')
                AND tbl_cmd_qty.created_at >= :opened AND tbl_cmd_qty.created_at < :closed
                ORDER BY serv_id, tbl_cmd_qty.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    } else {
        // Fallback: no days configured, return empty set for clarity (or remove filter to show all)
        $whereClause = $where ? "WHERE " . implode(' AND ', $where) : "";
        $sql = "SELECT *, concat(f_name, ' ', l_name) as server, tbl_cmd_qty.created_at, tbl_tables.table_no,
                tbl_cmd_qty.cmd_qty, menu.menu_price FROM `tbl_cmd_qty`
                INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                INNER JOIN category ON menu.cat_id=category.cat_id
                INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                INNER JOIN tbl_users ON tbl_users.user_id = tbl_cmd_qty.Serv_id
                LEFT JOIN tbl_vsdc_sales on tbl_cmd_qty.cmd_code = tbl_vsdc_sales.transaction_id
                $whereClause
                AND (tbl_vsdc_sales.has_refund IS NULL OR tbl_vsdc_sales.has_refund != '1')
                ORDER BY serv_id, tbl_cmd_qty.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by server, then by order code, then by menu item (to combine quantities)
$grouped = [];
foreach ($results as $row) {
    // Skip refunded orders
    // if (isset($row['has_refund']) && $row['has_refund'] == '1') {
    //     continue;
    // }
    
    $server = $row['server'];
    $orderCode = $row['cmd_code'];
    $menuId = $row['cmd_item'];

    if (!isset($grouped[$server])) {
        $grouped[$server] = [];
    }
    if (!isset($grouped[$server][$orderCode])) {
        $grouped[$server][$orderCode] = [
            'table_no' => $row['table_no'],
            'client' => $row['client'] ?? '-',
            'created_at' => $row['created_at'],
            'items' => []
        ];
    }

    // Group items by menu_id to combine quantities
    if (!isset($grouped[$server][$orderCode]['items'][$menuId])) {
        $grouped[$server][$orderCode]['items'][$menuId] = [
            'menu_name' => $row['menu_name'],
            'menu_price' => $row['menu_price'],
            'quantity' => 0,
            'created_at' => $row['created_at']
        ];
    }

    $grouped[$server][$orderCode]['items'][$menuId]['quantity'] += $row['cmd_qty'];
}

// Sort waiters by Total Sales (highest first)
uksort($grouped, function($a, $b) use ($grouped) {
    $totalA = 0;
    $totalB = 0;
    
    // Calculate total for waiter A
    foreach ($grouped[$a] as $orderCode => $orderData) {
        foreach ($orderData['items'] as $menuId => $item) {
            $totalA += ($item['menu_price'] * $item['quantity']);
        }
    }
    
    // Calculate total for waiter B
    foreach ($grouped[$b] as $orderCode => $orderData) {
        foreach ($orderData['items'] as $menuId => $item) {
            $totalB += ($item['menu_price'] * $item['quantity']);
        }
    }
    
    // Sort descending (highest sales first)
    return $totalB - $totalA;
});
?>
<form method="get" style="margin-bottom: 20px;">
    <?php
    foreach ($_GET as $key => $value) {
        if (!in_array($key, ['date_from', 'date_too', 'month'])) {
            echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
        }
    }
    ?>

    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <label style="display: flex; flex-direction: column;">
            <strong>From Date:</strong>
            <input type="date" class="form-control" name="date_from" value="<?= $_GET['date_from'] ?? $date_from ?>" style="margin-top: 5px;">
        </label>
        <label style="display: flex; flex-direction: column;">
            <strong>To Date:</strong>
            <input type="date" class="form-control" name="date_too" value="<?= $_GET['date_too'] ?? $date_too ?>" style="margin-top: 5px;">
        </label>
        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Apply Filter</button>
    </div>
</form>
<br>

<button id="printH"> Print </button>

<div class="text-nowrap table-responsive">
    <div id="content">
        <?php include '../holder/printHeader.php' ?>

        <!-- Waiter List -->
        <h2>Waiter Reports</h2>
        <div class="waiter-list">
            <?php
            $waiterIndex = 0;
            foreach ($grouped as $serverName => $orderGroups):
                $waiterIndex++;
                $waiterId = 'waiter-' . $waiterIndex;

                // Calculate grand total for this waiter
                $waiterGrandTotal = 0;
                foreach ($orderGroups as $orderCode => $orderData) {
                    foreach ($orderData['items'] as $menuId => $item) {
                        $waiterGrandTotal += ($item['menu_price'] * $item['quantity']);
                    }
                }
            ?>
                <div class="waiter-item" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <div class="waiter-header" onclick="toggleWaiterReport('<?= $waiterId ?>')"
                        style="padding: 15px; background-color: #f8f9fa; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 18px;"><?= htmlspecialchars($serverName) ?></strong>
                            <span style="margin-left: 20px; color: #666;">Total Sales: <?= number_format($waiterGrandTotal, 2) ?> RWF</span>
                        </div>
                        <span class="toggle-icon" id="icon-<?= $waiterId ?>" style="font-size: 20px;">▼</span>
                    </div>

                    <div class="waiter-details" id="<?= $waiterId ?>" style="display: none; padding: 15px;">
                        <h3 style="color: #333;">Report for <?= htmlspecialchars($serverName) ?></h3>
                        <table border="1" cellpadding="8" class="table table-striped table-bordered">
                            <thead style="background-color: #007bff; color: white;">
                                <tr>
                                    <th>Order Code</th>
                                    <th>Table</th>
                                    <th>Client</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                    <th>Date Served</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                foreach ($orderGroups as $orderCode => $orderData):
                                    $itemCount = count($orderData['items']);
                                    $firstItem = true;
                                    $orderTotal = 0;
                                ?>
                                    <?php foreach ($orderData['items'] as $menuId => $item):
                                        $itemTotal = $item['menu_price'] * $item['quantity'];
                                        $orderTotal += $itemTotal;
                                    ?>
                                        <tr>
                                            <?php if ($firstItem): ?>
                                                <td rowspan="<?= $itemCount + 1 ?>" style="vertical-align: middle; font-weight: bold;">
                                                    <?= htmlspecialchars($orderCode) ?>
                                                </td>
                                                <td rowspan="<?= $itemCount + 1 ?>" style="vertical-align: middle;">
                                                    <?= htmlspecialchars($orderData['table_no']) ?>
                                                </td>
                                                <td rowspan="<?= $itemCount + 1 ?>" style="vertical-align: middle;">
                                                    <?= htmlspecialchars($orderData['client']) ?>
                                                </td>
                                                <?php $firstItem = false; ?>
                                            <?php endif; ?>
                                            <td><?= htmlspecialchars($item['menu_name']) ?></td>
                                            <td style="text-align: center;"><strong> <?= $item['quantity'] ?></strong></td>
                                            <td style="text-align: right;"><?= number_format($item['menu_price'], 2) ?></td>
                                            <td style="text-align: right;"><?= number_format($itemTotal, 2) ?></td>
                                            <td><?= htmlspecialchars($item['created_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <tr style="background-color: #e9ecef;">
                                        <td colspan="4" style="text-align: right;"><strong>Order Total:</strong></td>
                                        <td colspan="3" style="text-align: right;"><strong><?= number_format($orderTotal, 2) ?> RWF</strong></td>
                                        <td></td>
                                    </tr>
                                    <?php $total += $orderTotal; ?>
                                <?php endforeach; ?>

                                <tr style="background-color: #28a745; color: white; font-size: 16px;">
                                    <td colspan="6" style="text-align: right; padding: 12px;"><strong>GRAND TOTAL:</strong></td>
                                    <td colspan="2" style="text-align: right; padding: 12px;">
                                        <strong><?= number_format($total, 2) ?> RWF</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php include '../holder/printFooter.php' ?>
    </div>
</div>

<script>
    function printInvoice() {
        var printContents = document.getElementById('content').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function toggleWaiterReport(waiterId) {
        // Get all waiter details divs
        var allDetails = document.querySelectorAll('.waiter-details');
        var allIcons = document.querySelectorAll('.toggle-icon');

        // Hide all waiter details and reset icons
        allDetails.forEach(function(detail) {
            if (detail.id !== waiterId) {
                detail.style.display = 'none';
            }
        });

        allIcons.forEach(function(icon) {
            if (icon.id !== 'icon-' + waiterId) {
                icon.textContent = '▼';
            }
        });

        // Toggle the clicked waiter's details
        var targetDetail = document.getElementById(waiterId);
        var targetIcon = document.getElementById('icon-' + waiterId);

        if (targetDetail.style.display === 'none' || targetDetail.style.display === '') {
            targetDetail.style.display = 'block';
            targetIcon.textContent = '▲';
        } else {
            targetDetail.style.display = 'none';
            targetIcon.textContent = '▼';
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#printH").click(function() {
            $("#headerprint").show();

            // Temporarily show all waiter details for printing
            $('.waiter-details').show();

            var printContents = document.getElementById('content').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

            // Restore the page after printing
            location.reload();
        });

        $("#headerprint").hide();
    });
</script>

<style>
    .table {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }

    .table tbody td {
        vertical-align: middle;
    }

    .waiter-header:hover {
        background-color: #e9ecef !important;
    }

    @media print {
        .waiter-header {
            page-break-after: avoid;
        }

        .waiter-details {
            page-break-inside: avoid;
        }

        .table {
            page-break-inside: auto;
        }

        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        button,
        form {
            display: none !important;
        }
    }
</style>