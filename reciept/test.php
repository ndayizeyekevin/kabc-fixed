<?php

// Or, if you're using the downloaded version, include the DOMPDF library
// require_once 'dompdf/autoload.inc.php';

// Use the DOMPDF namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Set up DOMPDF options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// HTML content for the PDF
$html = '<html><body>';
$html .= '<h1>This is a test PDF document</h1>';
$html .= '<p>This PDF is generated using DOMPDF and sent to the printer.</p>';
$html .= '</body></html>';

// Load HTML content into DOMPDF
$dompdf->loadHtml($html);

// (Optional) Set paper size
$dompdf->setPaper('A4', 'portrait');

// Render PDF (first pass)
$dompdf->render();

// Output the generated PDF to a file
$output = $dompdf->output();
file_put_contents('/path/to/your/file.pdf', $output);

// Now, send the file to the printer using a system command

// Specify the printer name (Linux example: 'lp', Windows example: '\\\\printer_name')
$printer = 'printer_name';  // Replace with your printer's name
$file = '/path/to/your/file.pdf';  // Path to the generated PDF file

// Command to send the file to the printer (Linux example: using 'lp' or 'lpr' command)
$command = "lp -d  $file"; // For Linux
// For Windows, use the print command instead
// $command = "print /D:$printer $file";

// Execute the print command
$output = shell_exec($command);

// Optional: Display the output (for debugging)
echo "Output: " . $output;
?>
