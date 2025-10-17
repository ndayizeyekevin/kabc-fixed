<?php
// Include the FPDF class
require('fpdf.php');

// Create a new PDF document with custom page size (4x6 inches)
$pdf = new FPDF('P', 'in', array(4, 6)); // 'P' for Portrait, 'in' for inches, array(4, 6) for 4x6 inches

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('Arial', 'B', 12);

// Add invoice title
$pdf->Cell(0, 0.5, 'Invoice', 0, 1, 'C');


// Add invoice details (example)
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2, 0.5, 'Date: ' . date('Y-m-d'), 0, 1);
$pdf->Cell(2, 0.5, 'Invoice Number: 12345', 0, 1);
$pdf->Cell(2, 0.5, 'Customer: John Doe', 0, 1);

// Add more invoice content like items, prices, and totals
$pdf->Ln(0.5);

// Example: Itemized list
$pdf->Cell(1.5, 0.5, 'Item', 1);
$pdf->Cell(1.5, 0.5, 'Price', 1, 1);

// Example item rows
$pdf->Cell(1.5, 0.5, 'Product A', 1);
$pdf->Cell(1.5, 0.5, '$10.00', 1, 1);

$pdf->Cell(1.5, 0.5, 'Product B', 1);
$pdf->Cell(1.5, 0.5, '$15.00', 1, 1);

// Add total amount
$pdf->Cell(1.5, 0.5, 'Total:', 1);
$pdf->Cell(1.5, 0.5, '$25.00', 1, 1);

// Output the PDF to the browser
$pdf->Output();
?>
