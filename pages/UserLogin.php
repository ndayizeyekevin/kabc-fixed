<?php
// die(var_dump(getenv("DB_USERNAME")));
require_once("../inc/session.php");
require_once("../inc/DBController.php");
$msg = '';
$msge = '';
// include'../holder/topkey.php';
// include'../holder/template_styles.php';

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
        $city = $_POST['city'];
        $address = $_POST['address'];
        $today = date("Y-m-d");
        $year = date("Y");
        
        $email_encrypt = password_hash($email, PASSWORD_DEFAULT);
        
        // // DO any thing here
        // $to = $email;
        // $subject = 'Application Sent to RESTAURENT Team';
        // $from = 'ndahayoptr@gmail.com';
        
        // // To send HTML mail, the Content-type header must be set
        // $headers = 'MIME-Version: 1.0' . "\r\n";
        // $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
        // // Create email headers
        // $headers .= 'From: '.$from."\r\n".
        // 'Reply-To: '.$from."\r\n" .
        // 'X-Mailer: PHP/' . phpversion();
        
        // Compose a simple HTML email message
//         $message = '<html><head>';
//         $message = ' <meta name="viewport" content="width=device-width, initial-scale=1.0" />';
//         $message = ' <meta name="x-apple-disable-message-reformatting" />';
//         $message = ' <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
//         $message = ' <meta name="color-scheme" content="light dark" />';
//         $message = ' <meta name="supported-color-schemes" content="light dark" />';
//         $message = ' <title>RESTAURENT</title>';
//         $message = '<body">';
//         $message .= '<div style="padding: 10px;border: 1px solid lightgray; text-align: center;width: 500px;">
//   <table>
//       <thead>
//           <th style="background-color: lightgray;border-top: 2px solid rgb(117, 8, 8);text-align: center;">
//               <img src="https://restaurent.aptc.rw/img/logo/logo_square.png" alt="" style="width: 150px;height: 150px;">
//           </th>
//       </thead>
//       <tbody>
//           <tr>
//           <td>
//               <p>Hi <b>'.$fname.'</b>, Your Application Sent Successfully!</p>
//               <p>Once you confirm, you will get an email and SMS with your Username and Password.</p>
//               <a href="https://restaurent.aptc.rw/pages/?page=login&emailcnfrm='.$email_encrypt.'" target="_blank">
//                   <button style="background-color: rgb(117, 8, 8); padding: 10px;width: 250px;color: white;font-size: 17px;border: none;border-radius: 4px;">Confirm your Application</button>
//               </a>
//           </td>
//           </tr>
//           <tr>
//               <td>
//                 <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
//                 <p class="f-fallback sub">https://restaurent.aptc.rw/</p>
//               </td>
//           </tr>
//           <tr>
//             <td>
//              <p>For security reason , if you did not register, please ignore this email or <a href="http://RESTAURENT.com/">contact support</a> if you have questions.</p>
//              <p>Thanks,
//              <br>The RESTAURENT Team</p>
//              <!-- Sub copy -->
//             </td>
//         </tr>
//           <tr>
//               <td>
//                   <hr>
//                 &copy; '.$year.' RESTAURENT. All rights reserved.</br>
//                 ITEC Ltd<br>
//                 KN 1 Rd, Kigali-Rwanda.<br>
//                 Phone (+250) 789028963
//               </td>
//           </tr>
//       </tbody>
//   </table> 
//   </div>';
//         $message .= '</body></html>';

