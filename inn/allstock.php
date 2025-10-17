<?php

if(isset($_POST['send_stock'])){


$jsonMaster = array();
$sql = $db->prepare("SELECT * FROM `stock` INNER JOIN menu ON menu.menu_id=stock.type WHERE `branch`= 'HQ'");
$sql->execute();
while($data = $sql->fetch()){
  $jsonMaster[] = '{"tin":"999900823",
    "bhfId":"00",
    "itemCd": "'.$data['itemCd'].'",
    "rsdQty":"'.$data['quantities'].'",
    "regrId":"01",
    "regrNm":"Admin",
    "modrNm":"Admin",
    "modrId":"01"
  }';

}

print_r(sendStockMaster($jsonMaster));

}
?>

<body>







<div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                         <div class="basic-tb-hd">
                          <h2><small><strong><i class="fa fa-users"></i> Manage Our Guest</strong></small></h2> 
                         </div>
                         
                         <hr>
                        <br>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Code </th>
                                    <th> Item Name </th>
                                    <th> Tax Code </th>
                                    <th> U. Price </th>
                                    <th>Qty in Stock</th>
                                    <th>Qty Unit Code</th>
                                </tr>
                                </thead>
                                <tbody>
                      <?php 
                      $i = 0;
                      $sql = $db->prepare("SELECT * FROM `stock` INNER JOIN menu ON menu.menu_id=stock.type WHERE `branch`= 'HQ'");
                      $sql->execute();
                      $rowCount = $sql->rowCount();
                      if($rowCount == 0){
                        $sql1 = $db->prepare("SELECT * FROM menu");
                        $sql1->execute();

                        while($data = $sql1->fetch()){
                            $db->prepare("INSERT INTO `stock` SET `branch`='HQ', `type`='".$data['menu_id']."', `quantities`=0 ")->execute();;
                        }

                      }
                      while($data = $sql->fetch()){
                        $i++;
                        

                      ?>
                      <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data['itemCd']; ?></td>
                        <td><?php echo $data['itemNm']; ?></td>
                        <td><?php echo $data['taxTyCd']; ?></td>
                        <td><?php echo $data['dftPrc']; ?></td>
                        <td><?php echo $data['quantities']; ?></td>
                        <td><?php echo $data['qtyUnitCd']; ?></td>
                      </tr>
                      <?php 
                      }?>
                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>








