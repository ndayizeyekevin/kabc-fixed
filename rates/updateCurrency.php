<?php session_start();
if(!$_SESSION['loggedIn']){
	echo "<script>window.location='../../index.php'</script>";
}

$breadcrumb_items = [
    ['name' => 'currencies', 'link' => '../rates/currency.php', 'active' => false],
    ['name' => 'currencies', 'link' => '', 'active' => true]
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
	
	
	
		 
		 $fullname=  mysqli_real_escape_string($conn,$_POST['fullname']);
		 $fullname= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
		 $fullname= stripslashes($fullname);
		 
		 
		 $exchange=  mysqli_real_escape_string($conn,$_POST['exchange']);
		 $exchange= htmlspecialchars($exchange, ENT_QUOTES, 'UTF-8');
		 $exchange= stripslashes($exchange);
		 
		
		 
		 
		 
$sql = "UPDATE  `currencies` SET `name`='$fullname', `currency_exchange`='$exchange' where currency_id='".$_REQUEST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Updated')</script>";
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
                                     
                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->
                                   
<?php $sql = "SELECT * FROM currencies where currency_id='".$_REQUEST['id']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>


 <form method="POST">
                 
						     <div class="mb-3">
                            <label class="form-label" for="venue_name">Currency name</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="fullname"
                                    placeholder="Currency Name e.g (USD,Euro)"
                                value="<?php echo $row['name'];?>"
                                    required />
                            </div>
                        </div>
						
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Exchange In Rwf</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="exchange"
                                    placeholder="123"
                               value ="<?php echo $row['currency_exchange'];?>"
                                    required />
                            </div>
                        </div>
						
						
				
						
					
			
                 
                   

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <input  type="submit" class="btn btn-primary" name="add" value="Update">
                        </div>
                    </form>



<?php	
	
  }
}
	?>										
											
                                                  
                                       

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