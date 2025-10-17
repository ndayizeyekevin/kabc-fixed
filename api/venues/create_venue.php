<?php
include_once '../../inc/config.php';

function createVenue($db, $data) {
    $venue_name = $data->venue_name;
    $venue_type = $data->venue_type;
    $capacity = $data->capacity;
    $amenities = $data->amenities; // assuming it's an array
    $location = $data->location;
    $status = $data->status;

    // Check if venue already exists
    $checkQuery = "SELECT * FROM tbl_ev_venues WHERE venue_name = :venue_name";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':venue_name', $venue_name);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        return json_encode(["status" => 409, "message" => "Venue already exists", "msg_type" => "error"]);
    }

    // Insert venue into venues table
    $query = "INSERT INTO tbl_ev_venues (venue_name, venue_type_id, capacity, location, status_id) 
              VALUES (:venue_name, :venue_type, :capacity, :location, :status)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':venue_name', $venue_name);
    $stmt->bindParam(':venue_type', $venue_type);
    $stmt->bindParam(':capacity', $capacity);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        $venue_id = $db->lastInsertId();

        // Insert amenities into venue_amenities table
        foreach ($amenities as $amenity) {
            $amenityQuery = "INSERT INTO tbl_ev_venue_amenities (venue_id, amenity_id) VALUES (:venue_id, :amenity)";
            $amenityStmt = $db->prepare($amenityQuery);
            $amenityStmt->bindParam(':venue_id', $venue_id);
            $amenityStmt->bindParam(':amenity', $amenity);
            $amenityStmt->execute();
        }

        $result = [
            "status" => 201,
            "message" => "Venue created successfully!",
            "msg_type" => "success"
        ];
    } else {
        $result = [
            "status" => 500,
            "message" => "An error occurred while creating the venue.",
            "msg_type" => "error"
        ];
    }

    return json_encode($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    echo createVenue($db, $data);
}
?>
