<?php
include'../inc/conn.php';
session_start();
$approver_name = $_SESSION['f_name'] . " " . $_SESSION['l_name'];

$sql = "UPDATE store_request SET  daf='".$conn->real_escape_string($approver_name)."' WHERE req_id='".$_REQUEST['id']."'";

if ($conn->query($sql) === TRUE) {
	$id = $_REQUEST['id'];
  echo "<script>alert('approved');
  window.location='index?resto=home'</script>";
} else {
  echo "Error updating record: " . $conn->error;
}
?>

