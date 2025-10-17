<?php
ob_start(); // Start output buffering — avoids headers already sent

session_start();
require_once('dom/dompdf/autoload.inc.php');
require_once("../../inc/config.php");

use Dompdf\Dompdf;

$from = $_SESSION['from'] ?? null;
$to = $_SESSION['to'] ?? null;

$conditions = "WHERE c.creadit_user IS NOT NULL";
$params = [];

if ($from && $to) {
    $conditions .= " AND DATE(c.created_at) BETWEEN :from AND :to";
    $params[':from'] = $from;
    $params[':to'] = $to;
}

$sql = $db->prepare("
    SELECT c.OrderCode, c.created_at, cr.f_name, cr.l_name, cr.phone, cr.tinnumber
    FROM tbl_cmd c
    INNER JOIN creadit_id cr ON c.creadit_user = cr.id
    $conditions
    ORDER BY c.created_at DESC
");
$sql->execute($params);

$html = '<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
h2 { text-align: center; margin-bottom: 20px; }
h3 { margin: 10px 0 5px; }
p { margin: 0 0 10px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th, td { border: 1px solid #999; padding: 6px; text-align: left; }
thead { background-color: #f0f0f0; }
tfoot td { font-weight: bold; }
</style>';

$html .= '<h2>Credit Orders Report</h2>';

if ($sql->rowCount()) {
    $no = 1;
    $grandTotal = 0;

    while ($order = $sql->fetch()) {
        $html .= "<h3>$no. {$order['f_name']} {$order['l_name']} - Order: {$order['OrderCode']}</h3>";
        $html .= "<p>Phone: {$order['phone']} | TIN: {$order['tinnumber']} | Date: {$order['created_at']}</p>";

        $itemSql = $db->prepare("
            SELECT m.menu_name, m.menu_price, q.cmd_qty
            FROM tbl_cmd_qty q
            JOIN menu m ON q.cmd_item = m.menu_id
            WHERE q.cmd_code = :code
        ");
        $itemSql->execute([':code' => $order['OrderCode']]);

        $html .= '<table>
            <thead>
                <tr>
                    <th>Item</th><th>Price</th><th>Qty</th><th>Total</th>
                </tr>
            </thead><tbody>';

        $orderTotal = 0;
        while ($item = $itemSql->fetch()) {
            $lineTotal = $item['menu_price'] * $item['cmd_qty'];
            $orderTotal += $lineTotal;
            $html .= "<tr>
                        <td>{$item['menu_name']}</td>
                        <td>" . number_format($item['menu_price'], 2) . "</td>
                        <td>{$item['cmd_qty']}</td>
                        <td>" . number_format($lineTotal, 2) . "</td>
                      </tr>";
        }

        $html .= "<tfoot><tr><td colspan='3'>Order Total</td><td>" . number_format($orderTotal, 2) . "</td></tr></tfoot>";
        $html .= "</tbody></table>";

        $grandTotal += $orderTotal;
        $no++;
    }

    $html .= "<h3 style='text-align:right;'>Grand Total: " . number_format($grandTotal, 2) . "</h3>";
} else {
    $html .= "<p>No credit orders found for this period.</p>";
}

// Generate PDF
$dompdf = new Dompdf();
$dompdf->set_option('defaultFont', 'Arial'); // or Helvetica or Times
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

file_put_contents('debug_output.html', $html); // See if HTML is valid
// Send the PDF to the browser
$dompdf->stream("credit_report_" . date('Ymd_His') . ".pdf", ["Attachment" => false]);

exit;
?>
