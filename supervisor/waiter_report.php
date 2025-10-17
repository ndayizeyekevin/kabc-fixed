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
$sql = "SELECT *,concat(f_name, ' ', l_name) as server, tbl_cmd_qty.created_at FROM `tbl_cmd_qty`
                        		INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                INNER JOIN category ON menu.cat_id=category.cat_id
                        		INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                INNER JOIN tbl_users ON tbl_users.user_id = tbl_cmd_qty.Serv_id
$whereClause
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
<form method="get">
    <?php
    foreach ($_GET as $key => $value) {
        if (!in_array($key, ['date_from', 'date_too', 'month'])) {
            echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
        }
    }
    ?>

    <label>From: <input type="date" class="form-control" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>"></label>
    <label>To: <input type="date" class="form-control" name="date_too" value="<?= $_GET['date_too'] ?? '' ?>"></label>

    <label>Or Month:
        <select name="month" class="form-control">
            <option value="">-- Select Month --</option>
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $value = str_pad($m, 2, '0', STR_PAD_LEFT);
                $selected = ($_GET['month'] ?? '') == $value ? 'selected' : '';
                echo "<option value='$value' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
            }
            ?>
        </select>
    </label>

    <button type="submit">Filter</button>
</form>
<br>

<button id="printH"> Print </button>

<div class="text-nowrap table-responsive">
    <div id="content">  
        <?php include '../holder/printHeader.php' ?>
        <?php foreach ($grouped as $serverName => $orders): ?>
            <h2>Waiter Report for <?= htmlspecialchars($serverName) ?></h2>
            <table border="1" cellpadding="5" class="table table-striped">
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Client</th>
                        <th>Menu Item</th>
                        <th>Price</th>
                        <th>Date Served</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; foreach ($orders as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['cmd_code']) ?></td>
                            <td><?= htmlspecialchars($row['client']?? '-') ?></td>
                            <td><?= htmlspecialchars($row['menu_name']) ?></td>
                            <td><?= number_format($row['menu_price'], 2) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                        <?php $total += $row['menu_price']; endforeach; ?>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td></td>
                            <td></td>
                            <td><strong><?= number_format($total, 2) ?></strong></td>
                            <td></td>
                        </tr>
                </tbody>
            </table>
            <br><br>
        <?php endforeach; ?>
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
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
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
</script>
