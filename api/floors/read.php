<?php
include_once '../../inc/config.php';

function getFloor($db)
{
    $floor_id = $_GET['floor_id'];

    // Get floor from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_floor WHERE id = :floor_id");
    $stmt->bindParam(':floor_id', $floor_id);
    $stmt->execute();
    $floor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($floor) {
        $result['status'] = 200;
        $result['floor'] = $floor;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Block not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getFloors($db)
{
    // Get floor from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_floor");
    $stmt->execute();
    $floors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($floors) {
        $result['status'] = 200;
        $result['room_blocks'] = $floors;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "No Block found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_POST['floor_id'])) {
        getFloor($db);
    } else {
        getFloors($db);
    }
}
