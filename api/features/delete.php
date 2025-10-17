<?php
include_once '../../inc/config.php';

function deleteFeature($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $feature_id = $input['feature_id'];

    // Check if feature already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_feature WHERE id = :feature_id");
    $stmt->bindParam(':feature_id', $feature_id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Feature not found", "msg_type" => "error"]);
        return;
    }

    // Delete feature from database
    $stmt = $db->prepare("DELETE FROM tbl_acc_feature WHERE id = :feature_id");
    $stmt->bindParam(':feature_id', $feature_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Feature deleted successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while deleting the feature.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    deleteFeature($db);
}
?>
