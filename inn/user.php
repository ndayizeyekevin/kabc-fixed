<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require_once("../inc/config.php");

$msg = '';
$msge = '';

// Handle new user creation
if (isset($_POST['add'])) {
    $f_name = $_POST['fname'];
    $l_name = $_POST['lname'];
    $mob_no = $_POST['phone'];
    $password = $_POST['password'];
    $email = $_POST["email"];
    $role = $_POST['role'];
    $nid = $_POST['nid'];
    $company = 1; // Assuming a default company ID. Change if necessary.
    $username = strtolower($f_name);
    $date = date("Y-m-d");

    $hashed_password = md5($password);

    try {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare("SELECT * FROM tbl_staff WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $msge = "Error: Email already used";
        } else {
            $stmt = $db->prepare("SELECT * FROM tbl_user_log WHERE pwd = ?");
            $stmt->execute([$hashed_password]);
            if ($stmt->fetch()) {
                $msge = "Error: Password already used";
            } else {
                $sql = "INSERT INTO `tbl_users`(`f_name`, `l_name`, `mobile_no`, `Uemail`,`role_id`, `id_no`,`User_cpnyID`, `user_status`, `user_reg_date`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$f_name, $l_name, $mob_no, $email, $role, $nid, $company, $date]);
                $lastId = $db->lastInsertId();

                $pdo_crud = "INSERT INTO `tbl_user_log` (`f_name`, `l_name`, `phone`,`usn`, `pwd`, `log_role`, `user_id`,`log_status`, `reg_date`,email) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)";
                $stmt = $db->prepare($pdo_crud);
                $stmt->execute([$f_name, $l_name, $mob_no, $username, $hashed_password, $role, $lastId, $date, $email]);

                $msg = "Successfully Added!";
                echo '<meta http-equiv="refresh" content="0;URL=index?resto=users">';
            }
        }
    } catch (PDOException $e) {
        $msge = "Error: " . $e->getMessage();
    }
}

// Handle user update
if (isset($_POST['update'])) {
    $adminID = $_POST['staff_id'];
    $family_name2 = $_POST['family_name_upd'];
    $firstname2 = $_POST['firstname_upd'];
    $pwd = $_POST['pwd'];
    $role2 = $_POST['role_upd'];

    try {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql1 = "UPDATE `tbl_users` SET `f_name` = ?, `l_name` = ?, `role_id` = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql1);
        $stmt->execute([$family_name2, $firstname2, $role2, $adminID]);

        if(!empty($pwd)) {
            $hashed_password = md5($pwd);
            $stmt = $db->prepare("SELECT * FROM tbl_user_log WHERE pwd = ? AND user_id != ?");
            $stmt->execute([$hashed_password, $adminID]);
            if ($stmt->fetch()) {
                $msge = "Error: Password already used";
            } else {
                $sql2 = "UPDATE `tbl_user_log` SET `f_name` = ?, `l_name` = ?, `pwd` = ? WHERE `user_id`= ?";
                $stmt = $db->prepare($sql2);
                $stmt->execute([$family_name2, $firstname2, $hashed_password, $adminID]);
            }
        } else {
            $sql2 = "UPDATE `tbl_user_log` SET `f_name` = ?, `l_name` = ? WHERE `user_id`= ?";
            $stmt = $db->prepare($sql2);
            $stmt->execute([$family_name2, $firstname2, $adminID]);
        }

        $msg = "Updated Successfully";
        echo '<meta http-equiv="refresh" content="0;URL=?resto=users">';
    } catch (PDOException $e) {
        $msge = "Error: " . $e->getMessage();
    }
}

// Handle user deletion
if (isset($_GET['deleteId'])) {
    $id = $_GET['deleteId'];
    try {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = $db->prepare("DELETE from `tbl_users` WHERE `user_id`= ?");
        $sql->execute([$id]);

        $sql = $db->prepare("DELETE from `tbl_user_log` WHERE `user_id`= ?");
        $sql->execute([$id]);

        $msg = "Deleted Successfully!";
        echo '<meta http-equiv="refresh" content="0;URL=?resto=users">';
    } catch (PDOException $e) {
        $msge = "Error: " . $e->getMessage();
    }
}

function getPassword($usn){
    global $db;
    try {
        $sql = "SELECT * FROM tbl_user_log where user_id= ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$usn]);
        if ($stmt->fetch()) {
            return "Hidden";
        }
    } catch (PDOException $e) {
        // Handle exception
    }
    return "N/A";
}
?>

<style>
    .form-ic-cmp{
        margin-bottom:10px;
    }
