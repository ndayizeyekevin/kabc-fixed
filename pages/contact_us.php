 <?php
 if(isset($_POST['submit'])){
     $name = $_POST['name'];
     $phone = $_POST['phone'];
     $email = $_POST['email'];
     $subject = $_POST['subject'];
     $message = $_POST['message'];
     $today = date("Y-m-d H:i:s");
     
     $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     
     $sql = $db->prepare("INSERT INTO `tbl_contact_us` (`name`,`email`,`phone`,`subject`,`message`,`due_date`)VALUES(:name,:email,:phone,:subject,:message,:due_date)");
     $sql->bindParam(':name',$name);
     $sql->bindParam(':email',$email);
     $sql->bindParam(':phone',$phone);
     $sql->bindParam(':subject',$subject);
     $sql->bindParam(':message',$message);
     $sql->bindParam(':due_date',$today);
     
     $sql->execute();
     
     $msg = "Your Message Sent Successfully!";
 }
 ?>
 <div id="contact-page" class="container">
    	<div class="bg">
    		<div class="row"> 
	    		<div class="col-sm-8">
	    			<div class="contact-form">
	    				<h2 class="title text-center">Get In Touch</h2>
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
				    	<form action="" id="main-contact-form" class="contact-form row" name="contact-form" method="post">
				            <div class="form-group col-md-4">
				                <input type="text" name="name" class="form-control" required="required" placeholder="Name">
				            </div>
				            <div class="form-group col-md-4">
				                <input type="text" name="phone" class="form-control" required="required" placeholder="Mobile Number">
				            </div>
				            <div class="form-group col-md-4">
				                <input type="email" name="email" class="form-control" required="required" placeholder="Email">
				            </div>
				            <div class="form-group col-md-12">
				                <input type="text" name="subject" class="form-control" required="required" placeholder="Subject">
				            </div>
				            <div class="form-group col-md-12">
				                <textarea name="message" id="message" required="required" class="form-control" rows="8" placeholder="Your Message Here"></textarea>
				            </div>                        
				            <div class="form-group col-md-12">
				                <input type="submit" name="submit" class="btn btn-primary pull-right" value="Submit">
				            </div>
				        </form>
	    			</div>
	    		</div>
	    		<div class="col-sm-4">
	    			<div class="contact-info">
	    				<h2 class="title text-center">Contact Info</h2>
	    				<address>
	    					<p>OUGAMI</p>
							<p>street KN 1 Rd 4,MUHIMA- Near Post Office</p>
							<p>P.O. Box 4179 KIGALI RWANDA</p>
							<p>Mobile: +250 788 730 582</p>
							<p>Email: info@ougami.com</p>
	    				</address>
	    				<div class="social-networks">
	    					<h2 class="title text-center">Social Networking</h2>
							<ul>
								<li>
									<a href="#"><i class="fa fa-facebook"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-twitter"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-google-plus"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-youtube"></i></a>
								</li>
							</ul>
	    				</div>
	    			</div>
    			</div>    			
	    	</div>  
    	</div>	
    </div><!--/#contact-page-->