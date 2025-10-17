<?php
// handle_customers.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new customer
    $names = $input['names'];
    $address = $input['Address'];
    $email = $input['email'];
    $phone = $input['phone'];
    $identification = $input['identification'];
    $tin = !empty($input['tin']) ? $input['tin'] : '0';
    $created_at = date('Y-m-d H:i:s');

    $query = "INSERT INTO tbl_ev_customers (names, address, email, phone, identification, tin, created_at) 
              VALUES (:names, :address, :email, :phone, :identification, :tin, :created_at)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':names', $names, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':identification', $identification, PDO::PARAM_STR);
    $stmt->bindParam(':tin', $tin, PDO::PARAM_STR);
    $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 201, "message" => "Customer created successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while creating the customer.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all customers
    $query = "SELECT * FROM tbl_ev_customers";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();

    if ($result) {
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $customers_result = !empty($customers) ? $customers : [];
        
        echo json_encode(["status" => 200, "message" => "Success!", "data" => $customers_result, "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the customers.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing customer
    $id = $input['id'];
    $names = $input['names'];
    $address = $input['Address'];
    $email = $input['email'];
    $phone = $input['phone'];
    $identification = $input['identification'];
    $tin = !empty($input['tin']) ? $input['tin'] : '0';

    $query = "UPDATE tbl_ev_customers SET names = :names, address = :address, email = :email, phone = :phone, identification = :identification, tin = :tin WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':names', $names, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':identification', $identification, PDO::PARAM_STR);
    $stmt->bindParam(':tin', $tin, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Customer updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the customer.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing customer
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $input['id'];

    $query = "DELETE FROM tbl_ev_customers WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Customer deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while deleting the customer.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
?>
