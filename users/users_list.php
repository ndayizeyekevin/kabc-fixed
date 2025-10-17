<?php session_start();
if(!$_SESSION['loggedIn']){
	echo "<script>window.location='../../index.php'</script>";
}

$breadcrumb_items = [
    ['name' => 'Users', 'link' => '../users/users_list.php', 'active' => false],
    ['name' => 'All Users', 'link' => '', 'active' => true]
];
?>


<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template-free">
<!-- header -->
<?php include_once "../../partials/header.php"; ?>


<?php if(isset($_POST['add'])){
	
	
	    $username=  mysqli_real_escape_string($conn,$_POST['username']);
		$username= htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
		$username= stripslashes($username);
		 
		 $email=  mysqli_real_escape_string($conn,$_POST['email']);
		 $email= htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
		 $email= stripslashes($email);
		 
		 
		 $role=  mysqli_real_escape_string($conn,$_POST['role']);
		 $role= htmlspecialchars($role, ENT_QUOTES, 'UTF-8');
		 $role= stripslashes($role);
		 
		 $fullname=  mysqli_real_escape_string($conn,$_POST['fullname']);
		 $fullname= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
		 $fullname= stripslashes($fullname);
		 
		 
		 $phone_number=  mysqli_real_escape_string($conn,$_POST['phone_number']);
		 $phone_number= htmlspecialchars($phone_number, ENT_QUOTES, 'UTF-8');
		 $phone_number= stripslashes($phone_number);
		 
		 
		 $password=  mysqli_real_escape_string($conn,$_POST['password']);
		 $password= htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
		 $password= stripslashes($password);
		 
		 $password = password_hash($password, PASSWORD_DEFAULT);
		 
		 
		 
$sql = "INSERT INTO `tbl_acc_users` (`user_id`, `username`, `email`, `password`, `full_name`, `phone_number`, `created_at`, `role_id`) 
VALUES (NULL, '$username', '$email', '$password ', ' $fullname', '$phone_number', CURRENT_TIMESTAMP, '$role');";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('User created')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
	
}
?>

<!-- / header -->

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- side Menu -->
            <?php include_once "../../partials/side_nav.php"; ?>
            <!-- / side Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- top Navbar -->
                <?php include_once "../../partials/top_nav.php"; ?>
                <!-- / top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- breadcrumbs -->
                        <p><?php include '../../partials/breadcrumb.php'; ?></p>

                        <div id="getAlert"></div>

                        <!-- edit venue offcanvas -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="editVenueOffcanvas" aria-labelledby="editVenueLabel">
                            <div class="offcanvas-header">
                                <h5 id="editVenueLabel" class="offcanvas-title">Edit Venue</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <!-- Edit Venue Form -->
                                <form id="edit-venue-form">
                                    <input type="hidden" id="edit_venue_id" />

                                    <div class="mb-3">
                                        <label for="edit_venue_name" class="form-label">Venue Name</label>
                                        <input type="text" class="form-control" id="edit_venue_name" placeholder="Venue Name" required />
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_venue_type" class="form-label">Venue Type</label>
                                        <select class="form-select" id="edit_venue_type" required>
                                            <!-- Venue types will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_capacity" class="form-label">Capacity</label>
                                        <input type="number" class="form-control" id="edit_capacity" placeholder="200" required />
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="edit_location" placeholder="1234 Street, City" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_status" class="form-label">Status</label>
                                        <select class="form-select" id="edit_status" required>
                                            <!-- Status options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_amenities" class="form-label">Amenities</label>
                                        <div id="edit_amenities_container" class="mb-2"></div>
                                        <select class="form-select" id="edit_amenity" aria-label="Select Amenity">
                                            <!-- Amenities options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="offcanvas-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Users List</h5>
                                        <button type="button" id="addVenueModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVenueModal">
                                            <i class="bx bx-plus"></i> Add New user
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->
                                       
                                       <table  class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Names</th>
                                                    <th>Username</th>
                                                    <th>Email</th>
                                                    <th>Phone number</th>
                                                    <th>Role</th>
                                                  
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
											<?php $sql = "SELECT * FROM tbl_acc_users ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>
	
	<tr>
                                                    <td>#<?php echo $row['user_id'];?></td>
                                                    <td><?php echo $row['full_name'];?></td>
                                                    <td><?php echo $row['username'];?></td>
                                                    <td><?php echo $row['email'];?></td>
                                                    <td><?php echo $row['phone_number'];?></td>
                                                   <td><?php echo getRoleNAME($row['role_id']);?></td>
                                                  
                                                    <th></th>
                                                </tr>
	
<?php	
	
  }
}
	?>										
											
                                                  
                                            </tbody>
                                        </table>

                                        <!-- Pagination -->
                                        <nav class="mt-2" aria-label="Page navigation">
                                            <ul class="pagination" id="pagination">
                                                <!-- Pagination links will be populated here -->
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- add venue -->
    <div class="modal fade" id="addVenueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label" for="venue_name">UserName</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                            
                                    placeholder="username"
									name="username"
                                
                                    required />
                            </div>
                        </div>
						     <div class="mb-3">
                            <label class="form-label" for="venue_name">Full name</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="fullname"
                                    placeholder="Full names"
                                
                                    required />
                            </div>
                        </div>
						
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Email</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="email"
                                    placeholder="Email"
                                
                                    required />
                            </div>
                        </div>
						
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Phone Number</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="phone_number"
                                    placeholder="phone_number"
                                
                                    required />
                            </div>
                        </div>
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Password</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="password"
                                    class="form-control"
                              name="password"
                                    placeholder="password"
                                
                                    required />
                            </div>
                        </div>
						
                        <div class="mb-3">
                            <label class="form-label" for="venue_type">Role Type</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_type_label" class="input-group-text"></i></span>
                                <select
                                    class="form-control"
                                   name="role"
                                    required>
									
									
<?php $sql = "SELECT * FROM tbl_acc_roles";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
?><option value='<?php echo $row['role_id']?>'><?php echo $row['role_name']?></option><?php
  }
}
?>
                                   
                                </select>
                            </div>
                        </div>
                 
                   

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <input  type="submit" class="btn btn-primary" name="add" value="Create">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal end -->

        <div id="showToast" class="toast-container position-relative"></div>

        <!-- Footer -->
        <?php include_once "../../partials/footer.php"; ?>
        <script src="js/venues.js"></script>
</body>

</html>