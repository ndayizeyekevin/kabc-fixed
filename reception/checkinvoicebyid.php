<?php


include '../inc/conn.php';

$sql = "SELECT * FROM `tbl_vsdc_sales` WHERE transaction_id ='".$_REQUEST['no']."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {

   $no = $row['invcNo'];

  }
}else{
   // echo 0;
}


//echo $_REQUEST['no'];

?>



<?php
include '../inc/conn.php';

 $receiptNo = $no;
$sql = "SELECT receipt_url FROM `receipts` WHERE `receipt_url` LIKE '%$receiptNo%'";
$result = $conn->query($sql);

// Display results
if ($result->num_rows > 0) {
    echo "<h2>Matching invoice:</h2><table border='1'><tr>";
    while ($column = $result->fetch_field()) {
        echo "<th>" . $column->name . "</th>";
    }

    echo "</tr>";

    // Fetch rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
   ?><center><a href="receipt/copy.php?<?php echo htmlspecialchars($value) ?><?php  ?>">VIEW</a></center><?php
} else {
    echo "No matching records found.";
}

$conn->close();
?>
