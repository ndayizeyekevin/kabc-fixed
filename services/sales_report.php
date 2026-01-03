<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_IId = $_SESSION['user_id'];
echo'<div class="container">';

// Fetch ALL reports for the logged-in waiter only
$sql = "SELECT *, concat(f_name, ' ', l_name) as server, tbl_cmd_qty.created_at, tbl_cmd_qty.cmd_status,
        tbl_tables.table_no, tbl_cmd_qty.cmd_qty, menu.menu_price
        FROM `tbl_cmd_qty`
        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
        INNER JOIN category ON menu.cat_id = category.cat_id
        INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
        INNER JOIN tbl_users ON tbl_users.user_id = tbl_cmd_qty.Serv_id
        WHERE tbl_cmd_qty.Serv_id = :user_id
        ORDER BY tbl_cmd_qty.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute([':user_id' => $user_IId]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by order code, then by menu item (to combine quantities)
$grouped = [];
foreach ($results as $row) {
    $server = $row['server'];
    $orderCode = $row['cmd_code'];
    $menuId = $row['cmd_item'];

    if (!isset($grouped[$orderCode])) {
        $grouped[$orderCode] = [
            'server' => $server,
            'table_no' => $row['table_no'],
            'client' => $row['client'] ?? '-',
            'created_at' => $row['created_at'],
            'items' => []
        ];
    }

    // Group items by menu_id to combine quantities
    if (!isset($grouped[$orderCode]['items'][$menuId])) {
        $grouped[$orderCode]['items'][$menuId] = [
            'menu_name' => $row['menu_name'],
            'menu_price' => $row['menu_price'],
            'quantity' => 0,
            'created_at' => $row['created_at']
        ];
    }

    $grouped[$orderCode]['items'][$menuId]['quantity'] += $row['cmd_qty'];
}
?>

<!-- JavaScript Date Filter -->
<div style="margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
    <label style="margin-right: 15px;">
        <strong>From:</strong>
        <input type="date" id="dateFrom" class="form-control" style="display: inline-block; width: auto;" value="<?= date('Y-m-d') ?>">
    </label>
    <label style="margin-right: 15px;">
        <strong>To:</strong>
        <input type="date" id="dateTo" class="form-control" style="display: inline-block; width: auto;" value="<?= date('Y-m-d') ?>">
    </label>
    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
    <button type="button" id="resetBtn" class="btn btn-secondary">Show All</button>
    <span id="filterStatus" style="margin-left: 15px; font-weight: bold; color: #28a745;"></span>
</div>
<br>

<button id="printH" class="btn btn-success"> Print </button>

<div class="text-nowrap table-responsive">
    <div id="content">
        <?php include '../holder/printHeader.php' ?>

        <?php if (!empty($grouped)): ?>
            <?php
            // Get server name from first order
            $firstOrder = reset($grouped);
            $serverName = $firstOrder['server'];
            ?>
            <div class="waiter-section">
                <h2 style="text-align: center; color: #333; margin-bottom: 20px;">
                    Sales Report for <?= htmlspecialchars($serverName) ?>
                </h2>

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
                        $grandTotal = 0;
                        foreach ($grouped as $orderCode => $orderData):
                            $orderDate = date('Y-m-d', strtotime($orderData['created_at']));
                            $itemCount = count($orderData['items']);
                            $firstItem = true;
                            $orderTotal = 0;
                        ?>
                            <?php foreach ($orderData['items'] as $menuId => $item):
                                $itemTotal = $item['menu_price'] * $item['quantity'];
                                $orderTotal += $itemTotal;
                            ?>
                                <tr class="order-row" data-order-code="<?= htmlspecialchars($orderCode) ?>" data-date="<?= $orderDate ?>">
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
                                    <td style="text-align: center;"><strong>x<?= $item['quantity'] ?></strong></td>
                                    <td style="text-align: right;"><?= number_format($item['menu_price'], 2) ?></td>
                                    <td style="text-align: right;" class="item-price"><?= number_format($itemTotal, 2) ?></td>
                                    <td><?= htmlspecialchars($item['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr class="order-total-row" data-order-code="<?= htmlspecialchars($orderCode) ?>" data-date="<?= $orderDate ?>" data-order-total="<?= $orderTotal ?>" style="background-color: #e9ecef;">
                                <td colspan="4" style="text-align: right;"><strong>Order Total:</strong></td>
                                <td colspan="2" style="text-align: right;"><strong><?= number_format($orderTotal, 2) ?> RWF</strong></td>
                                <td></td>
                            </tr>
                            <?php $grandTotal += $orderTotal; ?>
                        <?php endforeach; ?>

                        <tr class="grand-total-row" style="background-color: #28a745; color: white; font-size: 16px;">
                            <td colspan="6" style="text-align: right; padding: 12px;"><strong>GRAND TOTAL:</strong></td>
                            <td colspan="2" class="grand-total-value" style="text-align: right; padding: 12px;">
                                <strong><?= number_format($grandTotal, 2) ?> RWF</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="text-align: center; padding: 30px; font-size: 18px;">
                <strong>No sales records found for your account.</strong>
            </div>
        <?php endif; ?>

        <?php include '../holder/printFooter.php' ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function () {
        // Apply today's filter by default on page load
        filterReportsByDate();

        // Filter button click
        $("#filterBtn").click(function() {
            filterReportsByDate();
        });

        // Reset button click
        $("#resetBtn").click(function() {
            // Show all rows
            $(".order-row, .order-total-row").show();
            $(".waiter-section").show();

            // Recalculate grand total
            recalculateGrandTotal();

            $("#filterStatus").text("Showing all records").css("color", "#007bff");
        });

        // Print functionality
        $("#printH").click(function(){
            $("#headerprint").show();
            var printContents = document.getElementById('content').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });

        $("#headerprint").hide();
    });

    function filterReportsByDate() {
        var dateFrom = $("#dateFrom").val();
        var dateTo = $("#dateTo").val();

        if (!dateFrom || !dateTo) {
            alert("Please select both From and To dates");
            return;
        }

        var fromDate = new Date(dateFrom);
        var toDate = new Date(dateTo);

        if (fromDate > toDate) {
            alert("From date cannot be greater than To date");
            return;
        }

        var totalOrdersShown = 0;

        // Get all unique order codes
        var orderCodes = [];
        $(".order-row").each(function() {
            var orderCode = $(this).data("order-code");
            if (orderCodes.indexOf(orderCode) === -1) {
                orderCodes.push(orderCode);
            }
        });

        // Filter each order
        orderCodes.forEach(function(orderCode) {
            var orderRows = $(".order-row[data-order-code='" + orderCode + "']");
            var orderTotalRow = $(".order-total-row[data-order-code='" + orderCode + "']");

            if (orderRows.length > 0) {
                var orderDate = new Date(orderRows.first().data("date"));

                // Check if order date is within range
                if (orderDate >= fromDate && orderDate <= toDate) {
                    orderRows.show();
                    orderTotalRow.show();
                    totalOrdersShown++;
                } else {
                    orderRows.hide();
                    orderTotalRow.hide();
                }
            }
        });

        // Recalculate grand total
        recalculateGrandTotal();

        // Update filter status
        var statusText = "Showing records from " + dateFrom + " to " + dateTo + " (" + totalOrdersShown + " orders)";
        $("#filterStatus").text(statusText).css("color", "#28a745");
    }

    function recalculateGrandTotal() {
        var grandTotal = 0;

        // Sum up all visible order totals
        $(".order-total-row:visible").each(function() {
            var orderTotal = parseFloat($(this).data("order-total")) || 0;
            grandTotal += orderTotal;
        });

        // Update the grand total display
        var grandTotalCell = $(".grand-total-value");
        grandTotalCell.html("<strong>" + formatNumber(grandTotal) + " RWF</strong>");
    }

    function formatNumber(num) {
        return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function printInvoice() {
        var printContents = document.getElementById('content').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<style>
    .table {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .table thead th {
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }

    .table tbody td {
        vertical-align: middle;
    }

    @media print {
        .btn, #dateFrom, #dateTo, #filterBtn, #resetBtn, #filterStatus, label {
            display: none !important;
        }

        .table {
            page-break-inside: auto;
        }

        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
