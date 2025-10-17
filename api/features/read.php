<?php
include_once '../../inc/config.php';

function getFeature($db) {
    $feature_id = $_GET['feature_id'];

    // Get feature from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_feature WHERE id = :feature_id");
    $stmt->bindParam(':feature_id', $feature_id);
    $stmt->execute();
    $feature = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($feature) {
        $result['status'] = 200;
        $result['feature'] = $feature;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Feature not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getFeatures($db) {
    // Get feature from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_feature");
    $stmt->execute();
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($features) {
        $result['status'] = 200;
        $result['features'] = $features;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Feature not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_POST['feature_id'])) {
        getFeature($db);
    } else {
        getFeatures($db);
    }
}
?>
