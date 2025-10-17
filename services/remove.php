<?php include '../inc/conn.php';


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
//$client=$_POST['client'];

$order=$_REQUEST['order'];
$sql = "UPDATE tbl_cmd SET room_client='0' WHERE OrderCode='$order'";
if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Client successfuly Removed'); window.location='javascript:history.go(-1)'</script>";
} else {
  echo "Error updating record: " . $conn->error;
}

  



                                ?>