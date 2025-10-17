<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='dashboard.php';
$view = (isset($_GET['onkey']) && $_GET['onkey'] != '') ? $_GET['onkey'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='dashboard.php';
		break;
		
	case 'cashier' :
        $title="Finance";
		$page='cash.php';
		break;
		
	default :
	    $title="Home";
		$page ='dashboard.php';
     }

    require_once("../inc/ontouch.php");
?>

