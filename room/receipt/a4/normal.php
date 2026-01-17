<?php

require_once __DIR__.'/../dompdf/autoload.inc.php';
require_once(__DIR__."/../../../inc/config.php");




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


// $sqlseason = $db->prepare("SELECT * FROM tbl_mrc");
// $sqlseason->execute();
// $rows = $sqlseason->fetch();




use Dompdf\Dompdf;

$dompdf = new Dompdf();
$amount = $_REQUEST['amount'];
$sdc = $_REQUEST['sdc'];
$mrc = $_REQUEST['mrc'];
$mrc = $rows['mrc'];
$no = $_REQUEST['no'];
$receipt_no = $_REQUEST['no'];
$tot = $_REQUEST['total'];
$vs = $_REQUEST['vs'];
$tin = $_REQUEST['tin'] == 'null' ? '' : 'TIN: '.$_REQUEST['tin'];
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


$clientPhone = isset($_REQUEST['clMob']) ? 'MOBILE: '.$_REQUEST['clMob'] : '';
$names = isset($_REQUEST['names']) ? 'NAME: '.$_REQUEST['names'] : '';


$salestype = 'N';
$rectype = 'S';


$dateTime = DateTime::createFromFormat('YmdHis', $_REQUEST['dateData']);

$formattedDate = $dateTime->format('Y/m/d H:i:s');

// Extract the date and time components separately
$date = $dateTime->format('Y/m/d');
$time = $dateTime->format('H:i:s');


// $qrcode_data = '#'.$date.'#'.$time.'#'.$sdc.'#'.$receipt_no.'/ '.$tot.' '.$receiptT.'#'.$complete_int.'#'.$signature;
// require_once __DIR__.'/../../phpqrcode/ebmqr.php';
//
// QRcode::png($qrcode_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
// $qrcode = '<img src="https://rms.nsportsclub.rw/rm/images/qr.png" width="100" height="100"/>';


/* ================== QR CODE (SAFE, ISOLATED) ================== */

if (!empty($date) && !empty($time) && !empty($sdc) && !empty($receipt_no)
    && !empty($tot) && !empty($receiptT) && !empty($complete_int) && !empty($signature)
) {
    require_once __DIR__ . '/../../phpqrcode/ebmqr.php';

    $qr_payload =
        '#'.$date.
        '#'.$time.
        '#'.$sdc.
        '#'.$receipt_no.'/ '.$tot.' '.$receiptT.
        '#'.$complete_int.
        '#'.$signature;

    ob_start();
    QRcode::png($qr_payload, null, QR_ECLEVEL_L, 4, 2);
    $qr_image = ob_get_clean();

    $qrcode = '<img src="data:image/png;base64,'.base64_encode($qr_image).'" width="100" height="100">';
} else {
    // Fallback: keeps layout intact if something is missing
    $qrcode = '';
}

/* ================== END QR CODE ================== */


