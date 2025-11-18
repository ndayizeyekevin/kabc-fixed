<?php
/**
 * details_summary_pdf.php
 * Generates a PDF report using Dompdf for the detailed sales summary.
 * 
 * Accepts GET parameter 'selected_date' to fetch and display report for that date
 */

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ob_start();

require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include '../inc/conn.php';
date_default_timezone_set('Africa/Kigali');

// === Helper Functions (from details_summary.php) ===

function getServantName($id){
    include '../inc/conn.php';
    $sql = "SELECT * FROM `tbl_users` WHERE user_id ='$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            return $row['f_name']. " ".$row['l_name'];
        }
    }
    return '';
}

function getOrderTotal($order){
    include '../inc/conn.php';
    $sql = "SELECT cmd_qty,cmd_item FROM `tbl_cmd_qty` WHERE  cmd_code = '$order'";
    $result = $conn->query($sql);
    $sale=0;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sale = $sale + get_price($row['cmd_item']) * $row['cmd_qty'];
        }
    }
    return $sale;
}

function getTotalPaidByMethod($code, $method) {
    include '../inc/conn.php';
    $sql = "SELECT * FROM payment_tracks WHERE order_code = '$code' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sale += $row['amount'];
        }
    }
    return $sale;
}

function getInvoiceNo($id){
    global $db;
    include '../inc/conn.php';
    try {
        $sqli = $db->prepare("SELECT * FROM `tbl_cmd` WHERE OrderCode=:code");
        $sqli->execute(['code' => $id]);
        while($row = $sqli->fetch()){
            return $row['id'];
        }
    } catch (Exception $e) {
        return '';
    }
    return '';
}

function getEbmPaymentMode($id){
    include '../inc/conn.php';
    $sql = "SELECT * FROM tbl_vsdc_sales where transaction_id ='$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            return $row['pmtTyCd'];
        }
    }
    return '';
}

function getTotalByServicebycode($code, $category,$from,$last){
    include '../inc/conn.php';
    if($last==0){
        $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE  cmd_qty_id > '$from'   AND cmd_status = '12'  AND cmd_code = '$code'";
    } else {
        $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE cmd_qty_id > '$from' AND  cmd_qty_id <= '$last'  AND cmd_status = '12' AND cmd_code = '$code'";
    }
    $result = $conn->query($sql);
    $sale=0;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(getifpaid($row['cmd_code'])>0){
                if(get_category($row['cmd_item'])==$category){
                    $sale = $sale + get_price($row['cmd_item']) * $row['cmd_qty'];
                }
            }
        }
    }
    return $sale;
}

function getTotalByService($category,$from,$last){
    include '../inc/conn.php';
    if($last==0){
        if (strpos($from, ':') !== false) {
            $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE created_at > '$from' AND cmd_status = '12'";
        } else {
            $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE DATE(created_at) >= '$from' AND cmd_status = '12'";
        }
    } else {
        $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE DATE(created_at) >= '$from' AND DATE(created_at) <= '$last' AND cmd_status = '12'";
    }
    $result = $conn->query($sql);
    $sale=0;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(getifpaid($row['cmd_code'])>0){
                if(get_category($row['cmd_item'])==$category){
                    $sale = $sale + get_price($row['cmd_item']) * $row['cmd_qty'];
                }
            }
        }
    }
    return $sale;
}

function getifpaid($code){
    include '../inc/conn.php';
    $sql = "SELECT order_code FROM payment_tracks where order_code = '$code'";
    $result = $conn->query($sql);
    return $result->num_rows;
}

function get_category($id){
    include '../inc/conn.php';
    $sql = "SELECT cat_id FROM `menu` WHERE menu_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            return $row['cat_id'];
        }
    }
}

function get_price($id){
    include '../inc/conn.php';
    $sql = "SELECT  menu_price FROM `menu` WHERE menu_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            return $row['menu_price'];
        }
    }
}

// === Generate HTML ===

$html = '';
$reportDate = date('Y-m-d');

// Get selected date from GET parameter
$selected_day = $_GET['selected_date'] ?? date('Y-m-d');
$reportDate = $selected_day;

