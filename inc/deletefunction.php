<?php 
$id = $_REQUEST['id'];
$tablename = $_REQUEST['name'];
$key = $_REQUEST['key'];

include 'conn.php';		

$sql = "DELETE FROM $tablename WHERE $key = '$id'";
if ($conn->query($sql) === TRUE) {
  echo 1;
} else {
  echo "Error deleting record: " . $conn->error;
}

?>