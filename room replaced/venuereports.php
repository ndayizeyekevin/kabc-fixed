<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../inc/conn.php';

$today = date('Y-m-d');
$room_booking = $active_amount = $event_booking = $room_sum = $accoupied = $available = $customers = $venue_customer = $expenses_payments = 0;

// Room bookings
$sql = "SELECT * FROM tbl_acc_booking WHERE booking_status_id = 2";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if (time() <= strtotime($row['checkin_date'])) {
            $room_booking++;
            $active_amount += $row['room_price'];
        }
    }
}

// Event bookings
$sql = "SELECT * FROM tbl_ev_venue_reservations WHERE status = 'Confirmed'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($today == $row['reservation_date']) {
            $event_booking++;
        }
    }
}

// Room payments
$sql = "SELECT * FROM payments";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($today == date('Y-m-d', $row['payment_time'])) {
            $room_sum += $row['amount'];
        }
    }
}

// Room status
$accoupied = $conn->query("SELECT COUNT(*) as total FROM tbl_acc_room WHERE status_id = 2")->fetch_assoc()['total'];
$available = $conn->query("SELECT COUNT(*) as total FROM tbl_acc_room WHERE status_id = 3")->fetch_assoc()['total'];

// Customers
$sql = "SELECT * FROM tbl_acc_guest";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($today == date('Y-m-d', strtotime($row['created_at']))) {
            $customers++;
        }
    }
}

$sql = "SELECT * FROM tbl_ev_customers";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($today == date('Y-m-d', strtotime($row['created_at']))) {
            $venue_customer++;
        }
    }
}

// Expenses
$sql = "SELECT * FROM expenses";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($today == date('Y-m-d', strtotime($row['create_time']))) {
            $expenses_payments += $row['price'];
        }
    }
}

function getPaymentMethods($id, $method) {
    include '../inc/conn.php';
    $sum = 0;
    $sql = "SELECT * FROM venue_payments WHERE booking_id = '$id' AND method = '$method'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sum += $row['amount'];
        }
    }
    return $sum;
}

?>

<style>
    form, .table, .table-considered {
        margin: 20px auto;
        width: 95%;
        max-width: 1100px;
    }
    form select, form input[type="date"], form button {
        margin: 10px;
        padding: 10px;
        font-size: 14px;
    }
    .table, .table-considered {
        border-collapse: collapse;
        width: 100%;
        background-color: #f9f9f9;
    }
    .table th, .table td, .table-considered th, .table-considered td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }
    .table th, .table-considered th {
        background-color: #e0e0e0;
        font-weight: bold;
    }
    h4 {
        margin-top: 30px;
        margin-bottom: 10px;
        font-size: 20px;
        text-align: center;
    }
    #printH {
        margin: 20px auto;
        display: block;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

<form method="post">
    <label>Select Venue</label>
    <select name="venue">
        <option value="0">ALL</option>
        <?php 
        $sql = $db->prepare("SELECT * FROM tbl_ev_venues");
        $sql->execute();
        while($row = $sql->fetch()) {
            echo "<option value='{$row['id']}'>{$row['venue_name']}</option>";
        }
        ?>
    </select>

    <label>From date</label>
    <input type="date" name="from" required>

    <label>To</label>
    <input type="date" name="to" required>

    <button type="submit" name="searchVenue">Check</button>
</form>

<button id="printH">Print</button>
<div id="content">
    <?php include '../holder/printHeader.php'; ?>

    <?php 
    $no = 0;
    $total = 0;
    $cash = $pos = $momo = $credit = 0;

    if (isset($_POST['searchVenue'])) {
        $venue = $_POST['venue'];
        $from = $_POST['from'];
        $to = $_POST['to'];

        echo "<h4>Venue Report from <strong>$from</strong> to <strong>$to</strong></h4>";

        if ($venue == 0) {
            $sql = $db->prepare("SELECT * FROM tbl_ev_venue_reservations WHERE (status = 'Checkedout' OR status = 'Confirmed') AND reservation_date BETWEEN ? AND ?");
            $sql->execute([$from, $to]);
        } else {
            $sql = $db->prepare("SELECT * FROM tbl_ev_venue_reservations WHERE (status = 'Checkedout' OR status = 'Confirmed') AND venue_id = ? AND reservation_date BETWEEN ? AND ?");
            $sql->execute([$venue, $from, $to]);
        }
    } else {
        echo "<h4>Venue Report for Today: <strong>$today</strong></h4>";
        $sql = $db->prepare("SELECT * FROM tbl_ev_venue_reservations WHERE status = 'Checkedout' OR status = 'Confirmed'");
        $sql->execute();
    }

    echo "<table class='table'><thead><tr>
        <th>No</th><th>Names</th><th>Venue</th><th>Date</th><th>Amount</th>
    </tr></thead><tbody>";

    while($row = $sql->fetch()) {
        $cash += getPaymentMethods($row['id'], 'cash');
        $pos += getPaymentMethods($row['id'], 'card');
        $momo += getPaymentMethods($row['id'], 'momo');
        $credit += getPaymentMethods($row['id'], 'credit');
        $total += getVenuAmount($row['venue_id']);

        echo "<tr>
            <td>" . (++$no) . "</td>
            <td>" . getVenuCustomer($row['customer_id']) . "</td>
            <td>" . getVenuname($row['venue_id']) . "</td>
            <td>{$row['reservation_date']}</td>
            <td>" . number_format((float)getVenuAmount($row['venue_id'])) . "</td>
        </tr>";
    }

    echo "<tr><td><strong>Total:</strong></td><td></td><td></td><td></td><td><strong>" . number_format($total) . "</strong></td></tr>";
    echo "</tbody></table>";
    ?>
</div>

<table class="table table-considered">
    <tr><th>Method</th><th>Amount</th></tr>
    <tr><th>Cash</th><th><?php echo number_format($cash); ?> RWF</th></tr>
    <tr><th>Mobile Money</th><th><?php echo number_format($momo); ?> RWF</th></tr>
    <tr><th>POS/CARD</th><th><?php echo number_format($pos); ?> RWF</th></tr>
    <tr><th>Credit</th><th><?php echo number_format($credit); ?> RWF</th></tr>
    <tr><th>Total</th><th><?php echo number_format($cash + $pos + $momo + $credit); ?> RWF</th></tr>
</table>

<?php include '../holder/printFooter.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
    $(document).ready(function () {
        $("#printH").click(function () {
            $("#headerprint").show();
            var printContents = document.getElementById('content').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });
        $("#headerprint").hide();
    });
</script>
