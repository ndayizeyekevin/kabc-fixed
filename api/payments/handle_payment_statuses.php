<?php
// handle_payment_statuses.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new payment status
    $payment_status_name = $input['payment_status_name'];

    $query = "INSERT INTO tbl_acc_payment_status (payment_status_name) VALUES (:payment_status_name)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':payment_status_name', $payment_status_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 201, "message" => "Payment status created successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while creating the payment status.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all payment statuses
    $query = "SELECT * FROM tbl_acc_payment_status";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();

    if ($result) {
        $payment_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $payment_statuses_result = !empty($payment_statuses) ? $payment_statuses : [];
        
        echo json_encode(["status" => 200, "message" => "Success!", "data" => $payment_statuses_result, "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the payment statuses.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing payment status
    $id = $input['id'];
    $payment_status_name = $input['payment_status_name'];

    $query = "UPDATE tbl_acc_payment_status SET payment_status_name = :payment_status_name WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':payment_status_name', $payment_status_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Payment status updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the payment status.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing payment status
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $input['id'];

    $query = "DELETE FROM tbl_acc_payment_status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Payment status deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while deleting the payment status.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
?>
