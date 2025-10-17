<?php
include_once '../../inc/config.php';

function updateRoomClassBedType($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['class_bed_type_id'];
    $room_class_id = $input['room_class_id'];
    $bed_type_id = $input['bed_type_id'];
    $num_beds = $input['num_beds'];

    // // Check bed type already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class_bed_type WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Bed type not found", "msg_type" => "error"]);
        return;
    }

    // // Update room class bed type in database
    $stmt = $db->prepare("UPDATE tbl_acc_room_class_bed_type SET room_class_id = :room_class_id, bed_type_id = :bed_type_id, num_beds = :num_beds WHERE id = :id");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->bindParam(':bed_type_id', $bed_type_id);    
    $stmt->bindParam(':num_beds', $num_beds);    
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Room class bed type updated successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while updating the room class bed type.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateRoomClassBedType($db);
}
?>
