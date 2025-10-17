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
        $city = $_POST['city'];
        $today = date("Y-m-d");
        $year = date("Y");
        
        $confirmation = password_hash($email, PASSWORD_DEFAULT);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO `guest` (`firstname`,`lastname`,`country`,`city`,`address`,`nid_passport`,`phone`,`email`,`reg_date`)
        VALUES('$fname','$fam_name','$country','$city','$address','$nid','$phone','$email','$today')";
        $conn->exec($sql);
        
        $msg = "Added Successfully!";
        
        echo'<meta http-equiv="refresh"'.'content="1;URL=?resto=mngeResrv">';
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
}
    
//   Reservation
    if(ISSET($_POST['btnResrv'])){
        $guest = $_POST['guest'];
        $room = $_POST['room'];
        $chkin = $_POST['chkin'];
        $chkout = $_POST['chkout'];
        $adults = $_POST['adults'];
        $children = $_POST['children'];
        $today = date('Y-m-d H:i:s');
        
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $li_qu = $db->prepare("SELECT * FROM guest WHERE guest_id = '".$guest."'");
        $li_qu->execute();
        $fli = $li_qu->fetch();
        $names = $fli['firstname']." ".$fli['lastname'];
        $g_email = $fli['email'];
        $confirmation = password_hash($g_email, PASSWORD_DEFAULT);
        
        
         // DO any thing here
        $to = $g_email;
        $subject = 'Application Sent to RESTO Team';
        $from = 'ndahayoptr@gmail.com';
        
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
        $message = ' <title>RESTO</title>';
        $message = '<body">';
        $message .= '<div style="padding: 10px;border: 1px solid lightgray; text-align: center;width: 500px;">
   <table>
       <thead>
           <th style="background-color: lightgray;border-top: 2px solid rgb(117, 8, 8);text-align: center;">
               <img src="https://www.restaurent.aptc.rw/img/logo/logo_square.png" alt="" style="width: 150px;height: 150px;">
           </th>
       </thead>
       <tbody>
           <tr>
           <td>
               <p>Hi <b>'.$names.'</b>, Your Application Sent Successfully!</p>
               <a href="https://www.restaurent.aptc.rw/pages/booking/application_letter.php?emailcnfrm='.$confirmation.'" target="_blank">
                   <button style="background-color: rgb(117, 8, 8); padding: 10px;width: 250px;color: white;font-size: 17px;border: none;border-radius: 4px;">Download Application Letter</button>
               </a>
           </td>
           </tr>
           <tr>
               <td>
                <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                <p class="f-fallback sub">https://www.restaurent.aptc.rw/</p>
               </td>
           </tr>
           <tr>
            <td>
             <p>For security reason , if you did not reserve, please ignore this email or <a href="http://restaurent.aptc.rw/">contact support</a> if you have questions.</p>
             <p>Thanks,
             <br>The RESTO Team</p>
             <!-- Sub copy -->
            </td>
        </tr>
           <tr>
               <td>
                   <hr>
                &copy; '.$year.' RESTO. All rights reserved.</br>
                Phone (+250) 789028963
               </td>
           </tr>
       </tbody>
   </table> 
   </div>';
        $message .= '</body></html>';

        // Sending email
        if(mail($to, $subject, $message, $headers)){
        $msg=" Check your E-mail $email ";
        }
        
        $sql_check = $db->prepare("SELECT * FROM tbl_reservation WHERE (date(arrival) >= '".$chkin."' AND date(departure) <= '".$chkout."') AND roomID='".$room."' ");
        $sql_check->execute();
        $rowcount = $sql_check->rowCount();
        if($rowcount > 0){
            $fetchdate = $sql_check->fetch();
            $startDate = $fetchdate['arrival'];
            $endDate = $fetchdate['departure'];
            
            $msge = "Room Already Reserved!"." ". "from"." ".$startDate." "."to"." ".$endDate;
            echo'<meta http-equiv="refresh"'.'content="5;URL=?resto=mngeResrv">';
        }
        else{
            
        $sql1 = $db->prepare("INSERT INTO `tbl_reservation` (`roomID`,`guest_id`,`arrival`,`departure`,`adults`,`child`,`status`,`confirmation`,`resv_CpnyID`)
        VALUES(?,?,?,?,?,?,?,?,?)");
        $sql1->execute(array($room,$guest,$chkin,$chkout,$adults,$children,3,$confirmation,$company));
        
        $msg = "Reservation Saved Successfully! Please Check on Email: $g_email";
        echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=mngeResrv">';
    }
    }
 ?>

<!-- Form Element area Start-->
    <div class="form-element-area">
        <div class="container">
            <div class="row">
                
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
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> Manage Reservation</strong></small></h2>
                        </div>
                     <hr>
                    <br>
                        <form action="" method="POST">
                          <div class="row">
                            <label class="col-md-2 control-label" for=""><strong> <small>Select Guest</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                             <div class="input-group input-group-icon">
                               <select name="guest" class="form-control chosen" data-live-search="true" placeholder="Choose One" required>
                                  <option></option>
                                     <?php 
                                        $stmt = $db->prepare("SELECT * FROM `guest`");
                                        $stmt->execute();
                                        while($fetch = $stmt->fetch()){
                                        ?>
    									<option value="<?php echo $fetch['guest_id']; ?>"><?php echo $fetch['firstname'] ." ". $fetch['lastname']." (".$fetch['phone'].")"; ?></option>
    									<?php } ?>
    							     </select>
    							     
                               <span class="input-group-addon pointer" data-toggle="modal" data-target="#myModalone" title="New Guest?"><i class="fa fa-user-plus text-danger fa-sm"></i></span>
                              </div>
                            </div>
                            <label class="col-md-2 control-label" for=""><strong> <small>Room No.</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                 <select name="room" id="room" class="chosen form-control" data-live-search="true" onchange=AjaxFunction(); required>
                                    <option value=""></option>
                                    <?php
                                    $sql = $db->prepare("SELECT * FROM `tbl_rooms` WHERE rm_status=1 AND company_id = '".$cpny_ID."' ");
    		                        $sql->execute();
    		                        while($fetch = $sql->fetch()){
                                    ?>
						            <option value="<?php echo $fetch['room_id'] ?>"><?php echo $fetch['room_no']; ?></option>
						            <?php } ?>
						        </select>
                            </div>
                        </div>
                    <br>
                    <div class="row">
                    <div name='display_res' id='display_res'>
          
                    </div>
                    </div>
                      <br>  
                    <div class="row">
                        <label class="col-md-2 control-label" for=""><strong><small>Check In Date</small></strong><span class="text-danger">*</span></label>
                         <div class="col-md-3">
                           <input type="text" name="chkin" onfocus= (this.type='date') class="form-control input-sm" placeholder="Enter Check In Date" required>
                          </div>
                        <label class="col-md-2 control-label" for=""><strong><small>Check Out Date</small></strong></label>
                         <div class="col-md-3">
                          <input type="text" name="chkout" onfocus= (this.type='date') class="form-control input-sm" placeholder="Check Out Date" required>
                         </div>
                        </div>
                     <br>
                    <div class="row">
					    <div class="form-actions col-md-12">
					        <br />
					        <center>								
						        <button type="submit" id="" name="btnResrv" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Reserve Now!</button>
						        <button type="reset" class="btn btn-sm label-secondary margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
					        </center>
					    </div>
                    </div>
                  <br><br>
                 </form>
                </div>
            </div>
        </div>
       
    </div>
</div>
<!-- Form Element area End-->

<?php include 'guest_modalAdd.php';?>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#room").change(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_adlt_chld.php?room=' + $(this).val() , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>