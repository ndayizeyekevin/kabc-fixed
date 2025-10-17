<?php
include_once '../../inc/config.php';

// Function to delete venue
function deleteVenue($db, $venue_id) {
    // First, delete the venue amenities (because of the foreign key constraint)
    $deleteAmenitiesQuery = "DELETE FROM tbl_ev_venue_amenities WHERE venue_id = :venue_id";
    $stmt = $db->prepare($deleteAmenitiesQuery);
    $stmt->bindParam(':venue_id', $venue_id);
    $stmt->execute();

    // Now, delete the venue
    $deleteVenueQuery = "DELETE FROM tbl_ev_venues WHERE id = :venue_id";
    $stmt = $db->prepare($deleteVenueQuery);
    $stmt->bindParam(':venue_id', $venue_id);
    
    if ($stmt->execute()) {
        return json_encode(["status" => 200, "message" => "Venue deleted successfully!", "msg_type" => "success"]);
    } else {
        return json_encode(["status" => 500, "message" => "Failed to delete the venue.", "msg_type" => "error"]);
    }
}

// Check if the request method is DELETE and process the data
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));
    $venue_id = $data->venue_id; // Get the venue_id from the request data
    echo deleteVenue($db, $venue_id);
}
?>
