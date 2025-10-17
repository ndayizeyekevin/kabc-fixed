<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


require_once("config.php");

function generate_uuid(): string
{
    return bin2hex(random_bytes(16));
}


function formatingJson(
    $orgInvcNo,
    $payMode,
    $taxblAmtA,
    $taxblAmtB,
    $invcNo,
    $custTin,
    $prcOrdCd,
    $custNm,
    $salesTyCd,
    $rcptTyCd,
    $totItemCnt,
    $totTaxAmt,
    $totAmt,
    $receipt,
    $itemList,
    $taxblAmtC,
    $taxblAmtD,
    $cfmDt,
    $salesDt
) {
    $totTaxAmt = str_replace(',', '', number_format($totTaxAmt ?? 0, 2));
    global $branch_tin;
    global $branch_no;

    if ($rcptTyCd == "R") {
        $rfdRsnCd = "01";
    } else {
        $rfdRsnCd = "";
    }


    $json = '
  {"tin":"'.$branch_tin.'",
    "bhfId":"00",
    "invcNo":"'.$invcNo.'",
    "orgInvcNo":"'.$orgInvcNo.'",
    "custTin":'.$custTin.',
    "prcOrdCd":'.$prcOrdCd.',
    "custNm":"'.$custNm.'",
    "salesTyCd":"'.$salesTyCd.'",
    "rcptTyCd":"'.$rcptTyCd.'",
    "pmtTyCd":"'.$payMode.'",
    "salesSttsCd":"02",
    "cfmDt":"'.$cfmDt.'",
    "salesDt":"'.$salesDt.'",
    "stockRlsDt":null,
    "cnclReqDt":null,
    "cnclDt" :null,
    "rfdDt":null,
    "rfdRsnCd":"'.$rfdRsnCd.'",
    "totItemCnt":'.$totItemCnt.',
    "taxblAmtA":'.$taxblAmtA.',
    "taxblAmtB":'.$taxblAmtB.',
    "taxblAmtC":'.$taxblAmtC.',
    "taxblAmtD":'.$taxblAmtD.',
    "taxRtA":0,
    "taxRtB":18,
    "taxRtC":0,
    "taxRtD":0,
    "taxAmtA":0,
    "taxAmtB":'.$totTaxAmt.',
    "taxAmtC":0,
    "taxAmtD":0,
    "totTaxblAmt":'.$totAmt.',
    "totTaxAmt":'.$totTaxAmt.',
    "totAmt":'.$totAmt.',
    "prchrAcptcYn":"N",
    "remark":null,
    "regrId":"01",
    "regrNm":"admin",
    "modrId":"01",
    "modrNm":"admin",
    "receipt":'.$receipt.',
    "itemList":'.$itemList.'
  }';

    return $json;
}

function getLastId()
{
    global $db;
    $sql1 = $db->prepare("SELECT invcNo FROM tbl_receipts ORDER BY invcNo DESC LIMIT 1");
    $sql1->execute();
    $row = $sql1->fetch();
    return $row['invcNo'] + 1;
}

function countIo()
{
    global $db;
    $sql1 = $db->prepare("SELECT * FROM tbl_vsdc_io ORDER BY id DESC LIMIT 1");
    $sql1->execute();
    $row = $sql1->fetch();
    if ($row !== false) {
        return $row['id'] + 1;
    } else {
        return 1;
    }
}

function countMaster()
{
    global $db;
    $sql1 = $db->prepare("SELECT * FROM tbl_receipts ORDER BY salesId DESC LIMIT 1");
    $sql1->execute();
    $row = $sql1->fetch();
    if ($row !== false) {
        return $row['salesId'] + 1;
    } else {
        return 1;
    }
}

function getStockValue($type)
{
    global $db;
    // $br = $_SESSION['branch'];
    $sql1 = $db->prepare("SELECT * FROM stock INNER JOIN menu ON menu.menu_id=stock.type WHERE menu.itemCd = '$type' LIMIT 1");
    $sql1->execute();
    $row = $sql1->fetch();
    if ($row !== false) {
        return $row['quantities'];
    } else {
        return 0;
    }

}

function getRemainStock($type)
{
    global $db;
    $br = $_SESSION['branch'];
    $sql1 = $db->prepare("SELECT * FROM stock WHERE type = '$type' LIMIT 1");
    $sql1->execute();
    $row = $sql1->fetch();
    if ($row !== false) {
        return $row['quantities'];
    } else {
        return 0;
    }
}


function getItemClass($type)
{
    global $db;
    $sql1 = $db->prepare("SELECT * FROM menu WHERE itemCd = '$type' LIMIT 1");
    $sql1->execute();
    $row = $sql1->fetch();
    return $row['product_type'];

}

// function to change items in product list in negative numbers
function turn_tot_amt_negative($object): void
{
    $object->totAmt = -$object->totAmt;
}


