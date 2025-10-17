<?php
include_once '../../inc/config.php';

function createRoomClass($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $class_name = $input['class_name'];
    $base_price = $input['base_price'];

    // Insert room class into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_room_class (class_name, base_price) VALUES (:class_name, :base_price)");
    $stmt->bindParam(':class_name', $class_name);
    $stmt->bindParam(':base_price', $base_price);

    if ($stmt->execute()) {
        $result['status'] = 201;
        $result['message'] = "Room class created successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the room class.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createRoomClass($db);
}
?>
