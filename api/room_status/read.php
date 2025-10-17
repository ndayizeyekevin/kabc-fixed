<?php
include_once '../../inc/config.php';

function getRoomStatusss($db) {
    $status_id = $_GET['status_id'];

    // Get room status from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_status WHERE id = :status_id");
    $stmt->bindParam(':status_id', $status_id);
    $stmt->execute();
    $room_status = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room_status) {
        $result['status'] = 200;
        $result['room_status'] = $room_status;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Room status not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getRoomStatuses($db) {
    // Get room status from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room_status");
    $stmt->execute();
    $room_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($room_statuses) {
        $result['status'] = 200;
        $result['room_statuses'] = $room_statuses;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "No room status found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_POST['status_id'])) {
        getRoomStatusss($db);
    } else {
        getRoomStatuses($db);
    }
}
?>
