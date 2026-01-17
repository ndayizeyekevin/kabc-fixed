<?php 
require_once "inc/config.php";
include 'holder/topkey.php';
include 'holder/template_styles.php';

$tm = date("Y-m-d H:i:s");

if (isset($_SESSION['u_id']) && isset($_SESSION['user_id'])) {

    $logout_user = $db->prepare("UPDATE tbl_user_log SET last_logged_out=? WHERE u_id=?");

    try {
        $logout_user->execute(array($tm, $_SESSION['u_id']));

        $name = strtoupper($_SESSION['f_name'] . " " . $_SESSION['l_name']);
        $role = $_SESSION['log_role']; // store before session destroy

        session_unset();
        session_destroy();
        ?>

        <!-- Logout Register area Start-->
        <div class="login-content">
            <div class="nk-block toggled" id="l-login">
                <div class="nk-form">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-times"></i></span>
                        </button> 
                        <strong>Dear</strong> <?php echo htmlentities($name); ?>, You have successfully logged out.
                    </div>
                </div>
            </div>
        </div>
        <!-- Logout Register area End-->

        <?php
        // Redirect logic
        if (in_array($role, [5, 6, 10, 12])) { // Waiter
            echo '<meta http-equiv="refresh" content="1;URL=pages/index?page=ordercomplete">';
        } else {
            echo '<meta http-equiv="refresh" content="1;URL=pages/index?page=login">';
        }

    } catch (PDOException $ex) {
        echo $ex->getMessage();
    }

} else {
    // No session found — redirect straight to login
    echo '<meta http-equiv="refresh" content="0;URL=pages/index?page=login">';
    exit();
}

include 'holder/template_scripts.php';
include 'holder/lowkey.php';
?>
