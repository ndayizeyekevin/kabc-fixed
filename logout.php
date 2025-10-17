<?php 
require_once "inc/config.php";
include'holder/topkey.php';
include'holder/template_styles.php';
 
 $tm=date("Y-m-d H:i:s");
	
if (isset($_SESSION['u_id']) and isset($_SESSION['user_id']))
{

$logout_user = $db->prepare("UPDATE tbl_user_log SET last_logged_out=? WHERE u_id=?");
    try {
        // insert into tbl_personal_ug
        $logout_user->execute(array($tm, $_SESSION['u_id']));
        // end to update
		$name=strtoupper($_SESSION['f_name']." ".$_SESSION['l_name'] );

        session_unset();
        session_destroy();
        
        ?>

<!-- Logout Register area Start-->
    <div class="login-content">
        <!-- Login -->
    <div class="nk-block toggled" id="l-login">
        <div class="nk-form">
         <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true"><i class="fa fa-times"></i></span></button> 
            <strong> Dear</strong>  <?php echo htmlentities($name); ?> You have successfully logged out.
         </div>
      </div>
   </div>
</div>


    <?php             
        }
        catch (PDOException $ex) {
            //Something went wrong rollback!			
            echo $ex->getMessage();
        }
    }
    else
    {
 ?>
 
 <!-- Logout Register area Start-->
    <div class="login-content">
        <!-- Login -->
    <div class="nk-block toggled" id="l-login">
        <div class="nk-form">
         <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true"><i class="fa fa-times"></i></span></button> 
            <strong> OOH Snap</strong> No User Data. Use your login and try!.
         </div>
      </div>
   </div>
</div>

 
 </div>
<!-- Logout Register area End-->
 <?php
    }
   
 echo'<meta http-equiv="refresh"'.'content="1;URL=pages/index?page=login">'; 
  include'holder/template_scripts.php'; 
  include'holder/lowkey.php'; 
?>
