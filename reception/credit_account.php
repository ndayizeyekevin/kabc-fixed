

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

if(isset($_POST['add'])){
    
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
    include '../inc/conn.php';
	
	
		 $firstName=  mysqli_real_escape_string($conn,$_POST['names']);
		 $firstName= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
		 $firstName= stripslashes($firstName);
		 
		 
		 $phone=  mysqli_real_escape_string($conn,$_POST['phone']);
		 $phone= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
		 $phone= stripslashes($phone);
		 
		 
		 $email=  mysqli_real_escape_string($conn,$_POST['email']);
		 $email= htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
		 $email= stripslashes($email);
		 
		 
		 

		 
		 $tinnumber=  mysqli_real_escape_string($conn,$_POST['tinnumber']);
		 $tinnumber= htmlspecialchars($tinnumber, ENT_QUOTES, 'UTF-8');
		 $tinnumber= stripslashes($tinnumber);
		 


if ($tinnumber !=""){	 
$sql = "SELECT * FROM creadit_id WHERE tinnumber ='$tinnumber'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
      echo "<script>alert('Tin Number already  EXIST')</script>";

}else{



$time = time();
$sql = "INSERT INTO `creadit_id` (`id`, `f_name`, `email`, `phone`, `tinnumber`, `created_by`, `created_at`)
VALUES (NULL, '$firstName', '$email', '$phone', '$tinnumber', '$time', '$time');";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Added')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
			 
}
	 
	
}else{
    
    
    $time = time();
$sql = "INSERT INTO `creadit_id` (`id`, `f_name`, `email`, `phone`, `tinnumber`, `created_by`, `created_at`)
VALUES (NULL, '$firstName', '$email', '$phone', '$tinnumber', '$time', '$time');";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Added')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
    
    
}

}


?>
                        <!-- Booking List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                  <form method="POST">         <h5 class="mb-0">Create credits Account</h5>
                                      
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">                                           
                                                 <div class="card-body">
                                                                <div class="col-md-12">
                                                                 
                                                                    <input type="hidden" name="guest_id" id="guestID">
                                                                 
																 <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="mb-3">
                                                                                <label for="firstName" class="form-label">Names<span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" id="names" name="names"  placeholder="Names" required>
                                                                            </div>
                                                                        </div>
                                                                     
                                                                    </div>
                                                         
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="phone" class="form-label">Phone<span class="text-danger">*</span></label>
                                                                                <input type="tel" class="form-control" id="phone"  name="phone"placeholder="+25078..." required>
                                                                            </div>
                                                                        </div>
                                                                      <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="email" class="form-label">Email</label>
                                                                                <input type="email" class="form-control" id="email"  name="email" placeholder="john@gmail.com" >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                              
                                                                    <div class="row">
                                                                       
                                                                        <div class="col-md-12">
                                                                            <div class="mb-3">
                                                                                <label for="profession" class="form-label">Tin Number</label>
                                                                                <input type="text" class="form-control" id="tinnumber" name="tinnumber" placeholder="Tin number">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                  
															
				
																	
													

                                                                    <div class="required-fields">
                                                                        <p><span class="text-danger">*</span> Required fields</p>
                                                                    </div>
                                                                </div>
																
																  <button type="submit"  name="add" class="btn btn-outline-primary" value="Create Account">Create Account</button>
                                            
                                        
																
																</form>
                                                            </div>
										  
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