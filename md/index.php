<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='requests.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='requests.php';
		break;

	case 'stock_balance' :
		$title="Stock Balance";
		$page='stock_balance.php';
		break;
	
	case 'sales' :
		$title="Restaurant Sales";
		$page='sales.php';
		break;

	case 'room_sales' :
		$title="Room Sales";
		$page='room_sales.php';
		break;
	
	case 'cumurative':
		$title="Cumulative Stock Movement";
		$page='cumulative_stock.php';
		break;
	

	default :
	    $title="Home";
		$page ='requests.php';
}

    require_once("../inc/ontouch.php");
?>

