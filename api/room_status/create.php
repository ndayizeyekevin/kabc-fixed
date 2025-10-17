<?php
include_once '../../inc/config.php';

function createRoomStatus($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $status_name = $input['status_name'];

    // Check if status already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_status WHERE status_name = :status_name");
    $stmt->bindParam(':status_name', $status_name);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "A status with this name already exists", "msg_type" => "error"]);
        return;
    }

    // Insert room status into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_room_status (status_name) VALUES (:status_name)");
    $stmt->bindParam(':status_name', $status_name);

    if ($stmt->execute()) {
        $result['status'] = 201;
        $result['message'] = "Room status created successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the room status.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createRoomStatus($db);
}
?>
