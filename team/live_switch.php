<?php
include("DAO.php");
include("DAO2.php");

if(isset($_POST["on-id"])){
	$id = $_POST["on-id"];
	$dao = new DAO();
	echo $dao->setOn($id);
}

if(isset($_POST["off-id"])){
	$id = $_POST["off-id"];
	$dao = new DAO2();
	echo $dao->setOff($id);
}

?>