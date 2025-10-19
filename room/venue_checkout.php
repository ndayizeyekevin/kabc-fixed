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
		 
		 //currency_amount
         $currency_amount = $amount;
         //  Total in RWF
		 $amount = $amount * $rate;


$sql = "INSERT INTO `venue_payments` (`payment_id`, `booking_id`, `amount`, `payment_time`, `method`,remark,currency, currency_amount, rate) VALUES (NULL, '$re_id', '$amount', ' $date', '$method','$remark','$currency','$currency_amount','$rate');";

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

// Change Status
if(isset($_GET['change_status']) && !empty($_GET['change_status']) && isset($_GET['booking_id'])){
    $new_status = $_GET['change_status'];
    $booking_id = $_GET['booking_id'];

    $valid_statuses = ['Confirmed', 'Checkedout', 'Cancelled', 'Pending'];
    if(in_array($new_status, $valid_statuses)){
        $sql = "UPDATE `tbl_ev_venue_reservations` SET status = '$new_status' WHERE id = '$booking_id'";

        if ($conn->query($sql) === TRUE) {
           echo "<script>window.location='index?resto=venue_checkout&booking_id=$booking_id'</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Change Status - Only execute once per request
if(isset($_GET['change_status']) && !empty($_GET['change_status']) && isset($_GET['booking_id']) && !isset($_SESSION['status_updated'])){
    $new_status = $_GET['change_status'];
    $booking_id = $_GET['booking_id'];

    $valid_statuses = ['Confirmed', 'Checkedout', 'Cancelled', 'Pending'];
    if(in_array($new_status, $valid_statuses)){
        $sql = "UPDATE `tbl_ev_venue_reservations` SET status = '$new_status' WHERE id = '$booking_id'";

        if ($conn->query($sql) === TRUE) {
            // Set session flag to prevent repeated execution
            $_SESSION['status_updated'] = true;
            // Redirect without query parameters to prevent repeated execution
            $redirect_url = "?resto=venue_checkout&booking_id=$booking_id";
            echo "<script>alert('Status updated to $new_status'); window.location.href='$redirect_url';</script>";
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Clear the session flag after successful redirect
if(isset($_SESSION['status_updated'])){
    unset($_SESSION['status_updated']);
}
?>








<?php 
// Handle multiple items addition
if(isset($_POST['add_multiple'])){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id = $_REQUEST['booking_id'];
    
    // Check if items array exists and is not empty
    if(isset($_POST['item']) && is_array($_POST['item']) && isset($_POST['qty']) && is_array($_POST['qty']) && isset($_POST['price']) && is_array($_POST['price'])){

        $items = $_POST['item'];
        $quantities = $_POST['qty'];
        $prices = $_POST['price'];

        // Validate that arrays have the same length
        if(count($items) !== count($quantities) || count($items) !== count($prices)){
            echo "<script>alert('Error: Item, quantity, and price arrays mismatch'); window.location.href=window.location.href;</script>";
            exit;
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = array();

        // Loop through each item
        for($i = 0; $i < count($items); $i++){
            // Sanitize inputs
            $item = mysqli_real_escape_string($conn, trim($items[$i]));
            $item = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
            $item = stripslashes($item);

            $qty = mysqli_real_escape_string($conn, trim($quantities[$i]));
            $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
            $qty = stripslashes($qty);

            $price = mysqli_real_escape_string($conn, trim($prices[$i]));
            $price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8');
            $price = stripslashes($price);

            // Validate that item, qty, and price are not empty
            if(empty($item) || empty($qty) || $qty < 1 || empty($price) || $price < 0){
                $errorCount++;
                $errors[] = "Row " . ($i + 1) . ": Invalid item, quantity, or price";
                continue;
            }

            // Insert into database
            $sql = "INSERT INTO `venu_orders` (`order_id`, `booking_id`, `item`, `qty`, `price`, `created_at`)
                    VALUES (NULL, '$id', '$item', '$qty', '$price', CURRENT_TIMESTAMP)";

            if ($conn->query($sql) === TRUE) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Row " . ($i + 1) . ": " . $conn->error;
            }
        }
        
        // Display results
        if($successCount > 0 && $errorCount == 0){
            echo "<script>alert('Successfully added $successCount items!'); window.location.href=window.location.href;</script>";
        } elseif($successCount > 0 && $errorCount > 0){
            $errorMsg = implode("\\n", $errors);
            echo "<script>alert('Added $successCount items successfully.\\n\\nErrors:\\n$errorMsg'); window.location.href=window.location.href;</script>";
        } else {
            $errorMsg = implode("\\n", $errors);
            echo "<script>alert('Failed to add items:\\n$errorMsg'); window.location.href=window.location.href;</script>";
        }
        
    } else {
        echo "<script>alert('No items provided!'); window.location.href=window.location.href;</script>";
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

// Handle item edit
if(isset($_POST['edit_item'])){
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $item = mysqli_real_escape_string($conn, $_POST['item']);
    $item = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
    $item = stripslashes($item);

    $qty = mysqli_real_escape_string($conn, $_POST['qty']);
    $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
    $qty = stripslashes($qty);

    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8');
    $price = stripslashes($price);

    // Validate inputs
    if(empty($item) || empty($qty) || $qty < 1 || empty($price) || $price < 0){
        echo "<script>alert('Error: Invalid item, quantity, or price'); window.location.href=window.location.href;</script>";
        exit;
    }

    $sql = "UPDATE venu_orders SET item='$item', qty='$qty', price='$price' WHERE order_id='$order_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Item updated successfully!'); window.location.href=window.location.href;</script>";
    } else {
        echo "<script>alert('Error updating item: " . $conn->error . "'); window.location.href=window.location.href;</script>";
    }
}

// Handle item deletion
if(isset($_GET['delete_item']) && !empty($_GET['delete_item'])){
    $order_id = mysqli_real_escape_string($conn, $_GET['delete_item']);

    $sql = "DELETE FROM venu_orders WHERE order_id='$order_id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the same page but remove the delete_item parameter to prevent loops
        $url_params = $_GET;
        unset($url_params['delete_item']);
        $redirect_url = '?' . http_build_query($url_params);
        echo "<script>alert('Item deleted successfully!'); window.location.href='$redirect_url';</script>";
    } else {
        echo "<script>alert('Error deleting item: " . $conn->error . "'); window.location.href=window.location.href;</script>";
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
     .stf{
        color: #ffffff;
        font-weight: bold;
        background-color: green;
        padding: 12px;
        border-radius: 5px;
        transition: 0.5s ease;
        border: none;
    }
    .stf:hover{
        background-color: black;
        border-radius: none;
        border: green;
    }
    
    .item-row {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        position: relative;
    }

    .item-row:hover {
        background-color: #e9ecef;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .remove-item {
        width: 100%;
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
      <?php

$re_id = $_REQUEST['booking_id'];
$booking_amount=0;

$sql = $db->prepare("SELECT * FROM tbl_ev_venue_reservations where id='$re_id'");
                        		$sql->execute();
                        		$row = $sql->fetch();

   $customer_id = $row['customer_id'];
  $venue_id = $row['venue_id'];
  $re_id = $row['id'];
  $venue_status = $row['status'];

  // Debug: Show what we fetched
  echo "<!-- Debug: venue_status = $venue_status -->";

?>

                        <!-- Status Button - Top Right -->
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <!-- Breadcrumbs content can go here if needed -->
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle <?php
                                        if($venue_status == 'Confirmed') echo 'btn-success';
                                        elseif($venue_status == 'Pending') echo 'btn-warning';
                                        elseif($venue_status == 'Checkedout') echo 'btn-info';
                                        elseif($venue_status == 'Cancelled') echo 'btn-danger';
                                        else echo 'btn-outline-primary';
                                    ?>" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-info-circle"></i> Status: <?php echo $venue_status; ?>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="?resto=venue_checkout&booking_id=<?php echo $re_id; ?>&change_status=Confirmed">Confirmed</a></li>
                                        <li><a class="dropdown-item" href="?resto=venue_checkout&booking_id=<?php echo $re_id; ?>&change_status=Checkedout">Checked Out</a></li>
                                        <li><a class="dropdown-item" href="?resto=venue_checkout&booking_id=<?php echo $re_id; ?>&change_status=Cancelled">Cancelled</a></li>
                                        <li><a class="dropdown-item" href="?resto=venue_checkout&booking_id=<?php echo $re_id; ?>&change_status=Pending">Pending</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

      <?php





$sql = $db->prepare("SELECT amount, reservation_date, reservation_end_date FROM tbl_ev_venue_reservations WHERE id = :reservation_id LIMIT 1");
$sql->bindParam(':reservation_id', $re_id, PDO::PARAM_INT);
$sql->execute();
if ($reservationRow = $sql->fetch(PDO::FETCH_ASSOC)) {
    $daily_rate = (float)$reservationRow['amount'];
    $start_date = strtotime($reservationRow['reservation_date']);
    $end_date = strtotime($reservationRow['reservation_end_date']);

    // Calculate number of days (inclusive of both start and end date)
    $num_days = max(1, (($end_date - $start_date) / 86400) + 1);

    // Calculate total venue cost based on daily rate and number of days
    $booking_amount = $daily_rate * $num_days;
} else {
    $booking_amount = 0.0; // No amount found for this reservation
    $num_days = 0;
}


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


 $amountToPay = $amountToPay + ($row['qty'] * $row['price']);


}

// Add venue base rate to items total
$amountToPay = $amountToPay + (float)$booking_amount;
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
                                                        <p><strong>End Date</strong> <?php echo $row['reservation_end_date']; ?></p>
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
                                            <i class="bx bx-plus"></i> Add items
                                        </button>
                                        
                                        <a target="_blank" href="?resto=print_function_sheet&&booking_id=<?php echo $_REQUEST['booking_id']?>">View and Print</a>
                                    </div>
                                    <div class="card-body">

  
    <table class="table table-considered">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = $db->prepare("SELECT * FROM venu_orders where booking_id='".$_REQUEST['booking_id']."'");
            $sql->execute();
            $grandTotal = 0;
            while($row = $sql->fetch()){
                $total = $row['qty'] * $row['price'];
                $grandTotal += $total;
            ?>
            <tr>
                <td><?php echo $row['item']; ?></td>
                <td><?php echo $row['qty']; ?></td>
                <td><?php echo number_format($row['price'], 2); ?> RWF</td>
                <td><?php echo number_format($total, 2); ?> RWF</td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-info"
                                onclick="editItem(<?php echo $row['order_id']; ?>, '<?php echo addslashes($row['item']); ?>', <?php echo $row['qty']; ?>, <?php echo $row['price']; ?>)"
                                title="Edit Item">
                            <i class="bx bx-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="deleteItem(<?php echo $row['order_id']; ?>, '<?php echo addslashes($row['item']); ?>')"
                                title="Delete Item">
                            <i class="bx bx-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr style="border-top: 2px solid #000; font-weight: bold;">
                <td colspan="3" style="text-align: right;">Grand Total:</td>
                <td><?php echo number_format($grandTotal, 2); ?> RWF</td>
            </tr>
        </tbody>
    </table>
    <br><br>
    <table class="table table-considered">
        <tr> 
            <th> PAX NUMBER</th>
            <th>
                <form method="POST">
                <input type="text" name="paxnumber" value="<?php echo $pax?>" class="form-control"><br> 
                <input type="submit" name="savepax" value="Save Pax Number" class="btn btn-primary text-primary">
                </form>
            </th> 
        </tr>   
        <tr> 
            <th> OTHER NOTE</th>
            <th>
                <form method="POST">
                    <input type="text" name="functionnotes"  value="<?php echo $note?>" class="form-control"> <br> <input type="submit" class="btn btn-success text-success" name="savenote"  value="Save Note">
                </form>
            </th> 
        </tr>    

        
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
                                    <?php
                                    if($paidAmount > 0){
                                        ?>
                                        <!-- Create Refund Link -->
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Refunds</h5>
                                            <a href="./?resto=venue_refund&booking_id=<?php echo $re_id; ?>&balance=<?php echo $paidAmount; ?>" class="btn btn-outline-primary">
                                                <i class="bx bx-minus"></i> Make Refund
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Payments</h5>
                                      <?php if(!$due_amount==0){?>  <button type="button" id="addpayment" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addpaymentModal">
                                            <i class="bx bx-plus"></i> New Payment
                                        </button>
									  <?php } else if($due_amount==0){ ?>
                                        
                                            <button type="button" class="btn btn-info">
                                                    <i class="bx bx-check"></i> Fully Paid No Payments Needed
                                            </button>
                                        
                                        <?php }
                                       ?>
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
<div class="card mt-3">
                                            <div class="card-body text-center">
                                                <a href="progressive_venue_receipt.php?booking_id=<?php echo $_REQUEST['booking_id']; ?>" target="_blank" class="btn btn-success">
                                                    <i class="bx bx-printer"></i> Print Progressive Receipt
                                                </a>
                                            </div>
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
									  } 
                                      if ($balance > 0) {
                                          $payment_status = 'Unpaid';
                                      } else {
                                          $payment_status = 'Paid';
                                      }
 
                                      
                                      ?>
							
							
							 <?php if($payment_status=='Paid' && $due_amount==0)
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
										<p>Venue rent (<?php echo $num_days; ?> day<?php echo $num_days > 1 ? 's' : ''; ?> @ <?php echo number_format($daily_rate, 2); ?> RWF/day)</p>
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

                                            $vat = $booking_amount * 0.18;
                                            echo number_format($vat)?> RWF
										</div>
										</div>
						

		                                <div class="row">
										<div class="col-md-6">
										<p>TOTAL</p>
										</div>
										
										<div class="col-md-6">
										<p><?php echo number_format($booking_amount);?> RWF</p>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="addItemsForm">
                        <div id="itemsContainer">
                            <!-- First Item Row -->
                            <div class="item-row mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Item Name</label>
                                        <input type="text" name="item[]" class="form-control item-name" placeholder="Item name" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" name="qty[]" class="form-control item-qty" placeholder="Qty" min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" name="price[]" class="form-control item-price" placeholder="Price" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-item" style="display:none;">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addMoreItems">
                                <i class="bx bx-plus"></i> Add Another Item
                            </button>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-danger" data-bs-dismiss="modal">Close</button>
                            <input type="submit" class="stf" name="add_multiple" value="Create All Items">
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
                                <select onchange="convert()" id="currency"
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
                                                        <label class="form-label" for="currency-tab">Currency</label>
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"></span>
                                                            <select id="currency-tab" onchange="convertCurrency('currency-tab', 'amounts', 'convertedAmount-tab')" class="form-control" name="currency" required>
                                                                <?php
                                                                $sql = $db->prepare("SELECT * FROM  currencies");
                                                                $sql->execute();
                                                                while ($row = $sql->fetch()) {
                                                                ?><option value='<?php echo $row['currency_id'] ?>' data-rate='<?php echo $row['currency_exchange'] ?>'><?php echo $row['name'] ?> - (Rate: <?php echo $row['currency_exchange'] ?>)</option><?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <small id="convertedAmount-tab" class="form-text text-muted"></small>
                           
						
						
						
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
    
     function convertCurrency(selectId, amountId, convertedId) {
        var select = document.getElementById(selectId);
        var rate = parseFloat(select.options[select.selectedIndex].getAttribute('data-rate'));
        var dueAmount = <?php echo $due_amount ?>;
        
        if (!isNaN(rate) && rate > 0) {
            var convertedAmount = dueAmount / rate;
            document.getElementById(amountId).value = convertedAmount.toFixed(2);
            if (convertedId) {
                document.getElementById(convertedId).innerHTML = 'Equivalent to ' + dueAmount.toFixed(2) + ' RWF at rate ' + rate;
            }
        } else {
            document.getElementById(amountId).value = dueAmount.toFixed(2);
            if (convertedId) {
                document.getElementById(convertedId).innerHTML = '';
            }
        }
    }
    
   
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
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;

    // Add more items functionality
    document.getElementById('addMoreItems').addEventListener('click', function() {
        itemCount++;
        const itemsContainer = document.getElementById('itemsContainer');

        const newItemRow = document.createElement('div');
        newItemRow.className = 'item-row mb-3 p-3 border rounded';
        newItemRow.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item[]" class="form-control item-name" placeholder="Item name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="qty[]" class="form-control item-qty" placeholder="Qty" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price[]" class="form-control item-price" placeholder="Price" min="0" step="0.01" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </div>
        `;

        itemsContainer.appendChild(newItemRow);
        updateRemoveButtons();
    });

    // Remove item functionality
    document.getElementById('itemsContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const itemRow = e.target.closest('.item-row');
            itemRow.remove();
            itemCount--;
            updateRemoveButtons();
        }
    });

    // Show/hide remove buttons
    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-item');
        if (removeButtons.length === 1) {
            removeButtons[0].style.display = 'none';
        } else {
            removeButtons.forEach(btn => btn.style.display = 'block');
        }
    }

    // Form validation before submit
    document.getElementById('addItemsForm').addEventListener('submit', function(e) {
        const itemNames = document.querySelectorAll('.item-name');
        const itemQtys = document.querySelectorAll('.item-qty');
        const itemPrices = document.querySelectorAll('.item-price');
        let isValid = true;

        itemNames.forEach((input, index) => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        itemQtys.forEach((input, index) => {
            if (!input.value || input.value < 1) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        itemPrices.forEach((input, index) => {
            if (!input.value || input.value < 0) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill all item names and quantities correctly.');
        }
    });

    // Reset form when modal closes
    document.getElementById('addVenueModal').addEventListener('hidden.bs.modal', function() {
        const itemsContainer = document.getElementById('itemsContainer');
        itemsContainer.innerHTML = `
            <div class="item-row mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Item Name</label>
                        <input type="text" name="item[]" class="form-control item-name" placeholder="Item name" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="qty[]" class="form-control item-qty" placeholder="Qty" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price[]" class="form-control item-price" placeholder="Price" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-item" style="display:none;">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        itemCount = 1;
    });
});

// Edit item function
function editItem(orderId, itemName, quantity, price) {
    // Create and show edit modal
    const editModal = document.createElement('div');
    editModal.className = 'modal fade';
    editModal.id = 'editItemModal';
    editModal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" value="${orderId}">
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="item" class="form-control" value="${itemName}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="qty" class="form-control" value="${quantity}" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" class="form-control" value="${price}" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_item" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    // Remove existing modal if any
    const existingModal = document.getElementById('editItemModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.appendChild(editModal);

    // Show modal
    const modal = new bootstrap.Modal(editModal);
    modal.show();

    // Remove modal from DOM when hidden
    editModal.addEventListener('hidden.bs.modal', function() {
        editModal.remove();
    });
}

// Delete item function
function deleteItem(orderId, itemName) {
    if (confirm(`Are you sure you want to delete "${itemName}"? This action cannot be undone.`)) {
        // Get current URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const bookingId = urlParams.get('booking_id');

        // Create a temporary form to submit the delete request
        const form = document.createElement('form');
        form.method = 'GET';
        form.innerHTML = `
            <input type="hidden" name="delete_item" value="${orderId}">
            <input type="hidden" name="booking_id" value="${bookingId}">
        `;

        // Preserve other URL parameters if needed
        urlParams.forEach((value, key) => {
            if (key !== 'delete_item' && key !== 'booking_id') {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
        });

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

