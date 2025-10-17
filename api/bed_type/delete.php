<?php
include_once '../../inc/config.php';

function deleteBedType($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $bed_type_id = $input['bed_type_id'];

    // Check if bed type already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_bed_type WHERE id = :bed_type_id");
    $stmt->bindParam(':bed_type_id', $bed_type_id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Bed type not found", "msg_type" => "error"]);
        return;
    }

    // Delete bed type from database
    $stmt = $db->prepare("DELETE FROM tbl_acc_bed_type WHERE id = :bed_type_id");
    $stmt->bindParam(':bed_type_id', $bed_type_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Bed type deleted successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while deleting the bed type.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteBedType($db);
}
?>
