<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
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

function getItemPrice($id){
	
include '../inc/conn.php';	

$item = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($item);

$tbl_progress = $conn ->query ("SELECT * FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1;");

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    while($row2 = $tbl_progress->fetch_assoc()){
        return  $row2['new_price'] > 0 ? $row2['new_price'] : $row['price'] ;
    }
  }
}

}

function getAverage($id){
	

  return  getItemPrice($id);

    
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


 
if(isset($_GET['ac'])) {
    $code = $_GET['ac'];
    $date = date("Y-m-d");
    $allItemsValid = true;
    $errorMessage = "";
    
    // Start transaction for atomic operations
    $db->beginTransaction();
    
    try {
        // First pass: Check all items for sufficient stock
        $sql = $db->prepare("SELECT * FROM tbl_request_details WHERE req_code = ?");
        $sql->execute([$code]);
        
        while($rows = $sql->fetch()) {
            $currqty = $rows['quantity'];
            
            $stmt = $db->prepare("SELECT * FROM tbl_item_stock WHERE item = ?");
            $stmt->execute([$rows['items']]);
            $rowss = $stmt->fetch();
            
            if (!$rowss) {
                $allItemsValid = false;
                $errorMessage = "Item not found in stock: ".$rows['items'];
                break;
            }
            
            $stkqty = $rowss['qty'];
            $remainqty = $stkqty - $currqty;
            
            if($remainqty < 0) {
                $allItemsValid = false;
                $errorMessage = "Insufficient Stock for ".getItemName($rows['items'], $db);
                break;
            }
        }
        
        // Second pass: Only process if all items have sufficient stock
        if($allItemsValid) {
            $sql = $db->prepare("SELECT * FROM tbl_request_details WHERE req_code = ?");
            $sql->execute([$code]);
            
            while($rows = $sql->fetch()) {
                $currqty = $rows['quantity'];
                
                // Get current stock
                $stmt = $db->prepare("SELECT * FROM tbl_item_stock WHERE item = ?");
                $stmt->execute([$rows['items']]);
                $rowss = $stmt->fetch();
                $stkqty = $rowss['qty'];
                $lastqty = $stkqty;
                $remainqty = $stkqty - $currqty;
                
                // Update stock
                $sql_upd_qty = $db->prepare("UPDATE tbl_item_stock SET qty = ? WHERE item = ?");
                $sql_upd_qty->execute([$remainqty, $rows['items']]);
                
                // Record transaction
                $new_price = StoreController::getUnitePrice($db, (int)$rows['items']);
                $tot = $lastqty - $currqty;
                
                $stmts = $db->prepare("INSERT INTO tbl_progress 
                    (date, out_qty, last_qty, item, end_qty, new_price) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmts->execute([$date, $currqty, $lastqty, $rows['items'], $tot, $new_price]);
            }
            
            // Update request status
            $sql_upd = $db->prepare("UPDATE tbl_requests SET status = '8' WHERE req_code = ?");
            $sql_upd->execute([$code]);
            
            $db->commit();
            
            $_SESSION['success_message'] = "Request confirmed successfully";
            echo "<script>window.location='index?resto=home'</script>";

            exit();
        } else {
            $db->rollBack();
            $_SESSION['error_message'] = $errorMessage;
            echo "<script>alert('Stock Shortage');</script>";
            echo "<script>window.location='index?resto=home'</script>";
            exit();
        }
        
    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        // Display alert with the session message
            echo "<script>
                    alert('" . addslashes($_SESSION['error_message']) . "');
                    window.location='index?resto=request';
                  </script>";
        exit();
    }
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
			<div class="row">
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




<?php
if (isset($_GET['edit'], $_GET['req']) && !empty($_GET['edit']) && !empty($_GET['req'])) {
    $code = $_GET['req'];
    $item_id = $_GET['edit'];

    $sql = $db->prepare("SELECT * FROM tbl_request_details WHERE req_code = ? AND items = ?");
    $sql->execute([$code, $item_id]);
    $row = $sql->fetch();

    if ($row):
?>

<!-- Modal -->
<div id="editModal" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Requested Item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Item</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars(getItemName($row['items'])); ?>" disabled>
            </div>

            <div class="form-group col-md-6">
              <label>Requested Quantity</label>
              <input type="number" step='0.001' min="0" name="new_qty" class="form-control" value="<?php echo htmlspecialchars($row['quantity']); ?>" required>
              <input type="hidden" name="req_code" value="<?php echo htmlspecialchars($code); ?>">
              <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="EditQty" class="btn btn-primary">Save Changes</button>
          <a href="index.php?resto=request&req=<?php echo $_GET['req'] ?>" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

    <?php
    endif;
}

// Handle form submission
if (isset($_POST['EditQty'])) {
    $newQty = $_POST['new_qty'];
    $code = $_POST['req_code'];
    $item_id = $_POST['item_id'];

    $update = $db->prepare("UPDATE tbl_request_details SET quantity = ? WHERE req_code = ? AND items = ?");
    if ($update->execute([$newQty, $code, $item_id])) {
        echo "<p class='alert alert-success'>Quantity updated successfully.</p>";
        echo '<meta http-equiv="refresh" content="2;URL=?resto=request&req=' . htmlspecialchars($code) . '">';
    } else {
        echo "<p class='alert alert-danger'>Failed to update quantity.</p>";
    }
}
?>



	<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <?php if(empty($_GET['req'])){ ?>
            <!-- Search and Date Range Picker -->
            <div class="filter-controls" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
              <div class="row">
                <div class="col-md-6">
                  <label for="searchInput"><strong>Search:</strong></label>
                  <input type="text" id="searchInput" class="form-control" placeholder="Search by requester name, status..." style="margin-bottom: 10px;">
                </div>
                <div class="col-md-3">
                  <label for="startDate"><strong>From:</strong></label>
                  <input type="date" id="startDate" name="startDate" class="form-control">
                </div>
                <div class="col-md-3">
                  <label for="endDate"><strong>To:</strong></label>
                  <input type="date" id="endDate" name="endDate" class="form-control">
                </div>
              </div>
              <div class="row" style="margin-top: 10px;">
                <div class="col-md-12">
                  <button type="button" id="clearFilters" class="btn btn-secondary btn-sm">Clear Filters</button>
                  <span id="filterStatus" class="text-muted" style="margin-left: 15px;"></span>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table id="data-table-basic" class="table table-striped">
                                 <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request By</th>
                                        <th>Requested Date</th>
                                        <th>Required Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                  <?php
                  $i = 0;
                  $result = $db->prepare("SELECT * FROM tbl_requests
                    INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
                    INNER JOIN tbl_status ON tbl_requests.status = tbl_status.id
                     ORDER BY  requested_date DESC");
                  $result->execute();
                  while($fetch = $result->fetch()){
                    $i++;
                  ?>
                    <tr class="request-row" 
                        data-requester="<?php echo strtolower($fetch['f_name'].' '.$fetch['l_name']); ?>"
                        data-status="<?php echo strtolower($fetch['status_name']); ?>"
                        data-req-date="<?php echo $fetch['requested_date']; ?>"
                        data-req-code="<?php echo strtolower($fetch['req_code']); ?>">
                      <td><?php echo $i; ?></td>
                      <td><a  href="?resto=request&req=<?php echo $fetch['req_code'] ?>"><?php echo $fetch['f_name']." ".$fetch['l_name']; ?></a></td>
                      <td class="requested-date-cell"><?php echo $fetch['requested_date']; ?></td>
                      <td><?php echo $fetch['required_date']; ?></td>
                      <td><?php echo $fetch['status_name']; ?></td>
                      <td>
                        <?php if($fetch['status'] == '3'){ ?>
                        <a class="btn-sm" href="?resto=request&ac=<?php echo $fetch['req_code'] ?>" onclick="if(!confirm('Do you really want to Confirm This Request?'))return false;else return true;">Stock out</a>
                      <?php } ?>
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Request By</th>
                                    <th>Requested Date</th>
                                    <th>Required Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>


                            <?php
                        }elseif(!empty($_GET['req'])){
                            $code = $_GET['req'];
                            $stmt_req = $db->prepare("SELECT * FROM tbl_requests 
                            INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
                            WHERE req_code = '".$_GET['req']."'");
                            $stmt_req->execute();
                            $getrows = $stmt_req->fetch();
                            $status = $getrows['status'];
                            $dep = $getrows['f_name'];
                        ?>
                        <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                        Type: Raw Materials <br>
                        Date Requested: <?php echo  $req_date = $getrows['requested_date']; ?>
                        </div>
                        <div class="panel-title pull-right">
                        Requested By: <?php echo  $getrows['f_name']." ".$getrows['l_name']; ?><br>
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
                     
                     
                     <a  class="btn btn-info" href="?resto=printInternal&&req=<?php echo $_REQUEST['req']?>&&dep=<?php echo $dep?>&&req_date=<?php echo $req_date ?>">Print</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <br>
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Item Name </th>
                            <th> Unit </th>
                              <th> QTY.IN </th>
                              <th> RE.QTY </th>
                              <th> PRICE </th>
                              <th> Amount </th>
                              <?php if($status == 3): ?>
                              <th> Action </th>
                              <?php endif; ?>
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
                                  $total = (float)getAverage($row['items']) * (float)$row['quantity'];
                                	$amount =  $amount  + $total;
                            ?>
                            <tr>
                                <td><?php echo $ii; ?></td>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo getItemUnitName(getItemUnitId($row['items'])); ?></td>
                                 <td><?php echo getItemStock($row['items']); ?></td>
                                 <td><?php echo $row['quantity']; ?></td>
                                      <td><?php echo number_format((float)getAverage($row['items']), 3); ?></td>
                                      <td><?php echo number_format((float)getAverage($row['items']) * (float)$row['quantity']); ?></td>
                                      <?php if($status == 3){ ?>
                                      <td> <a href="index.php?resto=request&req=<?php echo $_GET['req']; ?>&edit=<?php echo $row['items'] ?>" class="btn btn-warning">Edit</a></td>
                                      <?php } ?>
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
                    </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add Request</h4>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
          <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Requested Date</strong><span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <input autocomplete="off" type="date" max="<?php echo date("Y-m-d") ?>" class="form-control" name="requested" required>
                    <input autocomplete="off" type="hidden" class="form-control" name="req_code" value="<?php echo $pcode;?>" required>
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Required Date</strong></label>
                <div class="col-md-4">
                    <input autocomplete="off" type="date" min="<?php echo date("Y-m-d") ?>" class="form-control" name="required" required>
                </div>
            </div>
            </div>
            <br>
            <div class="row">
                    <div class="form-group">
                        <label class="col-md-2 control-label" for=""><strong>Remark</strong></label>
                <div class="col-md-4">
                 <textarea class="form-control" name="remark" required></textarea>
            </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                 <table class="table table-border" id="myOrder">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>
                          <button type="button" name="addOrder" class="btn btn-success btn-sm btn_addOrder" required><span>
                            <i class="fa fa-plus"></i>
                          </span></button>
                        </th>
                    </tr>
        
                </thead>
                <tbody>
        
                </tbody>
              </table>
            </div>
            </div>
            <div class="form-group">
    		    <div class="form-actions col-md-12">
    		        <br />
    		        <center>								
    			        <button type="submit" name="add" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
    			        <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
    		        </center>
    		    </div>
            </div>
            </div>
            
            </form>
            </div>
        </div>
    </div>
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
      // Search and filter functionality
      function applyFilters() {
        var searchTerm = $('#searchInput').val().toLowerCase();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        var visibleCount = 0;
        var totalCount = 0;
        
        $("#data-table-basic tbody tr.request-row").each(function() {
          totalCount++;
          var $row = $(this);
          var requester = $row.data('requester') || '';
          var status = $row.data('status') || '';
          var reqCode = $row.data('req-code') || '';
          var dateText = $row.attr('data-req-date') || '';
          var dateOnly = dateText.split(' ')[0]; // Extract date part only

          var showRow = true;
          
          // Search filter
          if (searchTerm && searchTerm.length > 0) {
            var requesterText = $row.find('td:nth-child(2)').text().toLowerCase();
            var statusText = $row.find('td:nth-child(5)').text().toLowerCase();
            var reqCodeAttr = $row.attr('data-req-code') || $row.data('req-code') || '';
            
            var searchMatch = requesterText.includes(searchTerm) || 
                            statusText.includes(searchTerm) || 
                            reqCodeAttr.includes(searchTerm);
            if (!searchMatch) showRow = false;
          }
          
          // Date range filter
          if (startDate && dateOnly < startDate) showRow = false;
          if (endDate && dateOnly > endDate) showRow = false;
          
          $row.toggle(showRow);
          if (showRow) visibleCount++;
        });
        
        // Update filter status
        var statusText = 'Showing ' + visibleCount + ' of ' + totalCount + ' requests';
        if (searchTerm || startDate || endDate) {
          statusText += ' (filtered)';
        }
        $('#filterStatus').text(statusText);
      }
      
      // Event listeners for filters
      $('#searchInput').on('keyup', applyFilters);
      $('#startDate, #endDate').on('change', applyFilters);
      
      // Clear filters button
      $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#startDate').val('');
        $('#endDate').val('');
        applyFilters();
      });
      
      // Initialize filter status
      applyFilters();

      $(document).on('click','.btn_addOrder', function(){
        var html='';
        html+='<tr>';
        html+='<td><select class="form-control selectpicker productid" data-live-search="true" name="items[]" required><option value="">--Select Item--</option><?php
        echo fill_product($db)?></select></td>';
        html+='<td><input autocomplete="off" type="number" class="form-control quantity" name="quantity[]" required placeholder="0"></td>';
        html+='<td><input autocomplete="off" type="text" class="unit" disabled></td>';
        html+='<td><button type="button" name="remove" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-remove"></i></button></td>';
        html+='</tr>';

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
      });
    });
  </script>
  
  <style>
    .date-range-picker input[type="date"] {
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 5px 10px;
      font-size: 15px;
      background: #fff;
      transition: border-color 0.2s;
    }
    .date-range-picker input[type="date"]:focus {
      border-color: #007bff;
      outline: none;
    }
  </style>