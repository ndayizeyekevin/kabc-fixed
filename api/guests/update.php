<?php
include_once '../../inc/config.php';

function updateGuest($db) {
    $guest_id = $_POST['guest_id'];
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

    // Update guest in database
    $stmt = $db->prepare("UPDATE tbl_acc_guest SET first_name = :first_name, last_name = :last_name, email_address = :email_address, phone_number = :phone_number, date_of_birth = :date_of_birth, place_of_birth = :place_of_birth, nationality = :nationality, residence = :residence, profession = :profession, identification = :identification, passport_number = :passport_number, passport_expiration_date = :passport_expiration_date WHERE id = :guest_id");
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
    $stmt->bindParam(':guest_id', $guest_id);
    
    if($stmt->execute()){
        $result['status'] = 200;
        $result['message'] = "Guest updated successfully";
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 500;
        $result['message'] = "An error occured while updating the guest.";
        $result['msg_type'] = "error";
    }
    
    echo json_encode($result);     
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    updateGuest($db);
}
?>