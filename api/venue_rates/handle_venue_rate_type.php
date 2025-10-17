<?php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new venue rate type
    $type_name = $input['type_name'];

    $query = "INSERT INTO tbl_ev_venue_rate_type (type_name) VALUES (:type_name)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':type_name', $type_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => 201,
            "message" => "Rate type created successfully!",
            "msg_type" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => 500,
            "message" => "An error occurred while creating the rate type.",
            "msg_type" => "error"
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing venue rate type
    $id = $input['id'];
    $type_name = $input['type_name'];

    $query = "UPDATE tbl_ev_venue_rate_type SET type_name = :type_name WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':type_name', $type_name, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => 200,
            "message" => "Rate type updated successfully!",
            "msg_type" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => 500,
            "message" => "An error occurred while updating the rate type.",
            "msg_type" => "error"
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all venue rate types
    $query = "SELECT * FROM tbl_ev_venue_rate_type";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();

    if ($result) {
        $rate_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rate_types_result = !empty($rate_types) ? $rate_types : [];
        
        echo json_encode([
            "status" => 200, 
            "message" => "Success!",
            "data" => $rate_types_result,
            "msg_type" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => 500, 
            "message" => "An error occurred while fetching the rate types.",
            "msg_type" => "error"
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing venue rate type
    $id = $input['id'];

    $query = "DELETE FROM tbl_ev_venue_rate_type WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => 200,
            "message" => "Rate type deleted successfully!",
            "msg_type" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => 500,
            "message" => "An error occurred while deleting the rate type.",
            "msg_type" => "error"
        ]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
