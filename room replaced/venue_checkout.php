<?php
 include '../inc/conn.php';
 
 
 
if(isset($_POST['addpayment'])){
	

		 $re_id = $_REQUEST['booking_id'];
		 include '../inc/conn.php';
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




// Checkout Venue

if(isset($_GET['checkoutVenue']) && !empty($_GET['checkoutVenue'])){
    $venueBooking_id = $_GET['checkoutVenue'];
    
    $sql = "UPDATE `tbl_ev_venue_reservations` SET status = 'Checkedout' WHERE id = '$venueBooking_id'";
    
    if ($conn->query($sql) === TRUE) {
  echo "<script>window.location='index?resto=venue_booking_list'</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
}
?>








<?php if(isset($_POST['add'])){
		 ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

		 $qty=  mysqli_real_escape_string($conn,$_POST['qty']);
		 $qty= htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
		 $qty= stripslashes($qty);
		 
		 
		 $item=  mysqli_real_escape_string($conn,$_POST['item']);
		 $item= htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
		 $item= stripslashes($item);
		 

		 $id =$_REQUEST['booking_id'];
	
		 
		 
$sql = "INSERT INTO `venu_orders` (`order_id`, `booking_id`, `item`, `qty`, `price`, `created_at`) VALUES (NULL, '$id', '$item', '$qty', '0', CURRENT_TIMESTAMP);";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('created')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
	
}















if(isset($_POST['savepax'])){
	
			 ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	

		 $qty=  mysqli_real_escape_string($conn,$_POST['paxnumber']);
		 $qty= htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
		 $qty= stripslashes($qty);
		 
		 
		 

		 $id =$_REQUEST['booking_id'];
	
		 
		 
$sql = "UPDATE tbl_ev_venue_reservations SET pax ='$qty' where id='$id' ";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Added')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
	
}



if(isset($_POST['savenote'])){
		 $qty=  mysqli_real_escape_string($conn,$_POST['functionnotes']);
		 $qty= htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
		 $qty= stripslashes($qty);
		 $id =$_REQUEST['booking_id'];
	
		 
		 
$sql = "UPDATE tbl_ev_venue_reservations SET reservation_remarks ='$qty' where id='$id' ";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Added')</script>";
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
                        <div class="row mb-5">
  
					
							<?php 
	
							
 $re_id = $_REQUEST['booking_id'];
 $booking_amount=0;
							

$sql = $db->prepare("SELECT * FROM tbl_ev_venue_reservations where id='$re_id'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){

   $customer_id = $row['customer_id']; 
  $venue_id = $row['venue_id']; 
  $re_id = $row['id']; 
  $payment_status = $row['status']; 
}





$sql = $db->prepare("SELECT * FROM tbl_ev_venue_rates where venue_id='$venue_id'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
   $booking_amount = $row['amount']; 
}

//$booking_amount = $venue_id;


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
                                <li class="nav-item" >
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false">Function Sheet items</button>
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
$sql = "";
$sql = $db->prepare("SELECT * FROM venu_orders where booking_id='$re_id'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){

 
 //$amountToPay = $amountToPay + $row['price'] ;


}

$amountToPay =  $amountToPay +  $booking_amount;
$paidAmount = 0;
$sql = "";
$sql = $db->prepare("SELECT * FROM venue_payments where booking_id='$re_id'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){

 
 $paidAmount = $paidAmount + $row['amount'];
 
}



$due_amount =$amountToPay -$paidAmount;
?>




 
<?php $sql = "";
$sql = $db->prepare("SELECT * FROM tbl_ev_customers where id='$customer_id'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
	?>


										  <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">GUEST</div>
                                                    <div class="card-body">
                                                        <p><strong>Guest:</strong> <?php echo $name = $row['names']?></p>
                                                        <p><strong>Phone:</strong> <?php echo $phone = $row['phone'] ?></p>
                                                        <p><strong>Email:</strong> <?php echo $row['email'] ?></p>
                                                       
                                                     
                                                        <p><strong>Identification:</strong> <?php echo $row['identification'] ?></p>
                                                        <p><strong>TIN Number:</strong> <?php echo $row['tin'] ?></p>
                                                       
                                                    </div>
                                                </div>
                                            </div>
<?php }?>


<?php 
$sql = "";
$sql = $db->prepare("SELECT * FROM tbl_ev_venue_reservations where id='$re_id'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
                        		    
                        		    $pax = $row['pax'];
                        		    $note= $row['reservation_remarks'];
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
													 <p><strong>Reserved</strong> <?php echo $venue = getVenueName($row['venue_id'])?></p>
													
                                                        <p><strong>Date</strong> <?php echo $revdate = $row['reservation_date'];?></p>
                                                        <hr>
                                                        <p><strong>Time:</strong> <?php echo $stime = $row['start_time'];?> -  <?php echo $row['end_time'];?></p>
                                                        <hr>
                                                       
                                                       
                                                      
                                                    </div>
                                                </div>
                                            </div>
<?php } ?>
                                            <!-- Comments and Notes -->
                                            <div class="col-md-4">
                                                <?php
                                                $balance = $amountToPay - $paidAmount;
                                                ?>
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
                                                                    <strong class="fs-6"><?php echo number_format($amountToPay - $paidAmount); ?> RWF</strong>
                                                                </p>
                                                            </div>
                                                            <div class="col-12">
                                                                <p class="badge bg-default p-2 text-start balance-badge">
                                                                    <span>Checkout</span><br><br>
                                                                    <strong class="fs-6"><?php echo $balance != 0 ? "You can't checkout with unpaid Invoice" : "<a href='index?resto=venue_checkout&&checkoutVenue=$re_id' class='btn btn-outline-info'>Checkout</a>" ?></strong>
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
                                                <div class="card mt-2" hidden>
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
                                        <h5 class="mb-0">Items</h5>
                                        <button type="button" id="addVenueModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVenueModal">
                                            <i class="bx bx-plus"></i> Add item
                                        </button>
                                        
                                        <a href="?resto=print_function_sheet&&booking_id=<?php echo $_REQUEST['booking_id']?>">View and Print</a>
                                    </div>
                                    <div class="card-body">

  
<table class="table table-considered">
           
           <?php $sql = $db->prepare("SELECT * FROM venu_orders where booking_id='".$_REQUEST['booking_id']."'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
                        	?><tr> <th><?php echo $row['item']; ?> </th><th><?php echo $row['qty']; ?></th></tr> <?php	    
                        		    
                        		}?>
           
    
</table>
<br><br>
<table class="table table-considered">
<tr> <th> PAX NUMBER</th><th><form method="POST"><input type"text" name="paxnumber" value="<?php echo $pax?>" class="form-control"><br> <input type="submit" name="savepax" value="save"></form></th> </tr>   
<tr> <th> OTHER NOTE</th><th><form method="POST"><input type"text" name="functionnotes"  value="<?php echo $note?>" class="form-control"> <br> <input type="submit" name="savenote"  value="save"></form></th> </tr>    

    
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
                                      <?php if(!$due_amount==0){?>  <button type="button" id="addpayment" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addpaymentModal">
                                            <i class="bx bx-plus"></i> New Payment
                                        </button>
									  <?php } ?>
                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->
                                       
                                       <table  class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Amount</th>
                                                 
                                                    <th>Method</th>
                                                    <th>Date</th>
                                                   
                                                  
                                                
                                                </tr>
                                            </thead>
                                            <tbody>
											<?php 
											
											$sum = 0;
											$sql = " ";
$sql = $db->prepare("SELECT * FROM venue_payments WHERE booking_id='$re_id'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
	?>
	
	<tr>
                                                    <td><?php echo $sum = $sum + 1;?></td>
                                                  
											
                                                    <td><?php echo number_format($row['amount']);?> RWF</td>
                                                    <td><?php echo $row['method'];?></td>
                                                      <td><?php echo date('d/M/Y',$row['payment_time']);?></td>
                                                
                                                  
                                        
                                                </tr>
	
<?php	
	
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
							
							
							 
							
											<br><br>
							<input type="text" class="form-control" placeholder="Tin Number"><br>
<input type="text" class="form-control" placeholder="Purchase code">
							 
							
							
							<br>	<br>
						
						
                                <div class="card mb-4" id="invoice">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                     

                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->

<?php 



?>
									    <div class="col-md-12">
										
										<center>
										<!--    <img src="../../assets/img/logo/logo_sample.png" style="width:60px">-->
										<!--</center>-->
										<!--<br>-->
										<!-- <h5 class="mb-0">  <center>Invoice</center></h5>-->
				                        <?php include "../holder/printHeader.php" ?>
										<center>
											<?php 	if($due_amount!=0){
	
				echo "<h5>Unpaid</h5>";
				}?>
				
									<!--	<P>Centre Saint Paul Kigali Ltd-->
									<!--	<br>KN 31 St, Kigali, Rwanda-->
									<!--	<br>+250 78 12 000 00-->
									<!--TIN/VAT Number: 111477597<br></p>-->
									
									
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
										-------------------------------------
										<div class="row">
										<div class="col-md-6">
										<p>Venue rent</p>
										</div>
										
											<div class="col-md-6">
										<p class="pull-end"><?php echo number_format($booking_amount)?> RWF</p>
										</div>
										</div>
										

						
						
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
										
										
							
										

--------------------------------

<p>Invoice No: <?php echo $re_id?></p>
<p>Date: <?php echo date("d-m-Y H:I")?></p>

--------------------------------
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
                    <h5 class="modal-title" id="exampleModalLabel1">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <form method="POST">
                 
		
                       
									
									

                                   
                             
							    <div class="mb-3">
                            <label class="form-label" for="venue_name">Item Name</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input type="text" name="item" class="form-control" placeholder="Item name">
                            
                            </div>
                        </div>
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Quantity</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"></i></span>
                                <input type="number" name="qty" class="form-control" placeholder="Qty">
                            
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
                              id="amounts"
                                    placeholder="Amount"
                                   value="<?php echo $due_amount ?>"
                                    required 
									max="<?php echo $due_amount?>"/>
                            </div>
                        </div>
						
						
						    <div class="mb-3">
                            <label class="form-label" for="venue_name">Payment method</label>
                            <div class="input-group input-group-merge">
                                <select  id="currency"
                                    class="form-control"
                                   name="method"
                                    required>
									
									<option value='cash'>Cash</option>
									<option value='card'>Card</option>
									<option value='momo'>MTN Mobile Money</option>
							
									<option value='credit'>Credit</option>

                                   
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
									
									
<?php $sql = "";
$sql = $db->prepare("SELECT * FROM currencies");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
?><option value='<?php echo $row['currency_id']?>'><?php echo $row['name']?> - (Rate: <?php echo $row['currency_exchange']?>)</option><?php
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
                            <input  type="submit" class="btn btn-outline-info" name="addpayment" value="Create">
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
    
    
     function convert(){
         var currency = document.getElementById('currency').value;
         var amount = <?php echo $due_amount?>;
        
        
          var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          
          var tot =amount/this.responseText; 
        document.getElementById("amounts").value =tot.toFixed(2);
       // alert(this.responseText)
      }
    };
    xmlhttp.open("GET","getCurrency.php?id="+currency,true);
    xmlhttp.send();
        
        
            
            }
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
