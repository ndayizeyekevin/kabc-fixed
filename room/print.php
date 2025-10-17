<?php 
session_start(); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
// Initialize date range variables (default to today)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Convert dates to datetime format for timestamp comparison
$start_datetime = $start_date . ' 00:00:00';
$end_datetime = $end_date . ' 23:59:59';

error_reporting(E_ALL);
date_default_timezone_set('Africa/Kigali');
include '../inc/function.php';
include '../inc/conn.php';	

$today = date('Y-m-d');
?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <style type="text/css"> * {margin:0; padding:0; text-indent:0; }
         p { color: black; font-family:Tahoma, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt; margin:0pt; }
         .s1 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 20pt; }
         .s2 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 20pt; }
         .s3 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 20pt; }
         table, tbody {vertical-align: top; overflow: visible; }
      </style>
   </head>
   <body style="background-color:#e1e1e1">
       
  <center>   
      <button style="margin-top:30px;padding:10px;background-color:red;color:white;" onclick="printInvoice()" >Print To PDF </button></center>
       
      <div id="content" style="background-color:white;margin-top:50px;margin-left:10%;padding:20px;margin-right:10%;min-height:100%;padding-top:100px">
<?php include '../holder/printHeader.php'?>

<?php 
// Define common date conditions
// $inhouse_condition = "booking_status_id = 6 
//     AND checkin_date <= '$end_datetime' 
//     AND checkout_date >= '$start_datetime'";
$inhouse_condition = "booking_status_id = 6";
    
$checkout_condition = "booking_status_id = 5";

$expected_condition = "booking_status_id = 2 
    AND checkin_date BETWEEN '$start_datetime' AND '$end_datetime'";

$departure_condition = "booking_status_id = 6 
    AND checkout_date BETWEEN '$start_datetime' AND '$end_datetime'";

$booking_condition = "booking_status_id IN (1, 2,6) 
    AND created_at BETWEEN '$start_datetime' AND '$end_datetime'";

$roomstatus_condition = "booking_status_id IN (1,2,6) 
    AND checkin_date <= '$end_datetime' 
    AND checkout_date >= '$start_datetime'";

// Report headers with date range
$report_titles = [
    'inhouse' => "IN-HOUSE REPORT FROM $start_date TO $end_date",
    'checkutrepo' => "CHECKOUT REPORT FROM $start_date TO $end_date",
    'expected' => "EXPECTED ARRIVALS FROM $start_date TO $end_date",
    'breakfast' => "BREAKFAST REPORT FROM $start_date TO $end_date",
    'dep' => "DEPARTURES FROM $start_date TO $end_date",
    'booking' => "BOOKING LIST FROM $start_date TO $end_date",
    'rent' => "RENTAL REPORT FROM $start_date TO $end_date",
    'room' => "ROOMING REPORT FROM $start_date TO $end_date",
    'roomstatus' => "ROOM STATUS REPORT FROM $start_date TO $end_date"
];
?>

