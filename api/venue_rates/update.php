<?php
// api/venue-rates/update.php

include_once '../../config/database.php';
header('Content-Type: application/json');

// Get PUT data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->id) || empty($data->rate_type) || empty($data->amount) || empty($data->start_date) || empty($data->end_date) || empty($data->status)) {
    echo json_encode(['message' => 'Missing required fields']);
    exit;
}

// Prepare SQL query to update venue rate
$query = "UPDATE tbl_ev_venue_rates 
          SET rate_type = :rate_type, amount = :amount, start_date = :start_date, end_date = :end_date, status = :status 
          WHERE id = :id";

$stmt = $pdo->prepare($query);

// Bind parameters
$stmt->bindParam(':id', $data->id);
$stmt->bindParam(':rate_type', $data->rate_type);
$stmt->bindParam(':amount', $data->amount);
$stmt->bindParam(':start_date', $data->start_date);
$stmt->bindParam(':end_date', $data->end_date);
$stmt->bindParam(':status', $data->status);

// Execute query and check if the update was successful
if ($stmt->execute()) {
    echo json_encode(['message' => 'Venue rate updated successfully']);
} else {
    echo json_encode(['message' => 'Failed to update venue rate']);
}
?>
