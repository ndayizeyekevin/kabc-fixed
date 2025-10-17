<?php 
require_once("../inc/session.php");
require_once("../inc/DBController.php");
$msg = '';
$msge = '';

include '../inc/conn.php';
// include'../holder/topkey.php';
// include'../holder/template_styles.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    
    if(isset($_POST['forgot'])){
        
        $user  = $_POST['usn'];
        
        
$sql = "SELECT * FROM tbl_user_log where email ='$user'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
      
$msg_code = "Your Reset code:". rand(10000,10000);

// use wordwrap() if lines are longer than 70 characters
$msg_code = wordwrap($msg_code,70);

// send email
mail($user,"Reset Password",$msg_code);

$msg = 'We have sent you an email with reset link';




  }
}else{
    
    
// $msg_code = "Your Reset code:". rand(10000,10000);

// // use wordwrap() if lines are longer than 70 characters
// $msg_code = wordwrap($msg_code,70);

// // send email
// mail($user,"Reset Password",$msg_code);


    $msge = 'Email not found, contact admistartion for support';
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
