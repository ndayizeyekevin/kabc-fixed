<?php
// session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Include necessary files
require_once '../inc/config.php';
require_once '../dompdf/autoload.inc.php';

require_once './controllers/storeController.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Get parameters
$selection = $_GET['selection'] ?? null;
$from = $_GET['from'] ?? date('Y-m-d');
$to = $_GET['to'] ?? date('Y-m-d');

// Function definitions with PDO
function getItemPrice($id, $db) {
     global $db;
    return StoreController::getUnitePrice($db, $id);
    // try {
    //     $stmt = $db->prepare("SELECT price FROM tbl_items WHERE item_id = :id");
    //     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $defaultPrice = $stmt->fetchColumn();

    //     $stmt = $db->prepare("SELECT new_price FROM tbl_progress WHERE item = :id ORDER BY prog_id DESC LIMIT 1");
    //     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $newPrice = $stmt->fetchColumn();

    //     if ($newPrice > 0) {
    //         return $newPrice;
    //     }

    //     return $defaultPrice ?: 0;
    // } catch (PDOException $e) {
    //     error_log("Error getting item price: " . $e->getMessage());
    //     return 0;
    // }
}

function getDep($id) {
    if($id == 4) return "Kitchen";
    if($id == 5) return "BAR";
    if($id == 13) return "House Keeper";
    return "Unknown";
}

function getItemName($id, $db) {
    try {
        $stmt = $db->prepare("SELECT item_name FROM tbl_items WHERE item_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: "Unknown Item";
    } catch (PDOException $e) {
        error_log("Error getting item name: " . $e->getMessage());
        return "Unknown Item";
    }
}

// Create PDF options
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultPaperSize', 'A4');
$options->set('defaultPaperOrientation', 'landscape'); // Changed to landscape for better fit

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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left;
            border: 1px solid #000 !important;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
        }
        .department-header {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .department-total {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .grand-total {
            background-color: #d0d0d0;
            font-weight: bold;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        @page {
            margin: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <div class="company-logo">
            <img src="<?= $logo_png ?>" alt="Company Logo" style="width: 100px; height: auto;">
        </div>
        <div class="company-name"><?= $company_name ?></div>
        <div><?= $company_address ?></div>
        <div>TIN/VAT Number: <?= $company_tin ?></div>
        <div>Phone: <?= $company_phone ?></div>
        <div class="report-title">STOCK REPORT BY DEPARTMENT</div>
        <div class="period">Period: From ' . htmlspecialchars($from) . ' To ' . htmlspecialchars($to) . '</div>';

if ($selection) {
    $pdf_html .= '<div class="period">Department: ' . htmlspecialchars(getDep($selection)) . '</div>';
}

$pdf_html .= '</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Department</th>
                <th>Item Name</th>
                <th>QTY</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>';

// Calculate data with PDO and organize by department
$grandTotal = 0;
$departmentData = [];

try {
    if($selection){
        $stmt = $db->prepare("SELECT req.requested_date, req.department, r.items, r.quantity, SUM(r.quantity) AS total_qty 
            FROM tbl_requests AS req
            INNER JOIN tbl_request_details AS r ON req.req_code = r.req_code
            WHERE req.department = :dept 
            AND req.requested_date BETWEEN :from_date AND :to_date AND req.status !=3 GROUP BY req.department, r.items
            ORDER BY req.department, req.requested_date
        ");
        $stmt->bindParam(':dept', $selection, PDO::PARAM_INT);
    } else {
        $stmt = $db->prepare("SELECT req.requested_date, req.department, r.items, r.quantity, SUM(r.quantity) AS total_qty 
            FROM tbl_requests AS req
            INNER JOIN tbl_request_details AS r ON req.req_code = r.req_code
            WHERE req.requested_date BETWEEN :from_date AND :to_date AND req.status !=3 GROUP BY req.department, r.items
            ORDER BY req.department, req.requested_date
        ");
    }
    
    $stmt->bindParam(':from_date', $from);
    $stmt->bindParam(':to_date', $to);
    $stmt->execute();
    
    // Organize data by department
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $departmentId = $row['department'];
        if (!isset($departmentData[$departmentId])) {
            $departmentData[$departmentId] = [
                'name' => getDep($departmentId),
                'items' => [],
                'total' => 0
            ];
        }
        
        $itemPrice = getItemPrice($row['items'], $db);
        $totalPrice = $itemPrice * $row['total_qty'];
        
        $departmentData[$departmentId]['items'][] = [
            'date' => $row['requested_date'],
            'item_name' => getItemName($row['items'], $db),
            'quantity' => $row['total_qty'],
            'unit_price' => $itemPrice,
            'total_price' => $totalPrice
        ];
        
        $departmentData[$departmentId]['total'] += $totalPrice;
        $grandTotal += $totalPrice;
    }
    
    // Generate table rows with department grouping
    foreach ($departmentData as $deptId => $dept) {
        $pdf_html .= '<tr class="department-header">
            <td colspan="6">DEPARTMENT: ' . htmlspecialchars($dept['name']) . '</td>
        </tr>';
        
        foreach ($dept['items'] as $item) {
            $pdf_html .= '<tr>
                <td>' . htmlspecialchars($item['date']) . '</td>
                <td>' . htmlspecialchars($dept['name']) . '</td>
                <td>' . htmlspecialchars($item['item_name']) . '</td>
                <td>' . $item['quantity'] . '</td>
                <td class="text-right">' . number_format($item['unit_price'], 2) . '</td>
                <td class="text-right">' . number_format($item['total_price'], 2) . '</td>
            </tr>';
        }
        
        $pdf_html .= '<tr class="department-total">
            <td colspan="5" class="text-right">' . htmlspecialchars($dept['name']) . ' Subtotal:</td>
            <td class="text-right">' . number_format($dept['total'], 2) . ' RWF</td>
        </tr>';
    }
    
} catch (PDOException $e) {
    $pdf_html .= '<tr><td colspan="6">Error loading data: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}

$pdf_html .= '<tr class="grand-total">
        <td colspan="5" class="text-right">GRAND TOTAL:</td>
        <td class="text-right">' . number_format($grandTotal, 2) . ' RWF</td>
    </tr>';

$pdf_html .= '</tbody>
    </table>
    <div class="footer">
        Generated on: ' . date('Y-m-d H:i:s') . ' | Centre Saint Paul Kigali Ltd
    </div>
</body>
</html>';

// Load HTML content
$dompdf->loadHtml($pdf_html);

try {
    // Render the HTML as PDF
    $dompdf->render();
    
    // Output the generated PDF to Browser
    $dompdf->stream('stock-report-' . date('Ymd-His') . '.pdf', ['Attachment' => 0]);
    exit;
} catch (Exception $e) {
    die("Error generating PDF: " . $e->getMessage());
}
?>