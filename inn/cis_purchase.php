<?php

error_reporting(1);


$id = $_GET['invo'];

$sql = "SELECT * FROM `tbl_vsdc_purchase` WHERE id='".$id."'";
$sql1 = $db->prepare($sql);
$sql1->execute();
$row = $sql1->fetch();
$invo = $row['invcNo'];
$data = json_decode($row['itemList'], true);
$items = json_decode($row['itemList']);


 ?>


<?php 
if(isset($_POST['pchsSttsCd'])){

  $new_item = $_POST['item'];
  
  $new_qty = $_POST['qty'];


  $count = 0;
  foreach ($data as &$item) {

    
    $submittedValues = json_decode($new_item[$count]);

    $item['itemCd'] = $submittedValues[1];
    $item['itemClsCd'] = $submittedValues[2];
    $item['itemNm'] = $submittedValues[0];
    $item['qty'] = $new_qty[$count];
    $item['qtyUnitCd'] = $submittedValues[3];
    $item['pkgUnitCd'] = $submittedValues[4];
    

    $count++;
  }

  $new_items = json_encode($data);
  // echo $new_items;


  $pchsSttsCd=$_POST['pchsSttsCd'];

  $sqlseason = $db->prepare("SELECT * FROM `tbl_vsdc_purchase` WHERE `id`='$id'");
  $sqlseason->execute();
  $rows = $sqlseason->fetch();


    $json =  '{"tin":"'.$branch_tin.'",
      "bhfId":"00",
      "invcNo":"'.$rows['id'].'",
      "orgInvcNo":"'.$rows['spplrInvcNo'].'",
      "spplrTin":"'.$rows['spplrTin'].'",
      "spplrBhfId":"'.$rows['spplrBhfId'].'",
      "spplrNm":"'.$rows['spplrNm'].'",
      "spplrInvcNo":"'.$rows['spplrInvcNo'].'",
      "regTyCd":"M",
      "pchsTyCd":"N",
      "rcptTyCd":"P",
      "pmtTyCd":"'.$rows['pmtTyCd'].'",
      "pchsSttsCd":"'.$pchsSttsCd.'",
      "cfmDt":"'.date("YmdHis").'",
      "pchsDt":"'.date("Ymd").'",
      "wrhsDt":"'.$rows['wrhsDt'].'",
      "cnclReqDt":"'.$rows['cnclReqDt'].'",
      "cnclDt":"'.$rows['cnclDt'].'",
      "rfdDt":"'.$rows['rfdDt'].'",
      "totItemCnt":"'.$rows['totItemCnt'].'",
      "taxblAmtA":"'.$rows['taxblAmtA'].'",
      "taxblAmtB":"'.$rows['taxblAmtB'].'",
      "taxblAmtC":"'.$rows['taxblAmtC'].'",
      "taxblAmtD":"'.$rows['taxblAmtD'].'",
      "taxRtA":"'.$rows['taxRtA'].'",
      "taxRtB":"'.$rows['taxRtB'].'",
      "taxRtC":"'.$rows['taxRtC'].'",
      "taxRtD":"'.$rows['taxRtD'].'",
      "taxAmtA":"'.$rows['taxAmtA'].'",
      "taxAmtB":"'.$rows['taxAmtB'].'",
      "taxAmtC":"'.$rows['taxAmtC'].'",
      "taxAmtD":"'.$rows['taxAmtD'].'",
      "totTaxblAmt":"'.$rows['totTaxblAmt'].'",
      "totTaxAmt":"'.$rows['totTaxAmt'].'",
      "totAmt":"'.$rows['totAmt'].'",
      "remark":"'.$rows['remark'].'",
      "regrNm":"admin",
      "regrId":"01",
      "modrNm":"admin",
      "modrId":"01",
      "itemList":'.$rows['itemList'].'
    }';

    // echo $json;
    // echo "<br>";

  $invoice = countIo();
  $jsonIO[] = '{"tin":"'.$branch_tin.'",
    "bhfId":"00",
    "sarNo":"'.$invoice.'",
    "orgSarNo":"'.$invoice.'",
    "regTyCd":"M",
    "custTin":null,
    "custNm":null,
    "custBhfId":null,
    "sarTyCd":"02",
    "ocrnDt":"'.date("Ymd").'",
    "totItemCnt":"'.$rows['totItemCnt'].'",
    "totTaxblAmt":"'.$rows['totTaxblAmt'].'",
    "totTaxAmt":"'.$rows['totTaxAmt'].'",
    "totAmt":"'.$rows['totAmt'].'",
    "remark":"'.$rows['remark'].'",
    "regrId":"01",
    "regrNm":"Admin",
    "modrNm":"Admin",
    "modrId":"01",
    "itemList":'.$new_items.'
  }';

