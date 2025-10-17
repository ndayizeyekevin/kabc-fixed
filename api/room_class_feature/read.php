<?php
include_once '../../inc/config.php';

// function getRoomClassFeature($db) {
//     $id = $_GET['id'];

//     // Get room class feature from database
//     $stmt = $db->prepare("SELECT * FROM tbl_acc_room_class_feature WHERE id = :id");
//     $stmt->bindParam(':id', $id);
//     $stmt->execute();
//     $room_class_feature = $stmt->fetch(PDO::FETCH_ASSOC);

//     if ($room_class_feature) {
//         $result['status'] = 200;
//         $result['room_class_feature'] = $room_class_feature;
//         $result['msg_type'] = "success";
//     } else {
//         $result['status'] = 404;
//         $result['message'] = "Room class feature not found.";
//         $result['msg_type'] = "error";
//     }

//     echo json_encode($result);
// }

function getRoomClassFeatures($db) {
    // Get room class feature from database
    $stmt = $db->prepare("SELECT rcf.id, rcf.room_class_id, rcf.feature_id, rc.class_name AS room_class_name, 
    f.feature_name AS feature_name 
    FROM tbl_acc_room_class_feature rcf 
    JOIN tbl_acc_room_class rc ON rcf.room_class_id = rc.id 
    JOIN tbl_acc_feature f ON rcf.feature_id = f.id
    ");
    $stmt->execute();
    $room_class_features = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($room_class_features) {
        $result['status'] = 200;
        $result['room_class_features'] = $room_class_features;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Room class feature not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_POST['id'])) {
        getRoomClassFeature($db);
    } else {
        getRoomClassFeatures($db);
    }
}
?>
