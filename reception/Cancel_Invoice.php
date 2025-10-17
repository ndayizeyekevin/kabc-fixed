<?php

ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../inc/DBController.php";

try {
    $db->beginTransaction();

    $code = $_REQUEST['c'];

    $sql = "Delete from `tbl_cmd` WHERE  `OrderCode`='$code'";
    $sql1 = "Delete from `tbl_cmd_qty` WHERE  `cmd_code`='$code'";
    $didq = $db->prepare($sql);
    $didq1 = $db->prepare($sql1);
    $didq->execute();
    $didq1->execute();

    $db->commit();
    header("Location: index");
} catch (PDOException $e) {
    $db->rollBack();
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    header("Location: index");
}

ob_end_flush();
