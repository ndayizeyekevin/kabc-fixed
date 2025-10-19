<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// **ASSUMPTIONS:**
// 1. $db is a valid PDO connection object.
// 2. $_SESSION['user_id'] is set and holds the current user's ID.
// 3. The column names are: user_id=Serv_Id, menu item=cmd_item, quantity=cmd_qty.

// Database connection ($db) and session ($_SESSION) must be initialized before this script.

$user_IId = $_SESSION['user_id'];
echo'<div class="container">';

$where = [];
$params = [];
$date_today = date('Y-m-d');

// --- 1. Date Range Filter (Mandatory) ---
$date_from = $_GET['date_from'] ?? $date_today;
$date_too  = $_GET['date_too']  ?? $date_today;
$where[] = "DATE(tbl_cmd_qty.created_at) >= :date_from AND DATE(tbl_cmd_qty.created_at) <= :date_too";
$params[':date_from'] = $date_from;
$params[':date_too']  = $date_too;

// --- 2. Filter by Current Logged-in User ---
$where[] = "tbl_cmd_qty.Serv_Id = :user_id";
$params[':user_id'] = $user_IId;

// --- 3. Optional Month Filter ---
if (!empty($_GET['month'])) {
    $where[] = "MONTH(tbl_cmd_qty.created_at) = :month";
    $params[':month'] = $_GET['month'];
}

// Combine WHERE conditions
$whereClause = $where ? "WHERE " . implode(' AND ', $where) : "";

// --- 4. Final SQL Query (SYNTAX & DISTINCT FIXED) ---
// NOTE: DISTINCT is added to eliminate duplicates resulting from improper joins or bad data.
$sql = "SELECT DISTINCT
            tbl_cmd_qty.*, 
            menu.menu_price,
            menu.menu_name,
            tbl_cmd.client,
            tbl_cmd.OrderCode AS cmd_code, -- Use alias to ensure OrderCode is present
            concat(tbl_users.f_name, ' ', tbl_users.l_name) as server,
            tbl_cmd_qty.cmd_qty, 
            tbl_cmd_qty.created_at
        FROM `tbl_cmd_qty`
        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
        INNER JOIN category ON menu.cat_id = category.cat_id
        INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
        INNER JOIN tbl_cmd ON tbl_cmd.OrderCode = tbl_cmd_qty.cmd_code
        INNER JOIN tbl_users ON tbl_users.user_id = :user_id 
        $whereClause
        ORDER BY tbl_cmd_qty.created_at DESC";

// --- 5. Execute Query ---
try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage()); 
}

// Get the server name from the first result (or default if no orders found)
$serverName = $results[0]['server'] ?? 'Current User';
?>

<form method="get" class="mb-3">
    <?php
    // Preserve other GET parameters except the filter ones
    foreach ($_GET as $key => $value) {
        if (!in_array($key, ['date_from', 'date_too', 'month'])) {
            echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
        }
    }
    ?>

    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="date_from" class="form-label">From:</label>
            <input type="date" class="form-control" name="date_from" id="date_from" value="<?= htmlspecialchars($date_from) ?>">
        </div>
        <div class="col-md-3">
            <label for="date_too" class="form-label">To:</label>
            <input type="date" class="form-control" name="date_too" id="date_too" value="<?= htmlspecialchars($date_too) ?>">
        </div>
        <div class="col-md-3">
            <label for="month_select" class="form-label">Or Month:</label>
            <select name="month" id="month_select" class="form-control">
                <option value="">-- Select Month --</option>
                <?php
                $current_month = $_GET['month'] ?? '';
                for ($m = 1; $m <= 12; $m++) {
                    $value = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $selected = $current_month == $value ? 'selected' : '';
                    echo "<option value='$value' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>
</form>

<br>

<button id="printH" class="btn btn-secondary mb-3">Print Report</button>

<div class="text-nowrap table-responsive">
    <div id="content">
        <?php include '../holder/printHeader.php'; ?>
        
        <h2 class="mt-4">Waiter Report for <?= htmlspecialchars($serverName) ?></h2>
        
        <?php if (empty($results)): ?>
            <div class="alert alert-info">No orders found for your account in the selected date range.</div>
        <?php else: ?>
            <table border="1" cellpadding="5" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Client</th>
                        <th>Menu Item</th>
                        <th>Qty</th> 
                        <th>Unit Price</th>
                        <th>Subtotal</th> 
                        <th>Date Served</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grand_total = 0; foreach ($results as $row): 
                        // The columns are now correct based on the DISTINCT query
                        $quantity = $row['cmd_qty'];
                        $unit_price = $row['menu_price'];
                        $subtotal = $quantity * $unit_price;
                        $grand_total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['cmd_code']) ?></td>
                            <td><?= htmlspecialchars($row['client'] ?? "-") ?></td>
                            <td><?= htmlspecialchars($row['menu_name']) ?></td>
                            <td class="text-end"><?= htmlspecialchars($quantity) ?></td>
                            <td class="text-end"><?= number_format($unit_price, 2) ?></td>
                            <td class="text-end"><strong><?= number_format($subtotal, 2) ?></strong></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                        <tr class="table-info">
                            <td colspan="5"><strong>Total Sales</strong></td>
                            <td class="text-end"><strong><?= number_format($grand_total, 2) ?></strong></td>
                            <td></td>
                        </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <?php include '../holder/printFooter.php'; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#printH").click(function(){
            var printContents = document.getElementById('content').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });
    });
</script>