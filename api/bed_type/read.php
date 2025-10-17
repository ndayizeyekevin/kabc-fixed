<?php
include_once '../../inc/config.php';

function getBedType($db) {
    $bed_type_id = $_GET['bed_type_id'];

    // Get bed type from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_bed_type WHERE id = :bed_type_id");
    $stmt->bindParam(':bed_type_id', $bed_type_id);
    $stmt->execute();
    $bed_type = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bed_type) {
        $result['status'] = 200;
        $result['bed_type'] = $bed_type;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Bed type not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getBedTypes($db) {
    // Get bed type from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_bed_type");
    $stmt->execute();
    $bed_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($bed_types) {
        $result['status'] = 200;
        $result['bed_types'] = $bed_types;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Bed type not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_POST['bed_type_id'])) {
        getBedType($db);
    } else {
        getBedTypes($db);
    }
}
?>
