<?php
ob_start();
require_once("../inc/session.php");
require_once("../inc/DBController.php");

include '../holder/topkey.php';
include '../holder/template_styles.php';

if (!empty($_POST["remember"])) {
	setcookie("pwd", $_POST["pwd"], time() + 3600);
	//  	echo "Cookies Set Successfuly";
} else {

	setcookie("pwd", "");
	// 	echo "Cookies Not Set";
}

if (isset($_REQUEST['pwd'])) {

	/* die(var_dump($_REQUEST)); */


	$pwd = md5($_REQUEST['pwd']);
	$sql = $db->prepare("SELECT * FROM tbl_user_log WHERE pwd='$pwd'");
	$sql->execute();
	if ($sql->rowCount() != 0) {
		$res = $sql->fetch();
		$_SESSION['f_name'] = $res['f_name'];
		$_SESSION['l_name'] = $res['l_name'];
		$_SESSION['u_id'] = $res['u_id'];
		$_SESSION['usn'] = $res['usn'];
		$_SESSION['email'] = $res['email'];
		$_SESSION['log_role'] = $res['log_role'];
		$_SESSION['user_id'] = $res['user_id'];
		$_SESSION['access'] = true;
		$today = date("Y-m-d H:i:s");
		$name = strtoupper($_SESSION['f_name'] . " " . $_SESSION['l_name']);
		$login_time = $db->prepare("UPDATE tbl_user_log SET last_logged_in=? WHERE u_id=?");
		try {
			// time logged
			$login_time->execute(array($today, $_SESSION['u_id']));
		} catch (PDOException $ex) {
			//Something went wrong rollback!
			echo $ex->getMessage();
		}

		if ($_SESSION['log_role'] == 0) //SuperAdmin
		{
			header('Location:../team/index');
		} elseif ($_SESSION['log_role'] == 1) //Admin
		{
?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../inn/index">';
		} elseif ($_SESSION['log_role'] == 2) //Finance
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../finance/index">';
		} elseif ($_SESSION['log_role'] == 3) //Room  Manager
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../room/index">';
		} elseif ($_SESSION['log_role'] == 4) //Kitchen Manager
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../kitchen/index?resto=home">';
		} elseif ($_SESSION['log_role'] == 5) //Barman
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../barman/index">';
		} elseif ($_SESSION['log_role'] == 6) //Services
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../services/index">';
		} elseif ($_SESSION['log_role'] == 7) //Store Keeper
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php


			echo '<meta http-equiv="refresh"' . 'content="1;URL=../store/index">';
		} elseif ($_SESSION['log_role'] == 15) // Procurement
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php


			echo '<meta http-equiv="refresh"' . 'content="1;URL=../procurement/index">';

			//  $_SESSION['log_role']==15
		} elseif ($_SESSION['log_role'] == 14) // Receiver
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php


			echo '<meta http-equiv="refresh"' . 'content="1;URL=../receiver/index">';

			//  $_SESSION['log_role']==15
		} elseif ($_SESSION['log_role'] == 8) //Client
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../guest/index">';
		} elseif ($_SESSION['log_role'] == 9) //M. Director
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../md/index">';
		} elseif ($_SESSION['log_role'] == 10 ) //Reception
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../reception/index">';
		} elseif ($_SESSION['log_role'] == 11 ) //Reception
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../controller/index">';
		}
		elseif ($_SESSION['log_role'] == 12) //Reception
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../supervisor/index">';
		} elseif ($_SESSION['log_role'] == 13) //House Keeper
		{
		?>
			<div class="login-content">
				<div class="nk-block toggled" id="l-login">
					<div class="nk-form">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
							<strong> Dear</strong> <?php echo htmlentities($name); ?> You have successfully logged in.
						</div>
					</div>
				</div>
			</div>
		<?php
			echo '<meta http-equiv="refresh"' . 'content="1;URL=../house/index">';
		}
	} else {
		?>
		<div class="login-content">
			<div class="nk-block toggled" id="l-login">
				<div class="nk-form">
					<div class="alert alert-danger alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true"><i class="fa fa-times"></i></span></button>
						<strong> Sorry!</strong> Incorrect Username Or Password. Please Try Again!
					</div>
				</div>
			</div>
		</div>
<?php
		echo '<meta http-equiv="refresh"' . 'content="1;URL=index?page=login">';
	}
}
include '../holder/template_scripts.php';
include '../holder/lowkey.php';
ob_end_flush();
?>
