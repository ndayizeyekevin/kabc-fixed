<?php
 
// echo $url;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$uuid = generate_uuid();

// if (!empty($_SESSION['agro_id'])) {
//     $user = trim($_SESSION['agro']);
//     $sql1 = $db->prepare("SELECT * FROM agro1 WHERE agro = :name");
//     $sql1->execute([':name' => $user]);
//     $row = $sql1->fetch();
//     $_SESSION['balance'] = $row['balance'];
//     $_SESSION['agro_name'] = $row['agro'];
//     $_SESSION['agro_tin'] = $row['tin'];
//     $_SESSION['phone'] = $row['phone'];

// }

// $id = $_SESSION['agro_id'];
if (isset($_GET['action']) && isset($_GET['item'])) {
  $id = $_GET['item'];
  $sql1 = $db->prepare("DELETE FROM `cart_sales` WHERE cart_id = $id");
  $sql1->execute();
//   header("Location: index?resto=ebm");
}
 ?>

 <?php


 $count = 0; 
if (isset($_POST['save'])) {
// die(var_dump($_POST));
  $salestype= $_POST['salestype'];
  $rectype= $_POST['rectype'];
  if ($rectype==='R') {

  }
  
  $clientPhone = $_POST['clientPhone'];
  $client_name =$_POST['clientName'];
  $lastSale = getLastId()+3;
  $totalamount = $_POST['totalamount'];
  $phone = empty($_POST['clientPhone'])? "null": '"'.$_POST['clientPhone'].'"';

  $purchase_code = empty($_POST['purchasecode'])? "null": '"'.$_POST['purchasecode'].'"';



  $tinPhone = ltrim($clientPhone, '0');
 // Check if clientTin is not set or is empty
if (empty($_POST['clientTin']) || trim($_POST['clientTin']) === 'null') {
  $tin = "null";
} else {
  $tin = '"' . $_POST['clientTin'] . '"';
}
  $clientPhone = empty($_POST['clientPhone'])? "null": $_POST['clientPhone'];


  $agro_names = $_POST['clientName'];
  $agro_mo = $_POST['clientPhone'];
  $ref = 0;

  $totalitem= $_POST['totalitem'];
  $totalamountamount =  $_POST['totalamountamount'];
  
  $taxblAmtA = $_POST['taxblAmtA'];
  $taxblAmtB =$_POST['taxblAmtB'];
  $taxblAmtC = $_POST['taxblAmtC'];
  $taxblAmtD =$_POST['taxblAmtD'];
  
  
  $taxBamount = $_POST['taxBamount'];
  $taxB = $_POST['taxableAmount'];
  $taxA = $_POST['totTaxA'];
  $pmtTyCd = empty($_POST['pmtTyCd'])? "null":$_POST['pmtTyCd'];


  $productList = $_POST['productList'];
  
  $prdct =  '['.substr($productList, 0, -1).']';
  $prdctR = '';
  $json ="";
  // $branch = $_SESSION['branch'];
  // $agroid = $_SESSION['agro_id'];


  $cfmDt=date("YmdHis");
  $salesDt=date("Ymd");

  // echo $tin;

  $receipt = '{"custTin":'.$tin.',"custMblNo":"'.$clientPhone.'","rptNo":'.$lastSale.',"trdeNm":"","adrs":"'.$company_name.'","topMsg":"'.$company_name.'\n'.$company_address.'\nTin: $branch_tin","btmMsg":"Welcome","prchrAcptcYn":"N"}';        
  $json = formatingJson($ref, $pmtTyCd, $taxblAmtA, $taxblAmtB, $lastSale, $tin, $purchase_code, $client_name, $salestype, $rectype, $totalitem, $taxBamount, $totalamount, $receipt, $prdct, $taxblAmtC, $taxblAmtD, $cfmDt, $salesDt);
  


  switch (true) {
    case $salestype == 'N' && $rectype == 'S' :
  

      break;


      case $salestype == 'C' || $rectype=='R':
        $prevesales = $_POST['salesprev'];
        
        $sqlseason = $db->prepare("SELECT * FROM `tbl_vsdc_sales` WHERE `invcNo`='$prevesales'");
        $sqlseason->execute();
        $rows = $sqlseason->fetch();
        $prdct = $rows['itemList'];
        
        $prdct = json_decode($prdct, true);
		  foreach ($prdct as &$item) {
				// $item['taxAmt'] = number_format((float)$item['taxAmt'],2); 
			}

		$prdct = json_encode($prdct);
        
        
        $ref = $rows['invcNo'];
        $pmtTyCd=$rows['pmtTyCd']; 
        $taxblAmtA=$rows['taxblAmtA'];
        $taxA =  $rows['taxblAmtA'];
        $taxblAmtB=$rows['taxblAmtB']; 


        $client_name=$rows['custNm'];
        $purchase_code = $rows['custTin'] =="null" ? "null" : $purchase_code;

         $totalitem=$rows['totItemCnt']; 
         $totalamountamount=$rows['totTaxAmt']; 
         $totalamount=$rows['totAmt'];

         $taxblAmtC= $rows['taxblAmtC']; 
         $taxblAmtD = $rows['taxblAmtD'];

         $ref = $rectype == 'R' ? $ref : 0;

         $receipt = '{"custTin":'.$tin.',"custMblNo":'.$phone.',"rptNo":1,"trdeNm":"KABC","adrs":"'.$company_name.'","topMsg":"'.$company_name.'\n'.$company_address.'\nTin: $branch_tin","btmMsg":"Welcome","prchrAcptcYn":"N"}';        
         $json = formatingJson($ref, $pmtTyCd, $taxblAmtA, $taxblAmtB, $lastSale, $tin, $purchase_code, $client_name, $salestype, $rectype, $totalitem, $totalamountamount, $totalamount, $receipt, $prdct, $taxblAmtC, $taxblAmtD, $cfmDt, $salesDt);
        // $json=$_POST['sales'];
        break;
      

    default:
      # code...
      break;
  }

  // echo $json;


  $curl = curl_init();
    /* die($url); */
    /* die($json); */
curl_setopt_array($curl, array(
  CURLOPT_URL => $url.'/trnsSales/saveSales',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$json,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

	// echo $response;

curl_close($curl);

$data = json_decode($response);

// echo $data->resultMsg;

$responseData = json_decode($response, true);

if (isset($responseData['resultCd'])) {
$code = $data->resultCd;
if($code=='000'){


  $res = json_encode($data->data);
  $res = json_decode($res);
  
  $rcptNo =  $res->rcptNo;
  $totRcptNo=  $res->totRcptNo;
  $vsdcRcptPbctDate=  $res->vsdcRcptPbctDate;
  $sdcId=  $res->sdcId;
  $mrcNo=  $res->mrcNo;
  $int=  $res->intrlData;
  $rcptSign= $res->rcptSign;
  $amount= $totalamount;


  // data to save as result from rra with response
  
  $sql_upd = $db->prepare("UPDATE tbl_vsdc_sales SET `has_refund` = '1' WHERE `invcNo`='".$ref."'");
  if( $rectype == 'R'){    
    $sql_upd->execute();
  }
    

if($salestype =='N' ){



  // $sql = "UPDATE cart_sales SET status='1' where session_id='$id'";
  // $conn->query($sql);


  $jsonMaster = array();
  $jsonIO = array();

  foreach (json_decode($prdct) as $object) {
    $qty = getStockValue($object->itemCd)-$object->qty;
    if( $rectype == 'R'){
      $qty = getStockValue($object->itemCd)+$object->qty;
    }
    
    $itemClass = getItemClass($object->itemCd);
    if($itemClass != 3){

    $jsonMaster[] = '{"tin":"'.$branch_tin.'",
      "bhfId":"03",
      "itemCd": "'.$object->itemCd.'",
      "rsdQty":"'.$qty.'",
      "regrId":"01",
      "regrNm":"Admin",
      "modrNm":"Admin",
      "modrId":"01"
    }';

    
    $type_id = getItemId($object->itemCd);
    $sql_upd = $db->prepare("UPDATE stock SET `quantities` = $qty WHERE `type`='".$type_id."'");
    $sql_upd->execute();
    }

  }

// sendStockMaster($jsonMaster);

$invoice = countIo();
$jsonIO[] = '{"tin":"'.$branch_tin.'",
  "bhfId":"03",
  "sarNo":"'.$invoice.'",
  "orgSarNo":"'.$invoice.'",
    "regTyCd":"M",
    "custTin":null,
    "custNm":null,
    "custBhfId":null,
    "sarTyCd":"03",
    "ocrnDt":"'.date("Ymd").'",
    "totItemCnt":"'.$totalitem.'",
    "totTaxblAmt":"'.$totalamount.'",
    "totTaxAmt":"'.number_format($totalamountamount, 2, '.', '').'",
    "totAmt":"'.$totalamount.'",
    "remark":"",
    "regrId":"01",
    "regrNm":"Admin",
    "modrNm":"Admin",
    "modrId":"01",
    "itemList":'.$prdct.'
  }';

// print_r($jsonIO);
 // sendStockIO($jsonIO);



 


}



  $receipt_info = addslashes('{"custTin":"'.$tin.'","custMblNo":null,"rptNo":1,"trdeNm":"KABC","adrs":"KN 4 Ave","topMsg":"'.$company_name.'\n'.$company_address.'\nTin: $branch_tin","btmMsg":"Welcome","prchrAcptcYn":"N"}');
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
  


$tin = $tin ?? '9999999999';
$receipt_info = addslashes('{"custTin":"$tin","custMblNo":"0789303811","rptNo":1,"trdeNm":"NSTC","adrs":"'.$company_address.'","topMsg":"'.$company_name.'\n'.$company_address.'\nTin: $branch_tin","btmMsg":"Welcome","prchrAcptcYn":"N"}');
  $sql_inf = $db->prepare("INSERT INTO tbl_receipts SET 
  tin='$branch_tin',
  bhfId='03',
  invcNo='$lastSale',
  orgInvcNo=0,

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

  if($pmtTyCd=='01'){
    $pmtTyCd='CASH';
  }
  if($pmtTyCd=='02'){
    $pmtTyCd='CREDIT';
  }
  if($pmtTyCd=='03'){
    $pmtTyCd='CASH/CREDIT ';
  }
  if($pmtTyCd=='04'){
    $pmtTyCd='BANK CHECK';
  }
  if($pmtTyCd=='05'){
    $pmtTyCd='DEBIT&CREDIT CARD';
  }
  if($pmtTyCd=='06'){
    $pmtTyCd='MOBILE MONEY';
  }
  if($pmtTyCd=='07'){
    $pmtTyCd='OTHER PAYMENTS';
  }

  $receitType = $salestype.$rectype;
  $lin = 'receiptT='.$receitType.'&taxbleAmount='.$taxblAmtB.'&taxA='.$taxA.'&taxB='.$taxB.'&ref='.$ref.'&no='.$rcptNo.'&total='
  .$totRcptNo.'&vs='.$vsdcRcptPbctDate.'&sdc='. $sdcId.'&mrc='.$mrcNo.'&amount='.$amount.'&product='.$prdct.'&int='.$int.'&sign='. $rcptSign.'&tin='. $tin.'&tax='.$totalamountamount;
  $lin .='&salestype='.$salestype.'&rectype='.$rectype.'&receiptNo='.$lastSale;
  $lin .='&totalC='.$taxblAmtC.'&totalD='.$taxblAmtD;
  $lin .='&names='.$agro_names.'&clMob='.$agro_mo;
  $lin .='&dateData='.$cfmDt;
  $lin .='&pmtTyCd='.$pmtTyCd;


  $sqlseason = $db->prepare("SELECT * FROM system_configuration");
  $sqlseason->execute();
  $rows = $sqlseason->fetch();


  $stmts = $db->prepare("INSERT INTO `receipts` (`receipt_url`, `agro`)
		VALUES ('$lin', '$client_name')");
	  //  $stmts->execute();

     $lastInsertId = $db->lastInsertId();
  
  $printer = $rows['printer'];
  if($printer=='paper_roll'){
    if($receitType == 'NS'){
      echo "<script>window.open('receipt/normal.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'NR'){
      echo "<script>window.open('receipt/normal-refund.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'CS'){
      echo "<script>window.open('receipt/copy.php?receipt=$lin', '_blank')</script>";
    }
    if($receitType == 'CR'){
      echo "<script>window.open('receipt/copy-refund.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'TS'){
      echo "<script>window.open('receipt/training.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'TR'){
      echo "<script>window.open('receipt/training-refund.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'PS'){
      echo "<script>window.open('receipt/proforma.php?$lin', '_blank')</script>";
    }
  }else{
    if($receitType == 'NS'){
      echo "<script>window.open('receipt/a4/normal.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'NR'){
      echo "<script>window.open('receipt/a4/normal-refund.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'CS'){
      echo "<script>window.open('receipt/a4/copy.php?$lin', '_blank')</script>";
    }
    if($receitType == 'CR'){
      echo "<script>window.open('receipt/a4/copy-refund.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'TS'){
      echo "<script>window.open('receipt/a4/training.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'TR'){
      echo "<script>window.open('receipt/a4/training-refund.php?$lin', '_blank')</script>";
    }
  
    if($receitType == 'PS'){
      echo "<script>window.open('receipt/a4/proforma.php?$lin', '_blank')</script>";
    }
  }
  
//   file_put_contents('receipts.txt', $lin."\n", FILE_APPEND);
  



  


}else{
  
  // $sqlseason = $db->prepare("DELETE FROM `sales` WHERE `uuid`='$uuid'");
  // $sqlseason->execute();
  
  echo "<script>alert('$data->resultMsg')</script>";
  
}


} else {
  echo "<script>alert('Lost connection to VSDC')</script>";
}
    
    // echo "<script>
    //     $(document).ready(function(){
    //     $('#modal3').modal('show'); 
    //     });
    //     </script>";
}
    

// }

 ?>
 <?php 
function fill_product($db){
  $output= '';

  // $select = $db->prepare("SELECT * FROM types INNER JOIN tbl_inventory ON types.type_id=tbl_inventory.item");
  $select = $db->prepare("SELECT * FROM types");
  $select->execute();
  $result = $select->fetchAll();

  foreach($result as $row){
    $output.='<option value="'.$row['type_id'].'">'.$row["type_name"].'</option>';
  }

  return $output;
}
?>
<body>

    <!-- Page Content -->
    <div id="page-wrapper">
    <br>
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
            <p>Sorry! Amount is greater than balance!</p>
          </div>
          <div class="modal-footer">
            <a href="of_sales" class="btn btn-default">Ok</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
            <p>Sorry! Quantity is greater than stock balance!</p>
          </div>
          <div class="modal-footer">
            <a href="of_sales" class="btn btn-default">Ok</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
            <p>Sales recorded successfully!</p>
          </div>
          <div class="modal-footer">
            <a href="of_sales" class="btn btn-default">Ok</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="data-table-list" style="padding: 30px;">
                   <form method="POST" action="" id="form">
                    <div class="row">
                      <div class="col-lg-12">
                           
                      <div class="col-lg-6">
                            <label for="inputAddress">Client TIN</label>
                            <input type="text" name="clientTin" class="form-control" >
                            <textarea style="visibility:hidden" name="sales" class="form-control"></textarea>
                            <label for="inputAddress">Names</label>
                            <input type="text" name="clientName" class="form-control">
                            <label for="inputAddress">Phone</label>
                            <input type="text" name="clientPhone" class="form-control">
                            <label for="inputAddress">Purchase code</label>
                            <input type="text" name="purchasecode" id="purchasecode" autocomplete="off" class="form-control">
                            
                        </div>

                            <div class="col-lg-6">
                                <label for="inputAddress">Sales Type</label>
                                <Select name="salestype" id="salestype" placeholder="C,N,P,T" autocomplete="off" class="form-control aptc_margin" required>
                                <option value="P">Proforma</option>
                                <option value="N">Normal</option>
                                  <option value="C">Copy</option>
                                  <option value="T">Training</option>
                                  
                                </Select>
                                
                            </div>

                            <div class="col-lg-6">
                                <label for="inputAddress">Recipient type</label>
                                <select type="text" name="rectype" id="rectype" placeholder="S for sale or R of Refund" autocomplete="off" class="form-control aptc_margin" required>
                                  <option value="S">Sales</option>
                                  <option value="R">Refund</option>
                                </select>
                                
                            </div>
                            
                            
                            <div class="col-lg-6">
                                <label>Payment Method</label>
                                <select id="pmtTyCd" name="pmtTyCd" class="form-control selectpicker" data-live-search="true">
                                    <option value=""></option>

                                                                        <?php $cuntries = getInfoTable(7);
                                      foreach ($cuntries as $key => $value) {
                                    ?>
                                    <option value="<?php echo trim($value['code']) ?>"><?php echo $value['code_name'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                            

                            <div class="col-lg-6" id="prevsales">
  <label for="salesprev" class="form-label">Invoice no</label>

  <div class="d-flex gap-2">
    <input type="text" name="salesprev" id="salesprev" class="form-control selectpicker" data-live-search="true">
    <button class="btn btn-success" type="button" id="searchBtn">Search</button>
  </div>
</div>


                                      </div>

                            
                            </div>

                            <hr>

                                
                            <h4>List of items </h4>
                            <div class="row">

                              <div class="col-lg-2">
                              Select item
                                  <select class="form-control" id="itemselect">
                                    <option value="">--Add item--</option>
                                    <?php
                                    $sql = $db->prepare("SELECT * FROM menu");
                                    $sql->execute();
                                    while($rowss = $sql->fetch()){
                                    ?>
                                    <option value="<?php echo $rowss['itemCd'];?>"><?php echo $rowss['menu_name']; ?></option>
                                    <?php } ?>
                                </select>
                              </div>
                              <div class="col-lg-2">
                               Quantity<input class="form-control" type="number" id="qty" name="quantities">
                               
                            
                            <input class="form-control" type="hidden" id="transport" name="transport" value="0">
                            
                            <input class="form-control" type="hidden" id="discount" name="discount" value="0">
                            <input class="form-control" type="hidden" id="aptc_margin1" name="aptc_margin" value="0">
                                    </div>
                            <div class="col-lg-2">
                              <br>
                              <button class="btn btn-primary sendBtn" onclick="selectitemvsd()">Select</button>
                            </div>
                            </div>
                            <br>
                            

                               
<?php 

$sql = "SELECT * FROM cart_sales where status='0'";
$result = $conn->query($sql);
if ($result->rowCount() > 0) {

?>
                                <table class="table items">
                                <tr>
                                  <td>Item</td>
                                  <td>Tax Rate</td>
                                  <td>Price</td>
                                 
                                  <td>Quantity</td>
                                  <td>T.Price</td>
                                  <td>Action</td>
                                </tr>
                                
<?php 

$item_list= "";

$no = 0;
$tax = 0;
$totalNoTax =0;
$totalWithTax = 0;
$totaltablAmt = 0;
$totTaxA = 0;
$totTaxC = 0;
$totTaxD = 0;


// for recipt
$taxblAmtA = 0;
$taxblAmtB = 0;

$taxblAmtC = 0;
$taxblAmtD = 0;

$totalamountamount = 0;

 $sum = 0;

 
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
$no  = $no + 1;
 
// $sum = $sum + (getCartItemprice($row['item_id'])* $row['qty'] - ((getCartItemprice($row['item_id'])* $row['qty']) * (getdiscount($row['item_id'])/100)));
$sum = $sum + (getCartItemprice($row['item_id'])* $row['qty']);

$t = getCartItemprice($row['item_id']) * $row['qty'];
$t = $t - ($t * (getdiscount($row['item_id'])/100));
if(getax($row['item_id'])=='B'){

  $totaltablAmt += $t;
  $taxblAmtB += $t;
  $tax += $t * 18/118;

}elseif(getax($row['item_id'])=='A'){
 $tax += 0;
 $totTaxA += $t;
 $taxblAmtA += $t;
}elseif(getax($row['item_id'])=='C'){
  $tax += 0;
  $taxblAmtC += $t;
}elseif(getax($row['item_id'])=='D'){
  $tax += 0;
  $taxblAmtD += $t;
}
$totalamountamount = $tax;

$item_list = $item_list . getitemlist($row['item_id'],$no);
$sump = getCartItemprice($row['item_id'])* $row['qty'];
$Tprice = $sump-($sump * (getdiscount($row['item_id'])/100));

echo "<tr><td>".getCartItemname($row['item_id']). "</td><td>".getCartItemTaxRate($row['item_id'])."</td><td>".getCartItemprice($row['item_id'])."</td><td>".$row['qty']."</td><td>".$Tprice."</td><td><a href='?resto=ebm&item=".$row['cart_id']."&action=delete' class='btn btn-danger'>Delete</a></td></tr>
  <input type='hidden' name='quantity[]' value='".$row['qty']."'>
  <input type='hidden' name='amount[]' value='".getCartItemprice($row['item_id'])* $row['qty']."'>
  <input type='text' name='transport[]' value='".$row['transport']."'>
  <input type='hidden' name='discount[]' value='".$row['discount']."'>
  <input type='hidden' name='type_id[]' value='".$row['type_id']."'>
  <input type='hidden' name='aptc_margin[]' value='".$row['aptc_margin']."'>
  <input type='hidden' name='price[]' value='".getCartItemprice($row['item_id'])."'>
";

  }

echo $sum;
?>


<tr>
  <td colspan="2"></td>
  <td>Total</td>
  <td><?php echo $sum; ?></td>
  <input type="hidden" name="taxableAmount" value="<?php echo $totaltablAmt; ?>">
  <input type="hidden" name="totTaxA" value="<?php echo $totTaxA; ?>">
  <input type="hidden" name="taxBamount" value="<?php echo $tax; ?>">

  <input type="hidden" name="taxblAmtA" value="<?php echo $taxblAmtA; ?>">
  <input type="hidden" name="taxblAmtB" value="<?php echo $taxblAmtB; ?>">

  
  <input type="hidden" name="taxblAmtC" value="<?php echo $taxblAmtC; ?>">
  <input type="hidden" name="taxblAmtD" value="<?php echo $taxblAmtD; ?>">

</tr>
<tr>
  <td colspan="2"></td>
  <td>VAT</td>
  <td><?php echo $tax; ?></td>
  <input type="hidden" name="vat" value="<?php echo $sum; ?>">
</tr>
<tr>
  <td colspan="2"></td>
  <td>Total</td>
  <td><?php echo $sum+$tax; ?></td>
</tr>
</table>
<?php }  ?>
<table class="table prevsalesTable">

</table>



<?php
function getCartItemname($id){
//    include 'link.php';
global $conn;
  $sql = "SELECT * FROM menu where itemCd='$id'";
  $result = $conn->query($sql);
  
  if ($result->rowCount() > 0) {
    // output data of each row
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
   
      return $row['menu_name'];
  
  
    }
  }

}


function getCartItemTaxRate($id){
//   include 'link.php';
global $conn;
 $sql = "SELECT * FROM menu where itemCd='$id'";
 $result = $conn->query($sql);
 
 if ($result->rowCount() > 0) {
   // output data of each row
   while($row = $result->fetch(PDO::FETCH_ASSOC)) {
  
     return $row['taxTyCd'];
 
 
   }
 }

}


function getax($id){
// include 'link.php';
global $conn;
 $sql = "SELECT * FROM menu where itemCd='$id'";
 $result = $conn->query($sql);
 
 if ($result->rowCount() > 0) {

   while($row = $result->fetch(PDO::FETCH_ASSOC)) {
  
     return $row['taxTyCd'];
 
   }
 }

}


function getCartItemprice($id){
//   include 'link.php';
global $db;
 $sql = "SELECT * FROM menu where itemCd='$id'";
 $result = $db->query($sql);
 
 if ($result->rowCount() > 0) {
   // output data of each row
   while($row = $result->fetch(PDO::FETCH_ASSOC)) {
  
     return $row['dftPrc'];
 
 
   }
 }

}

function getquantity($id){
//   include 'link.php';
global $conn;
//   $user = $_SESSION['agro_id'];
  $sum= 0;

  $sql = "SELECT * FROM cart_sales where item_id='$id' AND status=0";
  $result = $conn->query($sql);
  
  if ($result->rowCount() > 0) {
    // output data of each row
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
   
      $sum = $sum + $row['qty'];
  
  
    }
  }
return $sum;
}

function getdiscount($id){
//   include 'link.php';
//   $user = $_SESSION['agro_id'];
global $conn;
  $sum= 0;

  $sql = "SELECT * FROM cart_sales where item_id='$id' AND status=0";
  $result = $conn->query($sql);
  
  if ($result->rowCount() > 0) {
    // output data of each row
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
   
      $sum = $sum + $row['discount'];
  
  
    }
  }
return $sum;
}


