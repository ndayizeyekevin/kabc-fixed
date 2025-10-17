<?php
include('dompdf/autoload.inc.php'); 
include('phpqrcode/qrlib.php');
use Dompdf\Dompdf; 
$dompdf = new Dompdf();
$html = '';

$img = '<img src="http://localhost/resto/reciept/rra.jpg" style="width: 40px;height: 40px;">';

$html .= '<div id="container" style="width: 100%; border: 1px solid black; margin: 0;">

<table border="0" align="center" width="100%">
    <tr>
        <td align="center">
            '.$img.'
        </td>
    </tr>
    <tr>
        <td align="center" style="display:grid;">
           <span>Centre Saint Paul</span><br>
           <span>KG 13 Avenue 22, Kigali, Rwanda</span><br>
           <span>Tin: 111477597</span><br>
           
        </td>
    </tr>
    
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
    <tr>
        <td align="center" style="display: grid;">
            <span>Client Tin:103149400</span><br>
            <span>Client Name: -</span>
            

        </td>
    </tr>
    
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
    
</table>
<table border="0"  align="center" width="100%">
    <tr>
        <td align="left" style="padding-left: 15px;">
            FANTA<br>
            1000.00x
        </td>
        <td align="center">
            <br>
            9.00
        </td>
        <td align="center">
            <br>
            9,000.00B
        </td>
    </tr>
<tr>
    <td align="left" style="padding-left: 15px;">
        discount<br>
        -0.00.%
    </td>
    <td align="center">
        <br>
        
    </td>
    <td align="center">
        <br>
        0.00
    </td>
</tr>
    <tr>
        <td colspan="3"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;"><b>TOTAL</b></td>
    <td align="center"><b>9,000.00</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL</td>
    <td align="center">A-EX 0.00</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL B-18.00%</td>
    <td align="center">9,000.00</td>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX B</td>
    <td align="center">1,620.00</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX</td>
    <td align="center">1,620.00</td>
</tr>
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;">CASH</td>
    <td align="right" style="padding-right: 20px;"> 9,000.00</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">ITEMS NUMBER</td>
    <td align="right" style="padding-right: 20px;">1</td>
</tr>
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="center" colspan="2">SDC INFORMATION</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">Date: '.date("Y-m-d").'</td>
    <td align="center">Time: '.date("H:i:s").'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">SDC ID:</td>
    <td align="center">SDC001000001</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">RECEIPT NUMBER:</td>
    <td align="center">168/258 NS</td>
</tr>
</table>
<table border="0"  align="center" width="100%">
<tr>
    <td align="center">Internal Data:</td>
</tr>
<tr>
    <td align="center">TE68-SLA2-34J5-EAV3-N569-88LJ-Q7</td>
</tr>
<tr>
    <td align="center">Receipt Signature:</td>
</tr>
<tr>
    <td align="center">V249-J39C-FJ48-HE2W</td>
</tr>
<tr>
<td align="center">
<img src="http://localhost/resto/reciept/qr.png">

</td>
</tr>
<tr>
    <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;">RECEIPT NUMBER:</td>
    <td align="right" style="padding-right: 20px;">152</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">DATE: '.date("Y-m-d").'</td>
    <td align="right" style="padding-right: 20px;">TIME: '.date("H:i:s").'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">MRC:</td>
    <td align="right" style="padding-right: 20px;">AAACC123456</td>
</tr>
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="center">THANK YOU</td>
</tr>
<tr>
    <td align="center">END OF RECEIPT</td>
</tr>
</table>
</div>
';
$dompdf->loadHtml($html); 
 
// (Optional) Setup the paper size and orientation 


// $dompdf->setPaper('A4', 'portrait'); 
$customPaper = array(0,20,300,900);
$dompdf->set_paper($customPaper);
$dompdf->set_option('isRemoteEnabled', true);
// Render the HTML as PDF 
$dompdf->render(); 
 
// Output the generated PDF (1 = download and 0 = preview) 
$dompdf->stream("receipt", array("Attachment" => 0));