<?php
class DAO2{
    
	public function setOff($id){
		try {
            $pdo = new PDO('mysql:host=localhost;dbname=itecrwan_onkey', 'itecrwan_onkey', 'fz+2r}1c^_C#');
            
            $stmt = $pdo->prepare("SELECT *FROM `tbl_company` WHERE `cpny_ID` = '$id'");
			$stmt->execute();
			$fetchStatus = $stmt->fetch();
            
            $status= "OFF";
            if($fetchStatus['cpny_status'] == "ON") {
                $status = "OFF";
            }
            else {
                $status = "ON";
            }
			$stmt2 = $pdo->prepare("UPDATE `tbl_company` set `cpny_status` = '$status' WHERE `cpny_ID` = '$id'");
			$stmt2->execute();

		}
		catch (PDOException $e) {
			echo 'Something Wrong: ' . $e->getMessage();
		}
	}	
	
}
?>