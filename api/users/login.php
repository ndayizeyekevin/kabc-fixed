<?php
include_once '../../inc/config.php';
include_once '../../helpers/encryption.php';
include_once '../../helpers/jwt_helper.php';


function loginUser($db) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $result['status'] = 404;
        $result['message'] = "Email or password is incorrect";
        $result['msg_type'] = "error";
        echo json_encode($result);
        return;
    }

    // Verify the password
    if (verifyPassword($password, $user['password'])) {
        // generate JWT token
        $token = generateJWT($user['user_id'], $user['email'], $user['role_id']);

        $result['status'] = 200;
        $result['message'] = "Login successful!";
        $result['msg_type'] = "success";
        $result['token'] = $token;
        // Optionally, include user data or token in the response
        // $result['user'] = $user; // Exclude password in the response
    } else {
        $result['status'] = 404;
        $result['message'] = "Email or password is incorrect";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    loginUser($db);
}
?>
