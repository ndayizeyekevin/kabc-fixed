<?php
include '../inc/conn.php';




// Base date (default: today)
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
if(isset($_GET['date'])){
    include '../inc/function.php';
}

// Generate 5-day range
$dates = [];
for ($i = 0; $i < 5; $i++) {
    $dates[] = date('Y-m-d', strtotime($date . " +$i day"));
}

// Fetch all rooms with their class
$rooms_sql = $conn->query("
    SELECT r.id, r.room_number, c.class_name
    FROM tbl_acc_room r
    INNER JOIN tbl_acc_room_class c ON r.room_class_id = c.id
    ORDER BY c.class_name, r.room_number
");
$rooms = $rooms_sql->fetch_all(MYSQLI_ASSOC);

// Fetch bookings for this date range
$booking_sql = $conn->prepare("
    SELECT b.id, b.guest_id, b.checkin_date, b.checkout_date, b.booking_status_id,
           br.room_id
    FROM tbl_acc_booking b
    INNER JOIN tbl_acc_booking_room br ON b.id = br.booking_id
    WHERE b.checkin_date <= ? 
      AND b.checkout_date >= ?
");
$booking_sql->bind_param("ss", $end_date, $start_date);
$start_date = $dates[0];
$end_date   = end($dates);
$booking_sql->execute();
$result   = $booking_sql->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);

// Organize bookings by room + date
$room_status = [];
foreach ($bookings as $b) {
    $room_id  = $b['room_id'];
    $checkin  = $b['checkin_date'];
    $checkout = $b['checkout_date'];

    foreach ($dates as $d) {
        if ($d >= $checkin && $d <= $checkout) {
            if ($d < $checkout) {
                if ($b['booking_status_id'] == 6) {
                    $status = 'in'; // Checked in
                } elseif (in_array($b['booking_status_id'], [1, 2])) {
                    $status = 'reserved'; // Reserved
                } else {
                    $status = null;
                    $checkin = null;
                    $checkout = null;
                }
            } else {
                $status = 'checkout'; // Checkout day
            }

            if ($status) {
                $room_status[$room_id][$d] = [
                    'status' => $status,
                    'guest'  => getGuestNames($b['guest_id'])
                ];
            }
        }
    }
}
?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>TYPE</th>
            <th>ROOM</th>
            <?php foreach ($dates as $d): ?>
                <th><?php echo $d; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['class_name']); ?></td>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <?php foreach ($dates as $d):
                    $cell  = $room_status[$room['id']][$d] ?? null;
                    $class = '';
                    $label = '';

                    if ($cell) {
                        switch ($cell['status']) {
                            case 'checkout':
                                $class = 'bg-light-pink';
                                $label = $cell['guest'];
                                break;
                            case 'in':
                                $class = 'bg-steel-blue';
                                $label = $cell['guest'];
                                break;
                            case 'reserved':
                                $class = 'bg-golden-yellow';
                                $label = $cell['guest'];
                                break;
                        }
                    }
                    ?>
                    <td style="<?php
                        if ($class == 'bg-light-pink') echo 'background:#f8d7da;';
                        else if ($class == 'bg-steel-blue') echo 'background:#4682b4;color:#fff;';
                        else if ($class == 'bg-golden-yellow') echo 'background:#ffd700;';
                    ?>">
                        <?php echo $label ? htmlspecialchars($label) : 'vacant'; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
