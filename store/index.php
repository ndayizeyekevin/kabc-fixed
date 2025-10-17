<?php
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='addrequest.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';
include './controllers/storeController.php';
switch ($view) {

	    case 'home' :
        $title="Dashboard";
		$page='addrequest.php';
		break;
		case 'WriteOffdetails' :
		$title="WriteOffdetails";
		$page='damagedGooddetails.php';
		break;
	    case 'staff_report' :
        $title="Staff Report";
		$page='staff_report.php';
		break;
		
	    case 'baqueting_report' :
        $title="baqueting report";
		$page='baqueting_report.php';
		break;
		
	    case 'stock' :
        $title="Stock List";
		$page='recstock.php';
		break;
		 
		case 'print_purchase' :
        $title="print_purchase";
		$page='print_purchase.php';
		break;
		
		case 'editPurchase' :
        $title="editPurchase";
		$page='editPurchase.php';
		break;
		
		case 'create_purchase' :
        $title="create_purchase";
		$page='create_purchase.php';
		break;
		
		case 'stock_take' :
        $title="stock_take";
		$page='stock_take.php';
		break;
		
		case 'addDeliveryItem' :
        $title="addDeliveryItem";
		$page='addDeliveryItem.php';
		break;
		
		case 'reportanalysis' :
        $title="report analysis";
		$page='reportanalysis.php';
		break;
		
		case 'update_item' :
        $title="Update edit";
		$page='update_item.php';
		break;
		
	    case 'printSupplier' :
        $title="Print";
		$page='print_supplier.php';
		break;
		
		case 'printStock' :
        $title="Print";
		$page='printStock.php';
		break;
		
		case 'ReportByDepertment' :
        $title="ReportByDepertment";
		$page='ReportByDepertment.php';
		break;
		
		case 'ReportByCategory' :
        $title="ReportByCategory";
		$page='ReportByCategory.php';
		break;
		
		case 'suppliers' :
        $title="Manage Suppliers";
		$page='suppliers.php';
		break;
		
		
		case 'StockLimit' :
        $title="Limit";
		$page='stocklimit.php';
		break;
		
		case 'stock_balance' :
        $title="Stock Balance";
		$page='stock_balance.php';
		break;
		
		case 'printDelivery' :
        $title="Print";
		$page='print_delivery.php';
		break;
		
		case 'printPurchase' :
        $title="Print";
		$page='print_purchase.php';
		break;
		
		case 'printInternal' :
        $title="Print";
		$page='print_internal.php';
		break;
		
		case 'request' :
        $title="Request";
		$page='addrequest.php';
		break;
		
		case 'categories' :
        $title="categories";
		$page='category.php';
		break;
		
		case 'supplier_report' :
        $title="Suplier Report";
		$page='supplier_report.php';
		break;

	    case 'reportc' :
		$title="Report";
		// $page='stock_report.php';
		$page='ReportByCategory.php';
		break;
		
		case 'user' :
		$title="Report";
		$page='stock_report.php';
		break;
		
		case 'purchases' :
		$title="Purchase";
		$page='purchase.php';
		break;
		
		case 'delivery' :
		$title="Delivery Note";
		$page='delivery_note.php';
		break;
		
		case 'WriteOff' :
		$title="WriteOff";
		$page='damagedGood.php';
		break;
	
		case 'requestStore' :
		$title="Request";
		$page='request.php';
		break;
		
		case 'manageRequest' :
		$title="Manage";
		$page='manage.php';
		break;
		
		case 'editDelivery' :
		$title="Edit Delivery";
		$page='editDelivery.php';
		break;
		
		case 'print_request' :
		$title="print_request";
		$page='printer_request.php';
		break;
		
		case 'view_purchase' :
		$title="View Purchase";
		$page='view_purchase.php';
		break;
		
		case 'viewDelivery' :
		$title="View Delivery";
		$page='view_delivery.php';
		break;
		
		case 'cumurative' :
		$title="Cumurative Stock";
		$page='cumurative_stock.php';
		break;
		case 'add_item' :
		$title="add_item";
		$page='add_item.php';
		break;
		
	default :
	    $title="Home";
		$page ='addrequest.php';
     }

    require_once("../inc/ontouch.php");
?>



