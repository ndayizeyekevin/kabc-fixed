
<style>
  .printHeader {
    display: none !important;
  }
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
      top: 80px;
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

<div class="printHeader">
  <?php 
  if (file_exists("../holder/printHeader.php")) {
    include "../holder/printHeader.php";
  } else {
    echo '<h2>Saint Paul Hotel</h2>';
  }
  ?>
</div>
<?php 
include '../inc/conn.php';

$room_booking = 0;
$today = date('Y-m-d');
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
    if(time() <= strtotime($row['checkout_date'])){
      $room_booking = $room_booking + 1; 
    }
  }
}
?>
<div class="container-fluid py-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
      <span style="font-size:1.3rem;font-weight:600;">In House Booking</span>
      <button type="button" class="btn btn-light btn-sm no-print" onclick="window.print()">
        <i class="fa fa-print"></i> Print
      </button>
    </div>
    <div class="card-body print-section">
      <h4 class="mb-4" style="font-size: 1.5rem;font-weight:bold;">In House Booking as on <strong><?php echo date('Y-m-d'); ?></strong></h4>
      <!-- Date Range Filter (Client-side only) -->
      <form class="row g-3 align-items-end mb-3 no-print" id="dateRangeForm" onsubmit="return false;">
        <div class="col-auto">
          <label for="fromDate" class="form-label mb-0">From</label>
          <input type="date" class="form-control" id="fromDate" name="fromDate">
        </div>
        <div class="col-auto">
          <label for="toDate" class="form-label mb-0">To</label>
          <input type="date" class="form-control" id="toDate" name="toDate">
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-primary" onclick="filterByDate()">Filter</button>
          <button type="button" class="btn btn-secondary" onclick="resetDateFilter()">Reset</button>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th scope="col">No</th>
              <th scope="col">Guest Names</th>
              <th scope="col">Room Name</th>
              <th scope="col">Room Type</th>
              <th scope="col">Check In</th>
              <th scope="col">Check Out</th>
              <th scope="col">Count</th>
              <th scope="col">Room Rate</th>
              <th scope="col">Pax</th>
            </tr>
          </thead>
          <tbody id="bookingTableBody">
            <?php 
            $no = 0;
            $sql = $db->prepare("SELECT * FROM tbl_acc_booking where booking_status_id = 6");
            $sql->execute();
            while($row = $sql->fetch()){
              $checkin = htmlspecialchars($row['checkin_date']);
            ?>
            <tr data-checkin="<?php echo $checkin; ?>">
              <td><strong><?php echo ++$no; ?></strong></td>
              <td><strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
              <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
              <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
              <td><?php echo $row['checkin_date']?></td>
              <td><?php echo $row['checkout_date'];?></td>
              <td><?php echo $row['duration']?></td>
              <td><?php echo number_format($row['room_price'])?></td>
              <td><?php echo $row['num_adults'] + $row['num_children']?></td>
            </tr>
            <?php }
            ?>
          </tbody>
<script>
// Date range filter for table rows (client-side only)
function filterByDate() {
  var from = document.getElementById('fromDate').value;
  var to = document.getElementById('toDate').value;
  var rows = document.querySelectorAll('#bookingTableBody tr');
  rows.forEach(function(row) {
    var checkin = row.getAttribute('data-checkin');
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
function resetDateFilter() {
  document.getElementById('fromDate').value = '';
  document.getElementById('toDate').value = '';
  filterByDate();
}
</script>
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
</div>
</div>
			
