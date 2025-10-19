<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='dashboard.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='dashboard.php';
		break;
	case 'viewRequests' :
        $title="Manage Request";
		$page='view_requests.php';
		break;
		
	default :
	    $title="Home";
		$page ='dashboard.php';
     }

    require_once("../inc/ontouch.php");
?>

