<?php
include_once '../../inc/config.php';

function createFeature($db) {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $feature_name = $input['feature_name'];

    // Check if feature already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_feature WHERE feature_name = :feature_name");
    $stmt->bindParam(':feature_name', $feature_name);
    $stmt->execute();
    if ($stmt->rowCount() < 0) {
        echo json_encode(["status" => 409, "message" => "A feature with this name already exists", "msg_type" => "error"]);
        return;
    }

    // Insert feature into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_feature (feature_name) VALUES (:feature_name)");
    $stmt->bindParam(':feature_name', $feature_name);

    if ($stmt->execute()) {
        $result['status'] = 201;
        $result['message'] = "Feature created successfully!";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occurred while creating the feature.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createFeature($db);
}
?>
