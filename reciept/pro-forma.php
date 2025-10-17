<?php
// =======================================================================
// Start output buffering to prevent "headers already sent" errors
// =======================================================================
ob_start();

// Enable all error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session and include necessary libraries
@session_start();
include('dompdf/autoload.inc.php');
use Dompdf\Dompdf;
use Dompdf\Options;

// Configure DomPDF options for better rendering
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);

// Include the database connection file
include '../inc/DBController.php';

// Get data from session and calculate totals
$_SESSION['servant_name'] = $_SESSION['f_name'] . " " . $_SESSION['l_name'];
// Assuming $ebmdata is the HTML string from the session
$ebmdata = isset($_SESSION['ebmdata']) ? $_SESSION['ebmdata'] : '';
$tableno = isset($_SESSION['tableno']) ? $_SESSION['tableno'] : '';
$total = $_SESSION['total'] ?? 0;
$tax = $total * 18 / 100;
$subtotal = $total - $tax;
$tin = isset($_SESSION['tin']) ? $_SESSION['tin'] : '111477597';
$servant = isset($_SESSION['servant_name']) ? $_SESSION['servant_name'] : '';

// Get the order information from the database
$no = '';
if (isset($_REQUEST['ref'])) {
    // SECURITY WARNING: This is vulnerable to SQL injection.
    $sql = "SELECT * FROM tbl_cmd WHERE OrderCode = '" . $_REQUEST['ref'] . "'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $no = $row["id"];
        }
    }
}

// NEW LOGIC: Use a regular expression to add the item numbers.
if (!empty($ebmdata)) {
    $ebmdata_with_numbers = '';
    $rows = explode('</tr>', $ebmdata);
    $item_number = 1;
    foreach ($rows as $row) {
        $trimmed_row = trim($row);
        if (!empty($trimmed_row)) {
            // Add the item number to the beginning of the row
            $row_with_number = '<tr><td>' . $item_number . '. ' . substr($trimmed_row, 4);
            $ebmdata_with_numbers .= $row_with_number . '</tr>';
            $item_number++;
        }
    }
    $ebmdata = $ebmdata_with_numbers;
}

// A4 Invoice template with improved design
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #' . $no . '</title>
    <style>
        @page {
            margin: 1cm;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            font-size: 12pt;
        }
        .invoice-box {
            width: 100%;
            margin: auto;
            padding: 15px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 12pt;
            line-height: 1.5;
        }
        .company-info {
            text-align: right;
        }
        .logo {
            text-align: left;
        }
        .logo img {
            max-width: 100px;
            height: auto;
        }
        .invoice-details {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f6f6f6;
            border-radius: 5px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 10px;
        }
        .items-table th {
            background-color: #f8f8f8;
            border-bottom: 2px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 10pt;
        }
        .totals-table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px;
        }
        .totals-table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .payment-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: 5px;
            border: 1px dashed #ddd;
        }
        .payment-info h4 {
            margin: 0 0 10px 0;
            color: #555;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #777;
            font-size: 10pt;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .bank-info {
            margin-top: 10px;
            border-top: 1px dashed #ddd;
            padding-top: 10px;
        }
        .bank-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .bank-info td {
            padding: 5px;
            vertical-align: top;
        }
        .highlight {
            color: #2a75b3;
            font-weight: bold;
        }
        h2, h3, h4 {
            color: #2a75b3;
            margin: 5px 0;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table width="100%">
            <tr>
                <td width="40%" class="logo">
                    <img src="'.$company_logo.'" alt="Company Logo">
                </td>
                <td width="60%" class="company-info">
                    <h2>'.$company_name.'</h2>
                    <p>
                        '.$company_address.'<br>
                        TIN: '.$company_tin.'<br>
                        Email: '.$company_email.'<br>
                        Tel: '.$company_phone.'
                    </p>
                </td>
            </tr>
        </table>
        
        <div class="invoice-details">
            <table width="100%">
                <tr>
                    <td width="60%">
                        <strong>Invoice #:</strong> ' . $no . '<br>
                        <strong>Date:</strong> ' . date("Y-m-d") . '<br>
                        <strong>Time:</strong> ' . date("H:i:s") . '
                    </td>
                    <td width="40%" style="text-align: right;">
                        <strong>Table:</strong> ' . $tableno . '<br>
                        <strong>Served by:</strong> ' . $servant . '<br>
                        <strong>MOMO CODE:</strong> <span class="highlight">007973</span>
                    </td>
                </tr>
            </table>
        </div>
        
        <h3 style="border-bottom: 1px solid #eee; padding-bottom: 5px;">Order Details</h3>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="45%">Item</th>
                    <th width="15%">Quantity</th>
                    <th width="20%">Unit Price</th>
                    <th width="20%">Amount</th>
                </tr>
            </thead>
            <tbody>
                ' . $ebmdata . '
            </tbody>
        </table>
        
        <div class="clearfix"></div>
        
        <table class="totals-table">
            <tr>
                <td align="left"><strong>Subtotal:</strong></td>
                <td align="right">' . number_format($subtotal, 2) . ' RWF</td>
            </tr>
            <tr>
                <td align="left"><strong>VAT (18%):</strong></td>
                <td align="right">' . number_format($tax, 2) . ' RWF</td>
            </tr>
            <tr class="total">
                <td align="left"><strong>TOTAL:</strong></td>
                <td align="right">' . number_format($total, 2) . ' RWF</td>
            </tr>
        </table>
        
        <div class="clearfix"></div>
        
        <div class="payment-info">
            <h4>Payment Information</h4>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <strong>TIP:</strong> ______________________<br>
                        <strong>Room:</strong> ______________________
                    </td>
                    <td width="50%">
                        <strong>Name:</strong> ______________________<br>
                        <strong>Signature:</strong> ______________________
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            <p><strong>Thank You for using our services</strong></p>
            <p><strong>'.$company_name.'</strong> | '.$company_address.' | TIN: '.$company_tin.'</p>
            <div class="bank-info">
                <table>
                    <tr>
                        <td width="50%">
                            <strong style="color:black;">BANK NAME:</strong> <u>BANK OF KIGALI (BK)</u><br>
                            <strong style="color:black;">SWIFT CODE:</strong> BKIGRWRW<br>
                            <strong style="color:black;">ACCOUNT NUMBER:</strong> XXXXX-XXXXXXX-XX /RW
                        </td>
                        <td width="50%">
                            <strong style="color:black;">BANK NAME:</strong> <u>BANK OF KIGALI (BK)</u><br>
                            <strong style="color:black;">SWIFT CODE:</strong> BKIGRWRW<br>
                            <strong style="color:black;">ACCOUNT NUMBER:</strong> XXXXX-XXXXXXX-XX /USD
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
ob_clean();
$dompdf->stream("Invoice_" . $no . ".pdf", array("Attachment" => 0));
?>