<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='purchase.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='purchase.php';
		break;

	
		
				
		case 'view_purchase' :
		$title="Request";
		$page='view_purchase.php';
		break;
		
			case 'create_purchase' :
		$title="create_purchase";
		$page='create_purchase.php';
		break;
		
		
			case 'editPurchase' :
		$title="editPurchase";
		$page='editPurchase.php';
		break;
		
		
		
		case 'print_purchase' :
		$title="print_purchase";
		$page='print_purchase.php';
		break;
		
		case 'manageRequest' :
		$title="Manage";
		$page='manage.php';
		break;
		
	
		
		
		
	default :
	    $title="Home";
		$page ='purchase.php';
     }

    require_once("../inc/ontouch.php");
?>

