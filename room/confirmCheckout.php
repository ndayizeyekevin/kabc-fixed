<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../inc/conn.php';
include_once '../inc/function.php';

// Helper function to safely prepare statements
function prepareStmt($conn, $sql) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);
    return $stmt;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['corporate_id'])) {
    // GET: corporate booking confirmation
    $booking_id     = intval($_GET['booking_id'] ?? 0);
    $room_id        = intval($_GET['room_ids'] ?? 0);
    $company_name   = mysqli_real_escape_string($conn, $_GET['company_name'] ?? '');
    $tin_number     = mysqli_real_escape_string($conn, $_GET['tin_number'] ?? '');
    $booking_amount = floatval($_GET['booking_amount'] ?? 0);

    if ($booking_id === 0) die("Error: Invalid booking ID.");

    // Check if booking exists
    $stmt = prepareStmt($conn, "SELECT booking_status_id FROM tbl_acc_booking WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($booking_status_id);
    if (!$stmt->fetch()) die("Error: Booking not found.");
    $stmt->close();

    // Get room_id from DB if missing
    if ($room_id === 0) {
        $stmt = prepareStmt($conn, "SELECT room_id FROM tbl_acc_booking_room WHERE booking_id = ? LIMIT 1");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->bind_result($room_id);
        $stmt->fetch();
        $stmt->close();

        if ($room_id === 0) die("Error: No room assigned to this booking.");
    }

    // Update booking record
    $stmt = prepareStmt($conn, "UPDATE tbl_acc_booking SET company = ?, booking_status_id = 5, corporate = 1 WHERE id = ?");
    $stmt->bind_param("si", $company_name, $booking_id);
    if (!$stmt->execute()) die("Error updating booking: " . $stmt->error);
    $stmt->close();

    // Insert payment if not exists
    $stmt = prepareStmt($conn, "SELECT payment_id FROM payments WHERE booking_id = ? LIMIT 1");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $payment_time = time();
        $method       = 'Credit';
        $remark       = 'Corporate';
        $currency     = 1;
        $rate         = 1;

        $stmtInsert = prepareStmt($conn, "INSERT INTO payments (booking_id, amount, payment_time, method, remark, currency, rate, tin_number)
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("idsssiis", $booking_id, $booking_amount, $payment_time, $method, $remark, $currency, $rate, $tin_number);
        if (!$stmtInsert->execute()) die("Error inserting payment: " . $stmtInsert->error);
        $stmtInsert->close();
    }
    $stmt->close();

    // Insert into tbl_acc_booking_room if not exists
    $stmt = prepareStmt($conn, "SELECT 1 FROM tbl_acc_booking_room WHERE booking_id = ? AND room_id = ?");
    $stmt->bind_param("ii", $booking_id, $room_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmtInsert = prepareStmt($conn, "INSERT INTO tbl_acc_booking_room (booking_id, room_id) VALUES (?, ?)");
        $stmtInsert->bind_param("ii", $booking_id, $room_id);
        if (!$stmtInsert->execute()) die("Error inserting tbl_acc_booking_room: " . $stmtInsert->error);
        $stmtInsert->close();
    }
    $stmt->close();

    // Update room status
    $room_number = getRoomName($room_id);
    if ($room_number !== null) {
        $status = updateRoomStatus($room_number, 12);
        if ($status === 1) {
            echo "<script>alert('Corporate details added successfully.'); window.location.href='index?resto=corporate';</script>";
            exit;
        } else {
            die("Error updating room status for room number: " . htmlspecialchars($room_number));
        }
    } else {
        die("Error: Room not found for ID " . htmlspecialchars($room_id));
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) ) {
    // POST: checkout confirmation
    $booking_id = intval($_GET['booking_id'] ?? 0);
    $balance    = floatval($_GET['balance'] ?? 0);
    $room       = mysqli_real_escape_string($conn, $_GET['room'] ?? '');
    $today      = date('Y-m-d');

    if ($booking_id === 0) die("Error: Invalid booking ID.");

    // Get room from DB if not provided
    if (empty($room)) {
        $stmt = prepareStmt($conn, "SELECT room_id FROM tbl_acc_booking_room WHERE booking_id = ? LIMIT 1");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->bind_result($room_id);
        $stmt->fetch();
        $stmt->close();

        if (empty($room_id)) die("Error: No room assigned to this booking.");
        $room = getRoomName($room_id);
    }

    // Update booking status
    $stmt = prepareStmt($conn, "UPDATE tbl_acc_booking SET booking_status_id = 5, balance = ?, checked_out_ate = ? WHERE id = ?");
    $stmt->bind_param("dsi", $balance, $today, $booking_id);
    if ($stmt->execute()) {
        $status = updateRoomStatus($room, 12);
        if ($status === 1) {
            echo "<script>alert('Checked Out successfully'); window.location='index?resto=Reservation';</script>";
        } else {
            die("Error updating room status for room: " . htmlspecialchars($room));
        }
    } else {
        die("Error updating booking: " . $stmt->error);
    }
    $stmt->close();
}

$conn->close();
?>
