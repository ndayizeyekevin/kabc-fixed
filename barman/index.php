<?php

require_once("../inc/config.php");
if ($logging_ID == '' or $USER_ID == '') {
	echo "<script>window.location.replace('../index')</script>";
}

$page = 'takeMenu_Order.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home':
		$title = "Dashboard";
		$page = 'takeMenu_Order.php';
		break;

	case 'prcsOrder':
		$title = "Menu Order";
		$page = 'takeMenu_Order.php';
		break;

	case 'printBar':
		$title = "printBar";
		$page = 'printBar.php';
		break;
	case 'norder':
		$title = "New Order";
		$page = 'takeOrder.php';
		break;
	case 'MostSold':
		$title = "MostSold";
		$page = 'MostSold.php';
		break;




	case 'menu':
		$title = "Menu";
		$page = 'menu_list.php';
		break;

	case 'prcsOrder_list':
		$title = "Menu Order";
		$page = 'takeMenu_Order_list.php';
		break;

	case 'reportb':
		$title = "Report";
		$page = 'sales_report.php';
		break;

	case 'servedReport':
		$title = "Report";
		$page = 'report_details.php';
		break;

	case 'prcsOrder_prcssng':
		$title = "New Order";
		$page = 'prcsOrder_prcssng_view.php';
		break;
	case 'lorder':
		$title = "New Order";
		$page = 'viewOrder.php';
		break;

	case 'prcsOrder_prcssng_view':
		$title = "Menu Order";
		$page = 'takeMenu_Order_prcssng_view.php';
		break;

	case 'request':
		$title = "Request";
		$page = 'req.php';
		break;

	case 'waiter_report':
		$title = "request";
		$page = 'waiter_report.php';
		break;
	
	case 'printInternal':
		$title = 'Print Internal Request';
		$page = 'print_internal.php';
		break;

	default:
		$title = "Home";
		$page = 'takeMenu_Order.php';
}

require_once("../inc/ontouch.php");
