 <?php
 if(isset($_POST['submit'])){
     try {
     $_SESSION['fname'] = $_POST['fname'];
     $_SESSION['lname'] = $_POST['lname'];
     $_SESSION['country'] = $_POST['country'];
     $_SESSION['city']= $_POST['city'];
     $_SESSION['address'] = $_POST['address'];
     $_SESSION['phone'] = $_POST['phone'];
     $_SESSION['email'] = $_POST['email'];
     $_SESSION['date']= date("Y-m-d H:i:s");
     $_SESSION['year']= date("Y");
     $_SESSION['today']  = $date;
     $email_encrypt = password_hash($_SESSION['email'], PASSWORD_DEFAULT);
     $sql = "INSERT INTO `guest` (`firstname`, `lastname`,`country`,`city`, `address`,`phone`,`email`,`letter_status`,`reg_Date`) 
                VALUES ('$_SESSION[fname]','$_SESSION[lname]','$_SESSION[country]','$_SESSION[city]','$_SESSION[address]','$_SESSION[phone]','$_SESSION[email]','unread','$_SESSION[date]')";
                $db->exec($sql);
                $lastId = $db->lastInsertId();
                
    $sql = "INSERT INTO `tbl_reservation` (`guest_id`, `arrival`,`departure`,`adults`, `child`,`status`,`confirmation`) 
                VALUES ('$lastId','$_SESSION[arraval]','$_SESSION[departure]','$_SESSION[Adults]','$_SESSION[Children]',3,'$email_encrypt')";
                $db->exec($sql);            
        
         
        
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
        $message .= '<div style="padding: 10px;border: 1px solid lightgray;width: 600px;">
   <table>
       <tr>
           <td>
               '.$_SESSION['cpny_name'].'<br>
               OUGAMI TEAM<br>
               BURUNDI - BUJUMBURA<br>
           </td>
           <td>
               <img src="https://www.ougami.com/img/logo/logo_square.png" alt="" style="width: 100px;height: 100px;">
           </td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>
               '.$_SESSION['fname'].'<br>
               '.$_SESSION['address'].'<br>
               '.$_SESSION['phone'].'<br>
           </td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>Date:'.$_SESSION['today'].'</td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
          <td>Dear '. $_SESSION['fname'].'</td>
       </tr>
       <tr>
           <td>
            Thank you for choosing '.$_SESSION['cpny_name'].'. We pleased to confirm your reservation on date from '. $_SESSION['arrival'].' upto ' .$_SESSION['departure'].'
            Thank you again for your reservation.<br><br>
           </td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>We remain at your disposal for any questions in case of need.<br><br></td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>The '.$_SESSION['cpny_name'].' team welcomes you!<br><br></td>
       </tr>
       <tr>
           <td>Due to the COVID-19 pandemic, all measures barrier have been put in place to avoid all risks of contamination.</td>
        </tr>
           <tr>
               <td>
                   <hr>
                &copy; '.$_SESSION['year'].' OUGAMI. All rights reserved.</br><br>
                ITEC Ltd<br>
                Phone (+257) 31040404 
               </td>
           </tr>
           <tr>
           <td><a href="https://www.ougami.com/pages/booking/application_letter.php&emailcnfrm='.$email_encrypt.'" target="_blank">
                   <button style="background-color: rgb(117, 8, 8); padding: 10px;width: 150px;color: white;font-size: 17px;border: none;border-radius: 4px;">Download</button>
               </a></td>
           </tr>
       </tbody>
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
                
                $guest_id = $db->lastInsertId();
                $msg = "Saved Successfully! Please Check Your Email"." ".$_SESSION['email'];
                echo'<meta http-equiv="refresh"'.'content="3;URL=index">';
     }
        catch (PDOException $ex) {
            //Something went wrong rollback!
            echo $ex->getMessage();
        }
 }
 ?>
 <div class="container">
    <div class="row">
        <div class="column">
            <?php include('temp/sidebar.php')?>
        </div>
	    <div class="col-sm-9 padding-right">
			<div class="features_items">	
    		  <h2 class="title text-center">Room and Rates</h2>
    		  
    		  <nav aria-label="breadcrumb">
			    <ol class="breadcrumb">
				    <li class="breadcrumb-item"><a href="#">Step 1: Select Dates</a></li>
                    <li class="breadcrumb-item" ><a href="#">Step 2: Select Rooms</a></li>
                    <li class="breadcrumb-item" ><a href="#">Step 3: Booking Cart</a></li>
                    <li class="breadcrumb-item" active aria-current="page" >Step 4: Personal Information</li>
			    </ol>
			  </nav>
			  <section id="cart_items">
			      <div class="review-payment">
				    <h2>Personal Details</h2>
			    </div>
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
                    <form id="main-contact-form" class="contact-form row" name="contact-form" method="POST" action="">
                        <div class="form-group col-md-6">
                            <input type="text" name="fname" class="form-control" required="required" placeholder="First Name">
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" name="lname" class="form-control" required="required" placeholder="Last Name">
                        </div>
                        <div class="form-group col-md-6">
                            <select name="country" class="form-control select2" required="required">
                               <option value="">Select Country</option>
                                    <?php
                                        $stmt = $db->query('SELECT * FROM tbl_country');
                                        try {
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                if($res_cntr==$row['cntr_id'])
                                                {
                                                ?>
                                                <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['cntr_name']; ?></option>
                    
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <option value="<?php echo $row['cntr_id']; ?>"><?php echo $row['cntr_name']; ?></option>
                                                <?php
                                            }
                                            }
                                        }
                                        catch (PDOException $ex) {
                                            //Something went wrong rollback!
                                            echo $ex->getMessage();
                                        }
                                    ?>   
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" name="city" class="form-control" required="required" placeholder="City">
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" name="address" class="form-control" required="required" placeholder="Address">
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" name="phone" class="form-control" required="required" placeholder="Phone Number">
                        </div>
                        <div class="form-group col-md-6">
                            <input type="email" name="email" class="form-control" required="required" placeholder="Email">
                        </div>                      
                        <div class="form-group col-md-6">
                            <input type="submit" name="submit" class="btn btn-primary pull-right" value="Submit">
                        </div>
                    </form>
</section>