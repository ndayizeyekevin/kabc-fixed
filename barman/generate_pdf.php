<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

include('../reciept/dompdf/autoload.inc.php');
require_once '../inc/DBController.php';
// require_once '../vendor/autoload.php';
require_once 'SalesReportController.php';

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

$controller = new SalesReportController($db);

// Get filter parameters from session
$from = $_SESSION['date_from'] ?? date('Y-m-d');
$to = $_SESSION['date_to'] ?? date('Y-m-d');
$productId = $_SESSION['product_id'] ?? null;

// Fetch sales data
$query = "SELECT *, SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
         INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
         WHERE DATE(tbl_cmd_qty.created_at) BETWEEN ? AND ? 
         AND menu.cat_id = '2'";
$params = [$from, $to];

if($productId) {
    $query .= " AND menu.menu_id = ?";
    $params[] = $productId;
}

$query .= " GROUP BY cmd_item";

$stmt = $db->prepare($query);
$stmt->execute($params);
$salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new \Dompdf\Dompdf();
$total = 0;
$itemsSold = 0;

// die();
foreach($salesData as $row) {
    $amount = $row['menu_price'] * $row['totqty'];
    $total += $amount;
    $itemsSold += $row['totqty'];
}

// Company information
$companyName = "Centre Saint Paul Kigali Ltd";
$companyAddress = "KN 31 St, Kigali, Rwanda";
$companyPhone = "+250 785 285 341 / +250 789 477 745";
$companyEmail = "TIN/VAT Number: 111477597";

// Get logo as base64 encoded string
$logoUrl = 'https://saintpaul.gope.rw/img/logo.png';
$logoData = @file_get_contents($logoUrl);
$logoBase64 = $logoData ? base64_encode($logoData) : '';

// Build HTML with embedded logo
$html = '<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
       body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { display: flex; margin-bottom: 20px; border-bottom: 2px solid #2c3e50; padding-bottom: 15px; }
        .logo { width: 100px; margin-right: 20px; }
        .company-info { flex-grow: 1; }
        .company-name { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 0; }
        .company-details { font-size: 12px; color: #666; margin: 5px 0; }
        .report-title { text-align: center; font-size: 24px; color: #2c3e50; margin: 10px 0; }
        
        .summary-section {
    margin: 15px 0;
}

.summary-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 14px;
}

.summary-table td {
    padding: 8px 10px;
    border: none;
}

.summary-label {
    font-weight: bold;
    color: #2c3e50;
    white-space: nowrap;
    padding-right: 5px !important;
    width: 1%;
}

.summary-value {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6 !important;
    border-radius: 4px;
    text-align: right;
    font-weight: bold;
    padding-right: 15px !important;
    width: 15%;
}
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
            font-size: 14px;
        }
        .detail-item {
            padding: 5px 0;
        }
        
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            font-size: 12px;
        }
        th { 
            background-color: #2c3e50; 
            color: white; 
            padding: 10px; 
            text-align: left; 
        }
        td { 
            padding: 8px; 
            border: 1px solid #dee2e6; 
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-row { 
            font-weight: bold; 
            background-color: #e9ecef; 
        }
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-style: italic;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">

</div>
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 15px;">
    <tr>
        <!-- Company Information -->
        <td width="60%" style="vertical-align: top; padding-right: 20px;">
        ';

        // Add logo if available
        if ($logoBase64) {
            $html .= '<img src="data:image/png;base64,'.$logoBase64.'" alt="Company Logo" style="height: 80px;">';
        } else {
            $html .= '<div style="height: 80px; width: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666; font-size: 12px;">LOGO</div>';
        }
        $html .= ' <h1 class="company-name" style="margin: 0 0 5px 0; font-size: 22px;">'.$companyName.'</h1>
            <div class="company-details" style="font-size: 12px; margin-bottom: 3px;">'.$companyAddress.'</div>
            <div class="company-details" style="font-size: 12px;">
                Phone: '.$companyPhone.' <br />'.$companyEmail.'
            </div>
        </td>
        
        <!-- Report Details -->
        <td width="40%" style="vertical-align: top; border-left: 1px solid #eee; padding-left: 15px;">
            <table width="100%" cellpadding="1" cellspacing="0">
                <tr>
                    <td width="35%" style="font-weight: bold; font-size: 11px;">Date Range:</td>
                    <td width="65%" style="font-size: 11px;">'.$from.' to '.$to.'</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 11px;">Report Type:</td>
                    <td style="font-size: 11px;">'.($productId ? 'Product Specific' : 'All Products').'</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 11px;">Generated:</td>
                    <td style="font-size: 11px;">'.date('Y-m-d H:i:s').'</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 11px;">Period:</td>
                    <td style="font-size: 11px;">'.getDateRangeLength($from, $to).'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
    
    </div>
 <h2 class="report-title">SALES  REPORT</h2>
    
  <div class="summary-section">
    <h3>Report Summary</h3>
    <table class="summary-table">
        <tr>
            <td class="summary-label">Total Sales</td>
            <td class="summary-value">RWF '.number_format($total, 2).'</td>
            <td class="summary-label">Items Sold</td>
            <td class="summary-value">'.number_format($itemsSold).'</td>
            <td class="summary-label">Unique Products</td>
            <td class="summary-value">'.count($salesData).'</td>
        </tr>
    </table>
</div>
    <br><br>
    <hr/>
   

    <table class = "data-table"> 
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Product Name</th>
                <th>Description</th>
                <th class="text-right">Unit Price (RWF)</th>
                <th class="text-center">Quantity</th>
                <th class="text-right">Amount (RWF)</th>
            </tr>
        </thead>
        <tbody>';

// Loop through sales data and add rows to table
foreach($salesData as $row) {
    $amount = $row['menu_price'] * $row['totqty'];
    
    $html .= '
            <tr>
                <td>'.$row['item_code'].'</td>
                <td>'.$row['menu_name'].'</td>
                <td>'.$row['menu_desc'].'</td>
                <td class="text-right">'.number_format($row['menu_price'], 2).'</td>
                <td class="text-center">'.$row['totqty'].'</td>
                <td class="text-right">'.number_format($amount, 2).'</td>
            </tr>';
}

$html .= '
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>GRAND TOTAL</strong></td>
                <td class="text-right"><strong>RWF '.number_format($total, 2).'</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Generated by Sales Report System | '.$companyName.'</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>';

// echo $html;
// die();
// Set PDF options
$options = new \Dompdf\Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$pdf->setOptions($options);

$pdf->loadHtml($html);
$pdf->setPaper('A4', 'portrait');
$pdf->render();

// Clear output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Stream PDF
$pdf->stream("sales_report_".date('Ymd_His').".pdf", [
    "Attachment" => false,
    "isRemoteEnabled" => true
]);
exit;
// Helper function to calculate date range length
function getDateRangeLength($start, $end) {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $interval = $startDate->diff($endDate);
    
    $days = $interval->days;
    if ($days === 0) {
        return 'Same Day';
    } elseif ($days === 1) {
        return '1 Day';
    } else {
        return $days . ' Days';
    }
}