// Query the days table to find the business day matching the selected date
$sql_day = $db->prepare("SELECT opened_at, closed_at FROM days 
                         WHERE DATE(opened_at) = :selected_date 
                            OR (:selected_date_start BETWEEN opened_at AND COALESCE(closed_at, NOW()))
                         ORDER BY opened_at DESC LIMIT 1");
$sql_day->execute(['selected_date' => $selected_day, 'selected_date_start' => $selected_day . ' 00:00:00']);

if ($sql_day->rowCount() > 0) {
    $day_row = $sql_day->fetch(PDO::FETCH_ASSOC);
    $from = $day_row['opened_at'];
    $last = $day_row['closed_at'];
} else {
    // Fallback: use selected date as boundaries
    $from = $selected_day . ' 00:00:00';
    $last = $selected_day . ' 23:59:59';
}

// Initialize payment totals
$cash = $card = $momo = $credit = $cheque = $room = 0;

// Build the HTML
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Details Summary</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 10px; font-size: 11px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .table th, .table td { padding: 5px 7px; border: 1px solid #ccc; font-size: 10px; }
        h1, h2, h3, h4 { margin: 3px 0; padding: 0; }
        hr { margin: 6px 0; border: none; border-top: 1px solid #ddd; }
        .btn { display: none; }
        textarea { display: none; }
    </style>
</head>
<body>';

// Include header from printHeader.php
ob_start();
include '../holder/printHeader.php';
$html .= ob_get_clean();

$html .= '<hr>
<h4 style="text-align: center;">Detailed Report for ' . htmlspecialchars($reportDate) . '</h4>
<hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Waiter(ess)</th>
                <th>Order Total</th>
                <th>Cash</th>
                <th>MOMO</th>
                <th>POS</th>
                <th>CREDIT</th>
                <th>ROOM CREDIT</th>
                <th>CHEQUE</th>
                <th>PAYMENT METHOD TOTAL</th>
                <th>BALANCE</th>
            </tr>
        </thead>
        <tbody>';

// Get orders for the selected date range
if ($last) {
    $sql = $db->prepare("SELECT q.*, c.room_client FROM tbl_cmd_qty q
                         LEFT JOIN payment_tracks pt ON q.cmd_code = pt.order_code
                         LEFT JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                         WHERE q.created_at >= :from_time
                         AND q.created_at <= :to_time
                         AND (pt.order_code IS NOT NULL OR c.room_client IS NOT NULL)
                         GROUP BY q.cmd_code");
    $sql->execute(['from_time' => $from, 'to_time' => $last]);
} else {
    $sql = $db->prepare("SELECT q.*, c.room_client FROM tbl_cmd_qty q
                         LEFT JOIN payment_tracks pt ON q.cmd_code = pt.order_code
                         LEFT JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                         WHERE q.created_at >= :from_time
                         AND (pt.order_code IS NOT NULL OR c.room_client IS NOT NULL)
                         GROUP BY q.cmd_code");
    $sql->execute(['from_time' => $from]);
}

$order = 0;
$order_details = [];

if($sql->rowCount()){
    while($fetch = $sql->fetch(PDO::FETCH_ASSOC)){
        if($fetch['cmd_code']){
            $code_for_calc = $fetch['cmd_code'];
            $order_total = getOrderTotal($code_for_calc);
            $order += $order_total;

            // Sum cashier payments
            $stmt_pt = $db->prepare("SELECT SUM(amount) AS paid FROM payment_tracks WHERE order_code = :code");
            $stmt_pt->execute(['code' => $code_for_calc]);
            $row_pt = $stmt_pt->fetch(PDO::FETCH_ASSOC);
            $amount_paid = (float)($row_pt['paid'] ?? 0);

            // Allocate room payments if attached to a room
            if (!empty($fetch['room_client'])) {
                $booking_id = $fetch['room_client'];

                $stmt_rp = $db->prepare("SELECT SUM(amount) AS room_paid FROM payments WHERE booking_id = :bid");
                $stmt_rp->execute(['bid' => $booking_id]);
                $room_payments = (float)($stmt_rp->fetch(PDO::FETCH_ASSOC)['room_paid'] ?? 0);

                if ($room_payments > 0) {
                    $stmt_acc = $db->prepare("SELECT booking_amount FROM tbl_acc_booking WHERE id = :bid");
                    $stmt_acc->execute(['bid' => $booking_id]);
                    $accommodation_cost = (float)($stmt_acc->fetch(PDO::FETCH_ASSOC)['booking_amount'] ?? 0);

                    $stmt_all = $db->prepare("SELECT SUM(m.menu_price * q.cmd_qty) AS total_orders
                        FROM tbl_cmd c
                        INNER JOIN tbl_cmd_qty q ON c.OrderCode = q.cmd_code
                        INNER JOIN menu m ON q.cmd_item = m.menu_id
                        WHERE c.room_client = :bid");
                    $stmt_all->execute(['bid' => $booking_id]);
                    $total_all_orders = (float)($stmt_all->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0);

                    $total_bill = $accommodation_cost + $total_all_orders;

                    if ($room_payments >= $total_bill) {
                        $amount_paid = max($amount_paid, $order_total);
                    } elseif ($room_payments > $accommodation_cost && $total_all_orders > 0) {
                        $payment_for_orders = $room_payments - $accommodation_cost;
                        $this_order_share = ($order_total / $total_all_orders) * $payment_for_orders;
                        $amount_paid += $this_order_share;
                    }
                }
            }

            $balance = $order_total - $amount_paid;
            $roomCredit = (!empty($fetch['room_client'])) ? max($balance, 0) : 0;

            $cash_paid = getTotalPaidByMethod($code_for_calc, '01');
            $momo_paid = getTotalPaidByMethod($code_for_calc, '06');
            $card_paid = getTotalPaidByMethod($code_for_calc, '05');
            $credit_paid = getTotalPaidByMethod($code_for_calc, '02');
            $cheque_paid = getTotalPaidByMethod($code_for_calc, '04');

            $cash += $cash_paid;
            $momo += $momo_paid;
            $card += $card_paid;
            $credit += $credit_paid;
            $cheque += $cheque_paid;
            $room += $roomCredit;

            $tt = $cheque_paid + $credit_paid + $card_paid + $momo_paid + $cash_paid;

            $order_details[] = [
                'invoice_no' => getInvoiceNo($code_for_calc),
                'waiter' => getServantName($fetch['Serv_id']),
                'order_total' => $order_total,
                'cash' => $cash_paid,
                'momo' => $momo_paid,
                'card' => $card_paid,
                'credit' => $credit_paid,
                'cheque' => $cheque_paid,
                'room' => $roomCredit,
                'method_total' => $tt,
                'balance' => $order_total - $tt,
                'payment_mode' => getEbmPaymentMode($code_for_calc)
            ];
        }
    }
}

// Output order details rows
foreach ($order_details as $detail) {
    $payment_mode = '';
    if ($detail['payment_mode'] == '01') $payment_mode = 'Cash';
    elseif ($detail['payment_mode'] == '02') $payment_mode = 'Credit';
    elseif ($detail['payment_mode'] == '06') $payment_mode = 'Mobile Money';
    elseif ($detail['payment_mode'] == '04') $payment_mode = 'Cheque';
    elseif ($detail['payment_mode'] == '05') $payment_mode = 'POS';

    $html .= '<tr>
        <td>' . htmlspecialchars($detail['invoice_no']) . '</td>
        <td>' . htmlspecialchars($detail['waiter']) . '</td>
        <td>' . number_format($detail['order_total']) . '</td>
        <td>' . number_format($detail['cash']) . '</td>
        <td>' . number_format($detail['momo']) . '</td>
        <td>' . number_format($detail['card']) . '</td>
        <td>' . number_format($detail['credit']) . '</td>
        <td>' . number_format($detail['room']) . '</td>
        <td>' . number_format($detail['cheque']) . '</td>
        <td>' . number_format($detail['method_total']) . '</td>
        <td>' . number_format($detail['balance']) . '</td>
    </tr>';
}

$py = $cheque + $cash + $momo + $card + $credit + $room;

$html .= '<tr>
    <th colspan="2">Total:</th>
    <th>' . number_format($order) . '</th>
    <th>' . number_format($cash) . '</th>
    <th>' . number_format($momo) . '</th>
    <th>' . number_format($card) . '</th>
    <th>' . number_format($credit) . '</th>
    <th>' . number_format($room) . '</th>
    <th>' . number_format($cheque) . '</th>
    <th colspan="2">' . number_format($py) . '</th>
</tr>

<tr>
    <th colspan="10"><strong>Sales by Category</strong></th>
    <th><strong>Total</strong></th>
</tr>';

// Get category totals
$totalbar = getTotalByService(2, $from, $last);
$totalresto = getTotalByService(1, $from, $last);
$totalcoffe = getTotalByService(32, $from, $last);
$transport = getTotalByService(33, $from, $last);

$html .= '<tr>
    <td colspan="10">Bar Sales</td>
    <td>' . number_format($totalbar) . '</td>
</tr>
<tr>
    <td colspan="10">Resto Sales</td>
    <td>' . number_format($totalresto) . '</td>
</tr>
<tr>
    <td colspan="10">Coffee Shop</td>
    <td>' . number_format($totalcoffe) . '</td>
</tr>
<tr>
    <td colspan="10">Transport Fees</td>
    <td>' . number_format($transport) . '</td>
</tr>
<tr>
    <td colspan="10">Rooms</td>
    <td>' . number_format($room) . '</td>
</tr>
<tr>
    <th colspan="10"><strong>Category Subtotal:</strong></th>
    <th><strong>' . number_format($totalbar + $totalresto + $totalcoffe + $transport + $room) . '</strong></th>
</tr>

        </tbody>
    </table>
</div>';

// === CREDITS LIST SECTION ===
$html .= '<hr>
<h4>Credits List</h4>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

// Get credits from tbl_vsdc_sales
try {
    $credits_stmt = $db->prepare("SELECT * FROM tbl_vsdc_sales WHERE pmtTyCd = '02' 
                                   AND DATE(created_at) = :date
                                   ORDER BY created_at DESC");
    $credits_stmt->execute(['date' => $reportDate]);
    
    if ($credits_stmt->rowCount() > 0) {
        while ($credit_row = $credits_stmt->fetch(PDO::FETCH_ASSOC)) {
            $html .= '<tr>
                <td>' . htmlspecialchars($credit_row['id'] ?? '') . '</td>
                <td>' . htmlspecialchars($credit_row['description'] ?? '') . '</td>
                <td>' . number_format($credit_row['amount'] ?? 0) . '</td>
                <td>' . date('Y-m-d H:i', strtotime($credit_row['created_at'] ?? '')) . '</td>
                <td>' . htmlspecialchars($credit_row['status'] ?? 'Pending') . '</td>
            </tr>';
        }
    } else {
        $html .= '<tr><td colspan="5" style="text-align: center;">No credits recorded</td></tr>';
    }
} catch (Exception $e) {
    $html .= '<tr><td colspan="5" style="text-align: center;">Error loading credits</td></tr>';
}

$html .= '</tbody>
    </table>
</div>

</body>
</html>';

// === Inject PDF CSS ===
$pdfCss = '<style>
    body{font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#111; margin:0; padding:0;}
    img{ max-width:100%; height:auto; }
    #content { margin-top: 0; padding-top: 0; }
    #headerprint { margin: 0 0 2px 0; padding:0; }
    #headerprint img { max-height: 60px; width:auto; display:block; }
    header, .print-header { display:none; }
    .table{ width:100%; border-collapse:collapse; margin-bottom:6px; margin-top:0; }
    .table th, .table td{ padding:5px 7px; border:1px solid #ccc; font-size:10px; }
    .table-responsive{ overflow: visible !important; margin-top:0; }
    h1,h2,h3,h4{ margin:3px 0; padding:0; }
    h4 { margin-top: 2px; margin-bottom: 2px; }
    textarea.form-control, .btn{ display:none !important; }
    hr { margin: 2px 0; border: none; border-top: 1px solid #ddd; }
    .alert { display:none; }
    thead { display: table-header-group; }
    tbody { display: table-row-group; }
    tr { page-break-inside: avoid; page-break-after: auto; break-inside: avoid; }
</style>';

if (stripos($html, '</head>') !== false) {
    $html = str_ireplace('</head>', $pdfCss . '</head>', $html);
} else {
    $html = $pdfCss . $html;
}

// === Configure Dompdf ===
$tempRoot = __DIR__ . '/../dompdf_temp';
$fontDir = $tempRoot . '/fonts';
$tempDir = $tempRoot . '/tmp';
if (!is_dir($fontDir)) { @mkdir($fontDir, 0777, true); }
if (!is_dir($tempDir)) { @mkdir($tempDir, 0777, true); }

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$options->set('fontDir', $fontDir);
$options->set('fontCache', $fontDir);
$options->set('tempDir', $tempDir);

$dompdf = new Dompdf($options);

try {
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    if (ob_get_length()) { ob_end_clean(); }
    
    $dompdf->stream('Details-Summary.pdf', ['Attachment' => false]);
    exit;
} catch (\Exception $e) {
    if (ob_get_length()) { ob_end_clean(); }
    http_response_code(500);
    error_log('details_summary_pdf error: ' . $e->getMessage());
    echo 'Unable to generate PDF. Check server error logs.';
    exit;
}
?>
