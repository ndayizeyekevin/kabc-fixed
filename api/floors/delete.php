<?php
include_once '../../inc/config.php';

function deleteFloor($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $floor_id = $input['floor_id'];

    // Check if floor already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_floor WHERE id = :floor_id");
    $stmt->bindParam(':floor_id', $floor_id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Floor not found", "msg_type" => "error"]);
        return;
    }

    // Delete floor from database
    $stmt = $db->prepare("DELETE FROM tbl_acc_floor WHERE id = :floor_id");
    $stmt->bindParam(':floor_id', $floor_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Floor deleted successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while deleting the floor.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteFloor($db);
}
?>
