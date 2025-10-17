




<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template-free">
<!-- Header -->

<!-- / Header -->





<?php if(isset($_POST['addpayment'])){
	
	
	
		 
		 $amount=  mysqli_real_escape_string($conn,$_POST['amount']);
		 $amount= htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
		 $amount= stripslashes($amount);
		 
		 $date = time();
		 
		 
		 $method=  mysqli_real_escape_string($conn,$_POST['method']);
		 $method= htmlspecialchars($method, ENT_QUOTES, 'UTF-8');
		 $method= stripslashes($method);
		 
		 $booking_id = $_SESSION['booking_id'];
		 
		
		 
		 
		 
$sql = "INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `payment_time`, `method`) VALUES (NULL, '$booking_id', '$amount', ' $date', '$method');";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Payment Added')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
	
}
?>








<?php if(isset($_POST['add'])){
	
	
	
		 
		 $fullname=  mysqli_real_escape_string($conn,$_POST['fullname']);
		 $fullname= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
		 $fullname= stripslashes($fullname);
		 
		 
		 
	$booking_id = $_SESSION['booking_id'];
		 
		 $qty=  mysqli_real_escape_string($conn,$_POST['qty']);
		 $qty= htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
		 $qty= stripslashes($qty);
		 
		 
		 $exchange=  mysqli_real_escape_string($conn,$_POST['exchange']);
		 $exchange= htmlspecialchars($exchange, ENT_QUOTES, 'UTF-8');
		 $exchange= stripslashes($exchange);
		 
		 
$sql = "INSERT INTO `orders` (`order_id`, `name`, `price`, `booking_id`,qty) VALUES (NULL, '$fullname', '$exchange', '$booking_id',' $qty');";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('created')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
	
}
?>


<!-- styles -->
<style>
    .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 3px !important;
    }

    .booking-header {
        margin-bottom: 20px;
    }

    .balance-box {
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        background-color: #fff;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .status-paid {
        color: #28a745;
        font-weight: bold;
    }

    .status-unpaid {
        color: #dc3545;
        font-weight: bold;
    }

    .nav-align-top .nav-tabs~.tab-content {
        box-shadow: none !important;
    }

    .nav-align-top>.tab-content {
        box-shadow: none;
    }

    .bg-default {
        background-color: #f5f5f9;
        color: #697a8d;
    }

    /* balance card */

    /* .balance-card {
        border: 1px solid ;
        padding: 20px;
        border-radius: 5px;
    } */

    .balance-card h2 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .balance-card p {
        margin: 0;
    }

    .balance-card .row {
        margin-bottom: 10px;
    }

    .balance-card .row .col-4 {
        text-align: center;
    }

    .balance-badge {
        border: 1px solid #F2A341;
    }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
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
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <h3>Checkout</h3>
                               
								
											<form method="post">
<input type="text" name="username" required class="form-control" name="user" placeholder="Guest Phone number">
<br>
<input type="submit" name="checkout" class="form-control" value="Check">
</form>
				<br>	
                            </div>
							
							
							<?php if(isset($_POST['checkout'])  || $_SESSION['room_search']!=null)  { 
							if( $_SESSION['room_search']){
								$name = $_SESSION['room_search'];
								
								if($_POST['username']!=''){
									$name = $_POST['username'];
									$_SESSION['room_search'] = $name;	
								}
								
							}else{
							 $name = $_POST['username'];
							 $_SESSION['room_search'] = $name;							 
							}
							 $sql = "SELECT * FROM tbl_acc_guest where phone_number='$name'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
 $guest_id = $row['id']; 
}}

?>
							
							
						
							
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" id="bookingStatus">Default</button>
                                    <ul class="dropdown-menu">
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Pending</a> </li>
                                        <li> <a class="dropdown-item" href="confirm.php?id=<?php echo $_REQUEST['booking_id']?>"> Confirmed</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Cancelled</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">No-Show</a> </li>
                                    </ul>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
                                    <ul class="dropdown-menu">
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Edit</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Move Booking</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Add Payment</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Add Order</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Create Invoice</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Split Payment</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Add Guest</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Add Reservation to Group</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Send Email</a> </li>
                                        <li> <a class="dropdown-item" href="javascript:void(0);">Delete</a> </li>                            
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div id="getBookingAlert"></div>

                        <div class="nav-align-top">
                            <ul class="nav nav-tabs justify-content-center" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">General</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false">Activites</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-messages" aria-controls="navs-top-messages" aria-selected="false">Payments</button>
                                </li>
								
								   <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-invoice" aria-controls="navs-top-messages" aria-selected="false">Invoice</button>
                                </li>
								
                            </ul>
                            <div class="tab-content" style="background-color: #f5f5f9; border: none">
                                <div class="tab-pane fade show active" id="navs-top-home" role="tabpanel">
                                    <div class="container">

                                        <!-- General and Balance -->
                                        <div class="row">
                                            <!-- General Info -->
 