</style>
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
										<h2>Manage Users</h2>
										<p>Welcome to <?php echo isset($Rname) ? $Rname : '';?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add User" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
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
                                        <th>Firstname</th>
                                        <th>Lastname</th>
                                        <th>Mobile Number</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                		$sql = $db->prepare("SELECT * FROM `tbl_users` 
                                		INNER JOIN `tbl_roles` ON tbl_users.role_id=tbl_roles.role_id order by user_id DESC");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                		    $i++;
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><?php echo $fetch['f_name']; ?></td>
                                            <td><?php echo $fetch['l_name']; ?></td>
                                            <td><?php echo $fetch['mobile_no']; ?></td>
                                            <td><?php echo $fetch['Uemail']; ?></td>
                                            <td><?php echo getPassword($fetch['user_id']); ?></td>
                                            <td><?php echo $fetch['role_name']; ?></td>
                                            
                                            <td>
                                                <a href="#" 
                                                   data-toggle="modal" 
                                                   data-target="#updateModal" 
                                                   data-id="<?php echo $fetch['user_id']; ?>" 
                                                   data-fname="<?php echo $fetch['f_name']; ?>" 
                                                   data-lname="<?php echo $fetch['l_name']; ?>" 
                                                   data-role="<?php echo $fetch['role_id']; ?>" 
                                                   class="edit_data"><i class="icon-edit"></i> Edit</a>|
                                                <a class="btn-sm" href="?resto=users&deleteId=<?php echo $fetch['user_id'] ?>" onclick="return confirm('Do you really want to Delete This User?');">Delete</a>
                                            </td>
                                     	</tr>
                                     	<?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add new user modal -->
    <div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add New User</h4>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label" for=""><strong>Firstname</strong><span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <input type="text" name="fname" class="form-control" required>
                            </div>
                            <label class="col-md-2 control-label" for=""><strong>Lastname</strong></label>
                            <div class="col-md-4">
                                <input type="text" name="lname" class="form-control" placeholder="" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label" for=""><strong>Mobile Number</strong><span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <input type="number" name="phone" class="form-control" required>
                            </div>
                            <label class="col-md-2 control-label" for=""><strong>Email</strong></label>
                            <div class="col-md-4">
                                <input type="email" name="email" class="form-control" placeholder="" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label" for=""><strong>ID No</strong><span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <input type="text" name="nid" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label" for=""><strong>Role</strong></label>
                            <div class="col-md-4">
                                <select name="role" class="form-control chosen" data-placeholder="Choose role...">
                                <?php
                                $stmt_roles = $db->query('SELECT * FROM tbl_roles');
                                try {
                                    while ($row_roles = $stmt_roles->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $row_roles['role_id']; ?>"><?php echo $row_roles['role_name']; ?></option>
                                        <?php
                                    }
                                } catch (PDOException $ex) {
                                    echo $ex->getMessage();
                                }
                                ?>
                                </select>
                            </div>
                            <label class="col-md-2 control-label" for=""><strong>Password</strong></label>
                            <div class="col-md-4">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-actions col-md-12">
                            <br />
                            <center>
                                <button type="submit" name="add" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
                                <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>
                            </center>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- Update user modal -->
    <div class="modal fade" id="updateModal" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update User</h4>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        First name
                                        <input type="text" name="family_name_upd" id="edit_fname" class="form-control" placeholder="Firstname Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                         Last name
                                        <input type="text" name="firstname_upd" id="edit_lname" class="form-control" placeholder="Last Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group ic-cmp-int">
                                    <div class="nk-int-st">
                                         Password (leave blank to keep current password)
                                        <input type="password" name="pwd" class="form-control" placeholder="Password here...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group ic-cmp-int">
                                    <div class="nk-int-st">
                                        <select name="role_upd" id="edit_role" class="form-control select2" style="width:100%" required>
                                            <?php
                                            $stmt_roles = $db->query('SELECT * FROM tbl_roles');
                                            while ($row_roles = $stmt_roles->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                <option value="<?php echo $row_roles['role_id']; ?>"><?php echo $row_roles['role_name']; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="edit_user_id" name="staff_id"/>
                        <button type="submit" class="btn btn-default" name="update">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
$(document).on("click", ".edit_data", function () {
     var userId = $(this).data('id');
     var fname = $(this).data('fname');
     var lname = $(this).data('lname');
     var role = $(this).data('role');

     $(".modal-body #edit_fname").val( fname );
     $(".modal-body #edit_lname").val( lname );
     $(".modal-body #edit_role").val( role );
     $(".modal-footer #edit_user_id").val( userId );
});
</script>
