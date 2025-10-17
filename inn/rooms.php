  <?php
  if(ISSET($_POST['btn_save'])){
      $floor_code = $_POST['floor_code'];
      $rm_code = $cmp_sn."".$floor_code;
      
      $max = $db->prepare("SELECT room_id,room_no FROM tbl_rooms WHERE room_no  LIKE '".$rm_code."%' ORDER BY room_id DESC LIMIT 1");
        $max->execute(array()); // execute the query with parameter $code...
        $rowcount = $max->rowCount();
        if ($rowcount > 0)// if there is data for that reg. number...
        {
        $row1 = $max->fetch(PDO::FETCH_ASSOC);
        // Verify the rooms...
        $room_no = $row1['room_no'];
        }
        
         $new = substr("$room_no", 3);                            
            $new=$new +1; 
                        
            if($new <10)
              {
                $c="0"; 
              }
            
              elseif($new >=10 AND $new < 100)
              {
                $c="";
              }
             $room_num =$rm_code.$c.$new;
             
         
    $sql_check = $db->prepare("SELECT * FROM tbl_rooms WHERE room_no = ?");
    try {
        
        $price = $_POST['price'];
        $descr = $_POST['descr'];
        $room_area = $_POST['room_area'];
        
        $sql_check->execute(array($room_num));
        $row_count_check = $sql_check->rowCount();
                if ($row_count_check >= 1)
                {
                $msge="This Room is Already Registered!";
                   
                }else{
              $room_type = $_POST['room_type'];
              $price = $_POST['price'];
              $price_fbu = $_POST['price_fbu'];
              $phone = $_POST['phone'];
              $adults = $_POST['adults'];
              $children = $_POST['children'];
              
            //   $profile=$_FILES['profile'];
            //   $file_name = $_FILES['profile']['name'];
            //   $file_location = $_FILES['profile']['tmp_name'];
                 
            //  if(move_uploaded_file($file_location, "../resto_picture/".$file_name)){
      	try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_rooms` (`room_no`,`price`,`price_fbu`,`room_type_id`,`company_id`,`rm_status`,`Adults`,`Children`)
            VALUES ('$room_num','$price','$price_fbu','$room_type',1,1,'$adults','$children')";
            $db->exec($sql);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
            //  }
             }
            $msg= "Room with Number: <b>".$room_num."</b> has been registered Successfully!";
            echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=room">';
                }catch(PDOException $e){
                echo $e->getMessage();
            }
  }
  // for activate Subject
    if(isset($_GET['acid']))
    {

        try{
    			$disable = 2;
    			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_rooms` SET `rm_status` = '".$disable."' WHERE `room_id` = '".$_GET['acid']."'";
    			$acidq = $db->prepare($sql);
    			$acidq->execute();
    			$msge="Room Deactivated successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=room">';
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
    		
      }
    
     // for Deactivate Subject
        else if(isset($_GET['did']))
          {
     try{
    			$enable = 1;
    			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_rooms` SET `rm_status` = '".$enable."' WHERE `room_id` = '".$_GET['did']."'";
    			$didq = $db->prepare($sql);
    			$didq->execute();
    			$msg="Room Activated successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=room">';
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
    		
    	}
     if(isset($_POST['update']))
    {

        try{
            $room_type_upd = $_POST['room_type_upd'];
            $price_upd = $_POST['price_upd'];
            $price_fbu_upd = $_POST['price_fbu_upd'];
            $adults_upd = $_POST['adults_upd'];
            $children_upd = $_POST['children_upd'];
            $roomid = $_POST['r_id'];
            
            echo $room_type_upd;
            
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql_upd = "UPDATE `tbl_rooms` SET `price` = '$price_upd',`price_fbu` = '$price_fbu_upd',`Adults` = '$adults_upd', `Children` = '$children_upd' WHERE `room_id` = $roomid ";
    			$conn->exec($sql_upd);
    			$msg="Room Updated successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=room">';
              
        }catch(PDOException $e){
    			echo $e->getMessage();
    		}
    }
    		
    	if(ISSET($_GET['delete_room'])){
    	    try{
    	        
		$id = $_GET['delete_room'];
		$sql = $conn->prepare("DELETE from `tbl_rooms` WHERE `room_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=room">';
		
	    }catch(PDOException $e){
			echo $e->getMessage();
		}
	}
    	
  ?>

  <!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
		    
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
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="fa fa-cog"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Manage Rooms</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Room" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
            							<th>Room No.</th>
            							<th>Room Type</th>
            							<th>Adults</th>
            							<th>Children</th>
            							<th>Availability</th>
            							<th>Descriptions</th>
            							<th>Room Status</th>
            							<th>Price($)</th>
    							        <th>Price(Rwf)</th>
            							<th>Action</th>
                                    </tr>
                                </thead>
                                	<tbody>
                                        <?php
                
                                            $sql_rooms = $db->prepare("SELECT * FROM tbl_rooms 
                                            INNER JOIN tbl_room_type ON tbl_rooms.room_type_id=tbl_room_type.type_ID");
                                            $sql_rooms->execute();
                                            $i = 0;
                                            while($fetrooms = $sql_rooms->fetch()) {
                                                $rm_id = $fetrooms['room_id'];
                                                $stat = $fetrooms['rm_status'];
                                                $avail = $fetrooms['availability'];
                                                $tot_price =$tot_price+$fetrooms['price'];
                                                
                                                $sql_stat = $db->prepare("SELECT * FROM tbl_status where id='$stat'");
                                                $sql_stat->execute();
                                                $fetstat = $sql_stat->fetch();
                                                
                                                $sql_avail = $db->prepare("SELECT * FROM tbl_status where id='$avail'");
                                                $sql_avail->execute();
                                                $fetavail = $sql_avail->fetch();
                                                $i += 1;
                                        ?>
                                     <tr class="gradeU">
            							<td><?php echo $i; ?></td>
                        				<td class="center">
                            				<span><?php echo $fetrooms['room_no']; ?></span><br/>
                        				</td>
                        				<td><?php echo $fetrooms['type_name']; ?></td>
                        				<td><?php echo $fetrooms['Adults']; ?></td>
                        				<td><?php echo $fetrooms['Children']; ?></td>
            							<td><?php if($fetavail['id'] == 4) {
            							    $hidd = 1;
            							    ?>
            							    <label class="text-default fa fa-circle"></label> <?php echo $fetavail['status_name']; ?> 
            							<?php
            							}
            							else {
            							 $hidd = 0;
            							?>
            							    <label class="text-success fa fa-circle"></label> <?php echo $fetavail['status_name']; ?>  
            							<?php
            							    }
            							?></td>
            							<td>
            							    <?php
                        				
                        				echo $fetrooms['desc_type'];
                        				?>
            							</td>
            							<td>
                						<?php if($fetstat['id'] == 1)
                                          { ?>
                                            <a href="?resto=room&acid=<?php echo $rm_id;?>" onclick="if(!confirm('Do you really want to Activate This Room?'))return false;else return true;"><label class="label label-success"><?php echo $fetstat['status_name']; ?></label></a><?php } else {?>
                
                                            <a href="?resto=room&did=<?php echo $rm_id;?>" onclick="if(!confirm('Do you really want to Deactivate This Room?'))return false;else return true;"><label class="label label-default"><?php echo $fetstat['status_name']; ?></label></a>
                                         <?php }?>
            							</td>
            							<td>$<?php echo number_format($fetrooms['price']).".00"; ?></td>
            							<td>Rwf<?php echo number_format($fetrooms['price_fbu']).".00"; ?></td>
                                        <td class="cell-icon text-center">
                                            <a href="" data-toggle="modal" data-target="#update<?php echo $rm_id;?>" class="edit_data"><i class="icon-edit"></i> Edit</a>
                                            |
                                            <?php
                                            if($hidd==0){
                                            ?>
                                            <a href="?resto=room&&delete_room=<?php echo $rm_id;?>&&room_name=<?php echo $fetrooms['room_no'];?>" onclick="if(!confirm('Do you really want to Delete This Room?'))return false;else return true;" title="Delete room"><span style="color: #870813;"><i class="icon-trash"></i> Delete</span></a>
                                            <?php 
                                            }elseif($hidd==1){
                                                echo "OnRent!";
                                            }else{
                                                echo "No action";
                                            }
                                            ?>
                                        
                                        </td>
            						</tr>
						            <div class="modal fade" id="update<?php echo $rm_id; ?>" role="dialog">
                                        <div class="modal-dialog modals-default">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="" enctype="multipart/form-data" method="POST">
                                                    <input type="hidden" name="r_id" value="<?php echo $rm_id; ?>">
                                                <div class="modal-body">
                                                    <h2>Update Room</h2>
                                                    
                                                    <div class="row">
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                        
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <small>Room Price In Dollar</small>
                                                                        <input type="text" name="price_upd" class="form-control" value="<?php echo $fetrooms['price'] ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                        
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <small>Room Price In Rwf</small>
                                                                        <input type="text" name="price_fbu_upd" class="form-control" value="<?php echo $fetrooms['price_fbu'] ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <small>Number Of Adults</small>
                                                                        <input type="text" name="adults_upd" class="form-control" value="<?php echo $fetrooms['Adults'] ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <small>Number Of Children</small>
                                                                        <input type="text" name="children_upd" class="form-control" value="<?php echo $fetrooms['Children'] ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <hr>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-default" name="update">Save changes</button>
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                            
                                                
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                     	<?php } ?>
					</tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
    							<th>Room No.</th>
    							<th>Room Type</th>
    							<th>Availability</th>
    							<th>Descriptions</th>
    							<th>Room Status</th>
    							<th>Price($)</th>
    							<th>Price(Rwf)</th>
    							<th>Action</th>
                            </tr>
                        </tfoot>
                      </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Data Table area End-->

<div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" enctype="multipart/form-data" method="POST">
                <div class="modal-body">
                    <h2>Add Room</h2>
                    
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group ic-cmp-int float-lb floating-lb">
                                <div class="form-ic-cmp">
                                    
                                </div>
                                <div class="nk-int-st">
                                    <select id="floor_code" name="floor_code" class="selectpicker" data-live-search="true" style="width:100%">
                                         <option>Select Floor</option>
                                             <?php
                                                 $stmt = $db->query('SELECT Block_code, block_name FROM tbl_block ORDER BY blck_id ASC');
                    
                                                        try
                                                        {
                                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                ?>
                                                                    <option value="<?php echo $row['Block_code']; ?>"><?php echo $row['block_name']; ?></option>
                                                                <?php
                                                                 }
                                                            }
                                                            catch (PDOException $ex) {
                                                            //Something went wrong rollback!
                                                            echo $ex->getMessage();
                                                    }
                                              ?>	
                                        </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group ic-cmp-int float-lb floating-lb">
                                <div class="form-ic-cmp">
                                    
                                </div>
                                <div class="nk-int-st">
                                    <input type="number" name="adults" class="form-control" placeholder="Number Of Adults" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group ic-cmp-int float-lb floating-lb">
                                <div class="form-ic-cmp">
                                    
                                </div>
                                <div class="nk-int-st">
                                    <input type="number" name="children" class="form-control" placeholder="Number Of Children" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group ic-cmp-int float-lb floating-lb">
                                <div class="form-ic-cmp">
                                    
                                </div>
                                <div class="nk-int-st">
                                    <select id="floor_code" name="room_type" class="selectpicker" data-live-search="true" style="width:100%" required>
                                         <option>Select Room Type</option>
                                             <?php
                                                 $sql = $db->query('SELECT * FROM tbl_room_type ORDER BY type_ID ASC');
                    
                                                        try
                                                        {
                                                            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                                                                ?>
                                                                    <option value="<?php echo $row['type_ID']; ?>"><?php echo $row['type_name']; ?></option>
                                                                <?php
                                                                 }
                                                            }
                                                            catch (PDOException $ex) {
                                                            //Something went wrong rollback!
                                                            echo $ex->getMessage();
                                                    }
                                              ?>	
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group ic-cmp-int float-lb floating-lb">
                                <div class="form-ic-cmp">
                                    
                                </div>
                                <div class="nk-int-st">
                                    <input type="text" name="price" class="form-control" placeholder="$0.00 Price per day" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group ic-cmp-int float-lb floating-lb">
                                <div class="form-ic-cmp">
                                    
                                </div>
                                <div class="nk-int-st">
                                    <input type="text" name="price_fbu" class="form-control" placeholder="Rwf0.00 Price per day" required>
                                </div>
                            </div>
                        </div>
                    </div>
                   </div>
                  <hr>
                <div class="modal-footer">
                    <button type="submit" name="btn_save" class="btn btn-default">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>