<?php
include_once '../../inc/config.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=rooms.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('ID', 'Room Number', 'Room Class', 'Block', 'Status', 'Capacity', 'Base Price', 'Features', 'Bed Types'));

$query = "
    SELECT r.id, r.room_number, rc.class_name AS room_class, rb.floor_number AS block, 
           rs.status_name AS status, r.capacity, rc.base_price, 
           GROUP_CONCAT(DISTINCT rf.feature_name SEPARATOR ', ') AS features,
           GROUP_CONCAT(DISTINCT bt.bed_type_name SEPARATOR ', ') AS bed_types
    FROM tbl_acc_room r
    JOIN tbl_acc_room_class rc ON r.room_class_id = rc.id
    JOIN tbl_acc_floor rb ON r.floor_id = rb.id
    JOIN tbl_acc_room_status rs ON r.status_id = rs.id
    LEFT JOIN tbl_acc_feature rf ON r.id = rf.id
    LEFT JOIN tbl_acc_bed_type bt ON r.id = bt.id
    WHERE 1=1";

$filters = $_GET;

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

foreach ($rooms as $room) {
    fputcsv($output, $room);
}

fclose($output);
?>
