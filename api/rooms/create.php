<?php
include_once '../../inc/config.php';

function createRoom($db)
{
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $floor_id = $input['block_id'];
    $room_class_id = $input['room_class_id'];
    $status_id = $input['status_id'];
    $room_number = $input['room_number'];
    $capacity = $input['capacity'];

    // Check if room already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room WHERE room_number = :room_number");
    $stmt->bindParam(':room_number', $room_number);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "A room with this number already exists", "msg_type" => "error"]);
        return;
    }

    try {

        // Insert room into database
        $stmt = $db->prepare("INSERT INTO tbl_acc_room (floor_id, room_class_id, status_id, room_number, capacity) VALUES (:floor_id, :room_class_id, :status_id, :room_number, :capacity)");
        $stmt->bindParam(':floor_id', $floor_id);
        $stmt->bindParam(':room_class_id', $room_class_id);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':capacity', $capacity);

        if ($stmt->execute()) {
            $result['status'] = 201;
            $result['message'] = "Room created successfully!";
            $result['msg_type'] = "success";
        } else {
            $result['status'] = 500;
            $result['message'] = "An error occurred while creating the room.";
            $result['msg_type'] = "error";
        }
        echo json_encode($result);
    } catch (Exception $e) {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the room." . $e->getMessage();
        $result['msg_type'] = "error";
        echo json_encode($result);
    }
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createRoom($db);
}
