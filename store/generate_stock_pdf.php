<?php
include '../inc/conn.php';
require_once '../inc/config.php'; // Make sure this creates a PDO connection

require_once './controllers/storeController.php';

 function getDep($id){
include '../inc/conn.php';		
$sql = "SELECT * FROM category where cat_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['cat_name'];
  }
}
}


function getItemPrice($id){
    global $db;
    return StoreController::getUnitePrice($db, $id);
	
// include '../inc/conn.php';	

// $item = "SELECT * FROM tbl_items where item_id='$id' ";
// $result = $conn->query($item);

// $tbl_progress = $conn ->query ("SELECT * FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1;");

// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {
//     while($row2 = $tbl_progress->fetch_assoc()){
//         return  $row2['new_price'] > 0 ? $row2['new_price'] : $row['price'];
//     }
//   }
// }

}


function getItemName($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['item_name'];
  }
}

}



// Get parameters from URL
$from = $_GET['s'] ?? date('Y-m-d');
$to = $_GET['to'] ?? date('Y-m-d');
$item = $_GET['item'] ?? 'all';

// Start building HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { text-align: right; margin-top: 20px; }
        .category-header { background-color: #e0e0e0; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
    <div class="company-logo">
            <img src="<?= $logo_png ?>" alt="Company Logo" style="width: 100px; height: auto;">
        </div>
        <div class="company-name"><?= $company_name ?></div>
        <div><?= $company_address ?></div>
        <div>TIN/VAT Number: <?= $company_tin ?></div>
        <div>Phone: <?= $company_phone ?></div>
        <h2>COST REPORT BY CATEGORY</h2>
        <p>From: '.$from.' To: '.$to.'</p>
    </div>';

// Query data (same as your original code)
$category_data = [];
$inAmounttotal = 0;

// $result2 = $db->prepare("
//     SELECT tbl_requests.requested_date, tbl_items.cat_id, tbl_items.item_id, tbl_items.item_name,
//           tbl_items.price, tbl_request_details.quantity, SUM(tbl_request_details.quantity) AS total_qqty
//     FROM tbl_requests 
//     INNER JOIN tbl_request_details ON tbl_requests.req_code = tbl_request_details.req_code
//     INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
//     WHERE DATE(tbl_requests.requested_date) >= ? AND DATE(tbl_requests.requested_date) <= ? AND tbl_requests.status !=3 GROUP BY tbl_items.cat_id, tbl_items.item_id");

// $filterDep = $selectedDep != 0 ? "AND tbl_requests.department = '$selectedDep'" : "";
$result2 = $db->prepare("SELECT tbl_requests.requested_date, tbl_items.cat_id, category.cat_name, tbl_requests.department, tbl_items.item_id AS item_id, tbl_items.item_name,
            tbl_items.price, tbl_request_details.quantity, SUM(tbl_request_details.quantity) AS total_quantity
            FROM tbl_requests INNER JOIN tbl_request_details ON tbl_requests.req_code = tbl_request_details.req_code
            INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id INNER JOIN category ON category.cat_id = tbl_items.cat_id
            WHERE DATE(tbl_requests.requested_date) >= ? AND DATE(tbl_requests.requested_date) <= ? AND tbl_requests.status !=3 GROUP BY tbl_requests.department, tbl_items.cat_id, tbl_items.item_id");
$result2->execute([$from, $to]);

while ($row = $result2->fetch()) {
    $cat_id = $row['cat_id'];
    if (!isset($category_data[$cat_id])) {
        $category_data[$cat_id] = [
            'cat_name' => getDep($cat_id),
            'items' => [],
            'subtotal' => 0,
        ];
    }

    // $unit_price = $row['price'];
    $unit_price = StoreController::getUnitePrice($db, $row['item_id']);
    $total_price = $unit_price * $row['total_quantity'];
    $inAmounttotal += $total_price;
    $category_data[$cat_id]['subtotal'] += $total_price;

    $category_data[$cat_id]['items'][] = [
        'item_name' => $row['item_name'],
        'quantity' => $row['total_quantity'],
        'unit_price' => $unit_price,
        'total_price' => $total_price,
    ];
}

// Build table HTML
$html .= '<table>
    <thead>
        <tr>
            <th>Category</th>
            <th>Item Name</th>
            <th>Qty</th>
            <th>U.P</th>
            <th>T.P</th>
        </tr>
    </thead>
    <tbody>';

foreach ($category_data as $cat) {
    $html .= '<tr class="category-header">
        <td colspan="5"><strong>Category: '.$cat['cat_name'].'</strong></td>
    </tr>';
    
    foreach ($cat['items'] as $item) {
        $html .= '<tr>
            <td>'.$cat['cat_name'].'</td>
            <td>'.$item['item_name'].'</td>
            <td>'.$item['quantity'].'</td>
            <td>'.number_format($item['unit_price'], 2).'</td>
            <td>'.number_format($item['total_price'], 2).'</td>
        </tr>';
    }
    
    $html .= '<tr>
        <td colspan="5" class="text-right"><b>Subtotal for '.$cat['cat_name'].': '.number_format($cat['subtotal'], 2).' RWF</b></td>
    </tr>';
}

$html .= '<tr>
    <td colspan="5" class="text-right"><b>Total: '.number_format($inAmounttotal, 2).' RWF</b></td>
</tr>';

$html .= '</tbody>
</table>
<div class="footer">
    <p>Generated on: '.date('Y-m-d H:i:s').'</p>
</div>
</body>
</html>';

// Use DOMPDF to generate PDF
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Create PDF options
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultPaperSize', 'A4');
$options->set('defaultPaperOrientation', 'portrait');
// $options = new Options();
// $options->set('isRemoteEnabled', true);
// $options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
// echo $html; // For debugging, you can echo the HTML to see if it renders correctly

// Output the generated PDF
$dompdf->stream("stock_report_".date('Y-m-d').".pdf", [
    "Attachment" => false
]);

exit;
?>