<?php
/**
 * sale_summary_pdf.php
 * Generates a PDF report using Dompdf.
 * 
 * Two modes:
 * 1. Server-side generation (recommended): POST/GET with 'server=1' and optional 'selectday' parameter
 *    Builds the report HTML server-side with all DB queries and feeds to Dompdf.
 * 2. Client HTML mode (legacy): POST with 'html' parameter
 *    Takes pre-rendered HTML from client and feeds to Dompdf.
 */

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ob_start();

require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include '../inc/conn.php';
date_default_timezone_set('Africa/Kigali');

// === Helper Functions (from sale_summary.php) ===

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

function getTotalCollectionPaidByMethod($date, $method) {
    include '../inc/conn.php';
    $sql = "SELECT * FROM payment_tracks WHERE service = 'collection' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($date == date('Y-m-d', $row['created_at'])) {
                $sale += $row['amount'];
            }
        }
    }
    return $sale;
}

function getTotalAdvancePaidByMethodLess($date, $method) {
    include '../inc/conn.php';
    $sql = "SELECT * FROM payment_tracks WHERE service = 'less advance' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($date == date('Y-m-d', $row['created_at'])) {
                $sale += $row['amount'];
            }
        }
    }
    return $sale;
}

function getTotalAdvancePaidByMethod($date, $method) {
    include '../inc/conn.php';
    $sql = "SELECT * FROM payment_tracks WHERE service = 'advance' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($date == date('Y-m-d', $row['created_at'])) {
                $sale += $row['amount'];
            }
        }
    }
    return $sale;
}

function getClosedByUser($to) {
    include '../inc/conn.php';
    $names = "............................";
    $sql = "SELECT * FROM days WHERE opened_at = '$to'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['created_by'];
        }
    }
    $id ??= $_SESSION['u_id'];
    $sql = "SELECT * FROM tbl_users WHERE user_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $names = $row['f_name'] . " " . $row['l_name'];
        }
    }
    return $names;
}

function lastDay() {
    include '../inc/conn.php';
    $sql = "SELECT DATE(closed_at) AS closed_at FROM days ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lastDate = $row['closed_at'];
        }
    }
    return $lastDate ?? date('Y-m-d');
}

// === Determine mode and generate HTML ===

$html = '';
$reportDate = lastDay();

