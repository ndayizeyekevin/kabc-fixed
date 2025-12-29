<?php 

date_default_timezone_set('Africa/Kigali');
// Disable all error display
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 0);
// ini_set('log_errors', 1);

// Report ALL possible errors and warnings
//  error_reporting(E_ALL | E_STRICT);

// Enable strict types (PHP 7+)
/* declare(strict_types=1); */

//define the core paths
//Define theme as absolute peths to make sure that require_once works as expected

//DIRECTORY_SEPARATOR is a PHP Pre-defined constants:
//(\ for windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS . '');

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT . DS . 'inc');

//load the database configuration first.
require_once("session.php");
require_once("DBController.php");

// $url = getenv('VSDC_BASE_URL') ?? "http://127.0.0.1:8080/rraVsdcSandbox2.1.2.3.7/";    // testing
// $url = "http://127.0.0.1:8080/rraVsdcLatestProdVersion/";   // production
$url = getenv('VSDC_URL');


$msg = '';
$msge = '';
$snp = '';

$logging_ID = $_SESSION['u_id'];
$email = $_SESSION['email'];
$USN = $_SESSION['usn'];
$log_role = $_SESSION['log_role'];

$USER_ID = $_SESSION['user_id'];

if ($_SESSION['log_role'] == 0) {
	$Rname = "Super Admin";
} else {

	$sql_role = $conn->prepare("SELECT * FROM tbl_roles WHERE role_id = '" . $_SESSION['log_role'] . "' ");
	$sql_role->execute();
	while ($fetch_role = $sql_role->fetch()) {

		$Rname = $fetch_role['role_name'];

		// Checking Company

		$stmt = $db->prepare("SELECT * FROM tbl_users INNER JOIN tbl_user_log ON tbl_users.user_id=tbl_user_log.user_id  WHERE tbl_user_log.user_id = ?");
		$stmt->execute(array($_SESSION['user_id']));
		$rows = $stmt->fetch();
		if($rows){
		    
		
		$company = $rows['User_cpnyID'];
		$Oug_UserID = $rows['user_id'];
		$serv_name = $rows['f_name'];

		$stmt = $db->query('SELECT *FROM tbl_company WHERE cpny_ID="' . $company . '" ORDER BY cpny_ID ASC');
		try {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$cpny_ID = $row['cpny_ID'];
				$cmp_sn = $row['short_name'];
				$cmp_full = $row['cpny_name'];
				$cpny_email = $row['cpny_email'];
				$cpny_phone = $row['cpny_phone'];
				$cpny_address = $row['cpny_address'];
				$cpny_notes = $row['cpny_notes'];
				$categ_ID = $row['categ_ID'];
				$cpny_logo = $row['cpny_logo'];
			}
		} catch (PDOException $ex) {
			//Something went wrong rollback!
			echo $ex->getMessage();
		}
		}
	}

	$sql_role = $conn->prepare("SELECT * FROM tbl_roles WHERE role_id = '" . $_SESSION['log_role'] . "' ");
	$sql_role->execute();
	while ($fetch_role = $sql_role->fetch()) {

		$Rname = $fetch_role['role_name'];
	}
}

//  pending for kitchen

$sqll = $db->prepare("SELECT * FROM `tbl_cmd_qty`
    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
    INNER JOIN category ON menu.cat_id=category.cat_id
	INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
	WHERE tbl_cmd_qty.cmd_status = '13' AND category.cat_id != '2'
	GROUP BY tbl_cmd_qty.cmd_table_id");
$sqll->execute();
$tot = $sqll->rowCount();


//  pending for barman

$sqlll = $db->prepare("SELECT * FROM `tbl_cmd_qty`
    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
    INNER JOIN category ON menu.cat_id=category.cat_id
	INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
	WHERE tbl_cmd_qty.cmd_status = '13' AND category.cat_id = '2'
	GROUP BY tbl_cmd_qty.cmd_table_id");
$sqlll->execute();
$tot2 = $sqlll->rowCount();


// processing for kitchen

$sqlll = $db->prepare("SELECT COUNT(DISTINCT cmd_table_id) AS tot FROM `tbl_cmd_qty`
    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
    INNER JOIN category ON menu.cat_id=category.cat_id
    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id=tbl_tables.table_id
    WHERE cmd_status = '7' AND category.cat_id != '2'");
$sqlll->execute();
$rowcount = $sqlll->rowCount();
if ($rowcount > 0) {
	$countss = $sqlll->fetch(PDO::FETCH_ASSOC);
	$tots = $countss['tot'];
} else {
	$tots = "0";
}


// <!--reservation-->

$sqllll = $db->prepare("SELECT COUNT(DISTINCT cmd_table_id) AS tottable FROM `tbl_cmd_qty`
        WHERE cmd_status != '12'");
$sqllll->execute();
$rowcount = $sqllll->rowCount();
if ($rowcount > 0) {
	$countsss = $sqllll->fetch(PDO::FETCH_ASSOC);
	$tot_reserv = $countsss['tottable'];
	$num = $countsss['tottable'];
} else {
	$tot_reserv =  "0";
	$num = "0";
}


$Oug_UserID ??= NULL;


$orderList = $db->prepare("SELECT COUNT(*) FROM `tbl_cmd_qty`
    		INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
    		WHERE tbl_cmd_qty.cmd_status != '12' AND tbl_cmd_qty.Serv_id= '" . $Oug_UserID . "'
    		GROUP BY tbl_cmd_qty.cmd_table_id");
$orderList->execute();
$ordercount = $orderList->rowCount();



$config = $db->prepare("SELECT * from system_configuration");
$config->execute();
$fetch_role = $config->fetch();

$branch_tin = $fetch_role['Tin'];
$mrc = $fetch_role['mrc'];
$system_name = $fetch_role['system_name'];
$branch_phone = $fetch_role['phone'];

