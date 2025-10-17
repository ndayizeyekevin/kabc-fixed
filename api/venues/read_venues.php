<?php
// Connect to the database
include_once '../../inc/config.php';
function getVenues($db) {
    $query = "SELECT v.id, v.venue_name, vt.type_name AS venue_type, v.capacity, GROUP_CONCAT(a.amenity_name SEPARATOR ', ') AS amenities, v.location, s.status_name AS status
              FROM tbl_ev_venues v
              JOIN tbl_ev_venue_types vt ON v.venue_type_id = vt.type_id
              JOIN tbl_ev_venue_status s ON v.status_id = s.status_id
              LEFT JOIN tbl_ev_venue_amenities va ON v.id = va.venue_id
              LEFT JOIN tbl_ev_amenities a ON va.amenity_id = a.amenity_id
              GROUP BY v.id";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($venues) {
        $result['status'] = 200;
        $result['venues'] = $venues;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "No venue found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo getVenues($db);
}
?>
