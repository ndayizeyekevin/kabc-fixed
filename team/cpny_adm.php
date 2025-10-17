<?php
    if(ISSET($_POST['add'])){
    $sql_check = $db->prepare("SELECT * FROM tbl_users WHERE Uemail = ?");
    try {
        $f_name = $_POST['fname'];
		$l_name = $_POST['lname'];
		$mob_no = $_POST['phone'];
		$email = $_POST["email"];
		$company = $_POST['company'];
		$country = $_POST['country'];
		$province = $_POST['province'];
		$district = $_POST['district'];
		$nid = $_POST['nid'];
		$password = md5($_POST['phone']);
		$username = strtolower($f_name).substr($mob_no, -2);
		
        
        $sql_check->execute(array($email));
        $row_count_check = $sql_check->rowCount();
                if ($row_count_check >= 1)
                {
                $msge="Email have been taken!";
                   
                }else{
                    
            //for user..
            $date = date("Y-m-d");
            $time = date("H:i:s");
            $role_id = '1';
            $staff_id = '1';
            $reg_date = date("Y-m-d");
            
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_users`(`f_name`, `l_name`, `mobile_no`, `Uemail`, `id_no`, `Ncntry_ID`, `prvnce_Id`, `distrct_ID`, `User_cpnyID`, `user_status`, `user_reg_date`) 
            VALUES ('$f_name', '$l_name','$mob_no', '$email', '$nid', '$country','$province','$district','$company','1','$date')";
            $conn->exec($sql);
            $lastId = $conn->lastInsertId();

            $pdo_crud = "INSERT INTO `tbl_user_log` (`f_name`, `l_name`, `phone`,`usn`, `pwd`, `log_role`, `user_id`,`log_status`, `reg_date`) 
            VALUES ('$f_name', '$l_name','$mob_no','$username',ENCODE('" . htmlspecialchars($mob_no, ENT_QUOTES) . "','ITEC_Team'), '$role_id', '$lastId','1', '$date')";
            $conn->exec($pdo_crud);
            
            $msg="Admin Account of ".$f_name." have been created USN: ".$username." Pwd:".$mob_no;
            echo'<meta http-equiv="refresh"'.'content="20;URL=?ougami=cpny_admin">';
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }
         }


// Update 

if(ISSET($_POST['update'])){
    try {
    $admin_id = $_POST['ad_id'];
    $f_name2 = $_POST['fname'];
	$l_name2 = $_POST['lname'];
	$mob_no2 = $_POST['ad_mobile'];
	$email2 = $_POST["ad_email"];
	$company2 = $_POST['ad_company'];
	$address2 = $_POST['ad_address'];
	
	$sql = "UPDATE `tbl_users` SET `firstName` = '$f_name2',`lastName` = '$l_name2',`email` = '$email2',`phone` = '$mob_no2',`address` = '$address2',`company_id` = '$company2' WHERE user_id = '$admin_id'";
	$db->exec($sql);
	
	$sql2 = "UPDATE `tbl_user_log` SET `f_name` = '$f_name2',`l_name` = '$l_name2',`phone` = '$mob_no2' WHERE `user_id`='$admin_id'";
	$db->exec($sql2);
	
	$msg = "Updated Successfully";

	echo'<meta http-equiv="refresh"'.'content="2;URL=?ougami=cpny_admin">';
    }catch(PDOException $e){
                echo $e->getMessage();
            }
}
?>
<!-- delete -->
<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_users` WHERE `user_id`='$id' ");
		$sql->execute();
		
		$sql = $conn->prepare("DELETE from `tbl_user_log` WHERE `user_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?ougami=cpny_admin">';
	}
?>


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
		

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                       
                        <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">View</a></li>
    <li><a data-toggle="tab" href="#menu1">Add New</a></li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
      <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Company</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                		$sql = $db->prepare("SELECT * FROM tbl_users 
                                		INNER JOIN tbl_company ON tbl_users.User_cpnyID = tbl_company.cpny_ID");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                     ?>
                                    <tr>
                                    <td><?php echo $fetch['adm_id']?></td>
                                    <td><?php echo $fetch['firstName']?></td>
                                    <td><?php echo $fetch['lastName']?></td>
                                    <td><?php echo $fetch['email']?></td>
                                    <td><?php echo $fetch['phone']?></td>
                                    <td><?php echo $fetch['address']?></td>
                                    <td><?php echo $fetch['cpny_name']?></td>
                                    <td>
                                    <button class="btn btn-default" data-toggle="modal" data-target="#update<?php echo $fetch['adm_id']; ?>">Edit</button> |
                                    <a class="btn-sm" href="?ougami=cpny_admin&myId=<?php echo $fetch['adm_id'] ?>" onclick="if(!confirm('Do you really want to Delete This Admin?'))return false;else return true;">Delete</a>
                                    </td>
                            </tr>
                                    <div class="modal fade" id="update<?php echo $fetch['adm_id']?>" role="dialog">
                                        
                                        <div class="modal-dialog modals-default">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="" enctype="multipart/form-data" method="POST">
                                                    <input type="hidden" name="ad_id" value="<?php echo $fetch['adm_id']; ?>">
                                                <div class="modal-body">
                                                    <h2>Update Company Admin</h2>
                                                    
                                                    <div class="row">
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                    
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input type="text" class="form-control" name="fname" value="<?php echo $fetch['firstName']; ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                   
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input type="text" class="form-control" name="lname" value="<?php echo $fetch['lastName']; ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                     
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input type="email" class="form-control" name="ad_email" value="<?php echo $fetch['email']; ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    
                                                    <div class="row">
                                                         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                                                    <div class="form-ic-cmp">
                                                                  
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input type="number" class="form-control" name="ad_mobile" value="<?php echo $fetch['phone']; ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                                                    <div class="form-ic-cmp">
                                                                      
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <input type="text" class="form-control" name="ad_address" value="<?php echo $fetch['address']; ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb form-elet-mg">
                                                                    <div class="nk-int-st">
                                                                         <select name="ad_company" class="chosen" data-placeholder="Choose company...">
                                                                        <?php
                                                                        $stmt = $db->query('SELECT * FROM tbl_company');
                                                                        try {
                                                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                                if($res_cntr==$row['cpny_ID'])
                                                                                {
                                                                                ?>
                                                                                <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['cpny_name']; ?></option>
                                            
                                                                                <?php
                                                                            }
                                                                            else
                                                                            {
                                                                                ?>
                                                                                <option value="<?php echo $row['cpny_ID']; ?>"><?php echo $row['cpny_name']; ?></option>
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
                            <?php
                            	}
                            ?>
                        </tbody>
                        
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Company</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                      </table>
                    </div>
    </div>
    <div id="menu1" class="tab-pane fade">
        <div class="data-table-area">
            <div class="data-table-list">
      <form action="" enctype="multipart/form-data" method="POST">
          <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Firstname</strong><span class="text-danger">*</span></label>
                <div class="col-md-3">
                    <input type="text" name="fname" class="form-control" required>
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Lastname</strong></label>
                <div class="col-md-3">
                        <input type="text" name="lname" id="national_id" class="form-control" placeholder="" required>
                </div>
            </div>
            </div>
            <br>
            <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Mobile Number</strong><span class="text-danger">*</span></label>
                <div class="col-md-3">
                    <input type="number" name="phone" class="form-control" required>
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Email</strong></label>
                <div class="col-md-3">
                        <input type="email" name="email" id="national_id" class="form-control" placeholder="" required>
                </div>
            </div>
            </div>
            <br>
            <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Passport No</strong><span class="text-danger">*</span></label>
                <div class="col-md-3">
                    <input type="text" name="nid" class="form-control" required>
                </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-md-2 control-label" for=""><strong>Company</strong></label>
                <div class="col-md-3">
                   
                  <select name="company" class="chosen" data-placeholder="Choose company...">
                                        <?php
                                        $stmt = $db->query('SELECT * FROM tbl_company');
                                        try {
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                if($res_cntr==$row['cpny_ID'])
                                                {
                                                ?>
                                                <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['cpny_name']; ?></option>
            
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <option value="<?php echo $row['cpny_ID']; ?>"><?php echo $row['cpny_name']; ?></option>
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
            <br>
             <label class="col-md-2 control-label" for=""><strong>Nationality</strong></label>
                <div class="col-md-3">
                        <select name="country" class="chosen" data-live-search="true" style="width:100%" required>
                             <option value="">Select Country</option>
                                <?php
                                    $stmt = $db->query('SELECT * FROM tbl_country');
                                    try {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            if($res_cntr==$row['cntr_id'])
                                            {
                                            ?>
                                            <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['cntr_name']; ?></option>
        
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <option value="<?php echo $row['cntr_id']; ?>"><?php echo $row['cntr_name']; ?></option>
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
            <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Province</strong><span class="text-danger">*</span></label>
                <div class="col-md-3">
                    <select name="province" id="province" class="chosen" data-live-search="true" style="width:100%" required>
                             <option value="">Select Province</option>
                                <?php
                                    $stmt = $db->query('SELECT * FROM tbl_province');
                                    try {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            if($res_cntr==$row['province_id'])
                                            {
                                            ?>
                                            <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['province_name']; ?></option>
        
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <option value="<?php echo $row['province_id']; ?>"><?php echo $row['province_name']; ?></option>
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
                <div name='display_res' id='display_res'>
                    
                        
                </div>
            </div>
            </div>
            </form>
        </div>
    </div>
</div>


                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#province").change(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_district.php?province=' + $(this).val() , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>