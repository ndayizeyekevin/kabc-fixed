<?php
include_once '../../inc/config.php';

function updateFeature($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $feature_id = $input['feature_id'];
    $feature_name = $input['feature_name'];

    // Check if feature already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_feature WHERE id = :feature_id");
    $stmt->bindParam(':feature_id', $feature_id);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 404, "message" => "Feature not found", "msg_type" => "error"]);
        return;
    }

    // Update feature in database
    $stmt = $db->prepare("UPDATE tbl_acc_feature SET feature_name = :feature_name WHERE id = :feature_id");
    $stmt->bindParam(':feature_name', $feature_name);
    $stmt->bindParam(':feature_id', $feature_id);

    if ($stmt->execute()) {
        $result['status'] = 200;
        $result['message'] = "Feature updated successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while updating the feature.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateFeature($db);
}
?>
