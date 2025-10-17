<?php
include_once '../../inc/config.php';

function createFloor($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $floor_number = $input['block_name'];

    // Check if floor already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_floor WHERE floor_number = :floor_number");
    $stmt->bindParam(':floor_number', $floor_number);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "A floor with this number already exists", "msg_type" => "error"]);
        return;
    }

    // Insert floor into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_floor (floor_number) VALUES (:floor_number)");
    $stmt->bindParam(':floor_number', $floor_number);

    if ($stmt->execute()) {
        $result['status'] = 201;
        $result['message'] = "Floor created successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the floor.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createFloor($db);
}
?>
