<?php

ob_start();

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Include necessary files
require_once '../inc/config.php'; // Adjust path as needed
require_once './controllers/StoreController.php'; // Adjust path as needed

// Get parameters
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get data
$storeController = new StoreController();
$data = $storeController->getStoreBalance($db, $start_date, $end_date);
$store_items = $data['data'];

if(!$data['success']){
    die("Failed to fetch data for PDF generation");
}

// Include DomPDF
require_once '../dompdf/autoload.inc.php'; // Adjust path as needed

use Dompdf\Dompdf;
use Dompdf\Options;

// Create PDF options
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultPaperSize', 'A4');
$options->set('defaultPaperOrientation', 'portrait');

$dompdf = new Dompdf($options);

// Generate HTML for PDF
$pdf_html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            margin: 0;
            padding: 20px;
        }
        .pdf-header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .pdf-logo { 
            max-height: 80px; 
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            margin: 15px 0;
            font-weight: bold;
        }
        .period {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .summary-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            width: 32%;
            border-left: 4px solid;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-card.in { border-left-color: #1cc88a; }
        .summary-card.out { border-left-color: #e74a3b; }
        .summary-card.balance { border-left-color: #4e73df; }
        .summary-label {
            text-transform: uppercase;
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .in-stock { background-color: #1cc88a; }
        .low-stock { background-color: #f6c23e; }
        .out-of-stock { background-color: #e74a3b; }
        .value-cell { font-weight: bold; }
        .text-success { color: #1cc88a; }
        .text-danger { color: #e74a3b; }
        .section-title {
            background-color: #e0e0e0;
            padding: 8px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <img src="https://saintpaul.gope.rw/img/logo.png" alt="Company Logo" class="pdf-logo">
        <div class="company-name">'.$company_name.'</div>
        <div>'.$company_address.'</div>
        <div>TIN/VAT Number: '.$company_tin.'</div>
        <div>Phone: '.$company_phone.'</div>
        <div class="report-title">Store Inventory Balance Report</div>
        <div class="period">Period: '.date('M j, Y', strtotime($store_items['start_date'])).' to '.date('M j, Y', strtotime($store_items['end_date'])).'</div>
    </div>
    
    
    <!-- Inventory Table -->
    <div class="section-title">Inventory Items</div>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Opening</th>
                <th>In</th>
                <th>Out</th>
                <th>Balance</th>
                <th>Price</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
$total_value = 0;
foreach ($store_items['items'] as $item) {
    $total_value += $item['inventory_value'];
    $status_class = '';
    if ($item['closing_balance'] > 20) {
        $status_class = 'in-stock';
        $status_text = 'In Stock';
    } elseif ($item['closing_balance'] > 0) {
        $status_class = 'low-stock';
        $status_text = 'Low Stock';
    } else {
        $status_class = 'out-of-stock';
        $status_text = 'Out of Stock';
    }
    
    $balance = $item['opening_balance'] + $item['total_in'] - $item['total_out'];
    
    $pdf_html .= '<tr>
        <td>'.htmlspecialchars($item['item_name']).'</td>
        <td>'.htmlspecialchars($item['unit_name']).'</td>
        <td>'.$item['opening_balance'].'</td>
        <td class="text-success">+'.$item['total_in'].'</td>
        <td class="text-danger">-'.$item['total_out'].'</td>
        <td class="value-cell">'.$balance.'</td>
        <td>'.number_format(StoreController::getUnitePrice($db, (int)$item['item']), 2).'</td>
        <td class="value-cell">'.$item['inventory_value'].'</td>
        <td>
            <span class="status-indicator '.$status_class.'"></span>
            '.$status_text.'
        </td>
    </tr>
    ';
}

$pdf_html .= '<tr>
<td colspan="7"><strong>Total</strong></td><td><strong>'. number_format($total_value,3) .'</strong></td>
<td></td>
        </tr></tbody>
    </table>
</body>
</html>';

$dompdf->loadHtml($pdf_html);

try {
    // Render the HTML as PDF
    $dompdf->render();
    
    // Clear output buffer to prevent any stray output
    ob_clean();
    
    // Output the generated PDF to Browser
    $dompdf->stream('inventory-balance-'.date('Ymd').'.pdf', ['Attachment' => 0]);
    exit;
} catch (Exception $e) {
    // Clean buffer and show error
    ob_end_clean();
    die("Error generating PDF: " . $e->getMessage());
}


// $dompdf->loadHtml($pdf_html);
// // $dompdf->render();
// // echo $pdf_html; // For debugging, you can echo the HTML to see if it renders correctly

// // Output the generated PDF
// $dompdf->stream('inventory-balance-'.date('Ymd').'.pdf', ['Attachment' => 0]);
// exit;
?>