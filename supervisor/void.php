<?php  include '../inc/conn.php';

$sql = "SELECT * FROM tbl_cmd_qty WHERE cmd_qty_id='".$_REQUEST['id']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    
 
 $time = time();
$sqli = "INSERT INTO `voided_items` (`id`, `item`, `qty`, `table_id`, `servant`, `created_at`,order_code) VALUES (NULL, '".$row['cmd_item']."', '".$row['cmd_qty']."', '".$row['cmd_table_id']."', '".$row['Serv_id']."', '$time', '".$row['cmd_code']."');";

if ($conn->query($sqli) === TRUE) {
    


$sqlii = "DELETE FROM tbl_cmd_qty WHERE cmd_qty_id='".$_REQUEST['id']."'";
if ($conn->query($sqlii) === TRUE) {
    
    
    
$sql2 = "SELECT * FROM tbl_cmd_qty WHERE cmd_code='".$row['cmd_code']."'";
$result2 = $conn->query($sql2);

if ($result2->num_rows > 0) {
  // output data of each row
  while($row2 = $result2->fetch_assoc()) {
      
        echo 1;
  }}else{
      
      
$sql3 = "DELETE FROM tbl_cmd WHERE id='".$row['cmd_code']."'";

if ($conn->query($sql3) === TRUE) {
    echo 1;
} else {
  echo "Error deleting record: " . $conn->error;
}
      
  }
    
    
    
    

} else {
  echo "Error deleting record: " . $conn->error;
}




} else {
  echo "Error: " . $sqli . "<br>" . $conn->error;
}
 
 
    

  }
}









?>