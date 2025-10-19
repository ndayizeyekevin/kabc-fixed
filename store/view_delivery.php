<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../inc/conn.php';	

function getItemName($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    return $row['item_name'];
  }
}

}

function getAverage($conn,int $itemId, float $newPrice, float $newQuantity): float {
  try {
      $stmt = $conn->prepare("SELECT end_qty, new_price * end_qty AS stock_value 
                             FROM tbl_progress 
                             WHERE item = :id 
                             ORDER BY prog_id DESC 
                             LIMIT 1");
      $stmt->execute([':id' => $itemId]);
      
      $current_stock_value = 0;
      $current_stock_qty = 0;
      
      if ($stmt->rowCount() > 0) {
          $result = $stmt->fetch();
          $current_stock_value = (float)$result['stock_value'];
          $current_stock_qty = (float)$result['end_qty'];
      }

      $new_incoming_stock = $newPrice * $newQuantity;

      $new_stock_value = $current_stock_value + $new_incoming_stock;
      $new_total_qty =  $current_stock_qty + $newQuantity;

      if ($new_total_qty == 0) {
          return 0.0;  
      }
      
      return $new_stock_value / $new_total_qty;
      
  } catch (\Throwable $th) {
      throw $th; 
  }
}

// function getItemPrice($id){
	
// include '../inc/conn.php';	

// $item = "SELECT * FROM tbl_items where item_id='$id' ";
// $result = $conn->query($item);

// $tbl_progress = $conn ->query ("SELECT * FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1;");

// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {
//     while($row2 = $tbl_progress->fetch_assoc()){
//         return  $row2['new_price'] > 0 ? $row2['new_price'] : $row['price'] ;
//     }
//   }
// }

// }
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


// GET AVERAGE COST
// function getAverage($item_id, $new_qty, $new_price){
//     include '../inc/conn.php';	

//     // Default opening quantity and price
//     $opening_qty = 0;
//     $opening_price = 0;

//     // Get last transaction from tbl_progress
//     $sql = "SELECT * FROM tbl_progress WHERE item='$item_id' ORDER BY prog_id DESC LIMIT 1";
//     $result = $conn->query($sql);

//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         $opening_qty = $row['end_qty']; // opening = last known closing qty
//         $opening_price = $row['new_price']; // last known average price
//     } else {
//         // No previous transactions — fallback to item price
//         $opening_price = getItemPrice($item_id);
//     }

//     // Total value = opening stock + new delivery
//     $total_value = ($opening_qty * $opening_price) + ($new_qty * $new_price);
//     $total_qty = $opening_qty + $new_qty;

//     // Avoid division by zero
//     if ($total_qty == 0) return $new_price;

//     $average_cost = $total_value / $total_qty;
//     return $average_cost;
// }




if (isset($_GET['delete']) && isset($_GET['req_id'])) {
    
    $req_id = intval($_GET['req_id']);
    
    $deleteQuery = "DELETE FROM request_store_item WHERE id = '$req_id'";
    
    if ($conn->query($deleteQuery) === TRUE) {
        echo "<script>
                window.location.href = '?resto=delivery'; // Redirect using JS
              </script>";
        exit();
    }
}
	
function getAverageOld($db, $item_id, $new_unit_cost, $new_qty) {
    // Fetch old quantity and weighted value from request_store_item
    $stmt = $db->prepare("
        SELECT 
            SUM(del_qty) AS total_qty,
            SUM(del_qty * del_price) AS total_value
        FROM request_store_item
        WHERE item_id = ? AND del_qty > 0 AND del_price > 0
    ");
    $stmt->execute([$item_id]);

    $old_qty = 0;
    $old_total_value = 0;

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $old_qty = (float)$row['total_qty'];
        $old_total_value = (float)$row['total_value'];
    }

    // New total value and quantity
    $new_total_value = $new_unit_cost * $new_qty;
    $total_qty = $old_qty + $new_qty;

    if ($total_qty > 0) {
        return round(($old_total_value + $new_total_value) / $total_qty, 2);
    } else {
        return round($new_unit_cost, 2); // fallback for first delivery
    }
}


// RECEIVING NEW STOCK


if (isset($_POST['id'])) {
    $req_id = intval($_POST['id']);
    $dated = $_POST['confirm_date'];
    $date = date("Y-m-d");

    // Get all items in the request
    // $sql_chk = $db->prepare("SELECT * FROM request_store_item WHERE req_id = ? order by id DESC LIMIT 1");
    $sql_chk = $db->prepare("SELECT * FROM request_store_item WHERE req_id = ? order by id DESC"); // REMOVED LIMIT ONE 
    $sql_chk->execute([$req_id]);

    while ($rows = $sql_chk->fetch(PDO::FETCH_ASSOC)) {
        $item = $rows['item_id'];
        $qty = (float)$rows['del_qty'];
        $unit_price = (float)$rows['del_price'];

        if ($qty <= 0 || $unit_price <= 0) continue;

        // Check stock existence
        $sql_chk2 = $db->prepare("SELECT * FROM tbl_item_stock WHERE item = ?");
        $sql_chk2->execute([$item]);

        if ($sql_chk2->rowCount() > 0) {
            // Existing stock: update qty
            $stock_row = $sql_chk2->fetch();
            $existing_qty = (float)$stock_row['qty'];
            $new_stock_qty = $existing_qty + $qty;

            // Update item stock
            $sqlqty = $db->prepare("UPDATE tbl_item_stock SET qty = ? WHERE item = ?");
            $sqlqty->execute([$new_stock_qty, $item]);

            // Get last end_qty from tbl_progress
            $sql_last = $db->prepare("SELECT end_qty FROM tbl_progress WHERE item = ? ORDER BY prog_id DESC LIMIT 1");
            $sql_last->execute([$item]);
            $lastqty = 0;
            if ($sql_last->rowCount() > 0) {
                $last_row = $sql_last->fetch();
                $lastqty = (float)$last_row['end_qty'];
            }
            $end_qty = $lastqty + $qty;
            // Calculate average cost using true formula
            $averageCost = getAverage($db, $item, $unit_price, $qty);

            // Insert into progress
            $insert = $db->prepare("INSERT INTO tbl_progress (date, in_qty, last_qty, item, end_qty, new_price) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$date, $qty, $lastqty, $item, $end_qty, $averageCost]);

        } else {
            // First time stock: insert into tbl_item_stock
            $insert_stock = $db->prepare("INSERT INTO tbl_item_stock (item, qty, date_tkn) VALUES (?, ?, ?)");
            $insert_stock->execute([$item, $qty, $date]);

            // First entry to tbl_progress
            $insert_progress = $db->prepare("INSERT INTO tbl_progress (date, in_qty, last_qty, item, end_qty, new_price) 
                                             VALUES (?, ?, 0, ?, ?, ?)");
            $insert_progress->execute([$date, $qty, $item, $qty, $unit_price]);
        }
    }

    // Mark the request as confirmed
    $sql = $db->prepare("UPDATE store_request SET status = 1, confirmed = ? WHERE req_id = ?");
    $sql->execute([$dated, $req_id]);

    echo "<script>window.location.href = '?resto=viewDelivery&&id=$req_id';</script>";
}



if(isset($_GET['ac'])){
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
		
		
		$supplier = $_POST['supplier'];
		$vat = $_POST['vat'];
		
	


$sql = "UPDATE store_request SET supplier='$supplier',vat='$vat',saved=1 WHERE req_id='".$_REQUEST['id']."'";
if ($conn->query($sql) === TRUE) {
 echo "<script>window.location='purchase.php'</script>";
} else {
 // echo "Error: " . $sql . "<br>" . $conn->error;
}
		
		
	}?>
	
<style>
    .form-ic-cmp{
        margin-bottom:10px;
    }
</style>
<!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
		    
		    <?php if($msg){?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
              </div>
            <?php } 
             else if($msge){?>
                 
             <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
              </div>
            <?php } ?>
			<div class="row" hidden>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="fa fa-cog"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Request Raw Materials</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				
	
				<h4>Good Received Note</h4>
				
                    <div class="data-table-list">
			
			
			
			<?php
                                    $i = 0;
                                		$result = $db->prepare("SELECT * FROM  store_request where req_id='".$_REQUEST['id']."'");
                                        $result->execute();
                                		while($fetch = $result->fetch()){

                                		    ?>
											<div class="col-lg-8">
											<h4>REQUEST Number: <?php echo $fetch['req_code']?></H4>
											</div>
											

                      
                           <?php
                           $status = (int)$fetch['status'];
                           $daf = isset($fetch['daf']) ? trim($fetch['daf']) : '';
                           $md = isset($fetch['md']) ? trim($fetch['md']) : '';

                           if($status == 0 && !empty($daf) && !empty($md)){
                               // Approved by MD - show confirm button
                           ?>
                            <form method="POST" id="confirmRequestForm" onsubmit="return handleConfirmSubmit(this)">

                                    <div class="col-lg-2">
                                        <input type="date" name="confirm_date" class="form form-control" required>
                                        <input type="hidden" name="id" value="<?php echo $_REQUEST['id']?>">
                                    </div>
                           <div class="col-lg-2">
											<button type="submit" class="btn btn-info" id="confirmBtn">
												<span class="btn-text">Confirm this request</span>
												<span class="btn-loading" style="display:none;">
													<i class="fa fa-spinner fa-spin"></i> Processing...
												</span>
											</button>
										</div>

                            </form>

										<?php } elseif($status == 0 && (empty($daf) || empty($md))){
                                            // Pending or Reviewed but not yet approved by MD - show wait button
                                        ?>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-warning" onclick="showWaitMessage()">
                                                <i class="fa fa-clock-o"></i> Waiting for Approval
                                            </button>
                                        </div>
                                        <?php } ?>
                  
											<?php
                  }?>
										
										
	  <?php
                                 
                                		$result = $db->prepare("SELECT * FROM  store_request where req_id='".$_REQUEST['id']."'");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
 
											
											$supplier = getSupplierName($fetch['supplier']);
											$vat = $fetch['vat'];
											
                                     	}?>							
			
			<!-- <hr> -->
					<BR>
										<BR>
					
                        <?php if(empty($_GET['req'])){ ?>
                        <div id="content">
                            <a href="?resto=addDeliveryItem&&req=<?php echo $_REQUEST['id']?>" class="btn btn-primary">Add Item</a>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped table-bordered" id="content">
                                 <thead>
                                     
                                             <tr>
                                        <th colspan="3"></th>
                                        <th colspan="3">Purchase </th>
                                         <th colspan="3">Delivery</th>
                                    
                                   
                                       

                                     
                                    </tr>
                                     
                                     
                                    <tr>
                                        <th>#</th>
                                        <th>Item </th>
                                         <th>Unit</th>
                                        <th>Qty</th>
                                        <th>P.U</th>
                                        <th>T.P</th>
                                          <th>Quantity</th>
                                          <th>P.U</th>
                                          <th>T.P</th>
                                             <th>Action</th>
                                   
                                       

                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
									
								     	$amount = 0;
									   $del_amount =0;
                                		$result = $db->prepare("SELECT * FROM  request_store_item WHERE req_id='".$_REQUEST['id']."'");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
                                		    $i++;
								// 			$total =  getItemPrice($fetch['item_id']) * $fetch['qty'];
											$total =  $fetch['pur_price'] * $fetch['pur_qty'];
											
											$del_total = $fetch['del_price'] * $fetch['del_qty'];
											
						    $amount = $amount + $total;
							$del_amount = $del_amount + $del_total;
											 
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><?php echo getItemName($fetch['item_id']); ?></td>
                                             <td><?php echo getItemUnitName(getItemUnitId($fetch['item_id'])); ?></td>
                                            <td><?php echo number_format((float)($fetch['pur_qty']),3); ?> </td>
                                            <!--<td><?php //echo $fetch['pur_price']; ?> </td>-->
                                            <td><?php echo number_format((float)($fetch['pur_price']),3); ?> </td> 
                                            <td><?php echo number_format((float)($fetch['pur_qty'] * $fetch['pur_price']),3); ?> </td>
                                       
                                           
                                           
                                             <td><?php echo number_format((float)$fetch['del_qty'],3); ?> </td>
                                             <td><?php echo number_format((float)$fetch['del_price'],3); ?></td>
                                              <td><?php echo number_format((float)($fetch['del_qty'] * $fetch['del_price']), 3); ?></td>
                                                 <td><a class="btn btn-primary" href="?resto=editDelivery&&delivery=5&&item=<?php echo $fetch['id']?>&&pitem=<?php echo $_REQUEST['id']; ?>">Edit</a></td>
                                                 <td><a class="btn btn-danger" href="?resto=viewDelivery&&req_id=<?php echo $fetch['id']?>&delete=true" 
                                               onclick="return confirm('Are you sure you want to delete?')"
                                                 >Delete</a></td>
                                          
                                         
                                     	</tr>
         <?php
	}
?>




  	<tr>
	<td colspan="2"><H4>Total</H4></td>
	<td></td>
	<td></td>

	<td></td>
	<td><H4><?php echo number_format($amount,3)?> </H4></td>
	
		<td></td>

	<td></td>
	<td><H4><?php echo number_format($del_amount, 3)?> </H4></td>
	</tr>
	
	  	<tr>
	<th colspan="8"><H4>Balance</H4></th>
	<th><H4><?php echo number_format($amount - $del_amount)?> </H4></th>
	</tr>
	


                            </tbody>
                          
                        </table>
						   </div>
						 </div>
						<div id="editor"></div>

						
                        </div>
                        <?php
                        }
                        elseif(!empty($_GET['req'])){
                            $code = $_GET['req'];
                            $stmt_req = $db->prepare("SELECT * FROM tbl_requests 
                            INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
                            WHERE req_code = '".$_GET['req']."'");
                            $stmt_req->execute();
                            $getrows = $stmt_req->fetch();
                            $status = $getrows['status'];
                        ?>
                        <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                        Type: Raw Materials <br>
                        Date Requested: <?php echo $getrows['requested_date']; ?>
                        </div>
                        <div class="panel-title pull-right">
                        Requested By: <?php echo $getrows['f_name']." ".$getrows['l_name']; ?><br>
                        Date Required: <?php echo $getrows['required_date']; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <hr>
                <div class="panel panel-default" style="padding:10px;">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                        <?php if($getrows['status'] == 3){ ?>
                        Requested Items <label class="label label-info"><b><i>PENDING</i></b></label>
                        <?php 
                    }
                    else{
                        ?>
                        Requested Items <label class="label label-success"><b><i>COMPLETED</i></b></label>
                        <?php
                    }
                     ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <br>
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Item Name </th>
                            <th> Quantity </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $ii = 0;
                        $result = $db->prepare("SELECT * FROM tbl_request_details
                        INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
                        WHERE req_code = '".$_GET['req']."'");
                        $result->execute();
                        for($i=0; $row = $result->fetch(); $i++){
                            $ii++;
                            ?>
                            <tr>
                                <td><?php echo $ii; ?></td>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>
                    </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalone" role="dialog">
 
    </div>
    
    <!-- add item -->
<div class="modal fade" id="adds" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Item Name</label>
            <select class="form-control selectpicker" data-live-search="true" name="item" required>
            <?php
            $sql = $db->prepare("SELECT * FROM tbl_items");
            $sql->execute();
            for($i=0; $row = $sql->fetch(); $i++){
                ?>
                <option value="<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></option>
                <?php
                }
                ?>
            </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required>
            <input type="hidden" name="code" class="form-control" value="<?php echo $_GET['req']; ?>">
          </div>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="add_item" class="btn btn-success">Add</button>
      </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rmv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Item Issues</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Item Name </th>
                            <th> Quantity </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $ii = 0;
                        $result = $db->prepare("SELECT * FROM tbl_request_details
                        INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
                        WHERE req_code = '".$_GET['req']."'");
                        $result->execute();
                        for($i=0; $row = $result->fetch(); $i++){
                            $ii++;
                            ?>
                            <tr>
                                <td><?php echo $ii; ?></td>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><a href="?resto=request&req=<?php echo $code; ?>&rmv=<?php echo $row['detail_id']; ?>">Remove</td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!-- <button type="submit" name="add_item" class="btn btn-success">Add</button> -->
      </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
   <script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>
    <script>
 $(document).ready(function(){
      $(document).on('click','.btn_addOrder', function(){
        var html='';
        html+='<tr>';
        html+='<td><select class="form-control selectpicker productid" data-live-search="true" name="items[]" required><option value="">--Select Item--</option><?php
        echo fill_product($db)?></select></td>';
        html+='<td><input autocomplete="off" type="number" class="form-control quantity" name="quantity[]" required placeholder="0"></td>';
        html+='<td><input autocomplete="off" type="text" class="unit" disabled></td>';
        html+='<td><button type="button" name="remove" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-remove"></i></button></td>'

        $('#myOrder').append(html);

        $('.productid').on('change', function(e){
          var productid = this.value;
          var tr=$(this).parent().parent();
          $.ajax({
            url:"getproduct.php",
            method:"get",
            data:{id:productid},
            success:function(data){
              $("#btn").html('<button type="submit" class="btn btn-success" name="add">Request</button>');
              tr.find(".quantity").val();
              tr.find(".unit").val(data["unit_name"]);
            }
          })
        })
         $('.selectpicker').selectpicker();
      })

      $(document).on('click','.btn-remove', function(){
        $(this).closest('tr').remove();
        calculate(0,0);
        $("#paid").val(0);
      })
    });
	
	// Prevent double submission of confirm request form
	var formSubmitted = false;
	
	function handleConfirmSubmit(form) {
		// Check if form was already submitted
		if (formSubmitted) {
			return false;
		}
		
		// Validate the date field
		var dateField = form.querySelector('input[name="confirm_date"]');
		if (!dateField.value) {
			alert('Please select a confirmation date');
			return false;
		}
		
		// Mark as submitted
		formSubmitted = true;
		
		// Get the button
		var btn = document.getElementById('confirmBtn');
		
		// Disable the button
		btn.disabled = true;
		
		// Change button appearance
		btn.querySelector('.btn-text').style.display = 'none';
		btn.querySelector('.btn-loading').style.display = 'inline';
		
		// Allow form submission
		return true;
	}
	
	// Reset if user navigates back
	window.addEventListener('pageshow', function(event) {
		if (event.persisted) {
			formSubmitted = false;
			var btn = document.getElementById('confirmBtn');
			if (btn) {
				btn.disabled = false;
				btn.querySelector('.btn-text').style.display = 'inline';
				btn.querySelector('.btn-loading').style.display = 'none';
			}
		}
	});

	// Show wait message for pending or reviewed requests
	function showWaitMessage() {
		alert('Please wait for the Managing Director to approve this request before confirming delivery.');
	}

  </script>
