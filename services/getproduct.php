<?php
    require_once ("../inc/config.php");

    $id = $_GET["id"];
    $select = $db->prepare("SELECT * FROM tbl_items
     WHERE item_id = :ppid ");
    $select->bindParam(":ppid", $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);
    $response=$row;
    header('Content-Type: application/json');
    echo json_encode($response);
?>