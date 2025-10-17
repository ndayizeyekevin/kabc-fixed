<?php
// handle_payment_modes.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new payment mode
    $payment_mode_name = $input['payment_mode_name'];

    $query = "INSERT INTO tbl_acc_payment_modes (payment_mode_name) VALUES (:payment_mode_name)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':payment_mode_name', $payment_mode_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 201, "message" => "Payment mode created successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while creating the payment mode.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all payment modes
    $query = "SELECT * FROM tbl_acc_payment_modes";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();

    if ($result) {
        $payment_modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $payment_modes_result = !empty($payment_modes) ? $payment_modes : [];
        
        echo json_encode(["status" => 200, "message" => "Success!", "data" => $payment_modes_result, "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the payment modes.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing payment mode
    $id = $input['id'];
    $payment_mode_name = $input['payment_mode_name'];

    $query = "UPDATE tbl_acc_payment_modes SET payment_mode_name = :payment_mode_name WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':payment_mode_name', $payment_mode_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Payment mode updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the payment mode.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing payment mode
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $input['id'];

    $query = "DELETE FROM tbl_acc_payment_modes WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Payment mode deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while deleting the payment mode.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
?>
