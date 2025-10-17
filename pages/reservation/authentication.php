<?php
ob_start();

$today = date("Y-m-d H:i:s");

 if (isset($_REQUEST['usn']) and isset($_REQUEST['pwd']))
    {
        
       if($_SESSION['id'] != ''){
           $sql = $db->prepare("SELECT * FROM tbl_user_log WHERE usn='" . $_REQUEST['usn'] . "' AND pwd=ENCODE('" . 
        htmlspecialchars($_REQUEST['pwd'], ENT_QUOTES) . "','ITEC_Team') ");
        $sql->execute();
        if ($sql->rowCount() != 0) {

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql1 = $db->prepare("INSERT INTO `tbl_reservation` (`guest_id`,`room_id`,`bed_id`,`check_in`,`check_out`,`cmpy_id`,`datetime`,`reserve_status`)
            VALUES(?,?,?,?,?,?,?,?)");
            $sql1->execute(array($_SESSION['user_id'],$_SESSION['id'],$_SESSION['bed'],$_SESSION['chk_in_date'],$_SESSION['chk_out_date'],$_SESSION['cid'],$today,3));
            
        $msg = "Your Reservation Saved Successfully!";
        echo'<meta http-equiv="refresh"'.'content="2;URL=?page=Home">';
}
else{
$msge = "Invalid Credentials! Please Register";
}
}
else{
  $msge = "Your Session Expired!";   
}
}
ob_end_flush();
?>
<?php
if(ISSET($_POST['register'])){
        try{
        $fname = $_POST['fname'];
        $fam_name = $_POST['fam_name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $nid = $_POST['nid'];
        $country = $_POST['country'];
        $address = $_POST['address'];
        $today = date("Y-m-d");
        $year = date("Y");
        
        $email_encrypt = password_hash($email, PASSWORD_DEFAULT);
        
        // DO any thing here
        $to = $email;
        $subject = 'Application Sent to OUGAMI Team';
        $from = 'info@itecrwanda.com';
        
        // To send HTML mail, the Content-type header must be set
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
        // Create email headers
        $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();
        
        // Compose a simple HTML email message
        $message = '<html><head>';
        $message = ' <meta name="viewport" content="width=device-width, initial-scale=1.0" />';
        $message = ' <meta name="x-apple-disable-message-reformatting" />';
        $message = ' <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        $message = ' <meta name="color-scheme" content="light dark" />';
        $message = ' <meta name="supported-color-schemes" content="light dark" />';
        $message = ' <title>OUGAMI</title>';
        $message = '<body">';
        $message .= '
        <div style="background-color: rgb(185,74,72, 0.3);">
        <a href=""><img src="https://www.ougami.com/img/onkey/bnner3.jpg" align="left" style="width:200px;height:80px;" alt=""/></a>

        <h1 style="color:#B94248;font-family: Arial, Helvetica, sans-serif;text-align:center;line-height:2.5em;">Ougami </h1>
        <hr>
        
        <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
        <td align="center">
        <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
        <td class="email-masthead">
        <h3 style="color:#B94248;font-family: Arial, Helvetica, sans-serif;text-align:center;line-height:2.5em;">
          <u>Application Message</u>
        </h3>
        </td>
        </tr>
        <!-- Email Body -->
        <tr>
        <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
        <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
        <!-- Body content -->
        <tr>
        <td class="content-cell">
        <div class="f-fallback">
        <h1>Hi '.$fname.',</h1>
        <p>Your Application Sent Successfully!</p>
        <!-- Action -->
        <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
        <td align="center">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
        <tr>
        <td align="center">
        <a href="https://www.ougami.com/pages/index?page=authenticate?emailcnfrm='.$email_encrypt.'" class="f-fallback button button--green" target="_blank">Please Confirm your Application</a>
        <p>Once you confirm, you will get an email and SMS with your Username and Password.</p>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        
        <p>For security reason , if you did not register, please ignore this email or <a href="http://ougami.com/">contact support</a> if you have questions.</p>
        <p>Thanks,
        <br>The Ougami Team</p>
        <!-- Sub copy -->
        <table class="body-sub" role="presentation">
        <tr>
        <td>
        <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
        <p class="f-fallback sub">https://www.ougami.com/</p>
        </td>
        </tr>
        </table>
        </div>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td>
         <hr>
         
        <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
        <td class="content-cell" align="center">
        <p class="f-fallback sub align-center">&copy; '.$year.' Ougami. All rights reserved.</p>
        <p class="f-fallback sub align-center">
        ITEC Ltd
        <br>KN 1 Rd, Kigali-Rwanda.
        <br>Phone (+250) 788730582
        </p>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </div>';
        $message .= '</body></html>';

        // Sending email
        if(mail($to, $subject, $message, $headers)){
        $msg=" Check your E-mail $email ";
        header('Location:index?msg='.$msg);
        } else{
        $msg=" Unable to send email. Please try again ";
        header('Location:index?msg='.$msg);
        }
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO `tbl_cust_application` (`appl_firstname`,`appl_lastname`,`appl_phone`,`appl_email`,`appl_nid_passport`,`country_id_comming_from`,`appl_address`,`appl_date`,`email_confirmation`,`email_status`)
        VALUES('$fname','$fam_name','$phone','$email','$nid','$country','$address','$today','$email_encrypt','no')";
        $conn->exec($sql);
        
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql1 = "INSERT INTO `tbl_reservation` (`guest_id`,`room_id`,`bed_id`,`check_in`,`check_out`,`cmpy_id`,`datetime`,`reserve_status`)
            VALUES ('$user_ID','$room','$bed','$chk_in_date','$chk_out_date','$cnpy','$today',3)";
            $db->exec($sql1);
        
        $msg = "Application Sent Successfully! Please Check on Email: $email";
        
        echo'<meta http-equiv="refresh"'.'content="2;URL=?page=authenticate">';
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    
    if(isset($_GET['emailcnfrm']))
       {
	$query = "
		SELECT * FROM tbl_cust_application 
		WHERE email_confirmation = :emailcnfrm
	";
	$statement = $conn->prepare($query);
	$statement->execute(
		array(
			':emailcnfrm'=>	$_GET['emailcnfrm']
		)
	);
    
	$no_of_row = $statement->rowCount();
	
	if($no_of_row > 0)
	{
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			if($row['email_status'] == 'no')
			{
				$update_query = "
				UPDATE tbl_cust_application 
				SET email_status = 'yes' 
				WHERE email_confirmation = '".$row['email_confirmation']."'	";
				$statement = $conn->prepare($update_query);
				$statement->execute();
				$sub_result = $statement->fetchAll();
				
    			$fname = $row['appl_firstname'];
                $fam_name = $row['appl_lastname'];
                $phone = $row['appl_phone'];
                $email = $row['appl_email'];
                $nid = $row['appl_nid_passport'];
                $country = $row['country_id_comming_from'];
                $address = $row['appl_address'];
                $today = date("Y-m-d");
				//Create ACCOUNT
				
				// insert into table customer
				$sql1 = "INSERT INTO `tbl_customers` (`cust_firstname`,`cust_lastname`,`cust_phone`,`cust_email`,`cust_nid_passport`,`country_id_comming_from`,`cust_address`,`cust_date`,`cust_status`)
                VALUES('$fname','$fam_name','$phone','$email','$nid','$country','$address','$today','1')";
                $conn->exec($sql1);
               	$lastId = $conn->lastInsertId();
                
				// insert into table user_log
				$username = strtolower($fname).substr($phone, -2).'@onkeyaccess.com';

				$sql2 = "INSERT INTO `tbl_user_log`(`f_name`,`l_name`,`phone`,`usn`,`pwd`,`log_role`,`user_id`,`log_status`,`reg_date`)
    			VALUES ('$fname','$fam_name','$phone','$username',ENCODE('" . htmlspecialchars($phone, ENT_QUOTES) . "','ITEC_Team'),'8','$lastId','1','$today')";
    			$conn->exec($sql2);
    			
    // 			send an email with username and password
    
                // DO any thing here
                $to = $email;
                $subject = 'Confirmation Sent to OUGAMI Team';
                $from = 'info@itecrwanda.com';
                
                // To send HTML mail, the Content-type header must be set
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                
                // Create email headers
                $headers .= 'From: '.$from."\r\n".
                'Reply-To: '.$from."\r\n" .
                'X-Mailer: PHP/' . phpversion();
                
                // Compose a simple HTML email message
                $message2 = '<html><head>';
                $message2 = ' <meta name="viewport" content="width=device-width, initial-scale=1.0" />';
                $message2 = ' <meta name="x-apple-disable-message-reformatting" />';
                $message2 = ' <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
                $message2 = ' <meta name="color-scheme" content="light dark" />';
                $message2 = ' <meta name="supported-color-schemes" content="light dark" />';
                $message2 = ' <title>OUGAMI</title>';
                $message2 = '<body">';
                $message2 .= '
                <div style="background-color: rgb(185,74,72, 0.3);">
                <a href=""><img src="https://www.ougami.com/img/onkey/bnner3.jpg" align="left" style="width:200px;height:80px;" alt=""/></a>
        
                <h1 style="color:#B94248;font-family: Arial, Helvetica, sans-serif;text-align:center;line-height:2.5em;">Ougami </h1>
                <hr>
                
                <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td class="email-masthead">
                <h3 style="color:#B94248;font-family: Arial, Helvetica, sans-serif;text-align:center;line-height:2.5em;">
                  <u>Application Message</u>
                </h3>
                </td>
                </tr>
                <!-- Email Body -->
                <tr>
                <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                <!-- Body content -->
                <tr>
                <td class="content-cell">
                <div class="f-fallback">
                <h1>Hi '.$fname.',</h1>
                <p>Congralatilations! Your Credentials Created Successfully!</p>
                <!-- Action -->
                <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                <tr>
                <td align="center">
                <h2>your username: <strong>'. $username .'</strong></h2>
                <h2>Password: <strong>'. $phone .'</strong></2>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>
                
                <p>For security reason , if you did not register, please ignore this email or <a href="http://ougami.com/">contact support</a> if you have questions.</p>
                <p>Thanks,
                <br>The OUGAMI Team</p>
                <!-- Sub copy -->
                <table class="body-sub" role="presentation">
                <tr>
                <td>
                <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                <p class="f-fallback sub">https://www.ougami.com/</p>
                </td>
                </tr>
                </table>
                </div>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td>
                 <hr>
                 
                <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td class="content-cell" align="center">
                <p class="f-fallback sub align-center">&copy; '.$year.' Ougami. All rights reserved.</p>
                <p class="f-fallback sub align-center">
                ITEC Ltd
                <br>KN 1 Rd, Kigali-Rwanda.
                <br>Phone (+250) 788730582
                </p>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>
                </div>';
                $message2 .= '</body></html>';
                
                // Sending email
                if(mail($to, $subject, $message2, $headers)){
                $msg=" Check your E-mail $email ";
                header('Location:index?msg='.$msg);
                } else{
                $msg=" Unable to send email. Please try again ";
                header('Location:index?msg='.$msg);
                }
				
				if(isset($sub_result))
				{
					$msg = 'Your Email Address Successfully Verified';
					echo'<meta http-equiv="refresh"'.'content="2;URL=?page=authenticate">';
				}
				
			}
			else
			{
				$msge = 'Your Email Address Already Verified';
				 echo'<meta http-equiv="refresh"'.'content="2;URL=?page=authenticate">';
			}
		}
	 }
	else
	{
		$msge = 'Invalid Link';
		 echo'<meta http-equiv="refresh"'.'content="2;URL=?page=authenticate">';
	}
  }
    
?>

<section id="">
	<div class="container">
			<div class="row">
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
				<div class="col-sm-4 col-sm-offset-1">
					<div class="login-form"><!--login form-->
						<h2>Login to your account</h2>
						<form action="" method="POST">

							<input type="text" name="usn" value="<?php if(isset($_COOKIE["usn"])) { echo $_COOKIE["usn"]; } ?>" placeholder="User Name" />
							<input type="password" name="pwd" value="<?php if(isset($_COOKIE["pwd"])) { echo $_COOKIE["pwd"]; } ?>" placeholder="Password" />
							
							<button type="submit" name="login" class="btn btn-default pull-right">Login</button>
						</form>
					</div><!--/login form-->
				</div>
				<div class="col-sm-1">
					<h2 class="or">OR</h2>
				</div>
				<div class="col-sm-4">
					<div class="signup-form"><!--sign up form-->
						<h2>New User Signup!</h2>
						<form action="" method="POST">
							<input type="text" name="fname" placeholder="First Name" required>
                            <input type="text" name="fam_name" id="fam_name" class="form-control" placeholder="Last Name" required>
                            <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
                            <input  type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                            <input  type="text" name="nid" id="nid" class="form-control" placeholder="ID/Passport" required>
							<input  type="text" name="address" id="address" class="form-control" placeholder="Address">
							<select id="country" name="country" class="selectpicker" data-live-search="true" style="width: 100%;" required placeholder="Select Country">
                                 <option>Select Country</option>
                                     <?php
                                    $stmt = $db->query('SELECT * FROM tbl_country');
                                    try {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           
                                            ?>
                                            <option value="<?php echo $row['cntr_id']; ?>"><?php echo $row['cntr_name']; ?></option>
                                            <?php
                                        }
                                       
                                    }
                                    catch (PDOException $ex) {
                                        //Something went wrong rollback!
                                        echo $ex->getMessage();
                                    }
                                    ?>   
                                </select><br><br>
							<button type="submit" name="register" class="btn btn-default pull-right">Signup</button><br><br>
						</form>
					</div><!--/sign up form-->
				</div>
			</div>
		</div>
	</section><!--/form-->