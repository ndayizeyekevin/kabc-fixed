    <?php
    // Add
        if(ISSET($_POST['add'])){
		try{
			$cpny_name = $_POST['cpny_name'];
			$short = $_POST['short_name'];
			$cpny_email = $_POST['cpny_email'];
			$cpny_phone = $_POST['cpny_phone'];
			$cpny_address = $_POST['cpny_address'];
			$cpny_cat = $_POST['cpny_cat'];
			$cpny_logo = $_POST['cpny_logo'];
			$status = 'ON';
			$date = date("Y-m-d");
			
        //get image
        
            $profile=$_FILES['profile'];
             $file_name = $_FILES['profile']['name'];
             $file_location = $_FILES['profile']['tmp_name'];
             
             if(move_uploaded_file($file_location, "../logoCpny/" . $file_name)){
                // echo $file_name;
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "INSERT INTO `tbl_company`(`cpny_name`,`short_name`,`cpny_email`,`cpny_phone`, `cpny_address`,`categ_ID`,`cpny_logo`,`cpny_reg_date`,`cpny_status`)
			VALUES ('$cpny_name','$short','$cpny_email','$cpny_phone', '$cpny_address','$cpny_cat','$file_name','$date','$status')";
			$db->exec($sql);
             }
			$msg = " Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="2;URL=index?onkey=cpny">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
        }
	?>
	
	<?php
// 	update

 if(ISSET($_POST['update'])){
		try{
			$c_id = $_POST['c_id'];
			$cpny_name = $_POST['c_name'];
			$cpny_email = $_POST['c_email'];
			$cpny_phone = $_POST['c_phone'];
			$cpny_address = $_POST['c_address'];
			$cpny_logo = $_POST['c_logo'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `tbl_company` SET `cpny_name` = '$cpny_name',`cpny_email` = '$cpny_email', `cpny_phone` = '$cpny_phone', `cpny_address` = '$cpny_address', `cpny_logo` = '$cpny_logo' WHERE cpny_ID = '$c_id'";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="2;URL=?onkey=cpny">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
 
 
 
 if(isset($_GET['acid']))
{
    
    try{
			$change_status = "ON";
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE `tbl_company`SET `cpny_status` = '".$change_status."' WHERE `cpny_ID` = '".$_GET['acid']."'";
			$conn->exec($sql);
			$msg="Company Activate successfully";
			
			echo'<meta http-equiv="refresh"'.'content="5;URL=?onkey=cpny">';
		}catch(PDOException $e){
			echo $e->getMessage();
		}
		
  }

 // for Deactivate Subject
    else if(isset($_GET['did']))
      {
 try{
			$change_status = "OFF";
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE `tbl_company`SET `cpny_status` = '".$change_status."' WHERE `cpny_ID` = '".$_GET['did']."'";
			$conn->exec($sql);
			$msge="Company Deactivate successfully";
			
			echo'<meta http-equiv="refresh"'.'content="2;URL=?onkey=cpny">';
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
										<h2>Manage Company</h2>
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
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                		$sql = $db->prepare("SELECT * FROM tbl_company 
                                		INNER JOIN tbl_company_category ON tbl_company.categ_ID = tbl_company_category.category_ID");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                         $CID = $fetch['cpny_ID'];
                                         $status = $fetch['cpny_status'];
                                     ?>
                                    <tr>
                                    <td><?php echo $fetch['cpny_ID']?></td>
                                    <td><img src="../logoCpny/<?php echo $fetch['cpny_logo']?>" class="img-circle" alt="Company logo" width="50px" ></td>
                                    <td><?php echo $fetch['cpny_name']?></td>
                                    <td><?php echo $fetch['cpny_email']?></td>
                                    <td><?php echo $fetch['cpny_phone']?></td>
                                    <td><?php echo $fetch['cpny_address']?></td>
                                    <td><?php echo $fetch['categ_name']?></td>
                                       <td>
                                           <?php if($status == 'OFF')
                                          { ?>
                                            <a href="index.php?onkey=cpny&acid=<?php echo $CID;?>" class="label label-danger" onclick="if(!confirm('Do you really want to Activate this Company?'))return false;else return true;" title="Enable" >Inactive</a><?php } else {?>
                
                                            <a href="index.php?onkey=cpny&did=<?php echo $CID;?>" class="label label-success" onclick="if(!confirm('Do you really want to Disactivate this Company?'))return false;else return true;" title="Disable" >Active</a>
                                         <?php }?>
                                           
                                        </td>
                                    <td>
                                    <a href="#" data-toggle="modal" data-target="#update<?php echo $fetch['cpny_ID']?>"><i class="fa fa-edit"></i> Edit</a>
                                    </td>
                            </tr>
                            <div class="modal fade" id="update<?php echo $fetch['cpny_ID']?>" role="dialog">
                              <div class="modal-dialog modals-default">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form action="" method="POST">
                                    <div class="modal-body">
                                        <h2>Update Company</h2>
                                        
                                        <div class="row">
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="form-ic-cmp">
                                                            <i class="notika-icon notika-support"></i>
                                                        </div>
                                                        <div class="nk-int-st">
                                                            <input type="text" class="form-control" value="<?php echo $fetch['cpny_name']; ?>" name="c_name" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="form-ic-cmp">
                                                            <i class="notika-icon notika-mail"></i>
                                                        </div>
                                                        <div class="nk-int-st">
                                                            <input type="text" class="form-control" value="<?php echo $fetch['cpny_email']; ?>" name="c_email" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb floating-lb">
                                                        <div class="form-ic-cmp">
                                                            <i class="notika-icon notika-phone"></i>
                                                        </div>
                                                        <div class="nk-int-st">
                                                            <input type="text" class="form-control" value="<?php echo $fetch['cpny_phone']; ?>" name="c_phone">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        <div class="row">
                                             <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                                        <div class="form-ic-cmp">
                                                            <i class="notika-icon notika-map"></i>
                                                        </div>
                                                        <div class="nk-int-st">
                                                            <input type="text" class="form-control" value="<?php echo $fetch['cpny_address']; ?>" name="c_address">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                                        <div class="nk-int-st">
                                                            <input type="file" class="form-control" value="<?php echo $fetch['cpny_logo']; ?>" name="c_logo">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                    </div>
                                            <hr>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-default" name="update">Update</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input type="hidden" value="<?php echo $fetch['cpny_ID']?>" name="c_id"/>
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
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Category</th>
                                <th>Status</th>
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
                    <h2>Add Company</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="cpny_name" placeholder="Company Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-mail"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="cpny_email" placeholder="Email Address" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-phone"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="cpny_phone" placeholder="Contact Number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <div class="row">
                         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-map"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="cpny_address" placeholder="Company Address" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-map"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <select name="cpny_cat" class="chosen" data-placeholder="Choose category...">
                                        <?php
                                        $stmt = $db->query('SELECT * FROM tbl_company_category');
                                        try {
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                if($res_cntr==$row['category_ID'])
                                                {
                                                ?>
                                                <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['categ_name']; ?></option>
            
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <option value="<?php echo $row['category_ID']; ?>"><?php echo $row['categ_name']; ?></option>
                                                <?php
                                            }
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
                                <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                    <div class="nk-int-st">
                                        <input type="file" class="form-control" name="profile" placeholder="logo">
                                    </div>
                                </div>
                            </div>
                           
                         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-map"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="short_name" placeholder="Short Name" required>
                                    </div>
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