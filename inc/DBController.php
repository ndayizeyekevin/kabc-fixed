<?php
// Set timezone
date_default_timezone_set('Africa/Kigali');

// Display all errors (for development only)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

try {

$host = getenv("DB_HOST");        // service name from docker-compose
$user = getenv("DB_USERNAME");           // root user
$pass = getenv("DB_PASSWORD");   // root password
$db1   = getenv("DB_NAME");

$db = new PDO(
    "mysql:host=$host;port=3306;dbname=$db1;charset=utf8mb4",
    $user,
    $pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);	$conn = $db;
} catch (pdoexception $e) {
	die("fatal error: connection failed! " . $e->getmessage());
}

$db;
$conn;

include 'function.php';
include 'functions.php';
include 'rra_functions.php';
