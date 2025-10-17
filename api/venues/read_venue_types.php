<?php
include_once '../../inc/config.php';

$query = "SELECT * FROM tbl_ev_venue_types";
$stmt = $db->prepare($query);
$stmt->execute();
$venue_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($venue_types);
?>
