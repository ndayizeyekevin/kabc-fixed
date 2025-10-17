<?php
require_once "../inc/config.php";
//upload.php

if(isset($_POST['image']))
{
	$data = $_POST['image'];
	$room = $_POST['room'];

	$image_array_1 = explode(";", $data);

	$image_array_2 = explode(",", $image_array_1[1]);

	$data = base64_decode($image_array_2[1]);
	
	$ima_path = '../room_gallery/'.$cmp_sn.time().'.png';

	$image_name = $cmp_sn.time().'.png';

	file_put_contents($ima_path, $data);
	
	try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_room_gallery` (`img_name`,`room_id`,`rm_gallery_cpny_id`,`img_status`)
            VALUES ('$image_name','$room','$cpny_ID',1)";
            $db->exec($sql);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
	echo $ima_path;
}

?>