<?php
require_once "../inc/config.php";
//upload.php

if(isset($_POST['image']))
{
	$data = $_POST['image'];

	$image_array_1 = explode(";", $data);

	$image_array_2 = explode(",", $image_array_1[1]);

	$data = base64_decode($image_array_2[1]);
	
	$image_path = '../logoCpny/'.$cmp_sn.time().'.png';

	$image_name = $cmp_sn.time().'.png';

	file_put_contents($image_path, $data);
	
	try {
           	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE `tbl_company` SET `cpny_logo` = '".$image_name."' WHERE `cpny_ID` = '".$cpny_ID."'";
			$acidq = $db->prepare($sql);
			$acidq->execute();
			
// 			unlink("../logoCpny/".$image_name);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
	echo $image_path;
}

?>