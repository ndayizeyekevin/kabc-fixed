<?php session_start();
include('dompdf/autoload.inc.php');
include('phpqrcode/qrlib.php');
use Dompdf\Dompdf;

include  '../inc/conn.php';


$dompdf = new Dompdf();
$html = '';
// Sample data from session (modify this with actual session data)
$bon = $_SESSION['bon'];
$tax = $_SESSION['total'] * 18/100;
$total = $_SESSION['total'];
$no = $_SESSION['no'];
$printedIdresto = $_SESSION['printedIdresto'];
$tin = $_SESSION['tin'];
$servant = $_SESSION['servant_name'];
$tableno = $_SESSION['tableno'];



$printedIdresto = substr($printedIdresto, 0, -1); 


$sql = "UPDATE tbl_cmd_qty SET printed = '1' WHERE  cmd_qty_id IN ($printedIdresto)";

if ($conn->query($sql) === TRUE) {
 // echo "Record updated successfully";
} else {
 // echo "Error updating record: " . $conn->error;
}



$img = '<img src="<?= $logo_png; ?>" style="width: 100px;height: 100px;">';

$html .= '<div id="container" style="width: 100%; border: 0px solid black; margin: 0;">
<table border="0" align="center" width="100%">
 
 
  <tr>
 
        <td align="center" style="display: grid;">
           <span> No '.$printedIdresto.'</span>
        </td>
    </tr>
 
 
 <tr>
 
        <td align="center" style="display: grid;">
           <span> No '.$no.'</span>
        </td>
    </tr>
    
     <tr>
        <td align="center" style="display: grid;">
           <span> Table  '.$tableno.'</span>
        </td>
    </tr>

    <tr>
        <td align="center" style="display: grid;">
           <span> BON DE COMMANDE (KITCHEN) </span>
        </td>
    </tr>
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
</table>
<p>
  '.$bon.'

</p>

<table border="0"  align="center" width="100%">
<tr>
    <td align="center">Requested by:  '.$servant.'</td>
</tr>
</table>

</div>';

$dompdf->loadHtml($html);

// Set paper size (optional, use custom size if needed)
$customPaper = array(0,0,226.8,1000);
$dompdf->set_paper($customPaper);

// Enable remote content (images, etc.)
$dompdf->set_option('isRemoteEnabled', true);

// Render PDF (first pass)
$dompdf->render();

// Alternatively, for Windows, you can use the print command
// $printer = '\\\\printer_name';  // Use the full printer path (example: '\\\\ComputerName\\PrinterName')
// $command = "print /D:$printer $pdfPath";
// shell_exec($command);

// Optionally, output the PDF to the browser for preview
$dompdf->stream("receipt", array("Attachment" => 0));

?>
