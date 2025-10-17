





<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template-free">
<!-- Header -->

<?php if(isset($_POST['addpayment'])){
	
		 

		 $re_id = $_SESSION['booking_id'];
		 
		 $amount=  mysqli_real_escape_string($conn,$_POST['amount']);
		 $amount= htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
		 $amount= stripslashes($amount);
		 
		 $date = time();

		 $method=  mysqli_real_escape_string($conn,$_POST['method']);
		 $method= htmlspecialchars($method, ENT_QUOTES, 'UTF-8');
		 $method= stripslashes($method);
		 
		 
		 $remark=  mysqli_real_escape_string($conn,$_POST['remark']);
		 $remark= htmlspecialchars($remark, ENT_QUOTES, 'UTF-8');
		 $remark= stripslashes($remark);
		 
		  $currency=  mysqli_real_escape_string($conn,$_POST['currency']);
		 $currency= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8');
		 $currency= stripslashes($currency);
		
		 $rate = getCurrencyValue($currency);
		 
		 $amount = $amount * $rate;
		 
		 
$sql = "INSERT INTO `venue_payments` (`payment_id`, `booking_id`, `amount`, `payment_time`, `method`,remark,currency,rate) VALUES (NULL, '$re_id', '$amount', ' $date', '$method','$remark','$currency','$rate');";

if ($conn->query($sql) === TRUE) {
 // echo "<script>alert('Payment Added')</script>";
  echo "<script>window.location='mark_as_venue_paid.php?id=$re_id'</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
	
}
?>








<?php if(isset($_POST['add'])){
	
	
	
		 
		  $service=  mysqli_real_escape_string($conn,$_POST['service']);
		 $service= htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
		 $service= stripslashes($service);
		 

		 
		 $qty=  mysqli_real_escape_string($conn,$_POST['qty']);
		 $qty= htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
		 $qty= stripslashes($qty);
		  $re_id = $_SESSION['booking_id'];

		 
		$price = getServicePrice($service);
        $total =  $price * $qty;
		 
		 
$sql = "INSERT INTO `venu_orders` (`order_id`, `names`, `price`, `booking_id`,qty,total) VALUES (NULL, '$service', '$price', '$re_id',' $qty','$total');";

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
            <?php include_once "../../partials/side_nav.php"; ?>
            <!-- / Side Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Top Navbar -->
                <?php include_once "../../partials/top_nav.php"; ?>
                <!-- / Top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Breadcrumbs -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <h3>Report</h3>
                               
								

				<br>	
                            </div>
							
							<?php
							
							


$sql = "SELECT * FROM tbl_ev_venue_reservations where id='".$_REQUEST['booking_id']."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
  $venue_id = $row['venue_id']; 
  $re_id = $row['id']; 
  $payment_status = $row['status']; 
  $customer_id = $row['customer_id'];
  $_SESSION['booking_id'] = $re_id;
  
}}


$sql = "SELECT * FROM tbl_ev_venue_rates where venue_id='$venue_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
  $booking_amount = $row['amount']; 
}}




?>
							
							
						
							
                            <div class="col-md-6 text-end">
                           
                            
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
 





<?php if($customer_id){ ?>





<?php 


$amountToPay = 0;
$sql = "SELECT * FROM venu_orders where booking_id='$re_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $amountToPay = $amountToPay + $row['price'];


}}

$amountToPay =  $amountToPay +  $booking_amount;
$paidAmount = 0;
$sql = "SELECT * FROM venue_payments where booking_id='$re_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $paidAmount = $paidAmount + $row['amount'];
 
}}



$due_amount =$amountToPay -$paidAmount;
?>




 
<?php $sql = "SELECT * FROM tbl_ev_customers where id='$customer_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>


										  <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">GUEST</div>
                                                    <div class="card-body">
                                                        <p><strong>Guest:</strong> <?php echo $row['names']?></p>
                                                        <p><strong>Phone:</strong> <?php echo $row['phone'] ?></p>
                                                        <p><strong>Email:</strong> <?php echo $row['email'] ?></p>
                                                       
                                                     
                                                        <p><strong>Identification:</strong> <?php echo $row['identification'] ?></p>
                                                        <p><strong>TIN Number:</strong> <?php echo $row['tin'] ?></p>
                                                       
                                                    </div>
                                                </div>
                                            </div>
<?php }}?>


