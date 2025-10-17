<?php
if(ISSET($_POST['add'])){
 try{
	$cat = $_POST['cat'];
			$sql = "INSERT INTO `category`(`cat_name`) 
					VALUES('$cat')";
			$db->exec($sql);
			
			$msg = " Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=categ">';
        }
        
        catch(PDOException $e){
        			echo $e->getMessage();
        		}
        }
?>
	<?php
// 	update

 if(ISSET($_POST['btn-update'])){
		try{
			$id = $_POST['id'];
        	$cat_name = $_POST['cat_name'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql2 = "UPDATE category SET cat_name='$cat_name' where cat_id='$id' ";
			$db->exec($sql2);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="1;URL=?resto=categ">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `category` WHERE `cat_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=categ">';
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
										<h2>Manage Category</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Category" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->


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
                                        <th>Category Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                        $i = 0;
                                		$sql = $conn->prepare("SELECT * FROM `category`");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                		    $i++
                                     	?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $fetch['cat_name']?></td>
                            <td>
                                <button class="" data-toggle="modal" data-target="#update<?php echo $fetch['cat_id']?>">Edit</button> |
                                 <a class="btn-sm" href="?resto=categ&myId=<?php echo $fetch['cat_id'] ?>" onclick="if(!confirm('Do you really want to Delete This Category?'))return false;else return true;">Delete</a>
                            </td>
                        </tr>
    <div id="update<?php echo $fetch['cat_id']?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Update Category</h4>
            </div>
            <div class="modal-body" style="height:150px">
              <!--start form-->
              <form class="form-horizontal" method="post" action="" enctype='multipart/form-data'>
                  <!-- Title -->
                  <input type="hidden" name="id" value="<?php echo $fetch['cat_id']?>">
                
                  <div class="form-group">
                      <label class="control-label col-lg-4" for="title">Category</label>
                      <div class="col-lg-8"> 
                        <input type="text" class="form-control" name="cat_name" value="<?php echo $fetch['cat_name']?>">
                      </div>
                  </div>
                  <div class="col-md-4">
                  </div>  
                  <div class="col-md-8">
                        <button type="submit" class="btn btn-sm btn-primary" name="btn-update">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                  </div>  
              </form>
              <!--end form-->
            </div>
           
        </div><!--modal content-->
    </div><!--modal dialog-->
</div>
                                    <?php
                                	}
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Category Name</th>
                                        <th>Action</th>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add New Category</h4>
            </div>
            <div class="modal-body">
              <!--start form-->
              <form class="form-horizontal" method="post" action="" enctype='multipart/form-data'>
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">Category Name</label>
                      <div class="col-lg-8"> 
                        <input type="text" class="form-control" name="cat" placeholder="Category Name" required>
                      </div>
                  </div> 
                  <!-- Buttons -->
                  <div class="form-group">
                      <!-- Buttons -->
                      <div class="col-lg-offset-2 col-lg-6">
                        <button type="submit" name="add" class="btn btn-sm btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                       </div>
                  </div>
              </form>
              <!--end form-->
            </div>
            
        </div><!--modal content-->
    </div><!--modal dialog-->
</div>