<?php
include_once '../../inc/config.php';

function checkRoomExistance($db, $room_id)
{
    // get the guest from db
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room WHERE id = :room_id");
    $stmt->bindParam(':room_id', $room_id);
    $stmt->execute();
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    return $room;
}

function updateRoom($db)
{
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $room_id = $input['room_id'];
    $floor_id = $input['block_id'];
    $room_class_id = $input['room_class_id'];
    $status_id = $input['status_id'];
    $room_number = $input['room_number'];
    $capacity = $input['capacity'];

    //check room existance
    $room = checkRoomExistance($db, $room_id);
    if ($room) {
        //update room details
        // Update room in database
        $stmt = $db->prepare("UPDATE tbl_acc_room SET floor_id = :floor_id, room_class_id = :room_class_id, status_id = :status_id, room_number = :room_number, capacity = :capacity WHERE id = :room_id");
        $stmt->bindParam(':floor_id', $floor_id);
        $stmt->bindParam(':room_class_id', $room_class_id);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':capacity', $capacity);

        if ($stmt->execute()) {
            $result['status'] = 200;
            $result['message'] = "Room updated successfully!";
            $result['msg_type'] = "success";
        } else {
            $result['status'] = 500;
            $result['message'] = "An error occurred while updating the room.";
            $result['msg_type'] = "error";
        }
    }else {
        $result['status'] = 404;
        $result['message'] = "Room not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateRoom($db);
}
