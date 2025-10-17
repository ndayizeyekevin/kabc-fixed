<?php


if(isset($_POST['save'])){
  $item = $_POST['item'];
  $warehouse = $_POST['warehouse'];
  $lpo = $_POST['lpo'];
  $qty = $_POST['qty'];
  $todayy = $_POST['duedate'];
  $qty1 = getStockValue($item)+$qty;
  $type_id = getItemId($item);


    $sql_chk1 = $db->prepare("SELECT * FROM stock INNER JOIN menu ON menu.menu_id=stock.type WHERE menu.itemCd = '$item' LIMIT 1");
    $sql_chk1->execute();

    if($sql_chk1->rowCount() > 0){
        $sql_upd_qty2 = $db->prepare("UPDATE stock SET `quantities`= quantities + '$qty' WHERE `type`='".$type_id."'");
        $sql_upd_qty2->execute();
    }else{
        $db->prepare("INSERT INTO `stock` SET `type`='".$type_id."', `quantities`='$qty' ")->execute();
    }



    $jsonMaster = array();
        
    $qty = $qty1;
    $jsonMaster[] = '{"tin":"'.$branch_tin.'",
      "bhfId":"00",
      "itemCd": "'.$item.'",
      "rsdQty":"'.$qty.'",
      "regrId":"01",
      "regrNm":"Admin",
      "modrNm":"Admin",
      "modrId":"01"
    }';

    sendStockMaster($jsonMaster);

	

	
    echo "<script>
        $(document).ready(function(){
        $('#modal').modal('show');
        });
        </script>";
}
?>
<body>







<div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                         <div class="basic-tb-hd">
                          <h2><small><strong><i class="fa fa-users"></i> Record stock in</strong></small></h2> 
                         </div>
                         
                       
                        <div class="container-fluid">
            <div class="row">
            <div class="col-lg-12">
                    
                </div>
                <div class="panel-body">
                   <form method="POST" id="manage-items">
                      <div class="col-lg-12">
                      <div id="msg"></div>
                      <div class="col-lg-4">
                                <label>Date</label>
                                <input type="date" name="duedate" class="form-control" required>
                            </div>
                            <div class="col-lg-4">
                                <label>Item</label>
                                <select name="item" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">--Select Item</option>
                                    <?php
                                    $sql = $db->prepare("SELECT * FROM menu where product_type !=3 ORDER BY menu_name");
                                    $sql->execute();
                                    while($rowss = $sql->fetch()){ ?>
                                        <option value="<?php echo $rowss['itemCd']; ?>"><?php echo $rowss['menu_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Quantity</label>
                                <input type="number" step="any" name="qty" class="form-control" required>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>LPO</label>
                                <input type="text" name="lpo" class="form-control" required>
                            </div>
                            </div>
                            <div class="col-lg-12">
                                <center>
                                <br><br>
                                <button type="submit" class="btn btn-success" name="save">Save</button>  
                                <a href="allstock" class="btn btn-default"><i class="fa fa-backward"></i> Back</a>
                            </center>
                                </div>
                        </form>

            <div class="clearfix"></div>
        </div>
        
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>






















<body>


    <!-- Page Content -->
    <div id="page-wrapper">
    <br>
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
            <p>Stock Updated Successfully!</p>
          </div>
          <div class="modal-footer">
            <a href="allstock" class="btn btn-default">Ok</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
       

<?php include('scripts.php'); ?>