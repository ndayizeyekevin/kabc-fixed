<?php
if(ISSET($_POST['add'])){
         try{
            $menu_name = $_POST['menu_name'];
			$price = $_POST['price'];
    
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "INSERT INTO `menu`(`menu_name`,`menu_price`,`menu_cpny_id`,`status`)
			VALUES ('$menu_name','$price','$cpny_ID','1')";
			$db->exec($sql);
			
			$msg = " Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=menu_menu">';
        }catch(PDOException $e){
        			echo $e->getMessage();
        		}
        }
?>
	<?php
// 	update

 if(ISSET($_POST['btn-update'])){
		try{
			$dr_id = $_POST['dr_id'];
			$dr_name = $_POST['dr_name'];
			$dr_price = $_POST['dr_price'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql2 = "UPDATE `menu` SET `menu_name` = '$dr_name',`menu_price` = '$dr_price' WHERE menu_id = '$dr_id' ";
			$db->exec($sql2);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="1;URL=?resto=menu_menu">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `menu` WHERE `menu_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=menu_drink>';
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
										<h2>Manage Drinks</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Drink" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
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
                                        <th>Drink Name</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                     $i = 0;
                                		$sql = $conn->prepare("SELECT * FROM `menu` WHERE cat_id = '2'");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                            $i++;
                                     	?>
                        <tr >
                            <td><?php echo $i; ?></td>
                            <td><?php echo $fetch['menu_name']?></td>
                            <td><?php echo $fetch['menu_price']?></td>
                        </tr>
                        <div class="modal fade" id="update<?php echo $fetch['menu_id']?>" role="dialog">
                            <div class="modal-dialog modals-default">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form action="" method="POST">
                                    <div class="modal-body">
                                        <h2>Update menu</h2>
                                        
                                        <div class="row">
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="nk-int-st">
                                                            <input type="text" class="form-control" value="<?php echo $fetch['menu_name']; ?>" name="dr_name">
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="nk-int-st">
                                                            <input type="text" class="form-control" value="<?php echo $fetch['menu_price']; ?>" name="dr_price">
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                            <hr>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-default" name="btn-update">Update</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input type="hidden" value="<?php echo $fetch['menu_id']?>" name="dr_id"/>
                                    </div>
                                    </form>
                                </div>
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
                                        <th>menu Name</th>
                                        <th>Price</th>
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
                    <h2>Add menu</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="menu_name" placeholder="menu Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="price" placeholder="menu Price" required>
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