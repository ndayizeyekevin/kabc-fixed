<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include_once '../../inc/config.php';

$filters = $_GET;

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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Rooms');

$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Room Number');
$sheet->setCellValue('C1', 'Room Class');
$sheet->setCellValue('D1', 'Block');
$sheet->setCellValue('E1', 'Status');
$sheet->setCellValue('F1', 'Capacity');
$sheet->setCellValue('G1', 'Base Price');
$sheet->setCellValue('H1', 'Features');
$sheet->setCellValue('I1', 'Bed Types');

$row = 2;
foreach ($rooms as $room) {
    $sheet->setCellValue("A$row", $room['id']);
    $sheet->setCellValue("B$row", $room['room_number']);
    $sheet->setCellValue("C$row", $room['room_class']);
    $sheet->setCellValue("D$row", $room['block']);
    $sheet->setCellValue("E$row", $room['status']);
    $sheet->setCellValue("F$row", $room['capacity']);
    $sheet->setCellValue("G$row", $room['base_price']);
    $sheet->setCellValue("H$row", $room['features']);
    $sheet->setCellValue("I$row", $room['bed_types']);
    $row++;
}

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="rooms.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
?>