function getitemlist($id,$no){
//   include 'link.php';
global $conn;
$list = "";
$totalbtax = 0;
$totalamountamount = 0;
 $sql = "SELECT * FROM menu where itemCd='$id'";
 $result = $conn->query($sql);
 
 if ($result->rowCount() > 0) {
   // output data of each row
   while($row = $result->fetch(PDO::FETCH_ASSOC)) {
     
     $qty = getquantity($id);
     $sum =  $row['dftPrc'] * $qty;
     

     $discount = $sum - ($sum * (getdiscount($id)/100));

    if($row['taxTyCd']=='B'){
     $btax =  $sum * 18/118;
    }else{
      $btax = 0;
    }
     $totalbtax = str_replace(',', '',number_format($btax,2));

     $list = '{"itemSeq":'.$no.',"itemCd":"'.$row['itemCd'].'","itemClsCd":"'.$row['itemClsCd'].'","itemNm":"'.$row['itemNm'].'","bcd":null,"pkgUnitCd":"'.$row['pkgUnitCd'].'","pkg":1,"qtyUnitCd":"'.$row['qtyUnitCd'].'","qty":'.$qty.',"prc":'.$row['dftPrc'].',"splyAmt":'.$sum.',"dcRt":0,"dcAmt":0, "totDcAmt":'.$discount.', "isrccCd":null,"isrccNm":null,"isrcRt":null,"isrcAmt":null,"taxTyCd":"'.$row['taxTyCd'].'","taxblAmt":'.$sum.',"taxAmt":'.$totalbtax.',"totAmt":'.$sum.'},';
 
   }
 }
 return $list;
}

