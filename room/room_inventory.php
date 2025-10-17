<?php
require_once '../inc/conn.php';

// Calculate week dates using robust logic
$today = new DateTime();
$today->setTime(0,0,0);
$weekOffset = isset($_GET['week']) ? intval($_GET['week']) : 0;
// If a date is provided, calculate the week offset so that the selected date is in the displayed week
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $selectedDate = new DateTime($_GET['date']);
    $selectedDayOfWeek = $selectedDate->format('N');
    $selectedMonday = clone $selectedDate;
    $selectedMonday->modify('-' . ($selectedDayOfWeek - 1) . ' days');
    $currentDayOfWeek = $today->format('N');
    $todayMonday = clone $today;
    $todayMonday->modify('-' . ($currentDayOfWeek - 1) . ' days');
    $diff = $todayMonday->diff($selectedMonday);
    $weekOffset = intval($diff->days / 7);
    if ($diff->invert) $weekOffset = -$weekOffset;
}

// Start with today's date
$today = new DateTime();
$today->setTime(0,0,0);

// Get the current day of week (1=Monday, 7=Sunday)
$currentDayOfWeek = $today->format('N');

// Calculate this week's Monday
$thisWeekMonday = clone $today;
$thisWeekMonday->modify('-' . ($currentDayOfWeek - 1) . ' days');

// Now apply the week offset
$monday = clone $thisWeekMonday;
$monday->modify("$weekOffset week");

$weekDates = [];
for ($i = 0; $i < 7; $i++) {
    $date = clone $monday;
    $date->modify("+$i days");
    $weekDates[] = $date->format('Y-m-d');
}

// Debug output for offset and week dates
echo "<div style='color:gray;font-size:12px;'>Week offset: $weekOffset | Week: " . $weekDates[0] . " to " . $weekDates[6] . "</div>";

