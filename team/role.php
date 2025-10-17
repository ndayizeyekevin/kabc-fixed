<?php
if(ISSET($_POST['add'])){
    $role_name = $_POST['cpny_role'];
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "INSERT INTO `tbl_roles`(`role_name`)
			VALUES ('$role_name')";
			$db->exec($sql);
			
			$msg = " Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="2;URL=index?onkey=cpny_role">';
}
?>
	<?php
// 	update

 if(ISSET($_POST['update'])){
		try{
			$r_id = $_POST['r_id'];
			$r_name = $_POST['r_name'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `tbl_roles` SET `role_name` = '$r_name' WHERE role_id = '$r_id'";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="2;URL=?onkey=cpny_role">';
			
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
										<h2>Manage Company Roles</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Company" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
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
                                        <th>Role Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                		$sql = $conn->prepare("SELECT * FROM `tbl_roles`");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                     	?>
                        <tr >
                            <td><?php echo $fetch['role_id']?></td>
                            <td><?php echo $fetch['role_name']?></td>
                            <td>
                                <button class="" data-toggle="modal" data-target="#update<?php echo $fetch['role_id']?>">Edit</button>
                            </td>
                        </tr>
                            <div class="modal fade" id="update<?php echo $fetch['role_id']?>" role="dialog">
                                <div class="modal-dialog modals-default">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="" method="POST">
                                        <div class="modal-body">
                                            <h2>Update Role</h2>
                                            
                                            <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int float-lb floating-lb">
                                                            <div class="form-ic-cmp">
                                                                <i class="notika-icon notika-support"></i>
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <input type="text" class="form-control" value="<?php echo $fetch['role_name']; ?>" name="r_name">
                                                            </div>
                                                        </div>
                                                    </div>
                                        </div>
                                                <hr>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-default" name="update">Update</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="hidden" value="<?php echo $fetch['role_id']?>" name="r_id"/>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                    <?php
                                	}
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Role Name</th>
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
                    <h2>Record New Role</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="cpny_role" placeholder="Role Name" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <hr>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-default" name="add">Save changes</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>