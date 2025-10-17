<?php

		ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../inc/DBController.php";
$invoice = $_GET['i'];


        $stmt_r = $db->prepare("SELECT receipt_url FROM `receipts` WHERE receiptNo = $invoice LIMIT 1");
        $stmt_r->execute();
        $row_r = $stmt_r->fetch();
        $url = $row_r['receipt_url'];

echo $url;

header("Location: receipt/normal.php?$url");
