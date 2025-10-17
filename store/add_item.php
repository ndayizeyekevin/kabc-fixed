<?php
include '../inc/conn.php';

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




if(ISSET($_GET['ac'])){
    $code = $_GET['ac'];
    $date = date("Y-m-d");
    
    $sql = $db->prepare("SELECT * FROM tbl_request_details WHERE req_code = '".$code."'");
    $sql->execute();
    while($rows = $sql->fetch()){
       $currqty = $rows['quantity'];
       $stmt = $db->prepare("SELECT * FROM tbl_item_stock WHERE item = '".$rows['items']."'");
       $stmt->execute();
       $rowss = $stmt->fetch();
       $stkqty = $rowss['qty'];
       
       $remainqty = $stkqty-$currqty;
       
       $sql_upd_qty = $db->prepare("UPDATE tbl_item_stock SET `qty`='$remainqty' WHERE `item`='".$rows['items']."' ");
	    $sql_upd_qty->execute();
	    
	    $sql_upd = $db->prepare("UPDATE tbl_requests SET `status`='8' WHERE `req_code`='".$code."' ");
	    $sql_upd->execute();

      $sql_chk2 = $db->prepare("SELECT * FROM tbl_progress WHERE item = '".$rows['items']."' ORDER BY prog_id DESC LIMIT 1");
      $sql_chk2->execute();
      $rowcount = $sql_chk2->rowCount();
          $fetch = $sql_chk2->fetch();
          $lastqty = $fetch['end_qty'];
          $tot = $lastqty-$currqty;
          
      $stmts = $db->prepare("INSERT INTO `tbl_progress` (`date`,`out_qty`,`last_qty`,`item`, `end_qty`) 
      VALUES ('$date','$currqty','$lastqty', '".$rows['items']."','$tot')");
      $stmts->execute();
    }
    $msg = "Confirmed Successfully";
    echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=request">';
}
?>
<?php
function fill_product($db){
  $output= '';

  $select = $db->prepare("SELECT * FROM tbl_items WHERE item_status = 1");
  $select->execute();
  $result = $select->fetchAll();

  foreach($result as $row){
    $output.='<option value="'.$row['item_id'].'">'.$row["item_name"].'</option>';
  }

  return $output;
}
?>
<?php
    function productcode() {
    
        return getReqCode($_SESSION['user_id']);
    }
    $pcode=productcode();
    ?>


	<?php if(isset($_POST['addRequest'])){



		$item = $_POST['item'];
		$qty = $_POST['qty'];
		$req_code = $_REQUEST['id'];
		$supplier = $_REQUEST['supplier'];

	    $item_price = (float)getItemPrice($item);
		$sql = "INSERT INTO `request_store_item` (`id`, `item_id`, `qty`, `pur_qty`, `pur_price`, `req_id`) VALUES (NULL, '$item', '$qty', '$qty', '$item_price', '$req_code');";

if ($conn->query($sql) === TRUE) {
	 echo "<script>window.location='?resto=view_purchase&&id=$req_code&&supplier=$supplier'</script>";
} else {
	 //echo "Error: " . $sql . "<br>" . $conn->error;
}


	}?>

<style>
	   .form-ic-cmp {
	       margin-bottom: 10px;
	   }
</style>
<div class="container mt-5">
	   <div class="row justify-content-center">
	       <div class="col-lg-8">
	           <div class="card">
	               <div class="card-header bg-primary text-white">
	                   <h4 class="card-title mb-0">
	                       <i class="fas fa-plus-circle me-2"></i>Add Forgotten Item
	                   </h4>
	               </div>
	               <div class="card-body">
	                   <?php if($msg){?>
	                       <div class="alert alert-success alert-dismissible fade show" role="alert">
	                           <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
	                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	                       </div>
	                   <?php } else if($msge){?>
	                       <div class="alert alert-danger alert-dismissible fade show" role="alert">
	                           <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
	                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	                       </div>
	                   <?php } ?>

	                   <a href="?resto=view_purchase&&id=<?php echo $_REQUEST['id']; ?>&&supplier=<?php echo $_REQUEST['supplier']; ?>" class="btn btn-secondary mb-3">
	                       <i class="fas fa-arrow-left me-2"></i>Go Back
	                   </a>

	                   <form action="" method="POST" enctype="multipart/form-data">
	                       <div class="row g-3">
	                           <div class="col-md-6">
	                               <label for="item" class="form-label">
	                                   <strong>Select Item</strong> <span class="text-danger">*</span>
	                               </label>
	                               <select name="item" id="item" class="selectpicker form-control" data-live-search="true" required>
	                                   <option value="">Choose an item...</option>
	                                   <?php
	                                   $result = $db->prepare("SELECT * FROM tbl_items");
	                                   $result->execute();
	                                   while($fetch = $result->fetch()){
	                                   ?>
	                                       <option value='<?php echo $fetch['item_id']?>'><?php echo $fetch['item_name']?></option>
	                                   <?php } ?>
	                               </select>
	                           </div>

	                           <div class="col-md-4">
	                               <label for="qty" class="form-label">
	                                   <strong>Quantity</strong>
	                               </label>
	                               <input type="text" name="qty" id="qty" class="form-control" placeholder="Enter quantity" pattern="^\d+(\.\d{1,3})?$" inputmode="decimal" required>
	                           </div>

	                           <div class="col-md-2 d-flex align-items-end">
	                               <button type="submit" name="addRequest" class="btn btn-primary w-100">
	                                   <i class="fas fa-plus me-2"></i>Add Item
	                               </button>
	                           </div>
	                       </div>
	                   </form>
	               </div>
	           </div>
	       </div>
	   </div>
</div>

<script>
	   $(document).ready(function() {
	       // Initialize Bootstrap Select
	       $('.selectpicker').selectpicker();

	       // Accepting Numbers and decimal places only
	       const input = document.getElementById('qty');
	       input.addEventListener('input', function () {
	           this.value = this.value
	               .replace(/[^0-9.]/g, '')       // allow only digits and dot
	               .replace(/(\..*)\./g, '$1');    // prevent more than one dot
	       });
	   });
</script>
