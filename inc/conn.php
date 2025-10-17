<?php
 require_once('DBController.php');
/* <?php */
// Set timezone
date_default_timezone_set('Africa/Kigali');

// die("System Is under maintainance");



// Display all errors (for development only)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

try {
$host = getenv("DB_HOST");        // service name from docker-compose
$user = getenv("DB_USERNAME");           // root user
$pass = getenv("DB_PASSWORD");   // root password
$db1   = getenv("DB_NAME");         // your database name
$port = 3306;             // mariadb container port

	$conn = new mysqli($host, $user, $pass, $db1, $port);


	// Check connection
	if ($conn->connect_error) {
		throw new Exception("Connection failed: " . $conn->connect_error);
	}

	// Set charset to UTF-8
	$conn->set_charset("utf8");

	// MySQLi configuration - execute additional SQL settings
	if (!$conn->query("SET SESSION sql_big_selects=1")) {
		throw new Exception("Error setting sql_big_selects: " . $conn->error);
	}

	if (!$conn->query("SET SESSION sql_mode=''")) {
		throw new Exception("Error setting sql_mode: " . $conn->error);
	}

} catch (Exception $e) {
	die("Fatal error: Connection failed! " . $e->getMessage());
}

/* include 'function.php'; */
/* include 'functions.php'; */
/* include 'rra_functions.php'; */