function callCURL($jsonData, $endpoint)
{
    global $url;
    $ch = curl_init($url.$endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}


function sendStockMaster($data)
{
    global $db;
    $return = array();
    foreach ($data as $dt) {
        $response = callCURL($dt, 'stockMaster/saveStockMaster');
        if (strlen($response) !== 0) {
            $return[] = $response;

            $dataArray = json_decode($dt, true);
            $tbl_data = '';
            $tbl_data .= 'item="'.$dataArray['itemCd'].'"';
            $tbl_data .= ',qty="'.$dataArray['rsdQty'].'"';
            $sql1 = $db->prepare("INSERT INTO tbl_vsdc_master SET $tbl_data");
            // $sql1->execute();
        }
    }
    return $return;
}

function sendStockIO($data)
{
    global $db;
    $return = array();
    foreach ($data as $dt) {
        $response = callCURL($dt, 'stock/saveStockItems');
        if (strlen($response) !== 0) {
            $return[] = $response;

            $dataArray = json_decode($dt, true);
            $tbl_data = '';
            $tbl_data .= 'item="'.$dataArray['ocrnDt'].'"';
            $tbl_data .= ',qty="'.$dataArray['totItemCnt'].'"';
            $sql1 = $db->prepare("INSERT INTO tbl_vsdc_io SET $tbl_data");
            $sql1->execute();

        }
    }
    return $return;
}


function getInfoTable($code = null)
{
    global $db;
    $sql1 = $db->prepare("SELECT * FROM `tbl_info` WHERE `code_crasification` = $code");
    $sql1->execute();
    $row = $sql1->fetchAll();
    return $row;

}

function getItemId($id)
{
    global $db;
    // $id = $db->real_escape_string($id);
    $sql = "SELECT * FROM menu WHERE itemCd = '$id'";
    $result = $db->query($sql);

    if ($result->rowCount() > 0) {
        // output data of each row
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            return $row['menu_id'];


        }
    }

}


function getTotals()
{
    global $db;
    $date = date('Y-m-d');
    $retval = mysqli_query($db, "SELECT * FROM `tbl_vsdc_sales` WHERE LEFT(`cfmDt`,10)='$date' AND salesTyCd = 'N'");
}


if (isset($_POST['salestype']) && isset($_POST['rectype']) && isset($_POST['fetchdata'])) {
    $rcptTyCd = $_POST['rectype'];
    $salestype = $_POST['salestype'];


    $tin = $_POST['tin'];
    $select = $db->prepare("SELECT `transaction_id`, totAmt, cfmDt, tbl_vsdc_sales.rcptTyCd, `salesTyCd`,`rcptTyCd`, invcNo, tbl_vsdc_sales.custTin , tbl_tables.table_no FROM `tbl_vsdc_sales`
INNER JOIN tbl_cmd_qty ON tbl_cmd_qty.`cmd_code` = tbl_vsdc_sales.transaction_id 
INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id where tbl_vsdc_sales.salesTyCd !='P' ORDER BY salesId DESC");
    if ($rcptTyCd == 'S' && $salestype == 'C') {
        $select = $db->prepare("SELECT `transaction_id`, totAmt, cfmDt, tbl_vsdc_sales.rcptTyCd, `salesTyCd`,`rcptTyCd`, invcNo, tbl_vsdc_sales.custTin , tbl_tables.table_no FROM `tbl_vsdc_sales`
INNER JOIN tbl_cmd_qty ON tbl_cmd_qty.`cmd_code` = tbl_vsdc_sales.transaction_id 
INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id where tbl_vsdc_sales.salesTyCd ='N' AND tbl_vsdc_sales.rcptTyCd ='S' AND tbl_vsdc_sales.has_refund='0' ORDER BY salesId DESC");
    }
    if ($rcptTyCd == 'R' && $salestype == 'C') {
        $select = $db->prepare("SELECT `transaction_id`, totAmt, cfmDt, tbl_vsdc_sales.rcptTyCd, `salesTyCd`,`rcptTyCd`, invcNo, tbl_vsdc_sales.custTin , tbl_tables.table_no FROM `tbl_vsdc_sales`
INNER JOIN tbl_cmd_qty ON tbl_cmd_qty.`cmd_code` = tbl_vsdc_sales.transaction_id 
INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id where tbl_vsdc_sales.salesTyCd ='C' AND tbl_vsdc_sales.has_refund='0' ORDER BY salesId DESC");
    }
    if ($rcptTyCd == 'R' && $salestype == 'N') {
        $select = $db->prepare("SELECT `transaction_id`, totAmt, cfmDt, tbl_vsdc_sales.rcptTyCd, `salesTyCd`,`rcptTyCd`, invcNo, tbl_vsdc_sales.custTin , tbl_tables.table_no FROM `tbl_vsdc_sales`
INNER JOIN tbl_cmd_qty ON tbl_cmd_qty.`cmd_code` = tbl_vsdc_sales.transaction_id 
INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id where tbl_vsdc_sales.salesTyCd ='N' AND tbl_vsdc_sales.rcptTyCd ='S' AND tbl_vsdc_sales.has_refund='0' ORDER BY salesId DESC");
    }

    if ($rcptTyCd == 'R' && $salestype == 'T') {
        $select = $db->prepare("SELECT `transaction_id`, totAmt, cfmDt, tbl_vsdc_sales.rcptTyCd, `salesTyCd`,`rcptTyCd`, invcNo, tbl_vsdc_sales.custTin , tbl_tables.table_no FROM `tbl_vsdc_sales`
INNER JOIN tbl_cmd_qty ON tbl_cmd_qty.`cmd_code` = tbl_vsdc_sales.transaction_id 
INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id where tbl_vsdc_sales.salesTyCd ='T' AND tbl_vsdc_sales.rcptTyCd ='S' AND tbl_vsdc_sales.has_refund='0' ORDER BY salesId DESC");
    }




    $select->execute();
    $result = $select->fetchAll();
    $output = '<option></option>';
    foreach ($result as $row) {
        // $output.='<option value="'.$row['transaction_id'].'">['.$row['totAmt'].'RWF] '.$row['invcNo'].' '.$row['salesTyCd'].$row['rcptTyCd'].'</option>';
        $output .= '<option value="'.$row['invcNo'].'">['.$row['totAmt'].'RWF]  RECEIPT NO: '.$row['invcNo'].' '.$row['table_no'].'</option>';
    }

    echo $output;
}

