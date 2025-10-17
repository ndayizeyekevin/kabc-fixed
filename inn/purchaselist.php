<?php
// include('auth.php');
// include('link.php');
// include('links.php');
// include('functions.php');
// include('rra.php');



if(isset($_POST['dateInput'])){
    $date = str_replace("-", "", $_POST['dateInput'])."000000";
    $jsonData = json_encode(array(
    
      "tin" => "$branch_tin",
      "bhfId" => "00",
      "lastReqDt" => $date
          
    ));


    
    $dateNow = date('YmdHis');

    $sql = "SELECT * FROM `tbl_vsdc_purchase` WHERE lastReqDt >='".$dateNow."'";
      $result = $conn->query($sql);

      if ($result->rowCount() > 0) {
        
      
        echo "<script>alert('Purchases on this date are alread imported')</script>";
        
      }else{

        echo $jsonData;
$resp= rra_function($jsonData, 'trnsPurchase/selectTrnsPurchaseSales');

// print_r($resp);

if($resp['resultCd'] == '000') {
    $items = $resp['data']['saleList'];
    foreach($items as $data):

      $itemsList = $data['itemList'];
      foreach ($itemsList as &$item) {
            $item['totDcAmt'] = $item['dcAmt'];
            
      }

    // print_r($itemsList);

    $db->prepare("INSERT INTO tbl_vsdc_purchase 
    SET spplrTin = '".$data['spplrTin']."',
        spplrNm = '".$data['spplrNm']."',
        spplrBhfId = '".$data['spplrBhfId']."',
        spplrInvcNo = '".$data['spplrInvcNo']."',
        rcptTyCd = '".$data['rcptTyCd']."',
        pmtTyCd = '".$data['pmtTyCd']."',
        cfmDt = '".$data['cfmDt']."',
        salesDt = '".$data['salesDt']."',
        stockRlsDt = '".$data['stockRlsDt']."',
        totItemCnt = '".$data['totItemCnt']."',
        taxblAmtA = '".$data['taxblAmtA']."',
        taxblAmtB = '".$data['taxblAmtB']."',
        taxblAmtC = '".$data['taxblAmtC']."',
        taxblAmtD = '".$data['taxblAmtD']."',
        taxRtA = '".$data['taxRtA']."',
        taxRtB = '".$data['taxRtB']."',
        taxRtC = '".$data['taxRtC']."',
        taxRtD = '".$data['taxRtD']."',
        taxAmtA = '".$data['taxAmtA']."',
        taxAmtB = '".$data['taxAmtB']."',
        taxAmtC = '".$data['taxAmtC']."',
        taxAmtD = '".$data['taxAmtD']."',
        totTaxblAmt = '".$data['totTaxblAmt']."',
        totTaxAmt = '".$data['totTaxAmt']."',
        totAmt = '".$data['totAmt']."',

        lastReqDt = '".$dateNow."',

        remark = '".$data['remark']."',
        itemList = '".json_encode($itemsList)."'"
        )->execute();

    endforeach;

  }
}

}



if(isset($_POST['taskCd'])){

    $taskCd=$_POST['taskCd'];
    $pmtTyCd=$_POST['pmtTyCd'];
    $pchsSttsCd=$_POST['pchsSttsCd'];

    $sqlseason = $db->prepare("SELECT * FROM `tbl_vsdc_purchase` WHERE `id`='$taskCd'");
    $sqlseason->execute();
    $rows = $sqlseason->fetch();

  
      $json =  '{"tin":"'.$branch_tin.'",
        "bhfId":"'.$branch_no.'",
        "invcNo":"'.$rows['id'].'",
        "orgInvcNo":"'.$rows['spplrInvcNo'].'",
        "spplrTin":"'.$rows['spplrTin'].'",
        "spplrBhfId":"'.$rows['spplrBhfId'].'",
        "spplrNm":"'.$rows['spplrNm'].'",
        "spplrInvcNo":"'.$rows['spplrInvcNo'].'",
        "regTyCd":"M",
        "pchsTyCd":"N",
        "rcptTyCd":"P",
        "pmtTyCd":"'.$pmtTyCd.'",
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
  
      echo $json;

    $invoice = countIo();
    $jsonIO[] = '{"tin":"'.$branch_tin.'",
      "bhfId":"'.$branch_no.'",
      "sarNo":"2",
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
      "itemList":'.$rows['itemList'].'
    }';
  
  //  print_r($jsonIO);
      
      $response = rra_function($json, 'trnsPurchase/savePurchases');
      
      echo $response;
    
      if (isset($response['resultCd'])) {
      $code = $response['resultCd'];

      if($code=='000'){

        // print_r(sendStockIO($jsonIO));

        $jsonMaster = array();
        foreach (json_decode($rows['itemList']) as $object) {
          $qty = getStockValue($object->itemCd)+$object->qty;
          $jsonMaster[] = '{"tin":"'.$branch_tin.'",
            "bhfId":"'.$branch_no.'",
            "itemCd": "'.$object->itemCd.'",
            "rsdQty":"'.$qty.'",
            "regrId":"01",
            "regrNm":"Admin",
            "modrNm":"Admin",
            "modrId":"01"
          }';
      
          $type_id = getItemId($object->itemCd);
          // $sql_upd = $db->prepare("UPDATE stock SET `quantities` = $qty WHERE `type`='".$type_id."' AND branch = '$branch'");
          // $sql_upd->execute();
      
        }

        print_r(sendStockMaster($jsonMaster));
        
        $db->prepare("UPDATE `tbl_vsdc_purchase` SET `status`='1' WHERE id='".$taskCd."'")->execute();
  
        echo "<script>alert('".$response['resultMsg']."')</script>";
      }else{
          echo "<script>alert('".$response['resultMsg']."')</script>";
      }
    }
  }





