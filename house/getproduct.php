<?php
    require_once ("../inc/config.php");

    $id = $_GET["id"];
    $select = $db->prepare("SELECT * FROM tbl_items 
    INNER JOIN tbl_unit ON tbl_items.item_unit=tbl_unit.unit_id
    INNER JOIN tbl_item_stock ON tbl_items.item_id=tbl_item_stock.item
     WHERE item_id = :ppid ");
    $select->bindParam(":ppid", $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);
    $response=$row;
    header('Content-Type: application/json');
    echo json_encode($response);
?>