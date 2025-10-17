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
  global $db;
  StoreController::getUnitePrice($db,$id);
	
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

if(isset($_POST['addRequest'])){
		
 // Set custom remark (optional, can be from UI too)
 $remark = 'damages-'.$_POST['comment'];

 $result = StoreController::recordDamagedItem($db, $_POST, $remark);

 if ($result['success']) {
     echo '<div class="alert alert-success">' . $result['message'] . '</div>';
     echo '<meta http-equiv="refresh" content="1;URL=index??resto=WriteOff">';
 } else {
     echo '<div class="alert alert-danger">Error: ' . $result['message'] . '</div>';
 }
 
	}?>

<?php
// Date filtering logic
$date_filter_sql = "";
$from_date = "";
$to_date = "";
$total_damaged_qty = 0;

if(isset($_POST['filter_dates']) || isset($_GET['from_date'])) {
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : $_GET['from_date'];
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : $_GET['to_date'];
    
    if($from_date && $to_date) {
        $date_filter_sql = " WHERE created_at BETWEEN '$from_date' AND '$to_date'";
    } elseif($from_date) {
        $date_filter_sql = " WHERE created_at >= '$from_date'";
    } elseif($to_date) {
        $date_filter_sql = " WHERE created_at <= '$to_date'";
    }
}

// Calculate total damaged quantity for filtered results
$total_result = $db->prepare("SELECT SUM(qty) as total_qty FROM damaged" . $date_filter_sql);
$total_result->execute();
$total_row = $total_result->fetch();
$total_damaged_qty = $total_row['total_qty'] ?? 0;
?>
	
<style>
    .form-ic-cmp{
        margin-bottom:10px;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .stats-box {
        background: #e7f3ff;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #007bff;
        margin-bottom: 20px;
    }
    .print-section {
        text-align: right;
        margin-bottom: 10px;
    }
    /* Signature footer - hidden on screen */
    .signature-section {
        display: none;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .table {
            font-size: 12px;
        }
        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }
        buttton, a{
            display: none !important;
        }
        /* Show signature footer only when printing */
        .signature-section {
            display: block;
            margin-top: 40px;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .signature-box {
            width: 32%;
            text-align: center;
        }
        .signature-box .sig-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            height: 0;
        }
    }
</style>

