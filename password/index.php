<?php
require_once("../inc/session.php");
require_once("../inc/DBController.php");

// ✅ Set to GMT+2
date_default_timezone_set("Africa/Kigali");

$msg = '';
$msge = '';
$validToken = false;
$userId = null;

// Check if token and email exist in URL
if (!isset($_GET['token']) || !isset($_GET['email'])) {
    die('Invalid request.');
}

$token = $_GET['token'];
$email = $_GET['email'];

// Current time in GMT+2
$currentTime = date("Y-m-d H:i:s");

// ✅ Validate token + fetch expiry
$stmt = $db->prepare("
    SELECT u_id, reset_expires 
    FROM tbl_user_log 
    WHERE reset_token = :token AND email = :email
");
$stmt->execute([
    ':token' => $token,
    ':email' => $email
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {

    // ✅ Compare expiry in PHP
    if ($user['reset_expires'] > $currentTime) {
        $validToken = true;
        $userId = $user['u_id'];
    } else {
        $msge = "Reset link has expired.";
    }

} else {
    $msge = "Invalid reset link.";
}

// Handle form submission only if token valid
if ($validToken && isset($_POST['reset'])) {

    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $msge = "Passwords do not match!";

    } else {
        // ✅ Hash password with MD5 (project requirement)
        $hashed = md5($password);

        // Check if password already used by another user
        $checkStmt = $db->prepare("
            SELECT u_id FROM tbl_user_log 
            WHERE pwd = :password AND u_id != :userId
        ");
        $checkStmt->execute([
            ':password' => $hashed,
            ':userId'   => $userId
        ]);

        if ($checkStmt->rowCount() > 0) {
            $msge = "This password is already in use. Please choose a different password.";
        } else {

            // ✅ Update password only if still valid
            $stmt = $db->prepare("
                UPDATE tbl_user_log 
                SET pwd = :password, reset_token = NULL, reset_expires = NULL
                WHERE reset_token = :token AND email = :email
            ");
            $stmt->execute([
                ':password' => $hashed,
                ':token'    => $token,
                ':email'    => $email
            ]);

            if ($stmt->rowCount() > 0) {
                $msg = "Password reset successful. <a href='../'>Login now</a>.";
                $validToken = false; 
            } else {
                $msge = "Invalid link or email no longer valid.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="margin-top:50px; max-width:500px;">

    <?php if($msg){ ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>

    <?php } elseif($msge){ ?>
        <div class="alert alert-danger"><?php echo $msge; ?></div>
    <?php } ?>

    <?php if($validToken){ ?>
        <h2>Reset Password</h2>
        <form method="POST">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>

            <button type="submit" name="reset" class="btn btn-primary btn-block">Reset Password</button>
        </form>
    <?php } ?>
</div>
</body>
</html>
