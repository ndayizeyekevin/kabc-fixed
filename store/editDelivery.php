<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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



function getItemPrice($id){
	
include '../inc/conn.php';	

$item = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($item);

$tbl_progress = $conn ->query ("SELECT * FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1;");

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    while($row2 = $tbl_progress->fetch_assoc()){
        return  $row2['new_price'] > 0 ? $row2['new_price'] : $row['price'];
    }
  }
}

}




function getAverage($id, $ne_price, $ne_qty) {
    include '../inc/conn.php';
    $totalValue = 0;
    $totalQty = 0;

    // 1. Get item_id from request_store_item
    $sql = "SELECT item_id FROM request_store_item WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) return false;

    $row = $result->fetch_assoc();
    $item_id = $row['item_id'];

    // 2. Get first stock entry of this month
    $firstDayOfMonth = date('Y-m-01');
    $sql = "SELECT * FROM tbl_progress 
            WHERE item = '$item_id' AND date >= '$firstDayOfMonth' 
            ORDER BY prog_id ASC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $entry = $result->fetch_assoc();

        $open_qty = $entry['last_qty'];
        $open_price = $entry['new_price'];
        $open_value = $open_qty * $open_price;

        $in_value = $ne_price * $ne_qty;

        $totalValue = $open_value + $in_value;
        $totalQty = $open_qty + $ne_qty;

        $average = ($totalQty > 0) ? ($totalValue / $totalQty) : $open_price;

        // Get last end_qty to use as last_qty for this new entry
        $last_sql = "SELECT end_qty FROM tbl_progress WHERE item = '$item_id' ORDER BY prog_id DESC LIMIT 1";
        $last_result = $conn->query($last_sql);
        $last_qty = 0;
        if ($last_result->num_rows > 0) {
            $last_row = $last_result->fetch_assoc();
            $last_qty = $last_row['end_qty'];
        }

        $new_end_qty = $last_qty + $ne_qty;
        $date = date('Y-m-d');

        // Insert new entry with calculated average cost
        $insert_sql = "INSERT INTO tbl_progress (date, in_qty, last_qty, out_qty, item, end_qty, new_price) 
                       VALUES ('$date', '$ne_qty', '$last_qty', 0, '$item_id', '$new_end_qty', '$average')";

        if ($conn->query($insert_sql)) {
            return $item_id;
        } else {
            return "Error inserting stock transaction: " . $conn->error;
        }
    } else {
        return "No opening stock found for this month.";
    }
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
	
	
	<?php 
	if(isset($_POST['addRequest'])){
		
		try {
			$db->beginTransaction();
			
			$qty = floatval($_POST['qty']);
			$price = floatval($_POST['price']);
			$date = date("Y-m-d");
			
			// Step 1: Get the item_id, old values, and request status
			$sql_chk = $db->prepare("SELECT rsi.*, sr.request_date, sr.status as request_status 
			                          FROM request_store_item rsi 
			                          LEFT JOIN store_request sr ON rsi.req_id = sr.req_id 
			                          WHERE rsi.id = ?");
			$sql_chk->execute([$_REQUEST['item']]);
			
			if($sql_chk->rowCount() > 0){
				$rows = $sql_chk->fetch();
				$item = $rows['item_id'];
				$old_qty = floatval($rows['del_qty']);
				$old_price = floatval($rows['del_price']);
				$request_status = $rows['request_status'];
				$delivery_date = $rows['updated_at'];
				
				// Step 2: Update request_store_item with new values (ALWAYS)
				$sql = $db->prepare("UPDATE request_store_item SET del_qty = ?, del_price = ?, updated_at = NOW() WHERE id = ?");
				$sql->execute([$qty, $price, $_REQUEST['item']]);
				
				// Check if request is confirmed (status = 1)
				if($request_status == 1){
					// REQUEST IS CONFIRMED - Update all 3 tables
					
					// Step 3: Find and update the corresponding tbl_progress entry
					$find_progress = $db->prepare(
						"SELECT prog_id FROM tbl_progress 
						 WHERE item = ? 
						 AND COALESCE(in_qty, 0) = ? 
						 AND COALESCE(out_qty, 0) = 0
						 ORDER BY prog_id DESC 
						 LIMIT 1"
					);
					$find_progress->execute([$item, $old_qty]);
					
					if($find_progress->rowCount() > 0){
						$prog_row = $find_progress->fetch();
						$prog_id = $prog_row['prog_id'];
						
						// Update the specific progress entry
						$update_progress = $db->prepare(
							"UPDATE tbl_progress 
							 SET in_qty = ?, new_price = ?
							 WHERE prog_id = ?"
						);
						$update_progress->execute([$qty, $price, $prog_id]);
						
						// Step 4: Recalculate ALL tbl_progress transactions for this item
						$recalc_sql = "
							UPDATE tbl_progress p
							INNER JOIN (
								SELECT 
									p1.prog_id,
									(
										SELECT COALESCE(SUM(COALESCE(p2.in_qty, 0) - COALESCE(p2.out_qty, 0)), 0)
										FROM tbl_progress p2
										WHERE p2.item = p1.item
										  AND (p2.date < p1.date OR (p2.date = p1.date AND p2.prog_id < p1.prog_id))
									) AS new_last_qty,
									(
										SELECT COALESCE(SUM(COALESCE(p2.in_qty, 0) - COALESCE(p2.out_qty, 0)), 0)
										FROM tbl_progress p2
										WHERE p2.item = p1.item
										  AND (p2.date < p1.date OR (p2.date = p1.date AND p2.prog_id <= p1.prog_id))
									) AS new_end_qty
								FROM tbl_progress p1
								WHERE p1.item = ?
							) calc ON p.prog_id = calc.prog_id
							SET 
								p.last_qty = calc.new_last_qty,
								p.end_qty = calc.new_end_qty
						";
						
						$recalc_stmt = $db->prepare($recalc_sql);
						$recalc_stmt->execute([$item]);
						
						// Step 5: Sync tbl_item_stock with the latest end_qty from tbl_progress
						$sync_stock = $db->prepare(
							"UPDATE tbl_item_stock s
							 INNER JOIN (
								 SELECT 
									 p1.item,
									 p1.end_qty,
									 p1.date
								 FROM tbl_progress p1
								 WHERE p1.item = ?
								 ORDER BY p1.date DESC, p1.prog_id DESC
								 LIMIT 1
							 ) latest ON s.item = latest.item
							 SET 
								 s.qty = latest.end_qty,
								 s.date_tkn = latest.date"
						);
						$sync_stock->execute([$item]);
						
						$db->commit();
						
						echo "<script>alert('✓ CONFIRMED Delivery Updated!\\n\\nOld Qty: $old_qty → New Qty: $qty\\n\\n✓ Updated request_store_item\\n✓ Updated tbl_progress\\n✓ Updated tbl_item_stock\\n\\nAll stock quantities recalculated.'); window.location='index?resto=viewDelivery&&id=".$_REQUEST['pitem']."'</script>";
						
					} else {
						$db->rollBack();
						echo "<script>alert('⚠ Warning: Could not find matching progress entry.\\n\\nOld Qty: $old_qty not found in tbl_progress for item $item.\\n\\nOnly request_store_item was updated.'); window.location='index?resto=viewDelivery&&id=".$_REQUEST['pitem']."'</script>";
					}
					
				} else {
					// REQUEST NOT CONFIRMED - Only update request_store_item (already done in Step 2)
					$db->commit();
					
					echo "<script>alert('✓ Delivery Updated (Not Confirmed)\\n\\nOld Qty: $old_qty → New Qty: $qty\\nOld Price: $old_price → New Price: $price\\n\\n✓ Updated request_store_item only\\n\\nNote: Stock tables will be updated when you confirm this delivery.'); window.location='index?resto=viewDelivery&&id=".$_REQUEST['pitem']."'</script>";
				}
				
			} else {
				throw new Exception("Request item not found");
			}
			
		} catch (Exception $e) {
			if($db->inTransaction()){
				$db->rollBack();
			}
			echo "<script>alert('❌ Error updating delivery:\\n\\n" . addslashes($e->getMessage()) . "\\n\\nFile: " . addslashes($e->getFile()) . "\\nLine: " . $e->getLine() . "'); history.back();</script>";
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
				
				   <div class="modal-content">
				   <br><br>
          
			<br><br>
            <div class="modal-body">
              <form action="" enctype="multipart/form-data" method="POST">
          <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Delivery Quantity</strong><span class="text-danger">*</span></label>


	
                <div class="col-md-4">
<input type="number" name="qty" placeholder="enter Quantity" step=".01" class="form-control">	
	
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Unit price</strong></label>
                <div class="col-md-4">
                             <input type="number" name="price" step=".01" placeholder="enter Unit Price" class="form-control">	
		
  </select>	
                </div>
            </div>
            </div>
            <br>
       
              <input type="submit" name="addRequest" Value="Save">
            </form>
			
            </div>
     
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
   <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
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
  </script>
