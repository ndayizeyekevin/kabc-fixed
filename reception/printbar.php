<?php session_start();
$printedIdresto = $_SESSION['printedIdbar'];
include  '../inc/conn.php';
$printedIdresto = substr($printedIdresto, 0, -1); 
$sql = "UPDATE tbl_cmd_qty SET printed = '1' WHERE  cmd_qty_id IN ($printedIdresto)";

if ($conn->query($sql) === TRUE) {
 echo  1;
} else {
 // echo "Error updating record: " . $conn->error;
}

?>