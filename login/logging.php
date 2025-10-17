<?php 
require_once "../inc/config.php";
include'../holder/topkey.php';
include'../holder/template_styles.php';

  if(isset($_REQUEST['msg'])){
      $msge = $_REQUEST['msg'];
  }
  
  // register
    
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
        $subject = 'Application Sent to OnKey Access Team';
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
        $message = ' <title>OnKey Access</title>';
        $message = '<body">';
        $message .= '
        <div style="background-color: rgb(185,74,72, 0.3);">
        <a href=""><img src="https://www.onkey.itecrwanda.com/img/onkey/bnner3.jpg" align="left" style="width:200px;height:80px;" alt=""/></a>

        <h1 style="color:#B94248;font-family: Arial, Helvetica, sans-serif;text-align:center;line-height:2.5em;">OnKey Access </h1>
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
        <a href="https://www.onkey.itecrwanda.com/onkey-controller/logging?emailcnfrm='.$email_encrypt.'" class="f-fallback button button--green" target="_blank">Please Confirm your Application</a>
        <p>Once you confirm, you will get an email and SMS with your Username and Password.</p>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        
        <p>For security reason , if you did not register, please ignore this email or <a href="http://itecrwanda.com/">contact support</a> if you have questions.</p>
        <p>Thanks,
        <br>The OnKey Access Team</p>
        <!-- Sub copy -->
        <table class="body-sub" role="presentation">
        <tr>
        <td>
        <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
        <p class="f-fallback sub">https://www.onkey.itecrwanda.com/</p>
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
        <p class="f-fallback sub align-center">&copy; '.$year.' OnKey Access. All rights reserved.</p>
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
        
        $msg = "Application Sent Successfully! Please Check on Email: $email";
        
        echo'<meta http-equiv="refresh"'.'content="60;URL=logging">';
    
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
                $subject = 'Confirmation Sent to OnKey Access Team';
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
                $message2 = ' <title>OnKey Access</title>';
                $message2 = '<body">';
                $message2 .= '
                <div style="background-color: rgb(185,74,72, 0.3);">
                <a href=""><img src="https://www.onkey.itecrwanda.com/img/onkey/bnner3.jpg" align="left" style="width:200px;height:80px;" alt=""/></a>
        
                <h1 style="color:#B94248;font-family: Arial, Helvetica, sans-serif;text-align:center;line-height:2.5em;">OnKey Access </h1>
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
                
                <p>For security reason , if you did not register, please ignore this email or <a href="http://itecrwanda.com/">contact support</a> if you have questions.</p>
                <p>Thanks,
                <br>The OnKey Access Team</p>
                <!-- Sub copy -->
                <table class="body-sub" role="presentation">
                <tr>
                <td>
                <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                <p class="f-fallback sub">https://www.onkey.itecrwanda.com/</p>
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
                <p class="f-fallback sub align-center">&copy; '.$year.' OnKey Access. All rights reserved.</p>
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
					echo'<meta http-equiv="refresh"'.'content="60;URL=logging">';
				}
				
			}
			else
			{
				$msge = 'Your Email Address Already Verified';
				 echo'<meta http-equiv="refresh"'.'content="60;URL=logging">';
			}
		}
	 }
	else
	{
		$msge = 'Invalid Link';
		 echo'<meta http-equiv="refresh"'.'content="60;URL=logging">';
	}
  }
    
