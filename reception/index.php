<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once("../inc/config.php");
if ($logging_ID == '' or $USER_ID == '') {
    echo"<script>window.location.replace('../index')</script>";
}

$page = 'Manage_CheckIn_guest.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

    case 'home':
        $title = "Dashboard";
        $page = 'Manage_CheckIn_guest.php';
        break;

    case 'history':
        $title = "History";
        $page = 'history.php';
        break;
    
    case 'receipts':
        $title = "Dashboard";
        $page = 'Manage_receipts.php';
        break;

    case 'recall':
        $title = "recall";
        $page = 'recall.php';
        break;
    case 'detailedsmumary':
        $title = "detailedsmumary";
        $page = 'details_summary.php';
        break;


    case 'void':
        $title = "void";
        $page = 'void.php';
        break;


    case 'view_reservation_details':
        $title = "View Reservation";
        $page = 'reserve_details.php';
        break;


    case 'advances':
        $title = "advances";
        $page = 'advances.php';
        break;
    case 'lorder':
        $title = "New Order";
        $page = __DIR__."/../services/viewOrder.php";
        break;

    case 'collections':
        $title = "collections";
        $page = 'collections.php';
        break;

    case 'sale_summary':
        $title = "sale_summary";
        $page = 'sale_summary.php';
        break;


    case 'days':
        $title = "days";
        $page = 'days.php';
        break;

    case 'mngeResrv':
        $title = "Manage Reservation";
        $page = 'Oug_reservAll.php';
        break;

    case 'transfer':
        $title = "Transfer Reservation";
        $page = 'transfer.php';
        break;


    case 'credit_account':
        $title = "credit_account";
        $page = 'credit_account.php';
        break;


    case 'chk_in_excel':
        $title = "Check In";
        $page = 'chkIn_excel.php';
        break;

    case 'chk_out_excel':
        $title = "Check Out";
        $page = 'chkOut_excel.php';
        break;

    case 'report':
        $title = "Report";
        $page = 'sales_report.php';
        break;


    case 'sales_temp':
        $title = "sales_t";
        $page = 'sale_temp.php';
        break;
    case 'norder':
        $title = "New Order";
        $page = 'takeOrder.php';
        break;
    case 'prcsOrder_prcssng':
        $title = "New Order";
        $page = 'prcsOrder_prcssng_view.php';
        break;

    case 'reportb':
        $title = "Report";
        $page = 'sales_report.php';
        break;

    case 'servedReport':
        $title = "Report";
        $page = 'report_details.php';
        break;

    case 'generalreport':
        $title = "General Report";
        $page = 'general_report.php';
        break;

    case 'OurGste':
        $title = "Guest List";
        $page = 'Manage_CheckIn_guest.php';
        break;

    case 'gstDet':
        $title = "Order Datails";
        $page = 'View_OrdderDet.php';
        break;

    case 'OurGste_view':
        $title = "Guest List";
        $page = 'view_reserv_details.php';
        break;


    case 'gstInvce':
        $title = "Guest Invoice";
        $page = 'GetGste_Invoice.php';
        break;

    case 'ebm':
        $title = "EBM Receipts";
        $page = 'ebm.php';
        break;

    case 'reports':
        $title = "sale_by_category";
        $page = 'sale_by_category.php';
        break;
    case 'reception':
        $title = "reception";
        $page = 'receipt-checks.php';
        break;


    case 'waiter_report':
        $title = "request";
        $page = 'waiter_report.php';
        break;

    case 'request':
        $title = "Request";
        $page = 'addrequest.php';
        break;
    case 'norder':
        $title = "New Order";
        $page = 'takeOrder.php';
        break;
    
    case 'printInternal':
		$title = 'Print Internal Request';
		$page = 'print_internal.php';
		break;
        
    default:
        $title = "Home";
        $page = 'Manage_CheckIn_guest.php';
}

require_once("../inc/ontouch.php");
?>

