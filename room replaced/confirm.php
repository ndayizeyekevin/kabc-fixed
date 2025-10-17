<?php 
// $id = $_REQUEST['id'];

// if(isset($_GET['id']) && !empty($_GET['id'])){
//     include '../inc/conn.php';

// $sql = "SELECT * FROM tbl_acc_booking where id='$id'";
// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {
// $room = getRoomName(getBookedRoom($row['id']));
// }}	  

// function getRoomName($id){
// 	$dbuser = 'panatech_st_resto'; $dbhost = 'panatechrwanda.com'; $dbpass = "panatech_st_resto"; $conn = mysqli_connect($dbhost, $dbuser, $dbpass,'panatech_st_resto');

// 	$sql = "SELECT * FROM  tbl_acc_room WHERE id='$id'";
// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {

// 	return $row['room_number'];
	
//   }
// }
	
	
// }

// }



// function getBookedRoom($id){
// 	$dbuser = 'panatech_st_resto'; $dbhost = 'panatechrwanda.com'; $dbpass = "panatech_st_resto"; $conn = mysqli_connect($dbhost, $dbuser, $dbpass,'panatech_st_resto');
// 	$sql = "SELECT * FROM  tbl_acc_booking_room WHERE booking_id='$id'";
//     $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {

// 	return $row['room_id'];
	
//   }
// }
	
	
// }
	 

// $today = date('Y-m-d');
// $sql = "UPDATE  `tbl_acc_booking` SET `booking_status_id`=2,checkin_date='$today' where  id ='".$_REQUEST['id']."'";

// if ($conn->query($sql) === TRUE) {
	
//  $status = updateRoomStatus($room,2);
// if($status==1){
//  echo "<script>alert('Booking comfirmed successfully');</script>";
//  $id = $_REQUEST['id'];
//  echo "<script>window.history.go(-1);</script>"; 
// }
	
	
// } else {
//   echo "Error: " . $sql . "<br>" . $conn->error;
// }



// function updateRoomStatus($id,$status){
// include '../inc/conn.php';

// $sql = "UPDATE `tbl_acc_room` SET status_id='$status' WHERE room_number='$id'";
// if ($conn->query($sql) === TRUE) {

// return 1;

// } else {
//   return 0;
// }
	
// }



// if(isset($_GET['cancel']) && !empty($_GET['cancel'])){
//     $sql = "UPDATE `tbl_acc_booking` SET `booking_status_id` = 1 WHERE id = '".$_GET['cancel']."'";
    
//     if($conn->query($sql)){
        	
//  $status = updateRoomStatus($room,3);
// if($status==1){
//  echo "<script>alert('Booking canceled successfully');</script>";
//  $id = $_REQUEST['id'];
//  echo "<script>window.history.go(-1);</script>"; 
// }
//     }
// }



?>



<?php
include '../inc/conn.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cancel_id = isset($_GET['cancel']) ? intval($_GET['cancel']) : 0;

function getBookedRoom($conn, $booking_id) {
    $sql = "SELECT room_id FROM tbl_acc_booking_room WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($room_id);
    if ($stmt->fetch()) {
        return $room_id;
    }
    return null;
}

function getRoomName($conn, $room_id) {
    $sql = "SELECT room_number FROM tbl_acc_room WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $stmt->bind_result($room_number);
    if ($stmt->fetch()) {
        return $room_number;
    }
    return null;
}

function updateRoomStatus($conn, $room_number, $status) {
    $sql = "UPDATE tbl_acc_room SET status_id = ? WHERE room_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $status, $room_number);
    return $stmt->execute();
}

// ===== Check-in logic =====
if ($id > 0) {
    $room_id = getBookedRoom($conn, $id);
    $room_number = getRoomName($conn, $room_id);
    $today = date('Y-m-d');

    $sql = "UPDATE tbl_acc_booking SET booking_status_id = 2 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if (updateRoomStatus($conn, $room_number, 2)) {
            echo "<script>alert('Booking confirmed successfully'); window.history.go(-1);</script>";
        }
    } else {
        echo "Error updating booking: " . $stmt->error;
    }
}

// ===== Cancel logic =====
if ($cancel_id > 0) {
    $room_id = getBookedRoom($conn, $cancel_id);
    $room_number = getRoomName($conn, $room_id);

    $sql = "UPDATE tbl_acc_booking SET booking_status_id = 3 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cancel_id);
    if ($stmt->execute()) {
        if (updateRoomStatus($conn, $room_number, 3)) {
            echo "<script>alert('Booking canceled successfully'); window.history.go(-1);</script>";
        }
    } else {
        echo "Error canceling booking: " . $stmt->error;
    }
}
?>




