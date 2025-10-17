<?php
include_once '../../inc/config.php';

function createGuest($db){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email_address = $_POST['email_address'];
    $phone_number = $_POST['phone_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $place_of_birth = $_POST['place_of_birth'];
    $nationality = $_POST['nationality'];
    $residence = $_POST['residence'];
    $profession = $_POST['profession'];
    $identification = $_POST['identification'];
    $passport_number = $_POST['passport_number'];
    $passport_expiration_date = $_POST['passport_expiration_date'];

    // Check if email already exists
    $stmt = $db->prepare("SELECT * FROM tbl_acc_guest WHERE email_address = :email_address");
    $stmt->bindParam(':email_address', $email_address);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => 409, "message" => "Guest with this email already exists", "msg_type" => "error"]);
        return;
    }

    // Insert guest into database
    $stmt = $db->prepare("INSERT INTO tbl_acc_guest (first_name, last_name, email_address, phone_number, date_of_birth, place_of_birth, nationality, residence, profession, identification, passport_number, passport_expiration_date) VALUES (:first_name, :last_name, :email_address, :phone_number, :date_of_birth, :place_of_birth, :nationality, :residence, :profession, :identification, :passport_number, :passport_expiration_date)");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email_address', $email_address);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':place_of_birth', $place_of_birth);
    $stmt->bindParam(':nationality', $nationality);
    $stmt->bindParam(':residence', $residence);
    $stmt->bindParam(':profession', $profession);
    $stmt->bindParam(':identification', $identification);
    $stmt->bindParam(':passport_number', $passport_number);
    $stmt->bindParam(':passport_expiration_date', $passport_expiration_date);

    if($stmt->execute()){
        $result['status'] = 201;
        $result['message'] = "Guest created successfully";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occured while creating the guest.";
        $result['msg_type'] = "error";
    }
    
    echo json_encode($result);    
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    createGuest($db);
}
?>