<?php $sql = "SELECT * FROM tbl_acc_booking where guest_id='$guest_id' and payment_status_id !=2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
 $guest_id = $row['guest_id'];
  $booking_id = $row['id'];
  
  $_SESSION['booking_id'] = $booking_id;
  
 $booking_amount = $row['booking_amount'];
 
}}else{
//echo "dd".$guest_id;
}?>


<?php if($guest_id){ ?>





<?php 


$amountToPay = 0;
$sql = "SELECT * FROM orders where booking_id='$booking_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $amountToPay = $amountToPay + $row['price'];


}}

 $amountToPay =  $amountToPay +  $booking_amount;
$paidAmount = 0;
$sql = "SELECT * FROM payments where booking_id='$booking_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $paidAmount = $paidAmount + $row['amount'];
 
}}?>




 
<?php $sql = "SELECT * FROM tbl_acc_guest where id='$guest_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>


										  <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">GUEST</div>
                                                    <div class="card-body">
                                                        <p><strong>Guest:</strong> <?php echo $row['first_name']." ".$row['last_name']?></p>
                                                        <p><strong>Phone:</strong> <?php echo $row['phone_number'] ?></p>
                                                        <p><strong>Email:</strong> <?php echo $row['email_address'] ?></p>
                                                        <p><strong>Address:</strong> <?php echo $row['address'] ?></p>
                                                        <p><strong>Date of Birth:</strong> <?php echo $row['date_of_birth'] ?></p>
                                                        <p><strong>Place of Birth:</strong> <?php echo $row['place_of_birth'] ?></p>
                                                        <p><strong>Nationality:</strong> <?php echo $row['nationality'] ?></p>
                                                        <p><strong>Residence:</strong> <?php echo $row['residence'] ?></p>
                                                        <p><strong>Passport:</strong> <?php echo $row['passport_number'] ?></p>
                                                        <p><strong>Expiration Date:</strong> <?php echo $row['passport_expiration_date'] ?></p>
                                                        <p><strong>Profession:</strong> <?php echo $row['profession'] ?></p>
                                                    </div>
                                                </div>
                                            </div>
<?php }}?>


<?php $sql = "SELECT * FROM tbl_acc_booking where id='$booking_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>

                                            <!-- Balance Info -->
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="row">
                                                            <div class="col-md-7">DATES OF STAY</div>
                                                            <div class="col-md-5 text-end">
                                                                <span class="badge bg-default p-3">Room: <?php echo getRoomName(getBookedRoom($row['id'])) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Check-in:</strong> <?php echo $row['checkin_date'];?></p>
                                                        <hr>
                                                        <p><strong>Check-out:</strong> <?php echo $row['checkout_date'];?></p>
                                                        <hr>
                                                        <p><strong>Nights:</strong> <?php echo $row['duration'];?></p>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <strong>Source:</strong> <?php echo $row['booked_from'];?>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Company:</strong> <?php echo $row['company'];?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="badge bg-default p-3 w-100 text-start"><strong>Coming From:</strong> <?php echo $row['coming_from'];?></p>
                                                        <p class="badge bg-default p-3 w-100 text-start"><strong>Going To:</strong> <?php echo $row['going_to'];?></p>
                                                    </div>
                                                </div>
                                            </div>
