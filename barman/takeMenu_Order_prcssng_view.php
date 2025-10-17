
<?php
 $id = $_GET['m'];
 $get_rsv = $db->prepare("SELECT tbl_rooms.room_no AS RoomName FROM tbl_reservation 
    INNER JOIN tbl_rooms ON tbl_rooms.room_id = tbl_reservation.roomID 
    WHERE tbl_reservation.reservation_id = '".$id."'");
   $get_rsv->execute();
   $getFetch = $get_rsv->fetch();

  // for activate Subject
    if(isset($_REQUEST['proccess']))
    {
    try{
        $menu_id = $_REQUEST['menu_id'];
        $reservation_ID = $_REQUEST['reservation_ID'];
        $orderCode = $_POST['orderCode'];
        $a = 5;
		
            for($i=0; $i<count($menu_id); $i++){
    			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_cmd` SET `status_id` = '".$a."' WHERE `menu_id` = '".$menu_id[$i]."' AND
    			`OrderCode` = '".$orderCode."'";
    			$acidq = $db->prepare($sql);
    			$acidq->execute();
		  }
		  $msg = "Successfully Completed!";
            echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=prcsOrder_prcssng">';
		  
		}catch(PDOException $e){
			echo $e->getMessage();
		}
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
                    <?php if($msg){?>
                    <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
                  </div>
                <?php } 
                  else if($msge){?>
                     
                 <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Oooh Snap!</strong> <?php echo htmlentities($msge); ?>
                  </div>
                 <?php
                  }else if($snp){
                ?>
                 <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Sorry!</strong> Access Denied!<?php echo htmlentities($snp); ?>
                  </div>
                <?php } ?>
                    <div class="normal-table-list mg-t-30">
                        <form action="" method="POST">
                        <div class="bsc-tbl-st" id="print">
                            <div class="basic-tb-hd">
                            <h2>Menu-Order <span class="rmNo">Table No. <?php echo $getFetch['RoomName']??"";?></span></h2>
                        </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu Order</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Date-Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 <?php
                                 $cpny_ID=$cpny_ID??0;
                                    $i = 0;
                            		$sql = $db->prepare("SELECT * FROM `tbl_cmd`
                                    INNER JOIN tbl_reservation ON tbl_cmd.reservat_id = tbl_reservation.reservation_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd.menu_id
                                    WHERE tbl_cmd.reservat_id = '".$id."' AND  tbl_cmd.status_id = 7 AND tbl_reservation.resv_CpnyID= '".$cpny_ID."' ");
                            		$sql->execute(array());
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                            		   $rsv_ID = $fetch['reservat_id'];
                            		   $cat_id= $fetch['cat_id'];
                            		   $subcat_ID = $fetch['subcat_ID'];
                            		   $menu_id = $fetch['menu_id'];
                            		   $OrderCode = $fetch['OrderCode'];
                            		   
                            		 $res_categ = $db->prepare("SELECT *FROM category WHERE cat_id ='".$cat_id."'");
                            		 $res_categ->execute();
                            		 $fCateg = $res_categ->fetch();
                            		 $Cname =  $fCateg['cat_name'];
                            		 
                            		 $res_sub_categ = $db->prepare("SELECT *FROM subcategory WHERE subcat_id ='".$subcat_ID."'");
                            		 $res_sub_categ->execute();
                            		 $fsub_Categ = $res_sub_categ->fetch();
                            		 $Subname =  $fsub_Categ['subcat_name'];
                                 	?>
                                    <tr>
                                        <td>
                                         <input type="checkbox" name="menu_id[]" id="<?php echo $menu_id;?>" value="<?php echo $menu_id;?>"/>
                                         <label for="<?php echo $menu_id;?>"></label>    
                                        </td>
                                        <td><?php echo $fetch['menu_name']; ?></td>
                                        <td><?php echo $Cname;?></td>
                                        <td><?php echo $Subname;?></td>
                                        <td><?php echo $fetch['created'];?></td>
                                    </tr>
                                    <?php 
                            		    }
                            		}
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="orderCode" value="<?php echo $OrderCode;?>">
                        <input type="hidden" name="reservation_ID" value="<?php echo $id;?>">
                        <br/>
                        <button type="submit" name="proccess" id="proccess" class="btn btn-info btn-sm" style="border-radius: 5px;" onclick="if(!confirm('Do you really want to Complete?'))return false;else return true;" ><i class="fa fa-step-forward"></i> Complete</button>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>