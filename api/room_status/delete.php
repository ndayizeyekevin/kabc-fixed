<?php
include_once '../../inc/config.php';

function deleteRoomStatus($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $status_id = $input['status_id'];

    // Check if floor already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_floor WHERE id = :status_id");
    $stmt->bindParam(':status_id', $status_id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 404, "message" => "Status not found", "msg_type" => "error"]);
        return;
    }

    // Delete room status from database
    $stmt = $db->prepare("DELETE FROM tbl_acc_room_status WHERE id = :status_id");
    $stmt->bindParam(':status_id', $status_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Room status deleted successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while deleting the room status.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteRoomStatus($db);
}
?>
