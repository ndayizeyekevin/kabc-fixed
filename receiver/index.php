<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='delivery_note.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='delivery_note.php';
		break;

			
		case 'viewDelivery' :
		$title="view_delivery";
		$page='view_delivery.php';
		break;
		
		case 'editDelivery' :
		$title="editDelivery";
		$page='editDelivery.php';
		break;
			case 'addDeliveryItem' :
		$title="addDeliveryItem";
		$page='addDeliveryItem.php';
		break;
		

		
			
		case 'printDelivery' :
		$title="print_delivery";
		$page='print_delivery.php';
		break;
		
	
		
		
		
	default :
	    $title="Home";
		$page ='delivery_note.php';
     }

    require_once("../inc/ontouch.php");
?>

