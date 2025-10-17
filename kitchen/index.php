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
		$page='takeMenu_Order.php';
		break;
		
	case 'prcsOrder' :
        $title="Menu Order";
		$page='takeMenu_Order.php';
		break;
		
		
	case 'stock' :
        $title="Stock List";
		$page='recstock.php';
		break;
		
	case 'stock_take' :
        $title="stock_take";
		$page='stock_take.php';
		break;
		
		
	case 'menu' :
        $title="Menu";
		$page='menu_list.php';
		break;
		
	case 'prcsOrder_list' :
        $title="Menu Order";
		$page='takeMenu_Order_list.php';
		break;
		
	case 'report' :
		$title="Report";
		$page='sales_report.php';
		break;

	case 'servedReport' :
		$title="Report";
		$page='report_details.php';
		break;	
		
	case 'prcsOrder_prcssng' :
        $title="Menu Order";
		$page='takeMenu_Order_prcssng.php';
		break;
		
	case 'prcsOrder_prcssng_view' :
        $title="Menu Order";
		$page='takeMenu_Order_prcssng_view.php';
		break;
		
	case 'request' :
        $title="Request";
		$page='addrequest.php';
		break;
		
    case 'delivered':
		$title="delivered Oreders";
		$page='delivered_orders.php';
		break;
		
	case 'printInternal' :
        $title="Print";
		$page='print_internal.php';
		break;
		
	default :
	    $title="Home";
		$page ='dashboard.php';
     }

    require_once("../inc/ontouch.php");
?>

