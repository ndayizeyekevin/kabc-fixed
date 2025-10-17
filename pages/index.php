<?php
require_once("../inc/session.php");
require_once("../inc/DBController.php");
$content = 'dashboard.php';
$view = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : '';

switch ($view) {

    case 'home':
        $title = "Home";
        $content = 'content.php';
        break;

    case 'login':
        $title = "Login";
        $content = 'UserLogin.php';
        break;
    case 'forgotPassword':
        $title = "forgotPassword";
        $content = 'forgotPassword.php';
        break;


        // 	case 'accomodations' :
        //         $title="Accomodation";
        // 		$content='accomodations.php';
        // 		break;

        // 	case 'accomodation' :
        //         $title="Accomodation";
        // 		$content='all_rooms.php';
        // 		break;

        // 	case 'details' :
        //         $title="Details";
        // 		$content='cpny_details.php';
        // 		break;

        // 	case 'yr_cart' :
        //         $title="Cart";
        // 		$content='booking/cart.php';
        // 		break;

        // 	case 'user_info' :
        //         $title="Info";
        // 		$content='booking/info.php';
        // 		break;

        // 	case 'letter' :
        //         $title="Application-Letter";
        // 		$content='booking/application_letter.php';
        // 		break;

        // 	case 'available' :
        //         $title="Details";
        // 		$content='availability.php';
        // 		break;

        //   case 'reserve' :
        //         $title="Reserve Details";
        // 		$content='reservation/cpny_reserve_details.php';
        // 		break;

        //   case 'authenticate' :
        //         $title="Auntentication";
        // 		$content='reservation/authentication.php';
        // 		break;

        //   case 'cpny' :
        //         $title="Category";
        // 		$content='cpny_cat.php';
        // 		break;

        // 	case 'contact' :
        //         $title="Contact Us";
        // 		$content='contact_us.php';
        // 		break;

        // 	default :
        // 	    $title="Home";
        // 		$content ='content.php';
}

require_once("controller/pin.php");
?>

