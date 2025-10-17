<?php
include_once '../../inc/config.php';

function deleteGuest($db)
{
    $guest_id = $_GET['guest_id'];

    // get the guest from db
    $stmt = $db->prepare("SELECT * FROM tbl_acc_guest WHERE id = :guest_id");
    $stmt->bindParam(':guest_id', $guest_id);
    $stmt->execute();
    $guest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($guest) {
        // Delete guest from database
        $stmt = $db->prepare("DELETE FROM tbl_acc_guest WHERE id = :guest_id");
        $stmt->bindParam(':guest_id', $guest_id);

        if ($stmt->execute()) {
            $result['status'] = 200;
            $result['message'] = "Guest deleted successfully!";
            $result['msg_type'] = "success";
        } else {
            $result['status'] = 500;
            $result['message'] = "An error occurred while deleting the guest.";
            $result['msg_type'] = "error";
        }
    } else {
        $result['status'] = 404;
        $result['message'] = "Guest not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    deleteGuest($db);
}
