<?php
 include '../inc/conn.php';
function lastday(){
include '../inc/conn.php';
$sql = "SELECT * FROM days ORDER BY id DESC LIMIT 1 ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
         $last = $row['to_id'];
     }}
      return $last;

}
$sql = "SELECT * FROM tbl_cmd_qty ORDER BY cmd_qty_id DESC LIMIT 1 ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

      $lastid = $row['cmd_qty_id'];


  }}
$date = date('Y-m-d');
$time = time();
$from = lastday();
$sql = "INSERT INTO `days` (`id`, `date`, `from_id`, `to_id`, `created_at`) VALUES (NULL, '$date', '$from', '$lastid', '$time');";
if ($conn->query($sql) === TRUE) {
  echo "<script>Day Successfull closed</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}


?>