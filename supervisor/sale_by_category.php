<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once ("../inc/config.php");

$from = isset($_SESSION['date_from']) ? $_SESSION['date_from'] : date('Y-m-d');
$to = isset($_SESSION['date_to']) ? $_SESSION['date_to'] : date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['date_from'], $_POST['date_to'])) {
        $_SESSION['date_from'] = $from = $_POST['date_from'];
        $_SESSION['date_to'] = $to = $_POST['date_to'];
    }
}

$data = [];
$totalAmount = 0;
$totalQuantity = 0;

try {
    $db->query('SET SESSION SQL_BIG_SELECTS=1');
    $db->exec("SET SESSION sql_mode=''");

    $type = $_REQUEST['type'];
    $cat_id_condition = "= '" . $type . "'";

    $sql = $db->prepare("SELECT menu.menu_name, menu.menu_desc, menu.menu_price, SUM(cmd_qty) AS totqty
        FROM `tbl_cmd_qty`
        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
        WHERE DATE(tbl_cmd_qty.created_at) BETWEEN :from AND :to 
        AND cmd_status = '12' 
        AND menu.cat_id $cat_id_condition
        GROUP BY cmd_item
        ORDER BY totqty DESC");

    $sql->execute(['from' => $from, 'to' => $to]);

    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $menu_price = floatval(str_replace(',', '', $row['menu_price']));
        $qty = floatval($row['totqty']);
        $amount = $menu_price * $qty;

        $data[] = [
            'menu_name' => $row['menu_name'],
            'menu_desc' => $row['menu_desc'],
            'menu_price' => $menu_price,
            'totqty' => $qty,
            'amount' => $amount,
        ];

        $totalAmount += $amount;
        $totalQuantity += $qty;
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $data = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales by Category Report</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .table-responsive { 
            margin-top: 20px; 
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: #fff;
        }
        .card-modern {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            background: #fff;
        }
        .table thead th {
            background: #343a40;
            color: white;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card card-modern p-4">
                <div class="card-header bg-transparent border-0">
                    <h3 class="text-center mb-0">Sales by Category Report</h3>
                    <p class="text-center text-muted mt-2">
                        <?php echo date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)); ?>
                    </p>
                </div>

                <div class="card-body">
                    <form action="" method="POST" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($from); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($to); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" onclick="printTable()" class="btn btn-secondary d-block w-100">Print</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive" id="print-area">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Qty Sold</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['menu_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['menu_desc']); ?></td>
                                        <td>RWF <?php echo number_format($item['menu_price'], 0); ?></td>
                                        <td><?php echo number_format($item['totqty'], 0); ?></td>
                                        <td>RWF <?php echo number_format($item['amount'], 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">TOTAL:</th>
                                    <th><?php echo number_format($totalQuantity, 0); ?></th>
                                    <th>RWF <?php echo number_format($totalAmount, 0); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
    function printTable() {
        var printContents = document.getElementById('print-area');
        if (!printContents) {
            window.print();
            return;
        }
        var w = window.open('', '', 'height=900,width=1200');
        w.document.write('<html><head><title>Sales by Category Report</title>');
        w.document.write('<link rel="stylesheet" href="../css/bootstrap.min.css">');
        w.document.write('<style> body{padding:20px;} table{width:100%;} th,td{font-size:12px;} </style>');
        w.document.write('</head><body>');
        // Include header title and date range for context
        w.document.write('<h3 style="text-align:center;margin-bottom:0;">Sales by Category Report</h3>');
        w.document.write('<p style="text-align:center;color:#6c757d;margin-top:4px;">' +
            <?php echo json_encode(date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to))); ?> +
        '</p>');
        w.document.write(printContents.innerHTML);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        w.print();
        w.close();
    }
    </script>
</body>
</html>
