<?php
// get_available_rooms_per_booking.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $checkin_date = $data['checkin_date'];
    $checkout_date = $data['checkout_date'];
    $num_adults = $data['num_adults'];
    $num_children = empty($data['num_children'])? 0: $data['num_adults'];

    try {
        $query = "
            SELECT 
                r.id,
                r.capacity,
                rc.id AS room_class_id,
                rc.class_name AS room_class,
                rc.base_price,
                COUNT(r.id) AS num_available_rooms,
                rc.base_price,
                GROUP_CONCAT(DISTINCT bt.bed_type_name SEPARATOR ', ') AS bed_types,
                GROUP_CONCAT(DISTINCT CONCAT(bt.bed_type_name, ' (', rcbt.num_beds, ')') SEPARATOR ', ') AS bed_details,
                GROUP_CONCAT(DISTINCT f.feature_name SEPARATOR ', ') AS features,
                fl.floor_number
            FROM 
                tbl_acc_room r
            JOIN 
                tbl_acc_room_class rc ON r.room_class_id = rc.id
            LEFT JOIN 
                tbl_acc_room_class_bed_type rcbt ON rc.id = rcbt.room_class_id
            LEFT JOIN 
                tbl_acc_bed_type bt ON rcbt.bed_type_id = bt.id
            LEFT JOIN 
                tbl_acc_room_class_feature rcf ON rc.id = rcf.room_class_id
            LEFT JOIN 
                tbl_acc_feature f ON rcf.feature_id = f.id
            LEFT JOIN 
                tbl_acc_booking_room br ON r.id = br.room_id
            LEFT JOIN 
                tbl_acc_booking b ON br.booking_id = b.id
                AND (
                    b.checkin_date < :checkout_date
                    AND b.checkout_date > :checkin_date
                )
            LEFT JOIN 
                tbl_acc_room_maintenance rm ON r.id = rm.room_id
                AND rm.date BETWEEN :checkin_date AND :checkout_date
            JOIN 
                tbl_acc_floor fl ON r.floor_id = fl.id
            WHERE 
                b.id IS NULL
                AND rm.id IS NULL
                AND r.status_id IN (3, 12) -- Only rooms that are available or checked out
            GROUP BY 
                rc.id, rc.class_name, rc.base_price
            ORDER BY 
                rc.base_price ASC
        ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':checkin_date', $checkin_date, PDO::PARAM_STR);
        $stmt->bindParam(':checkout_date', $checkout_date, PDO::PARAM_STR);
        $stmt->bindParam(':total_guests', $total_guests, PDO::PARAM_INT);

        $total_guests = $num_adults + $num_children;
        $stmt->execute();

        $available_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => 200, "message" => "Success!", "data" => $available_rooms, "msg_type" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the available rooms.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // $data = json_decode(file_get_contents("php://input"), true);

    $room_class = $_GET['room_class'];
    $checkin_date = $_GET['checkin_date'];
    $checkout_date = $_GET['checkout_date'];
    $total_guests = $_GET['total_guests'];

    try {
        $query = "
        SELECT 
            r.id,
            r.room_number,
            rc.id AS room_class_id,
            rc.class_name AS room_class,
            rc.base_price,
            GROUP_CONCAT(DISTINCT bt.bed_type_name SEPARATOR ', ') AS bed_types,
            GROUP_CONCAT(DISTINCT CONCAT(bt.bed_type_name, ' (', rcbt.num_beds, ')') SEPARATOR ', ') AS bed_details,
            GROUP_CONCAT(DISTINCT f.feature_name SEPARATOR ', ') AS features,
            fl.floor_number,
            rs.status_name
        FROM 
            tbl_acc_room r
        JOIN 
            tbl_acc_room_class rc ON r.room_class_id = rc.id
        JOIN 
            tbl_acc_room_status rs ON r.status_id = rs.id
        LEFT JOIN 
            tbl_acc_room_class_bed_type rcbt ON rc.id = rcbt.room_class_id
        LEFT JOIN 
            tbl_acc_bed_type bt ON rcbt.bed_type_id = bt.id
        LEFT JOIN 
            tbl_acc_room_class_feature rcf ON rc.id = rcf.room_class_id
        LEFT JOIN 
            tbl_acc_feature f ON rcf.feature_id = f.id
        LEFT JOIN 
            tbl_acc_booking_room br ON r.id = br.room_id
        LEFT JOIN 
            tbl_acc_booking b ON br.booking_id = b.id
            AND (b.checkin_date < :checkout_date AND b.checkout_date > :checkin_date)
        LEFT JOIN 
            tbl_acc_room_maintenance rm ON r.id = rm.room_id
            AND rm.date BETWEEN :checkin_date AND :checkout_date
        JOIN 
            tbl_acc_floor fl ON r.floor_id = fl.id
        WHERE
            rm.id IS NULL -- exclude maintenance
            AND rc.id = :room_class
            AND (
                -- 1️⃣ Room is free (no overlapping booking)
                (b.id IS NULL AND r.status_id = 3)

                OR

                -- 2️⃣ Room is occupied/reserved/booked but will be free before the check-in
                (r.status_id IN (1, 2, 13) AND NOT EXISTS (
                    SELECT 1 
                    FROM tbl_acc_booking_room br2
                    JOIN tbl_acc_booking b2 ON br2.booking_id = b2.id
                    WHERE br2.room_id = r.id
                    -- This condition ensures no booking extends into the check-in date
                    AND b2.checkout_date > :checkin_date
                ))
            )
        GROUP BY 
            r.id, 
            r.room_number, 
            rc.id, 
            rc.class_name, 
            rc.base_price, 
            fl.floor_number, 
            rs.status_name
        ORDER BY 
            r.room_number ASC
        ";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':room_class', $room_class, PDO::PARAM_STR);
        $stmt->bindParam(':checkin_date', $checkin_date, PDO::PARAM_STR);
        $stmt->bindParam(':checkout_date', $checkout_date, PDO::PARAM_STR);
        $stmt->bindParam(':total_guests', $total_guests, PDO::PARAM_INT);

        $stmt->execute();

        $available_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => 200, "message" => "Success!", "data" => $available_rooms, "msg_type" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the available rooms.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
