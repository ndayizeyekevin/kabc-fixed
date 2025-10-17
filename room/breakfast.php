<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
include '../inc/conn.php';
?>
<style>
  .printHeader { display: none !important; }
  @media print {
    /* Reset page margins */
    @page {
      margin: 0.5cm;
      size: A4 landscape; /* Consider landscape for wider tables */
    }
    
    body * { visibility: hidden; }
    .print-section, .print-section *, .printHeader, .printHeader * { visibility: visible; }
    .print-section { 
      position: absolute; 
      left: 0; 
      top: 60px; 
      width: 100%; 
    }
    .printHeader { 
      display: block !important; 
      position: absolute; 
      top: -10rem; 
      left: 0; 
      width: 100%; 
      text-align: center; 
      margin-bottom: 1rem; 
      border: none !important; 
    }
    .printHeader h2 {
      font-size: 16px !important;
      margin: 0 !important;
      padding: 5px 0 !important;
    }
    
    /* Compact table styles */
    table { 
      border-collapse: collapse !important;
      width: 100% !important;
      font-size: 9px !important; /* Reduced from 20px */
      line-height: 1.2 !important;
    }
    
    table, td, th { 
      border: 0.5px solid black !important; /* Thinner borders */
      padding: 2px 4px !important; /* Minimal padding */
    }
    
    th {
      font-size: 10px !important;
      font-weight: bold !important;
      padding: 3px 4px !important;
      background-color: #f0f0f0 !important;
      color: white !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    
    td {
      font-size: 9px !important;
      vertical-align: middle !important;
    }
    
    td strong {
      font-weight: 600 !important;
    }
    
    /* Compact header */
    h4 { 
      display: block !important; 
      text-align: center; 
      padding: 3px 0 !important; 
      margin: 5px 0 10px 0 !important;
      font-size: 12px !important; /* Reduced from 1.5rem */
    }
    
    /* Signature section */
    .signature-section { 
      display: block !important; 
      margin-top: 20px !important; /* Reduced spacing */
      font-size: 8px !important;
    }
    .signature-row { 
      display: flex; 
      justify-content: space-between; 
      gap: 15px; 
    }
    .signature-box { 
      width: 32%; 
      text-align: center; 
      font-size: 8px !important;
    }
    .signature-box strong {
      font-size: 9px !important;
    }
    .signature-box .sig-line { 
      margin-top: 30px !important; /* Reduced from 50px */
      border-top: 0.5px solid #000; 
      height: 0; 
    }
    .signature-box small {
      font-size: 7px !important;
    }
    
    /* Footer timestamp */
    .print-section > div:last-child {
      text-align: center !important;
      font-size: 7px !important;
      margin-top: 10px !important;
    }
    
    .no-print { display: none !important; }
    a { 
      text-decoration: none !important; 
      text-transform: capitalize !important; 
      color: #000 !important; 
    }
    a[href]:after { content: none !important; }
  }
  
  /* Regular screen view - white text for table headers */
  .table-dark th {
    color: white !important;
  }
</style>
<div class="printHeader">
  <?php 
  if (file_exists("../holder/printHeader.php")) {
    include "../holder/printHeader.php";
  } else {
    echo '<h2>Saint Paul Hotel</h2>';
  }
  ?>
</div>
<div class="container-fluid py-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
      <span style="font-size:1.3rem;font-weight:600;">Breakfast Report</span>
      <button type="button" class="btn btn-light btn-sm no-print" onclick="window.print()">
        <i class="fa fa-print"></i> Print
      </button>
    </div>
    <div class="card-body print-section">
      <h4 class="mb-4" style="font-size: 1.5rem;font-weight:bold;">Breakfast Report - Currently In-House Guests (<?php echo date('Y-m-d'); ?>)</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th scope="col">No</th>
              <th scope="col">Guest Names</th>
              <th scope="col">Company</th>
              <th scope="col">Nationality</th>
              <th scope="col">Room Name</th>
              <th scope="col">Room Type</th>
              <th scope="col">Adult</th>
              <th scope="col">Children</th>
            </tr>
          </thead>
          <tbody id="bookingTableBody">
            <?php 
            $no = 0;
            $total_adult = 0;
            $total_child = 0;
            $today = date('Y-m-d');
            // Get only guests who are currently checked in and haven't checked out yet
            $sql = $db->prepare("SELECT * FROM tbl_acc_booking 
                                WHERE booking_status_id = 6 
                                AND checkin_date <= ? 
                                AND checkout_date > ?");
            $sql->execute([$today, $today]);
            while($row = $sql->fetch()){
              if(getGuestBookingOption($row['id'])==1){
                $no++;
                $total_adult += $row['num_adults'];
                $total_child += $row['num_children'];
                $checkin = htmlspecialchars($row['checkin_date'] ?? '');
            ?>
            <tr data-checkin="<?php echo $checkin; ?>">
              <td><strong><?php echo $no ?></strong></td>
              <td><strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
              <td><?php echo $row['company']?></td>
              <td><?php echo getGuestNationality($row['guest_id'])?></td>
              <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
              <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
              <td><?php echo $row['num_adults']?></td>
              <td><?php echo $row['num_children']?></td>
            </tr>
            <?php }
            }
            ?>
            <tr><td colspan='6'><strong>TOTAL</strong></td> <td><strong><?php echo number_format($total_adult) ?></strong></td> <td><strong><?php echo number_format($total_child) ?></strong></td></tr>
            <tr><td colspan='7'><strong>ALL TOTAL</strong></td><td><strong><?php echo number_format($total_child + $total_adult) ?></strong></td></tr>
          </tbody>
        </table>
      </div>
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
</div>

  <!-- Footer -->

</body>

</html>
