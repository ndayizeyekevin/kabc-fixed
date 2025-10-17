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

function deleteRoom($db)
{
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $room_id = $input['room_id'];

    $room = checkRoomExistance($db, $room_id);
    if ($room) {
        // Delete room from database
        $stmt = $db->prepare("DELETE FROM tbl_acc_room WHERE id = :room_id");
        $stmt->bindParam(':room_id', $room_id);

        if ($stmt->execute()) {
            $result['status'] = 200;
            $result['message'] = "Room deleted successfully!";
            $result['msg_type'] = "success";
        } else {
            $result['status'] = 500;
            $result['message'] = "An error occurred while deleting the room.";
            $result['msg_type'] = "error";
        }
    } else {
        $result['status'] = 404;
        $result['message'] = "Room not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteRoom($db);
}
