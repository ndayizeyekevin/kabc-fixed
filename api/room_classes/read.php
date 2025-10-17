<?php
include_once '../../inc/config.php';

function getRoomClassss($db) {
    $room_class_id = $_GET['room_class_id'];

    // Get room class from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class WHERE id = :room_class_id");
    $stmt->bindParam(':room_class_id', $room_class_id);
    $stmt->execute();
    $room_class = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room_class) {
        $result['status'] = 200;
        $result['room_class'] = $room_class;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Room class not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getRoomClasses($db)
{
    // Get room class from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class");
    $stmt->execute();
    $room_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($room_classes) {
        $result['status'] = 200;
        $result['room_classes'] = $room_classes;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "No room class found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_POST['room_class_id'])) {
        getRoomClassss($db);
    } else {
        getRoomClasses($db);
    }
}