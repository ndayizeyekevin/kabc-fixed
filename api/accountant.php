<?php
include_once '../config/database.php';
include_once '../helpers/auth_middleware.php';

// Define allowed roles (e.g., 1 for admin)
$allowedRoles = [3];

// Authorize the request
$userData = authorize($allowedRoles);

// If authorization passes, proceed with the admin functionality
echo json_encode([
    "status" => 200,
    "message" => "Welcome to the admin panel!",
    "user" => $userData
]);
?>
