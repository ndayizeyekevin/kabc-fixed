<?php
// handle_reservations.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new reservation
    $venue_id = $input['venue_id'];
    $customer_id = $input['customer_id'];
    $reservation_date = $input['reservation_date'];
    $reservation_end_date = $input['reservation_end_date'];
    $start_time = $input['start_time'];
    $end_time = $input['end_time'];
    $amount = $input['amount'];
    $status = $input['status'];

    // Check for overlapping reservations
    $query = "SELECT COUNT(*) FROM tbl_ev_venue_reservations WHERE venue_id = :venue_id AND reservation_date = :reservation_date AND ((start_time <= :start_time AND end_time > :start_time) OR (start_time < :end_time AND end_time >= :end_time) OR (start_time >= :start_time AND end_time <= :end_time))";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':venue_id', $venue_id, PDO::PARAM_INT);
    $stmt->bindParam(':reservation_date', $reservation_date, PDO::PARAM_STR);
    $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
    $stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
    $stmt->execute();
    $overlapping_reservations = $stmt->fetchColumn();

    if ($overlapping_reservations > 0) {
        echo json_encode(["status" => 409, "message" => "Reservation conflicts with an existing reservation.", "msg_type" => "error"]);
    } else {
        $query = "INSERT INTO tbl_ev_venue_reservations (venue_id, customer_id, reservation_date, reservation_end_date, start_time, end_time, amount, status) VALUES (:venue_id, :customer_id, :reservation_date, :reservation_end_date, :start_time, :end_time, :amount, :status)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':venue_id', $venue_id, PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date', $reservation_date, PDO::PARAM_STR);
        $stmt->bindParam(':reservation_end_date', $reservation_end_date, PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(["status" => 201, "message" => "Reservation created successfully!", "msg_type" => "success"]);
        } else {
            echo json_encode(["status" => 500, "message" => "An error occurred while creating the reservation.", "msg_type" => "error"]);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $conditions = ["r.status NOT IN ('Checkedout')"];
        $params = [];

        if (isset($_GET['from']) && !empty($_GET['from'])) {
            $conditions[] = "r.reservation_date >= :from";
            $params[':from'] = $_GET['from'];
        }

        if (isset($_GET['to']) && !empty($_GET['to'])) {
            $conditions[] = "r.reservation_date <= :to";
            $params[':to'] = $_GET['to'];
        }

        $whereClause = implode(" AND ", $conditions);

        $query = "
            SELECT
                r.id,
                r.reservation_date,
                r.reservation_end_date,
                r.start_time,
                r.end_time,
                r.amount,
                r.status,
                r.venue_id,
                r.customer_id,
                v.venue_name AS venue_name,
                c.names AS customer_name
            FROM tbl_ev_venue_reservations r
            JOIN tbl_ev_venues v ON r.venue_id = v.id
            JOIN tbl_ev_customers c ON r.customer_id = c.id
            WHERE $whereClause
            ORDER BY r.reservation_date DESC
        ";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $reservations_result = !empty($reservations) ? $reservations : [];

        echo json_encode([
            "status" => 200,
            "message" => "Success!",
            "data" => $reservations_result,
            "msg_type" => "success"
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => 500,
            "message" => "Database error: " . $e->getMessage(),
            "msg_type" => "error"
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing reservation
    $id = $input['id'];
    $venue_id = $input['venue_id'];
    $customer_id = $input['customer_id'];
    $reservation_date = $input['reservation_date'];
    $reservation_end_date = $input['reservation_end_date'];
    $start_time = $input['start_time'];
    $end_time = $input['end_time'];
    $amount = $input['amount'];
    $status = $input['status'];
    // Overlap check
    $query = "SELECT COUNT(*) FROM tbl_ev_venue_reservations WHERE venue_id = :venue_id AND reservation_date = :reservation_date AND id != :id AND ((start_time <= :start_time AND end_time > :start_time) OR (start_time < :end_time AND end_time >= :end_time) OR (start_time >= :start_time AND end_time <= :end_time))";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':venue_id', $venue_id, PDO::PARAM_INT);
    $stmt->bindParam(':reservation_date', $reservation_date, PDO::PARAM_STR);
    $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
    $stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $overlapping_reservations = $stmt->fetchColumn();

    if ($overlapping_reservations > 0) {
        echo json_encode(["status" => 409, "message" => "Reservation conflicts with an existing reservation.", "msg_type" => "error"]);
    } else {
        // Perform update
        $updateQuery = "UPDATE tbl_ev_venue_reservations SET venue_id = :venue_id, customer_id = :customer_id, reservation_date = :reservation_date, reservation_end_date = :reservation_end_date, start_time = :start_time, end_time = :end_time, amount = :amount, status = :status WHERE id = :id";
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':venue_id', $venue_id, PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date', $reservation_date, PDO::PARAM_STR);
        $stmt->bindParam(':reservation_end_date', $reservation_end_date, PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(["status" => 200, "message" => "Reservation updated successfully!", "msg_type" => "success"]);
        } else {
            echo json_encode(["status" => 500, "message" => "An error occurred while updating the reservation.", "msg_type" => "error"]);
        }
    }
}
 elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing reservation
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $input['id'];

    // check if pending
    $query = "SELECT status FROM tbl_ev_venue_reservations WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $status = $stmt->fetchColumn();
    
    if ($status == 'Pending') {
        $query = "DELETE FROM tbl_ev_venue_reservations WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["status" => 200, "message" => "Reservation deleted successfully!", "msg_type" => "success"]);
        } else {
            echo json_encode(["status" => 500, "message" => "An error occurred while deleting the reservation.", "msg_type" => "error"]);
        }
    } else {
        echo json_encode(["status" => 400, "message" => "Only Pending Reservations can be deleted", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
