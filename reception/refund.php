<?php

ini_set('serialize_precision', -1);
ini_set('precision', 14);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once "../inc/DBController.php";

if (!isset($_GET['ref'])) {
    die("Missing 'ref' parameter");
}

$ref = $_GET['ref'];
$q = "SELECT json FROM `json_receipts` WHERE r_id = :invcId LIMIT 1";

$select = $db->prepare($q);
$select->bindParam(":invcId", $_GET['ref'], PDO::PARAM_INT);
$select->execute();

$row = $select->fetch(PDO::FETCH_ASSOC);

if ($row) {
    // Decode JSON
    $data = json_decode($row['json'], true);

    $lastSale = getLastId() + 1;
    if (json_last_error() === JSON_ERROR_NONE) {
        // Change values
        $data['rcptTyCd'] = "R";
        $data['rfdRsnCd'] = "03";
        $data['orgInvcNo'] = $data['invcNo'];
        $data['invcNo'] = $lastSale;

        // Encode back
        $newJson = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
        );
$url = getenv('VSDC_BASE_URL');
/* die(var_dump($newJson)); */
        $url = getenv('VSDC_URL');
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url.'/trnsSales/saveSales',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $newJson,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
if ($response === false) {
	var_dump($url);
    die('cURL Error: ' . curl_error($curl));
}

curl_close($curl);
        curl_close($curl);

        $data_res = json_decode($response);

        $responseData = json_decode($response, true);
        $rectype = 'R';

        $totalamount = $data['totAmt'];
        if (isset($responseData['resultCd'])) {
            $code = $data_res->resultCd;
            if ($code == '000') {


                $res = json_encode($data_res->data);
                $res = json_decode($res);

                $rcptNo =  $res->rcptNo;
                $totRcptNo =  $res->totRcptNo;
                $vsdcRcptPbctDate =  $res->vsdcRcptPbctDate;
                $sdcId =  $res->sdcId;
                $mrcNo =  $res->mrcNo;
                $int =  $res->intrlData;
                $rcptSign = $res->rcptSign;
                $amount = $totalamount;
                $sql_upd = $db->prepare("UPDATE tbl_vsdc_sales SET `has_refund` = '1' WHERE `invcNo`='".$ref."'");
                if ($rectype == 'R') {
                    $sql_upd->execute();
                }
                $tin = $data['custTin'];
                $branch_tin = $data['tin'];
                $purchase_code = $data['prcOrdCd'];
                $client_name = $data['custNm'];
                $agro_mo = $data['receipt']['custMblNo'];
                $salestype = $data['salesTyCd'];
                $cfmDt = $data['cfmDt'];
                $salesDt = $data['salesDt'];
                $totalitem = $data['totItemCnt'];
                $taxblAmtD = $data['taxblAmtD'];
                $taxblAmtC = $data['taxblAmtC'];
                $taxblAmtB = $data['taxblAmtB'];
                $taxblAmtA = $data['taxblAmtA'];
                $totalamount = $data['totTaxblAmt'];
                $pmtTyCd = $data['pmtTyCd'];
                $totalamountamount = $data['totTaxAmt'];
                $prdct = json_encode($data['itemList']);
                $uuid = 123;
                $receipt_info = addslashes('{"custTin":"'.$tin.'","custMblNo":null,"rptNo":1,"trdeNm":"KABC","adrs":"'.$company_address.' / '.$company_phone.'","topMsg":"'.$company_name.'\n'.$company_address.' / '.$company_phone.'\nTin: '.$branch_tin.'","btmMsg":"Welcome","prchrAcptcYn":"N"}');
                $sql_inf = $db->prepare("INSERT INTO tbl_vsdc_sales SET 
                tin='$branch_tin',
                bhfId='03',
                invcNo='$lastSale',
                orgInvcNo=0,
                custTin='$tin',
                prcOrdCd='$purchase_code',
                custNm='$client_name',
                custPhone='$agro_mo',
                salesTyCd='$salestype',
                rcptTyCd='$rectype',
                pmtTyCd='$pmtTyCd',
                salesSttsCd='02',
                cfmDt='$cfmDt',
                salesDt='$salesDt',
                stockRlsDt='".date("YmdHis")."',
                transaction_id = '$uuid',
                totItemCnt='$totalitem',
                taxblAmtA='$taxblAmtA',
                taxblAmtB='$taxblAmtB',
                taxblAmtC='$taxblAmtC',
                taxblAmtD='$taxblAmtD',
                taxRtA=0,
                taxRtB=0,
                taxRtC=0,
                taxRtD=0,
                taxAmtA=0,
                taxAmtB='$totalamountamount',
                taxAmtC=0,
                taxAmtD=0,
                totTaxblAmt='$totalamount',
                totTaxAmt='$totalamountamount',
                totAmt='$totalamount',
                prchrAcptcYn='N',
                regrId='01',
                regrNm='admin',
                modrId='01',
                modrNm='admin',
                receipt= '$receipt_info',
                itemList='$prdct',
                rcptNo='$rcptNo'");
                $sql_inf->execute();
		        $tin = (int)($tin ?? 0);
                $receipt_info = addslashes('{"custTin":"$tin","custMblNo":"NULL","rptNo":1,"trdeNm":"'.$company_name.'","adrs":"'.$company_address.' / '.$company_phone.'","topMsg":"'.$company_name.'\n'.$company_address.' / '.$company_phone.'\nTin: '.$branch_tin.'","btmMsg":"Welcome","prchrAcptcYn":"N"}');
                $sql_inf = $db->prepare("INSERT INTO tbl_receipts SET 
                tin='$branch_tin',
                bhfId='03',
                invcNo='$lastSale',
                orgInvcNo=0,
                custTin='$tin',
                prcOrdCd='$purchase_code',
                custNm='$client_name',
                salesTyCd='$salestype',
                rcptTyCd='$rectype',
                pmtTyCd='$pmtTyCd',
                salesSttsCd='02',
                cfmDt='$cfmDt',
                salesDt='$salesDt',
                stockRlsDt='".date("YmdHis")."',
                transaction_id = '$uuid',
                totItemCnt='$totalitem',
                taxblAmtA='$taxblAmtA',
                taxblAmtB='$taxblAmtB',
                taxblAmtC='$taxblAmtC',
                taxblAmtD='$taxblAmtD',
                taxRtA=0,
                taxRtB=0,
                taxRtC=0,
                taxRtD=0,
                taxAmtA=0,
                taxAmtB='$totalamountamount',
                taxAmtC=0,
                taxAmtD=0,
                totTaxblAmt=0,
                totTaxAmt='$totalamountamount',
                totAmt='$totalamount',
                prchrAcptcYn='N',
                regrId='01',
                regrNm='admin',
                modrId='01',
                modrNm='admin',
                receipt= '$receipt_info',
                itemList='$prdct',
                rcptNo='$rcptNo'

                ");
                $sql_inf->execute();

                if ($pmtTyCd == '01') {
                    $pmtTyCd = 'CASH';
                }
                if ($pmtTyCd == '02') {
                    $pmtTyCd = 'CREDIT';
                }
                if ($pmtTyCd == '03') {
                    $pmtTyCd = 'CASH/CREDIT ';
                }
                if ($pmtTyCd == '04') {
                    $pmtTyCd = 'BANK CHECK';
                }
                if ($pmtTyCd == '05') {
                    $pmtTyCd = 'DEBIT&CREDIT CARD';
                }
                if ($pmtTyCd == '06') {
                    $pmtTyCd = 'MOBILE MONEY';
                }
                if ($pmtTyCd == '07') {
                    $pmtTyCd = 'OTHER PAYMENTS';
                }

                $receitType = $salestype.$rectype;
                $lin = 'receiptT='.$receitType.'&taxbleAmount='.$taxblAmtB.'&taxA='.$taxA.'&taxB='.$taxB.'&ref='.$ref.'&no='.$rcptNo.'&total='
                .$totRcptNo.'&vs='.$vsdcRcptPbctDate.'&sdc='. $sdcId.'&mrc='.$mrcNo.'&amount='.$amount.'&product='.$prdct.'&int='.$int.'&sign='. $rcptSign.'&tin='. $tin.'&tax='.$totalamountamount;
                $lin .= '&salestype='.$salestype.'&rectype='.$rectype.'&receiptNo='.$lastSale;
                $lin .= '&totalC='.$taxblAmtC.'&totalD='.$taxblAmtD;
                $lin .= '&names='.$agro_names.'&clMob='.$agro_mo;
                $lin .= '&dateData='.$cfmDt;
                $lin .= '&pmtTyCd='.$pmtTyCd;


                $sqlseason = $db->prepare("SELECT * FROM system_configuration");
                $sqlseason->execute();
                $rows = $sqlseason->fetch();


                $stmts = $db->prepare("INSERT INTO `receipts` (`receipt_url`, `agro`)
		VALUES ('$lin', '$client_name')");
                $stmts->execute();

                $lastInsertId = $db->lastInsertId();

                $printer = $rows['printer'];
                if ($printer == 'paper_roll') {

                    if ($receitType == 'NR') {
                        echo "<script>window.open('receipt/normal-refund.php?$lin', '_blank')</script>";
                    }
                    echo "<script>window.location.href = 'index?resto=ebm';</script>";

                } else {

                    if ($receitType == 'NR') {
                        echo "<script>window.open('receipt/a4/normal-refund.php?$lin', '_blank')</script>";
                    }

                }

            } else {

                echo "<script>alert('$data_res->resultMsg')</script>";

            }


        } else {
            $error = $responseData['error'];
            echo "<script>alert('$error')</script>";
        }
    } else {
        echo "JSON decode error: " . json_last_error_msg();
    }
} else {
    echo "No record found";
}


echo "<script>close();</script>";
