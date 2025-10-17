<?php
include_once '../../inc/config.php';

function deleteRoomClass($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $room_class_id = $input['room_class_id'];

    // Check if room class already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class WHERE id = :room_class_id");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Room class not found", "msg_type" => "error"]);
        return;
    }

    // Delete room class from database
    $stmt = $db->prepare("DELETE FROM tbl_acc_room_class WHERE id = :room_class_id");
    $stmt->bindParam(':room_class_id', $room_class_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Room class deleted successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while deleting the room class.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteRoomClass($db);
}
?>
