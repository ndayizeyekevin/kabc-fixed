<?php
// handle_event_types.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new event type
    $event_name = $input['event_name'];
    $event_code = $input['event_code'];
    $created_at = date('Y-m-d H:i:s');

    $query = "INSERT INTO tbl_ev_event_type (event_name, event_code, created_at) VALUES (:event_name, :event_code, :created_at)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':event_name', $event_name, PDO::PARAM_STR);
    $stmt->bindParam(':event_code', $event_code, PDO::PARAM_STR);
    $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 201, "message" => "Event type created successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while creating the event type.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all event types
    $query = "SELECT * FROM tbl_ev_event_type";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();

    if ($result) {
        $event_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $event_types_result = !empty($event_types) ? $event_types : [];
        
        echo json_encode(["status" => 200, "message" => "Success!", "data" => $event_types_result, "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the event types.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing event type
    $id = $input['id'];
    $event_name = $input['event_name'];
    $event_code = $input['event_code'];

    $query = "UPDATE tbl_ev_event_type SET event_name = :event_name, event_code = :event_code WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':event_name', $event_name, PDO::PARAM_STR);
    $stmt->bindParam(':event_code', $event_code, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Event type updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the event type.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing event type
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $input['id'];

    $query = "DELETE FROM tbl_ev_event_type WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Event type deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while deleting the event type.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
?>
