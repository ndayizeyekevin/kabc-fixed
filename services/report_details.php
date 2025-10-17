<?php
if(isset($_POST['update'])){
  $orderqty = $_POST['orderqty'];
  $orderqtyid = $_POST['orderqtyid'];
  $orderCode = $_POST['orderCode'];

  for($i=0; $i<count($orderqtyid); $i++){
    $sqlupd = $db->prepare("UPDATE tbl_cmd_qty SET cmd_qty = '".$orderqty[$i]."' WHERE cmd_qty_id = '".$orderqtyid[$i]."'");
    $sqlupd->execute();
  }
  
  $sqlupd = $db->prepare("UPDATE tbl_cmd SET status_id = '13' WHERE `OrderCode` = '".$orderCode."'");
  $sqlupd->execute();
  $msg = "Order quantity updated!";
  echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
}

  if(isset($_POST['confirm']))
    {
    try{
        $menu_id = $_POST['menu_id'];
        $reservation_ID = $_POST['reservation_ID'];
        $orderCode = $_POST['orderCode'];
        $table = $_GET['m'];
        
        $a = 8;
         for($i=0; $i<count($menu_id); $i++){
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE `tbl_cmd` SET `status_id` = '".$a."' WHERE `menu_id` = '".$menu_id[$i]."' AND
			`OrderCode` = '".$orderCode."'";
			$acidq = $db->prepare($sql);
			$acidq->execute();
			
			$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_status` = '".$a."' WHERE `cmd_item` = '".$menu_id[$i]."' AND
			`cmd_code` = '".$orderCode."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			$msg = "Successfully Delivered!";
            echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
		  }

	}catch(PDOException $e){
	 echo $e->getMessage();
	 }
   }

 $id = $_GET['m'];
 $code = $_GET['s'];

 $stmts = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$code."'");
 $stmts->execute();
 $getsts = $stmts->fetch();
 $status = $getsts['status_id'];

 $get_rsv = $db->prepare("SELECT * FROM tbl_tables
    WHERE table_id = '".$id."'");
   $get_rsv->execute();
   $getFetch = $get_rsv->fetch();
   
   $GetSts = $db->prepare("SELECT *FROM tbl_status WHERE id = '".$status."'");
   $GetSts->execute();
   $fsts = $GetSts->fetch();
   
   $sttus = $fsts['status_name'];
   
   if($status == 13){
    $snp = "waiting Menu order to be completed";   
   }elseif($status == 5){
    $msg = "Order completed";
   }elseif($status == 7){
    $msg = "Order is in process";
   }elseif($status == 8){
    $msg = "Order delivered";
   }
   
   if(isset($_GET['id'])){
       $item = $_GET['id'];
       $m=$_GET['m'];
       $s=$_GET['s'];
       
      $sql = $db->prepare("DELETE FROM tbl_cmd WHERE id = '".$item."'");
      $sql->execute();
       
      $msge = "Item Removed Successfully!";
      echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=prcsOrder_prcssng&m='.$m.'&s='.$s.'">';
   }
 ?>
  <style>
      .rmNo {
      text-align: center;
      text-transform: uppercase;
      color: #4CAF50;
    }
  </style>
 <div class="normal-table-area">
        <div class="container">
            <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                
                    <div class="normal-table-list mg-t-30">
                        <form action="" method="POST">
                        <div class="bsc-tbl-st" id="print">
                            <div class="basic-tb-hd">
                            <h2><small> <i class="fa fa-refresh"></i> <?php echo $sttus; ;?><span class="rmNo"> Table No. <?php echo $getFetch['table_no'];?></span></small> </h2>
                        </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu Order</th>
                                        <th>Quantity</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Date-Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 <?php
                                    $i = 0;
                            		$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    WHERE tbl_cmd_qty.cmd_code='".$code."'");
                            		$sql->execute(array());
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                                   $OrderCode = $fetch['cmd_code'];
                            		   
                            		   
                                   
                                   $GetStsqty = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$OrderCode."'");
                                   $GetStsqty->execute();
                                   $fstsqty = $GetStsqty->fetch();

                                   $GetStsmenu = $db->prepare("SELECT * FROM menu WHERE menu_id = '".$fetch['cmd_item']."'");
                                   $GetStsmenu->execute();
                                   $fstsmenu = $GetStsmenu->fetch();
                                   
                                   $rsv_ID = $fstsqty['reservat_id'];
                            		   $cat_id= $fstsmenu['cat_id'];
                            		   $subcat_ID = $fstsmenu['subcat_ID'];
                            		   $menu_id = $fetch['cmd_item'];
                            		   $OrderCode = $fstsqty['OrderCode'];
                            		   $status = $fstsqty['status_id'];
                            		   
                            		   $GetSts = $db->prepare("SELECT *FROM tbl_status WHERE id = '".$status."'");
                                    $GetSts->execute();
                                    $fsts = $GetSts->fetch();


                            		 $res_categ = $db->prepare("SELECT * FROM category WHERE cat_id ='".$cat_id."'");
                            		 $res_categ->execute();
                            		 $fCateg = $res_categ->fetch();
                            		 $Cname =  $fCateg['cat_name'];
                            		 
                            		 $res_sub_categ = $db->prepare("SELECT *FROM subcategory WHERE subcat_id ='".$subcat_ID."'");
                            		 $res_sub_categ->execute();
                            		 $fsub_Categ = $res_sub_categ->fetch();
                            		 $Subname =  $fsub_Categ['subcat_name'];
                                 	?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><?php echo $fstsmenu['menu_name']; ?></td>
                                        <td>
                                        <?php
                                          echo $fetch['cmd_qty'];
                                        ?>  
                                      </td>
                                        <td><?php echo $Cname;?></td>
                                        <td><?php echo $Subname;?></td>
                                        <td><?php echo $fstsqty['dTrm'];?></td>
                                        <td>
                                        <?php
                                        if($status == 3){
                                            $bullet ='<i class="fa fa-circle" aria-hidden="true" style="color: #dd5252;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                            
                                        } 
                                        elseif($status == 5){
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                        }
                                        elseif($status == 8){
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                        }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php 
                            		    }
                            		}
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>