<?php
ob_start();
session_start();
require_once('dom/dompdf/autoload.inc.php');
require_once("../../inc/config.php");
use Dompdf\Dompdf;

// Date filters (optional)

$from = $_SESSION['from'] ?? null;
$to = $_SESSION['to'] ?? null;

$conditions = "WHERE c.room_client IS NOT NULL";
$params = [];

if ($from && $to) {
    $conditions .= " AND DATE(c.created_at) BETWEEN :from AND :to";
    $params[':from'] = $from;
    $params[':to'] = $to;
}

// Fetch all guest orders
$sql = $db->prepare("
    SELECT c.OrderCode, c.created_at, g.first_name, g.last_name, g.id AS guest_id
    FROM tbl_cmd c
    INNER JOIN tbl_acc_guest g ON c.room_client = g.id
    $conditions
    ORDER BY g.last_name ASC, c.created_at DESC
");
$sql->execute($params);

$html = '<style>
body { font-family: Helvetica, sans-serif; font-size: 12px; }
h2 { text-align: center; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th, td { border: 1px solid #999; padding: 5px; text-align: left; }
thead { background: #f0f0f0; }
h3 { margin-top: 30px; }
</style>';

$html .= '<h2>Room Guest Orders Report</h2>';

if ($sql->rowCount()) {
    $guestNo = 1;
    $grandTotal = 0;

    while ($order = $sql->fetch()) {
        $html .= "<h3>$guestNo. Guest: {$order['first_name']} {$order['last_name']} - Order: {$order['OrderCode']}</h3>";
        $html .= "<p>Date: {$order['created_at']}</p>";

        // Get items in this order
        $itemSql = $db->prepare("
            SELECT m.menu_name, m.menu_price, q.cmd_qty
            FROM tbl_cmd_qty q
            JOIN menu m ON q.cmd_item = m.menu_id
            WHERE q.cmd_code = :code
        ");
        $itemSql->execute([':code' => $order['OrderCode']]);

        $html .= '<table><thead><tr>
                    <th>Item</th><th>Price</th><th>Qty</th><th>Total</th>
                  </tr></thead><tbody>';

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

        $html .= "<tr><td colspan='3'><strong>Total</strong></td><td><strong>" . number_format($orderTotal, 2) . "</strong></td></tr>";
        $html .= "</tbody></table>";

        $grandTotal += $orderTotal;
        $guestNo++;
    }

    $html .= "<h3 style='text-align:right;'>Grand Total: " . number_format($grandTotal, 2) . "</h3>";
} else {
    $html .= "<p>No room guest orders found for this period.</p>";
}

// Output PDF
$dompdf = new Dompdf();
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("room_guest_orders_" . date('Ymd_His') . ".pdf", ["Attachment" => false]);
exit;
