<?php
require_once("../inc/session.php");
require_once("../inc/DBController.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$msg = '';
$msge = '';

if (isset($_POST['forgot'])) {

    $email = trim($_POST['usn']);

    // ✅ Check user
    $stmt = $db->prepare("SELECT u_id FROM tbl_user_log WHERE email = :email");
    $stmt->execute([':email' => $email]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {

        // Secure token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Update DB
        $stmt2 = $db->prepare("
            UPDATE tbl_user_log 
            SET reset_token = :token, reset_expires = :expires 
            WHERE email = :email
        ");
        $stmt2->execute([
            ':token'   => $token,
            ':expires' => $expires,
            ':email'   => $email
        ]);

        // Reset link
        $resetLink = "$website/password/?token=$token&email=$email";

        // SMTP env vars
        $smtpHost     = getenv("SMTP_HOST");
        $smtpPort     = getenv("SMTP_PORT");
        $smtpSecure   = getenv("SMTP_SECURE");
        $smtpUser     = getenv("SMTP_USERNAME");
        $smtpPassword = getenv("SMTP_PASSWORD");

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPassword;
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port       = (int)$smtpPort;

            $mail->setFrom($smtpUser, "Support");
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Reset Your Password";
            $mail->Body = "
                <p>Hello,</p>

                  <p>We received a request to reset the password for your account associated with this email address.</p>

                  <p>To reset your password, please click the link below:</p>

                  <p><a href='". $resetLink ."'>Reset Your Password</a></p>

                  <p><strong>Important:</strong> This link will expire in 1 hour for security reasons.</p>

                  <p>If you did not request a password reset, you can safely ignore this email. Your account will remain secure.</p>

                  <p>For additional help, contact our support team at <a href='mailto:ndayizeyekevin6@gmail.com'>ndayizeyekevin6@gmail.com</a>.</p>

                  <p>Thank you,<br>
                  <strong>GOPE Solutions Team</strong></p>

            ";

            $mail->send();
            $msg = "We have sent you reset instructions.";

        } catch (Exception $e) {
            $msge = "Email could not be sent. SMTP Error: {$mail->ErrorInfo}";
        }

    } else {
        $msge = "Email not found. Contact support.";
    }
}
?>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <section id="">
	<div class="container">
			<div class="row">
               
            
            
				<div class="col-sm-5 col-sm-offset-3" style="margin-bottom:50px;margin-top:100px;">
				    
				     <div class="col-sm-9 col-sm-offset-1">
                <?php if($msg){?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
              </div>
            <?php } 
             else if($msge){?>
                 
             <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
              </div>
            <?php } ?>
            </div>
				    
				   <center> <img src="<?= $logo_png; ?>"></center>
					<div class="login-form">
						<h2></h2>
						<form action="" method="POST">

							<input type="email" name="usn"  required class="form-control" placeholder="type your email" />
							<br>
							<button type="submit" name="forgot" class="btn btn-default btn-block">Continue</button>
							
							
						
						</form>
					</div>
					<!--/login form-->
				</div>
