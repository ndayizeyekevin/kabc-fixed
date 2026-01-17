<?php 
include '../inc/conn.php';
// ...existing PHP logic...
?>
<style>
  .printHeader { display: none !important; }
  @media print {
    body * { visibility: hidden; }
    .print-section, .print-section *, .printHeader, .printHeader * { visibility: visible; }
    .print-section { position: absolute; left: 0; top: 80px; width: 100%; }
    .printHeader { display: block !important; position: absolute; top: -15rem; left: 0; width: 100%; text-align: center; margin-bottom: 2rem; border:none !important; }
    .signature-section { display: block !important; margin-top: 40px; }
    .signature-row { display: flex; justify-content: space-between; gap: 20px; }
    .signature-box { width: 32%; text-align: center; }
    .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
    .no-print { display: none !important; }
    table, td { border: 1px solid black !important; font-size: 20px !important; }
    a { text-decoration: none !important; text-transform: capitalize !important; color: #000 !important; }
    a[href]:after { content: none !important; }
    h4{ display: block !important; text-align: center; padding: 5px 0; }
  }
</style>
<div class="colr-area">
  
  <div class="container">
            
            
         
                <!-- Date Range Filter (server-side) -->
                <form class="row g-3 align-items-end mb-3 no-print" id="dateRangeForm" method="get" action="./index">
                  <input type="hidden" name="resto" value="rent">
                  <div class="col-auto">
                    <label for="fromDate" class="form-label mb-0">From</label>
                    <input type="date" class="form-control" id="fromDate" name="fromDate" value="<?php echo htmlspecialchars($_GET['fromDate'] ?? '', ENT_QUOTES); ?>">
                  </div>
                  <div class="col-auto">
                    <label for="toDate" class="form-label mb-0">To</label>
                    <input type="date" class="form-control" id="toDate" name="toDate" value="<?php echo htmlspecialchars($_GET['toDate'] ?? '', ENT_QUOTES); ?>">
                  </div>
                  <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="./index?resto=rent" class="btn btn-secondary">Reset</a>
                  </div>
                </form>
              <h5 class="card-header">RENTAL REPORT <button type="button" class="btn btn-success no-print" onclick="window.print()"><i class="fa fa-print"></i> Print</button></h5>
              <div class="text-nowrap table-responsive print-section">
                <?php
                $fromDateHeader = !empty($_GET['fromDate']) ? date('Y-m-d', strtotime($_GET['fromDate'])) : null;
                $toDateHeader = !empty($_GET['toDate']) ? date('Y-m-d', strtotime($_GET['toDate'])) : null;
                ?>
                <?php
                require_once "../holder/printHeader.php";
                ?>
                <h4 style="font-size:28px;font-weight:bold;">
                  Rental Report
                  <?php if ($fromDateHeader || $toDateHeader): ?>
                    <div style="font-size:16px;font-weight:normal;">Period: <?php echo htmlspecialchars($fromDateHeader ?? 'N/A'); ?> to <?php echo htmlspecialchars($toDateHeader ?? date('Y-m-d')); ?></div>
                  <?php else: ?>
                    <div style="font-size:16px;font-weight:normal;">As on <?php echo date('Y-m-d'); ?></div>
                  <?php endif; ?>
                </h4>
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                        <th>No</th>
                        <th>Room Type</th>
                      <th>Guest Names</th>
                          <th>Company</th>
                   
                      <th>Check In</th>
                      <th>Check Out</th>
                      <th>Count</th>
                    
                      <th>Room Rate</th>
                      
                    </tr>
                  </thead>
                  <tbody id="bookingTableBody" class="table-border-bottom-0">		  
                  <?php 
                  $no = 0;
                  $total_amount = 0; // Initialize total amount variable
                  $total_count = 0;
                  // Server-side date filtering (fallback defaults)
                  $fromDateParam = !empty($_GET['fromDate']) ? $_GET['fromDate'] : '2023-01-01';
                  $toDateParam = !empty($_GET['toDate']) ? $_GET['toDate'] : date('Y-m-d');
                  // Validate and normalize dates
                  $fromDate = date('Y-m-d', strtotime($fromDateParam));
                  $toDate = date('Y-m-d', strtotime($toDateParam));

                  $sql = $db->prepare("SELECT * FROM tbl_acc_booking WHERE booking_status_id in(6, 5) AND checkin_date >= :fromDate AND checkout_date <= :toDate ORDER BY id DESC");
                  $sql->execute([
                      ':fromDate' => $fromDate,
                      ':toDate' => $toDate
                  ]);
                  while($row = $sql->fetch()){
                    $checkin = htmlspecialchars($row['checkin_date'] ?? '');
                    $total_amount += floatval($row['room_price']); // Add room price to total
                    $total_count++;
                  ?>
                  <tr data-checkin="<?php echo $checkin; ?>">
                      <td><strong><?php echo $no = $no+1?></strong></td>
                      <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                      <td><strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                      <td><?php echo $row['company']?></td>
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                      <td><?php  
                        $checkinRow = $row['checkin_date'];
                        $checkoutRow = $row['checkout_date'];
                        $nights = (int) max(1, (strtotime($checkoutRow) - strtotime($checkinRow)) / (60*60*24));
                        echo $nights;
                      ?></td>
                      <td><?php echo number_format($row['room_price'])?></td>

                  </tr>
                  <?php }
                  ?>
                  <!-- Total Row -->
                  <tr style="background-color: #f8f9fa; font-weight: bold;">
                      <td colspan="7" style="text-align: right; padding: 10px;"><strong>TOTAL (<?php echo $total_count; ?>):</strong></td>
                      <td style="padding: 10px;"><strong>RWF <?php echo number_format($total_amount); ?></strong></td>
                  </tr>
                  <script>
                  // Optional client-side quick filter (server-side is primary)
                  function filterByDate() {
                    var from = document.getElementById('fromDate').value;
                    var to = document.getElementById('toDate').value;
                    var rows = document.querySelectorAll('#bookingTableBody tr');
                    rows.forEach(function(row) {
                      var checkin = row.getAttribute('data-checkin') || '';
                      if (!from && !to) {
                        row.style.display = '';
                        return;
                      }
                      if (from && checkin < from) {
                        row.style.display = 'none';
                        return;
                      }
                      if (to && checkin > to) {
                        row.style.display = 'none';
                        return;
                      }
                      row.style.display = '';
                    });
                  }
                  // Reset reloads page and clears server-side filters
                  function resetDateFilter() {
                    window.location.href = './index?resto=rent';
                  }
                  </script>
				  
				  
                 
                 
                   
                  </tbody>
                </table>
                <!-- Print-only signature footer placed right after the table -->
                <div class="signature-section" style="display:none;">
                  <div class="signature-row">
                    <div class="signature-box" style="text-align:left;">
                      <strong>Printed by:</strong><br>
                      <?php 
                      $printedBy = '';
                      if (isset($_SESSION['user_fullname'])) { $printedBy = $_SESSION['user_fullname']; }
                      elseif (isset($_SESSION['fullname'])) { $printedBy = $_SESSION['fullname']; }
                      elseif (isset($_SESSION['name'])) { $printedBy = $_SESSION['name']; }
                      elseif (isset($_SESSION['username'])) { $printedBy = $_SESSION['username']; }
                      elseif (isset($_SESSION['f_name']) || isset($_SESSION['l_name'])) { $printedBy = trim(($_SESSION['f_name'] ?? '') . ' ' . ($_SESSION['l_name'] ?? '')); }
                      echo htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8');
                      ?><br>
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
                <div style="text-align:center;font-size:10px;margin-top:20px;">
                  Printed on: <?php echo date("Y-m-d H:i:s"); ?>
                </div>
              </div>  
        </div>