// Get all rooms and their classes
// Fetch all rooms and their classes
$sql = "SELECT r.id as room_id, r.room_number, rc.class_name as room_type FROM tbl_acc_room r JOIN tbl_acc_room_class rc ON r.room_class_id = rc.id ORDER BY rc.class_name ASC, r.room_number ASC";
$result = $conn->query($sql);
$rooms = [];
$availableCount = 0;
$reservedCount = 0;
$occupiedCount = 0;
if ($result) {
    // Preload all bookings for the week
    $roomBookings = [];
    $dateStart = $weekDates[0];
    $dateEnd = $weekDates[6];
    $bookingSql = "SELECT br.room_id, b.checkin_date, b.checkout_date, b.booking_status_id, b.guest_id FROM tbl_acc_booking_room br JOIN tbl_acc_booking b ON br.booking_id = b.id WHERE b.checkin_date <= ? AND b.checkout_date >= ?";
    $bookingStmt = $conn->prepare($bookingSql);
    if ($bookingStmt) {
        $bookingStmt->bind_param("ss", $dateEnd, $dateStart);
        $bookingStmt->execute();
        $bookingRes = $bookingStmt->get_result();
        while ($bk = $bookingRes->fetch_assoc()) {
            $roomBookings[$bk['room_id']][] = $bk;
        }
        $bookingStmt->close();
    }

    // Preload guest names
    $guestNames = [];
    $guestIds = [];
    foreach ($roomBookings as $roomId => $bookings) {
        foreach ($bookings as $bk) {
            if (!empty($bk['guest_id'])) $guestIds[$bk['guest_id']] = true;
        }
    }
    if (count($guestIds)) {
        $ids = implode(',', array_map('intval', array_keys($guestIds)));
        $gsql = "SELECT id, first_name, last_name FROM tbl_acc_guest WHERE id IN ($ids)";
        $gres = $conn->query($gsql);
        if ($gres) {
            while ($g = $gres->fetch_assoc()) {
                $guestNames[$g['id']] = $g['first_name'] . ' ' . $g['last_name'];
            }
        }
    }

    while ($row = $result->fetch_assoc()) {
        $roomData = [
            'room_id' => $row['room_id'],
            'room_no' => $row['room_number'],
            'room_type' => $row['room_type'],
            'days' => []
        ];
        foreach ($weekDates as $date) {
            $status = 'available';
            $guest = '';
            if (!empty($roomBookings[$row['room_id']])) {
                foreach ($roomBookings[$row['room_id']] as $bk) {
                     if ($date >= $bk['checkin_date'] && $date < $bk['checkout_date']) {
                        // Map booking_status_id to status
                        $sid = $bk['booking_status_id'];
                        if (in_array($sid, [6])) {
                            $status = 'occupied';
                        } elseif (in_array($sid, [14])) {
                            $status = 'maintenance';
                        } elseif (in_array($sid, [1, 2, 13])) {
                            $status = 'reserved';
                        } elseif (in_array($sid, [5, 12])) {
                            $status = 'available';
                        }
                        // Show guest name for reserved, occupied, maintenance
                        if (in_array($status, ['reserved','occupied','maintenance']) && !empty($bk['guest_id']) && isset($guestNames[$bk['guest_id']])) {
                            $guest = $guestNames[$bk['guest_id']];
                        }
                        break;
                    }
                }
            }
            $roomData['days'][] = [
                'status' => $status,
                'guest' => $guest,
                'date' => $date
            ];
            switch ($status) {
                case 'available':
                    $availableCount++;
                    break;
                case 'reserved':
                    $reservedCount++;
                    break;
                case 'occupied':
                    $occupiedCount++;
                    break;
            }
        }
        $rooms[] = $roomData;
    }
}
?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      a{
        text-decoration: none;
      }
        .inventory-table { overflow-x: auto; }
        .room-cell { min-width: 120px; height: 60px; cursor: pointer; border: 1px solid #dee2e6; font-size: 12px; padding: 5px; position: relative; }
    .status-available { background-color: #28a745 !important; color: white; }
    .status-reserved { background-color: #ffc107 !important; color: black; }
    .status-occupied { background-color: #e91e63 !important; color: white; }
    .status-maintenance { background-color: #dc3545 !important; color: white; }
        .guest-name { font-weight: bold; font-size: 11px; }
        .legend-box { width: 20px; height: 20px; display: inline-block; margin-right: 5px; border: 1px solid #dee2e6; }
        .today { border: 2px solid #ffc107 !important; box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.5); }
        .card { margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-header { font-weight: bold; }
    </style>

    <div class="container">
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col">
                <h2 class="text-primary">Room Inventory Management</h2>
                <?php
                $firstDate = new DateTime($weekDates[0]);
                $lastDate = new DateTime($weekDates[6]);
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                echo '<p class="text-muted" id="dateRange">' .
                    $months[$firstDate->format('n')-1] . ' ' . $firstDate->format('d') . ', ' . $firstDate->format('Y') .
                    ' - ' .
                    $months[$lastDate->format('n')-1] . ' ' . $lastDate->format('d') . ', ' . $lastDate->format('Y') .
                    '</p>';
                ?>
            </div>
            <div class="row mt-3">
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h3 class="text-success" id="availableCount"><?php echo $availableCount; ?></h3>
                        <p class="mb-0">Available Rooms</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h3 class="text-warning" id="reservedCount"><?php echo $reservedCount; ?></h3>
                        <p class="mb-0">Reserved Rooms</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h3 class="text-danger" id="occupiedCount"><?php echo $occupiedCount; ?></h3>
                        <p class="mb-0">Occupied Rooms</p>
                    </div>
                </div>
            </div>
        </div>
            <div class="col-auto">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Room Status Legend</h6>
                        <div><span class="legend-box status-available"></span> Available / Checked Out</div>
                        <div><span class="legend-box status-reserved"></span> Reserved</div>
                        <div><span class="legend-box status-occupied"></span> Occupied (Checked In)</div>
                        <div><span class="legend-box status-maintenance"></span> Maintenance</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Weekly Room Inventory</h5>
                    </div>
                    <div class="col-auto">
                        <span class="text-white mx-2" id="weekDisplay">
                        <?php
                        echo $months[$firstDate->format('n')-1] . ' ' . $firstDate->format('d') . ' - ' .
                            $months[$lastDate->format('n')-1] . ' ' . $lastDate->format('d') . ', ' . $firstDate->format('Y');
                        ?>
                        </span>
                        <button class="btn btn-outline-light btn-sm ms-2" onclick="setWeek(0)">Today</button>
                        <button class="btn btn-light btn-sm" onclick="setWeek(<?php echo $weekOffset - 1; ?>)">← Previous Week</button>
                        <button class="btn btn-light btn-sm" onclick="setWeek(<?php echo $weekOffset + 1; ?>)">Next Week →</button>
                        <input type="date" id="jumpDate" class="form-control form-control-sm d-inline-block ms-2" style="width:160px;" value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : $today->format('Y-m-d'); ?>" min="2020-01-01" max="2100-12-31" title="Jump to week by date">
                        <button class="btn btn-primary btn-sm ms-1" onclick="jumpToWeek()">Go</button>
                        <script>
                        function setWeek(offset) {
                            var url = new URL(window.location.href);
                            url.searchParams.set('week', offset);
                            url.searchParams.delete('date');
                            window.location.href = url.toString();
                        }
                        function jumpToWeek() {
                            var dateStr = document.getElementById('jumpDate').value;
                            if (!dateStr) return;
                            var url = new URL(window.location.href);
                            url.searchParams.set('date', dateStr);
                            url.searchParams.delete('week');
                            window.location.href = url.toString();
                        }
                        </script>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="inventory-table">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 120px;">Room # / Type</th>
                                <?php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                foreach ($weekDates as $i => $date) {
                                    $dateObj = new DateTime($date);
                                    echo '<th class="text-center" id="day' . $i . '">' . $days[$i] . '<br>' . $months[$dateObj->format('n')-1] . ' ' . str_pad($dateObj->format('d'), 2, '0', STR_PAD_LEFT) . '</th>';
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody id="roomTableBody">
                            <?php
                            if (empty($rooms)) {
                                echo '<tr><td colspan="9" class="text-center">No rooms found</td></tr>';
                            } else {
                                $todayDate = date('Y-m-d');
                                foreach ($rooms as $room) {
                                    echo '<tr>';
                                    echo '<td class="fw-bold">' . htmlspecialchars($room['room_no']) . '<br><span style="font-weight:normal;font-size:11px;color:#888;">' . htmlspecialchars($room['room_type']) . '</span></td>';
                                    foreach ($room['days'] as $i => $day) {
                                        $statusClass = 'status-' . $day['status'];
                                        $isToday = ($day['date'] === $todayDate) ? 'today' : '';
                                        $dateObj = new DateTime($day['date']);
                                        $dateFormatted = $months[$dateObj->format('n')-1] . ' ' . str_pad($dateObj->format('d'), 2, '0', STR_PAD_LEFT);
                                        echo '<td class="room-cell text-center ' . $statusClass . ' ' . $isToday . '" data-date="' . $dateFormatted . '">';
                                        if (in_array($day['status'], ['reserved','occupied','maintenance'])) {
                                            if (!empty($day['guest'])) {
                                                echo '<div class="guest-name">' . htmlspecialchars($day['guest']) . '</div>';
                                                echo '<small>' . ucfirst($day['status']) . '</small>';
                                            } else if ($day['status'] === 'reserved') {
                                                echo '<div class="guest-name">Group Booking</div>';
                                                echo '<small>Reserved</small>';
                                            } else {
                                                echo ucfirst($day['status']);
                                            }
                                        } else {
                                            echo 'Available';
                                        }
                                        echo '</td>';
                                    }
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
                        </div>
