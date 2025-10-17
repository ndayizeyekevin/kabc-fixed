<style>
    body {
        background-color: #e1e1e1;
        font-family: Tahoma, sans-serif;
    }
    table {
        width: 90%;
        border-collapse: collapse;
        margin: 0 auto; /* Center table */
    }
    th, td {
        border: 2px solid #000 !important;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    .header, .footer {
    text-align: center;
    font-weight: bold;
    font-size: 14pt;
    }


@media print {
    body {
        background-color: white !important;
    }
    #content {
        width: 100% !important;
        margin: 0;
        padding: 2px;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    table, th, td {
        border: 2px solid black !important;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    button {
        display: none !important;
    }
}
</style>

<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Africa/Kigali');

include '../inc/conn.php';

function getItemName($id) {
    global $db;
    $stmt = $db->prepare("SELECT item_name FROM tbl_items WHERE item_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['item_name'] : '';
}

function getItemUnitId($id) {
    global $db;
    $stmt = $db->prepare("SELECT item_unit FROM tbl_items WHERE item_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['item_unit'] : 0;
}

function getItemUnitName($id) {
    global $db;
    $stmt = $db->prepare("SELECT unit_name FROM tbl_unit WHERE unit_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['unit_name'] : '';
}

function getSupplierName($id) {
    global $db;
    $stmt = $db->prepare("SELECT name FROM suppliers WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['name'] : '';
}

$supplier = $_REQUEST['id'] ?? null;
$start = $_REQUEST['s'] ?? null;
$end = $_REQUEST['e'] ?? null;

$start = $start ? strtotime($start) : null;
$end = $end ? strtotime($end) : null;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { background-color: #e1e1e1; font-family: Tahoma, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f2f2f2; }
        .header, .footer { text-align: center; font-weight: bold; font-size: 14pt; }
    </style>
</head>
<body>
<center><button onclick="printInvoice()">Print To PDF</button></center>
<div id="content" style="background-color:white; margin:50px auto; padding:20px; width:80%;">
<?php include '../holder/printHeader.php'; ?>

<div class="header">
    SUPPLIER REPORT
    <?php
        if ($supplier) {
            echo "<strong> OF " . getSupplierName($supplier) . "</strong> ";
        } else {
            echo "(All Suppliers) ";
        }
        if ($start && $end) {
            echo "From <strong>" . date("Y-m-d", $start) . "</strong> to <strong>" . date("Y-m-d", $end) . "</strong>";
        }
    ?>
</div>

<br>

<table style='width:100%;' border="2">
    <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Supplier</th>
            <th>Item</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>U.P</th>
            <th>T.P</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>

<?php
$i = 0;
$grand_total = 0;

// ✅ Summary report for all suppliers
if (!$supplier || $_GET['id'] == 'all') {
    $suppliers = $db->query("SELECT * FROM suppliers");
    while ($s = $suppliers->fetch()) {
        $sup_id = $s['id'];
        $sup_name = $s['name'];
        $total = 0;

        $stmt = $db->prepare("SELECT * FROM store_request WHERE supplier = ?");
        $stmt->execute([$sup_id]);

        while ($req = $stmt->fetch()) {
            $req_date = strtotime($req['request_date']);
            if ($start && $end && ($req_date < $start || $req_date > $end)) continue;

            $req_id = $req['req_id'];
            $items = $db->prepare("SELECT * FROM request_store_item WHERE req_id = ?");
            $items->execute([$req_id]);

            while ($item = $items->fetch()) {
                $total += $item['del_qty'] * $item['del_price'];
            }
        }

        if ($total > 0) {
            $i++;
            $grand_total += $total;
            echo "<tr>
                    <td>$i</td>
                    <td>-</td>
                    <td><strong>$sup_name</strong></td>
                    <td colspan='4'>Summary</td>
                    <td>" . number_format($total) . "</td>
                    <td>" . number_format($total) . "</td>
                  </tr>";
        }
    }

    if ($i == 0) {
        echo '<tr><td colspan="9" style="text-align:center;color:red;">No data found for the selected filter.</td></tr>';
    } else {
        echo "<tr>
                <td colspan='7'><strong>Grand Total</strong></td>
                <td><strong>" . number_format($grand_total) . "</strong></td>
                <td><strong>" . number_format($grand_total) . "</strong></td>
              </tr>";
    }

} else {
    // ✅ Detailed report for selected supplier
    $stmt = $db->prepare("SELECT * FROM store_request WHERE supplier = ?");
    $stmt->execute([$supplier]);

    $has_data = false;

    while ($row = $stmt->fetch()) {
        $req_date = strtotime($row['request_date']);
        if ($start && $end && ($req_date < $start || $req_date > $end)) continue;

        $req_id = $row['req_id'];
        $date = $row['request_date'];
        $supplier_name = getSupplierName($row['supplier']);

        $item_stmt = $db->prepare("SELECT * FROM request_store_item WHERE req_id = ?");
        $item_stmt->execute([$req_id]);

        while ($item = $item_stmt->fetch()) {
            $i++;
            $qty = $item['del_qty'];
            $price = $item['del_price'];
            $total_price = $qty * $price;
            $grand_total += $total_price;

            echo "<tr>
                    <td>$i </td>
                    <td>$date</td>
                    <td>$supplier_name</td>
                    <td>" . getItemName($item['item_id']) . "</td>
                    <td>" . getItemUnitName(getItemUnitId($item['item_id'])) . "</td>
                    <td>" . ($qty) . "</td>
                    <td>" . ($price) . "</td>
                    <td>" . number_format($total_price) . "</td>
                    <td>" . number_format($total_price) . "</td>
                  </tr>";
            $has_data = true;
        }
    }

    if (!$has_data) {
        echo '<tr><td colspan="9" style="text-align:center;color:red;">No data found for the selected filter.</td></tr>';
    } else {
        echo "<tr>
                <td colspan='7'><strong>Grand Total</strong></td>
                <td><strong>" . number_format($grand_total) . "</strong></td>
                <td><strong>" . number_format($grand_total) . "</strong></td>
              </tr>";
    }
}
?>

    </tbody>
</table>

</div>

<script>

function printInvoice() {
    var content = document.getElementById('content').innerHTML;
    var styles = document.querySelectorAll('style, link[rel="stylesheet"]');
    var styleText = '';
    
    // Collect all styles
    styles.forEach(function(style) {
        styleText += style.outerHTML;
    });
    
    // Create a print window
    var printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Supplier Report</title>
            ${styleText}
            <style>
                @media print {
                    body {
                        margin: 0;
                        padding: 10px;
                    }
                    #content {
                        width: 100%;
                        margin: 0;
                        padding: 0;
                    }
                    button {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            ${content}
            <script>
                window.addEventListener('load', function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                });
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>

</body>
</html>