//         // Sending email
//         if(mail($to, $subject, $message, $headers)){
//         $msg=" Check your E-mail $email ";
//         header('Location:index?msg='.$msg);
//         } else{
//         $msg=" Unable to send email. Please try again ";
//         header('Location:index?msg='.$msg);
//         }
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO `guest`(`firstname`, `lastname`, `country`, `city`, `address`, `nid_passport`, `phone`, `email`, `reg_Date`)
        VALUES('$fname','$fam_name','$country','$city','$address','$nid','$phone','$email','$today')";
        $conn->exec($sql);
        
        $msg = "Registered Successfully!";
        
        echo'<meta http-equiv="refresh"'.'content="60;URL=?page=login">';
    
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
                
                $username = $fname;
				//Create ACCOUNT
				
				// insert into table customer
				$sql1 = "INSERT INTO `tbl_customers` (`cust_firstname`,`cust_lastname`,`cust_phone`,`cust_email`,`cust_nid_passport`,`country_id_comming_from`,`cust_address`,`cust_date`,`cust_status`)
                VALUES('$fname','$fam_name','$phone','$email','$nid','$country','$address','$today','1')";
                $conn->exec($sql1);
               	$lastId = $conn->lastInsertId();
                
				// insert into table user_log

				$sql2 = "INSERT INTO `tbl_user_log`(`f_name`,`l_name`,`phone`,`usn`,`pwd`,`log_role`,`user_id`,`log_status`,`reg_date`)
    			VALUES ('$fname','$fam_name','$phone','$fname',ENCODE('" . htmlspecialchars($phone, ENT_QUOTES) . "','ITEC_Team'),'8','$lastId','1','$today')";
    			$conn->exec($sql2);
    			
    // 			send an email with username and password
    
                // DO any thing here
                $to = $email;
                $subject = 'Confirmation Sent to RESTAURENT Team';
                $from = 'ndahayoptr@gmail.com';
                
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
                $message2 = ' <title>RESTAURENT</title>';
                $message2 = '<body">';
                $message2 .= '
                 <div style="padding: 10px;border: 1px solid lightgray; text-align: center;width: 500px;">
                <table>
                <thead>
                <th style="background-color: lightgray;border-top: 2px solid rgb(117, 8, 8);text-align: center;">
               <img src="https://restaurent.aptc.rw/img/logo/logo_square.png" alt="" style="width: 150px;height: 150px;" alt="" style="width: 150px;height: 150px;">
                </th>
                </thead>
             <tbody>
           <tr>
           <td>
               <p>Hi <b>'.$fname.'</b>, Congralatilations! Your Credentials Created Successfully!</p>
               <p>your username: <strong>'. $username .'</strong></p>
               <p>Password: <strong>'. $phone .'</strong></p>
           </td>
           </tr>
           
           <tr>
               <td>
                <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                <p class="f-fallback sub">https://restaurent.aptc.rw/</p>
               </td>
           </tr>
           <tr>
            <td>
             <p>For security reason , if you did not register, please ignore this email or <a href="http://restaurent.aptc.rw/">contact support</a> if you have questions.</p>
             <p>Thanks,
             <br>The RESTAURENT Team</p>
             <!-- Sub copy -->
            </td>
        </tr>
           <tr>
               <td>
                   <hr>
                &copy; '.$year.' RESTAURENT. All rights reserved.</br>
                Phone (+250) 789028963
               </td>
           </tr>
       </tbody>
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
					echo'<meta http-equiv="refresh"'.'content="60;URL=?page=login">';
				}
				
			}
			else
			{
				$msge = 'Your Email Address Already Verified';
				 echo'<meta http-equiv="refresh"'.'content="60;URL=?page=login">';
			}
		}
	 }
	else
	{
		$msge = 'Invalid Link';
		 echo'<meta http-equiv="refresh"'.'content="60;URL=?page=login">';
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
				<div class="col-sm-5 col-sm-offset-3" style="margin-bottom:50px;margin-top:100px;">
				   <center> <img src="https://saintpaul.gope.rw/img/logo.png"></center>
					<div class="login-form"><!--login form-->
						<h2></h2>
						<form action="log_success" method="POST">

							<input type="password" name="pwd"  value="<?php if(isset($_COOKIE["pwd"])) { echo $_COOKIE["pwd"]; } ?>" placeholder="type password" />
						
						
							<button type="submit" name="login" class="btn btn-default btn-block">Continue</button>
							
							<hr>
						<center>	<a href="forgotPassword" style='color:red'>Forgot Password</a> </center>
						</form>
					</div>
					<!--/login form-->
				</div>
				<br><br>
				<!--<div class="col-sm-1">-->
				<!--	<h2 class="or">OR</h2>-->
				<!--</div>-->
				<!--<div class="col-sm-4">-->
				<!--	<div class="signup-form"><!--sign up form-->
				<!--		<h2>New User Signup!</h2>-->
				<!--		<form action="" method="POST">-->
				<!--			<input type="text" name="fname" placeholder="First Name" required>-->
    <!--                        <input type="text" name="fam_name" id="fam_name" class="form-control" placeholder="Last Name" required>-->
    <!--                        <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>-->
    <!--                        <input  type="email" name="email" id="email" class="form-control" placeholder="Email" required>-->
    <!--                        <input  type="text" name="nid" id="nid" class="form-control" placeholder="ID/Passport" required>-->
				<!--			<input  type="text" name="address" id="address" class="form-control" placeholder="Address">-->
				<!--			<input  type="text" name="city" id="city" class="form-control" placeholder="City">-->
				<!--			<select id="country" name="country" class="select2" data-live-search="true" style="width: 100%;" required placeholder="Select Country">-->
    <!--                             <option value="">Select Country</option>-->
    <!--                                 <php-->
    <!--                                $stmt = $db->query('SELECT * FROM tbl_country');-->
    <!--                                try {-->
    <!--                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {-->
                                           
    <!--                                        ?>-->
    <!--                                        <option value="<php echo $row['cntr_id']; ?>"><php echo $row['cntr_name']; ?></option>-->
    <!--                                        <php-->
    <!--                                    }-->
    <!--                                }-->
    <!--                                catch (PDOException $ex) {-->
                                        <!--//Something went wrong rollback!-->
    <!--                                    echo $ex->getMessage();-->
    <!--                                }-->
    <!--                                ?>   -->
    <!--                            </select>-->
                                
    <!--                            <br><br>-->
				<!--			<button type="submit" name="register" class="btn btn-default pull-right">Signup</button><br><br>-->
				<!--		</form>-->
				<!--	</div><!--/sign up form-->
				</div>
			</div>
	</section><!--/form-->
