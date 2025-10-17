
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template-free">
<!-- Header -->

<?php 
include '../inc/conn.php';
$id = $_REQUEST['booking'];




$sql = "UPDATE  `tbl_acc_booking` SET `booking_type`='Group' where  id ='$id'";

if ($conn->query($sql) === TRUE) {
	
	

 echo "<script>alert('Changed to group successfully');</script>";
  echo "<script>window.history.go(-1);</script>"; 

	
	
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

 ?>

