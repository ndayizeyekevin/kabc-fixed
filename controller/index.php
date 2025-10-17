<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='Manage_CheckIn_guest.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
	       $title="Dashboard";
		$page='Manage_CheckIn_guest.php';
		break;
	case 'gstDet':
        $title = "Order Datails";
        $page = 'View_OrdderDet.php';
        break;
	case 'waiter_report':
        $title = "waiter_report";
        $page = 'waiter_report.php';
        break;
	case 'reception':
        $title = "reception";
        $page = 'receipt-checks.php';
        break;
		case 'sale_summary':
        $title = "sale_summary";
        $page = 'sale_summary.php';
        break;

	 case 'reportb':
        $title = "Report";
        $page = 'sales_report.php';
        break;
	case 'void':
        $title = "void";
        $page = 'void.php';
        break;

	case 'detailedsmumary':
        $title = "detailedsmumary";
        $page = 'details_summary.php';
        break;
	case 'stock_balance':
        $title = "stock_balance";
        $page = 'stock_balance.php';
        break;
	case 'cumurative':
        $title = "cumurative";
        $page = 'cumurative_stock.php';
        break;

	case 'requests':
        $title = "requests";
        $page = 'addrequest.php';
        break;
        case 'requestStore' :
		$title="Request";
		$page='request.php';
		break;
        case 'supplier_report' :
		$title="supplier_report";
		$page='supplier_report.php';
		break;
        case 'ReportByCategory' :
		$title="ReportByCategory";
		$page='ReportByCategory.php';
		break;
        case 'ReportByDepartment' :
		$title="ReportByDepartment";
		$page='ReportByDepartment.php';
		break;
        case 'staff_report' :
		$title="staff_report";
		$page='staff_report.php';
		break;
        case 'baqueting_report' :
		$title="baqueting_report";
		$page='baqueting_report.php';
		break;
        case 'manageRequest' :
		$title="manageRequest";
		$page='manage.php';
		break;
        case 'purchase' :
		$title="purchase";
		$page='purchase.php';
		break;
        case 'view_purchase' :
		$title="view_purchase";
		$page='view_purchase.php';
		break;
        case 'breakfast' :
		$title="breakfast";
		$page='breakfast.php';
		break;

        case 'inhouse' :
		$title="inhouse";
		$page='active_booking.php';
		break;
        case 'venuereports':
		$title = "venuereports";
		$page = 'venuereports.php';
		break;
        case 'checkutrepo':
		$title = "Checked out";
		$page = 'check_out_report.php';
		break;

	default :
	    $title="Controller Index";
		$page ='Manage_CheckIn_guest.php';
     }

    require_once("../inc/ontouch.php");
?>

