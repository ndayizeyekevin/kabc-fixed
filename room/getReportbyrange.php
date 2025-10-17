<?php
include '../inc/conn.php';

// Get total rooms dynamically from database
function getTotalRooms() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as room_count FROM tbl_acc_room");
    if ($result && $row = $result->fetch_assoc()) {
        return (int)$row['room_count'];
    }
    return 73; // Fallback to 73 if query fails
}

define('TOTAL_ROOMS', getTotalRooms());

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

function average_room_rate($date, $bookings) {
    global $conn;

    // Calculate total income and total rooms occupied for the specific date
    $totalIncome = 0;
    $roomsOccupied = 0;

    foreach ($bookings as $row) {
        // Skip if either date is empty/null
        if (empty($row['checkin_date']) || empty($row['checkout_date'])) {
            continue;
        }

        // Convert dates to timestamps safely
        $checkin = strtotime($row['checkin_date']);
        $checkout = strtotime($row['checkout_date']);
        $current = strtotime($date);

        // Check if the room is occupied on this date
        if ($checkin !== false && $checkout !== false && $current !== false) {
            if ($current >= $checkin && $current < $checkout) {
                // Room is occupied on this date
                $roomsOccupied++;
                // Add the daily rate (room_price is per night)
                $totalIncome += $row['room_price'];
            }
        }
    }

    // Calculate average room rate: Total Income / Total Rooms Occupied
    if ($roomsOccupied > 0) {
        return $totalIncome / $roomsOccupied;
    }

    return 0;
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
    $rate = ($sold / TOTAL_ROOMS) * 100;
    return $rate;
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
                $roomsOccupied = soldRoom($day, $bookings);
                $dailyIncome = 0;

                // Calculate daily income for this date
                foreach ($bookings as $row) {
                    if (empty($row['checkin_date']) || empty($row['checkout_date'])) {
                        continue;
                    }
                    $checkin = strtotime($row['checkin_date']);
                    $checkout = strtotime($row['checkout_date']);
                    $current = strtotime($day);

                    if ($checkin !== false && $checkout !== false && $current !== false) {
                        if ($current >= $checkin && $current < $checkout) {
                            $dailyIncome += $row['room_price'];
                        }
                    }
                }

                $occupancyCount += $roomsOccupied; // Total rooms occupied across all days
                $total += $dailyIncome; // Total income across all days
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

    if ($type == 'average_room_rate') {
        // Period Average Room Rate = Total Income / Total Rooms Occupied
        return $occupancyCount > 0 ? round($total / $occupancyCount, 2) : 0;
    }

    if ($type == 'room_occupancy_index') {
        return $occupancyCount > 0 ? round($total / $occupancyCount, 2) : 0;
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
                $roomsOccupied = soldRoom($day, $bookings);
                $occupancyRate = round(getDailyOccupancyRate($day, $bookings), 2);
                $value = $occupancyRate . " %";
                $rows .= "<tr><td>$day</td><td>$roomsOccupied / " . TOTAL_ROOMS . "</td><td>$value</td></tr>";
                continue 2; // Skip the default row append
                break;
            case 'average_room_rate':
                $roomsOccupied = soldRoom($day, $bookings);
                $dailyIncome = 0;

                // Calculate daily income
                foreach ($bookings as $row) {
                    if (empty($row['checkin_date']) || empty($row['checkout_date'])) {
                        continue;
                    }
                    $checkin = strtotime($row['checkin_date']);
                    $checkout = strtotime($row['checkout_date']);
                    $current = strtotime($day);

                    if ($checkin !== false && $checkout !== false && $current !== false) {
                        if ($current >= $checkin && $current < $checkout) {
                            $dailyIncome += $row['room_price'];
                        }
                    }
                }

                $avgRate = $roomsOccupied > 0 ? round($dailyIncome / $roomsOccupied, 2) : 0;
                $value = number_format($avgRate, 0);
                $rows .= "<tr><td>$day</td><td>" . number_format($dailyIncome, 0) . "</td><td>$roomsOccupied</td><td>$value</td></tr>";
                continue 2; // Skip the default row append
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

    // Calculate totals for the period
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
    $totalRoomsOccupied = 0;
    $totalIncome = 0;
    $days = 0;

    foreach ($period as $date) {
        $day = $date->format('Y-m-d');
        $totalRoomsOccupied += soldRoom($day, $bookings);

        // Calculate daily income
        foreach ($bookings as $row) {
            if (empty($row['checkin_date']) || empty($row['checkout_date'])) {
                continue;
            }
            $checkin = strtotime($row['checkin_date']);
            $checkout = strtotime($row['checkout_date']);
            $current = strtotime($day);

            if ($checkin !== false && $checkout !== false && $current !== false) {
                if ($current >= $checkin && $current < $checkout) {
                    $totalIncome += $row['room_price'];
                }
            }
        }

        $days++;
    }

    if ($type == 'daily_occupancy_rate') {
        echo "
        <tr><td colspan='3' style='font-weight:bold; background:#d4edda; color:#155724; text-align:left; padding:10px;'>
            <strong>📊 Period Summary:</strong><br>
            Total Days: $days | Total Rooms Occupied: $totalRoomsOccupied | Total Available: " . (TOTAL_ROOMS * $days) . "<br>
            <strong>Formula:</strong> ($totalRoomsOccupied / " . (TOTAL_ROOMS * $days) . ") × 100 = <strong>$total%</strong>
        </td></tr>
        $table
        <tr style='font-weight:bold; background:#007bff; color:white;'>
            <td>Period Average</td>
            <td>$totalRoomsOccupied / " . (TOTAL_ROOMS * $days) . "</td>
            <td>$total %</td>
        </tr>";
    } elseif ($type == 'average_room_rate') {
        echo "
        <tr><td colspan='4' style='font-weight:bold; background:#d4edda; color:#155724; text-align:left; padding:10px;'>
            <strong>💰 Period Summary:</strong><br>
            Total Days: $days | Total Income: " . number_format($totalIncome, 0) . " | Total Rooms Occupied: $totalRoomsOccupied<br>
            <strong>Formula:</strong> (" . number_format($totalIncome, 0) . " / $totalRoomsOccupied) = <strong>" . number_format($total, 0) . "</strong>
        </td></tr>
        <tr style='background:#007bff; color:white;'><th>Date</th><th>Daily Income</th><th>Rooms Occupied</th><th>Avg Room Rate</th></tr>
        $table
        <tr style='font-weight:bold; background:#007bff; color:white;'>
            <td>Period Average</td>
            <td>" . number_format($totalIncome, 0) . "</td>
            <td>$totalRoomsOccupied</td>
            <td>" . number_format($total, 0) . "</td>
        </tr>";
    } else {
        echo "
        <tr style='background:#eee'><th>Date</th><th>Value</th></tr>
        $table
        <tr style='font-weight:bold; background:#f0f0f0'>
            <td>Total</td>
            <td>$total</td>
        </tr>";
    }
} else {
    $month = $_POST['month']; // format: YYYY-MM
    $type = $_POST['type'];
    $start = $month . "-01";
    $end = date("Y-m-t", strtotime($start));
    $bookings = getBookings();
    echo cumurative($start, $end, $type, $bookings);
}
?>
