<?php
include_once '../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $query = "SELECT * FROM tbl_ev_venue_status";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $venue_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($venue_status);
}
