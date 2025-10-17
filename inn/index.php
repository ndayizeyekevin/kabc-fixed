<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require_once ("../inc/config.php");
if($logging_ID == '' or $USER_ID == ''){
    echo"<script>window.location.replace('../index')</script>";
}

$page='dashboard.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home' :
        $title="Dashboard";
		$page='dashboard.php';
		break;
	case 'addactivity' :
        $title="addactivity";
		$page='addactivity.php';
		break;
		
	case 'department' :
        $title="Department";
		$page='dept.php';
		break;
		
	case 'add_users' :
        $title="Add Users";
		$page='add_user.php';
		break;
		
			case 'menup' :
        $title="Manage Menus";
		$page='menup.php';
		break;
		
		case 'ebm_purchase' :
        $title="EBM purchase";
		$page='cis_purchase.php';
		break;
		
		
			case 'ebm_sale' :
        $title="EBM Sale";
		$page='of_sales.php';
		break;
		
			case 'ebm_customer' :
        $title="EBM Sale";
		$page='saveCustomer.php';
		break;
		
		case 'ebm_stock' :
        $title="EBM Stock";
		$page='saveItem.php';
		break;
		
		
	case 'users' :
        $title="Manage Users";
		$page='user.php';
		break;
		
			case 'suppliers' :
        $title="Manage Suppliers";
		$page='suppliers.php';
		break;

		case 'corporates' :
        $title="Manage Suppliers";
		$page='corporates.php';
		break;
		
		
		case 'StoreRequests' :
        $title="Store Request";
		$page='store_requests.php';
		break;
		
			case 'printStock' :
        $title="Stock Report";
		$page='printStock.php';
		break;
				case 'printBar' :
        $title="Stock Report";
		$page='printBar.php';
		break;
		
			
		case 'viewRequests' :
        $title="Manage Request";
		$page='view_requests.php';
		break;
		
		
	case 'menu_drink' :
        $title="Manage Menus";
		$page='drink.php';
		break;
		
	case 'menu' :
        $title="Manage Menus";
		$page='menu.php';
		break;
		
	case 'menu_combo' :
        $title="Manage Combo";
		$page='combo.php';
		break;
		
	case 'categ' :
        $title="Category";
		$page='category.php';
		break;
		
	case 'subcateg' :
        $title="Sub-Category";
		$page='subcategory.php';
		break;	
		
	case 'table' :
        $title="Manage Table";
		$page='managetable.php';
		break;
		
	case 'room_type' :
        $title="Room Type";
		$page='r_type.php';
		break;
		
	case 'bed_type' :
        $title="Bed Type";
		$page='b_type.php';
		break;
		
	case 'swimming' :
        $title="Swimming";
		$page='swim.php';
		break;
		
	case 'massages' :
        $title="Massage";
		$page='massage.php';
		break;
		
	case 'gyms' :
        $title="Gyms";
		$page='gym.php';
		break;
		
	case 'galleries' :
        $title="Gallery";
		$page='gallery.php';
		break;
		
	case 'view' :
        $title="View All";
		$page='view_all.php';
		break;	
		
	case 'tbl' :
        $title="Manage Tables";
		$page='table.php';
		break;
		
	case 'chk_out' :
        $title="Booking";
		$page='booking.php';
		break;
		
	case 'photos' :
        $title="Main Gallery";
		$page='main_gallery.php';
		break;
		
	case 'view_reservation' :
        $title="View Reservation";
		$page='rpt_reservation.php';
		break;		
		
	case 'new_reservation' :
        $title="Add Reservation";
		$page='add_reservation.php';
		break;	
	
	case 'amenities' :
        $title="View Amenities";
		$page='amen.php';
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
		
			case 'void' :
		$title="void";
		$page='void.php';
		break;
		
		
		case 'waiterReport' :
		$title="Report";
		$page='sales_report_by_employee.php';
		break;
		

    	case 'reportb' :
		$title="Report";
		$page='sales_report.php';
		break;

	case 'reportc' :
		$title="Report";
		$page='stock_report.php';
		break;

	case 'servedReport' :
		$title="Report";
		$page='report_details.php';
		break;
		
	case 'generalreport' :
		$title="General Report";
		$page='general_report.php';
		break;
		
	case 'stock' :
		$title="Our stock";
		$page='allstock.php';
		break;

	case 'stocktake' :
		$title="Stock take";
		$page='recordStock.php';
		break;
		
		case 'import' :
		$title="Importations";
		$page='importlist.php';
		break;
		
	case 'purchase' :
		$title="Purchases";
		$page='purchaselist.php';
		break;
	case 'purchaseDetail' :
		$title="Purchases";
		$page='cis_purchase.php';
		break;
		
	default :
	    $title="Home";
		$page ='dashboard.php';
     }

    require_once("../inc/ontouch.php");
?>