//  print_r($jsonIO);
//  echo "<br>";


      $jsonMaster = array();
      foreach (json_decode($new_items) as $object) {
        $qty = getStockValue($object->itemCd)+$object->qty;

        // echo "stock ".getStockValue($object->itemCd)."<br>";
        // echo "new: ".$object->qty;

        $jsonMaster[] = '{"tin":"'.$branch_tin.'",
          "bhfId":"00",
          "itemCd": "'.$object->itemCd.'",
          "rsdQty":"'.$qty.'",
          "regrId":"01",
          "regrNm":"Admin",
          "modrNm":"Admin",
          "modrId":"01"
        }';
    
        if($pchsSttsCd == '02'){
          $type_id = getItemId($object->itemCd);
          $sql_upd = $db->prepare("UPDATE stock SET `quantities` = $qty WHERE `type`='".$type_id."'");
          $sql_upd->execute();
        }
        
    
      }

      // print_r($jsonMaster);
      // echo "<br>";
      // echo $json;
    
    $response = rra_function($json, 'trnsPurchase/savePurchases');

  print_r($response);
  
    if (isset($response['resultCd'])) {
    $code = $response['resultCd'];

    if($code=='000'){

      // print_r(sendStockIO($jsonIO));
      // print_r(sendStockMaster($jsonMaster));
      if($pchsSttsCd == '02'){
        sendStockIO($jsonIO);
        sendStockMaster($jsonMaster);
      }
      
      $db->prepare("UPDATE `tbl_vsdc_purchase` SET `status`='1' WHERE id='".$id."'")->execute();

      echo "<script>alert('".$response['resultMsg']."')</script>";
        echo "<script>
        $(document).ready(function(){
        $('#modal').modal('show'); 
        });
        </script>";
    }else{
        echo "<script>alert('".$response['resultMsg']."')</script>";
    }
  }
}


?>

<body>
    <!-- <?php include('navfixed.php');?> -->
<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">APTC</h4>
      </div>
       <div class="modal-body">
        <p>Saved Successfully!</p>
      </div>
      <div class="modal-footer">
        <a href="index?resto=purchase" class="btn btn-default">Ok</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <!-- Page Content -->
    <div id="page-wrapper">
   
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h1 class="page-header">Purchase registration</h1>
                    <div class="pull-right data-table-list">

                <div class="container mt-5">
                
            </div>
            <form method="POST">
                <div class="panel-body">
                        <div class="row">

                        
                        <div class="col-lg-4">
                            <label for="inputAddress">Purchase Status Code</label>
                            <select name="pchsSttsCd" class="form-control" data-live-search="true">
                              <option value="02">Approved</option>
                              <option value="04">Canceled</option>
                              <option value="05">Refunded</option>
                              <option value="06">Transferres</option>
                            </select>
                        </div>
                        </div>
                     
        <br>

<?php 


    foreach ($items as $value) {
      # code...
   
?>
  <div class="row">
  <div class="col-lg-2">
     <h3><?php echo $value->itemNm; ?></h3>
      
 </div>
  <div class="col-lg-2">
        Select item
        <select class="form-control" name="item[]" required>
      <option value="">--Add item--</option>
      <?php
      $sql = $db->prepare("SELECT * FROM menu where product_type !=3");
      $sql->execute();
      while($rowss = $sql->fetch()){
      ?>
      <option value='<?php echo json_encode([$rowss['menu_name'], $rowss['itemCd'], $rowss['itemClsCd'],  $rowss['qtyUnitCd'], $rowss['pkgUnitCd']]); ?>'><?php echo $rowss['menu_name']; ?></option>
      <?php } ?>
      </select>
 </div>
 
  <div class="col-lg-2">
      Quantity Kg
      <input type="number" class="form-control" name="qty[]" id="qty" value="<?php echo $value->qty ?>">
 </div>
 

 <div class="col-lg-2">
        <br>
     
 </div>
 </div>
 <?php  } ?>



<div class="col-lg-12">
  <br>
    <input type="submit" class="btn btn-success" name="save_purchase" value="save purchase">
</div>
</form>




<!-- <div class="clearfix"></div> -->
        </div>
    </div>


    </div>
    </div>

        <script src="js/jquery.js"></script>
    
<!-- /#page-wrapper -->

<?php include('scripts.php'); ?>


