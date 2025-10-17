<?php 
include '../inc/conn.php';

define('TOTAL_ROOMS', 72); // Adjust this if needed

function getBookings() {
    global $conn;
    $sql = "SELECT * FROM tbl_acc_booking WHERE booking_status_id IN (5,6)";
    $result = $conn->query($sql);
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    return $bookings;
}

function soldRoom($date, $bookings) {
    $count = 0;
    foreach ($bookings as $row) {
    // Skip if either date is empty/null
    if (empty($row['checkin_date']) || empty($row['checkout_date'])) {
        continue;
    }
    
    // Convert dates to timestamps safely
    $checkin = strtotime($row['checkin_date']);
    $checkout = strtotime($row['checkout_date']);
    $current = strtotime($date);
    
    // Only compare if all dates are valid
    if ($checkin !== false && $checkout !== false && $current !== false) {
        if ($current >= $checkin && $current < $checkout) {
            $count++;
        }
    }
}
    return $count;
}

function roomOccupancy($date, $bookings) {
    $count = 0;
    foreach ($bookings as $row) {
    // Skip if either date is empty/null
    if (empty($row['checkin_date']) || empty($row['checkout_date'])) {
        continue;
    }
    
    // Convert dates to timestamps safely
    $checkin = strtotime($row['checkin_date']);
    $checkout = strtotime($row['checkout_date']);
    $current = strtotime($date);
    
    // Only compare if all dates are valid
    if ($checkin !== false && $checkout !== false && $current !== false) {
        if ($current >= $checkin && $current < $checkout) {
            $count++;
        }
    }
}
    return $count;
}

function average_room_rate($date,$b) {
    global $conn;

    // query bookings
    $sql = "SELECT AVG(room_price) AS r_price FROM tbl_acc_booking WHERE booking_status_id IN (5,6) AND checkin_date <= '$date' AND checkout_date > '$date' ORDER BY checkin_date";
    $result = $conn->query($sql);
    if (!$result || $result->num_rows == 0) {   
        return 0;
    }
    $row = $result->fetch_assoc();
    return $row['r_price'] ? $row['r_price'] : 0;
}

function room_occupancy_index($date, $bookings) {
    $guest = 0;
    foreach ($bookings as $row) {
        if (strtotime($date) >= strtotime($row['checkin_date']) && strtotime($date) <= strtotime($row['checkout_date'])) {
            $guest += $row['num_adults'] + $row['num_children'];
        }
    }
    return $guest;
}

function getDailyRevpar($date, $bookings) {
    $total = 0;
    foreach ($bookings as $row) {
        if (strtotime($date) >= strtotime($row['checkin_date']) && strtotime($date) <= strtotime($row['checkout_date'])) {
            $total += $row['room_price'];
        }
    }
    return $total;
}

function getDailyOccupancyRate($date, $bookings) {
    $sold = soldRoom($date, $bookings);
    return $sold / TOTAL_ROOMS;
}

function cumurative($start, $end, $type, $bookings, $format = 'Y-m-d') {
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    $total = 0;
    $days = 0;
    $occupancyCount = 0;

    foreach ($period as $date) {
        $day = $date->format($format);
        $days++;

        switch ($type) {
            case 'daily_occupancy_rate':
                $total += soldRoom($day, $bookings);
                break;

            case 'average_room_rate':
                $occupancy = roomOccupancy($day, $bookings);
                $rate = average_room_rate($day, $bookings);
                $occupancyCount += $occupancy;
                $total += $rate;
                break;

            case 'room_occupancy_index':
                $occupancy = roomOccupancy($day, $bookings);
                $index = room_occupancy_index($day, $bookings);
                $occupancyCount += $occupancy;
                $total += $index;
                break;

            case 'revpar':
                $total += getDailyRevpar($day, $bookings);
                break;
        }
    }

    if ($type == 'daily_occupancy_rate') {
        return round(($total / (TOTAL_ROOMS * $days)) * 100, 2); // percent
    }

    if (in_array($type, ['average_room_rate', 'room_occupancy_index'])) {
        // return $occupancyCount > 0 ? round($total / $occupancyCount, 2) : 0;
        return $total;
    }

    if ($type == 'revpar') {
        return round($total / (TOTAL_ROOMS * $days), 2);
    }

    return 0;
}

function getDatesFromRange($start, $end, $type, $bookings, $format = 'Y-m-d') {
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
    $rows = "";

    foreach ($period as $date) {
        $day = $date->format($format);
        switch ($type) {
            case 'daily_occupancy_rate':
                $value = round(getDailyOccupancyRate($day, $bookings) * 100, 2) . " %";
                break;
            case 'average_room_rate':
                $occupancy = roomOccupancy($day, $bookings);
                $value = round(average_room_rate($day, $bookings), 2)?? "0";
                break;
            case 'room_occupancy_index':
                $occupancy = roomOccupancy($day, $bookings);
                $value = $occupancy > 0 ? round(room_occupancy_index($day, $bookings) / $occupancy, 2) : "0";
                break;
            case 'revpar':
                $value = round(getDailyRevpar($day, $bookings) / TOTAL_ROOMS, 2);
                break;
            default:
                $value = "N/A";
        }
        $rows .= "<tr><td>$day</td><td>$value</td></tr>";
    }

    return $rows;
}

// MAIN HANDLER
if ($_REQUEST['t'] == 0) {
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $type = $_POST['type'];
    //fetch bookings
    if (empty($start) || empty($end)) {
        echo "<tr><td colspan='2'><center><h6>Please select a valid date range</h6></center></td></tr>";
        exit;
    }
    if (strtotime($start) > strtotime($end)) {
        echo "<tr><td colspan='2'><center><h6>Start date cannot be later than end date</h6></center></td></tr>";
        exit;
    }
 // query bookings
$sql = "SELECT AVG(room_price) r_price FROM tbl_acc_booking WHERE booking_status_id IN (5,6)  AND checkin_date <= ? AND checkout_date > ? ORDER BY checkin_date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $end, $start);
$stmt->execute();
$result = $stmt->get_result();
    // $result = $conn->query($sql);
    if ($result->num_rows == 0) {   
        echo "<tr><td colspan='2'><center><h6>No bookings found for the selected date range</h6></center></td></tr>";
        exit;
    }
  

    $bookings = getBookings();

    $table = getDatesFromRange($start, $end, $type, $bookings);
    $total = cumurative($start, $end, $type, $bookings);

    echo "
    <table border='1' style='width:100%; border-collapse:collapse; text-align:center'>
        <thead>
            <tr style='background:#eee'><th>Date</th><th>Value</th></tr>
        </thead>
        <tbody>
            $table
            <tr style='font-weight:bold; background:#f0f0f0'>
                <td>Total</td>
                <td>$total</td>
            </tr>
        </tbody>
    </table>";
} else {
    $month = $_POST['month']; // format: YYYY-MM
    $type = $_POST['type'];
    $start = $month . "-01";
    $end = date("Y-m-t", strtotime($start));
    $bookings = getBookings();
    echo cumurative($start, $end, $type, $bookings);
}
?>