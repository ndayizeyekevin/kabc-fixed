<?php 

function getVenuCustomer($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_ev_customers where id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['names'];
  }
}

}
function getVenuname($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_ev_venues where id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['venue_name'];
  }
}

}

function getVenuAmount($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_ev_venue_rates where venue_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['amount'];
  }
}

}



// FUNCTION TO INVOICE NUMBER BY BOOKING ID

function getInvoiceNumberByBookingId($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM invoice where booking_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['inv_id'];
  }
}

}



function getSaleItemPrice($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['price'];
  }
}

}

function getBookingTotalByMonth($year,$month){
  //  $year = 2024;
        	include'conn.php';

                      $sum=0;
                     $sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 6 OR booking_status_id = 5";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                          
                          
                        $date = strtotime($row['checkin_date']);

                        if(date("m",$date)==$month && date("Y",$date)==$year){
                            $sum=$sum+$row['booking_amount'];
                        } 
                          
                      }}
                      
                      
                      return $sum ;
                      
}



function getSalesTotalByMonth($year,$month){
  //  $year = 2024;
    
    	include'conn.php';




$sum  =0;


   $sql = "SELECT * FROM tbl_cmd_qty where cmd_status = 12";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                          
                          
                        $date = strtotime($row['created']);

                        if(date("m",$date)==$month && date("Y",$date)==$year){
                            $sum=$sum+getSaleItemPrice($row['cmd_item']);
                        } 
                          
                          //
                      }}
                    
                    

                      return   $sum ;
                      
                      
                      
                      
                      
                      
                      
                                 	
                                   
                      
                      
                      
                      
                      
}



function getReqCode($userId) {
   // Timestamp in milliseconds (8 bytes)
   $timestamp = round(microtime(true) * 1000);

   // Random number (0–1000)
   $random = rand(0, 1000);

   // Pack into binary string
   $data = pack('J', $timestamp) . pack('N', $userId) . chr($random);

   // Base64 encode (URL-safe, no padding)
   $shortCode = rtrim(strtr(base64_encode($data), '+/', '-_'), '=');

   return "REQ-" . $shortCode;
}






function getRoleNAME($id){
	include'conn.php';
	$sql = "SELECT * FROM tbl_acc_roles WHERE role_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['role_name'];
	
  }
}
	
	
}


function getServiceName($id){
	include'conn.php';
	$sql = "SELECT * FROM services WHERE service_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['service_name'];
	
  }
}
	
	
}





function getServicePrice($id){
	include'conn.php';
	$sql = "SELECT * FROM services WHERE service_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['service_price'];
	
  }
}
}




function updateRoomStatus($id,$status){
 include'conn.php';


$sql = "UPDATE `tbl_acc_room` SET status_id='$status' WHERE room_number='$id'";
if ($conn->query($sql) === true) {

return 1;

} else {
  return $id;
}

	
	
}





function getRoomStatus($id){
	include'conn.php';
	$sql = "SELECT * FROM tbl_acc_room WHERE room_class_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['status_id'];
	
  }
}
	
	
}



















function getRoomStatusName($id){
	include'conn.php';
	$sql = "SELECT * FROM tbl_acc_room_status WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['status_name'];
	
  }
}
	
	
}


function updateRoomStatusById($id,$status){
 include'conn.php';


$sql = "UPDATE `tbl_acc_room` SET status_id='$status' WHERE id='$id'";
if ($conn->query($sql) === TRUE) {

return 1;

} else {
  return $id;
}

	
	
}







function getVenueName($id){
	include'conn.php';
	$sql = "SELECT * FROM tbl_ev_venues WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['venue_name'];
	
  }
}
	
	
}


function getCustomerNames($id){
	include'conn.php';
	$sql = "SELECT * FROM tbl_ev_customers WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['names'];
	
  }
}
	
	
}



function getGuestNationality($id){
	include'conn.php';
	$sql = "SELECT * FROM tbl_acc_guest WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['nationality'];
	
  }
}
	
	
}



