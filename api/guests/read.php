<?php
include_once '../../inc/config.php';

function getGuest($db)
{
    $guest_id = $_GET['guest_id'];

    // get the guest from db
    $stmt = $db->prepare("SELECT * FROM tbl_acc_guest WHERE id = :guest_id");
    $stmt->bindParam(':guest_id', $guest_id);
    $stmt->execute();
    $guest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($guest) {
        $result['status'] = 200;
        $result['guest'] = $guest;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Guest not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getGuests($db)
{
    // get the guests from db
    $stmt = $db->prepare("SELECT * FROM tbl_acc_guest");
    $stmt->execute();
    $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($guests) {
        $result['status'] = 200;
        $result['guests'] = $guests;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "No guests found";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['guest_id'])) {
        getGuest($db);
    }else {
        getGuests($db);
    }
}
