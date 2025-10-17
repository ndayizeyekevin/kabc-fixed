<?php
ob_start();
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('dompdf/autoload.inc.php');
include('phpqrcode/qrlib.php');
include('../inc/DBController.php');

use Dompdf\Dompdf;
use Dompdf\Options;

$dompdf = new Dompdf();

// Validate and sanitize input
$id = isset($_GET['m']) ? intval($_GET['m']) : 0;
$code = isset($_GET['c']) ? htmlspecialchars($_GET['c']) : '';

if (!$id || !$code) {
    die("Invalid parameters.");
}

// Fetch table details
$get_rsv = $db->prepare("SELECT * FROM tbl_tables WHERE table_id = :id");
$get_rsv->bindParam(':id', $id);
$get_rsv->execute();
$getFetch = $get_rsv->fetch();

if (!$getFetch) {
    die("Table not found.");
}

// Process the order if form is submitted
if (isset($_REQUEST['proccess'])) {
    try {
        $menu_id = $_REQUEST['menu_id'];
        $orderCode = htmlspecialchars($_POST['orderCode']);
        $a = 5;

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE tbl_cmd SET status_id = :status WHERE OrderCode = :orderCode";
        $stmt = $db->prepare($sql);
        $stmt->execute([':status' => $a, ':orderCode' => $orderCode]);

        $sql = "UPDATE tbl_cmd_qty SET cmd_status = :status WHERE cmd_qty_id = :menu_id AND cmd_code = :orderCode";
        $stmt = $db->prepare($sql);

        foreach ($menu_id as $id) {
            $stmt->execute([
                ':status' => $a,
                ':menu_id' => $id,
                ':orderCode' => $orderCode
            ]);
        }

        $msg = "Successfully Completed!";
        echo '<meta http-equiv="refresh" content="1;URL=index?resto=prcsOrder">';
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

// Generate HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
    body {
        font-family: monospace;
        font-size: 10px;
        margin: 0;
        padding: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 2px 4px;
        font-size: 9px;
    }
        .rmNo {
            text-align: center;
            text-transform: uppercase;
            color: #ba4a48;
            font-weight: bold;
            font-size: 18px;
        }
        h2 { text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; }
    </style>
</head>
  <body> <table>
  <tr>
  <td>
  <img src="https://saintpaul.gope.rw/img/logo.png" style="width: 100px;height: 100px;">
  </td>
  <td>
 <center> CENTRE SAINT-PAUL KIGALI Ltd<br>
TEL:+250 785 285 341 / +250 789 477 745<br>
www.centrestpaul.com<br>
  TIN/VAT: 111477597</p>
  </td>
  </tr> </table>

    <h2>Menu-Order <span class="rmNo">Table No. ' . htmlspecialchars($getFetch['table_no']) . '</span></h2>
    <table>
        <thead>
            <tr>
                <th>Menu Order</th>
                <th>Price</th>
                <th>Sub-Category</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

$bonbar = "";
$sql = $db->prepare("
    SELECT q.*, m.menu_name, m.menu_price, m.cat_id, m.subcat_ID
    FROM tbl_cmd_qty q
    JOIN menu m ON m.menu_id = q.cmd_item
    WHERE q.cmd_code = :code AND q.cmd_status IN (1,13) AND m.cat_id = 2
");
$sql->bindParam(':code', $code);
$sql->execute();

while ($fetch = $sql->fetch()) {
    $OrderCode = $fetch['cmd_code'];
    $cat_id = $fetch['cat_id'];
    $subcat_ID = $fetch['subcat_ID'];

    $bonbar .= $fetch['menu_name'] . '<br> Qty: ' . $fetch['cmd_qty'] . '<br> ' . $fetch['message'] . '<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';

    $res_categ = $db->prepare("SELECT cat_name FROM category WHERE cat_id = :cat_id");
    $res_categ->bindParam(':cat_id', $cat_id);
    $res_categ->execute();
    $Cname = $res_categ->fetchColumn() ?: '';

    $res_sub_categ = $db->prepare("SELECT subcat_name FROM subcategory WHERE subcat_id = :subcat_id");
    $res_sub_categ->bindParam(':subcat_id', $subcat_ID);
    $res_sub_categ->execute();
    $Subname = $res_sub_categ->fetchColumn() ?: '';

    $html .= '
        <tr>
            <td>' . htmlspecialchars($fetch['menu_name']) .' X '.intval($fetch['cmd_qty']). '</td>
            <td>' . number_format($fetch['menu_price'], 2) . '</td>
            <td>' . htmlspecialchars($Subname) . '</td>
            <td>' . htmlspecialchars(($fetch['menu_price'] * $fetch['cmd_qty'])) . '</td>
        </tr>';
}

$_SESSION['bonbar'] = $bonbar;
$f_name=$_SESSION['f_name'];
$l_name=$_SESSION['l_name'];
$name = $f_name.' '.$l_name;
$html .= '
        </tbody>
    </table>
    <div class="footer">
        Generated on: ' . date('Y-m-d H:i:s') . '
  <br>
        By: '.$name.'
    </div>
</body>
</html>';

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf->setOptions($options);

$customPaper = array(0, 0, 280, 2000); // ~3.8in width × ~27in height
$dompdf->set_paper($customPaper);
$dompdf->set_option('isremoteenabled', true);

$dompdf->loadHtml($html);
// $dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("order_details_" . $getFetch['table_no'] . ".pdf", ["Attachment" => false]);
?>
