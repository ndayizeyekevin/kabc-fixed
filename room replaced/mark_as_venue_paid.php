<?php include_once '../inc/conn.php';

$id = $_REQUEST['id'];

$sql = "UPDATE  `tbl_ev_venue_reservations` SET `status`='Confirmed' where  id ='".$_REQUEST['id']."'";

if ($conn->query($sql) === TRUE) {
	
  echo "<script>alert('Confirmed')</script>";
	
echo "<script>history.back()</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

 ?>
