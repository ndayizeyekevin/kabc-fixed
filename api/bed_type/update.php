<?php
include_once '../../inc/config.php';

function updateBedType($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $bed_type_id = $input['bed_type_id'];
    $bed_type_name = $input['bed_type_name'];

    // Check if floor already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_bed_type WHERE id = :bed_type_id");
    $stmt->bindParam(':bed_type_id', $bed_type_id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Bed type not found", "msg_type" => "error"]);
        return;
    }

    // Update bed type in database
    $stmt = $db->prepare("UPDATE tbl_acc_bed_type SET bed_type_name = :bed_type_name WHERE id = :bed_type_id");
    $stmt->bindParam(':bed_type_name', $bed_type_name);
    $stmt->bindParam(':bed_type_id', $bed_type_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Bed type updated successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while updating the bed type.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateBedType($db);
}
?>
