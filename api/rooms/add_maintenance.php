<?php
include_once '../../inc/config.php';

function addRoomMaintenance($db, $data) {
    $stmt = $db->prepare("
        INSERT INTO tbl_acc_room_maintenance (room_id, date, issue)
        VALUES (:room_id, :date, :issue)
    ");
    $stmt->bindParam(':room_id', $data['room_id']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->bindParam(':issue', $data['issue']);
    if ($stmt->execute()) {
        echo json_encode(["status" => 201, "message" => "Maintenance record added successfully", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "Failed to add maintenance record", "msg_type" => "error"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    addRoomMaintenance($db, $data);
}
?>
