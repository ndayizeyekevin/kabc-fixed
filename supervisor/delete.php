<?php 
include '../inc/conn.php';

$sql = "DELETE FROM tbl_cmd_qty WHERE cmd_qty_id='".$_REQUEST['id']."'";
if ($conn->query($sql) === TRUE) {
  echo "<script>history.go(-1)";
} else {
  echo "Error deleting record: " . $conn->error;
}

?>