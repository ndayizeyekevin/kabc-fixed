<?php
include '../inc/conn.php';

function getRoomNam($id){
    include '../inc/conn.php';
    $sql = "SELECT * FROM  tbl_acc_room WHERE id='$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            return $row['room_number'];
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get form values
    $room_id = isset($_GET['room_ids']) ? $_GET['room_ids'] : '';
    $corporate_id = isset($_GET['corporate_id']) ? intval($_GET['corporate_id']) : 0;
    $booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
    $booking_amount = isset($_GET['booking_amount']) ? floatval($_GET['booking_amount']) : 0;
    $balance = isset($_GET['balance']) ? floatval($_GET['balance']) : 0;

    // Fetch corporate info
    $company_name = '';
    $tin_number = '';
    if ($corporate_id > 0) {
        $corp_sql = "SELECT name, tin_number FROM corporates WHERE id = $corporate_id LIMIT 1";
        $corp_result = mysqli_query($conn, $corp_sql);
        if ($corp_result && mysqli_num_rows($corp_result) > 0) {
            $corp_row = mysqli_fetch_assoc($corp_result);
            $company_name = mysqli_real_escape_string($conn, $corp_row['name']);
            $tin_number = mysqli_real_escape_string($conn, $corp_row['tin_number']);
        }
    }

    // 1. Update booking record with corporate info
    $updateSql = "UPDATE tbl_acc_booking 
        SET company = '$company_name', corporate_id = $corporate_id, `booking_status_id`=5, corporate = 1 
        WHERE id = $booking_id";
    $updateResult = mysqli_query($conn, $updateSql);

    if ($updateResult) {
        // 2. Insert payment with TIN number
        $payment_time = time();
        $method = 'Credit';
        $remark = 'Corporate';
        $currency = 1;
        $rate = 1;

        $insertSql = "INSERT INTO payments (booking_id, amount, payment_time, method, remark, currency, rate, tin_number)
            VALUES ($booking_id, $booking_amount, '$payment_time', '$method', '$remark', '$currency', $rate, '$tin_number')";
        $insertResult = mysqli_query($conn, $insertSql);

        if ($insertResult) {
            // 3. Optionally insert tbl_acc_booking_room (uncomment if needed)
            // $insertTbl = "INSERT INTO tbl_acc_booking_room (booking_id, room_id) VALUES($booking_id, $room_id)";
            // $insertTblResult = mysqli_query($conn, $insertTbl);

            updateRoomStatus(getRoomNam($room_id), 12);

            echo "<script>alert('Corporate details added successfully.'); window.location.href='index?resto=corporate';</script>";
            exit;
        } else {
            echo "Error inserting payment: " . mysqli_error($conn);
        }
    } else {
        echo "Error updating booking: " . mysqli_error($conn);
    }
} 