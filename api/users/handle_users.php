<?php
include_once '../../inc/config.php';
include_once '../../helpers/encryption.php';

/**
 * Register a new user
 */
function registerUser($db) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $role_id = $_POST['role_id'];

    // Check if email already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "Email already exists", "msg_type" => "error"]);
        return;
    }

    // Hash the password
    $hashed_password = encryptPassword($password);

    // Insert user into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_users (username, email, password, full_name, phone_number, role_id) VALUES (:username, :email, :password, :full_name, :phone_number, :role_id)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':role_id', $role_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => 201, "message" => "User registered successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "Error occurred while registering user", "msg_type" => "error"]);
    }
}

/**
 * Get user details
 */
function getUser($db, $userId = null) {
    if ($userId) {
        $stmt = $db->prepare("SELECT user_id, username, email, full_name, phone_number, role_id FROM tbl_acc_users WHERE user_id = :id");
        $stmt->bindParam(':id', $userId);
    } else {
        $stmt = $db->prepare("SELECT user_id, username, email, full_name, phone_number, role_id FROM tbl_acc_users");
    }

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        echo json_encode(["status" => 200, "data" => $users, "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 404, "message" => "No users found", "msg_type" => "error"]);
    }
}

/**
 * Update user details
 */
function updateUser($db, $userId) {
    parse_str(file_get_contents("php://input"), $_PUT);
    $username = $_PUT['username'] ?? null;
    $email = $_PUT['email'] ?? null;
    $full_name = $_PUT['full_name'] ?? null;
    $phone_number = $_PUT['phone_number'] ?? null;
    $role_id = $_PUT['role_id'] ?? null;

    // Update user in database
    $stmt = $db->prepare("UPDATE tbl_acc_users SET username = :username, email = :email, full_name = :full_name, phone_number = :phone_number, role_id = :role_id WHERE user_id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':role_id', $role_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "User updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "Error updating user", "msg_type" => "error"]);
    }
}

/**
 * Delete a user
 */
function deleteUser($db, $userId) {
    $stmt = $db->prepare("DELETE FROM tbl_acc_users WHERE user_id = :id");
    $stmt->bindParam(':id', $userId);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "User deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "Error deleting user", "msg_type" => "error"]);
    }
}

// API Request Handler
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    registerUser($db);
} elseif ($method === 'GET') {
    $userId = $_GET['user_id'] ?? null;
    getUser($db, $userId);
} elseif ($method === 'PUT') {
    $userId = $_GET['user_id'] ?? null;
    if ($userId) {
        updateUser($db, $userId);
    } else {
        echo json_encode(["status" => 400, "message" => "User ID is required for updating", "msg_type" => "error"]);
    }
} elseif ($method === 'DELETE') {
    $userId = $_GET['user_id'] ?? null;
    if ($userId) {
        deleteUser($db, $userId);
    } else {
        echo json_encode(["status" => 400, "message" => "User ID is required for deletion", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method Not Allowed", "msg_type" => "error"]);
}
?>
