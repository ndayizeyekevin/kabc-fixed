<?php
include_once '../../inc/config.php';

function createBedType($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $bed_type_name = $input['bed_type_name'];

    // Check if bed type already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_bed_type WHERE bed_type_name = :bed_type_name");
    $stmt->bindParam(':bed_type_name', $bed_type_name);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "A bed type with this name already exists", "msg_type" => "error"]);
        return;
    }

    // Insert bed type into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_bed_type (bed_type_name) VALUES (:bed_type_name)");
    $stmt->bindParam(':bed_type_name', $bed_type_name);

    if ($stmt->execute()) {
        $result['status'] = 201;
        $result['message'] = "Bed type created successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the bed type.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createBedType($db);
}
?>
