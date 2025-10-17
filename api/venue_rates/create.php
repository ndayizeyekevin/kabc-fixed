<?php
// api/venue-rates/create.php

include_once '../../inc/config.php';
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->venue_id) || empty($data->rate_type) || empty($data->amount) || empty($data->start_date) || empty($data->end_date) || empty($data->status)) {
    echo json_encode(['message' => 'Missing required fields']);
    exit;
}

// Prepare SQL query to insert new rate
$query = "INSERT INTO tbl_ev_venue_rates (venue_id, rate_type, amount, start_date, end_date, status) 
          VALUES (:venue_id, :rate_type, :amount, :start_date, :end_date, :status)";

$stmt = $pdo->prepare($query);

// Bind parameters
$stmt->bindParam(':venue_id', $data->venue_id);
$stmt->bindParam(':rate_type', $data->rate_type);
$stmt->bindParam(':amount', $data->amount);
$stmt->bindParam(':start_date', $data->start_date);
$stmt->bindParam(':end_date', $data->end_date);
$stmt->bindParam(':status', $data->status);

// Execute query and check if insertion is successful
if ($stmt->execute()) {
    echo json_encode(['message' => 'Venue rate created successfully']);
} else {
    echo json_encode(['message' => 'Failed to create venue rate']);
}
?>
