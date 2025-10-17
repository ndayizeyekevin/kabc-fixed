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
        
        echo'<meta http-equiv="refresh"'.'content="1;URL=?resto=new_reservation">';
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
}
    
//   Reservation
    if(ISSET($_POST['add_rsv'])){
        
        $guest = $_POST['guest'];
        $room = $_POST['room'];
        $chkin = $_POST['chkin'];
        $chkout = $_POST['chkout'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $adults = $_POST['adults'];
        $children = $_POST['children'];
        $pay = $_POST['pay'];
        $country = $_POST['country'];
        $address = $_POST['address'];
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
        $subject = 'Application Sent to resto Team';
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
        $message = ' <title>resto</title>';
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
             <br>The resto Team</p>
             <!-- Sub copy -->
            </td>
        </tr>
           <tr>
               <td>
                   <hr>
                &copy; '.$year.' resto. All rights reserved.</br>
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
        header('Location:index?msg='.$msg);
        } else{
        $msg=" Unable to send email. Please try again ";
        header('Location:index?msg='.$msg);
        }
        
        $sql_check = $db->prepare("SELECT * FROM tbl_reservation WHERE (date(arrival) >= '".$chkin."' AND date(departure) <= '".$chkout."') AND roomNo='".$room."' ");
        $sql_check->execute();
        $rowcount = $sql_check->rowCount();
        if($rowcount > 0){
            $fetchdate = $sql_check->fetch();
            $startDate = $fetchdate['arrival'];
            $endDate = $fetchdate['departure'];
            
            $msge = "Room Already Reserved!"." ". "from"." ".$startDate." "."to"." ".$endDate;
            echo'<meta http-equiv="refresh"'.'content="5;URL=?resto=new_reservation">';
        }
        else{
            $msg = "Room Reserved";
        
        $sql1 = $db->prepare("INSERT INTO `tbl_reservation` (`roomID`,`guest_id`,`arrival`,`departure`,`adults`,`child`,`status`,`confirmation`,`resv_CpnyID`)
        VALUES(?,?,?,?,?,?,?,?,?)");
        $sql1->execute(array($room,$guest,$chkin,$chkout,$adults,$children,3,$confirmation,$cpny_ID));
        
        $msg = "Reservation Saved Successfully! Please Check on Email: $g_email";
        echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=new_reservation">';
    }
    }
    
    ?>
<!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
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
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="fa fa-cog"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Add Reservation</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->
<div class="container">	
	 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <form action="" method="POST">
                <div class="form-example-int form-horizental">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                <label class="hrzn-fm">Select Guest</label>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <div class="nk-int-st" style="display:flex;">
                                    <select name="guest" class="selectpicker" data-live-search="true" required>
                                        <option value="">Select Guest</option>
                                    <?php 
                                    $stmt = $db->prepare("SELECT * FROM `guest` ");
                                    $stmt->execute();
                                    while($fetch = $stmt->fetch()){
                                    ?>
									<option value="<?php echo $fetch['guest_id']; ?>"><?php echo $fetch['firstname'] ." ". $fetch['lastname']; ?></option>
									<?php } ?>
							        </select>&nbsp&nbsp&nbsp
							        <div class="breadcomb-report"><button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Reservation" class="btn"><i class="fa fa-plus-circle"></i> Add</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-example-int form-horizental mg-t-15">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                <label class="hrzn-fm">Room Number</label>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <div class="nk-int-st">
                                    <select name="room" class="selectpicker" data-live-search="true" required>
                                        <option value="">Select Room Number</option>
                                        <?php
                                        $sql = $db->prepare("SELECT * FROM `tbl_rooms` WHERE room_id NOT IN (SELECT roomID FROM `tbl_reservation`) ");
        		                        $sql->execute();
        		                        while($fetch = $sql->fetch()){
                                        ?>
							            <option value="<?php echo $fetch['room_id'] ?>"><?php echo $fetch['room_no']; ?></option>
							            <?php } ?>
							        </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-example-int form-horizental mg-t-15">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                <label class="hrzn-fm">Check In Date</label>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <div class="nk-int-st">
                                    <input type="text" name="chkin" onfocus= (this.type='date') class="form-control input-sm" placeholder="Enter Check In Date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-example-int form-horizental mg-t-15">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                <label class="hrzn-fm">Check Out Date</label>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <div class="nk-int-st">
                                    <input type="text" name="chkout" onfocus= (this.type='date') class="form-control input-sm" placeholder="Check Out Date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-example-int form-horizental mg-t-15">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                <label class="hrzn-fm">Adults</label>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <div class="nk-int-st">
                                    <input type="number" min="0" max="10" name="adults" class="form-control input-sm" placeholder="Number Of Adults">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-example-int form-horizental mg-t-15">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                <label class="hrzn-fm">Children</label>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <div class="nk-int-st">
                                    <input type="number" min="0" max="10" name="children" class="form-control input-sm" placeholder="Number Of Children">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-example-int mg-t-15">
                    <div class="row">
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="breadcomb-report">
                            <button type="submit" name="add_rsv" title="save reservation" class="btn"><i class="fa fa-upload"></i>  Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" enctype="multipart/form-data" method="POST">
                <div class="modal-body">
                    <h2>Add Guest</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input type="text" class="form-control" name="fname" placeholder="First Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input type="text" name="fam_name" id="fam_name" class="form-control" placeholder="Last Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                        <input  type="text" onfocus=(this.type='email') name="email" id="email" class="form-control" placeholder="Email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input  type="text" name="nid" id="nid" class="form-control" placeholder="ID/Passport" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input  type="text" name="address" id="address" class="form-control" placeholder="Address">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                        <select id="country" name="country" class="selectpicker" data-live-search="true" style="width: 100%;" required placeholder="Select Country" required>
                                         <option value="">Select Country</option>
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
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="form-ic-cmp">
                                        
                                    </div>
                                    <div class="nk-int-st">
                                       <input type="text" name="city" id="city" class="form-control" placeholder="City">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                   </div>
                  <hr>
                <div class="modal-footer">
                    <button type="submit" name="register" class="btn btn-default">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>