<?php $sql = "SELECT * FROM tbl_ev_venue_reservations where id='$re_id'";
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
                                                            <div class="col-md-7">Reservations info</div>
                                                      
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
													 <p><strong>Reserved</strong> <?php echo getVenueName($row['venue_id'])?></p>
													
                                                        <p><strong>Date</strong> <?php echo $row['reservation_date'];?></p>
                                                        <hr>
                                                        <p><strong>Time:</strong> <?php echo $row['start_time'];?> -  <?php echo $row['end_time'];?></p>
                                                        <hr>
                                                       
                                                       
                                                      
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
                                            
                                            </div>
                                        </div>

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
                                        <h5 class="mb-0">Additianal services / Orders</h5>
                                      
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
                                                    <th>Total</th>
                                                   
                                                  
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
											<?php 
											
											$sum = 0;
											$total = 0;
											$sql = "SELECT * FROM venu_orders WHERE booking_id='$re_id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  	$total = 	$total  + $row['total'];
	 
	?>
	
	<tr>
                                                    <td><?php echo $sum = $sum + 1;?></td>
                                                    <td><?php echo getServiceName($row['names']);?></td>
													 <td><?php echo $row['qty'];?></td>
                                                    <td><?php echo  number_format($row['price']);?></td>
													<td><?php echo  number_format($row['total']);?></td>
                                                    
                                                
                                                  
                                                    <th><a href="delete.php?id=<?php echo $row['order_id']?>">Delete</a></th>
                                                </tr>
	
<?php	
	
  }
}
	?>										
											
             	<tr>
                                                    <td>Grand Total:</td>
                                                    <td></td>
													 <td></td>
                                                    <td></td>
                                                    <td></td>
													<td><?php echo number_format($total);?> RWF</td>
                                                    
                                                
                                                  
                                                   
                                                </tr>                                     
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
											$sql = "SELECT * FROM venue_payments WHERE booking_id='$re_id' ";
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
						
													
  <?php if($due_amount==0)
  {?>  <a href="mark_as_venue_paid.php?id=<?php echo $re_id?>" class="btn btn-info" >
                                            <i class="bx bx-check"></i> Mark as paid
                                        </a>
									  <?php }else{
										  ?>
										  <a href="" class="btn btn-danger" >
                                           Unpaid Payments
                                        </a><?php
									  } ?>
							
							
							 <?php if($payment_status=='Confirmed' && $due_amount==0)
  {?>  <button onclick="printInvoice()" class="btn btn-info" >
                                            <i class="bx bx-check"></i> Print Invoice
                                        </button>
									  <?php } ?>
							
							
							 
							
							
							<br>	<br>
						
						
                                <div class="card mb-4" id="invoice">
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
										
										
											<?php $sql = "SELECT * FROM tbl_ev_customers where id='$customer_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>


								
                                                        <p>Guest:<br>Names: <?php echo $row['names']?><br>
                                                        Phone: <?php echo $row['phone'] ?></p>
                                                    
                                               
<?php }}?>
										-------------------------------------------
										<div class="row">
										<div class="col-md-6">
										<p>Venue rent</p>
										</div>
										
											<div class="col-md-6">
										<p class="pull-end"><?php echo number_format($booking_amount)?> RWF</p>
										</div>
										</div>
										
<?php 
$no = 0;
$sql = "SELECT * FROM venu_orders WHERE booking_id='$re_id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
	  $no = $no + $row['price'];
	?>
											<div class="row">
										<div class="col-md-6">
										<p><?php echo $row['names']?></p>
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

<p>Invoice No: <?php echo $re_id?></p>
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
                            <label class="form-label" for="venue_type">Select service/Item</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_type_label" class="input-group-text"></i></span>
                                <select
                                   class="form-control"
                                   name="service"
                                    required>
									
									
<?php $sql = "SELECT * FROM  services";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
?><option value='<?php echo $row['service_id']?>'><?php echo $row['service_name']?> - <?php echo number_format($row['service_price'])?> RWF</option><?php
  }
}
?>
                                   
                                </select>
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
                                    type="number"
                                    class="form-control"
                              name="amount"
                                    placeholder="Amount"
                                   value="<?php echo $due_amount ?>"
                                    required 
									max="<?php echo $due_amount?>"/>
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
						
						
									             <div class="mb-3">
                            <label class="form-label" for="venue_type">Currency</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_type_label" class="input-group-text"></i></span>
                                <select
                                    class="form-control"
                                   name="currency"
                                    required>
									
									
<?php $sql = "SELECT * FROM currencies";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
?><option value='<?php echo $row['currency_id']?>'><?php echo $row['name']?> - (Rate: <?php echo $row['currency_exchange']?>)</option><?php
  }
}
?>
                                   
                                </select>
                            </div>
                        </div>
						
						
						
							    <div class="mb-3">
                            <label class="form-label" for="venue_name">Payment Remark</label>
                            <div class="input-group input-group-merge">
                                <select
                                    class="form-control"
                                   name="remark"
                                    required>
									
									<option value='Advance'>Advance payment</option>
									<option value='Partial'>Partial Payment</option>
									<option value='Full'>Full Payment</option>
									<option value='Credit'>On Credit</option>
								

                                   
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
		 <script> function printInvoice() { var printContents = document.getElementById('invoice').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>
	
  
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