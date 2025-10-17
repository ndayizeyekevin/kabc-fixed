<?php
include_once '../../inc/config.php';

// Function to update venue details
function updateVenue($db, $data) {
    $venue_id = $data->venue_id;
    $venue_name = $data->venue_name;
    $venue_type = $data->venue_type;
    $capacity = $data->capacity;
    $location = $data->location;
    $status = $data->status;
    $amenities = $data->amenities;

    // Step 1: Update venue details in tbl_ev_venues
    $updateVenueQuery = "UPDATE tbl_ev_venues SET venue_name = :venue_name, venue_type_id = :venue_type, capacity = :capacity, location = :location, status_id = :status WHERE id = :venue_id";
    $stmt = $db->prepare($updateVenueQuery);
    $stmt->bindParam(':venue_name', $venue_name);
    $stmt->bindParam(':venue_type', $venue_type);
    $stmt->bindParam(':capacity', $capacity);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':venue_id', $venue_id);

    if (!$stmt->execute()) {
        return json_encode(["status" => 500, "message" => "Failed to update venue", "msg_type" => "error"]);
    }

    // Step 2: Remove old amenities associated with this venue
    $deleteOldAmenitiesQuery = "DELETE FROM tbl_ev_venue_amenities WHERE venue_id = :venue_id";
    $stmtDelete = $db->prepare($deleteOldAmenitiesQuery);
    $stmtDelete->bindParam(':venue_id', $venue_id);
    if (!$stmtDelete->execute()) {
        return json_encode(["status" => 500, "message" => "Failed to delete old amenities", "msg_type" => "error"]);
    }

    // Step 3: Add new amenities to the venue (check if they exist in the tbl_ev_amenities table)
    foreach ($amenities as $amenity_id) {
        // Check if the amenity_id exists in tbl_ev_amenities
        $checkAmenityQuery = "SELECT * FROM tbl_ev_amenities WHERE amenity_id = :amenity_id";
        $stmtCheck = $db->prepare($checkAmenityQuery);
        $stmtCheck->bindParam(':amenity_id', $amenity_id);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // If the amenity exists, insert it into tbl_ev_venue_amenities
            $insertAmenityQuery = "INSERT INTO tbl_ev_venue_amenities (venue_id, amenity_id) VALUES (:venue_id, :amenity_id)";
            $stmtInsert = $db->prepare($insertAmenityQuery);
            $stmtInsert->bindParam(':venue_id', $venue_id);
            $stmtInsert->bindParam(':amenity_id', $amenity_id);
            if (!$stmtInsert->execute()) {
                return json_encode(["status" => 500, "message" => "Failed to add amenity", "msg_type" => "error"]);
            }
        } else {
            // If the amenity_id doesn't exist, skip it or return an error
            return json_encode(["status" => 400, "message" => "Invalid amenity ID: $amenity_id", "msg_type" => "error"]);
        }
    }

    return json_encode(["status" => 200, "message" => "Venue updated successfully!", "msg_type" => "success"]);
}

// Check if the request method is POST and process the data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    echo updateVenue($db, $data);
}
?>