?>
 <div class="col-lg-6">
    <!-- <label for="inputAddress">Total tax</label> -->
    <input type="hidden" name="totalamountamount" id="totalamountamount" value="<?php echo $totalamountamount ?>" autocomplete="off" class="form-control" required>
    <!-- <label for="inputAddress">Total amount</label> -->
    <input type="hidden" name="totalamount" id="totalamount" value="<?php echo $sum ?>" autocomplete="off" class="form-control " required readonly>
    <!-- <label for="inputAddress">Total items</label> -->
    <input type="hidden" name="totalitem" id="totalitem" value="<?php echo $no ?>" autocomplete="off" class="form-control" required>
    <!-- <label for="inputAddress">Product</label> -->
    <input type="hidden" name="productList" id="productList" value='<?php echo $item_list; ?>' autocomplete="off" class="form-control aptc_margin" required>
    <br><br>
</div>
<br>
<div class="buttons">
<center><button class="btn btn-success" name="save" id="save">Save</button> 
 <a href="lstsales" class="btn btn-default"><i class="fa fa-backward"></i> Back</a></center>
</div>

</form>
        <div class="clearfix"></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</div></div>
<!-- /#page-wrapper -->

<script>
function selectitemvsd() {
  var x = document.getElementById("itemselect").value;
  var qty = document.getElementById("qty").value;
  var transport = document.getElementById("transport").value;
  var discount = document.getElementById("discount").value;
  var aptc_margin = document.getElementById("aptc_margin1").value;

var id = "0";
var data = new FormData();
data.append("item",x);
data.append("qty",qty);
data.append("user", id);
data.append("transport", transport);
data.append("discount", discount);
data.append("aptc_margin", aptc_margin)
var xhr = new XMLHttpRequest();
xhr.withCredentials = true;

xhr.addEventListener("readystatechange", function() {
  if(xhr.readyState === 4) {
    var resp = xhr.responseText.trim();
    console.log(resp);
    if(xhr.responseText.trim() == '000'){
      alert("No enough stock");
    }else{
      location.reload();
      
    }
  }
});

xhr.open("POST", "addtocart.php");

xhr.send(data);

}
</script>