<?php if(isset($_REQUEST['page']) && isset($report_titles[$_REQUEST['page']])): ?>
    <br><br>
    <table border="0" class="table">
        <tr>
            <td colspan="8">
                <center><h3><?= $report_titles[$_REQUEST['page']] ?></h3></center>
            </td>
        </tr>
    </table>
    <br><br>

    <?php if($_REQUEST['page']=='inhouse'): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Guest Names</th>
                    <th>Room Name</th>
                    <th>Room Type</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Count</th>
                    <th>Room Rate</th>
                    <th>Pax</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
                $sql = "SELECT * FROM tbl_acc_booking WHERE $inhouse_condition";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= getGuestNames($row['guest_id']) ?></td>
                    <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                    <td><?= getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                    <td><?= $row['checkin_date'] ?></td>
                    <td><?= $row['checkout_date'] ?></td>
                    <td><?= $row['duration'] ?></td>
                    <td><?= number_format($row['room_price']) ?></td>
                    <td><?= $row['num_adults'] + $row['num_children'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
    <?php elseif($_REQUEST['page']=='checkutrepo'): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Guest Names</th>
                    <th>Room Name</th>
                    <th>Room Type</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Count</th>
                    <th>Room Rate</th>
                    <th>Pax</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
                // $sql = "SELECT * FROM tbl_acc_booking WHERE $checkout_condition";
                $sql = "SELECT * FROM tbl_acc_booking WHERE booking_status_id = 5";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): 
	$checkin = new DateTime($row['checkin_date']);
    $checkout = new DateTime($row['checkout_date']);
    $duration = $checkin->diff($checkout)->days;
                ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= getGuestNames($row['guest_id']) ?></td>
                    <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                    <td><?= getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                    <td><?= $row['checkin_date'] ?></td>
                    <td><?= $row['checkout_date'] ?></td>
                    <td><?= $duration ?></td>
                    <td><?= number_format($row['room_price']) ?></td>
                    <td><?= $row['num_adults'] + $row['num_children'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif($_REQUEST['page']=='expected'): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Guest Names</th>
                    <th>Company</th>
                    <th>Nationality</th>
                    <th>Room Name</th>
                    <th>Room Type</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Room Rate</th>
                    <th>Debtor</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
                // $sql = "SELECT * FROM tbl_acc_booking WHERE $expected_condition";

$today = Date('Y-m-d');

// $sql = $db->prepare("SELECT * FROM tbl_acc_booking where booking_status_id =2");
$sql = "SELECT * FROM tbl_acc_booking WHERE checkin_date = '$today' AND booking_status_id NOT IN (3,5)";
          // die(var_dump($sql));
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= getGuestNames($row['guest_id']) ?></td>
                    <td><?= $row['company'] ?></td>
                    <td><?= getGuestNationality($row['guest_id']) ?></td>
                    <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                    <td><?= getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                    <td><?= $row['checkin_date'] ?></td>
                    <td><?= $row['checkout_date'] ?></td>
                    <td><?= number_format($row['room_price']) ?></td>
                    <td>Himself/herself</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif($_REQUEST['page']=='breakfast'): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Guest Names</th>
                    <th>Company</th>
                    <th>Nationality</th>
                    <th>Room Name</th>
                    <th>Room Type</th>
                    <th>Adults</th>
                    <th>Children</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
        //         $sql = "SELECT * FROM tbl_acc_booking 
        //                 WHERE $inhouse_condition
        //   /* AND booking_option = 1 */";
                $sql = "SELECT * FROM tbl_acc_booking 
                        WHERE booking_status_id = 6";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= getGuestNames($row['guest_id']) ?></td>
                    <td><?= $row['company'] ?></td>
                    <td><?= getGuestNationality($row['guest_id']) ?></td>
                    <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                    <td><?= getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                    <td><?= $row['num_adults'] ?></td>
                    <td><?= $row['num_children'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif($_REQUEST['page']=='dep'): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Guest Names</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Rate</th>
                    <th>Days</th>
                    <th>Extra</th>
                    <th>Total Amount</th>
                    <th>Balance</th>
                    <th>Debtor</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
                $sql = "SELECT * FROM tbl_acc_booking WHERE $departure_condition";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): 
	$checkin = new DateTime($row['checkin_date']);
    $checkout = new DateTime($row['checkout_date']);
    $duration = $checkin->diff($checkout)->days;
                    $total = ($row['room_price'] * $duration) + getExtraTotal($row['id']);
                    $balance = $total - getSingleBookingDueTotal($row['id']);
                ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= getGuestNames($row['guest_id']) ?></td>
                    <td><?= $row['checkin_date'] ?></td>
                    <td><?= $row['checkout_date'] ?></td>
                    <td><?= number_format($row['room_price']) ?></td>
                    <td><?= $duration ?></td>
                    <td><?= number_format(getExtraTotal($row['id'])) ?></td>
                    <td><?= number_format($total) ?></td>
                    <td><?= number_format($balance) ?></td>
                    <td>Himself/herself</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif($_REQUEST['page']=='booking'): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Guest Name</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Rate</th>
                    <th>Days</th>
                    <th>Company</th>
                    <th>Residence</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
                $sql = "SELECT * FROM tbl_acc_booking WHERE $booking_condition";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                    <td><?= getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                    <td><?= getGuestNames($row['guest_id']) ?></td>
                    <td><?= $row['checkin_date'] ?></td>
                    <td><?= $row['checkout_date'] ?></td>
                    <td><?= number_format($row['room_price']) ?></td>
                    <td><?= $row['duration'] ?></td>
                    <td><?= $row['company'] ?: '-' ?></td>
                    <td>-</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif($_REQUEST['page']=='rent'): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Guest Names</th>
                    <th>Company</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Count</th>
                    <th>Room Rate</th>
                    <th>Debtor</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
                $sql = "SELECT * FROM tbl_acc_booking WHERE $inhouse_condition";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                    <td><?= getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                    <td><?= getGuestNames($row['guest_id']) ?></td>
                    <td><?= $row['company'] ?></td>
                    <td><?= $row['checkin_date'] ?></td>
                    <td><?= $row['checkout_date'] ?></td>
                    <td><?= $row['duration'] ?></td>
                    <td><?= number_format($row['room_price']) ?></td>
                    <td>Himself/herself</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif($_REQUEST['page']=='room'): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Guest Names</th>
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Nationality</th>
                        <th>ID/Passport</th>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 0;
                    $sql = "SELECT * FROM tbl_acc_booking WHERE $inhouse_condition";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?= ++$no ?></td>
                        <td><?= getGuestNames($row['guest_id']) ?></td>
                        <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?= $row['checkin_date'] ?></td>
                        <td><?= $row['checkout_date'] ?></td>
                        <td><?= getGuestDetail($row['guest_id'],'nationality') ?></td>
                        <td>
                            <?= getGuestDetail($row['guest_id'],'identification') ?: 
                                getGuestDetail($row['guest_id'],'passport_number') ?>
                        </td>
                        <td><?= $row['company'] ?></td>
                        <td><?= getGuestDetail($row['guest_id'],'phone_number') ?></td>
                        <td><?= getGuestDetail($row['guest_id'],'email_address') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif($_REQUEST['page']=='roomstatus'): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Room Type</th>
                        <th>Guest Names</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Room Rate</th>
                        <th>Company</th>
                        <th>Telephone</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 0;
                    $sql = "SELECT * FROM tbl_acc_booking WHERE $roomstatus_condition";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?= ++$no ?></td>
                        <td><?= getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?= getGuestNames($row['guest_id']) ?></td>
                        <td><?= $row['checkin_date'] ?></td>
                        <td><?= $row['checkout_date'] ?></td>
                        <td><?= number_format($row['room_price']) ?></td>
                        <td><?= $row['company'] ?></td>
                        <td><?= getGuestDetail($row['guest_id'],'phone_number') ?></td>
                        <td><?= getGuestDetail($row['guest_id'],'email_address') ?></td>
                        <td><?= getRoomStatusName(getRoomStatus(getRoomClass(getBookedRoom($row['id'])))) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <br><br>
    <td> 
        Printed By: <?= $_SESSION['f_name']." ".$_SESSION['l_name'] ?> <br>  
        Printed on: <?= date('Y-m-d H:i:s') ?>                     
    </td>
<?php endif; ?>
</div>
    
    
    
   </body>
</html>

<script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>


