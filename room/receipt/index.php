<?php
require_once 'dompdf/autoload.inc.php'; 
use Dompdf\Dompdf; 
$dompdf = new Dompdf();





$img = '<img src="qr_img.php?d=these are my data" style="width: 100px;height: 100px;">';
$html = '<div id="container" style="width: 100%; border: 1px solid black; margin: 0;">

<table border="0" align="center" width="100%">
    <tr>
        <td align="center">
            '.$img.'
        </td>
    </tr>
    <tr>
        <td align="center" style="display:grid;">
           <span>AGROPROCESSING TRUST CORPORATION ltd</span><br>
           <span>Kigali, City</span><br>
           <span>Tin: 103454201</span><br>
           
        </td>
    </tr>
    
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
    <tr>
        <td align="center" style="display: grid;">
            <span>Welcome to our shop</span><br>
            <span>CLIENT ID: 0000000000</span>
            

        </td>
    </tr>
    
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
    
</table>
<table border="0"  align="center" width="100%">
    <tr>
        <td align="left" style="padding-left: 15px;">
            UREA<br>
            640.00x
        </td>
        <td align="center">
            <br>
            50
        </td>
        <td align="center">
            <br>
            32,000.00A-EX
        </td>
    </tr>
    <tr>
        <td align="left" style="padding-left: 15px;">
            DAP<br>
            722.00x
        </td>
        <td align="center">
            <br>
            10
        </td>
        <td align="center">
            <br>
            7,220.00A-EX
        </td>
    </tr>
    <tr>
        <td align="left" style="padding-left: 15px;">
            NPK-17<br>
            654.00x
        </td>
        <td align="center">
            <br>
            70.00
        </td>
        <td align="center">
            <br>
            45,780.00A-EX
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;"><b>TOTAL</b></td>
    <td align="center"><b>85,000.00</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL A-EX</td>
    <td align="center">85,000.00</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL B-18.00%</td>
    <td align="center">0.00</td>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX B</td>
    <td align="center">0.00</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX</td>
    <td align="center">0.00</td>
</tr>
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;">CASH</td>
    <td align="right" style="padding-right: 20px;"> 85,000.00</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">ITEMS NUMBER</td>
    <td align="right" style="padding-right: 20px;">3</td>
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
    <td align="left" style="padding-left: 25px;">Date: 26/07/2012</td>
    <td align="center">Time: 11:07:35</td>
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
    <td align="center">asfasdf</td>
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
    <td align="left" style="padding-left: 25px;">DATE: 25/5/2012</td>
    <td align="right" style="padding-right: 20px;">TIME: 11:09:32</td>
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
    <td align="center">COME BACK AGAIN</td>
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
$dompdf->stream("codexworld", array("Attachment" => 0));