// Check for server-side generation request
if (!empty($_POST['server']) || !empty($_GET['server'])) {
    // Server-side mode: build report HTML using DB queries
    
    $selected_day = $_POST['selectday'] ?? $_GET['selectday'] ?? date('Y-m-d');
    
    // Query the days table to find the business day matching the selected date
    $sql_day = $db->prepare("SELECT opened_at, closed_at FROM days 
                             WHERE DATE(opened_at) = :selected_date 
                             ORDER BY opened_at DESC LIMIT 1");
    $sql_day->execute(['selected_date' => $selected_day]);
    
    if ($sql_day->rowCount() > 0) {
        $day_row = $sql_day->fetch(PDO::FETCH_ASSOC);
        $from = $day_row['opened_at'];
        $to = $day_row['closed_at'] ?? date('Y-m-d H:i:s');
        $reportDate = $selected_day;
    } else {
        // Fallback: use selected date as boundaries
        $from = $selected_day . ' 00:00:00';
        $to = $selected_day . ' 23:59:59';
        $reportDate = $selected_day;
    }
    
    // Initialize payment totals
    $cash = $card = $momo = $credit = $cheque = $cashCredit = $others = 0;
    $cashAdvance = $cardAdvance = $momoAdvance = $creditAdvance = $chequeAdvance = 0;
    $cashAdvanceLess = $cardAdvanceLess = $momoAdvanceLess = $creditAdvanceLess = $chequeAdvanceLess = 0;
    $cashCollection = $cardCollection = $momoCollection = $creditCollection = $chequeCollection = 0;
    $roomCreditTotal = 0;
    
    // Get all orders within the date range
    $sql = $db->prepare(
        "SELECT q.cmd_code
         FROM tbl_cmd_qty q
         LEFT JOIN payment_tracks pt ON q.cmd_code = pt.order_code
         LEFT JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
         WHERE q.created_at >= :from_time AND q.created_at <= :to_time
         AND (pt.order_code IS NOT NULL OR c.room_client IS NOT NULL)
         GROUP BY q.cmd_code"
    );
    $sql->execute(['from_time' => $from, 'to_time' => $to]);
    
    if ($sql->rowCount()) {
        while ($fetch = $sql->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($fetch['cmd_code'])) {
                $cmd = $fetch['cmd_code'];
                $cash        += getTotalPaidByMethod($cmd, '01');
                $card        += getTotalPaidByMethod($cmd, '05');
                $momo        += getTotalPaidByMethod($cmd, '06');
                $credit      += getTotalPaidByMethod($cmd, '02');
                $cheque      += getTotalPaidByMethod($cmd, '04');
                $cashCredit  += getTotalPaidByMethod($cmd, '03');
                $others      += getTotalPaidByMethod($cmd, '07');
            }
        }
    }
    
    // Calculate advance totals
    $cashAdvance     += getTotalAdvancePaidByMethod($reportDate, '01');
    $cardAdvance     += getTotalAdvancePaidByMethod($reportDate, '05');
    $momoAdvance     += getTotalAdvancePaidByMethod($reportDate, '06');
    $creditAdvance   += getTotalAdvancePaidByMethod($reportDate, '02');
    $chequeAdvance   += getTotalAdvancePaidByMethod($reportDate, '04');
    
    // Calculate advance less totals
    $cashAdvanceLess     += getTotalAdvancePaidByMethodLess($reportDate, '01');
    $cardAdvanceLess     += getTotalAdvancePaidByMethodLess($reportDate, '05');
    $momoAdvanceLess     += getTotalAdvancePaidByMethodLess($reportDate, '06');
    $creditAdvanceLess   += getTotalAdvancePaidByMethodLess($reportDate, '02');
    $chequeAdvanceLess   += getTotalAdvancePaidByMethodLess($reportDate, '04');
    
    // Calculate collection totals
    $cashCollection     += getTotalCollectionPaidByMethod($reportDate, '01');
    $cardCollection     += getTotalCollectionPaidByMethod($reportDate, '05');
    $momoCollection     += getTotalCollectionPaidByMethod($reportDate, '06');
    $creditCollection   += getTotalCollectionPaidByMethod($reportDate, '02');
    $chequeCollection   += getTotalCollectionPaidByMethod($reportDate, '04');
    
    // Get room orders
    $roomStmt = $db->prepare("SELECT q.cmd_code
        FROM tbl_cmd_qty q
        INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
        WHERE q.created_at >= :from_time AND q.created_at <= :to_time
          AND c.room_client IS NOT NULL
        GROUP BY q.cmd_code");
    $roomStmt->execute(['from_time' => $from, 'to_time' => $to]);
    
    if ($roomStmt->rowCount()) {
        while ($rc = $roomStmt->fetch(PDO::FETCH_ASSOC)) {
            $cmd = $rc['cmd_code'];
            $cmdInfo = $db->prepare("SELECT room_client FROM tbl_cmd WHERE OrderCode = :code LIMIT 1");
            $cmdInfo->execute(['code' => $cmd]);
            $cmdRow = $cmdInfo->fetch(PDO::FETCH_ASSOC);
            if (!$cmdRow || empty($cmdRow['room_client'])) { continue; }
            $booking_id = $cmdRow['room_client'];
            
            $sumOrder = $db->prepare("SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) AS total
                FROM tbl_cmd_qty
                INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                WHERE tbl_cmd_qty.cmd_code = :cmd");
            $sumOrder->execute(['cmd' => $cmd]);
            $rowSum = $sumOrder->fetch(PDO::FETCH_ASSOC);
            $order_total = (float)($rowSum['total'] ?? 0);
            
            $cashierPay = $db->prepare("SELECT SUM(amount) AS paid FROM payment_tracks WHERE order_code = :code");
            $cashierPay->execute(['code' => $cmd]);
            $cashierRow = $cashierPay->fetch(PDO::FETCH_ASSOC);
            $amount_paid = (float)($cashierRow['paid'] ?? 0);
            
            $roomPay = $db->prepare("SELECT SUM(amount) AS room_paid FROM payments WHERE booking_id = :bid");
            $roomPay->execute(['bid' => $booking_id]);
            $roomPayments = (float)($roomPay->fetch(PDO::FETCH_ASSOC)['room_paid'] ?? 0);
            
            if ($roomPayments > 0) {    
                $accStmt = $db->prepare("SELECT booking_amount FROM tbl_acc_booking WHERE id = :bid");
                $accStmt->execute(['bid' => $booking_id]);
                $accRow = $accStmt->fetch(PDO::FETCH_ASSOC);
                $accommodation_cost = (float)($accRow['booking_amount'] ?? 0);
                
                $allOrders = $db->prepare("SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) AS total_orders
                    FROM tbl_cmd
                    INNER JOIN tbl_cmd_qty ON tbl_cmd.OrderCode = tbl_cmd_qty.cmd_code
                    INNER JOIN menu ON tbl_cmd_qty.cmd_item = menu.menu_id
                    WHERE tbl_cmd.room_client = :bid");
                $allOrders->execute(['bid' => $booking_id]);
                $total_all_orders = (float)($allOrders->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0);
                
                $total_bill = $accommodation_cost + $total_all_orders;
                
                if ($total_bill > 0) {
                    if ($roomPayments >= $total_bill) {
                        $amount_paid = max($amount_paid, $order_total);
                    } elseif ($roomPayments > $accommodation_cost && $total_all_orders > 0) {
                        $payment_for_orders = $roomPayments - $accommodation_cost;
                        $this_order_share = ($order_total / $total_all_orders) * $payment_for_orders;
                        $amount_paid = $amount_paid + $this_order_share;
                    }
                }
            }
            
            $balance = $order_total - $amount_paid;
            if ($balance > 0) {
                $roomCreditTotal += $balance;
            }
        }
    }
    
    // Build the HTML
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Summary</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 10px; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .table th, .table td { padding: 5px 7px; border: 1px solid #000000ff; font-size: 12px; }
        h1, h2, h3, h4 { margin: 3px 0; padding: 0; }
        hr { margin: 6px 0; border: none; border-top: 1px solid #000000ff; }
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
    <h4 style="text-align: center;">Report for ' . htmlspecialchars($reportDate) . '</h4>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Total</th>
                    <th>All Total</th>
                </tr>
            </thead>
            <tbody>';
    
    $alltotal = 0;
    $departments = [
        "Bar Sales" => 2,
        "Resto Sales" => 1,
    ];
    
    foreach ($departments as $key => $val) {
        // Get category total
        $sumStmt = $db->prepare(
            "SELECT SUM(CAST(REPLACE(m.menu_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS total
             FROM tbl_cmd_qty q
             INNER JOIN menu m ON m.menu_id = q.cmd_item
             INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
             WHERE q.created_at BETWEEN :from_time AND :to_time
               AND m.cat_id = :val
               AND (c.room_client IS NOT NULL OR (SELECT SUM(amount) FROM payment_tracks WHERE order_code = c.OrderCode) >= 
                   (SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) 
                    FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item 
                    WHERE tbl_cmd_qty.cmd_code = c.OrderCode))"
        );
        $sumStmt->execute(['from_time' => $from, 'to_time' => $to, 'val' => $val]);
        $rowSum = $sumStmt->fetch(PDO::FETCH_ASSOC);
        $categoryTotal = (float)($rowSum['total'] ?? 0);
        
        // Get orders for category
        $cmdSql = $db->prepare(
            "SELECT DISTINCT c.OrderCode
             FROM tbl_cmd c
             WHERE c.created_at BETWEEN :from_time AND :to_time
             AND (c.room_client IS NOT NULL OR (SELECT SUM(amount) FROM payment_tracks WHERE order_code = c.OrderCode) >= 
                 (SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) 
                  FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item 
                  WHERE tbl_cmd_qty.cmd_code = c.OrderCode))"
        );
        $cmdSql->execute(['from_time' => $from, 'to_time' => $to]);
        
        $groupedData = [];
        $cmdRows = $cmdSql->fetchAll(PDO::FETCH_ASSOC);
        $cmdCodes = array_column($cmdRows, 'OrderCode');
        
        $roomMap = [];
        if (!empty($cmdCodes)) {
            $placeholders = implode(',', array_fill(0, count($cmdCodes), '?'));
            $roomAllStmt = $db->prepare("SELECT OrderCode, room_client FROM tbl_cmd WHERE OrderCode IN ($placeholders)");
            $roomAllStmt->execute($cmdCodes);
            while ($r = $roomAllStmt->fetch(PDO::FETCH_ASSOC)) {
                $roomMap[$r['OrderCode']] = $r['room_client'];
            }
        }
        
        $total = 0;
        foreach ($cmdRows as $cmdRow) {
            $cmd = $cmdRow['OrderCode'];
            $itemSql = $db->prepare("SELECT menu.*, tbl_cmd_qty.cmd_qty FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item WHERE cmd_code = :cmd AND menu.cat_id = :val");
            $itemSql->execute(['cmd' => $cmd, 'val' => $val]);
            $items = $itemSql->fetchAll(PDO::FETCH_ASSOC);
            
            $orderTotal = 0;
            foreach ($items as $item) {
                $price = (float)str_replace(',', '', $item['menu_price']);
                $qty = (int)$item['cmd_qty'];
                $orderTotal += $price * $qty;
            }
            
            $paidAmount = getTotalPaidByMethod($cmd, '01') + getTotalPaidByMethod($cmd, '05') + getTotalPaidByMethod($cmd, '06') + getTotalPaidByMethod($cmd, '02') + getTotalPaidByMethod($cmd, '04') + getTotalPaidByMethod($cmd, '03') + getTotalPaidByMethod($cmd, '07');
            
            $isRoomOrder = isset($roomMap[$cmd]) && !empty($roomMap[$cmd]) && $roomMap[$cmd] > 0;
            
            if (($isRoomOrder) || ($paidAmount >= $orderTotal && $orderTotal > 0)) {
                foreach ($items as $item) {
                    $menuDesc = $item['menu_desc'];
                    if (!isset($groupedData[$menuDesc])) {
                        $groupedData[$menuDesc] = [];
                    }
                    $groupedData[$menuDesc][] = $item;
                }
            }
        }
        
        // Department header row (one row in the outer table)
        $html .= '<tr>
                    <td><h2>' . htmlspecialchars($key) . '</h2></td>
                    <td>' . number_format($categoryTotal) . '</td>
                    <td></td>
                </tr>';

        // For each grouped subcategory, emit a full-width row that contains its own table.
        foreach ($groupedData as $key_desc => $item_list) {
            $html .= '<tr><td colspan="3">';
            $html .= '<h3>' . htmlspecialchars($key_desc) . '</h3>';
            $html .= '<table class="table">
                        <thead>
                            <tr>
                                <th>ITEM NAME</th>
                                <th>PRICE</th>
                                <th>QTY</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>';

            $categoryItems = [];
            foreach ($item_list as $val) {
                $menuName = $val['menu_name'];
                $price = (float) str_replace(',', '', (string)$val['menu_price']);
                $qty = (int)$val['cmd_qty'];
                $amount = $price * $qty;

                if (!isset($categoryItems[$menuName])) {
                    $categoryItems[$menuName] = [
                        'price' => $price,
                        'qty' => 0,
                        'total' => 0
                    ];
                }
                $categoryItems[$menuName]['qty'] += $qty;
                $categoryItems[$menuName]['total'] += $amount;
            }

            foreach ($categoryItems as $itemName => $itemData) {
                $total += $itemData['total'];
                $html .= '<tr>
                            <td>' . htmlspecialchars($itemName) . '</td>
                            <td>' . number_format($itemData['price']) . '</td>
                            <td>' . $itemData['qty'] . '</td>
                            <td>' . number_format($itemData['total']) . '</td>
                        </tr>';
            }

            $html .= '</tbody>
                    </table>';
            $html .= '</td></tr>';
        }

        // Sub total row for this department (single row across the outer table)
        $html .= '<tr>
                    <td></td>
                    <td colspan="2">Sub Total: ' . number_format($total) . ' RWF</td>
                </tr>';

        $alltotal += $total;
    }
    
    $html .= '<tr>
                <th colspan="2"><h3>Grand Total</h3></th>
                <th><h3>' . number_format($alltotal) . ' RWF</h3></th>
            </tr>
        </tbody>
        </table>
        <hr>
        <h3>Cash: ' . number_format($cash + $cashCollection + $cashAdvance - $cashAdvanceLess) . ' RWF</h3>
        <h3>POS: ' . number_format($card + $cardCollection + $cardAdvance - $cardAdvanceLess) . ' RWF</h3>
        <h3>Momo: ' . number_format($momo + $momoCollection + $momoAdvance - $momoAdvanceLess) . ' RWF</h3>
        <h3>Credit: ' . number_format($credit + $creditCollection + $creditAdvance - $creditAdvanceLess) . ' RWF</h3>
        <h3>Room Credit: ' . number_format($roomCreditTotal) . ' RWF</h3>
        <h3>Bank Cheque: ' . number_format($cheque + $chequeCollection + $chequeAdvance - $chequeAdvanceLess) . ' RWF</h3>
        <h3>CASH/CREDIT: ' . number_format($cashCredit ?? 0) . ' RWF</h3>
        <h3>OTHERS: ' . number_format($others ?? 0) . ' RWF</h3>
        <hr>
        <h1>Total: ' . number_format($cash + $card + $momo + $cheque + $credit + $cashCredit + $others + $roomCreditTotal +
                                     $cashCollection + $cardCollection + $momoCollection + $chequeCollection + $creditCollection +
                                     $cashAdvance + $cardAdvance + $momoAdvance + $chequeAdvance + $creditAdvance - 
                                     $cashAdvanceLess - $cardAdvanceLess - $momoAdvanceLess - $creditAdvanceLess - $chequeAdvanceLess) . ' RWF</h1>
        <hr>
    </div>
    <table class="table">
                                <tr>
                                    <td>
                                        <br>Cashier<br><br>Names: ' . getClosedByUser($reportDate) . '<br>
                                        <br>Signature: .........................................
                                    </td>
                                    <td>
                                        <br>Reviewed By (F&B):<br><br>Names: .........................................<br>
                                        <br>Signature: .........................................
                                    </td>
                                    <td>
                                        <br>Received By (G. Cashier):<br><br>Names: .........................................<br>
                                        <br>Signature: .........................................
                                    </td>
                                </tr>
                            </table>
</body>
</html>';

} elseif (!empty($_POST['html'])) {
    // Client HTML mode (legacy)
    $html = $_POST['html'];
} else {
    // No input provided
    if (ob_get_length()) { ob_end_clean(); }
    http_response_code(400);
    echo 'No data provided';
    exit;
}

// === Inject PDF CSS ===
$pdfCss = '<style>
    body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color: #000000ff; margin:0; padding:0;}
    img{ max-width:100%; height:auto; }
    #content { margin-top: 0; padding-top: 0; }
    #headerprint { margin: 0 0 2px 0; padding:0; }
    #headerprint img { max-height: 60px; width:auto; display:block; }
    header, .print-header { display:none; }
    .table{ width:100%; border-collapse:collapse; margin-bottom:6px; margin-top:0; }
    .table th, .table td{ padding:5px 7px; border:1px solid #000000ff; font-size:12px; }
    .table-responsive{ overflow: visible !important; margin-top:0; }
    h1,h2,h3,h4{ margin:3px 0; padding:0; }
    h4 { margin-top: 2px; margin-bottom: 2px; }
    textarea.form-control, .btn{ display:none !important; }
    hr { margin: 4px 0; border: none; border-top: 2px solid #000000ff; }
    .alert { display:none; }
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
    
    $dompdf->stream('Sales-Summary.pdf', ['Attachment' => false]);
    exit;
} catch (\Exception $e) {
    if (ob_get_length()) { ob_end_clean(); }
    http_response_code(500);
    error_log('sale_summary_pdf error: ' . $e->getMessage());
    echo 'Unable to generate PDF. Check server error logs.';
    exit;
}

