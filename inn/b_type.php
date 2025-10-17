  <?php
  if(ISSET($_POST['btn_save'])){
    $sql_check = $db->prepare("SELECT * FROM tbl_bed_type WHERE bed_name = ?");
    try {
        
        $t_name = $_POST['t_name'];
        $t_descr = $_POST['t_descr'];
        
        $sql_check->execute(array($t_name));
        $row_count_check = $sql_check->rowCount();
                if ($row_count_check >= 1)
                {
                $msge="This Bed Type Already Registered!";
                   
                }else{
             
      	try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_bed_type` (`bed_name`,`bed_descr`,`company_id`,`bed_status`)
            VALUES ('$t_name','$t_descr','$cpny_ID',1)";
            $db->exec($sql);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
             }
            $msg= "Bed Type registered Successfully!";
            echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=bed_type">';
                }catch(PDOException $e){
                echo $e->getMessage();
            }
  }
  ?>
  
  <?php
  if(ISSET($_POST['btn-update'])){
		try{
			$t_id = $_POST['tid'];
			$tname = $_POST['tname'];
			$tdescr = $_POST['tdescr'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `tbl_bed_type` SET `bed_name` = '$tname', `bed_descr` = '$tdescr' WHERE bed_id = '$t_id'";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=bed_type">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_bed_type` WHERE `bed_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=bed_type">';
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
										<h2>Manage Beds</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Room Type" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
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
							<th>Bed Name</th>
							<th>Descriptions</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                        <?php

                            $sql_rooms = $db->prepare("SELECT * FROM tbl_bed_type
                            WHERE company_id = '".$cpny_ID."'");
                            $sql_rooms->execute();
                           
                            $i = 0;
                            while($fetrooms = $sql_rooms->fetch()) {
                                $i += 1;
                        ?>
                                     <tr class="gradeU">
							<td><?php echo $i; ?></td>
            				<td class="center">
                				<span><?php echo $fetrooms['bed_name']; ?></span><br/>
            				</td>
            				<td><?php echo $fetrooms['bed_descr']; ?></td>
                            <td class="cell-icon text-center">
                                <a href="" data-toggle="modal" data-target="#update<?php echo $fetrooms['bed_id'];?>" class="edit_data"><i class="icon-edit"></i> Edit</a>|
                                <a href="?resto=bed_type&&myId=<?php echo $fetrooms['bed_id']; ?>" onclick="if(!confirm('Do you really want to Delete This Bed Type?'))return false;else return true;" title="Delete bed"><span style="color: #870813;"><i class="icon-trash"></i> Delete</span></a>
                            </td>
						</tr>
					<div class="modal fade" id="update<?php echo $fetrooms['bed_id']; ?>" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="POST">
                <div class="modal-body">
                    <h2>Update Beds</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" value="<?php echo $fetrooms['bed_name']; ?>" name="tname">
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <textarea class="form-control" name="tdescr"><?php echo $fetrooms['bed_descr']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                    </div>
                        <hr>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default" name="btn-update">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="hidden" value="<?php echo $fetrooms['bed_id'];?>" name="tid"/>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
                                     	<?php } ?>
					</tbody>
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
                    <h2>Add Bed Type</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input type="text" name="t_name" class="form-control" placeholder="Bed Type Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                        <textarea name="t_descr" class="form-control" placeholder="Bed Description" required></textarea>
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