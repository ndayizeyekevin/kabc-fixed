<?php

include '../inc/conn.php';

$sql = "DELETE FROM store_request WHERE req_id='".$_REQUEST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "<script>window.location='index?resto=requestStore'</script>";
} else {
  echo "Error deleting record: " . $conn->error;
}

?>