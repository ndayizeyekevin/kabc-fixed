<?php
include('../dompdf/autoload.inc.php'); 
include("../inc/DBController.php");
include('../phpqrcode/qrlib.php');
use Dompdf\Dompdf; 
$dompdf = new Dompdf();
$html = '';
if(ISSET($_GET['resrv']))
		{
		foreach($_GET as $loc=>$item)
        $_GET[$loc] = base64_decode(urldecode($item));
		
        $reservation_ID = $_GET[$loc];
        $todaysdate = date("Y-m-d H:i:s");
        
        $pay_no = time();
		

        $stmt = $db->prepare("SELECT * FROM tbl_cmd
        WHERE OrderCode = '".$reservation_ID."'");
		$stmt->execute();
		$gets = $stmt->fetch();
        $table = $gets['reservat_id'];
        $tin = $gets['client'];

        $sqltable = $db->prepare("SELECT * FROM tbl_tables WHERE table_id = '$table'");
        $sqltable->execute();
        $gettable = $sqltable->fetch();

        $img = '<img src="http://localhost/resto/reception/rra.jpg" style="width: 40px;height: 40px;">';

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
            <span>Client Tin:'.$tin.'</span><br>
            <span>Client Name: -</span>
            

        </td>
    </tr>
    
    <tr>
        <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
    
</table>
<table border="0"  align="center" width="100%">
';
$Mtot = 0;
$tottax = 0;
$sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
WHERE tbl_cmd_qty.cmd_status = '12' AND cmd_code = '".$reservation_ID."'
GROUP BY tbl_cmd_qty.cmd_item");
$sql->execute(array());
$count = $sql->rowCount();
while($fetch2 = $sql->fetch()){
   
    $qty = $fetch2['totqty'];
    
    $Mprice = $fetch2['menu_price']*$qty;
    $Mtot =$Mtot+$Mprice;
    $discount = $gets['discount'];
    $totprice = $Mtot-$discount;

    $tax = $fetch2['tax'];

    $tottax += $tax*$Mprice;

    $html .='
    <tr>
        <td align="left" style="padding-left: 15px;">
            '.$fetch2['menu_name'].'<br>
            '.$fetch2['menu_price'].'.00x
        </td>
        <td align="center">
            <br>
            '.$qty.'.00
        </td>
        <td align="center">
            <br>
            '.number_format($Mprice).'.00'.$fetch2['tax_type'].'
        </td>
    </tr>
    ';
}
$html .='
<tr>
    <td align="left" style="padding-left: 15px;">
        discount<br>
        -'.number_format(($discount*100/$Mtot),2).'.%
    </td>
    <td align="center">
        <br>
        
    </td>
    <td align="center">
        <br>
        '.number_format($discount,2).'
    </td>
</tr>
    <tr>
        <td colspan="3"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
    </tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;"><b>TOTAL</b></td>
    <td align="center"><b>'.number_format($Mtot,2).'</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL</td>
    <td align="center">A-EX 0.00</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL B-18.00%</td>
    <td align="center">'.number_format($tottax,2).'</td>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX B</td>
    <td align="center">'.number_format($tottax,2).'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">TOTAL TAX</td>
    <td align="center">'.number_format($tottax,2).'</td>
</tr>
<tr>
    <td colspan="2"><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>

<table border="0"  align="center" width="100%">
<tr>
    <td align="left" style="padding-left: 25px;">CASH</td>
    <td align="right" style="padding-right: 20px;"> '.number_format($Mtot,2).'</td>
</tr>
<tr>
    <td align="left" style="padding-left: 25px;">ITEMS NUMBER</td>
    <td align="right" style="padding-right: 20px;">'.$count.'</td>
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
';
 $text=date("Ymd").'#'.date("His").'SDC001000001#TE68-SLA2-34J5-EAV3-N569-88LJ-Q7#V249-J39C-FJ48-HE2W';
 $folder="../images/";
 $file_name=time().".png";
 $file_name=$folder.$file_name;
 QRcode::png($text,$file_name);
$html .='
<img src="http://localhost/resto/images/'.$file_name.'">

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
        }

//  $text='https://nmii.aptc.rw/';
//  $folder="../images/";
//  $file_name="qr.png";
//  $file_name=$folder.$file_name;
//  QRcode::png($text,$file_name);
//  echo"<img src='http://localhost/resto/images/qr.png'>";
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