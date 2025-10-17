<?php
include_once '../../inc/config.php';

function readRoomAvailability($db) {
    $stmt = $db->prepare("
        SELECT r.id AS room_id, b.checkin_date AS start, DATE_ADD(b.checkin_date, INTERVAL b.duration DAY) AS end
        FROM tbl_acc_room r
        JOIN tbl_acc_booking_room br ON r.id = br.room_id
        JOIN tbl_acc_booking b ON br.booking_id = b.id
    ");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(["status" => 200, "availability" => $result]);
    } else {
        echo json_encode(["status" => 500, "message" => "Failed to fetch data", "msg_type" => "error"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    readRoomAvailability($db);
}
?>