<?php }} ?>
                                            <!-- Comments and Notes -->
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        BALANCE
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <p class="badge bg-default p-2 text-start">
                                                                    <span>Amount</span><br><br>
                                                                    <strong class="fs-6"><?php echo number_format($amountToPay)?>  RWF</strong>
                                                                </p>
                                                            </div>
                                                            <div class="col-12">
                                                                <p class="badge bg-default p-2 text-start">
                                                                    <span>Paid</span><br><br>
                                                                    <strong class="fs-6"><?php echo number_format($paidAmount)?> RWF</strong>
                                                                </p>
                                                            </div>
                                                            <div class="col-12">
                                                                <p class="badge bg-default p-2 text-start balance-badge">
                                                                    <span>Balance</span><br><br>
                                                                    <strong class="fs-6"><?php echo number_format($amountToPay -$paidAmount); ?> RWF</strong>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="row" hidden>
                                                            <h6>Included (Addons)</h6>
                                                            <div class="col-6">
                                                                <div class="badge bg-default p-2 mb-3 w-100">
                                                                    <div class="row">
                                                                        <div class="col-md-6 text-start">
                                                                            Vat(18%) Tax
                                                                        </div>
                                                                        <div class="col-md-6 text-end">
                                                                            <strong>9,000RWF</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="badge bg-default p-2 mb-3 w-100">
                                                                    <div class="row">
                                                                        <div class="col-md-6 text-start">
                                                                            Laundry
                                                                        </div>
                                                                        <div class="col-md-6 text-end">
                                                                            <strong>0 RWF</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- preferred payment method -->
                                                        <h6 hidden>PREFERRED PAYMENT METHOD</h6>
                                                    </div>
                                                </div>
                                                <div class="card mt-2">
                                                    <div class="card-header">
                                                        COMMENTS AND NOTES
                                                        <button type="button" class="btn btn-sm btn-primary float-end">
                                                            <i class="fa fa-plus"></i> Save
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <textarea class="form-control" rows="2" placeholder="Add comment..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
<?php $sql = "SELECT * FROM tbl_acc_booking where id='$booking_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>
                                        <!-- Accommodation and Orders -->
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-header">Accommodation</div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Dates</th>
                                                                        <th>Adults</th>
                                                                        <th>Children</th>
                                                                        <th>Room Type</th>
                                                                        <th>Room</th>
                                                                        <th>Features</th>
                                                                        <th>Cancellation Policy</th>
                                                                        <th>Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><?php echo $row['checkin_date'];?> - <?php echo $row['checkout_date'];?> </td>
                                                                        <td><?php echo $row['num_adults'];?></td>
                                                                        <td><?php echo $row['num_children'];?></td>
                                                                      <td><?php echo getBookedRoom($row['id'])?></td>
                                                                        <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                                                                        <td>Free WIFI</td>
                                                                        <td>For Reception</td>
                                                                        <td><?php echo number_format($row['booking_amount']);?> RWF</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
<?php }} ?>
                                        <!-- Orders -->
                                        <div class="row mt-4">
                                            <div class="col-md-12" hidden>
                                                <div class="card">
                                                    <div class="card-header">Orders</div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Quantity</th>
                                                                        <th>Price</th>
                                                                        <th>Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Pizza Amici</td>
                                                                        <td>1</td>
                                                                        <td>12,000 RWF</td>
                                                                        <td>12,000 RWF</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Brownie</td>
                                                                        <td>1</td>
                                                                        <td>8,000 RWF</td>
                                                                        <td>8,000 RWF</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Cola</td>
                                                                        <td>1</td>
                                                                        <td>2,000 RWF</td>
                                                                        <td>2,000 RWF</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Cookie Plate</td>
                                                                        <td>1</td>
                                                                        <td>5,000 RWF</td>
                                                                        <td>5,000 RWF</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payments and Invoices -->
                                        <div class="row mt-4">
                                       

                                       
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                                       <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Booking Orders and services</h5>
                                        <button type="button" id="addVenueModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVenueModal">
                                            <i class="bx bx-plus"></i> New Order
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->
                                       
                                       <table  class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>QTY</th>
                                                    <th>Price</th>
                                                   
                                                  
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
											<?php 
											
											$sum = 0;
											$sql = "SELECT * FROM orders WHERE booking_id='$booking_id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>
	
	<tr>
                                                    <td><?php echo $sum = $sum + 1;?></td>
                                                    <td><?php echo $row['name'];?></td>
													 <td><?php echo $row['qty'];?></td>
                                                    <td><?php echo $row['price'];?></td>
                                                    
                                                
                                                  
                                                    <th><a href="delete.php?id=<?php echo $row['order_id']?>">Delete</a></th>
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
                                <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">
                                
								
								
								             <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Payments</h5>
                                        <button type="button" id="addpayment" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addpaymentModal">
                                            <i class="bx bx-plus"></i> New Payment
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->
                                       
                                       <table  class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                 
                                                    <th>Method</th>
                                                    <th>Date</th>
                                                   
                                                  
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
											<?php 
											
											$sum = 0;
											$sql = "SELECT * FROM payments WHERE booking_id='$booking_id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>
	
	<tr>
                                                    <td><?php echo $sum = $sum + 1;?></td>
                                                  
											
                                                    <td><?php echo number_format($row['amount']);?> RWF</td>
                                                    <td><?php echo $row['method'];?></td>
                                                      <td><?php echo date('d/M/Y',$row['payment_time']);?></td>
                                                
                                                  
                                                    <th><a href="delete.php?id=<?php echo $row['payment_id']?>">Delete</a></th>
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
								
								
								
								
								
								
								
								
								<div class="tab-pane fade" id="navs-top-invoice" role="tabpanel">
                                
								
								
								             <div class="row">
											 <div class="col-md-4">
											 </div>
                            <div class="col-md-4">
							</br>
							<a href="print_invoice.php?id=<?php echo $booking_id?>" class="btn btn-info">Confirm Payment & Print Invoice</A>
							<br>	<br>
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                     

                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->