function getGuestBookingOption($id){
	include'conn.php';
	$sql = "SELECT * FROM tbl_acc_booking_room_options WHERE booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['option_id'];
	
  }
}
	
	
}





function getBookedRoom($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_booking_room WHERE booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['room_id'];
	
  }
}
	
	
}



function getRoomCapacity($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_room WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['capacity'];
	
  }
}

}


function getRoomType($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_bed_type WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['bed_type_name'];
	
  }
}
	
	
}



function getRoomName($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_room WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['room_number'];
	
  }
}
	
	
}



function getRoomClass($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_room WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['room_class_id'];
	
  }
}
	
	
}

function getRoomClassType($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_room_class WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['class_name'];
	
  }
}
	
	
}



function getRoomPrice($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_room_class WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['base_price'];
	
  }
}
	
	
}





function getGuestNames($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_guest WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['first_name']." " .$row['last_name'];
	
  }
}
	
	
}




function getGuestDetail($id,$field){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_guest WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row[$field];
	
  }
}
	
	
}




function getFetaureName($id){
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_feature WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['feature_name'];
	
  }
}
	
	
}



function getCurrencyValue($id){
	include'conn.php';
	$sql = "SELECT * FROM  currencies WHERE currency_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['currency_exchange'];
	
  }
}
}


function getCurrencyName($id){
	include'conn.php';
	$sql = "SELECT * FROM  currencies WHERE currency_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	return $row['name'];
	
  }
}
}



function getRoomClassFeature($id){
	$feature = "";
	include'conn.php';
	$sql = "SELECT * FROM  tbl_acc_room_class_feature WHERE room_class_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	$feature = $feature.getFetaureName($row['feature_id'])."<br>";
	
  }
}
	return $feature ;
	
}





function getExtraTotal($id){
	include'conn.php';


$booking_amount = 0;
 
$amountToPay = 0;
$sql = "SELECT * FROM orders where booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
 $amountToPay = $amountToPay + $row['price'];
}}

$guest_booking = 0;
$sql = "SELECT * FROM guest_booking where booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
 $guest_booking = $guest_booking + $row['amount'];
}}

$booking_amount  = $booking_amount +  $guest_booking;
$amountToPay =  $amountToPay +  $booking_amount ;
	
return  $amountToPay;
}





function getSingleBookingTotal($id){
	include'conn.php';


$sql = "SELECT * FROM tbl_acc_booking where id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
 $booking_amount = $row['booking_amount'];
}}
 
$amountToPay = 0;
$sql = "SELECT * FROM orders where booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
 $amountToPay = $amountToPay + $row['price'];
}}

$guest_booking = 0;
$sql = "SELECT * FROM guest_booking where booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
 $guest_booking = $guest_booking + $row['amount'];
}}

$booking_amount  = $booking_amount +  $guest_booking;
$amountToPay =  $amountToPay +  $booking_amount ;
	
return  $amountToPay;
}









function getSingleReservationTotal($id){
	include'conn.php';


$sql = "SELECT * FROM tbl_ev_venue_rates where venue_id='$id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
  $booking_amount = $row['amount']; 
}}

$amountToPay = 0;
$sql = "SELECT * FROM venu_orders where booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $amountToPay = $amountToPay + $row['price'];


}}

$amountToPay =  $amountToPay +  $booking_amount;
return  $amountToPay;
}

function getSingleReservationDueTotal($id){
	include'conn.php';
$paidAmount = 0;
$sql = "SELECT * FROM venue_payments where booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 
 $paidAmount = $paidAmount + $row['amount'];

}}

return   $paidAmount;
}




function getSingleBookingDueTotal($id){
    global $conn;
	$paidAmount = 0;
$sql = "SELECT * FROM payments where booking_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 $paidAmount = $paidAmount + $row['amount'];
 

}}
return  $paidAmount;

}


// GET GROUP NAME
function getGroupName($id){
    include 'conn.php';
    $groupName = "";
    $sql= "SELECT * FROM `groups` WHERE group_id = '$id'";
    $result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

 $groupName = $row['name'];
 
}}


return $groupName;
    
}


?>