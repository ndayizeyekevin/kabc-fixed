<?php


// error_reporting(1);
if(isset($_POST['dateInput'])){
    $date = str_replace("-", "", $_POST['dateInput'])."000000";

    $dateNow = date('YmdHis');
    
    // $sql = "SELECT * FROM `tbl_vsdc_importations` WHERE LEFT(`lastReqDt`, 8)>='".substr($date, 0, 8)."'";
    $sql = "SELECT * FROM `tbl_vsdc_importations` WHERE lastReqDt >='".$dateNow."'";
      $result = $conn->query($sql);

      if ($result->rowCount() > 0) {

        echo "<script>alert('Importation on this date are alread imported')</script>";
        
      }else{



    $jsonData = '{"tin": "'.$branch_tin.'","bhfId": "00","lastReqDt": "'.$date.'"}';
    // echo $jsonData;

    $resp= rra_function($jsonData, 'imports/selectImportItems');
    $i = 0;

    // print_r($resp);

    if($resp['resultCd'] == '000') {
      $items = $resp['data']['itemList'];
      foreach($items as $data):
      $db->prepare("INSERT INTO `tbl_vsdc_importations` SET `taskCd`='".$data['taskCd']."', itemSeq='".$data['itemSeq']."', pkg='".$data['pkg']."',
       `dclDe`='".$data['dclDe']."', `dclNo`='".$data['dclNo']."', `hsCd`='".$data['hsCd']."', `itemNm`='".addslashes($data['itemNm'])."', `imptItemsttsCd`='".$data['imptItemsttsCd']."', `orgnNatCd`='".$data['orgnNatCd']."', 
       `exptNatCd`='".$data['exptNatCd']."', `qty`='".$data['qty']."', `qtyUnitCd`='".$data['qtyUnitCd']."', `totWt`='".$data['totWt']."', `netWt`='".$data['netWt']."', `spplrNm`='".addslashes($data['spplrNm'])."', `agntNm`='".addslashes($data['agntNm'])."',
        `invcFcurAmt`='".$data['invcFcurAmt']."', `invcFcurCd`='".$data['invcFcurCd']."', `invcFcurExcrt`='".$data['invcFcurExcrt']."', `lastReqDt`='".$dateNow."', `status`='".$data['imptItemsttsCd']."'")->execute();
      endforeach;

    }
  }
   
}

if(isset($_POST['imptItemSttsCd'])){

  $taskCd=$_POST['taskCd'];
  // $itemNm= $_POST['itemNm']; 
  // $qty= $_POST['qty'];
  $dclDe= $_POST['dclDe'];
  $itemSeq= $_POST['itemSeq'];
  $hsCd= $_POST['hsCd'];
  // $qtyUnitCd = $_POST['qtyUnitCd'];

    $itemJson = json_decode($_POST['item'], true);
    $item = $itemJson['values'];
    $itemCd = $item[0];
    $itemClsCd = $item[1];

    $json = '{
        "tin": "'.$branch_tin.'",
        "bhfId": "00",
        "taskCd": "'.$_POST['taskCd'].'",
        "dclDe": "'.$_POST['dclDe'].'",
        "itemSeq": "'.$_POST['itemSeq'].'",
        "hsCd": "'.$_POST['hsCd'].'",
        "itemClsCd": "'.$itemClsCd.'",
        "itemCd": "'.$itemCd.'",
        "imptItemSttsCd": "'.$_POST['imptItemSttsCd'].'",
        "remark": "remark",
        "modrNm":"Admin",
        "modrId":"Admin"
    }';

    // echo $json;

 

    $response = rra_function($json, 'imports/updateImportItems');
  
    // print_r($response);
    
    if (isset($response['resultCd'])) {
    $code = $response['resultCd'];
    if($code=='000'){

      $db->prepare("UPDATE `tbl_vsdc_importations` SET itemCd='".$itemCd."', `imptItemsttsCd`='".$_POST['imptItemSttsCd']."' WHERE taskCd='".$taskCd."'")->execute();



    $sqlseason = $db->prepare("SELECT * FROM `tbl_vsdc_importations` WHERE `taskCd`='$taskCd'");
    $sqlseason->execute();
    $rows = $sqlseason->fetch();

    $query = "SELECT * FROM `menu` WHERE `itemCd`='$itemCd'";
    
    $itemInfo = $db->prepare($query);
    $itemInfo->execute();
    $item = $itemInfo->fetch();

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
        "totItemCnt":1,
        "totTaxblAmt": 0,
        "totTaxAmt": 0,
        "totAmt":"'.$rows['invcFcurAmt'].'",
        "remark":null,
        "regrId":"01",
        "regrNm":"Admin",
        "modrNm":"Admin",
        "modrId":"01",
        "itemList":[{
          "itemSeq": 1,
          "itemCd": "'.$itemCd.'",
          "itemClsCd": "'.$item['itemClsCd'].'",
          "itemNm": "'.$item['itemNm'].'",
          "bcd": null,
          "pkgUnitCd": "'.$item['pkgUnitCd'].'",
          "pkg": "'.$rows['pkg'].'",
          "qtyUnitCd": "'.$item['qtyUnitCd'].'",
          "qty": "'.$rows['qty'].'",
          "prc": "'.$rows['invcFcurAmt'].'",
          "splyAmt": "'.$rows['invcFcurAmt'].'",
          "dcRt": 0,
          "dcAmt": 0,
          "taxTyCd": "'.$item['taxTyCd'].'",
          "taxblAmt": "'.$rows['invcFcurAmt'].'",
          "taxAmt": 0,
          "totAmt": "'.$rows['invcFcurAmt'].'",
          "totDcAmt": 0
      }]
      }';
      



        
        $jsonMaster = array();
        
        $qty = getStockValue($itemCd)+$rows['qty'];
        $jsonMaster[] = '{"tin":"'.$branch_tin.'",
          "bhfId":"00",
          "itemCd": "'.$itemCd.'",
          "rsdQty":"'.$qty.'",
          "regrId":"01",
          "regrNm":"Admin",
          "modrNm":"Admin",
          "modrId":"01"
        }';
    


      if($_POST['imptItemSttsCd'] == 3){
        $type_id = getItemId($itemCd);
        $sql_upd = $db->prepare("UPDATE stock SET `quantities` = $qty WHERE `type`='".$type_id."' AND branch = 'HQ'");
        $sql_upd->execute();
      
        sendStockIO($jsonIO);
        sendStockMaster($jsonMaster);
      }



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
                    <h1 class="page-header">Import List</h1>
                    <div class="pull-right data-table-list">
                    
                
                
                
<div class="container mt-5 ">
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
                

   <div class="panel-body table-responsive">
                
                <table class="table table-striped table-bordered table-hover" style="max-width: 100%;" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Declaration Number </th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Quantity Unit Code</th>
                            <th>Supplier's name</th>
                            <th>Agent name</th>
                            <th>Invoice Foreign Currency Amount</th>
                            <th>Invoice Foreign Currency</th>
                            <th>Action</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                      <?php 

                      
                      $i = 0;

                      $sql = $db->prepare("SELECT * FROM tbl_vsdc_importations ORDER BY id DESC");
                      $sql->execute();
                      while($data = $sql->fetch()){
                        $i++;
                      ?>
                      <tr>
                        <td><?php echo $i; ?></td>
                        
                        <td><?php echo $data['dclNo']; ?></td>
                        <td><?php echo $data['itemNm']; ?></td>
                        <td><?php echo $data['qty']; ?></td>
                        <td><?php echo $data['qtyUnitCd']; ?></td>
                        <td><?php echo $data['spplrNm']; ?></td>
                        <td><?php echo $data['agntNm']; ?></td>
                        <td><?php echo $data['invcFcurAmt']; ?></td>
                        <td><?php echo $data['invcFcurCd']; ?></td>
                        
                        <td>
                        <?php if($data['imptItemsttsCd'] == 2){ ?>
                          <button class="btn btn-primary" 
                            data-taskCd="<?php echo $data['taskCd']; ?>" 
                            data-dclDe="<?php echo $data['dclDe']; ?>" 
                            data-itemSeq="<?php echo $data['itemSeq']; ?>"
                            data-hsCd="<?php echo $data['hsCd']; ?>"
                            data-toggle="modal" data-target="#modal1">Waiting</button>
                        <?php } else {?>
                          <?php
                            
                            if($data['imptItemsttsCd'] == 1){
                              echo '<button class="btn btn-success">Unsent</button>';
                            }
                            if($data['imptItemsttsCd'] == 3){
                              echo '<button class="btn btn-success">Approved</button>';
                            }
                            if($data['imptItemsttsCd'] == 4){
                              echo '<button class="btn btn-danger">Cancelled</button>';
                            }
                            
                            ?></button>

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
            <h4 class="modal-title" id="myModalLabel"></h4>
          </div>
           <div class="modal-body">
           <form method="post">
            <!-- Select box for item -->
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

            <!-- Select box for item status -->
            <div class="form-group">
              <label for="statusSelect">Select Status:</label>
              <select class="form-control" id="statusSelect" name="imptItemSttsCd">
              <?php $cuntries = getInfoTable(26);
                    foreach ($cuntries as $key => $value) {
                ?>
                <option value="<?php echo trim($value[1]) ?>"><?php echo $value[3] ?></option>
                <?php }
                ?>
              </select>
            </div>

            <input type="hidden" name="taskCd">
            <input type="hidden" name="itemSeq">
            <input type="hidden" name="dclDe">
            <input type="hidden" name="hsCd">


            <!-- Buttons for form actions -->
            <div class="text-right">
              <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
          </div>
          <div class="modal-footer">
            <a href="importlist" class="btn btn-default">Close</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


</div>
    </div>
                    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  // JavaScript to handle modal events
  $('#modal1').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    
    var taskCd = button.data('taskcd'); // Extract value from data-* attributes
    var itemSeq = button.data('itemseq');
    var dclDe = button.data('dclde');
    var hsCd = button.data('hscd');
    // console.log(taskCd);
    
    $('input[name="taskCd"]').val(taskCd);
    $('input[name="itemSeq"]').val(itemSeq);
    $('input[name="dclDe"]').val(dclDe);
    $('input[name="hsCd"]').val(hsCd);

  });
</script>