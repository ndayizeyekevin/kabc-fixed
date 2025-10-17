<?php
include_once '../../config/database.php';

function updateFloor($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $floor_id = $input['block_id'];
    $floor_number = $input['block_name'];

    // Check if floor already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_floor WHERE id = :floor_id");
    $stmt->bindParam(':floor_id', $floor_id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Block not found", "msg_type" => "error"]);
        return;
    }

    // Update floor in database
    $stmt = $db->prepare("UPDATE tbl_acc_floor SET floor_number = :floor_number WHERE id = :floor_id");
    $stmt->bindParam(':floor_number', $floor_number);
    $stmt->bindParam(':floor_id', $floor_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Block updated successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while updating the Block.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateFloor($db);
}
?>
