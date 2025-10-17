<?php
$json = null;
$list='';
$url = "http://localhost:8080/rraVsdcSandbox2.1.2.3.7/";
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1); 
// error_reporting(E_ALL); 
// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1); 
// error_reporting(E_ALL); 
include "../inc/conn.php";

// PHP block to fetch booking status and determine class
$bookingId = isset($_REQUEST['booking_id']) ? intval($_REQUEST['booking_id']) : 0;
$statusName = 'Default';
$statusClass = 'btn-secondary';

if ($bookingId > 0) {
    try {
        $sql = "SELECT t2.status_name FROM tbl_acc_booking AS t1 JOIN tbl_acc_booking_status AS t2 ON t1.booking_status_id = t2.id WHERE t1.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $booking = $result->fetch_assoc();
            $statusName = $booking['status_name'];

            switch ($statusName) {
                case 'Pending':
                    $statusClass = 'btn-warning';
                    break;
                case 'Confirmed':
                    $statusClass = 'btn-success';
                    break;
                case 'Cancelled':
                    $statusClass = 'btn-danger';
                    break;
                case 'No-Show':
                    $statusClass = 'btn-secondary';
                    break;
                default:
                    $statusClass = 'btn-secondary';
                    break;
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        $statusName = 'Error';
        $statusClass = 'btn-secondary';
    }
}


$room_ids = 0;

if (isset($_POST['addpayment'])) {



    $amount =  mysqli_real_escape_string($conn, $_POST['amount']);
    $amount = htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
    $amount = stripslashes($amount);

    $date = time();

    $method =  mysqli_real_escape_string($conn, $_POST['method']);
    $method = htmlspecialchars($method, ENT_QUOTES, 'UTF-8');
    $method = stripslashes($method);


    $remark =  mysqli_real_escape_string($conn, $_POST['remark']);
    $remark = htmlspecialchars($remark, ENT_QUOTES, 'UTF-8');
    $remark = stripslashes($remark);

    $currency =  mysqli_real_escape_string($conn, $_POST['currency']);
    $currency = htmlspecialchars($currency, ENT_QUOTES, 'UTF-8');
    $currency = stripslashes($currency);

    $rate = getCurrencyValue($currency);

    $amount = $amount * $rate;



    $stmt = $conn->prepare("INSERT INTO `payments`
                      (`payment_id`, `booking_id`, `amount`, `payment_time`, `method`, `remark`, `currency`, `rate`)
                      VALUES
                      (NULL, ?, ?, ?, ?, ?, ?, ?)");

    // Check if prepare was successful
    if ($stmt === false) {
        echo "<script>alert('Error preparing statement: " . htmlspecialchars($conn->error) . "')</script>";
        exit;
    }

    // Bind parameters to prevent SQL injection
    $booking_id = $_REQUEST['booking_id'];
    $stmt->bind_param("sdssssd", $booking_id, $amount, $date, $method, $remark, $currency, $rate);

    // Execute the query
    if ($stmt->execute()) {
        // Payment inserted successfully, now update the invoice
        $updateStmt = $conn->prepare("UPDATE invoice SET `Paid` = Paid + ?, `balance` = Total - (Paid + ?) WHERE `booking_id` = ?");

        if ($updateStmt === false) {
            echo "<script>alert('Error preparing update statement: " . htmlspecialchars($conn->error) . "')</script>";
            exit;
        }

        // Bind parameters for the update query
        $updateStmt->bind_param("dds", $amount, $amount, $booking_id);

        // Execute the update
        if ($updateStmt->execute()) {
            echo "<script>alert('Payment Added Successfully')</script>";
        } else {
            echo "<script>alert('Error updating invoice: " . htmlspecialchars($updateStmt->error) . "')</script>";
        }

        $updateStmt->close();
    } else {
        echo "<script>alert('Error adding payment: " . htmlspecialchars($stmt->error) . "')</script>";
    }

    $stmt->close();
}

if (isset($_POST['update_payment_method'])) {
    $payment_id = intval($_POST['edit_payment_id']);
    $new_method = mysqli_real_escape_string($conn, $_POST['edit_method']);
    $sql = "UPDATE payments SET method='$new_method' WHERE payment_id=$payment_id";
    if ($conn->query($sql) === true) {
        echo "<script>alert('Payment method updated successfully');window.location('" . $_SERVER['REQUEST_URI'] . "');</script>";
    } else {
        echo "<script>alert('Failed to update payment method');</script>";
    }
}
?>




<!-- styles -->
<style>
    .colr{
        color: #ffffff !important; 
        background-color: #28a745 !important;
    }
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
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h3>Booking #<?php echo $booking_id = $_REQUEST['booking_id']; ?> </h3>

                        </div>



                        <?php if (isset($_POST['addgroup'])) {
                            $service = mysqli_real_escape_string($conn, $_POST['group']);
                            $service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
                            $service = stripslashes($service);


                            $sql = "UPDATE  `tbl_acc_booking` SET `booking_type`='Group', group_id='$service' where  id ='$booking_id'";

                            if ($conn->query($sql) === TRUE) {



                                echo "<script>alert('Changed to group successfully');</script>";
                                // echo "<script>window.history.go(-1);</script>";




                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }
if (isset($_POST['add'])) {
    $service = mysqli_real_escape_string($conn, $_POST['service']);
    $service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
    $service = stripslashes($service);

    $qty = mysqli_real_escape_string($conn, $_POST['qty']);
    $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
    $qty = stripslashes($qty);

    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8');
    $price = stripslashes($price);

    $total = $price * $qty;

    $sql = "INSERT INTO `orders` (`order_id`, `name`, `price`, `booking_id`, qty, total) VALUES (NULL, '$service', '$price', '" . $_REQUEST['booking_id'] . "', '$qty', '$total');";

                            if ($conn->query($sql) === TRUE) {
                                echo "<script>alert('created')</script>";
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }



                        $today = date('Y-m-d');

                        $sql = $db->prepare("SELECT * FROM tbl_acc_booking where id='" . $_REQUEST['booking_id'] . "'");
                        $sql->execute();
                        while ($row = $sql->fetch()) {
                            $room_ids = getBookedRoom($row['id']);

    $guest_id = $row['guest_id'];
    $ckin = $row['checkin_date'];
    $ckout = $row['checkout_date'];
    $booking_amount_price = $row['room_price'];

    $today = date_create($today);
    $ckin = date_create($ckin);
    $ckout = date_create($ckout);
    $diff = date_diff($ckin, $ckout);
    $night = $diff->format("%a");

                            $booking_amount = $booking_amount_price * $night;

                            $payment_status = $row['payment_status_id'];
                            $booking_status_id = $row['booking_status_id'];
                            $booking_type = $row['booking_type'];
                            $booking_room_capacity = getRoomCapacity(getBookedRoom($row['id']));
                        } ?>


                        <div class="col-md-6 text-end">
                            <div class="btn-group">
                                <button class="btn <?php echo $statusClass; ?>" type="button" data-bs-toggle="dropdown" id="bookingStatus"><?php echo $statusName; ?></button>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
                                <ul class="dropdown-menu">
                                    <?php if ($booking_status_id != 3) { ?>
                                        <li> <a class="dropdown-item" href="?resto=edit&&booking=<?php echo $_REQUEST['booking_id'] ?>">Edit</a> </li>
                                    <?php } ?>

                                    <?php if ($booking_status_id == 1) { ?>
                                        <li> <a class="dropdown-item" href="confirm.php?id=<?php echo $_REQUEST['booking_id'] ?>">Confirm this booking</a> </li>
                                        <!--<li> <a class="dropdown-item" href="confirm.php?cancel=<?php echo $_REQUEST['booking_id'] ?>">Cancel Booking</a> </li>-->
                                        <li> <a class="dropdown-item" href="confirm.php?cancel=<?php echo $_REQUEST['booking_id'] ?>" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">Cancel Booking</a>
                                        </li>

                                    <?php } ?>

                                    <?php if ($booking_status_id != 6 && $booking_status_id != 3) { ?>

                                        <li> <a class="dropdown-item" href="?resto=checkin&&booking=<?php echo $_REQUEST['booking_id'] ?>">Check in</a> </li>

                                    <?php } ?>





                                    <!--<li> <a class="dropdown-item" href="?resto=moving&&booking=<?php //echo $_REQUEST['booking_id']
            ?>">Move Booking</a> </li>-->


                                    <?php if ($booking_room_capacity == 2 || $booking_type == 'Group') { ?>
                                        <!-- <li> <a class="dropdown-item" href="?resto=split_invoice&&bookingbooking=<?php //echo $_REQUEST['booking_id']
                    ?>">Split Payment</a> </li>
          <!--                              <li> <a class="dropdown-item" href="?resto=booking_guest&&booking=<?php //echo $_REQUEST['booking_id']
              ?>">Add Guest</a> </li>-->
                                    <?php } else { ?>


                                        <li>



                                            <!--<a class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#addToGroupModal" >Add Reservation to Group</a> </li>-->

                                        <?php } ?>
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

                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-checkout" aria-controls="navs-top-messages" aria-selected="false">Check Out</button>
                            </li>


                        </ul>
                        <div class="tab-content" style="background-color: #f5f5f9; border: none">
                            <!--Top Navigation General Tab -->
                            <div class="tab-pane fade show active" id="navs-top-home" role="tabpanel">
                                <div class="container">

                                    <!-- General and Balance -->
                                    <div class="row">
                                        <!-- General Info -->


                                        <?php


                                        $amountToPay = 0;


                                        $sql = $db->prepare("SELECT * FROM orders where booking_id='" . $_REQUEST['booking_id'] . "'");
                                        $sql->execute();
                                        while ($row = $sql->fetch()) {



                                            $amountToPay = $amountToPay + ($row['price'] * $row['qty']);
                                        }

                                        $guest_booking = 0;



                                        $sql = $db->prepare("SELECT * FROM guest_booking where booking_id='" . $_REQUEST['booking_id'] . "'");
                                        $sql->execute();
                                        while ($row = $sql->fetch()) {



                                            $guest_booking = $guest_booking + $row['amount'];
                                        }

                                        $code = getClientOrder();
                                        function getClientOrder()
                                        {
                                            include '../inc/conn.php';

                                            $sql = "SELECT * FROM `tbl_cmd` WHERE room_client='" . $_REQUEST['booking_id'] . "'";
                                            $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            return $row["OrderCode"];
        }
    } else {
        return "";
    }
}







                                        $menutotal = 0;

                                        $i = 0;
                                        $tot = array();
                                        $sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    WHERE tbl_cmd_qty.cmd_code='" . $code . "'");
                                        $sql->execute(array());
                                        if ($sql->rowCount()) {
                                            while ($fetch = $sql->fetch()) {
                                                $i++;
                                                $tot[] = $fetch['cmd_status'];

                                                $OrderCode = $fetch['cmd_code'];
                                                $status = $fetch['cmd_status'];



                                                $GetStsqty = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '" . $OrderCode . "'");
                                                $GetStsqty->execute();
                                                $fstsqty = $GetStsqty->fetch();

                                                $GetStsmenu = $db->prepare("SELECT * FROM menu WHERE menu_id = '" . $fetch['cmd_item'] . "'");
                                                $GetStsmenu->execute();
                                                $fstsmenu = $GetStsmenu->fetch();

                                                $rsv_ID = $fstsqty['reservat_id'];
                                                $cat_id = $fstsmenu['cat_id'];
                                                $menu_price = $fstsmenu['menu_price'];
                                                $menutotal += ($menu_price * $fetch['cmd_qty']);
                                                $subcat_ID = $fstsmenu['subcat_ID'];
                                                $menu_id = $fetch['cmd_qty_id'];
                                                $OrderCode = $fstsqty['OrderCode'];
                                                $serv = $fstsqty['Serv_id'];
                                            }
                                        }


$booking_amount = $booking_amount + $guest_booking + $menutotal;
$amountToPay = $amountToPay + $booking_amount;

                                        $paidAmount = 0;


                                        $sql = $db->prepare("SELECT * FROM payments where booking_id='" . $_REQUEST['booking_id'] . "'");
                                        $sql->execute();
                                        while ($row = $sql->fetch()) {



    $paidAmount = $paidAmount + $row['amount'];
    $taxblAmtB += ($row['amount'] ?? 0);
}

                                        $tax = $amountToPay * 0.18;

                                        $due_amount = $amountToPay - $paidAmount;
                                        ?>





                                        <?php


$sql = $db->prepare("SELECT * FROM tbl_acc_guest where id='$guest_id'");
$sql->execute();
while ($row = $sql->fetch()) {
    ?>


                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">GUEST</div>
                                                    <div class="card-body">
                                                        <p><strong>Guest:</strong>
                                                            <?php echo $row['first_name'] . " " . $row['last_name'] ?></p>
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
                                        <?php } ?>


                                        <?php


                                        $sql = $db->prepare("SELECT * FROM tbl_acc_booking where id='" . $_REQUEST['booking_id'] . "'");
$sql->execute();
while ($row = $sql->fetch()) {


    $bookingComment = $row['booking_comment']
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
                                                        <p><strong>Check-in:</strong> <?php echo $row['checkin_date']; ?></p>
                                                        <hr>
                                                        <p><strong>Check-out:</strong> <?php echo $row['checkout_date']; ?></p>
                                                        <hr>
                                                        <p><strong>Nights:</strong> <?php echo $row['duration']; ?></p>
                                                        <p><strong>Price Per Night:</strong> <?php echo number_format($row['room_price']); ?> RWF</p>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <strong>Source:</strong> <?php echo $row['booked_from']; ?>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Company:</strong> <?php echo $row['company'] != "" || $row['company'] != null ? $row['company'] : getGroupName($row['group_id']); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="badge bg-default p-3 w-100 text-start"><strong>Coming From:</strong> <?php echo $row['coming_from']; ?></p>
                                                        <p class="badge bg-default p-3 w-100 text-start"><strong>Going To:</strong> <?php echo $row['going_to']; ?></p>
                                                    </div>
                                                </div>
                                            </div>

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
                                                                    <strong class="fs-6"><?php echo number_format($amountToPay) ?> RWF</strong>
                                                                </p>
                                                            </div>
                                                            <div class="col-12">
                                                                <p class="badge bg-default p-2 text-start">
                                                                    <span>Paid</span><br><br>
                                                                    <strong
                                                                        class="fs-6"><?php echo number_format($paidAmount);
                                                                    /* $taxblAmtB = $totalamount = $paidAmount ?? 0;  */
                                                                    ?>
                                                                        RWF</strong>
                                                                </p>
                                                            </div>
                                                            <div class="col-12">
                                                                <p class="badge bg-danger p-2 text-start mt-2">
                                                                    <span>Left</span><br><br>
                                                                    <!-- <strong class="fs-6"><?php // echo number_format($due_amount); ?> RWF</strong> -->
                                                                     <!-- Display the balance amount only if status is checked in booking using the select query to fetch the status -->
                                                                      <?php
                                                                      $fetchQuery = $db->prepare("SELECT booking_status_id FROM tbl_acc_booking WHERE id = ?");
                                                                      $fetchQuery->execute([$_GET['booking_id']]);
                                                                      $bookingStatus = $fetchQuery->fetchColumn();

                                                                      if ($bookingStatus == 6) {
                                                                          echo "<strong class='fs-6'>" . number_format($due_amount) . " RWF</strong>";
                                                                      } else {
                                                                          echo "<strong class='fs-6'>0 RWF</strong>";
                                                                          echo "<strong class='fs-6'> (Booking not checked in)</strong>";
                                                                      }
                                                                      ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <h6>Included (Addons)</h6>
                                                            <div class="col-12">
                                                                <?php echo getRoomClassFeature(getRoomClass(getBookedRoom($row['id']))); ?>
                                                            </div>

                                                        </div>
                                                        <!-- preferred payment method -->
                                                        <h6 hidden>PREFERRED PAYMENT METHOD</h6>
                                                    </div>
                                                </div>
                                                <div class="card mt-2">
                                                    <div class="card-header">
                                                        COMMENTS AND NOTES

                                                    </div>
                                                    <div class="card-body">
                                                        <?php echo $bookingComment ?>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                <?php } ?>

                                <?php


                                    $sql = $db->prepare("SELECT * FROM tbl_acc_booking where id='" . $_REQUEST['booking_id'] . "'");
$sql->execute();
$ii = 0;
while ($row = $sql->fetch()) {

    $ii += 1;
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
                                                                        <td><?php echo $row['checkin_date']; ?> -
                                                                            <?php echo $row['checkout_date']; ?>
                                                                        </td>
                                                                        <td><?php echo $row['num_adults']; ?></td>
                                                                        <td><?php echo $row['num_children']; ?></td>
                                                                        <td><?php echo getBookedRoom($row['id']) ?></td>
                                                                        <td><?php echo getRoomName(getBookedRoom($row['id'])) ?>
                                                                        </td>
                                                                        <td>Free WIFI</td>
                                                                        <td>For Reception</td>
                                                                        <td><?php echo number_format($row['booking_amount']); ?>
                                                                            RWF</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <!-- Orders -->
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">Restaurant orders</div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Menu Order</th>
                                                                    <th>Quantity</th>
                                                                    <th>U . P</th>
                                                                    <th>T . P</th>
                                                                    <th>Category</th>
                                                                    <th>Sub-Category</th>
                                                                    <th>Date-Time</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php



                            $total = 0;

$i = 0;
$tot = array();
$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    WHERE tbl_cmd_qty.cmd_code='" . $code . "'");
$sql->execute(array());
if ($sql->rowCount()) {
    while ($fetch = $sql->fetch()) {
        $i++;
        $ii++;
        $tot[] = $fetch['cmd_status'];

        $OrderCode = $fetch['cmd_code'];
        $status = $fetch['cmd_status'];



        $GetStsqty = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '" . $OrderCode . "'");
        $GetStsqty->execute();
        $fstsqty = $GetStsqty->fetch();

        $GetStsmenu = $db->prepare("SELECT * FROM menu WHERE menu_id = '" . $fetch['cmd_item'] . "'");
        $GetStsmenu->execute();
        $fstsmenu = $GetStsmenu->fetch();

        $rsv_ID = $fstsqty['reservat_id'];
        $cat_id = $fstsmenu['cat_id'];
        $menu_price = $fstsmenu['menu_price'];
        $total = $total + $menu_price;
        $subcat_ID = $fstsmenu['subcat_ID'];
        $menu_id = $fetch['cmd_qty_id'];
        $OrderCode = $fstsqty['OrderCode'];
        $serv = $fstsqty['Serv_id'];

        $GetSts = $db->prepare("SELECT * FROM tbl_status WHERE id = '" . $status . "'");
        $GetSts->execute();
        $fsts = $GetSts->fetch();


        $res_categ = $db->prepare("SELECT * FROM category WHERE cat_id ='" . $cat_id . "'");
        $res_categ->execute();
        $fCateg = $res_categ->fetch();
        $Cname = $fCateg['cat_name'];

        $res_sub_categ = $db->prepare("SELECT *FROM subcategory WHERE subcat_id ='" . $subcat_ID . "'");
        $res_sub_categ->execute();
        $fsub_Categ = $res_sub_categ->fetch();
        $Subname = $fsub_Categ['subcat_name'];
        ?>
                                                                        <tr>
                                                                            <?php
            if ($status == '5') {
                ?>
                                                                                <td>
                                                                                    <input type="checkbox" name="menu_id[]"
                                                                                        id="<?php echo $menu_id; ?>"
                                                                                        value="<?php echo $menu_id; ?>" />
                                                                                    <label for="<?php echo $menu_id; ?>"></label>
                                                                                </td>

                                                                            <?php } else { ?>
                                                                                <td><?php echo $i; ?></td>
                                                                            <?php } ?>
                                                                            <td><?php echo $fstsmenu['menu_name']; ?></td>
                                                                            <td>
                                                                                <?php
                if ($status == '3') { ?>
                                                                                    <input type="number" name="orderqty[]"
                                                                                        value="<?php echo $fetch['cmd_qty']; ?>"
                                                                                        class="form-control">
                                                                                    <input type="hidden" name="orderqtyid[]"
                                                                                        value="<?php echo $fetch['cmd_qty_id']; ?>"
                                                                                        class="form-control">
                                                                                    <?php
                } else {
                    echo $fetch['cmd_qty'];
                }
        ?>
                                                                            </td>
                                                                            <td><?php echo $menu_price; ?></td>
                                                                            <td><?php echo $menu_price * $fetch['cmd_qty']; ?>
                                                                            </td>
                                                                            <td><?php echo $Cname; ?></td>
                                                                            <td><?php echo $Subname; ?></td>
                                                                            <td><?php echo $fetch['created_at']; ?></td>
                                                                            <td>
                                                                                <?php
        if ($status == '3') {
            $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #dd5252;"></i>';
            $text = $fsts['status_name'];
            echo $bullet . " " . $text;
            ?>
                                                                                    <a href="?resto=prcsOrder_prcssng&m=<?php echo $_GET['m'] ?>&s=<?php echo $_GET['s']; ?>&id=<?php echo $fetch['cmd_qty_id']; ?>&st=<?php echo $_GET['st']; ?>"
                                                                                        class="btn btn-link btn-sm"
                                                                                        style="color:#e46a76;"
                                                                                        onclick="if(!confirm('Do you really want to remove this item on Order?'))return false;else return true;"><i
                                                                                            class="fa fa-close"></i> Remove</a>
                                                                                    <?php
        } elseif ($status == '5') {
            $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
            $text = $fsts['status_name'];
            echo $bullet . " " . $text;
        } elseif ($status == '7') {
            $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
            $text = $fsts['status_name'];
            echo $bullet . " " . $text;
        } elseif ($status == '8') {
            $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
            $text = $fsts['status_name'];
            echo $bullet . " " . $text;
        } elseif ($status == '13') {
            $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
            $text = $fsts['status_name'];
            echo $bullet . " " . $text;
        }
        ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
    }
}
?>
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

                            <!-- Profile Activities Information division  -->
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

                                                <table class="table">
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




                                                        $sql = $db->prepare("SELECT * FROM orders WHERE booking_id='" . $_REQUEST['booking_id'] . "'");
                                                        $sql->execute();
                                                        while ($row = $sql->fetch()) {

    $total = $total + $row['total'];

    ?>

                                                            <tr>
                                                                <td><?php echo $sum = $sum + 1; ?></td>
                                                                <td><?php echo getServiceName($row['name']); ?></td>
                                                                <td><?php echo $row['qty']; ?></td>
                                                                <td><?php echo  number_format($row['price']); ?></td>
                                                                <td><?php echo  number_format($row['total']); ?></td>



                                                                <th><a href="delete.php?id=<?php echo $row['order_id'] ?>">Delete</a></th>
                                                            </tr>

                                                        <?php

                                                        }

                                                        ?>

                                                        <tr>
                                                            <td>Grand Total:</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td><?php echo number_format($total); ?> RWF</td>
                                                            <td><?php echo number_format($total); ?> RWF</td>




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

                            <!--Messages Top navigation Pad-->
                            <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">
                                <div class="row">
                                    <div class="col-xl">

                                        <!-- Payment Table -->
                                        <div class="card mb-4">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">Payments</h5>
                                                <?php //if(!$due_amount==0){
                                                ?> <button type="button" id="addpayment" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addpaymentModal">
                                                    <i class="bx bx-plus"></i> New Payment
                                                </button>
                                                <?php //} 
                                                ?>
                                            </div>
                                            <div class="card-body">
                                                <!-- Venues List -->

                                               

                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Name</th>

                                                            <th>Method</th>
                                                            <th>Date</th>



                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php

                                                        $sum = 0;
                                                        $sum = 0;



$sql = $db->prepare("SELECT * FROM payments WHERE booking_id='" . $_REQUEST['booking_id'] . "'");
$sql->execute();
while ($row = $sql->fetch()) {
    ?>

                                                            <tr>
                                                                <td><?php echo $sum = $sum + 1; ?></td>


                                                                <td><?php echo number_format($row['amount']); ?> RWF</td>
                                                                <td><?php echo $row['method']; ?></td>
                                                                <td><?php echo date('d/M/Y', $row['payment_time']); ?></td>


                                                                <th></th>
                                                            </tr>

                                                        <?php

                                                        }

                                                        ?>


                                                    </tbody>
                                                </table>
                                                <!-- Print Receipt Button -->
                                        <div class="card mt-3">
                                            <div class="card-body text-center">
                                                <a href="progressive_receipt.php?booking_id=<?php echo $_REQUEST['booking_id']; ?>" target="_blank" class="btn btn-success">
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

                            <?php
$purchase_code='123214';
// Handle VSDC Sale
$lastSale = getLastId();
$branch_tin = $tin = getenv('VSDC_TIN');
$clientTin = '';
$ref = 0;
$clientTin = empty($clientTin) ? "null" : $clientTin;
$phone = empty($clientPhone) ? "null" : $clientPhone;
$salestype = "N";
$rectype = "S";
$cfmDt = date("YmdHis");
$salesDt = date("Ymd");
$taxblAmtA = 0;
$taxblAmtC = 0;
$taxblAmtD = 0;
$taxB = 0;
$taxA = 0;

if (isset($_POST['sale']) && isset($_POST['json'])) {
    // If JSON is sent as string
    if (is_string($_POST['json'])) {
        $json = json_decode($_POST['json'], true) ?? [];
    } elseif (is_array($_POST['json'])) {
        $json = $_POST['json'];
    } else {
        $json = [];
    }

    $branch_tin = (int)($json['tin'] ?? '');
    $branch_phone = $json['branch_phone'] ?? '';
    $lastSale = $json['invcNo'] ?? 0;
    $tin = (int)($json['custTin'] ?? '');
    $phone = $json['phone'] ?? '';
    $purchase_code = $json['prcOrdCd'] ?? '';
    $client_name = $json['custNm'] ?? '';
    $salestype = $json['salesTyCd'] ?? 'N';
    $rectype = $json['rcptTyCd'] ?? 'S';
    $pmtTyCd = $json['pmtTyCd'] ?? '01';
    $cfmDt = $json['cfmDt'] ?? date('YmdHis');
    $salesDt = $json['salesDt'] ?? date('Ymd');
    $totalitem = $json['totItemCnt'] ?? 0;
    $taxblAmtA = $json['taxblAmtA'] ?? 0;
    $taxblAmtB = $json['taxblAmtB'] ?? 0;
    $taxblAmtC = $json['taxblAmtC'] ?? 0;
    $taxblAmtD = $json['taxblAmtD'] ?? 0;
    $taxBamount = $json['taxAmtB'] ?? 0;
    $tot_amount = $json['totAmt'] ?? 0;
    $prdct = json_encode($json['itemList'] ?? []);
    $rcptNo = $json['invcNo'] ?? 0;
    $receipt_info = json_encode($json['receipt'] ?? []);




    // die(var_dump($_POST['json']));

    $ch = curl_init($url . '/trnsSales/saveSales');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST['json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($_POST['json'])
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response);
    $responseData = json_decode($response, true);
    $prdct = json_decode($_POST['json']);
    $prdct = json_encode($prdct->itemList);

    if (isset($responseData['resultCd'])) {
        $code = $data->resultCd;
        if ($code == '000') {
            $res = json_encode($data->data);
            $res = json_decode($res);

            $rcptNo = $res->rcptNo;
            $totRcptNo = $res->totRcptNo;
            $vsdcRcptPbctDate = $res->vsdcRcptPbctDate;
            $sdcId = $res->sdcId;
            $mrcNo = $res->mrcNo;
            $int = $res->intrlData;
            $rcptSign = $res->rcptSign;
            $amount = $tot_amount;

            // Handle stock for normal sales
            if ($salestype == 'N') {
                $jsonMaster = array();
                $jsonIO = array();

                foreach (json_decode($prdct) as $object) {
                    $itemClass = getItemClass($object->itemCd);
                    if ($itemClass != 3) {
                        $qty = getStockValue($object->itemCd) - $object->qty;

                        $jsonMaster[] = '{"tin":"' . $branch_tin . '",
                            "bhfId":"00",
                            "itemCd": "' . $object->itemCd . '",
                            "rsdQty":"' . $qty . '",
                            "regrId":"01",
                            "regrNm":"Admin",
                            "modrNm":"Admin",
                            "modrId":"01"
                        }';

                        $type_id = getItemId($object->itemCd);
                        $sql_upd = $db->prepare("UPDATE stock SET quantities = :qty WHERE type = :type");
                        $sql_upd->execute([':qty' => $qty, ':type' => $type_id]);
                    }
                }

                sendStockMaster($jsonMaster);

                $prdct = json_decode($prdct, true);
                foreach ($prdct as &$item) {
                    $item['totDcAmt'] = $item['dcAmt'];
                }
                $prdct = json_encode($prdct);

                $invoice = countIo();
                $jsonIO[] = '{"tin":"' . $branch_tin . '",
                    "bhfId":"00",
                    "sarNo":"' . $invoice . '",
                    "orgSarNo":"' . $invoice . '",
                    "regTyCd":"M",
                    "custTin":null,
                    "custNm":null,
                    "custBhfId":null,
                    "sarTyCd":"11",
                    "ocrnDt":"' . date("Ymd") . '",
                    "totItemCnt":"' . $totalitem . '",
                    "totTaxblAmt":"' . $tot_amount . '",
                    "totTaxAmt":"' . number_format($taxBamount, 2, '.', '') . '",
                    "totAmt":"' . $tot_amount . '",
                    "remark":"",
                    "regrId":"01",
                    "regrNm":"Admin",
                    "modrNm":"Admin",
                    "modrId":"01",
                    "itemList":' . $prdct . '
                }';

                sendStockIO($jsonIO);
            }

            // Save to database
            $receipt_info = addslashes('{"custTin":' . $tin . ',"custMblNo":null,"rptNo":1,"trdeNm":"NSTC","adrs":"KN 4 Ave","topMsg":"CENTRE SAINT PAUL LTD\nKG 13 Avenue 22, Kigali,   Rwanda\nTin: ' . $branch_tin . '\nPhone: ' . $branch_phone . '","btmMsg":"CIS Version 1 Powered by RRA VSDC EBM2.1 \n -------------------------------- \n Welcome","prchrAcptcYn":"N"}');
            
            $sql_inf = $db->prepare("INSERT INTO tbl_vsdc_sales SET
                tin='$branch_tin', bhfId='00', invcNo='$lastSale', orgInvcNo=0,
                custTin='$tin', custPhone='$phone', prcOrdCd='$purchase_code',
                custNm='$client_name', salesTyCd='$salestype', rcptTyCd='$rectype',
                pmtTyCd='$pmtTyCd', salesSttsCd='02', cfmDt='$cfmDt', salesDt='$salesDt',
                stockRlsDt='" . date("YmdHis") . "', totItemCnt='$totalitem',
                taxblAmtA='$taxblAmtA', taxblAmtB='$taxblAmtB', taxblAmtC='$taxblAmtC',
                taxblAmtD='$taxblAmtD', taxRtA=0, taxRtB=0, taxRtC=0, taxRtD=0,
                taxAmtA=0, taxAmtB='$taxBamount', taxAmtC=0, taxAmtD=0,
                totTaxblAmt='$tot_amount', totTaxAmt='$taxBamount', totAmt='$tot_amount',
                prchrAcptcYn='N', regrId='01', regrNm='admin', modrId='01', modrNm='admin',
                receipt='$receipt_info', itemList='$prdct', rcptNo='$rcptNo'");
            $sql_inf->execute();

            $sql_inf = $db->prepare("INSERT INTO tbl_receipts SET
                tin='$branch_tin', bhfId='00', invcNo='$lastSale', orgInvcNo=0,
                custTin='$tin', prcOrdCd='$purchase_code', custNm='$client_name',
                salesTyCd='$salestype', rcptTyCd='$rectype', pmtTyCd='$pmtTyCd',
                salesSttsCd='02', cfmDt='$cfmDt', salesDt='$salesDt',
                stockRlsDt='" . date("YmdHis") . "', totItemCnt='$totalitem',
                taxblAmtA='$taxblAmtA', taxblAmtB='$taxblAmtB', taxblAmtC='$taxblAmtC',
                taxblAmtD='$taxblAmtD', taxRtA=0, taxRtB=0, taxRtC=0, taxRtD=0,
                taxAmtA=0, taxAmtB='$taxBamount', taxAmtC=0, taxAmtD=0,
                totTaxblAmt=0, totTaxAmt='$taxBamount', totAmt='$tot_amount',
                prchrAcptcYn='N', regrId='01', regrNm='admin', modrId='01', modrNm='admin',
                receipt='$receipt_info', itemList='$prdct', rcptNo='$rcptNo'");
            $sql_inf->execute();

            // Prepare payment type display
            $pmtTycdMap = [
                '01' => 'CASH',
                '02' => 'CREDIT',
                '00' => 'CASH/CREDIT',
                '04' => 'BANK CHECK',
                '05' => 'DEBIT&CREDIT CARD',
                '06' => 'MOBILE MONEY',
                '07' => 'OTHER PAYMENTS'
            ];
            $pmtTyCd = $pmtTycdMap[$pmtTyCd] ?? 'CASH';

            $receitType = $salestype . $rectype;

            $params = [
                'receiptT' => $receitType,
                'taxbleAmount' => $taxblAmtB,
                'taxA' => $taxblAmtA,
                'taxB' => $taxBamount,
                'ref' => $purchase_code,
                'no' => $rcptNo,
                'total' => $tot_amount,
                'vs' => $cfmDt,
                'sdc' => $branch_tin,
                'mrc' => $lastSale,
                'amount' => $tot_amount,
                'product' => $prdct,
                'int' => $int,
                'sign' => $rcptSign,
                'tin' => $tin,
                'tax' => $taxBamount,
                'salestype' => $salestype,
                'rectype' => $rectype,
                'receiptNo' => $lastSale,
                'totalC' => $taxblAmtC,
                'totalD' => $taxblAmtD,
                'names' => $client_name,
                'clMob' => $phone,
                'dateData' => $cfmDt,
                'pmtTyCd' => $pmtTyCd,
                'branch_phone' => $branch_phone,
                'receipt' => $receipt_info
            ];

            $lin = http_build_query($params);

            $sqlseason = $db->prepare("SELECT * FROM system_configuration");
            $sqlseason->execute();
            $rows = $sqlseason->fetch();

            $printer = $rows['printer'];
            $receiptTypeMap = [
                'NS' => 'normal.php',
                'NR' => 'normal-refund.php',
                'CS' => 'copy.php',
                'CR' => 'copy-refund.php',
                'TS' => 'training.php',
                'TR' => 'training-refund.php',
                'PS' => 'proforma.php'
            ];

            if (isset($receiptTypeMap[$receitType])) {
                $folder = ($printer == 'paper_roll') ? 'receipt/' : 'receipt/a4/';
                echo "<script>window.open('{$folder}{$receiptTypeMap[$receitType]}?$lin', '_blank')</script>";
            }

            $insert = $db->prepare("INSERT INTO `tbl_payment`(`table_id`, `amount`, `discount`, `tot_amount`, `user`, `due_date`)
                VALUES ('$table','$price_amount','$discount_amount','$tot_amount','$waiter','$todaysdate')");
            $insert->execute();

            $sql = "UPDATE `tbl_cmd` SET `status_id` = '12' WHERE `OrderCode` = '" . $_REQUEST['c'] . "'";
            $didq = $db->prepare($sql);
            $didq->execute();

            $sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_status` = '12' WHERE `cmd_code` = '" . $_REQUEST['c'] . "'";
            $didq2 = $db->prepare($sql2);
            $didq2->execute();

            $msge = $data->resultMsg;
            echo '<meta http-equiv="refresh" content="1;URL=?resto=OurGste">';
        } else {
            $msge = $data->resultMsg;
            echo "<script>alert('" . $msge . "')</script>";
        }
    } else {
        echo "<script>alert('Lost connection to vsdc')</script>";
    }
} else {
    /* echo('No sale or json data received'); */
}
?>
                            <!--Invoice to invoce-->
                            <!-- Merged Invoice Tab with Checkout Information -->
                            <div class="tab-pane fade" id="navs-top-invoice" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <?php if ($due_amount == 0) { ?>
                                            <a href="mark_as_paid.php?id=<?php echo $_REQUEST['booking_id'] ?>" class="btn btn-info btn-sm">
                                                <i class="bx bx-check"></i> Mark as paid
                                            </a>
                                        <?php } else { ?>
                                            <a href="#" class="btn btn-danger btn-sm">
                                                Unpaid Payments
                                            </a>
                                            <?php }


                                        $invoice_booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : 0;

// Sanitize the input to prevent SQL injection
$invoice_booking_id = filter_var($invoice_booking_id, FILTER_SANITIZE_NUMBER_INT);
try {
    $checkInvoice = "SELECT * FROM invoice WHERE booking_id = ?";
    $stmt = $conn->prepare($checkInvoice);
    $stmt->bind_param("i", $invoice_booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        ?>
                                                <button id="printInvoiceBtn" class="btn btn-info btn-sm">
                                                    View Invoice
                                                </button>
                                                <?php
    } else {
        ?>
                                                <a href="?resto=room_booking_details&booking_id=<?php echo $invoice_booking_id; ?>&invoice_booking_id=<?php echo $invoice_booking_id; ?>"
                                                    class="btn btn-outline-warning btn-sm">
                                                    Generate Invoice
                                                </a>
                                                <?php
    }
    $stmt->close();
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
}
?>

                                        <!-- <button id="printInvoiceBtn" class="btn btn-info btn-sm"> -->
                                        <!--     <i class="bx bx-file"></i> Print Invoice -->
                                        <!-- </button> -->

                                        <div class="mt-2">
                                            <input type="text" class="form-control form-control-sm mb-1" placeholder="TIN Number">
                                            <input type="text" class="form-control form-control-sm" placeholder="Purchase code">
                                        </div>

                                        <div class="card mt-2 mb-2" id="invoice">
                                            <div class="card-header py-1 d-flex justify-content-between align-items-center">
                                                <h4 class="m-0">INVOICE No: ==> INV - <?php echo getInvoiceNumberByBookingId($_GET['booking_id']); ?></h4>
                                                <div style="text-align:right;">Date: <?php echo date('Y-m-d'); ?></div>
                                            </div>
                                            <div class="card-body p-1">

                                                <!-- Guest Information -->
                                                <div class="guest-info">
                                                    <h6 class="mb-1">Guest Information</h6>
                                                    <?php
            $sql = $db->prepare("SELECT * FROM tbl_acc_guest WHERE id='$guest_id'");
$sql->execute();
while ($row = $sql->fetch()) {
    ?>
                                                        <p class="mb-0 small">
                                                            <strong>Guest:</strong> <?php echo $client_name = $row['first_name'] . " " . $row['last_name']; ?><br>
                                                            <strong>Phone:</strong> <?php echo $row['phone_number'] ?>
                                                        </p>
                                                    <?php } ?>
                                                </div>

                                                <!-- Room Information -->
                                                <div class="room-info mt-1">
                                                    <?php
                                                    $sql = $db->prepare("SELECT * FROM tbl_acc_booking WHERE id='" . $_REQUEST['booking_id'] . "'");
$sql->execute();
while ($row = $sql->fetch()) {
    $ckin = $row['checkin_date'];
    $roomprice = $row['room_price'];

                                                        // Calculate nights
                                                        $today = date('Y-m-d');
                                                        $today_date = date_create($today);
                                                        $checkin_date = date_create($ckin);
                                                        $diff = date_diff($checkin_date, $today_date);
                                                        $night = $diff->format("%a");
                                                        $booked_nights = $row['duration'];

                                                        // Calculate accommodation cost
                                                        $accomodation = $roomprice * $booked_nights;
    ?>
                                                        <p class="mb-0 small">
                                                            <strong>Room:</strong> <?php echo getRoomName(getBookedRoom($row['id'])) ?><br>
                                                            <strong>Check-in:</strong> <?php echo $row['checkin_date']; ?> |
                                                            <strong>Check-out:</strong> <?php echo $row['checkout_date']; ?><br>
                                                            <strong>Nights:</strong> <?php echo $night; ?> |
                                                            <strong>Price/Night:</strong> <?php echo number_format($roomprice); ?> RWF
                                                        </p>
                                                    <?php } ?>
                                                </div>

                                                <hr class="my-1">

                                                <!-- Charges Breakdown -->
                                                <div class="charges mt-1">
                                                    <h6 class="mb-1">Charges</h6>

                                                    <table class="table-sm mb-1" style="width:100%">
                                                        <tr>
                                                            <td width="70%">Accommodation (<?php echo $booked_nights; ?> nights)</td>
                                                            <td width="30%" class="text-end"><?php echo number_format($accomodation); ?> RWF</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Restaurant</td>
                                                            <td class="text-end"><?php echo number_format($menutotal); ?> RWF</td>
                                                        </tr>

                                                        <?php
    $no = 0;
$sql = $db->prepare("SELECT * FROM orders WHERE booking_id='" . $_REQUEST['booking_id'] . "'");
$sql->execute();
                                                        $iii=1;

                                                        if($menutotal>0){

$list .= '{"itemSeq":' . $iii . ',
              "itemCd":"RW2AMU0000276","itemClsCd":"90101500","itemNm":"Accommodation",
              "bcd":null,"pkgUnitCd":"AM","pkg":1,"qtyUnitCd":"U","qty":1,
              "prc":' . $menutotal . ',"splyAmt":' . $menutotal . ',"dcRt":' . 0 . ',
              "dcAmt":' . 0 . ', "isrccCd":null,"isrccNm":null,"isrcRt":null,"isrcAmt":null,"taxTyCd":"B",
              "taxblAmt":' . $menutotal . ',"taxAmt":' . number_format(($menutotal*18/118), 2, '.', '') . ',"totAmt":' . ($menutotal) . '},';
                                                        $iii++;
                                                        }
$list .= '{"itemSeq":' . $iii . ',
              "itemCd":"RW2AMU0000276","itemClsCd":"90101500","itemNm":"Accommodation",
              "bcd":null,"pkgUnitCd":"AM","pkg":1,"qtyUnitCd":"U","qty":'.$night.',
              "prc":' . ($accomodation/$night). ',"splyAmt":' . ($accomodation) . ',"dcRt":' . 0 . ',
              "dcAmt":' . 0 . ', "isrccCd":null,"isrccNm":null,"isrcRt":null,"isrcAmt":null,"taxTyCd":"B",
              "taxblAmt":' . ($accomodation) . ',"taxAmt":' . number_format((($accomodation)*18/118),2,'.','') .',"totAmt":' . ($accomodation) . '},';
                                                        $iii++;
while ($row = $sql->fetch()) {
    $no = $no + $row['total'];
    ?>
                                                            <tr>
                                                                <td><?php echo getServiceName($row['name']); ?> (<?php echo $row['qty'] ?>)</td>
                                                                <td class="text-end"><?php echo number_format($row['total']) ?> RWF</td>
                                                            </tr>
                                                        <?php 
                                                        
                                                        
$taxBamount = number_format(($row['total']*18/118), 2, '.', '');
$list .= '{"itemSeq":' . $iii . ',
              "itemCd":"RW2AMU0000276","itemClsCd":"90101500","itemNm":"'.getServiceName($row['name']).'",
              "bcd":null,"pkgUnitCd":"AM","pkg":1,"qtyUnitCd":"U","qty":'.$row['qty'].',
              "prc":' . ($row['total']/ $row['qty']). ',"splyAmt":' . $row['total'] . ',"dcRt":' . 0 . ',
              "dcAmt":' . 0 . ', "isrccCd":null,"isrccNm":null,"isrcRt":null,"isrcAmt":null,"taxTyCd":"B",
              "taxblAmt":' . $row['total'] . ',"taxAmt":' . $taxBamount . ',"totAmt":' . $row['total'] . '},';
                                                        $iii++;
                                                        }
                                                       
                                                       
$totalitem += $row['qty'];
                                                        ?>
                                                    </table>

                                                    <hr class="my-1">

                                                    <?php
                                                    $subtotal = $accomodation + $no + $menutotal;
                                                    $vat = $subtotal * 0.18;
                                                    $total = $subtotal;
                                                    ?>

                                                    <table class="table-sm" style="width:100%">
                                                        <tr>
                                                            <td width="70%">Subtotal</td>
                                                            <td width="30%" class="text-end"><?php echo number_format($subtotal); ?> RWF</td>
                                                        </tr>

                                                        <tr>
                                                            <td>VAT (18%)</td>
                                                            <td class="text-end"><?php echo number_format($vat); ?> RWF</td>
                                                        </tr>

                                                        <tr class="font-weight-bold">
                                                            <td><strong>TOTAL</strong></td>
                                                            <td class="text-end"><strong><?php
                                                               
                                                                echo number_format($total);
 $taxblAmtB=$total; 
                                                               
$taxBamount = number_format(($taxblAmtB * 18 / 118), 2, '.', '');

$productList = $list;
$prdct =  '[' . substr($productList, 0, -1) . ']';

$receipt = '{"custTin":'.$clientTin.',"custMblNo":"'.$clientPhone.'","rptNo":'.$lastSale.',"trdeNm":"","adrs":"KN 4 Ave","topMsg":"Centre Saint Paul Kigali Ltd\nKN 31 St, Kigali, Rwanda\nTin: '.$branch_tin.'","btmMsg":"Welcome","prchrAcptcYn":"N"}';
$methods = ['cash' => '01', 'Mobile Money' => '02'];

$totalitem = $iii-1;
$json = formatingJson(
    $ref,
    '01',
    0,
    $taxblAmtB,
    $lastSale,
    $tin,
    $purchase_code,
    $client_name,
    $salestype,
    $rectype,
    $totalitem,
    $taxBamount,
    $taxblAmtB,
    $receipt,
    $prdct,
    $taxblAmtC,
    $taxblAmtD,
    $cfmDt,
    $salesDt
);

                                                        /* var_dump($json); */

                                                                ?> RWF</strong></td>

                                                        </tr>

                                                        <tr>
                                                            <td>Paid Amount</td>
                                                            <td class="text-end"><?php echo number_format($paidAmount); ?> RWF</td>
                                                        </tr>

                                                        <tr class="font-weight-bold">
                                                            <td><strong>Balance</strong></td>
                                                            <td class="text-end"><strong><?php echo number_format($total - $paidAmount); ?> RWF</strong></td>
                                                        </tr>
                                                    </table>

 <form method="POST">
                <button name="sale" class="btn btn-primary">Print RRA Receipt</button>
                <textarea style="visibility: hidden" name="json"><?=$json?></textarea>
              </form>
                                                </div>

                                                <?php
                                                // Prepare EBM data
                                                $ebmdata = '<tr><td align="left" style="padding-left: 5px;">Accommodation<br>' . $roomprice . ' </td><td align="center"><br>' . $night . '</td><td align="center"><br>' . $accomodation . '</td></tr>';

                                                if ($menutotal) {
                                                    $ebmdata = $ebmdata . '<tr><td align="left" style="padding-left: 5px;">Food & Drinks<br>' . $menutotal . ' </td><td align="center"></td><td align="center"><br>' . $no . '</td></tr>';
                                                }

                                                $_SESSION['total'] = $total;
                                                $_SESSION['discount'] = 0;
                                                $_SESSION['ebmdata'] = $ebmdata;
                                                ?>

                                                <hr class="my-3">

                                                <div class="footer-note mt-1 small">
                                                    <p class="mb-0">Thank you for your business!</p>
                                                    <?php if (($total - $paidAmount) > 0) { ?>
                                                        <p class="mb-0 text-danger">Please settle your outstanding balance before checkout.</p>
                                                    <?php } ?>
                                                </div>


                                                <?php


                                                /*
                            Creating Invoice and Insert in into database
                        */

                                                if (isset($_GET['invoice_booking_id']) && $_GET['invoice_booking_id'] != '' && !empty($_GET['invoice_booking_id'])) {
                                                    try {
                                                        $guest_id = 0;

                                                        $booking_id = $_REQUEST['booking_id'];
                                                        $sql = $db->prepare("SELECT * FROM tbl_acc_booking WHERE id = :booking_id");
                                                        $sql->bindParam(':booking_id', $booking_id);
                                                        $sql->execute();

                                                        $row = $sql->fetch();
                                                        if ($row) {
                                                            $guest_id = $row['guest_id'];
                                                        }
                                                        $booking_id = $_GET['invoice_booking_id'];

                                                        // Use proper date formatting
                                                        $inv_date = date('Y-m-d H:i:s');
                                                        $inv_guest_id = $guest_id;
                                                        $room_no = getRoomName(getBookedRoom($row['id']));
                                                        $total = number_format($total, 2, '.', '');
                                                        $paid = number_format($paidAmount, 2, '.', '');
                                                        $balance = number_format($total - $paidAmount, 2, '.', '');

                                                        // Use prepared statements for inserting data too
                                                        $stmt = $db->prepare("INSERT INTO `invoice`
                                                    (`inv_id`, `booking_id`, `inv_date`, `inv_guest_id`, `room_no`, `description`, `Total`, `Paid`, `balance`)
                                                    VALUES
                                                    (null, :booking_id, :inv_date, :inv_guest_id, :room_no, '', :total, :paid, :balance)");

                                                        $stmt->bindParam(':booking_id', $booking_id);
                                                        $stmt->bindParam(':inv_date', $inv_date);
                                                        $stmt->bindParam(':inv_guest_id', $inv_guest_id);
                                                        $stmt->bindParam(':room_no', $room_no);
                                                        $stmt->bindParam(':total', $total);
                                                        $stmt->bindParam(':paid', $paid);
                                                        $stmt->bindParam(':balance', $balance);

                                                        // Execute the query
                                                        $result = $stmt->execute();

        if ($result) {
            echo "<script>alert('Invoice created successfully'); window.location='?resto=room_booking_details&&booking_id=$booking_id'</script>";
        } else {
            echo "<script>alert('Failed to create invoice'); window.location='?resto=room_booking_details&&booking_id=$booking_id'</script>";
        }
    } catch (Exception $e) {
        echo 'Database error: ' . $e->getMessage();
    }
}
?>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <!--Checkout Tab navigation-->
                            <div class="tab-pane fade" id="navs-top-checkout" role="tabpanel">



                                <div class="row ">
                                    <div class="col-md-12 ">

                                        <div class="card">
                                            <div class="card-header">Booking summary</div>
                                            <div class="card-body">
                                                <!--<form method="POST">   -->
                                                <?php if (isset($_POST['add'])) {
                                                    $checked_in =  mysqli_real_escape_string($conn, $_POST['checkindate']);
                                                    $night =  mysqli_real_escape_string($conn, $_POST['$roomprice']);
                                                    $today =  date('Y-m-d');
                                                }
                                                ?>
                                                <!-- Balance Info -->
                                                <div class="col-md-12">
                                                    <div class="">
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-7"></div>


                                                                <?php


                                                                $sql = $db->prepare("SELECT * FROM tbl_acc_booking where id='" . $_REQUEST['booking_id'] . "'");
                                                                $sql->execute();
                                                                while ($row = $sql->fetch()) {

    ?>
                                                                    <div class="col-md-5 text-end">
                                                                        <span class="badge bg-default p-3">Room: <?php echo getRoomName(getBookedRoom($row['id'])) ?></span>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <p><strong>Checked-in at:</strong> <?php echo $ckin = $row['checkin_date']; ?></p>

                                                            <p><strong>Suppossed to check-out:</strong> <?php echo $row['checkout_date']; ?></p>
                                                            <p><strong>Today</strong> <?php echo $today = date('Y-m-d'); ?></p>
                                                            <hr>

                                                                <?php
                                                                $ckin = date_create($ckin);
$ckout = date_create($row['checkout_date']);
$today = date_create(date('Y-m-d')); // always get current date

// if today is greater than checkout date, use checkout date
if ($today > $ckout) {
    $endDate = $ckout;
} else {
    $endDate = $today;
}

// calculate number of nights
$diff = date_diff($ckin, $endDate);
$night = $diff->format("%a");
 ?>


                                                            <p><strong>Nights:</strong> <?php echo $night; ?></p>
                                                            <p><strong>Price Per Night:</strong> <?php

        $roomprice = $row['room_price'];
    echo number_format($row['room_price']); ?> RWF
                                                                </p>




                                                            <p><strong>Total accomodation :</strong> <?php

    $accomodation = $row['room_price'] * $night;
    echo number_format($row['room_price'] * $night); ?>
                                                                    RWF
                                                                </p>



                                                            <p><strong>Total addittion Service fee:</strong> <?php


    echo number_format($no); ?> RWF
                                                                </p>


                                                            <p><strong>Total restaurant:</strong> <?php


    echo number_format($menutotal); ?> RWF
                                                                </p>


                                                            <p><strong>Total To pay:</strong> <?php

    // $topay = $accomodation + $menutotal + $no;
    echo number_format($total); ?> RWF
                                                                </p>


                                                            <p><strong>Paid:</strong> <?php


    echo number_format($paidAmount); ?> RWF
                                                                </p>






                                                                <hr>
                                                                <p><strong>Balance:</strong>
                                                                    <?php
    $balance = $total - $paidAmount;
    echo number_format($balance); ?> RWF
                                                                </p>

                                                            <?php if ($balance > 0) {


                                                                    echo "You can't checkout with unpaid invoices";
                                                                } else {


                                                                    if ($booking_status_id == 5) {
                                                                    } else { ?>

                                                                <?php


                                                                    } ?>
                                                                    <a class="btn btn-info"
                                                                        href="confirmCheckout.php?balance=<?php echo $balance ?>&&booking_id=<?php echo $_REQUEST['booking_id'] ?>&&room=<?php echo getRoomName(getBookedRoom($row['id'])) ?>&&booking_amount=<?php echo $accomodation ?>&action=checkout">
                                                                        Confirm checkout</a>

                                                                <?php }
                                                                ?>
                                                                <!-- <a class="btn btn-info" href="confirmCheckout.php?balance=<?php //echo $balance?>&&booking_id=<?php //echo $_REQUEST['booking_id']?>&&room=<?php //echo getRoomName(getBookedRoom($row['id']))?>&&booking_amount=<?php //echo $accomodation?>"> Add Corporate</a> -->
                                                                <?php
                                                                if ($balance > 0) {
                                                                    ?>
                                                                    <a class="btn btn-info" href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#corporateModal">
                                                                        Add Corporate
                                                                    </a>
                                                                    <?php
                                                                }
    ?>
                                                                <?php
} ?>




                                                        </div>



                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--End of check out BTN-->

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
                            <span id="venue_type_label" class="input-group-text"></span>
                            <select class="form-control" name="service" required>
                                <?php
                                $sql = $db->prepare("SELECT * FROM  services");
$sql->execute();
while ($row = $sql->fetch()) {
    ?>
                                    <option value='<?php echo $row['service_id'] ?>'><?php echo $row['service_name'] ?>
                                    </option>
                                    <?php
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


                    <div class="mb-3">
                        <label class="form-label" for="price">Price</label>
                        <div class="input-group input-group-merge">
                            <span id="price_label" class="input-group-text"></i></span>
                            <input
                                type="text"
                                class="form-control"
                                name="price"
                                placeholder="Enter agreed price"

                                required />
                        </div>
                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-primary" name="add" value="Create">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Corporate Modal -->
<div class="modal fade" id="corporateModal" tabindex="-1" aria-labelledby="corporateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="confirmCheckout.php" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="corporateModalLabel">Add Corporate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="corporate_id">Select Corporate</label>
                    <select class="form-control" name="corporate_id" required>
                        <option value="">-- Select Corporate --</option>
                        <?php
                        $sql = $conn->query("SELECT * FROM corporates");
while ($row = $sql->fetch_assoc()) {
    echo "<option value='{$row['id']}'>{$row['name']} (TIN: {$row['tin_number']})</option>";
}
?>
                    </select>
                    <!-- Hidden Fields -->
                    <input type="hidden" name="balance" value="<?php echo $balance ?>">
                    <input type="hidden" name="booking_id" value="<?php echo $_REQUEST['booking_id'] ?>">
                    <input type="hidden" name="room_ids" value="<?php echo $room_ids ?>">
                    <input type="hidden" name="booking_amount" value="<?php echo $balance ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Send</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="addToGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">





                    <div class="mb-3">
                        <label class="form-label" for="venue_type">Group</label>
                        <div class="input-group input-group-merge">
                            <span id="venue_type_label" class="input-group-text"></i></span>
                            <select
                                class="form-control"
                                name="group"
                                required>


                                <?php
        $sql = $db->prepare("SELECT * FROM  groups");
$sql->execute();
while ($row = $sql->fetch()) {
    ?>
                                    <option value='<?php echo $row['group_id'] ?>'><?php echo $row['name'] ?></option><?php
}

?>

                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" name="addgroup" value="Create">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- modals -->
 
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

                                />
                                <!-- Add this near your amount input -->
                                <button type="button" onclick="setFullAmount()" class="btn btn-sm btn-outline-secondary">
                                    Pay Full Amount (<?php echo number_format($due_amount) ?> RWF)
                                </button>
                        </div>


                        <!-- Optional: Add a small conversion message display -->
                        <div id="conversionMessage" class="small text-muted mt-1"></div>


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
                                    <option value='airtelmoney'>Airtel Money</option>
                                    <option value='Credit'>Credit</option>


                                </select>
                            </div>
                        </div>



                        <div class="mb-3">
                            <label class="form-label" for="venue_type">Currency</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_type_label" class="input-group-text"></i></span>
                                <select id="currency" onchange="convert()"
                                    class="form-control"
                                    name="currency"
                                    required>


                                    <?php
                                    $sql = $db->prepare("SELECT * FROM  currencies");
                                    $sql->execute();
                                    while ($row = $sql->fetch()) {
                                    ?><option value='<?php echo $row['currency_id'] ?>'><?php echo $row['name'] ?> - (Rate: <?php echo $row['currency_exchange'] ?>)</option><?php
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
                            <input type="submit" class="btn btn-outline-info colr" name="addpayment" value="Create New Payment">
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>


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

<!-- <script src="js/room_booking_details.js"></script> -->
<script>
    function printInvoice() {
        var printContents = document.getElementById('invoice').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<script>
    function convert() {
        var currency = document.getElementById('currency').value;
        var amount = <?php echo $due_amount ?>;


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                var tot = amount / this.responseText;
                document.getElementById("amounts").value = tot.toFixed(2);
                // alert(this.responseText)
            }
        };
        xmlhttp.open("GET", "getCurrency.php?id=" + currency, true);
        xmlhttp.send();



    }
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
    function setFullAmount() {
        document.getElementById("amounts").value = <?php echo $due_amount ?>;
        convert(); // Trigger conversion after setting full amount
    }
</script>
<script>
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Make sure the button exists before attaching the event listener
        const printBtn = document.getElementById('printInvoiceBtn');

        if (printBtn) {
            printBtn.addEventListener('click', function() {
                // Create a new window for printing
                const printWindow = window.open('', '_blank', 'height=600,width=800');

                // Get the invoice content
                const invoiceContent = document.getElementById('invoice').innerHTML;

                // Create a complete HTML document with proper styling
                printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Invoice</title>
                    <style>
                        @page {
                            size: A4;
                            margin: 2mm; /* Further reduced page margins */
                        }
                        body {
                            font-family: Arial, sans-serif;
                            margin: 10px 20px; /* Further reduced margin */
                        }
                        .card {
                            border: 1px solid #ddd;
                            margin-bottom: 2px; /* Further reduced */
                            padding: 10px; /* Further reduced padding */
                        }
                        .card-header {
                            background-color: #f8f9fa;
                            display: flex;
                            /* justify-content: space-between;*/
                            border-bottom: 1px solid #ddd;
                            padding: 1px; /* Further reduced padding */
                        }
                        .header {
                            height: 100px; /* Further reduced header height */
                        }
                        .row {
                            display: flex;
                            border: 0px; /* Removed border */
                            margin: 0; /* No margin */
                        }
                        .col-md-6 {
                            width: 50%;
                            padding: 0 1px; /* Minimal padding */
                        }
                        .text-end {
                            text-align: right;
                        }
                        hr {
                            border: 0;
                            border-top: 1px solid #eee;
                            margin: 1px 0; /* Further reduced margin */
                        }
                        .font-weight-bold {
                            font-weight: bold;
                        }
                        .text-danger {
                            color: #dc3545;
                        }
                        .footer-note {
                            margin-top: 2px; /* Further reduced margin */
                            font-size: 10px; /* Further reduced font size */
                        }
                        /* Added for better space optimization */
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            padding: 1px;
                        }
                    </style>
                </head>
                <body>
                    <div class="card">
                    <table width="100%">
            <tr>
                <td width="40%" class="logo">
                    <img src="https://saintpaul.gope.rw/img/logo.png" alt="Company Logo">
                </td>
                <td width="60%" class="company-info">
                    <h2>Centre Saint Paul Kigali Ltd</h2>
                    <p>
                        Kigali, Rwanda<br>
                        TIN: 111477597<br>
                        Email: info@saintpaul.rw<br>
                        Tel: +250 785 285 341
                    </p>
                </td>
            </tr>
        </table>
                        ${invoiceContent}
        <div class="footer" style="padding:30px 0px;">
            <p style="color:gray; text-align:center;"><strong >Thank You for using our services</strong></p>
            <p style="color:gray; text-align:center;"><strong >Centre Saint Paul Kigali Ltd</strong> | Kigali, Rwanda | TIN: 111477597</p>

            <div class="bank-info">
                <table>
                    <tr>
                        <td width="50%">
                            <strong style="color:black;">BANK NAME:</strong> <u>BANK OF KIGALI (BK)</u><br>
                            <strong style="color:black;">SWIFT CODE:</strong> BKIGRWRW<br>
                            <strong style="color:black;">ACCOUNT NUMBER:</strong> 00041-07763514-45 /RW
                        </td>
                        <td width="50%">
                            <strong style="color:black;">BANK NAME:</strong> <u>BANK OF KIGALI (BK)</u><br>
                            <strong style="color:black;">SWIFT CODE:</strong> BKIGRWRW<br>
                            <strong style="color:black;">ACCOUNT NUMBER:</strong> 00041-07763515-46 /USD
                        </td>
                    </tr>
                </table>
            </div>
        </div>
                    </div>
                </body>
                </html>
            `);

                // Close the document and trigger print
                printWindow.document.close();

                // Wait for content to load before printing
                printWindow.onload = function() {
                    printWindow.focus(); // Focus the new window
                    printWindow.print(); // Trigger browser print dialog

                    // Close the window after printing
                    printWindow.onafterprint = function() {
                        printWindow.close();
                    };

                    // Fallback if onafterprint is not supported
                    setTimeout(function() {
                        if (!printWindow.closed) {
                            printWindow.close();
                        }
                    }, 2000);
                };
            });
        } else {
            console.error('Print button not found in the DOM');
        }
    });
</script>

