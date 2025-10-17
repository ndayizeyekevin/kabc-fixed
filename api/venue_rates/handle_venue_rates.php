<?php
// handle_venue_rates.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new venue rate
    $venue_id = $input['venue_id'];
    $rate_type = $input['rate_type'];
    $amount = $input['amount'];
    $start_date = $input['start_date'];
    $end_date = $input['end_date'];
    $status = $input['status'];

    $query = "INSERT INTO tbl_ev_venue_rates (venue_id, rate_type, amount, start_date, end_date, status) 
              VALUES (:venue_id, :rate_type, :amount, :start_date, :end_date, :status)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':venue_id', $venue_id, PDO::PARAM_INT);
    $stmt->bindParam(':rate_type', $rate_type, PDO::PARAM_STR);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 201, "message" => "Venue rate created successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while creating the venue rate.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all venue rates
    $query = "
    SELECT vr.id, vr.amount, vr.start_date, vr.end_date, vr.status, vr.venue_id, vr.rate_type, v.venue_name AS venue_name, rt.type_name AS rate_type_name 
    FROM tbl_ev_venue_rates vr JOIN tbl_ev_venues v ON vr.venue_id = v.id 
    JOIN tbl_ev_venue_rate_type rt ON vr.rate_type = rt.id
    ";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();

    if ($result) {
        $venue_rates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $venue_rates_result = !empty($venue_rates) ? $venue_rates : [];
        
        echo json_encode(["status" => 200, "message" => "Success!", "data" => $venue_rates_result, "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the venue rates.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing venue rate
    $id = $input['id'];
    $venue_id = $input['venue_id'];
    $rate_type = $input['rate_type'];
    $amount = $input['amount'];
    $start_date = $input['start_date'];
    $end_date = $input['end_date'];
    $status = $input['status'];

    $query = "UPDATE tbl_ev_venue_rates SET venue_id = :venue_id, rate_type = :rate_type, amount = :amount, start_date = :start_date, end_date = :end_date, status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':venue_id', $venue_id, PDO::PARAM_INT);
    $stmt->bindParam(':rate_type', $rate_type, PDO::PARAM_STR);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Venue rate updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the venue rate.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing venue rate
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $input['id'];

    $query = "DELETE FROM tbl_ev_venue_rates WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Venue rate deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while deleting the venue rate.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
?>
