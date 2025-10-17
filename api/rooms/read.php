<?php
include_once '../../inc/config.php';

function getRoom($db)
{
    $room_id = $_GET['room_id'];

    // Get room from database
    $stmt = $db->prepare("SELECT * FROM tbl_acc_room WHERE id = :room_id");
    $stmt->bindParam(':room_id', $room_id);
    $stmt->execute();
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room) {
        $result['status'] = 200;
        $result['room'] = $room;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "Room not found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

function getRooms($db, $filters = [])
{
    $query = "
        SELECT r.id, r.room_number, r.floor_id, r.room_class_id, r.status_id, rc.class_name AS room_class_name, rb.floor_number AS block_name, 
               rs.status_name AS status_name, r.capacity, rc.base_price, rcbt.num_beds,
               GROUP_CONCAT(DISTINCT rf.feature_name SEPARATOR ', ') AS features,
               GROUP_CONCAT(DISTINCT bt.bed_type_name SEPARATOR ', ') AS bed_types
        FROM tbl_acc_room r
        JOIN tbl_acc_room_class rc ON r.room_class_id = rc.id
        JOIN tbl_acc_floor rb ON r.floor_id = rb.id
        JOIN tbl_acc_room_status rs ON r.status_id = rs.id
        LEFT JOIN tbl_acc_feature rf ON r.id = rf.id
        LEFT JOIN tbl_acc_bed_type bt ON r.id = bt.id
        LEFT JOIN tbl_acc_room_class_bed_type rcbt ON rcbt.room_class_id = rc.id
        WHERE 1=1";

    // Apply filters
    if (!empty($filters['room_number'])) {
        $query .= " AND r.room_number LIKE :room_number";
    }
    if (!empty($filters['room_class_id'])) {
        $query .= " AND r.room_class_id = :room_class_id";
    }
    if (!empty($filters['block_id'])) {
        $query .= " AND r.floor_id = :block_id";
    }
    if (!empty($filters['status_id'])) {
        $query .= " AND r.status_id = :status_id";
    }
    if (!empty($filters['capacity'])) {
        $query .= " AND r.capacity = :capacity";
    }
    if (!empty($filters['price_min'])) {
        $query .= " AND rc.base_price >= :price_min";
    }
    if (!empty($filters['price_max'])) {
        $query .= " AND rc.base_price <= :price_max";
    }

    $query .= " GROUP BY r.id";
    $stmt = $db->prepare($query);

    // Bind filter values
    if (!empty($filters['room_number'])) {
        $stmt->bindValue(':room_number', '%' . $filters['room_number'] . '%');
    }
    if (!empty($filters['room_class_id'])) {
        $stmt->bindValue(':room_class_id', $filters['room_class_id']);
    }
    if (!empty($filters['block_id'])) {
        $stmt->bindValue(':block_id', $filters['block_id']);
    }
    if (!empty($filters['status_id'])) {
        $stmt->bindValue(':status_id', $filters['status_id']);
    }
    if (!empty($filters['capacity'])) {
        $stmt->bindValue(':capacity', $filters['capacity']);
    }
    if (!empty($filters['price_min'])) {
        $stmt->bindValue(':price_min', $filters['price_min']);
    }
    if (!empty($filters['price_max'])) {
        $stmt->bindValue(':price_max', $filters['price_max']);
    }

    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rooms) {
        $result['status'] = 200;
        $result['rooms'] = $rooms;
        $result['msg_type'] = "success";
    } else {
        $result['status'] = 404;
        $result['message'] = "No room found.";
        $result['msg_type'] = "error";
    }

    echo json_encode($result);
}

// Call the function if GET request is made
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $filters = $_GET;
    if (isset($filters['room_id'])) {
        getRoom($db);
    } else {
        getRooms($db, $filters);
    }
}
?>
