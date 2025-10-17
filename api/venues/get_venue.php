<?php
include_once '../../inc/config.php';

function getVenue($db, $venueId) {
    $query = "
        SELECT v.id, v.venue_name, v.venue_type_id, v.capacity, v.location, v.status_id,
               GROUP_CONCAT(va.amenity_id) AS amenity_ids, 
               GROUP_CONCAT(a.amenity_name) AS amenity_names
        FROM tbl_ev_venues v
        LEFT JOIN tbl_ev_venue_amenities va ON va.venue_id = v.id
        LEFT JOIN tbl_ev_amenities a ON a.amenity_id = va.amenity_id
        WHERE v.id = :venue_id
        GROUP BY v.id
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':venue_id', $venueId);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $amenityIds = explode(",", $row['amenity_ids']);
        $amenityNames = explode(",", $row['amenity_names']);

        // Combine the amenity IDs and names into an array of objects
        $amenities = [];
        foreach ($amenityIds as $index => $amenityId) {
            $amenities[] = [
                'amenity_id' => $amenityId,
                'amenity_name' => $amenityNames[$index],
            ];
        }

        return [
            'status' => 200,
            'data' => [
                'venue_id' => $row['id'],
                'venue_name' => $row['venue_name'],
                'venue_type_id' => $row['venue_type_id'],
                'capacity' => $row['capacity'],
                'location' => $row['location'],
                'status_id' => $row['status_id'],
                'amenities' => $amenities
            ]
        ];
    } else {
        return [
            'status' => 404,
            'message' => 'Venue not found'
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['venue_id'])) {
    $venueId = $_GET['venue_id'];
    echo json_encode(getVenue($db, $venueId));
}
?>
