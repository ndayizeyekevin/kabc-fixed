<?php
include '../inc/conn.php';

$today = date('Y-m-d');
$start_date = $_GET['start_date'] ?? $today;
$end_date   = $_GET['end_date'] ?? $today;

// Build datetime interval for filtering
$start_datetime = $start_date . ' 00:00:00';
$end_datetime   = $end_date . ' 23:59:59';

// Fetch bookings for the given date range
$bookings = $db->prepare("
    SELECT * FROM tbl_acc_booking 
    WHERE booking_status_id = 6
    AND checkin_date BETWEEN :start_date AND :end_date
");
$bookings->execute([
    ':start_date' => $start_date,
    ':end_date' => $end_date
]);

// Store results in an array for reuse in the table and print
$rows = $bookings->fetchAll(PDO::FETCH_ASSOC);

// Count totals
$total_adult = 0;
$total_child = 0;
foreach ($rows as $row) {
    if (getGuestBookingOption($row['id']) == 1) {
        $total_adult += $row['num_adults'];
        $total_child += $row['num_children'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Breakfast Report</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="colr-area">
    <div class="container">
        <h5 class="card-header">
            Breakfast Report (<?= htmlspecialchars($start_date) ?> to <?= htmlspecialchars($end_date) ?>)
        </h5>

        <!-- Date Filter Form -->
        <form method="GET" action="./index?resto=breakfast&" class="mb-3" style="margin: 20px 0;">
            <input type="hidden" name="resto" value="breakfast">
            
            <label><strong>Start Date:</strong></label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
            
            <label><strong>End Date:</strong></label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="./print.php?page=breakfast&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" class="btn btn-info" target="_blank">
              Print
            </a>
        </form>

        <div class="text-nowrap table-responsive" id="printArea">
            <table class="table" id="guestTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Guest Names</th>
                        <th>Company</th>
                        <th>CheckeIn</th>
                        <th>CheckOut</th>
                        <th>Nationality</th>
                        <th>Room Type</th>
                        <th>Pax</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 0;
                foreach ($rows as $row) {
                    if (getGuestBookingOption($row['id']) == 1) {
                      if($row['company'] == ''){
                        $row['company'] = '-';
                      }
                        echo "<tr>
                            <td><strong>".(++$no)."</strong></td>
                            <td><strong>".getGuestNames($row['guest_id'])."</strong></td>
                            <td>{$row['company']}</td>
                            <td>{$row['checkin_date']}</td>
                            <td>{$row['checkout_date']}</td>
                            <td>".getRoomName(getBookedRoom($row['id']))."</td>
                            <td>".getRoomClassType(getRoomClass(getBookedRoom($row['id'])))."</td>
                            <td>Adult: {$row['num_adults']} | Children: {$row['num_children']}</td>
                        </tr>";
                    }
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='7'><strong>TOTAL</strong></td> 
                        <td><strong><?= $total_adult ?> Adult(s) | <?= $total_child ?> Child(ren)</strong></td>
                    </tr>
                    <tr>
                        <td colspan='7'><strong>ALL TOTAL</strong></td>
                        <td><strong><?= number_format($total_child + $total_adult) ?> People</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div id="showToast" class="toast-container position-relative"></div>

<script>
$(document).ready(function () {
    $('#guestTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "ordering": false,
        "searching": true,
        "paging": true,
        "info": true
    });
});
</script>

</body>
</html>