<!-- Breadcomb area Start-->
	<div class="breadcomb-area no-print">
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

                   <!-- Print Header (only visible when printing) -->
                   <div class="print-header" style="display: none;">
                       <h2>Damaged Items Report</h2>
                       <p>Generated on: <?php echo date('d-m-Y H:i:s'); ?></p>
                       <?php if($from_date || $to_date): ?>
                           <p>Date Range: 
                           <?php 
                           if($from_date && $to_date) {
                               echo date('d-m-Y', strtotime($from_date)) . ' to ' . date('d-m-Y', strtotime($to_date));
                           } elseif($from_date) {
                               echo 'From ' . date('d-m-Y', strtotime($from_date));
                           } elseif($to_date) {
                               echo 'Until ' . date('d-m-Y', strtotime($to_date));
                           }
                           ?>
                           </p>
                       <?php endif; ?>
                   </div>

                   <!-- Date Filter Section -->
                   <div class="filter-section no-print">
                       <h4>Filter by Date Range</h4>
                       <form method="POST" action="">
                           <div class="row">
                               <div class="col-md-3">
                                   <label>From Date:</label>
                                   <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>">
                               </div>
                               <div class="col-md-3">
                                   <label>To Date:</label>
                                   <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>">
                               </div>
                               <div class="col-md-3">
                                   <label>&nbsp;</label><br>
                                   <button type="submit" name="filter_dates" class="btn btn-primary">Filter</button>
                                   <a href="<?php echo $_SERVER['PHP_SELF']; ?>?resto=<?php echo $_GET['resto'] ?? ''; ?>" class="btn btn-secondary">Clear</a>
                               </div>
                           </div>
                       </form>
                   </div>

                   <!-- Statistics Box -->
                   <div class="stats-box">
                       <div class="row">
                           <div class="col-md-6">
                               <h4>Total Damaged Quantity: <strong><?php echo number_format($total_damaged_qty); ?></strong></h4>
                           </div>
                           <div class="col-md-6 text-right no-print">
                               <button onclick="printReport()" class="btn btn-success">
                                   <i class="fa fa-print"></i> Print Report
                               </button>
                           </div>
                       </div>
                   </div>
          
			<br><br>
            <div class="modal-body">
              
             <div class="table-responsive">
						
                            <table id="data-table-basic" class="table table-striped">
                                      <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>U.Price</th>
                                        <th>T.Price</th>
                                        <th>Comment</th>
                                    
                                        <th class="no-print" hidden>Action</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    $tPrice = 0;
                                		$result = $db->prepare("SELECT * FROM damaged" . $date_filter_sql . " ORDER BY created_at DESC");
                                        $result->execute();
                                        
                                		while($fetch = $result->fetch()){
                                		    $i++;
                                        $tPrice += (float)$fetch['qty'] * (float)getItemPrice($fetch['item_id']);
                                     	?>
                                     	    <tr>
                                     	      <td><?php echo $i; ?></td>
                                     	      <td><?php echo is_numeric($fetch['created_at']) ? date('d-m-Y', $fetch['created_at']) : $fetch['created_at']; ?></td>
                                            <td><?php echo getItemName($fetch['item_id'])?></td>
                                            <td><?php echo (float)$fetch['qty']?></td>
                                            <?php
                                              $price =(float) getItemPrice($fetch['item_id']);
                                              $formattedPrice = number_format($price, ($price == (int)$price) ? 0 : 2);
                                              $total = $price *  (float)$fetch['qty'];
                                              $formattedTotal = number_format($total, ($total == (int)$total) ? 0 : 2);
                                              ?>
                                              <td><b><?php echo $formattedPrice; ?></b> FRW</td>
                                              <td><b><?php echo $formattedTotal; ?></b> FRW</td>
                                            <td><?php echo $fetch['message']?></td>
                                            <td class="no-print" hidden><a  href="#">Delete</a> | <a  href="#">Edit</a></td>
                                          </tr>
                                          <?php
                                            }
                                          ?>
                                          <tr> <td colspan="3"><strong>Total</strong></td><td><strong><?php echo number_format((float)$total_damaged_qty,3); ?></strong></td> <td></td><td><strong><?php echo number_format($tPrice,3); ?></strong> FRW</td><td></td></tr>

                            </tbody>
                          
                        </table>

                        <?php
                        // Determine the current user's full name for the print footer based on logged-in user ID
                        $printedBy = '';
                        try {
                            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                                $stmtPB = $db->prepare("SELECT u.f_name, u.l_name 
                                                        FROM tbl_users u 
                                                        INNER JOIN tbl_user_log l ON u.user_id = l.user_id 
                                                        WHERE l.user_id = ? LIMIT 1");
                                $stmtPB->execute([$_SESSION['user_id']]);
                                $rowPB = $stmtPB->fetch(PDO::FETCH_ASSOC);
                                if ($rowPB) {
                                    $printedBy = trim(($rowPB['f_name'] ?? '') . ' ' . ($rowPB['l_name'] ?? ''));
                                }
                            }
                        } catch (Exception $e) {
                            // ignore and fallback below
                        }
                        // Fallbacks to existing session-based names if DB lookup didn't yield a value
                        if ($printedBy === '') {
                            if (isset($_SESSION['user_fullname'])) { $printedBy = $_SESSION['user_fullname']; }
                            elseif (isset($_SESSION['fullname'])) { $printedBy = $_SESSION['fullname']; }
                            elseif (isset($_SESSION['name'])) { $printedBy = $_SESSION['name']; }
                            elseif (isset($_SESSION['username'])) { $printedBy = $_SESSION['username']; }
                            elseif (isset($_SESSION['f_name']) || isset($_SESSION['l_name'])) { $printedBy = trim(($_SESSION['f_name'] ?? '') . ' ' . ($_SESSION['l_name'] ?? '')); }
                        }
                        ?>

                        <!-- Print-only signature footer placed right after the table -->
                        <div class="signature-section">
                            <div class="signature-row">
                                <div class="signature-box" style="text-align:left;">
                                    <strong>Printed by:</strong><br>
                                    <?php echo htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box">
                                    <strong>Received by:</strong><br>
                                    <br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box" style="text-align:right;">
                                    <strong>Approved by:</strong><br>
                                    <br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
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
    
    // Print functionality
    function printReport() {
        // Show print header
        document.querySelector('.print-header').style.display = 'block';
        
        // Print the page
        window.print();
        
        // Hide print header after printing
        setTimeout(function() {
            document.querySelector('.print-header').style.display = 'none';
        }, 1000);
    }
    
    document.getElementById("floatInput").addEventListener("input", function (e) {
    let value = this.value;

    
    const valid = value
        .replace(/[^0-9.]/g, '') 
        .replace(/^(\d*\.\d*).*$/, '$1') // allow only one dot in input value

    this.value = valid;
    });
    
    
    
    
    
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