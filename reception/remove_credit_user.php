<?php include '../inc/conn.php';


$order=$_REQUEST['order'];
$sql = "UPDATE tbl_cmd SET creadit_user='0' WHERE OrderCode='$order'";
if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Client successfuly Removed'); window.location='javascript:history.go(-1)'</script>";
} else {
  echo "Error updating record: " . $conn->error;
}

  



                                ?>