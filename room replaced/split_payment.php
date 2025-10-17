
<style>
    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .table td {
        vertical-align: middle;
        /* Vertically align content within cells */
    }

    .top-section {
        position: relative;
    }

    .room-params {
        position: relative;
        z-index: 1;
    }

    hr {
        width: 100%;
        border-top: 2px solid #ccc;
        margin-top: 30px;
    }

    .custom-tabs-container {
        border-bottom: 1px solid #e5e5e5;
    }

    .custom-tabs .nav-link {
        border: none;
        background: none;
        color: #6c757d;
        /* Gray for inactive tabs */
        font-weight: bold;
        text-transform: uppercase;
        font-size: 14px;
        padding: 10px 20px;
        position: relative;
        transition: color 0.3s ease;
    }

    .custom-tabs .nav-link:hover {
        color: #0d6efd;
        /* Highlight on hover */
    }

    .custom-tabs .nav-link.active {
        color: black;
        /* Active tab color */
        font-weight: bold;
    }

    .custom-tabs .nav-link.active::after {
        content: '';
        display: block;
        width: 100%;
        height: 3px;
        background-color: #0F7CBF;
        /* Blue underline */
        position: absolute;
        bottom: 0;
        left: 0;
    }

    .nav-tabs .nav-item .nav-link:not(.active) {
        /* remove background color */
        background-color: transparent;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 54px;
        height: 28px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        border-radius: 34px;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #F2A341;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    /* clear span */
    .clear-span {
        cursor: pointer;
        /* Makes the cursor change to a pointer */
        color: #dc3545;
        /* Text color */
        padding: 5px 10px;
        /* Padding to give it some clickable area */
        border: 1px solid transparent;
        /* Border for visual feedback */
        border-radius: 5px;
        /* Rounded corners */
        transition: background-color 0.3s ease, border-color 0.3s ease;
        /* Smooth transition for effects */
    }

    .clear-span:hover {
        background-color: rgba(220, 53, 69, 0.1);
        /* Light background color on hover */
        border-color: #dc3545;
        /* Border color on hover */
    }

    /* validations */
    .is-invalid {
        border-color: #dc3545;
        background-color: #f8d7da;
    }

    .is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        color: #dc3545;
        display: block;
    }

    .is-invalid~.form-check-label {
        color: #dc3545;
    }

    /* customize the .current-transaction-tab to fit the media query for the small size screens */
    @media (max-width: 717px) {
        .current-transaction-tab {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 10px;
            font-size: 12px;
            background-color: #e7f3ff;
            /* Adjust background color as needed */
        }

        .current-transaction-tab span {
            display: block;
            width: 100%;
            text-align: center;
        }
    }
</style>
<!-- Layout wrapper -->
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

                        <!-- Booking List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
									
                                    <form method="POST"> 
									<h5 class="mb-0">Split Payment</h5>
									
									<br>
									<button type="button" id="addpayment" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addpaymentModal">
                                            <i class="bx bx-plus"></i> New Split Payment
                                        </button>
									
									
                                      
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">                                           
                                                 <div class="card-body">
                                                                <div class="col-md-12">
                                                       
<?php 



 if(isset($_POST['addpayment'])){
	
	
		 
		 $amount=  mysqli_real_escape_string($conn,$_POST['amount']);
		 $amount= htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
		 $amount= stripslashes($amount);
		 
		 $date = time();

		 $guest_id=  mysqli_real_escape_string($conn,$_POST['guest_id']);
		 $guest_id= htmlspecialchars($guest_id, ENT_QUOTES, 'UTF-8');
		 $guest_id= stripslashes($guest_id);
		 
	
		
		 
$sql = "INSERT INTO `split_payments` (`split_id`, `booking_id`, `guest_id`, `paid_amount`, `created_at`) VALUES (NULL, '".$_REQUEST['booking']."', '$guest_id', '$amount', '$date');";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Payment Added')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
	
}








 $sql = "SELECT * FROM tbl_acc_booking where id='".$_REQUEST['booking']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
 $guest_id = $row['guest_id'];
 $booking_amount = $row['booking_amount'];
 
}}

$amountToPay = 0;
$sql = "SELECT * FROM orders where booking_id='".$_REQUEST['booking']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $amountToPay = $amountToPay + $row['price'];


}}

 $amountToPay =  $amountToPay +  $booking_amount;
$paidAmount = 0;
$sql = "SELECT * FROM payments where booking_id='".$_REQUEST['booking']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $paidAmount = $paidAmount + $row['amount'];
 
}}


$sql = "SELECT * FROM booking_guest where book_id='".$_REQUEST['booking']."'";
$result = $conn->query($sql);
$no_of_booking= $result->num_rows + 1;

$total = $amountToPay - $paidAmount;
//$total = $total  / $no_of_booking;






?>

									













                  <table  class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Names</th>
                                                    <th>To Pay</th>
                                                </tr>
                                            </thead>
                                            <tbody>
<?php 


$sql = $db->prepare("SELECT * FROM tbl_acc_booking where id='".$_REQUEST['booking']."'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
 $guest_id = $row['guest_id'];
}

?>

<?php 
$c = 0;

$sql = $db->prepare("SELECT * FROM split_payments where booking_id='".$_REQUEST['booking']."'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){?>

									


	<tr>
                                                    <td>#<?php echo $c = $c +1;?></td>
                                                    <td><?php 	     echo getGuestNames($row['guest_id']); ?></td>
                                                      <td><?php 	  echo number_format($row['paid_amount']); ?> RWF</td>
                                                      <td><a href="edit_slipt.php?id=<?php echo $row['split_id']?>&&back=<?php echo $_REQUEST['booking']?>">Edit</a></td>
                                                 
                                                    
                                                
                                                  
                                                   
                                                </tr>
<?php }?>



									
                                                  
                                            </tbody>
                                        </table>

										   

                                        
														
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



		<div class="modal fade" id="addpaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
					
					
					
						       <div class="mb-3">
                            <label class="form-label" for="venue_type">Select Guest</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_type_label" class="input-group-text"></i></span>
                                <select
                                    class="form-control"
                                   name="guest_id"
                                    required>
									


	<option value='<?php echo $guest_id ?>'><?php 	    echo getGuestNames($guest_id); ?></option>								
<?php 


$sql = $db->prepare("SELECT * FROM booking_guest where book_id='".$_REQUEST['booking']."'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){?>
<option value='<?php echo $row['guest_id'] ?>'><?php 	    echo getGuestNames($row['guest_id']); ?></option>
	
<?php	
	
  
}?>


                                   
                                </select>
                            </div>
                        </div>
					
					
					
                 
						<div class="mb-3">
                            <label class="form-label" for="venue_name">Payment</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="number"
                                    class="form-control"
                              name="amount"
                                    placeholder="Amount"
                                   value="<?php echo $total ?>"
                                    required 
									max="<?php echo $total?>"/>
                            </div>
                        </div>
						
						
					
						
						
							
					
						
				
						
					
			
                 
                   

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <input  type="submit" class="btn btn-primary" name="addpayment" value="Create">
                        </div>
                    </form>
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