<?php
include_once '../../inc/config.php';

function deleteRoomClassFeature($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $id = $input['id'];

    // Check if floor already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class_feature WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 404, "message" => "Room class feature found", "msg_type" => "error"]);
        return;
    }

    // Delete room class feature from database
    $stmt = $db->prepare("DELETE FROM tbl_acc_room_class_feature WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Room class feature deleted successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while deleting the room class feature.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteRoomClassFeature($db);
}
?>
