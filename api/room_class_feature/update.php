<?php
include_once '../../inc/config.php';

function updateRoomClassFeature($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $id = $input['id'];
    $room_class_id = $input['room_class_id'];
    $feature_id = $input['feature_id'];

    // Check if room feature already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class_feature WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 404, "message" => "Room class feauture not found", "msg_type" => "error"]);
        return;
    }

    // Update room class feature in database
    $stmt = $db->prepare("UPDATE tbl_acc_room_class_feature SET room_class_id = :room_class_id, feature_id = :feature_id WHERE id = :id");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->bindParam(':feature_id', $feature_id);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Room class feature updated successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while updating the room class feature.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateRoomClassFeature($db);
}
?>
