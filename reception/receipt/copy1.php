<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
require_once 'dompdf/autoload.inc.php'; 
require_once ("../../inc/config.php");

$sqlseason = $db->prepare("SELECT * FROM system_configuration");
$sqlseason->execute();
$rows = $sqlseason->fetch();

$companyTin = $rows['Tin'];
$msg = $rows['receipt_msg'];
$ebm_version = $rows['ebm_version'];
$cis_version = $rows['version'];
$phone = $rows['phone'];

$sqlseason = $db->prepare("SELECT * FROM system_configuration");
$sqlseason->execute();
$rows = $sqlseason->fetch();


$sqlseason = $db->prepare("SELECT * FROM receipts WHERE id=".$_REQUEST['receipt']);
$sqlseason->execute();
$rows2 = $sqlseason->fetch();

$url = $rows2['receipt_url'];

parse_str($url, $queryParams);




use Dompdf\Dompdf; 
$dompdf = new Dompdf();
$amount = $queryParams['amount'];
$sdc = $queryParams['sdc'];
$mrc = $queryParams['mrc'];
$mrc = $rows['mrc'];
$no = $queryParams['no'];
$receipt_no = $queryParams['no'];
$tot = $queryParams['total'];
$vs = $queryParams['vs'];
$tin = $queryParams['tin'] =='null' ? '': 'CLIENT TIN: '.preg_replace('/\D/', '',$queryParams['tin']);
$sign = $queryParams['sign'];
$int = $queryParams['int'];
$ref = $queryParams['ref'];

$chunkedArray = str_split($int, 4);
$complete_int = implode('-', $chunkedArray);

$chunkedArray = str_split($sign, 4);
$signature = implode('-', $chunkedArray);

$receiptT = $queryParams['receiptT'];

$taxA = $queryParams['taxA'];
$taxB = $queryParams['taxB'];
$taxbleAmount = $queryParams['taxbleAmount'];
$totalC = $queryParams['totalC'];
$totalD = $queryParams['totalD'];

$tax = $queryParams['tax'];
$receiptNo = $queryParams['receiptNo'];

$clientPhone = isset($queryParams['clMob']) ? 'CLIENT MOBIL: '.$queryParams['clMob'] : '';
$names = isset($queryParams['names']) ? 'CLIENT NAME: '.$queryParams['names'] : '';

//$tin = 11;
$salestype = 'C';
$rectype = 'S';


$dateTime = DateTime::createFromFormat('YmdHis', $queryParams['dateData']);

$formattedDate = $dateTime->format('Y/m/d H:i:s');

// Extract the date and time components separately
$date = $dateTime->format('Y/m/d');
$time = $dateTime->format('H:i:s');


$qrcode_data = '#'.$date.'#'.$time.'#'.$sdc.'#'.$receipt_no.'/ '.$tot.' '.$receiptT.'#'.$complete_int.'#'.$signature;
require_once '../phpqrcode/ebmqr.php';

QRcode::png($qrcode_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
$qrcode = '<img src="https://nstcr.gope.rw/reception/phpqrcode/'.$PNG_WEB_DIR.basename($filename).'" />';  

$title = '';
$title1='';
$body = '';
$footer = '';
switch(true){

    case $salestype == 'C' && $rectype == 'S':
        $title = 'COPY';
        $title1 = '';
        $body = 'THIS IS NOT AN OFFICIAL RECEIPT';
        $footer = 'COPY';
        break;



}



$product = $queryParams['product'];
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
$html .= '<div id="container" style="width: 100%; border: 1px solid black; margin: 0;">

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
            <span>KN 4 Ave / 0789303811</span><br>
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
            <span> WELCOME </span><br>
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
    <tr>
        <td colspan="3" align="center">
            <span><b>'.$body.'</b></span>
        </td>
    </tr>
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
    <td align="left" style="padding-left: 25px;">CASH</td>
    <td align="right" style="padding-right: 20px;">'.number_format($total,2).'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">ITEMS NUMBER</td>
    <td align="right" style="padding-right: 20px;">'.$no.'</td>
</tr>
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
    <tr>
        <td colspan="2" align="center">
            <span><b>'.$footer.'</b></span>
        </td>
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
    <td align="left" style="padding-left: 25px;">Date: '.$date.'</td>
    <td align="center">Time: '.$time.' </td>
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
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="center">'.$msg.'</td>
</tr>

</table>
</div>
';

echo $html;

// $dompdf->loadHtml($html); 
 
// // (Optional) Setup the paper size and orientation 


// // $dompdf->setPaper('A4', 'portrait'); 
// $customPaper = array(0,20,300,900);
// $dompdf->set_paper($customPaper);
// $dompdf->set_option('isRemoteEnabled', true);
// // Render the HTML as PDF 
// $dompdf->render(); 
//  ob_end_clean();
// // Output the generated PDF (1 = download and 0 = preview) 
// $dompdf->stream("codexworld", array("Attachment" => 0));