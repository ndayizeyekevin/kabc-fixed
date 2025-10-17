<?php
// session_start();
$chartType = $_SESSION['chartType'] ?? 'bar';

// ------------------ CONFIG ------------------
include '../inc/conn.php';

// ------------------ FUNCTION ------------------
function calculate_monthly_occupancy_index($month)
{
    global $conn;
    
    // Validate input month (1-12)
    $month = (int)$month;
    if ($month < 1 || $month > 12) {
        trigger_error("Invalid month provided", E_USER_WARNING);
        return 0;
    }

    $year = date('Y');
    $start_date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
    $end_date = date("Y-m-t", strtotime($start_date));

    // Validate dates
    if (strtotime($start_date) === false || strtotime($end_date) === false) {
        trigger_error("Invalid date calculation", E_USER_WARNING);
        return 0;
    }

    // Get all bookings with status 5 or 6
    $bookings = [];
    $sql = "SELECT * FROM tbl_acc_booking WHERE booking_status_id IN (5,6)";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        $result->free();
    } else {
        trigger_error("Database query failed: " . $conn->error, E_USER_WARNING);
        return 0;
    }

    // Get total rooms count
    $total_rooms = 0;
    $room_result = $conn->query("SELECT COUNT(*) as room_count FROM tbl_acc_room");
    if ($room_result) {
        $row = $room_result->fetch_assoc();
        $total_rooms = (int)$row['room_count'];
        $room_result->free();
    } else {
        trigger_error("Failed to get room count: " . $conn->error, E_USER_WARNING);
        return 0;
    }
    
    $current_time = time();
    $total_days = 0;
    $total_guests = 0;
    $current_date = $start_date;

    while ($current_date <= $end_date) {
        $guest_count = 0;
        $current_timestamp = strtotime($current_date);
        
        if ($current_timestamp === false) {
            trigger_error("Invalid date in loop: $current_date", E_USER_WARNING);
            $current_date = date("Y-m-d", strtotime($current_date . " +1 day"));
            continue;
        }

        foreach ($bookings as $row) {
            // Validate and parse dates
            $checkin = !empty($row['checkin_date']) ? date('Y-m-d', strtotime($row['checkin_date'])) : null;
            $checkout = !empty($row['checkout_date']) ? date('Y-m-d', strtotime($row['checkout_date'])) : null;
            
            if (!$checkin || !$checkout) {
                continue; // Skip invalid bookings
            }

            if ($current_date >= $checkin &&
                $current_date < $checkout &&
                $current_time >= $current_timestamp) {
                $adults = isset($row['num_adults']) ? (int)$row['num_adults'] : 0;
                $children = isset($row['num_children']) ? (int)$row['num_children'] : 0;
                $guest_count += $adults + $children;
            }
        }

        // For daily calculation, ensure index is at least 1 if there are guests
        $daily_index = ($guest_count > 0 && $total_rooms > 0) ? max(1, $guest_count / $total_rooms) : 0;
        $total_guests += $daily_index * $total_rooms; // Adjust total guests to account for minimum index
        
        $total_days++;
        $current_date = date("Y-m-d", strtotime($current_date . " +1 day"));
    }

    if ($total_days == 0 || $total_rooms == 0) {
        return 0;
    }

    // Calculate monthly average - already accounts for daily minimums
    $occupancy_index = $total_guests / ($total_days * $total_rooms);
    return round($occupancy_index, 2);
}
function get_daily_occupancy_index($day){
    global $conn;
    // Ensure $conn is a valid MySQLi object
    if (!($conn instanceof mysqli)) {
        trigger_error("Database connection is not valid.", E_USER_ERROR);
        return 0;
    }
    $day_escaped = $conn->real_escape_string($day);

    $sql = "SELECT COUNT(*) as total_booked FROM `tbl_acc_booking` WHERE booking_status_id IN (5,6) AND checkin_date <= '$day_escaped' AND checkout_date > '$day_escaped'";
    $result = $conn->query($sql);
    $total_booked = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        $total_booked = $row['total_booked'];
        $result->free();
    }

    $sql = "SELECT COUNT(*) as total_rooms FROM `tbl_acc_room`";
    $result = $conn->query($sql);
    $total_rooms = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        $total_rooms = $row['total_rooms'];
        $result->free();
    }

    return ($total_rooms > 0) ? round($total_booked * 100 / $total_rooms, 2) : 0;
}
// ------------------ HANDLE AJAX: CHART DATA ------------------
if (isset($_GET['action']) && $_GET['action'] === 'fetch_chart_data') {
    header('Content-Type: application/json');
    $data = [];

    for ($i = 1; $i <= 12; $i++) {
        $data[] = calculate_monthly_occupancy_index($i);
    }

    echo json_encode($data);
    exit;
}

