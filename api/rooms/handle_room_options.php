<?php
include_once '../../inc/config.php';

function createRoomOption($db, $data) {
    $option_code = $data->option_code;
    $option_description = $data->option_description;

    // Check if the option already exists
    $checkQuery = "SELECT * FROM tbl_acc_room_options WHERE option_code = :option_code";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':option_code', $option_code);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        return json_encode(["status" => 409, "message" => "Option already exists", "msg_type" => "error"]);
    }

    // Insert new option
    $query = "INSERT INTO tbl_acc_room_options (option_code, option_description) VALUES (:option_code, :option_description)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':option_code', $option_code);
    $stmt->bindParam(':option_description', $option_description);

    if ($stmt->execute()) {
        return json_encode(["status" => 201, "message" => "Option created successfully!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occurred while creating the option.", "msg_type" => "error"]);
    }
}

function readRoomOptions($db) {
    $query = "SELECT * FROM tbl_acc_room_options";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->execute()) {
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode(["status" => 200,"data"=>$options, "message" => "Success!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occured while fetching options.", "msg_type" => "error"]);
    }
}

function updateRoomOption($db, $data) {
    $id = $data->id;
    $option_code = $data->option_code;
    $option_description = $data->option_description;

    // Check if the option already exists
    $checkQuery = "SELECT * FROM tbl_acc_room_options WHERE option_code = :option_code AND id != :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':option_code', $option_code);
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        return json_encode(["status" => 409, "message" => "Option already exists", "msg_type" => "error"]);
    }

    // Update option
    $query = "UPDATE tbl_acc_room_options SET option_code = :option_code, option_description = :option_description WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':option_code', $option_code);
    $stmt->bindParam(':option_description', $option_description);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        return json_encode(["status" => 200, "message" => "Option updated successfully!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occurred while updating the option.", "msg_type" => "error"]);
    }
}

function deleteRoomOption($db, $data) {
    $id = $data->id;

    // Delete option
    $query = "DELETE FROM tbl_acc_room_options WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        return json_encode(["status" => 200, "message" => "Option deleted successfully!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occurred while deleting the option.", "msg_type" => "error"]);
    }
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"));

switch ($requestMethod) {
    case 'POST':
        echo createRoomOption($db, $data);
        break;
    case 'GET':
        echo readRoomOptions($db);
        break;
    case 'PUT':
        echo updateRoomOption($db, $data);
        break;
    case 'DELETE':
        echo deleteRoomOption($db, $data);
        break;
    default:
        echo json_encode(["status" => 405, "message" => "Method not allowed", "msg_type"=>"error"]);
        break;
}
?>
