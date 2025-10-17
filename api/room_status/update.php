<?php
include_once '../../inc/config.php';

function updateRoomStatus($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $status_id = $input['status_id'];
    $status_name = $input['status_name'];

    // Check if status already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_status WHERE id = :status_id");
    $stmt->bindParam(':status_id', $status_id);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {  // Fix the logic here as well
        echo json_encode(["status" => 404, "message" => "Status not found", "msg_type" => "error"]);
        return;
    }

    // Update room status in database
    $stmt = $db->prepare("UPDATE tbl_acc_room_status SET status_name = :status_name WHERE id = :status_id");
    $stmt->bindParam(':status_name', $status_name);
    $stmt->bindParam(':status_id', $status_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Room status updated successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while updating the room status.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateRoomStatus($db);
}
?>
