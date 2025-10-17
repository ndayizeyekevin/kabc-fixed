<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='takeMenu_Order.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='addrequest.php';
		break;
		
	
		
	
	case 'stock' :
        $title="Stock List";
		$page='recstock.php';
		break;

	case 'printInternal':
		$title="Print Internal Requisition";
		$page='print_internal.php';
		break;
		

		
		
	
		
	

	
		
	case 'request' :
        $title="Request";
		$page='addrequest.php';
		break;
		
	default :
	    $title="Home";
		$page ='addrequest.php';
     }

    require_once("../inc/ontouch.php");
?>

