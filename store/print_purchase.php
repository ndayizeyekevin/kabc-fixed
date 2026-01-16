<style>
    table {
            border-collapse: collapse;
            font-size: 16px;
            font-weight:700;
        }
        th, td {
            border: 0px solid #ddd;
            padding:10px;
        }
        th {
            background-color: #f2f2f2;
            font-weight:900;
        }
        
        
        .itemst{
            border: 1px solid #ddd;
        }
        
       
</style>




<?php include '../inc/conn.php';
include '../inc/config.php';

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



function getCurrentStock($id){
	
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
    
   </head>
   <body style="background-color:#e1e1e1">
       
  <center>     <button onclick="printInvoice()" class="btn btn-primary">Print To PDF </button></center>
       
    <div  id="content" style="background-color:white;margin-top:50px;margin-left:10%;padding:20px;margin-right:10%;height:100%;padding-top:100px">
  <?php include '../holder/printHeader.php'?>
      
      
          <table border="0" style="width:100%">
          <tr>
      
      <td>
          <br>
<center><H3>LOCAL PURCHASE ORDER # <?= $_GET['id'];?> </H3></center>
 </br>
 
 
         

      </td>
      
          
      </tr></table>
      
      
      
      
      
      <div class="container">
        <table border="0">
          <tr>
      
            <td>
            <h5><b>Supplier:   			
              <?php 
                $selected_supplier = $db->prepare("SELECT supplier FROM store_request WHERE req_id= :req_id");
                $selected_supplier->execute([':req_id' => $_REQUEST['id']]);
                $supplier_row = $selected_supplier->fetch(PDO::FETCH_ASSOC);
                $supplier_id = $supplier_row['supplier'];
                //  Select Supplier Name

                $name_stmt = $db->prepare("SELECT name FROM suppliers WHERE id= :supplier_id");
                $name_stmt->execute([':supplier_id' => $supplier_id]);
                $name_row = $name_stmt->fetch(PDO::FETCH_ASSOC);
                echo $name_row['name'];
            
            
            

            ?>
            
            </b> </h5>
              

            </td>
        </tr>
      
      <tr>
        <td>
  
         <h5><b> LPO Number:     <?php echo $_REQUEST['id']?>      </b></h5>
 
       </td>

      </tr>
      
      <tr>

        <td>
  
         <h5> <b>DATE:     <?php $result = $db->prepare("SELECT * FROM  store_request WHERE req_id='".$_REQUEST['id']."'");
                                         $result->execute();
                                     while($fetch = $result->fetch()){
                                     echo $fetch['request_date']; 
                                          $request = $fetch['request_from']; 
                                     
                                     }   ?>   </b></h5>
  
       </td>
      </tr>
      
          
      </table>
      
      
      <br>     <br>    
      
      
 <center>
      
            <table class="itemst" border="0" width="100%">
                                 <thead>
                                    <tr>
                                        <th style="border: 1px solid black; padding:10px; font-weight:700;">#</th>
                                        <th style="border: 1px solid black; padding:10px; font-weight:700;">Item </th>
                                        <th style="border: 1px solid black; padding:10px; font-weight:700;">QTY</th>
                                        <th style="border: 1px solid black; padding:10px; font-weight:700;">U.P</th>
                                        <th style="border: 1px solid black; padding:10px; font-weight:700;">T.P</th>

                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    $amount = 0;


                                		$result = $db->prepare("SELECT * FROM  request_store_item WHERE req_id='".$_REQUEST['id']."'");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
                                		    $i++;
                                		   
                        $total =  getItemPrice($fetch['item_id']) * $fetch['qty'];
                        $amount = $amount + $total;
                                		    
                                     	?>
                                     	<tr>
                                     	    <td style="border: 1px solid black; padding:10px; font-weight:700;" width="5%"><?php echo $i; ?></td>
                                            <td style="border: 1px solid black; padding:10px; font-weight:700;" width="20%"><?php echo getItemName($fetch['item_id']); ?></td>
                                            <td align="right" style="border: 1px solid black; padding:10px; font-weight:700;" width="10%"><?php echo number_format((float)$fetch['qty'],3); ?></td>
                                            <td align="right" style="border: 1px solid black; padding:10px; font-weight:700;" width="10%"><?php echo number_format((float)getItemPrice($fetch['item_id']),3); ?></td>
                                            <td align="right" style="border: 1px solid black; padding:10px; font-weight:700;" width="15%"><?php echo number_format((float)(getItemPrice($fetch['item_id']) * $fetch['qty']),3); ?></td>
                                           
                                          
                                         
                                     	</tr>
         <?php
	}
?>




  	<tr>
	<td colspan="4" style="border: 1px solid black; padding: 5px;"><H4><b>Total</b></H4></td>
	<td align="right" style="border: 1px solid black; padding-right: 10px;"><H4><b><?php echo number_format($amount, 2)?> </b></H4></td>

	</tr>



                            </tbody>
                          
                        </table>
                                    
                                    
                              </center>      
                                    
                   <br>   <br>      <br>      <br>                    
                                    
                  <table border="0" style="width:100%">
          <tr>
              
              
              
                   <td>
 
    <?php
    // Fetch user's info
    $query = $db->prepare("SELECT * FROM store_request WHERE req_id = :req_id");
    $query->execute([':req_id' => $_REQUEST['id']]);
    $data = $query->fetch(PDO::FETCH_ASSOC);
    $daf = $data['daf'];
    $md = $data['md'];
    $store_keeper = $data['user_info'];
    ?>
    <b>Store Keeper</b> <br>Ordered by: <?php echo $store_keeper; ?> <br><br> Signature .........................
    
      </td>
              <!-- <td>
 
    <b>Controler</b> <br>Verified by: ....................... <br><br> Signature .........................
    
      </td> -->
      
      <td>
       
  

    
    
      <b><?php    $by= $_SESSION['f_name']." ". $_SESSION['l_name'];
    //   if($request==$by){
    //   echo "Operation Manager";
    //   }else{
      echo "DAF";//} ?></b> <br>Verified by: <?php echo $daf; ?> <br><br> Signature .........................
      </td>
      
            <td>
          
                
 
  <b>Managing Director</b> <br>Approved: <?php echo $md; ?> <br><br> Signature .........................
            </td>
            
        
  
      </tr></table>
        <br>   <br>   <br>   <br> 
      </div>
      
    
   </body>
</html>

<script> 
  function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } 
</script>




<!DOCTYPE html>