<script type="text/javascript">
    $(document).ready(function () {
      
      $("#searchBtn").click(function (e) {
        e.preventDefault();
        loadPrevSales();
    });


        function loadPrevSales() {
          var branch = $("#salesprev").val();
          // console.log(branch);

          $.post("prevsales.php", { item: branch }, function(data) {
            console.log(data);
            
              $(".items").hide();
              $(".prevsalesTable").html(data.table);
              $("input[name='clientName']").val(data?.names);
              $("input[name='clientPhone']").val(data.phone?.replace(/"/g, ''));
              $("input[name='clientTin']").val(data.tin?.replace(/"/g, ''));
              $("textarea[name='sales']").val(JSON.stringify(data.sale));
          });
      }


        $("#branch").change(function () {
            var branch = $('#branch').val();
             $.post("load_of_sales.php", { branch:branch}, function(data){
                 
                 $("#display").html(data);
                 location.reload();
                });
        });
        
        });
  </script>           
    <script type="text/javascript">
    $(document).ready(function () {
      $("#prevsales").hide();
        function updatePrice() {
          
          $.ajax({
            url: "../inc/testfunc.php",
            method: "POST",
            data: {
              salestype: $("select[name='salestype']").val(),
              rectype: $("select[name='rectype']").val(),
              tin: "00",
              fetchdata: 'fetchdata',
            },
            
            success: function (data) {
              // console.log(data);
              $("select[name='salesprev'").append(data);
              $("select[name='salesprev'").selectpicker('refresh');
            },
          });
        }

        // Add an on() event handler to the select boxes.
        $("select[name='salestype'], select[name='rectype']").change(function(){
          $("select[name='salesprev'").empty();
          var salestype = $("select[name='salestype']").val();
          var rectype = $("select[name='rectype']").val();
          if(salestype == 'C' || rectype=='R'){
            $('#pmtTyCd').prop('disabled', true);
            
            $("#prevsales").show();
            $(".prevsalesTable").show();
            $(".items").hide();
          }else{
            $('#pmtTyCd').prop('disabled', false);
            $("#prevsales").hide();
            $(".prevsalesTable").hide();
            $(".items").show();
          }
          updatePrice();
        });




        $("#item").change(function () {
            var item = $("#item").val();
            $.ajax({
            url:"load_price.php",
            method:"POST",
            data:{item:item},
            dataType:"json",
            success:function(data)
    {
        $("#price").val(data);
    }
            });
        });

        var quantity = $(this);
        var bal = parseInt($("#bal").val());
        var amount = parseInt($('#totalamount').val());
        // console.log("amount: ",amount);
        // console.log("balance: ",bal);
        
        
        // if (bal >= amount) {
        //     $("#error").html("<label class='label label-success'>Balance Enough!</label>");
        //   } else {
        //     $("#error").html("<label class='label label-danger'>Balance not Enough!</label>");
        //     // $(".buttons").hide();
        //   }
     
        });


    
  </script> 
