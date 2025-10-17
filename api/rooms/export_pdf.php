<?php
require '../../vendor/autoload.php';

// use FPDF;
require('../../vendor/setasign/fpdf/fpdf.php');

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

// echo json_encode($rooms);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Company Information
$company_name = "Ste Paul";
$company_address = "KN 32 ST Kigali - Nyarugenge";
$company_contact = "Phone: +250 785 285 341 | Email: csaintpaulkgl@gmail.com";

$pdf->Cell(0, 10, $company_name, 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, $company_address, 0, 1, 'C');
$pdf->Cell(0, 5, $company_contact, 0, 1, 'C');
$pdf->Ln(10);

// Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Rooms Report', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);

// Headers
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(20, 10, 'Room No', 1);
$pdf->Cell(25, 10, 'Room Class', 1);
$pdf->Cell(15, 10, 'Block', 1);
$pdf->Cell(20, 10, 'Status', 1);
$pdf->Cell(20, 10, 'Capacity', 1);
$pdf->Cell(20, 10, 'Base Price', 1);
$pdf->Cell(35, 10, 'Features', 1);
$pdf->Cell(35, 10, 'Bed Types', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

// Data
foreach ($rooms as $room) {
    $pdf->Cell(10, 10, $room['id'], 1);
    $pdf->Cell(20, 10, $room['room_number'], 1);
    $pdf->Cell(25, 10, $room['room_class'], 1);
    $pdf->Cell(15, 10, $room['block'], 1);
    $pdf->Cell(20, 10, $room['status'], 1);
    $pdf->Cell(15, 10, $room['capacity'], 1);
    $pdf->Cell(20, 10, $room['base_price'], 1);
    $pdf->Cell(35, 10, $room['features'], 1);
    $pdf->Cell(35, 10, $room['bed_types'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'rooms.pdf');
?>
