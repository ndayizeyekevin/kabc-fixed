<?php

ob_start();

include('../receipt/dompdf/autoload.inc.php'); 
require_once ("../../inc/config.php");
use Dompdf\Dompdf; 

$css = "
    @page {
        margin: 0;
    }
    body {
        margin: 5px;
        padding: 5px;
    }
";

$sqlseason = $db->prepare("SELECT * FROM system_configuration");
$sqlseason->execute();
$rows = $sqlseason->fetch();

$companyTin = $rows['Tin'];
$msg = $rows['receipt_msg'];
$ebm_version = $rows['ebm_version'];
$cis_version = $rows['version'];
$phone = $rows['phone'];



$dompdf = new Dompdf();
$amount = $_REQUEST['amount'];
$sdc = $_REQUEST['sdc'];

$mrc = $rows['mrc'];
$no = $_REQUEST['no'];
$receipt_no = $_REQUEST['no'];
$tot = $_REQUEST['total'];
$vs = $_REQUEST['vs'];
$tin = $_REQUEST['tin'] =='null' ? '': 'CLIENT TIN: '.preg_replace('/\D/', '',$_REQUEST['tin']);
// $tin = preg_replace('/\D/', '', $tin);
$sign = $_REQUEST['sign'];
$int = $_REQUEST['int'];

$chunkedArray = str_split($int, 4);
$complete_int = implode('-', $chunkedArray);

$chunkedArray = str_split($sign, 4);
$signature = implode('-', $chunkedArray);

$receiptT = $_REQUEST['receiptT'];

$taxA = $_REQUEST['taxA'];
$taxB = $_REQUEST['taxB'];
$taxbleAmount = $_REQUEST['taxbleAmount'];
$totalC = $_REQUEST['totalC'];
$totalD = $_REQUEST['totalD'];

$tax = $_REQUEST['tax'];
$receiptNo = $_REQUEST['receiptNo'];


$clientPhone = isset($_REQUEST['clMob']) ? 'CLIENT MOBILE: '.preg_replace('/\D/', '',$_REQUEST['clMob']) : '';
// $clientPhone = preg_replace('/\D/', '', $clientPhone);
$names = isset($_REQUEST['names']) ? 'CLIENT NAME: '.$_REQUEST['names'] : '';

//$tin = 11;
$salestype = 'N';
$rectype = 'S';


$dateTime = DateTime::createFromFormat('YmdHis', $_REQUEST['dateData']);
$formattedDate = $dateTime->format('Y/m/d H:i:s');
$date = $dateTime->format('Y/m/d');
$time = $dateTime->format('H:i:s');


$dateTime1 = DateTime::createFromFormat('YmdHis', $_REQUEST['vs']);
$formattedDate1 = $dateTime1->format('Y/m/d H:i:s');
$date1 = $dateTime1->format('Y/m/d');
$time1 = $dateTime1->format('H:i:s');


$qrcode_data = '#'.$date.'#'.$time.'#'.$sdc.'#'.$receipt_no.'/ '.$tot.' '.$receiptT.'#'.$complete_int.'#'.$signature;
require_once '../phpqrcode/ebmqr.php';