<?php 



?>
									    <div class="col-md-12">
										
										<center><img src="../../assets/img/logo/logo_sample.png" style="width:60px">
										</center>
										<br>
										 <h5 class="mb-0">  <center>Invoice</center></h5>
				
										<center>
										
										<P>St Paul
										<br>Kigali Rwanda
										<br>+250 78 12 000 00
										<br>wwww.yourwebsite.com</p>
										</center>
										------------------------------------------
										
										
											<?php $sql = "SELECT * FROM tbl_acc_guest where id='$guest_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>


								
                                                        <p>Guest:<br>Names: <?php echo $row['first_name']." ".$row['last_name']?><br>
                                                        Phone: <?php echo $row['phone_number'] ?></p>
                                                    
                                               
<?php }}?>
										-------------------------------------------
										<div class="row">
										<div class="col-md-6">
										<p>Accommodation</p>
										</div>
										
											<div class="col-md-6">
										<p class="pull-end"><?php echo number_format($booking_amount)?> RWF</p>
										</div>
										</div>
										
<?php 
$no = 0;
$sql = "SELECT * FROM orders WHERE booking_id='$booking_id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
	  $no = $no + $row['price'];
	?>
											<div class="row">
										<div class="col-md-6">
										<p><?php echo $row['name']?></p>
										</div>
										
									   <div class="col-md-6">
										<p class="pull-end"><?php echo number_format($row['price'])?> RWF</p>
										</div>
										</div>
										
<?php }}?>
										
						-------------------------------------------		
						
						
										<div class="row">
										<div class="col-md-6">
										<p>VAT (18 %)</p>
										</div>
										
										<div class="col-md-6">
										<p class="pull-end"><?php 

$vat = $booking_amount + $no;
										echo number_format($vat  * 18/100)?> RWF</p>
										</div>
										</div>
						

		                                <div class="row">
										<div class="col-md-6">
										<p>TOTAL</p>
										</div>
										
										<div class="col-md-6">
										<p><?php echo number_format($no + $booking_amount);?> RWF</p>
										</div>
										</div>
										
										
							
										

-------------------------------------------		

<p>Invoice No: <?php echo $booking_id?></p>
<p>Date: <?php echo date("d-m-Y H:I")?></p>

-------------------------------------------	
<br>
<center><p>Thank You for using our services</p></center>
					
										</div>
										
									   
									   
									  
									   
                                       

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
								
								
									<?php } ?>	
								
							<?php } ?>			
                            </div>
                        </div>

                    </div>
                    <!-- / Content -->
                </div>
            </div>
        </div>
    </div>


		  <div class="modal fade" id="addVenueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                 
						     <div class="mb-3">
                            <label class="form-label" for="venue_name">Order</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="fullname"
                                    placeholder="Cola"
                                
                                    required />
                            </div>
                        </div>
						
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Quantity</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="qty"
                                    placeholder="1"
                                
                                    required />
                            </div>
                        </div>
						
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Price</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="exchange"
                                    placeholder="123"
                                
                                    required />
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
                            <label class="form-label" for="venue_name">Payment</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                              name="amount"
                                    placeholder="Amount"
                                
                                    required />
                            </div>
                        </div>
						
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Payment method</label>
                            <div class="input-group input-group-merge">
                                <select
                                    class="form-control"
                                   name="method"
                                    required>
									
									<option value='cash'>Cash</option>
									<option value='card'>Card</option>
									<option value='momo'>MTN Mobile Money</option>
									<option value='airtelmoney'>Airtel  Money</option>
									<option value='Credit'>Credit</option>

                                   
                                </select>
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
   
    <script src="js/room_booking_details.js"></script>
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