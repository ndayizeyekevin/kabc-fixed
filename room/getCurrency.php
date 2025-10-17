<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = $_REQUEST['id'];

	include'../inc/conn.php';
	$sql = "SELECT * FROM  currencies WHERE currency_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

	echo $row['currency_exchange'];
	
  }
}else{
    echo $id;
}
	
	
