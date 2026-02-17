<?php
    require_once ("../inc/config.php");
if ($logging_ID == '' or $USER_ID == '') {
	echo "<script>window.location.replace('../index')</script>";
}

$page = 'dashbaord.php';
$view = (isset($_GET['resto']) && $_GET['resto'] != '') ? $_GET['resto'] : '';

switch ($view) {

	case 'home':
		$title = "Dashboard";
		$page = 'dashbaord.php';
		break;

	case 'ebm' :
        $title="EBM Receipts";
    	$page='ebm.php';
    	break;
	case 'Guestsr':
		$title = "Guests";
		$page = 'Manage_CheckIn_guest.php';
		break;
	case 'gstInvce' :
        $title="Guest Invoice";
    	$page='GetGste_Invoice.php';
    	break;

	case 'allRoom':
		$title = "All Room";
		$page = 'all_rooms.php';
		break;

	case 'venuereports':
		$title = "venuereports";
		$page = 'venuereports.php';
		break;

	case 'all_invoices':
		$title = "All invoices";
		$page = 'get_all_invoice.php';
		break;
case 'progressive_receipt':
		$title = "progressive_receipt";
		$page = 'progressive_receipt.php';
		break;
	case 'groups':
		$title = "groups";
		$page = 'groups.php';
		break;

	case 'group_bookings':
		$title = "group booking";
		$page = 'group_bookings.php';
		break;
	case 'edit_group_booking':
		$title = "Edit Group Booking";
		$page = 'edit_group_booking.php';
		break;

	case 'daily_occupancy_rate':
		$title = "daily_occupancy_rate";
		$page = 'daily_occupancy_rate.php';
		break;



	case 'average_room_rate':
		$title = "average_room_rate";
		$page = 'average_room_rate.php';
		break;




	case 'room_occupancy_index':
		$title = "room_occupancy_index";
		$page = 'room_occupancy_index.php';
		break;



	case 'revpar':
		$title = "revpar";
		$page = 'revpar.php';
		break;




case 'room_inventory':
		$title = "room_inventory";
		$page = 'room_inventory.php';
		break;
case 'venue_inventory':
		$title = "venue_inventory";
		$page = 'venue_inventory.php';
		break;

	case 'guest':
		$title = "Guest";
		$page = 'guest.php';
		break;


	case 'block':
		$title = "room block";
		$page = 'room_block.php';
		break;


	case 'class':
		$title = "Room Class";
		$page = 'room_class.php';
		break;

	case 'roomstatus':
		$title = "Room Status";
		$page = 'room_status.php';
		break;


	case 'bedtype':
		$title = "Bed type";
		$page = 'bed_type.php';
		break;


	case 'room_class_feature':
		$title = "room_class_feature";
		$page = 'room_class_feature.php';
		break;


	case 'room_class_bed_type':
		$title = "room_class_bed_type";
		$page = 'room_class_bed_type.php';
		break;



	case 'venue_checkout':
		$title = "venue_checkout";
		$page = 'venue_checkout.php';
		break;


	case 'Reservation':
		$title = "Reservation";
		$page = 'room_booking_list.php';
		break;


	case 'print_function_sheet':
		$title = "print_function_sheet";
		$page = 'print_function_sheet.php';
		break;


	case 'venue_booking_list':
		$title = "venue_booking_list";
		$page = 'venue_booking_list.php';
		break;

	case 'checkout':
		$title = "checkout";
		$page = 'checkout.php';
		break;



	case 'customers':
		$title = "customers";
		$page = 'customers.php';
		break;



	case 'Booking_list':
		$title = "booking";
		$page = 'today.php';
		break;

	case 'inhouse':
		$title = "In House";
		$page = 'active_booking.php';
		break;

	case 'checkutrepo':
		$title = "Checked out";
		$page = 'check_out_report.php';
		break;


	case 'expected_arrival':
		$title = "expected_arrival";
		$page = 'expected_arrival.php';
		break;


	case 'breakfast':
		$title = "breakfast";
		$page = 'breakfast.php';
		break;


	case 'rent':
		$title = "rent";
		$page = 'rent.php';
		break;


	case 'rooming':
		$title = "rooming";
		$page = 'rooming.php';
		break;


	case 'expected_dep':
		$title = "expected_dep";
		$page = 'expected_dep.php';
		break;


	case 'room_reservations':
		$title = "room_reservations";
		$page = 'room_reservations.php';
		break;



	case 'eventType':
		$title = "eventType";
		$page = 'event_types.php';
		break;



	case 'venueRates':
		$title = "venueRates";
		$page = 'venue_rates.php';
		break;


	case 'venue_list':
		$title = "venue list";
		$page = 'venue_list.php';
		break;

	case 'room_booking_details':
		$title = "room_booking_details";
		$page = 'room_booking_details.php';
		break;

	case 'checkin':
		$title = "checkin";
		$page = 'checkin.php';
		break;


	case 'edit':
		$title = "edit";
		$page = 'edit.php';
		break;

	case 'moving':
		$title = "moving";
		$page = 'move_booking.php';
		break;

	case 'currencyIssue' :
		$title="Manage Currencies";
		$page='currency.php';
		break;

	case 'split_invoice':
		$title = "split_invoice";
		$page = 'split_payment.php';
		break;

	case 'credit_account':
		$title = "credit_account";
		$page = 'credit_account.php';
		break;

	case 'corporate':
		$title = "Corporate Clients";
		$page = 'credit_account.php';
		break;

	case 'addtogroup':
		$title = "addtogroup";
		$page = 'add_to_group.php';
		break;

	case 'refund_page':
		$title = "Process Refund";
		$page = 'refund_page.php';
		break;

	// Venue Refund
	case 'venue_refund':
		$title = "Process Refund";
		$page = 'venue_refund.php';
		break;

	// Internal Request
	case 'request':
		$title = 'Internal Request';
		$page = 'request.php';
		break;
	
	case 'printInternal':
		$title = 'Print Internal Request';
		$page = 'print_internal.php';
		break;


	default:
		$title = "Home";
		$page = 'dashbaord.php';
		break;
}

require_once("../inc/ontouch.php");




