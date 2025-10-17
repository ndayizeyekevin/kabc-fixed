<?php
include_once '../../inc/config.php';

function createRoomClassFeature($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $room_class_id = $input['room_class_id'];
    $feature_id = $input['feature_id'];

    // Check if feature already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class_feature WHERE room_class_id = :room_class_id and feature_id = :feature_id");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->bindParam(':feature_id', $feature_id);    
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "This feature already exist for the selected room", "msg_type" => "error"]);
        return;
    }

    // Insert room class feature into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_room_class_feature (room_class_id, feature_id) VALUES (:room_class_id, :feature_id)");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->bindParam(':feature_id', $feature_id);

    if ($stmt->execute()) {
        $result['status'] = 201;
        $result['message'] = "Room class feature created successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the room class feature.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createRoomClassFeature($db);
}
?>
