<?php
if(ISSET($_POST['add'])){
    $dept_name = $_POST['dept_name'];
    $dept_desc = $_POST['dept_desc'];
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "INSERT INTO `tbl_department`(`dept_name`,`dept_desc`,`comp_id`)
			VALUES ('$dept_name','$dept_desc','$company')";
			$db->exec($sql);
			
			$msg = " Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=department">';
}
?>
	<?php
// 	update

 if(ISSET($_POST['update'])){
		try{
			$d_id = $_POST['d_id'];
			$d_name = $_POST['d_name'];
			$d_desc = $_POST['d_desc'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `tbl_department` SET `dept_name` = '$d_name', `dept_desc` = '$d_desc' WHERE dept_id = '$d_id'";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=department">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_department` WHERE `dept_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=cpny_role">';
	}
?>


<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                            <h2>Department</h2>
                            </div>
                        <div class="table-responsive">
                            <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModalone" style="margin: 5px;">Add New Department</button>
                            <!--notification-->
                
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
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                       <th>#</th>
                                        <th>Department Name</th>
                                        <th>Department Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                		$sql = $conn->prepare("SELECT * FROM `tbl_department`");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                     	?>
                        <tr >
                            <td><?php echo $fetch['dept_id']?></td>
                            <td><?php echo $fetch['dept_name']?></td>
                            <td><?php echo $fetch['dept_desc']?></td>
                            <td>
                                <button class="" data-toggle="modal" data-target="#update<?php echo $fetch['dept_id']?>">Edit</button> |
                                 <a class="btn-sm" href="?resto=cpny_role&myId=<?php echo $fetch['dept_id'] ?>" onclick="if(!confirm('Do you really want to Delete This Role?'))return false;else return true;">Delete</a>
                            </td>
                        </tr>
                            <div class="modal fade" id="update<?php echo $fetch['dept_id']?>" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="POST">
                <div class="modal-body">
                    <h2>Update Department</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" value="<?php echo $fetch['dept_name']; ?>" name="d_name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" value="<?php echo $fetch['dept_desc']; ?>" name="d_desc">
                                    </div>
                                </div>
                            </div>
                    </div>
                        <hr>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default" name="update">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="hidden" value="<?php echo $fetch['dept_id']?>" name="d_id"/>
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
                                        <th>Department Name</th>
                                        <th>Department Description</th>
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
                <form action="" method="POST">
                <div class="modal-body">
                    <h2>Add Department</h2>
                    
                    <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="dept_name" placeholder="Department Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="dept_desc" placeholder="Department Description" required>
                                    </div>
                                </div>
                            </div>
                    
                </div>
                        <hr>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default" name="add">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>