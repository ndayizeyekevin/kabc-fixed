<div class="colr-area">
        <div class="container">
            <!-- Side Menu -->
           
            <!-- / Side Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Top Navbar -->
                
                <!-- / Top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Breadcrumbs -->
                       

                        <!-- booking off canvas -->
                        <!-- add booking -->
                        

                        <div id="getBookingAlert"></div>

<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
// ini_set('log_errors', 1); // Uncomment this line and set a writable path if you have access
// ini_set('error_log', '/path/to/your/custom_error.log'); // Set a writable path here

include '../inc/conn.php';

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed in groups.php: " . ($conn->connect_error ?? "Connection object not set. Check ../inc/conn.php path and content."));
}

if(isset($_POST['add'])){
	


	
		 $firstName=  mysqli_real_escape_string($conn,$_POST['firstName']);
		 $firstName= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
		 $firstName= stripslashes($firstName);
		 
		 $comment=  mysqli_real_escape_string($conn,$_POST['comment']);
		 $comment= htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
		 $comment= stripslashes($comment);
		 
		 
		 $time = time();

		
$sql = "SELECT * FROM `groups` WHERE name='$firstName'";
$result = $conn->query($sql);

if ($result === FALSE) { // Check if the SELECT query itself failed
    echo "Error checking existing group: " . $conn->error . "<br>";
} elseif ($result->num_rows == 0) {

	 $sql = "INSERT INTO `groups` (`group_id`, `name`, `created_at`, `comment`) VALUES (NULL, '$firstName', NOW(), '$comment');";
if ($conn->query($sql) === TRUE) {
 echo "<script>alert('Group Added')</script>";
} else {
  echo "Error inserting group: " . $sql . "<br>" . $conn->error;
}
}else{
     echo "<script>alert('Group Already exist')</script>";
}

}


?>
                        <!-- Booking List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                  <form method="POST">         <h5 class="mb-0">Create Groups</h5>
                                      
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">                                           
                                                 <div class="card-body">
                                                                <div class="col-md-12">
                                                            
                                                                    <input type="hidden" name="guest_id" id="guestID">
																 <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="firstName" class="form-label">Group Name<span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" id="firstName" name="firstName"  placeholder="Group name" required>
                                                                            </div>
                                                                        </div>
                                                                   
                                                                    </div>
                                                                   
																	  <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="mb-3">
                                                                                <label for="bookingComment" class="form-label">Comment</label>
                                                                                <textarea class="form-control" name="comment" rows="3" placeholder="Add Comment"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
																	

                                                                    <div class="required-fields">
                                                                        <p><span class="text-danger">*</span> Required fields</p>
                                                                    </div>
                                                                </div>
																
																  <button  name="add" class="btn btn-outline-primary" value="Create">Create</button>
                                            
                                        
																
																</form>
                                                            </div>
                                                            
                                                            
                                                            <h4>All Groups</h4>
                                                            
                                                            <table class="table table-bordered">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Name</th>
                                                                <th>Comment</th>
                                                                <th>Date Created</th>
                                                                <th>Number of People</th>
                                                                <th>Action</th>
                                                            </tr>
 <?php $sql = "SELECT * FROM `groups`";
$result = $conn->query($sql);
  $no =0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
      $no = $no + 1;
      $group_id = $row['group_id'];
        
      // Query to get the number of people in the group
      $people_sql = "SELECT SUM(COALESCE(num_adults, 0)) as total_adults, SUM(COALESCE(num_children, 0)) as total_children FROM tbl_acc_booking WHERE group_id = $group_id";
      $people_result = $conn->query($people_sql);
      $total_people = 0;
      if ($people_result) {
          $people_row = $people_result->fetch_assoc();
          $total_adults = $people_row['total_adults'] ?? 0;
          $total_children = $people_row['total_children'] ?? 0;
          $total_people = $total_adults + $total_children;
      }

?>


<tr>
    <td><?php echo  $no ?></td>
 <td><?php echo  $row['name'] ?></td>
  <td><?php echo  $row['comment'] ?></td>
   <td><?php echo  $row['created_at'] ?></td>
   <td><?php echo $total_people ?></td>
   
    <td><a href="?resto=group_bookings&&id=<?php echo $row['group_id']?>">View Bookings</a></td>
</tr>
<?php
  }
}
	?>					</table>				  
                                        </div>
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

    <!-- modals -->
    <!-- change price modal -->
    <div class="modal fade" id="changePriceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookingLabel">Change Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" class="form-control" id="changed-price">
                </div>
                <button class="btn btn-primary" id="save-price-btn">Change</button>
            </div>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>

    <!-- Footer -->
   
    <script src="js/room_booking_list.js"></script>
    <script src="js/room_booking_custom.js"></script>
    <!-- <script>src="js/handle_bookings.js"</script> -->
    <script>
        // toggle passport / id field
        $(document).ready(function() {
            $("#toggleSwitch").change(function() {
                if ($(this).is(":checked")) {
                    $("#identificationField").hide(); // Hide Identification Field
                    $("#passportField").show(); // Show Passport Fields
                } else {
                    $("#passportField").hide(); // Hide Passport Fields
                    $("#identificationField").show(); // Show Identification Field
                }
            });
        });
    </script>
</body>

</html>
