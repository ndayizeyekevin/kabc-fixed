<?php
include_once '../../inc/config.php';

function createBookingStatus($db, $data) {
    $status_name = $data->status_name;

    // Check if status already exists
    $checkQuery = "SELECT * FROM tbl_acc_booking_status WHERE status_name = :status_name";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':status_name', $status_name);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        return json_encode(["status" => 409, "message" => "Status already exists", "msg_type" => "error"]);
    }

    // Insert new status
    $query = "INSERT INTO tbl_acc_booking_status (status_name) VALUES (:status_name)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status_name', $status_name);

    if ($stmt->execute()) {
        return json_encode(["status" => 201, "message" => "Status created successfully!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occurred while creating the status.", "msg_type" => "error"]);
    }
}

function readBookingStatuses($db) {
    $query = "SELECT * FROM tbl_acc_booking_status";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->execute()) {
        $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode(["status" => 200,"data"=>$statuses, "message" => "Success!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occured while fetching statuses.", "msg_type" => "error"]);
    }
}

function updateBookingStatus($db, $data) {
    $id = $data->id;
    $status_name = $data->status_name;

    // Check if status already exists
    $checkQuery = "SELECT * FROM tbl_acc_booking_status WHERE status_name = :status_name AND id != :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':status_name', $status_name);
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        return json_encode(["status" => 409, "message" => "Status already exists", "msg_type" => "error"]);
    }

    // Update status
    $query = "UPDATE tbl_acc_booking_status SET status_name = :status_name WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status_name', $status_name);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        return json_encode(["status" => 200, "message" => "Status updated successfully!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occurred while updating the status.", "msg_type" => "error"]);
    }
}

function deleteBookingStatus($db, $data) {
    $id = $data->id;

    // Delete status
    $query = "DELETE FROM tbl_acc_booking_status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        return json_encode(["status" => 200, "message" => "Status deleted successfully!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "An error occurred while deleting the status.", "msg_type" => "error"]);
    }
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"));

switch ($requestMethod) {
    case 'POST':
        echo createBookingStatus($db, $data);
        break;
    case 'GET':
        echo readBookingStatuses($db);
        break;
    case 'PUT':
        echo updateBookingStatus($db, $data);
        break;
    case 'DELETE':
        echo deleteBookingStatus($db, $data);
        break;
    default:
        echo json_encode(["status" => 405, "message" => "Method not allowed"]);
        break;
}
?>