QRcode::png($qrcode_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
$qrcode = '<img src="https://nstcr.gope.rw/reception/phpqrcode/'.$PNG_WEB_DIR.basename($filename).'" />';  

$title = '';
$title1='';
$body = '';
$footer = '';
switch(true){
    
    case $salestype == 'N' && $rectype == 'R':
        $title = 'REFUND';
        $title1 = '';
        $body = '';
        $footer = '';
        break;

        case $salestype == 'N' && $rectype == 'S':
            $title = '';
            $title1 = 'Welcome';
            $body = '';
            $footer = '';
            break;

    case $salestype == 'C' && $rectype == 'S':
        $title = 'COPY';
        $title1 = '';
        $body = 'THIS IS NOT AN OFFICIAL RECEIPT';
        $footer = 'COPY';
        break;
    case $salestype == 'C' && $rectype == 'R':
        $title = 'COPY';
        $title1 = 'REFUND';
        $body = 'THIS IS NOT AN OFFICIAL RECEIPT';
        $footer = 'COPY';
        break;

    case $salestype == 'T' && $rectype == 'S':
        $title = 'TRAINING MODE';
        $title1 = '';
        $body = 'THIS IS NOT AN OFFICIAL RECEIPT';
        $footer = 'TRAINING MODE';
        break;

    case $salestype == 'T' && $rectype == 'R':
        $title = 'TRAINING MODE';
        $title1 = 'REFUND';
        $body = 'THIS IS NOT AN OFFICIAL RECEIPT';
        $footer = 'TRAINING MODE';
        break;

    case $salestype == 'P':
        $title = 'PROFORMA';
        $body = 'THIS IS NOT AN OFFICIAL RECEIPT';
        $footer = 'PROFORMA';
        break;
}



$product = $_REQUEST['product'];
$res = 1;
$total = 0 ;
$no = 0;
$json_decoded = json_decode($product);

$a = 0;
$b = 0;
$c = 0;
$d = 0;


foreach($json_decoded  as $data )
{

    if($data->taxTyCd == 'A') $a=1;
    if($data->taxTyCd == 'B') $b=1;
    if($data->taxTyCd == 'C') $c=1;
    if($data->taxTyCd == 'D') $d=1;
//   $total = $total + $data->qty * $data->prc;
$discounting = ($data->prc*$data->qty)-$data->dcAmt;
  $total = $data->dcRt != 0 ? ($data->prc*$data->qty)-$data->dcAmt + $total : $data->totAmt + $total;
  $no = $no + 1;
  $res =  $res.'<tr>
  <td align="left" style="padding-left: 15px;">'.$data->itemNm.'<br>
      '.$data->prc.'x
  </td>
  <td align="center">
      <br>
      '.number_format($data->qty, 2).'
  </td>
  <td align="center">
      <br>
      '.number_format(($data->prc*$data->qty), 2).''.$data->taxTyCd.'
  </td>
</tr>';


$res .= $data->dcRt != 0 ? '
<tr>
  <td align="left" style="padding-left: 15px;" colspan="2"> Discount -'.$data->dcRt.'%</td>
  <td align="center">
       '.number_format($discounting,2).'
  </td>
</tr>' : ''; 
}



$html = '<html><head>';
$html .= '<style>';
$html .= 'body{ font-size: 14px; margin: 0; padding: 0; }';
$html .= '</style>';
$html .= '</head><body>';
$img = '<img src="https://nstcr.gope.rw/reception/receipt/images/rra1.png" style="width: 40px;height: 40px;  ">';
$img1 = '<img src="https://nstcr.gope.rw/reception/receipt/images/rra2.png" style="width: 40px;height: 40px; float: right;">';
$html .= '<div id="container" style="border: 1px solid black; margin: -30px;">

<table border="0" align="center" width="100%">
    <tr>
        <td align="" >
            '.$img.'
            '.$img1.'
        </td>
    </tr>
    <tr>
        <td align="center" style="display:grid;">
           <span>Centre Saint Paul Kigali Ltd</span><br>
           <span>KN 31 St, Kigali, Rwanda</span><br>
           <span>Tin: '.$companyTin.'</span><br>
           <span>Phone: '.$phone.'</span><br>
           <span><b>'.$title.'</b></span>
        </td>
    </tr>
    
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
    <tr>
        <td align="center" style="display: grid;">
            <span> <b>'.$title1.'</b></span><br>
            <span>'.$tin.'</span><br>
            <span>'.$names.'</span><br>
            <span>'.$clientPhone.'</span>
        </td>
    </tr>
    
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
    
</table>
<table border="0"  align="center" width="100%">
    '.$res.'
    <tr>
        <td colspan="3"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
   
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;"><b>TOTAL</b></td>
    <td align="center"><b>'.number_format($total,2).'</b></td>
</tr>
';

if($a){
$html .= '<tr>
    <td align="left" style="padding-left: 25px;">TOTAL A-EX</td>
    <td align="center">'.number_format($taxA,2).'</td>
</tr>';}

if($b){
    $html .= '<tr>
    <td align="left" style="padding-left: 25px;">TOTAL B-18.00%</td>
    <td align="center">'.number_format($taxbleAmount, 2).'</td>
</tr>';}

if($c){

    $html .= '<tr>
    <td align="left" style="padding-left: 25px;">TOTAL C</td>
    <td align="center">'.number_format($totalC, 2).'</td>
</tr>';}

if($d){
    $html .= '<tr>
    <td align="left" style="padding-left: 25px;">TOTAL D</td>
    <td align="center">'.number_format($totalD, 2).'</td>
</tr>';}

if($b){
    $html .= '<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX B</td>
    <td align="center">'.number_format($tax,2).'</td>
</tr>';}


$html .= '<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX</td>
    <td align="center">'.number_format($tax,2).'</td>
</tr>
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>
<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;">'.$_GET['pmtTyCd'].'</td>
    <td align="right" style="padding-right: 20px;">'.number_format($total,2).'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">ITEMS NUMBER</td>
    <td align="right" style="padding-right: 20px;">'.$no.'</td>
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
    <td align="left" style="padding-left: 25px;">Date: '.$date1.'</td>
    <td align="center">Time: '.$time1.' </td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">SDC ID:</td>
    <td align="center">'.$sdc.'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">RECEIPT NUMBER:</td>
    <td align="center">'.$receipt_no.'/ '.$tot.' '.$receiptT.'</td>
</tr>
</table>
<table border="0"  align="center" width="100%">
<tr>
    <td align="center">Internal Data:</td>
</tr>
<tr>
    <td align="center">'.$complete_int.'</td>
</tr>
<tr>
    <td align="center">Receipt Signature:</td>
</tr>
<tr>
    <td align="center">'.$signature.'</td>
</tr>
<tr>
    <td align="center">'.$qrcode.'</td>
</tr>
<tr>
    <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;">RECEIPT NUMBER:</td>
    <td align="right" style="padding-right: 20px;">'.$receiptNo.'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">DATE: '.$date.'</td>
    <td align="right" style="padding-right: 20px;">TIME: '.$time.'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">MRC:</td>
    <td align="right" style="padding-right: 20px;">'.$mrc.'</td>
</tr>

<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>

</tr>

<tr>

    <td align="left" style="padding-left: 25px;">CIS Version:</td>
    <td align="left" style="padding-right: 20px;">'.$cis_version.'</td>
    
</tr>
<tr>
    
    <td align="left" style="padding-left: 25px;">Powered by:</td>
    <td align="right" style="padding-right: 20px;">RRA VSDC EBM2.1</td>
</tr>

</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="center">'.$msg.'</td>
</tr>

</table>
</div>
';

// echo $html;

$dompdf->loadHtml($html); 

$customPaper = array(0, 0, 226.8, 999999);

$dompdf->set_paper($customPaper);

$dompdf->set_option('isRemoteEnabled', true);
// Render the HTML as PDF 
$dompdf->render(); 
 
ob_end_clean();
$dompdf->stream("codexworld", array("Attachment" => 0));