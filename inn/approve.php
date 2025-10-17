<?php 
include'../inc/conn.php';

$sql = "UPDATE store_request SET status=1 WHERE req_id='".$_REQUEST['id']."'";

if ($conn->query($sql) === TRUE) {
	$id = $_REQUEST['id'];
  echo "<script>alert('approved');
  window.location='index?resto=viewRequests'</script>";
} else {
  echo "Error updating record: " . $conn->error;
}
?>

