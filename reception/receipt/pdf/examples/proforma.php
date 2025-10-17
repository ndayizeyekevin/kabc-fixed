<?php
//require_once('vendor/autoload.php'); // Path to TCPDF autoload file

require_once('tcpdf_include.php');
// Create new TCPDF object
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Invoice');

// Set default header data
$pdf->SetHeaderData('', 0, 'INVOICE', 'Your Company Name', array(0,64,255), array(0,64,128));

// Set margins
$pdf->SetMargins(15, 30, 15); // Left, Top, Right
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Logo
$logo = 'https://example.com/logo.png'; // Path to your logo image
$pdf->Image($logo, 15, 10, 40, 0, 'PNG'); // Position X, Y, width, height, type (image format)

// Invoice title and information
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Invoice #12345', 0, 1, 'C'); // Invoice title

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Date: ' . date("Y-m-d"), 0, 1, 'C');
$pdf->Cell(0, 10, 'Due Date: ' . date("Y-m-d", strtotime('+30 days')), 0, 1, 'C');

// Invoice table
$pdf->Ln(10); // Line break

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(80, 10, 'Description', 1, 0, 'C');
$pdf->Cell(30, 10, 'Unit Price', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total', 1, 1, 'C');

// Sample items (you can dynamically populate this with data from your database)
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(80, 10, 'Product A', 1, 0, 'C');
$pdf->Cell(30, 10, '$20.00', 1, 0, 'C');
$pdf->Cell(30, 10, '2', 1, 0, 'C');
$pdf->Cell(30, 10, '$40.00', 1, 1, 'C');

$pdf->Cell(80, 10, 'Product B', 1, 0, 'C');
$pdf->Cell(30, 10, '$15.00', 1, 0, 'C');
$pdf->Cell(30, 10, '3', 1, 0, 'C');
$pdf->Cell(30, 10, '$45.00', 1, 1, 'C');

// Calculate totals
$total = 40.00 + 45.00;
$tax = $total * 0.18; // 18% tax
$grandTotal = $total + $tax;

// Summary
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(140, 10, 'Subtotal', 1, 0, 'R');
$pdf->Cell(30, 10, '$' . number_format($total, 2), 1, 1, 'C');

$pdf->Cell(140, 10, 'Tax (18%)', 1, 0, 'R');
$pdf->Cell(30, 10, '$' . number_format($tax, 2), 1, 1, 'C');

$pdf->Cell(140, 10, 'Grand Total', 1, 0, 'R');
$pdf->Cell(30, 10, '$' . number_format($grandTotal, 2), 1, 1, 'C');

// Footer
$pdf->SetY(-15);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 10, 'Thank you for your business!', 0, 1, 'C');
$pdf->Cell(0, 10, 'Your Company Name | Address | Contact Info', 0, 1, 'C');

// Output PDF (this will force the file to be downloaded)
$pdf->Output('invoice.pdf', 'I');
?>
