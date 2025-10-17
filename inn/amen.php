  <?php
  if(ISSET($_POST['save'])){
    $sql_check = $db->prepare("SELECT * FROM amenities WHERE amen_name = ?");
    try {
        
        $t_name = $_POST['amen_name'];
        $t_descr = $_POST['amen_desc'];
        
        $sql_check->execute(array($t_name));
        $row_count_check = $sql_check->rowCount();
                if ($row_count_check >= 1)
                {
                $msge="This Amenity Already Registered!";
                   
                }else{
             
      	try {
  	      $profile=$_FILES['profile'];
          $file_name = $_FILES['profile']['name'];
          $file_location = $_FILES['profile']['tmp_name'];
                 
             if(move_uploaded_file($file_location, "../amen_image/".$file_name)){
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `amenities` (`amen_name`,`amen_desc`,`amen_image`)
            VALUES ('$t_name','$t_descr','$file_name')";
            $db->exec($sql);
      	}
            }catch(PDOException $e){
                echo $e->getMessage();
            }
             }
            $msg= "Amenity registered Successfully!";
            echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=amenities">';
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
			
			$sql = "UPDATE `amenities` SET `amen_name` = '$tname', `amen_desc` = '$tdescr' WHERE amen_id = '$t_id'";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
			echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=amenities">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
 <?php
  if(ISSET($_POST['update_image'])){
		try{
		    $imgid = $_POST['imgid'];
		    
		    $chkimg = $db->prepare("SELECT * FROM amenities WHERE amen_id = '".$imgid."'");
		    $chkimg->execute();
		    $rowimg = $chkimg->fetch();
		    $img = $rowimg['amen_image'];
		    $path = '../amen_image/'.$img;
		    unlink($path);
		    
			 $image=$_FILES['image'];
          $file_name = $_FILES['image']['name'];
          $file_location = $_FILES['image']['tmp_name'];
          if($file_name == ""){
              $msge = "No Image Found!";
          }
          else{
                 
             if(move_uploaded_file($file_location, "../amen_image/".$file_name)){
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `amenities` SET `amen_image` = '$file_name' WHERE amen_id = '".$imgid."' ";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
			echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=amenities">';
             }
          }
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `amenities` WHERE `amen_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=amenities">';
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
										<h2>Manage Amenities</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Amenities" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
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
            							<th>Amenity Name</th>
            							<th>Image</th>
            							<th>Descriptions</th>
            							<th>Action</th>
            						</tr>
					            </thead>
					<tbody>
                        <?php

                            $sql_rooms = $db->prepare("SELECT * FROM amenities");
                            $sql_rooms->execute();
                           
                            $i = 0;
                            while($fetrooms = $sql_rooms->fetch()) {
                                $i += 1;
                        ?>
                                     <tr class="gradeU">
							<td><?php echo $i; ?></td>
            				<td class="center">
                				<span><?php echo $fetrooms['amen_name']; ?></span><br/>
            				</td>
            				<td><a href="" data-toggle="modal" data-target="#update_image<?php echo $fetrooms['amen_id'];?>"><img src="../amen_image/<?php echo $fetrooms['amen_image']; ?>" class="img-thumbnail" style="width:60px;height:60px;"></a></td>
            					<td><?php echo $fetrooms['amen_desc']; ?></td>
                            <td class="cell-icon text-center">
                                <a href="" data-toggle="modal" data-target="#update<?php echo $fetrooms['amen_id'];?>" class="edit_data"><i class="icon-edit"></i> Edit</a>|
                                <a href="?resto=amenities&&myId=<?php echo $fetrooms['amen_id'];?>" onclick="if(!confirm('Do you really want to Delete This Amenity?'))return false;else return true;" title="Delete room"><span style="color: #870813;"><i class="icon-trash"></i> Delete</span></a>
                            </td>
						</tr>
					    <div class="modal fade" id="update<?php echo $fetrooms['amen_id']; ?>" role="dialog">
                            <div class="modal-dialog modals-default">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form action="" method="POST">
                                    <div class="modal-body">
                                        <h2>Update Amenities</h2>
                                        
                                        <div class="row">
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="nk-int-st">
                                                            <input type="text" class="form-control" value="<?php echo $fetrooms['amen_name']; ?>" name="tname">
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="nk-int-st">
                                                            <textarea class="form-control" name="tdescr"><?php echo $fetrooms['amen_desc']; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                            <hr>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-default" name="btn-update">Update</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input type="hidden" value="<?php echo $fetrooms['amen_id'];?>" name="tid"/>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="update_image<?php echo $fetrooms['amen_id'];; ?>" role="dialog">
                            <div class="modal-dialog modals-default">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form action="" enctype="multipart/form-data" method="POST">
                                    <div class="modal-body">
                                        <h2>Update Amenities Image</h2>
                                        
                                        <div class="row">
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="nk-int-st">
                                                            <input type="text" onfocus=(type='file') class="form-control" name="image" placeholder="Choose Image">
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                        </div>
                                        </div>
                                            <hr>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-default" name="update_image">Update</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input type="hidden" value="<?php echo $fetrooms['amen_id'];?>" name="imgid"/>
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
    							<th>Type Name</th>
    							<th>Descriptions</th>
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
                    <h2>Add Amenities</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input class="form-control" id="name" name="amen_name" placeholder="Amenity Name" type="text" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                         <textarea class="form-control" id="description" name="amen_desc" placeholder="Amenity Description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                         <input type="text" onfocus=(type='file') class="form-control" name="profile" value="" placeholder="Choose file to upload">
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                  <hr>
                <div class="modal-footer">
                    <button type="submit" name="save" class="btn btn-default">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>