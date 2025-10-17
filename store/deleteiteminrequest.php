<?php

include '../inc/conn.php';

$sql = "DELETE FROM request_store_item WHERE id='".$_REQUEST['id']."'";

if ($conn->query($sql) === TRUE) {
 echo "<script>history.go(-1)</script>";
} else {
  echo "Error deleting record: " . $conn->error;
}

?>