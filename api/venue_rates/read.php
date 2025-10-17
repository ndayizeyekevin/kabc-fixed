<?php
// api/venue-rates/read.php

include_once '../../inc/config.php';

header('Content-Type: application/json');

// Get venue_id from GET parameters
if (isset($_GET['venue_id'])) {
    $venue_id = $_GET['venue_id'];
} else {
    echo json_encode(['message' => 'venue_id parameter is required']);
    exit;
}

// Prepare SQL query to fetch venue rates
$query = "SELECT * FROM tbl_ev_venue_rates WHERE venue_id = :venue_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':venue_id', $venue_id);

// Execute query and fetch results
$stmt->execute();

// Fetch results
$rates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if rates were found
if ($rates) {
    // Return the results as JSON
    echo json_encode(['rates' => $rates]);
} else {
    // If no rates are found, return an empty array
    echo json_encode(['rates' => []]);
}
?>
