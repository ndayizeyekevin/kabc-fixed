<?php
include_once '../../inc/config.php';



function getRoomClassBedType($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $id = $input['id'];

    // Get room class bed type from database
    $stmt = $db->prepare(" SELECT rcbt.id, rc.class_name AS room_class_name, 
    bt.type_name AS bed_type_name, rcbt.num_beds 
    FROM tbl_acc_room_class_bed_type rcbt 
    JOIN tbl_acc_room_class rc ON rcbt.room_class_id = rc.id 
    JOIN tbl_acc_bed_type bt ON rcbt.bed_type_id = bt.id 
    WHERE rcbt.id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $room_class_bed_type = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room_class_bed_type) {
        $result['status'] = 200;
        $result['room_class_bed_type'] = $room_class_bed_type;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Room class bed type not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getRoomClassBedTypes($db) {
    // Get room class bed type from database
    $stmt = $db->prepare(" SELECT rcbt.id, rcbt.room_class_id, rcbt.bed_type_id, rc.class_name AS room_class_name, 
    bt.bed_type_name AS bed_type_name, rcbt.num_beds 
    FROM tbl_acc_room_class_bed_type rcbt 
    JOIN tbl_acc_room_class rc ON rcbt.room_class_id = rc.id 
    JOIN tbl_acc_bed_type bt ON rcbt.bed_type_id = bt.id ");
    $stmt->execute();
    $room_class_bed_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($room_class_bed_types) {
        $result['status'] = 200;
        $result['room_class_bed_types'] = $room_class_bed_types;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "No room class bed type found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['id'])) {
        getRoomClassBedType($db);
    } else {
        getRoomClassBedTypes($db);
    }
}
?>
