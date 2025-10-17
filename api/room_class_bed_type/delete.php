<?php
include_once '../../inc/config.php';

function deleteRoomClassBedType($db) {
    $id = $_POST['id'];

    // Check if floor already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class_bed_type WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 404, "message" => "Room class bed type not found", "msg_type" => "error"]);
        return;
    }

    // Delete room class bed type from database
    $stmt = $db->prepare("DELETE FROM tbl_acc_room_class_bed_type WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Room class bed type deleted successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while deleting the room class bed type.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteRoomClassBedType($db);
}
?>