?>

<body>
 
<div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h1 class="page-header">Purchase List</h1>
                    <div class="pull-right data-table-list">

                <div class="container mt-5">
    <form method="post">
        <div class="form-row">
            <div class="col-md-8 mb-3">
                <label for="dateInput">Select a Date:</label>
                <input type="date" class="form-control" id="dateInput" name="dateInput" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>&nbsp;</label> <!-- Empty label for spacing -->
                <button type="submit" class="btn btn-primary btn-block">Select</button>
            </div>
        </div>
    </form>
</div><br><br>
                <div class="panel-body">
                
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier TIN </th>
                            <th> Supplier Name </th>
                            <th> Total Item Count </th>
                            <th>Invoice</th>
                            <th>Total Amount</th>
                            <th>Items</th>
                            <th>Action</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                      <?php 
                       $i = 0;

                       $sql = $db->prepare("SELECT * FROM tbl_vsdc_purchase ORDER BY id DESC");
                       $sql->execute();
                       while($data = $sql->fetch()){
                         $i++;
                      ?>
                      <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data['spplrTin']; ?></td>
                        <td><?php echo $data['spplrNm']; ?></td>
                       
                        <td><?php echo $data['totItemCnt']; ?></td>
                        <td><?php echo $data['spplrInvcNo']; ?></td>
                        <td><?php echo $data['totAmt']; ?></td>
                        <td><?php
                        $items = json_decode($data['itemList'],true);
                        foreach ($items as $key => $value) {
                            echo $value['itemNm'].'=>'.$value['itemCd'].'=>'.$value['qty'].''.$value['qtyUnitCd'].'<br>';
                        } ?></td>
                        <td>
                        <?php
                        if($data['status'] == 0){

                        ?>  
                        <a href="index?resto=purchaseDetail&invo=<?php echo $data['id']; ?>" class="btn btn-success">
                            Save purchase</a>

                        <?php
                        }else{
                          ?>
                          <a href="#" class="btn btn-danger">Saved</a>
                        <?php
                        }
                        ?>
                        </td>
                      </tr>
                      <?php  
                      }?>
                </tbody>
            </table>

            <div class="clearfix"></div>
        </div>
    </div>
    <!-- /.row -->

    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
           <form method="post">
           <div class="form-group">
              <label for="itemSelect">Select Item:</label>
              <select class="form-control" id="itemSelect" name="item">
              <option value="">--Add item--</option>
                                    <?php
                                    $sql = $db->prepare("SELECT * FROM menu");
                                    $sql->execute();
                                    while($rowss = $sql->fetch()){
                                    ?>
                                    <option value='{"values":["<?php echo $rowss['itemCd'];?>","<?php echo $rowss['itemClsCd'];?>"]}'><?php echo $rowss['menu_name']; ?></option>
                                    <?php } ?>
              </select>
            </div>
           <div class="form-group">
            
            <label for="inputAddress">Payment Type Code</label>
            <select name="pmtTyCd" class="form-control selectpicker" data-live-search="true">
              <option value="01">Cash</option>
              <option value="02">Credit</option>
              <option value="03">Cash/Credit</option>
              <option value="04">Bank Check</option>
              <option value="05">Bedit&Credit Card</option>
              <option value="06">Mobile Money</option>
              <option value="07">Other</option>
            </select>
                    </div>
            <div class="form-group">
            
                          
                       
                            <label for="inputAddress">Purchase Status Code</label>
                            <select name="pchsSttsCd" class="form-control selectpicker" data-live-search="true">
                              <option value="02">Approved</option>
                              <option value="01">Wait for Approval</option>
                              <option value="03">Cancel Requested</option>
                              <option value="04">Canceled</option>
                              <option value="05">Refunded</option>
                              <option value="06">Transferres</option>
                            </select>
                        
              
            </div>

            <input type="hidden" name="taskCd">

            <!-- Buttons for form actions -->
            <div class="text-right">
              <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
              <button type="submit" class="btn btn-primary">Save purchase</button>
            </div>
          </form>
          </div>
          <div class="modal-footer">
            <a href="purchaselist" class="btn btn-default">Close</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



</div>
    </div>


<script>
  // JavaScript to handle modal events
  $('#modal1').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var taskCd = button.data('id');
    
    $('input[name="taskCd"]').val(taskCd);

  });
</script>