?>


 <!-- Login Register area Start-->
    <div class="login-content">
        <!-- Login -->
        <div class="nk-block toggled" id="l-login">
          <form action="log_success" method="POST">
              
               <?php if($msg){?>
               
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                   <span aria-hidden="true"><i class="fa fa-times"></i></span></button> 
                <strong> Well Done!</strong> <?php echo htmlentities($msg); ?>
             </div>
                <?php } 
                 else if($msge){?>
                 
                  <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span></button> 
                  <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
                </div>
                 
                <?php } ?>
              
            <div class="nk-form">
                <div class="input-group">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                    <div class="nk-int-st">
                        <input type="text" name="usn" class="form-control" placeholder="Username" value="<?php if(isset($_COOKIE["usn"])) { echo $_COOKIE["usn"]; } ?>" autocomplete="off" required>
                    </div>
                </div>
                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                    <div class="nk-int-st">
                        <input type="password" name="pwd" class="form-control" placeholder="Password" value="<?php if(isset($_COOKIE["pwd"])) { echo $_COOKIE["pwd"]; } ?>" required>
                    </div>
                </div>
                <div class="fm-checkbox">
                    <label><input type="checkbox" name="remember" id="remember" class="i-checks"><i></i> Keep me signed in</label>
                </div>
                <a href="#l-register" data-ma-action="nk-login-switch" data-ma-block="#l-register" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow right-arrow-ant"></i></a>
                
                 <div class="form-example-int mg-t-15">
                    <button type="submit" class="btn btn-default notika-btn-default">Sign-In</button>
                </div>
            </div>
          </form> 

            <div class="nk-navigation nk-lg-ic">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="notika-icon notika-plus-symbol"></i> <span>Register</span></a>
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Pwd</span></a>
            </div>
        </div>
        
        <!-- Register -->
        <div class="nk-block" id="l-register">
          <form action="" method="POST">
            <div class="nk-form">
                 <div class="form-group ic-cmp-int float-lb floating-lb">
                    <div class="form-ic-cmp">
                        <i class="notika-icon notika-support"></i>
                    </div>
                    <div class="nk-int-st">
                        <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" required>
                    </div>
                </div>
                
                 <div class="form-group ic-cmp-int float-lb floating-lb">
                    <div class="form-ic-cmp">
                        <i class="notika-icon notika-support"></i>
                    </div>
                    <div class="nk-int-st">
                        <input type="text" name="fam_name" id="fam_name" class="form-control" placeholder="Last Name" required>
                    </div>
                </div>
                
                <div class="form-group ic-cmp-int float-lb floating-lb">
                    <div class="form-ic-cmp">
                        <i class="notika-icon notika-phone"></i>
                    </div>
                    <div class="nk-int-st">
                        <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone" required>
                    </div>
                </div>

                <div class="form-group ic-cmp-int float-lb floating-lb">
                    <div class="form-ic-cmp">
                        <i class="notika-icon notika-mail"></i>
                    </div>
                    <div class="nk-int-st">
                        <input  type="text" name="email" id="email" class="form-control" placeholder="Email">
                    </div>
                </div>
                
                <div class="form-group ic-cmp-int float-lb floating-lb">
                    <div class="form-ic-cmp">
                        <i class="notika-icon notika-credit-card"></i>
                    </div>
                    <div class="nk-int-st">
                        <input  type="text" name="nid" id="nid" class="form-control" placeholder="ID/Passport">
                    </div>
                </div>
                
                 <div class="form-group ic-cmp-int float-lb floating-lb">
                    <div class="form-ic-cmp">
                        <i class="notika-icon notika-flag"></i>
                    </div>
                    <div class="nk-int-st">
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
                        </select>
                   
                      <!--<label class="nk-label">Country</label>-->
                    </div>
                </div>
                
                <div class="form-group ic-cmp-int float-lb floating-lb">
                    <div class="form-ic-cmp">
                        <i class="notika-icon notika-house"></i>
                    </div>
                    <div class="nk-int-st">
                        <input  type="text" name="address" id="address" class="form-control" placeholder="Address">
                    </div>
                </div>
                
                <a href="#l-login" data-ma-action="nk-login-switch" data-ma-block="#l-login" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow right-arrow-ant"></i></a>
              
                 <div class="form-example-int mg-t-15">
                    <button type="submit" name="register" class="btn btn-default notika-btn-default">Register</button>
                </div>
            </div>
            
            </form>

            <div class="nk-navigation rg-ic-stl">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="notika-icon notika-right-arrow right-arrow-ant"></i> <span>Sign in</span></a>
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Pwd</span></a>
            </div>
        </div>

        <!-- Forgot Password -->
        <div class="nk-block" id="l-forget-password">
            <div class="nk-form">
                <p class="text-left">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eu risus. Curabitur commodo lorem fringilla enim feugiat commodo sed ac lacus.</p>

                <div class="input-group">
                    <span class="input-group-addon nk-ic-st-pro"><i class="fa fa-envelope"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="Email Address">
                    </div>
                </div>

                <a href="#l-login" data-ma-action="nk-login-switch" data-ma-block="#l-login" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow right-arrow-ant"></i></a>
            </div>

            <div class="nk-navigation nk-lg-ic rg-ic-stl">
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="notika-icon notika-right-arrow right-arrow-ant"></i> <span>Sign in</span></a>
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="notika-icon notika-plus-symbol"></i> <span>Register</span></a>
            </div>
        </div>
    </div>
 <!-- Login Register area End-->
    
<?php 
  include'../holder/template_scripts.php'; 
  include'../holder/lowkey.php'; 
?>