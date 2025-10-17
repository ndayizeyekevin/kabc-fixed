<?php
ob_start();
require_once("../inc/conn.php");
require_once("../inc/DBController.php");

require '../dompdf/vendor/autoload.php'; 
use Dompdf\Dompdf;
use Dompdf\Options;


if (!isset($_SESSION['date_from']) || !isset($_SESSION['date_to'])) {
    $from = date('Y-m-d');
    $to = date("Y-m-d");
} else {
    $from = $_SESSION['date_from'];
    $to = $_SESSION['date_to'];
}



?>
<html>

<head>
    <title>Kitchen Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>Kitchen Report</h2>
    <table>
        <thead>
            <tr>
                <th>ITEM NO</th>
                <th>ITEM NAME</th>
                <th>ITEM DESCRIPTION</th>
                <th>PRICE</th>
                <th>QTY</th>
                <!--<th>AMOUNT</th>-->
                <!--<th>TAX RATE</th>-->
                <!--<th>TAX</th>-->
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i=0;
            $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                WHERE DATE(created) BETWEEN ? AND ? AND cmd_status = '12'
                GROUP BY cmd_item");
            $sql->execute([$from, $to]);

            while ($fetch = $sql->fetch()) {
                $i++;
                $amount = $fetch['menu_price'] * $fetch['totqty'];
                // $tottax = $amount * ((int)$fetch['tax'] / 100);
            ?>
                <tr>
                    <td><?php echo (int)$i; ?></td>
                    <td><?php echo $fetch['menu_name']; ?></td>
                    <td><?php echo $fetch['menu_desc']; ?></td>
                    <td><?php echo number_format($fetch['menu_price']); ?></td>
                    <td><?php echo $fetch['totqty']; ?></td>
                    <td><?php echo number_format($amount); ?></td>
                    <!--<td>-->
                    <?php
                    // <!--    echo $fetch['tax'];-->
                        ?>
                    <!--    </td>-->
                    <!--<td>-->
                    <?php
                    // <!--echo number_format($tottax); -->
                    ?>
                    <!--</td>-->
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</body>

</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("kitchen_report.pdf", ["Attachment" => 0]);
exit;
?>