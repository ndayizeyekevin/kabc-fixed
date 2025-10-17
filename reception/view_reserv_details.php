<?php
$id = $_REQUEST['g'];
$sql = $db->prepare("SELECT * FROM guest WHERE guest_id = '".$id."'");
$sql->execute();
$fetch = $sql->fetch();

$sql2 = $db->prepare("SELECT * FROM tbl_reservation INNER JOIN tbl_rooms ON tbl_reservation.roomID = tbl_rooms.room_id WHERE guest_id = '".$id."'");
$sql2->execute();
$fetch2 = $sql2->fetch();
$room_id = $fetch2['roomID'];

?>
<?php
if(ISSET($_POST['save']))
          {
     try{
         
         $arrival = $_POST['arrival'];
         $departure = $_POST['departure'];
         
    			$a = 11;
    			$sql = $db->prepare("UPDATE `tbl_reservation` SET `status` = '".$a."', `arrival` = '".$arrival."', `departure` = '".$departure."' WHERE `guest_id` = '".$id."'");
    			$sql->execute();
    			
    			$b = 2;
    			$sql_upd = $db->prepare("UPDATE `tbl_rooms` SET `rm_status` = '".$b."' WHERE `room_id` = '".$room_id."'");
    			$sql_upd->execute();
    			
    			$msg = "Reservation Checkedin Successfully!";
    			echo '<meta http-equiv="refresh"'.'content="1;URL=?ougami=mngeResrv">';
    			
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
    		
    	}
?>
<!-- Form Element area Start-->
    <div class="form-element-area">
        <div class="container">
            <div class="row">
                
                <?php if($msg){?>
                  <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
                  </div>
                <?php } 
                 else if($msge){?>
                     
                 <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
                  </div>
                <?php } ?>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-user"></i><?php echo $fetch['firstname']." ".$fetch['lastname']; ?></strong></small></h2>
                        </div>
                     <hr>
                    <br>
                        <form action="" method="POST">
                          <div class="row">
                            <label class="col-md-2 control-label" for=""><strong> <small>Guest</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                               <input type="text" name="chkin" class="form-control input-sm" value="<?php echo $fetch['firstname']." ".$fetch['lastname']; ?>" readonly>
                              </div>
                            <label class="col-md-2 control-label" for=""><strong> <small>Passport</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                             <input type="text" name="chkin" class="form-control input-sm" value="<?php echo $fetch['nid_passport']; ?>" readonly>   
                            </div>
                        </div>
                    <br>
                    <div class="row">
                            <label class="col-md-2 control-label" for=""><strong> <small>Phone</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                               <input type="text" name="chkin" class="form-control input-sm" value="<?php echo $fetch['phone']; ?>" readonly>
                              </div>
                            <label class="col-md-2 control-label" for=""><strong> <small>Room No</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                             <input type="text" name="chkin" class="form-control input-sm" value="<?php echo $fetch2['room_no']; ?>" readonly>   
                            </div>
                        </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 control-label" for=""><strong><small>Arrival</small></strong><span class="text-danger">*</span></label>
                         <div class="col-md-3">
                           <input type="date" name="arrival" class="form-control input-sm" value="<?php echo $fetch2['arrival']; ?>" required>
                          </div>
                        <label class="col-md-2 control-label" for=""><strong><small>Check Out Date</small></strong></label>
                         <div class="col-md-3">
                          <input type="date" name="departure" class="form-control input-sm" value="<?php echo $fetch2['departure']; ?>" required>
                         </div>
                        </div>
                     <br>
                    <div class="row">
					    <div class="form-actions col-md-12">
					        <br />
					        <center>								
						        <button type="submit" id="" name="save" class="btn btn-sm label-info margin" onclick="if(!confirm('Do you really want to Checkin This Reservation?'))return false;else return true;" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Confirm</button>
						        <button type="reset" class="btn btn-sm label-secondary margin"><i class="fa fa-backward"></i> Back</button>								
					        </center>
					    </div>
                    </div>
                  <br><br>
                 </form>
                </div>
            </div>
        </div>
       
    </div>
</div>
<!-- Form Element area End-->