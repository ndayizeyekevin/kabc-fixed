<?php 
include_once '../inc/conn.php';

$id = $_REQUEST['id'];

$sql = "UPDATE  `tbl_acc_booking` SET `payment_status_id`=2 where  id ='".$_REQUEST['id']."'";

if ($conn->query($sql) === TRUE) {
	
//  echo "<script>alert('Booking Confirmed')</script>";
	
  echo "<script>window.location='index?resto=room_booking_details&&booking_id=$id'</script>";
  
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

 ?>
