<?php include '../inc/conn.php';


    $client=$_POST['client'];
    $order=$_REQUEST['order'];
   
         $sql = "UPDATE tbl_cmd SET room_client='$client' WHERE OrderCode='$order'";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Client successfuly added'); window.location='javascript:history.go(-1)'</script>";
} else {
  echo "Error updating record: " . $conn->error;
}

  



                                ?>