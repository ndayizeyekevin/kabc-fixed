<style>
    table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 0px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
</style>



<?php include '../inc/conn.php';
include '../inc/config.php';

function getItemStock($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM tbl_item_stock where item='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['qty'];
  }
}

}

function getItemName($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['item_name'];
  }
}

}

function getItemPrice($id) {
    include '../inc/conn.php';

    // Get the item from tbl_items
    $itemQuery = "SELECT * FROM tbl_items WHERE item_id = '$id'";
    $itemResult = $conn->query($itemQuery);

    if ($itemResult && $itemResult->num_rows > 0) {
        $item = $itemResult->fetch_assoc();
        $defaultPrice = $item['price'];

        // Check tbl_progress for the latest new_price
        $progressQuery = "SELECT new_price FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1";
        $progressResult = $conn->query($progressQuery);

        if ($progressResult && $progressResult->num_rows > 0) {
            $progress = $progressResult->fetch_assoc();
            if ($progress['new_price'] > 0) {
                return $progress['new_price'];
            }
        }

        // Fallback to default item price
        return $defaultPrice;
    }

    // If item not found, return null or any fallback value you want
    return null;
}


function getItemUnitId($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['item_unit'];
  }
}

}


function getItemUnitName($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM tbl_unit where unit_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['unit_name'];
  }
}

}


		
function getSupplierName($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM suppliers where id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['name'];
  }
}

}	

?>
<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <style type="text/css"> * {margin:0; padding:0; text-indent:0; }
         p { color: black; font-family:Tahoma, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt; margin:0pt; }
         .s1 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 12pt; }
         .s2 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 7.5pt; }
         .s3 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 7.5pt; }
         table, tbody {vertical-align: top; overflow: visible; }
      </style>
   </head>
   <body style="background-color:#e1e1e1">
       
  <center>     <button onclick="printInvoice()">Print To PDF </button></center>
       
    <div  id="content" style="background-color:white;margin-top:50px;margin-left:10%;padding:20px;margin-right:10%;height:100%;padding-top:100px">
  <?php include '../holder/printHeader.php'?>
      
      
          <table border="0" style="width:100%">
          <tr><td>
    
      </td>

      
            <td>
          <p class="s1" style="padding-top: 6pt;text-indent: 0pt;text-align: center;border-width: thin;padding:20px">
              
              <?php
                  
                  $code = $_GET['req'];
                            $stmt_req = $db->prepare("SELECT * FROM tbl_requests 
                            WHERE req_code = '".$_GET['req']."'");
                            $stmt_req->execute();
                            $getrows = $stmt_req->fetch();
                            $req_date = $getrows['requested_date'];
                  
                  ?>
              
              
REQUISITION FORM  <?PHP ECHO  $_REQUEST['req'] ?> | REQUESTED ON  <?php echo $req_date; ?> 
     </p>
      </td>
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>   
      
         <table style="border:2px solid #000" width="100%" class="table table-striped " id="dataTables-example">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Item Name </th>
                            <th> Unit </th>
                              <th> QTY.IN </th>
                              <th> RE.QTY </th>
                              <th> PRICE </th>
                                <th> Amount </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $ii = 0;
                        $amount = 0;
                        $result = $db->prepare("SELECT * FROM tbl_request_details
                        INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
                        WHERE req_code = '".$_GET['req']."'");
                        $result->execute();
                        for($i=0; $row = $result->fetch(); $i++){
                                  $ii++;
                                  $total = (float)getItemPrice($row['items']) * (float)$row['quantity'];
                                	$amount =  $amount  + $total;
                            
                            ?>
                            <tr>
                                <td><?php echo $ii; ?></td>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo getItemUnitName(getItemUnitId($row['items'])); ?></td>
                                 <td><?php echo getItemStock($row['items']); ?></td>
                                 <td><?php echo $row['quantity']; ?></td>
                                      <td><?php echo number_format((float)getItemPrice($row['items']),2); ?></td>
                                       <td><?php echo number_format((float)getItemPrice($row['items']) * (float)$row['quantity']); ?></td>
                            </tr>
                            <?php
                        }
                        ?>

                              <tr>
                                <td colspan='6'>Total</td>
                          
                                <td><?php echo number_format($amount)?></td>
                            </tr>
                    </tbody>
                </table>
                                    
                                    
                                    
                   <br><br><br><br>                    
                                    
            <table border="0" style="width:100%">
          <tr>
              <td>
                  
                  <?php
                  
                  $code = $_GET['req'];
                            $stmt_req = $db->prepare("SELECT * FROM tbl_requests 
                            INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
                            WHERE req_code = '".$_GET['req']."'");
                            $stmt_req->execute();
                            $getrows = $stmt_req->fetch();
                            $status = $getrows['status'];
                  
                  ?>
   <center><b> Prepared by:<br>(Names and signature) <br><?php echo $getrows['f_name']." ".$getrows['l_name']; ?> </b></center>
   
    
    
      </td>
      
 
      
            <td>
          
                
   <center><b> Verified by:<br>(Names and signature) <br>........................ </b></center>
 
   
            </td>
            
            
                
            <td>
          
    <center><b> Approved by:<br>(Names and signature) <br>........................ </b> </center>


            </td>
      </tr></table>
      <br><br><br><br>
      <br><br><br><br>
     <strong> Printed At: <?php echo date('H:m:s'); ?></strong>
      
      
      
    
   </body>
</html>

<script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>




<!DOCTYPE html>