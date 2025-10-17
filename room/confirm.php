<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// This is the correct, refined version of the script.
// It includes the function.php file to access its functions.
include '../inc/conn.php';
// include '../inc/function.php'; // Correctly include the functions file

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cancel_id = isset($_GET['cancel']) ? intval($_GET['cancel']) : 0;

// Renamed functions to avoid conflicts with function.php
function getBookedRoom_local($conn, $booking_id) {
    $sql = "SELECT room_id FROM tbl_acc_booking_room WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($room_id);
    if ($stmt->fetch()) {
        return $room_id;
    }
    return null;
}

function getRoomName_local($conn, $room_id) {
    $sql = "SELECT room_number FROM tbl_acc_room WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $stmt->bind_result($room_number);
    if ($stmt->fetch()) {
        return $room_number;
    }
    return null;
}

function updateRoomStatus_local($conn, $room_number, $status) {
    $sql = "UPDATE tbl_acc_room SET status_id = ? WHERE room_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $status, $room_number);
    return $stmt->execute();
}

// ===== Check-in logic =====
if ($id > 0) {
    $room_id = getBookedRoom_local($conn, $id);
    $room_number = getRoomName_local($conn, $room_id);
    $today = date('Y-m-d');

    $sql = "UPDATE tbl_acc_booking SET booking_status_id = 2, checkin_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $today, $id);
    if ($stmt->execute()) {
        // The call to updateRoomStatus now correctly uses the renamed local function
        if (updateRoomStatus_local($conn, $room_number, 2)) {
            echo "<script>alert('Booking confirmed successfully'); window.location.href = 'index.php?resto=room_booking_details&booking_id=$id';</script>";
            exit;

        }
    } else {
        echo "Error updating booking: " . $stmt->error;
    }
}

// ===== Cancel logic =====
if ($cancel_id > 0) {
    $room_id = getBookedRoom_local($conn, $cancel_id);
    $room_number = getRoomName_local($conn, $room_id);

    $sql = "UPDATE tbl_acc_booking SET booking_status_id = 3 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cancel_id);
    if ($stmt->execute()) {
        // The call to updateRoomStatus now correctly uses the renamed local function
        if (updateRoomStatus_local($conn, $room_number, 3)) {
            echo "<script>alert('Booking canceled successfully'); window.location.href = 'index.php?resto=room_booking_details&booking_id=$cancel_id';</script>";
            exit;
        }
    } else {
        echo "Error canceling booking: " . $stmt->error;
    }
}
?>