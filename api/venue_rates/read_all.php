<?php
// api/venue-rates/read_all.php

include_once '../../inc/config.php';

header('Content-Type: application/json');

// Prepare SQL query to fetch all venue rates
$query = "SELECT * FROM tbl_ev_venue_rates";
$stmt = $pdo->prepare($query);

// Execute query and fetch results
$stmt->execute();

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
