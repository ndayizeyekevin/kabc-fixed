<?php
// handle_event_details.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new event detail
    $reservation_id = $input['reservation_id'];
    $event_type = $input['event_type'];
    $guest_count = $input['guest_count'];
    $setup_requirements = $input['setup_requirements'];
    $catering_needs = $input['catering_needs'];
    $special_requests = $input['special_requests'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // check if the venue has capacity to host guest available
    // Fetch venue capacity 
    $query = "SELECT v.capacity FROM tbl_ev_venues v JOIN tbl_ev_venue_reservations r ON v.id = r.venue_id WHERE r.id = :reservation_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
    $stmt->execute();
    $venue = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($guest_count > $venue['capacity']) {
        echo json_encode(["status" => 400, "message" => "The guest count exceeds the venue's capacity of " . $venue['capacity'] . ". Please reduce the guest count.", "msg_type" => "error"]);
    } else {

        $query = "INSERT INTO tbl_ev_event_details (reservation_id, event_type, guest_count, setup_requirements, catering_needs, special_requests, created_at, updated_at) 
              VALUES (:reservation_id, :event_type, :guest_count, :setup_requirements, :catering_needs, :special_requests, :created_at, :updated_at)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
        $stmt->bindParam(':event_type', $event_type, PDO::PARAM_STR);
        $stmt->bindParam(':guest_count', $guest_count, PDO::PARAM_INT);
        $stmt->bindParam(':setup_requirements', $setup_requirements, PDO::PARAM_STR);
        $stmt->bindParam(':catering_needs', $catering_needs, PDO::PARAM_STR);
        $stmt->bindParam(':special_requests', $special_requests, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        $stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(["status" => 201, "message" => "Event detail created successfully!", "msg_type" => "success"]);
        } else {
            echo json_encode(["status" => 500, "message" => "An error occurred while creating the event detail.", "msg_type" => "error"]);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all event details
    if (isset($_GET['reservation_id'])) {
        $reservation_id = $_GET['reservation_id'];
        $query = "
        SELECT ed.*, et.event_name FROM tbl_ev_event_details ed
        JOIN tbl_ev_event_type et ON ed.event_type  = et.id
        WHERE reservation_id = :reservation_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
    } else {
        $query = "SELECT * FROM tbl_ev_event_details";
        $stmt = $db->prepare($query);
    }
    $result = $stmt->execute();

    if ($result) {
        $event_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $event_details_result = !empty($event_details) ? $event_details : [];

        echo json_encode(["status" => 200, "message" => "Success!", "data" => $event_details_result, "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the event details.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing event detail
    $id = $input['id'];
    $event_type = $input['event_type'];
    $guest_count = $input['guest_count'];
    $setup_requirements = $input['setup_requirements'];
    $catering_needs = $input['catering_needs'];
    $special_requests = $input['special_requests'];
    $updated_at = date('Y-m-d H:i:s');

    $query = "UPDATE tbl_ev_event_details SET event_type = :event_type, guest_count = :guest_count, setup_requirements = :setup_requirements, catering_needs = :catering_needs, special_requests = :special_requests, updated_at = :updated_at WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':event_type', $event_type, PDO::PARAM_STR);
    $stmt->bindParam(':guest_count', $guest_count, PDO::PARAM_INT);
    $stmt->bindParam(':setup_requirements', $setup_requirements, PDO::PARAM_STR);
    $stmt->bindParam(':catering_needs', $catering_needs, PDO::PARAM_STR);
    $stmt->bindParam(':special_requests', $special_requests, PDO::PARAM_STR);
    $stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Event detail updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the event detail.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing event detail
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $inpu['id'];

    $query = "DELETE FROM tbl_ev_event_details WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Event detail deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while deleting the event detail.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    // Handle reservation confirmation, cancellation, and fulfillment
    $id = $input['id'];
    $status = $input['status'];

    $query = "UPDATE tbl_ev_venue_reservations SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Reservation status updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the reservation status.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
