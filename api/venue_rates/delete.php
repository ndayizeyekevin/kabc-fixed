<?php
// api/venue-rates/delete.php

include_once '../../inc/config.php';
header('Content-Type: application/json');

// Get DELETE data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    echo json_encode(['message' => 'Rate ID is required']);
    exit;
}

// Prepare SQL query to delete venue rate
$query = "DELETE FROM tbl_ev_venue_rates WHERE id = :id";

$stmt = $pdo->prepare($query);

// Bind parameters
$stmt->bindParam(':id', $data->id);

// Execute query and check if deletion was successful
if ($stmt->execute()) {
    echo json_encode(['message' => 'Venue rate deleted successfully']);
} else {
    echo json_encode(['message' => 'Failed to delete venue rate']);
}
?>
