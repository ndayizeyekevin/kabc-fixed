<?php
include_once '../../inc/config.php';

function readRoomMaintenance($db, $roomId) {
    $stmt = $db->prepare("
        SELECT date, issue
        FROM tbl_acc_room_maintenance
        WHERE room_id = :room_id
        ORDER BY date DESC
    ");
    $stmt->bindParam(':room_id', $roomId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(["status" => 200, "maintenance" => $result]);
    } else {
        echo json_encode(["status" => 500, "message" => "No Maintenance data", "msg_type" => "error"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['room_id'])) {
    readRoomMaintenance($db, $_GET['room_id']);
}
?>
