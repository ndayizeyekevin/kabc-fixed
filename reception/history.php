<?php
     // for cancel Subject

     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);

     if(!isset($_REQUEST['user'])){
        $servant = $_GET['user'];
        $date = $_REQUEST['todate'];
        $query1 ="SELECT * FROM `tbl_cmd_qty`
                                    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.serv_id = '43' and (tbl_cmd_qty.created BETWEEN '2025-03-19 00:00:00' AND '2025-03-19 23:59:59')
                                    GROUP BY cmd_code
                                    ORDER BY cmd_table_id ASC
                                    ";
     }else {
        $query1 = "SELECT * FROM `tbl_cmd_qty`
                                    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.serv_id = '43' and (tbl_cmd_qty.created BETWEEN '$date 00:00:00' AND '$date 23:59:59')
                                    GROUP BY cmd_code
                                    ORDER BY cmd_table_id ASC
                                    ";

                                    
     }
    
    

        if(isset($_GET['b']))
          {
     try{
    			$b = 4;
    			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_reservation` SET `status` = '".$b."' WHERE `reservation_id` = '".$_GET['b']."'";
    			$didq = $db->prepare($sql);
    			$didq->execute();
    			$msge="Reservation Cancelled successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=mngeResrv">';
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
    		
    	} 
    	 else if(isset($_GET['d']))
          {
     try{
        $reservation_ID = $_GET['d'];
        $todaysdate = date("Y-m-d H:i:s");
        
        $stmt_room = $db->prepare("SELECT * FROM `tbl_reservation` WHERE reservation_id = '".$reservation_ID."' ");
        $stmt_room->execute();
        $row_room = $stmt_room->fetch();
        $rrr = $row_room['roomID'];
        $ggg = $row_room['guest_id'];
        
        $stmt_r = $db->prepare("SELECT * FROM `tbl_rooms` WHERE room_id = '".$rrr."' ");
        $stmt_r->execute();
        $row_r = $stmt_r->fetch();
        $price = $row_r['price'];
        $price_fbu = $row_r['price_fbu'];
        
        $stmt_guest = $db->prepare("SELECT * FROM `guest` WHERE guest_id = '".$ggg."' ");
        $stmt_guest->execute();
        $row_guest = $stmt_guest->fetch();
        $country = $row_guest['country'];
       
        if($country != 15){
           $f_price = $price; 
        }
        else{
           $f_price = $price_fbu;
        }
        
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
    		
    	}
    	
?>

<!-- Data Table area Start-->
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

                        <form method="GET">
         
         <div class="row">
             <div class="col-md-5">
     Servant<br>
     <input type="hidden" name="resto" value="history">
   	 <Select name ="user" class ="form-control">
        <?php
        
        $sql_roomss = $db->prepare("SELECT * FROM tbl_users where role_id =6");
                                        $sql_roomss->execute();
                                        while($fetroomss = $sql_roomss->fetch()) {
                                            echo "<option value='".$fetroomss['user_id']."'>".$fetroomss['f_name']."</option>";
                                        }
        
        ?> 
        
    </select>
             </div>
             
              <div class="col-md-5">
     Date:<br>
   	  <input type="date" name ="todate" class ="form-control">
             </div> 
             
                    <div class="col-md-2">
<br>
   	   <button  name ="check" class ="btn btn-info" value="Check">Check</button>
             </div> 
             
             </div>
        
   	 </form>

                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Table No</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $totprice = 0;
                                    $sql_rooms = $db->prepare($query1);
                                        $sql_rooms->execute();
                                        $i = 0;
                                        while($fetrooms = $sql_rooms->fetch()) {
                                            $i++;
                                            $totprice = $totprice+$fetrooms['menu_price'];

                                            $stmts = $db->prepare("SELECT * FROM tbl_status WHERE id = '".$fetrooms['cmd_status']."'");
                                            $stmts->execute();
                                            $fsts = $stmts->fetch();
                                    ?>
                                 <tr class="gradeU">
        							<td><?php echo $i; ?></td>
                    				<td><?php echo $fetrooms['table_no']; ?> |  <?php  $d = $fetrooms['Serv_id']; ?>  
                    				
                    				
                    				
                    				
                    				
                    			<?php	$sql_roomss = $db->prepare("SELECT * FROM tbl_users WHERE user_id ='$d'");
                                        $sql_roomss->execute();
                                        $i = 0;
                                        while($fetroomss = $sql_roomss->fetch()) {
                                            echo $fetroomss['f_name'];
                                        }
                    				?>
                    				
                    				
                    				
                    				</td>
        							<td>
        							    
        							     <?php echo $fetrooms['created']; ?>
        							</td>
                                    <td>
                                    <?php 
                                    $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
                                    $text = $fsts['status_name'];
                                    echo $bullet." ".$text;
                                    ?>
                                    </td>
        							<td>
                                        <?php if($fetrooms['cmd_status']){ ?>
        							    <a href="?resto=gstDet&res=<?php echo $fetrooms['cmd_table_id'];?>&c=<?php echo $fetrooms['cmd_code'];?>" onclick="if(!confirm('Do you really want to View Details?'))return false;else return true;" class="label label-primary">Details</a>

                                     <?php } ?>  
                                     
                                     
                                     <a hidden href="?resto=transfer&res=<?php echo $fetrooms['Serv_id'];?>&c=<?php echo $fetrooms['cmd_code'];?>"  class="label label-primary">Transfer</a>
        							</td>
        						</tr>
					            <?php   
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
    <!-- Data Table area End-->
    
    <div id="update<?php echo $fetrooms['reservation_id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header text-center">
            <h2 class="modal-title"><i class="fa fa-pencil"></i> <?php echo $cpny_ID;?> Profile</h2>
          </div>
          <!-- END Modal Header -->
    
          <!-- Modal Body -->
          <div class="modal-body">
             
                <form id="settings_form" method="POST" enctype="multipart/form-data" class="form-horizontal form-bordered">
                 
                  <fieldset style="margin-top: 10px;">
                     
                     <div class="form-group">
                      <label class="col-md-4 control-label" for="">Firstname</label>
                      <div class="col-md-8">
                        <input type="text" id="cpname" name="cpname" class="form-control" value="<?php echo $cmp_full;?>" placeholder="Enter Company Name..">
                      </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Company Email</label>
                        <div class="col-md-8">
                            <input type="Email" id="cpemail" name="cpemail" class="form-control" value="<?php echo $cpny_email;?>" placeholder="Enter Email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Phone</label>
                        <div class="col-md-8">
                            <input type="text" id="cphone" name="cphone" class="form-control" value="<?php echo $cpny_phone;?>" placeholder="Company Phone">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Address</label>
                        <div class="col-md-8">
                            <input type="text" id="cpny_address" name="cpny_address" class="form-control" value="<?php echo $cpny_address;?>" placeholder="Company Phone">
                        </div>
                    </div>
                    
                  </fieldset>
                    <div class="form-group form-actions" style="margin-top: 10px;">
                        <div class="col-xs-12 text-right">
                            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" name="btn-profile" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>
          <!-- END Modal Body -->
        </div>
      </div>
    </div>