// ------------------ HANDLE AJAX: DATE RANGE ------------------
if (isset($_GET['action']) && $_GET['action'] === 'fetch_daterange') {
    $start = $_GET['start'];
    $end = $_GET['end'];

    $sql = "SELECT * FROM tbl_acc_booking WHERE booking_status_id IN (5,6)";
    $result = $conn->query($sql);
    $total_rooms = $conn->query("SELECT * FROM tbl_acc_room")->num_rows;
    $current_time = time();

    $start_date = new DateTime($start);
    $end_date = new DateTime($end);
    $current_time = time();

    $html = '';

    // Process each day in the range
    while ($start_date <= $end_date) {
        $current_day = $start_date->format('Y-m-d');
        $guest_count = 0;
        $stmt = $conn->prepare("SELECT SUM(num_children) + SUM(num_adults) as gest_count FROM tbl_acc_booking WHERE booking_status_id IN (5,6) AND checkin_date <= ? AND checkout_date > ?");
        $stmt->bind_param("ss", $current_day, $current_day);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            $guest_count = (int)$row['gest_count'];
            $stmt->close();
        } else {
            trigger_error("Database query failed: " . $conn->error, E_USER_WARNING);
            continue;
        }
        // find total rooms occupied on that day
        $stmt = $conn->prepare("SELECT COUNT(*) as total_rooms FROM tbl_acc_booking WHERE booking_status_id IN (5,6) AND checkin_date <= ? AND checkout_date > ?");
        $stmt->bind_param("ss", $current_day, $current_day);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            $total_rooms = (int)$row['total_rooms'];
            $stmt->close();
        } else {
            trigger_error("Database query failed: " . $conn->error, E_USER_WARNING);
            continue;
        }
        $gs_count = $guest_count;
        // Ensure index is at least 1 if there are guests
        if ($guest_count > 0 && $total_rooms > 0) {
            $guest_index = max(1, $guest_count / $total_rooms);
        } else {
            $guest_index = 0; // No guests or rooms
        }
        // Calculate occupancy index
        if ($total_rooms > 0) {
            $guest_count = round($guest_count * 100 / $total_rooms, 2);
        } else {
            $guest_count = 0; // No rooms available
        }

        $index = $guest_index;
        $class = ($index > 1) ? 'highlight' : '';
        
        $html .= "<tr class='$class'><td>$current_day</td><td>" . round($index, 2) . "</td></tr>";
        
        $start_date->add(new DateInterval('P1D'));
    }

    echo $html;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Occupancy Index</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
</head>
<body>
    <style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 8px 12px;
        border: 1px solid #ccc;
        text-align: center;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #e6f7ff;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    .highlight {
        background-color: #d4edda !important;
        color: #155724;
        font-weight: bold;
    }
</style>

<div class="container">
    <h2>Room Occupancy Index - <?php echo date('Y'); ?></h2>

    <label>Choose Chart Type:</label>
    <select id="chartType" onchange="renderChart()">
        <option value="bar" <?= $chartType == 'bar' ? 'selected' : '' ?>>Bar</option>
        <option value="line" <?= $chartType == 'line' ? 'selected' : '' ?>>Line</option>
    </select>

    <canvas id="occupancyChart" width="600" height="300"></canvas>

    <hr>
    <h4>Check by Date Range</h4>
    <input type="date" id="start_date">
    <input type="date" id="end_date">
    <button onclick="getDateRangeData()">Check</button>

    <table border="1" cellpadding="5" style="margin-top:20px;">
        <thead>
            <tr><th>Date</th><th>Occupancy Index</th></tr>
        </thead>
        <tbody id="dateRangeResult"></tbody>
    </table>
</div>

<script>
let chart;

function renderChart() {
    const type = document.getElementById("chartType").value;

    fetch("room_occupancy_index.php?action=fetch_chart_data")
        .then(res => res.json())
        .then(data => {
            const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const ctx = document.getElementById("occupancyChart").getContext("2d");
            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Room Occupancy Index",
                        data: data,
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 2,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: { 
                                beginAtZero: true,
                                // Ensure y-axis shows whole numbers since we can't have fractional occupancy below 1
                                callback: function(value) {
                                    if (value % 1 === 0) {
                                        return value;
                                    }
                                }
                            },
                            // Set suggested minimum to 0 and maximum based on data
                            suggestedMin: 0,
                            suggestedMax: Math.max(...data) > 0 ? Math.max(...data) + 1 : 5
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return "Occupancy Index: " + tooltipItem.yLabel;
                            }
                        }
                    }
                }
            });
        });
}

function getDateRangeData() {
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;

    if (!start || !end) {
        alert("Please select both start and end dates.");
        return;
    }

    document.getElementById("dateRangeResult").innerHTML = "<tr><td colspan='2'>Loading...</td></tr>";

    fetch("room_occupancy_index.php?action=fetch_daterange&start=" + start + "&end=" + end)
        .then(res => res.text())
        .then(html => {
            document.getElementById("dateRangeResult").innerHTML = html;
        });
}

window.onload = function () {
    renderChart();
    
    // Set default dates (today and tomorrow)
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    document.getElementById('start_date').value = today;
    document.getElementById('end_date').value = tomorrowStr;
};
</script>
</body>
</html>
