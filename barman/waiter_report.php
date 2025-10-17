<?php
error_reporting(0);
echo'<div class="container">';
$where = [];
$params = [];

// Use full date range filter
$date_from = $_GET['date_from'] ?? date('Y-m-d');
$date_too  = $_GET['date_too']  ?? date('Y-m-d');

$where[] = "DATE(tbl_cmd_qty.created_at) >= :date_from AND DATE(tbl_cmd_qty.created_at) <= :date_too";
$params[':date_from'] = $date_from;
$params[':date_too']  = $date_too;

// Always include users with log_role = '6'
$where[] = "tbl_users.role_id IN (6,5,10,12)";

// If month filter is set
if (!empty($_GET['month'])) {
    $where[] = "MONTH(tbl_cmd_qty.created_at) = :month";
    $params[':month'] = $_GET['month'];
}

// Combine WHERE conditions
$whereClause = $where ? "WHERE " . implode(' AND ', $where) : "";

// Final SQL

// Only include drinks or coffee categories

// Only include drinks (cat_id = 2)
$sql = "SELECT *,concat(f_name, ' ', l_name) as server, tbl_cmd_qty.created_at FROM `tbl_cmd_qty`
                         		INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                INNER JOIN category ON menu.cat_id=category.cat_id
                         		INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                INNER JOIN tbl_users ON tbl_users.user_id = tbl_cmd_qty.Serv_id
" . $whereClause . " AND menu.cat_id='2'
ORDER BY serv_id, tbl_cmd_qty.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by server
$grouped = [];
foreach ($results as $row) {
    $server = $row['server'];
    $grouped[$server][] = $row;
}
?>
<style>
    .date-selection {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-bottom: 10px;
    }
    .date-selection label {
        font-weight: bold;
        margin-right: 5px;
    }
    .date-selection input[type="date"] {
        border: 1px solid #007bff;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 15px;
        color: #333;
        background: #f8f9fa;
        transition: border-color 0.2s;
    }
    .date-selection input[type="date"]:focus {
        border-color: #0056b3;
        outline: none;
    }
    .date-selection button[type="submit"] {
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 6px 16px;
        font-size: 15px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .date-selection button[type="submit"]:hover {
        background: #0056b3;
    }
</style>
<form method="get" class="date-selection">
    <?php
    foreach ($_GET as $key => $value) {
        if (!in_array($key, ['date_from', 'date_too', 'month'])) {
            echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
        }
    }
    ?>

    <label>From: <input type="date" class="form-control" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>"></label>
    <label>To: <input type="date" class="form-control" name="date_too" value="<?= $_GET['date_too'] ?? '' ?>"></label>
    <button type="submit">Filter</button>
</form>
<br>


<button id="printWaiter" class="btn btn-info"> <i class="fa fa-print"></i> Print Report</button>


<div class="text-nowrap table-responsive">
    <div id="waiter-print-content">  
        <div class="printHeader" style="display:none;">
            <center>
                <img src='<?= $logo_png; ?>' style='max-height:80px;'>
                <div>
                    <?= $company_name ?><br>
                    <?= $company_address ?><br>
                    TIN/VAT Number: <?= $company_tin ?><br>
                    <br>
                    Phone: <?= $company_phone ?><br>
                </div>
                <h2 style="margin-top:20px;">Waiters Report</h2>
                <div style="font-size:18px; margin-bottom:10px;">
                    Date Range: <strong><?php echo htmlspecialchars($date_from); ?></strong> to <strong><?php echo htmlspecialchars($date_too); ?></strong><br>
                    Printed at: <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
                </div>
            </center>
            <br>
        </div>
        <?php foreach ($grouped as $serverName => $orders): ?>
            <h2 style="text-align:center;">Waiter Report for <?= htmlspecialchars($serverName) ?></h2>
            <table border="1" cellpadding="5" class="table table-striped" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Code</th>
                        <th>Client</th>
                        <th>Menu Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Date Served</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; $totalQty = 0; $idx=1; foreach ($orders as $row): ?>
                        <?php $rowTotal = $row['menu_price'] * $row['cmd_qty']; ?>
                        <tr>
                            <td><?= $idx++ ?></td>
                            <td><?= htmlspecialchars($row['cmd_code']) ?></td>
                            <td><?= htmlspecialchars($row['client']?? '-') ?></td>
                            <td><?= htmlspecialchars($row['menu_name']) ?></td>
                            <td><?= number_format($row['menu_price'], 2) ?></td>
                            <td><?= $row['cmd_qty'] ?></td>
                            <td><?= number_format($rowTotal, 2) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                        <?php $total += $rowTotal; $totalQty += $row['cmd_qty']; endforeach; ?>
                        <tr>
                            <td colspan="5"><strong>Total</strong></td>
                            <td><strong><?= $totalQty ?></strong></td>
                            <td><strong><?= number_format($total, 2) ?></strong></td>
                            <td></td>
                        </tr>
                </tbody>
            </table>
            <br><br>
        <?php endforeach; ?>
        <!-- Single signature section at the bottom -->
        <div class="signature-section" style="margin-top:40px;">
            <div class="signature-row" style="display:flex;justify-content:space-between;gap:20px;">
                <div class="signature-box" style="width:32%;text-align:left;">
                    <strong>Printed by:</strong><br>
                    <br>
                    <div class="sig-line" style="margin-top:50px;border-top:1px solid #000;height:0;"></div>
                    <small>Name & Signature</small>
                </div>
                <div class="signature-box" style="width:32%;text-align:center;">
                    <strong>Received by:</strong><br><br>
                    <div class="sig-line" style="margin-top:50px;border-top:1px solid #000;height:0;"></div>
                    <small>Name & Signature</small>
                </div>
                <div class="signature-box" style="width:32%;text-align:right;">
                    <strong>Approved by:</strong><br><br>
                    <div class="sig-line" style="margin-top:50px;border-top:1px solid #000;height:0;"></div>
                    <small>Name & Signature</small>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
@media print {
    body * { visibility: hidden; }
    #waiter-print-content, #waiter-print-content * { visibility: visible; }
    #waiter-print-content { position: absolute; left: 0; top: 0; width: 100%; }
    .btn, .no-print { display: none !important; }
    table, td, th { border: 1px solid black !important; font-size: 18px !important; }
    h2 { text-align: center; }
    .signature-section { display: block !important; margin-top: 40px; }
    .signature-row { display: flex; justify-content: space-between; gap: 20px; }
    .signature-box { width: 32%; text-align: center; }
    .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
    .printHeader { display: block !important; visibility: visible !important; }
}
</style>

<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#printWaiter").click(function(){
            window.print();
        });
    });
</script>
