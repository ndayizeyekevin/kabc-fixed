<?php
include_once '../../inc/config.php';

function createRoomClassBedType($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $room_class_id = $input['room_class_id'];
    $bed_type_id = $input['bed_type_id'];
    $num_beds = $input['num_beds'];

    // Check if bed type already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class_bed_type WHERE room_class_id = :room_class_id AND bed_type_id = :bed_type_id AND num_beds = :num_beds");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->bindParam(':bed_type_id', $bed_type_id);
    $stmt->bindParam(':num_beds', $num_beds);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "This bed type already exists", "msg_type" => "error"]);
        return;
    }

    // Insert room class bed type into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_room_class_bed_type (room_class_id, bed_type_id, num_beds) VALUES (:room_class_id, :bed_type_id, :num_beds)");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->bindParam(':bed_type_id', $bed_type_id);
    $stmt->bindParam(':num_beds', $num_beds);

    if ($stmt->execute()) {
        $result['status'] = 201;
        $result['message'] = "Room class bed type created successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the room class bed type.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createRoomClassBedType($db);
}
?>
