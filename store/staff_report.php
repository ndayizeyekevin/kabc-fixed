<?php
//
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
//
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
?>
<!-- Add this style section -->
<style>
    /* Default styles (screen) */
    .printHeader {
        display: none !important; /* Hidden by default */
    }
    
    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }
        .print-section, .print-section *,
        .printHeader, .printHeader * {
            visibility: visible;
        }
        .print-section {
            position: absolute;
            left: 0;
            top: 80px; /* Adjust based on header height */
            width: 100%;
        }
        .printHeader {
            display: block !important;
            position: absolute;
            top: -15rem;
            left: 0;
            width: 100%;
            text-align: center;
            margin-bottom: 2rem;
            border:none !important;
        }
        /* Signature area visible on print */
        .signature-section { display: block !important; margin-top: 40px; }
        .signature-row { display: flex; justify-content: space-between; gap: 20px; }
        .signature-box { width: 32%; text-align: center; }
        .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
        .no-print {
            display: none !important;
        }
        table, td {
            border: 1px solid black !important;
            font-size: 20px !important;
        }
        a {
            text-decoration: none !important;
            text-transform: capitalize !important;
            color: #000 !important;
        }
        a[href]:after {
            content: none !important;
        }
        h4{
            display: block !important;
            text-align: center;
            padding: 5px 0;
        }
    }
</style>

<!-- Breadcomb area Start-->
<div class="breadcomb-area">
    <div class="container">
        <!-- ... existing code ... -->
    </div>
</div>

<!-- Data Table area Start-->
<div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="printHeader">
                <?php 
                include "../holder/printHeader.php"; // Include the print header
                ?>
                </div>
                <div class="data-table-list">
                    <?php if(empty($_GET['req'])){ ?>
                    <!-- Add date filter form -->
                    <div class="date-filter no-print">
                        <form method="get" action="">
                            <input type="hidden" name="resto" value="staff_report">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control" 
                                           value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control" 
                                           value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="?resto=staff_report" class="btn btn-default">Reset</a>
                                      <a href="?resto=staff_report_details" class="btn btn-default">Staff Report Details</a>
                                </div>
                                
                                    
                                  
                             
                                <div class="col-md-3 text-right">
                                    <label>&nbsp;</label><br>
                                    <button type="button" class="btn btn-success" onclick="window.print()">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="print-section">
                        <div class="table-responsive">
                            <?php
                            // Always use date filter: default to today if not set
                            $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d');
                            $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
                            ?>
                            <h4 style="font-size: 28px;font-weight:bold;">Staff Meal Report as on 
                                <strong><?php echo $from_date; ?></strong> 
                                To 
                                <strong><?php echo $to_date; ?></strong>
                            </h4>
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <!-- <th>Date</th> -->
                                        <th>Item Name</th>
                                        <!-- <th>QTY.IN</th> -->
                                        <th>Total Quantity</th>
                                        <th>PRICE</th>
                                        <th>Amount</th>
                                        <!-- Status column removed: not relevant for total Quantity summary -->
                                    </tr>
                                </thead>
<?php
$i = 0;
// Base SQL with necessary JOINs

// New SQL: Get total Quantity per item (all time or filtered by date if needed)
$sql = "SELECT tbl_request_details.items, tbl_items.item_name, SUM(tbl_request_details.quantity) as qtys
        FROM tbl_request_details
        INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
        INNER JOIN tbl_requests ON tbl_request_details.req_code = tbl_requests.req_code
        WHERE tbl_requests.req_type = 'Staff' AND tbl_requests.status = 8";


// Always use date filter: default to today if not set
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
$sql .= " AND DATE(tbl_requests.requested_date) >= '$from_date' AND DATE(tbl_requests.requested_date) <= '$to_date'";

$sql .= " GROUP BY tbl_request_details.items ORDER BY qtys DESC, tbl_items.item_name ASC";

// Debug
// die(var_dump($sql));

$result = $db->prepare($sql);
$result->execute();
$total = 0;
while($fetch = $result->fetch()){
    $i++;
?>
<tr>
    <td><?php echo $i; ?></td>
    <!-- <td><?php echo $fetch['requested_date']; ?></td> -->
    <td><?php echo $fetch['item_name']; ?></td>
    <td><?php echo $fetch['qtys']; ?></td>
    <td><?php echo number_format((float)getAverage($fetch['items']), 2); ?></td>
    <td><?php $a = (float)getAverage($fetch['items']) * (float)$fetch['qtys']; echo number_format($a); $total+=$a; ?></td>
    <!-- Status cell removed: not relevant for total Quantity summary -->
</tr>
<?php } ?>
<tr>
    <td colspan="4"><strong>Total</strong></td>
    <td><strong><?php echo number_format($total); ?></strong></td>
</tr>
                            </table>

                            <?php
                            // Resolve Printed by full name from logged-in user ID with safe fallbacks
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
                    <?php } 
                    elseif(!empty($_GET['req'])){
                        // ... existing detail view code ...
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ... rest of the existing code ... -->

<script>
// Print function
function printData() {
    var divToPrint = document.querySelector('.print-section');
    var header = document.querySelector('.printHeader').outerHTML;
    
    var html = '<html><head><title>Request Report</title>';
    html += '<link rel="stylesheet" href="path/to/your/bootstrap.css">';
    html += '<style>@page { size: auto; margin: 5mm; }</style>';
    html += '</head><body>';
    html += header; // Include the header
    html += divToPrint.innerHTML;
    html += '<div style="text-align:center;font-size:10px;margin-top:20px;">Printed on: <?php echo date("Y-m-d H:i:s"); ?></div>';
    html += '</body></html>';
    
    var newWin = window.open('', '_blank');
    newWin.document.write(html);
    newWin.document.close();
    newWin.focus();
    setTimeout(function() { newWin.print(); newWin.close(); }, 200);
}
</script>