$title = '';
$title1 = '';
$body = '';
$footer = '';
switch (true) {

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


$itemcode = '';
$itemName = '';
$qty = '';
$tax1 = '';
$price = '';
$tprice = '';
$discount = '';
$disct = '';


foreach ($json_decoded as $index => $data) {

    if ($data->taxTyCd == 'A') {
        $a = 1;
    }
    if ($data->taxTyCd == 'B') {
        $b = 1;
    }
    if ($data->taxTyCd == 'C') {
        $c = 1;
    }
    if ($data->taxTyCd == 'D') {
        $d = 1;
    }
    //   $total = $total + $data->qty * $data->prc;
    $discounting = ($data->prc * $data->qty) - $data->dcAmt;
    $total = $data->dcRt != 0 ? ($data->prc * $data->qty) - $data->dcAmt + $total : $data->totAmt + $total;
    $no = $no + 1;

    if ($data->dcRt != 0) {
        $itemcode = $itemcode.$data->itemCd."<br><br>";
        $qty = $qty.number_format($data->qty, 2)."<br><br>";
        $tax1 = $tax1.$data->taxTyCd."<br><br>";
        $discount = '<br><b> Discount -'.$data->dcRt.'%</b>';
        $disct = number_format($discounting, 2);
        $itemName = $itemName.$data->itemNm.$discount."<br>";
        $price = $price.number_format($data->prc)."<br><br>";
        $tprice = $tprice.number_format(($data->prc * $data->qty), 2).'<br><b>'.$disct."</b><br>";
    } else {
        $itemcode = $itemcode.$data->itemCd."<br>";
        $itemName = $itemName.$data->itemNm."<br>";
        $qty = $qty.number_format($data->qty, 2)."<br>";
        $tax1 = $tax1.$data->taxTyCd."<br>";
        $price = $price.number_format($data->prc, 2)."<br>";
        $tprice = $tprice.number_format(($data->prc * $data->qty), 2)."<br>";
    }

}


$html = '<html><head>';
$html .= '<style>';
$html .= 'body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }
        .invoice-header, .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-header td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-table th {
            border: 1px solid #000;
            text-align: left;
            padding: 5px;
        }
        .invoice-table th, .invoice-table td {
            border-right: 1px solid #000;
            border-left: 1px solid #000;

            text-align: left;
            padding: 5px;
        }

        .total {
            border-collapse: collapse; /* Merges borders for cleaner appearance */
            width: 100%; /* Optional: Full width table */
        }

        .total th, .total td {
            border: 1px solid black; /* Adds borders to cells */
            padding: 2px; /* Adds spacing within cells */
            text-align: left; /* Aligns text to the left */
        }
        
        .invoice-table tr td {
            height: 500px;
            vertical-align: top;
        }

        .invoice-table {
            
            border-bottom: 1px solid #000;
            height: 550px; /* Set the table height */
        }
        .right {
            text-align: right;
        }';
$html .= '</style>';
$html .= '</head><body>';
$img = '<img src="https://rms.nsportsclub.rw/reception/receipt/images/rra1.png" style="height: 100px;  ">';
$img1 = '<img src="https://rms.nsportsclub.rw/reception/receipt/images/rra2.png" style="width: 100px;height: 100px; float: right;">';
$html .= '<div id="container" style="width: 100%; margin: -10px; padding: 1px;">

<table align="center" width="100%">
    <tr>
        <td widt="30%">
            '.$img.'
       </td>
         <td align="left">
                   <span>'.$company_name.'</span><br>
            <span>'.$company_address.' / '.$company_phone.'</span><br>
            <span>Tin: '.$companyTin.'</span><br>
            <span><b>'.$title.'</b></span>
        </td>
        
        <td align="">
            '.$img1.'
        </td>

    </tr>

    <tr><td>INVOICE TO</td></td>
    <tr style="">
        
        <td style="padding-left: 10px; border: 1px solid black;">
            <span>'.$tin.'</span><br>
            <span>'.$names.'</span><br>
            <span>'.$clientPhone.'</span>
        </td>
        <td style="padding-left: 10px;">
            
        </td>
        <td style="padding-left: 10px; border: 1px solid black;">
            <span>INVOICE NO: '.$receiptNo.'</span><br>
            <span>DATE: '.$date.' '.$time.'</span><br>
            
        </td>

    </tr>

</table>

<table border="0" class="invoice-table" width="100%">
    <tr style="border: 1px solid black;">
        <th align="left">Item code</th><th align="left">Item Description</th><th align="left">Qty</th><th align="left">Tax</th><th align="left">Unit Price</th><th align="left">Total Price</th>
    </tr>
    <tr>
        <td align="top">'.$itemcode.'</td>
        <td>'.$itemName.'</td>
        <td>'.$qty.'</td>
        <td>'.$tax1.'</td>
        <td>'.$price.'</td>
        <td>'.$tprice.'</td>

    </tr>
    
   
</table>

<table border="0" width="100%">
   <tr>
    <td width="30%">
            <table border="0" class="total" >
                <tr>
                        <td style="font-size: 12px;" width="30%">
                            '.$_GET['pmtTyCd'].'
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;" width="30%">
                            ITEMS NUMBER: '.$no.'
                        </td>
                    </tr>
                    
            
            </table>
        </td>
        <td></td>
        <td></td>
    </tr>
</table>


<table border="0" class="descriptions" width="100%">
<tr>
    <td width="45%">
        <table border="0" width="100%">
            <tr>
                <td align="left">SDC INFORMATION</td>
            </tr>
             <tr>
                <td align="left">------------------------------</td>
            </tr>
            <tr>
                <td align="left" style="">Date: '.$date.' '.$time.'</td>

            </tr>
            <tr>
                <td align="left" style="">SDC ID: '.$sdc.'</td>
                
            </tr>
            <tr>
                <td align="left" style="">RECEIPT NUMBER: '.$receipt_no.'/ '.$tot.' '.$receiptT.'</td>
            </tr>
            <tr>
                <td align="left" style="">Internal Data: '.$complete_int.'</td>
            </tr>
            <tr>
                <td align="left" style="">Receipt Signature: '.$signature.'</td>
            </tr>
            <tr>
                <td align="left">------------------------------</td>
            </tr>
            <tr>
                <td align="left" style="">RECEIPT NUMBER: '.$receiptNo.'</td>
            </tr>
            <tr>
                <td align="left" style="">DATE: '.$date.' '.$time.'</td>
            </tr>
            <tr>
                <td align="left" style="">MRC: '.$mrc.'</td>
            </tr>
            <tr>
                <td align="left">------------------------------</td>
            </tr>
            <tr>
                <td align="left" style="">CIS Version: '.$cis_version.'</td>
            </tr>
            <tr>
                <td align="left" style="">Powered by: RRA VSDC EBM2.1</td>
            </tr>
        </table>
    </td>
    <td align="center" style="vertical-align: top;">
        '.$qrcode.' 
    </td>
    <td align="center" style="vertical-align: top;">
        <table border="1" class="total" align="center" >
            <tr>
                <td align="left" style="padding-left: 25px;"><b>TOTAL</b></td>
                <td align="center"><b>'.number_format($total, 2).'</b></td>
            </tr>
            ';

if ($a) {
    $html .= '<tr>
                <td align="left" style="padding-left: 25px;">TOTAL A-EX</td>
                <td align="center">'.number_format($taxA, 2).'</td>
            </tr>';
}

if ($b) {
    $html .= '<tr>
                <td align="left" style="padding-left: 25px;">TOTAL B-18.00%</td>
                <td align="center">'.number_format($taxbleAmount, 2).'</td>
            </tr>';
}

if ($c) {

    $html .= '<tr>
                <td align="left" style="padding-left: 25px;">TOTAL C</td>
                <td align="center">'.number_format($totalC, 2).'</td>
            </tr>';
}

if ($d) {
    $html .= '<tr>
                <td align="left" style="padding-left: 25px;">TOTAL D</td>
                <td align="center">'.number_format($totalD, 2).'</td>
            </tr>';
}

if ($b) {
    $html .= '<tr>
                <td align="left" style="padding-left: 25px;">TOTAL TAX B</td>
                <td align="center">'.number_format($tax, 2).'</td>
            </tr>';
}


$html .= '<tr>
                <td align="left" style="padding-left: 25px;">TOTAL TAX</td>
                <td align="center">'.number_format($tax, 2).'</td>
            </tr>
            </table>
    </td>
</tr>
</table>



</div>

';

// echo $html;
// die();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation


$dompdf->setPaper('A4', 'portrait');

$dompdf->set_option('isRemoteEnabled', true);
// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF (1 = download and 0 = preview)
$dompdf->stream("codexworld", array("Attachment" => 0));

