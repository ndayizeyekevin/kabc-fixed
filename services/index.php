<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='viewOrder.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='viewOrder.php';
		break;

	case 'Service' :
        $title="Service";
		$page='services.php';
		break;


	case 'norder' :
        $title="New Order";
		$page='takeOrder.php';
		break;

	case 'lorder' :
        $title="New Order";
		$page='viewOrder.php';
		break;

	case 'prcsOrder_prcssng' :
        $title="New Order";
		$page='prcsOrder_prcssng_view.php';
		break;

	case 'report' :
		$title="Report";
		$page='sales_report.php';
		break;

	case 'servedReport' :
		$title="Report";
		$page='report_details.php';
		break;

	default :
	    $title="Home";
		$page ='viewOrder.php';
     }

    require_once("../inc/ontouch.php");
?>

