<?php
// handle_room_bookings.php
include_once '../../inc/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     // Create a new room booking
//     // var_dump(json_encode($input));
//     try {
//         $booking_type = $input['booking_type'];
//         $guest_type = $input['guest_type'];
//         $notify_guest = $input['notify_guest'];
//         $guest_info = $input['guest_info'];
//         $checkin_date = $input['checkin_date'];
//         $checkout_date = $input['checkout_date'];
//         $num_adults = $input['num_adults'];
//         $num_children = $input['num_children'];
//         $coming_from = $input['coming_from'];
//         $going_to = $input['going_to'];
//         $booked_from = $input['booked_from'];
//         $company = $input['company'];
//         $booking_status_id = $input['booking_status'];
//         $duration = $input['duration'];
//         $room_id = $input['room_id'];
//         $room_option = $input['room_option'];
//         $room_price = $input['room_price'];
//         $vat_amount = $input['vat_amount'];
//         $total_booking_amount = $input['total_booking_amount'];

//         // Validate required fields
//         if (empty($guest_info['first_name']) || empty($guest_info['last_name']) || empty($guest_info['phone']) || empty($guest_info['nationality']) || empty($guest_info['email']) || empty($guest_info['residence']) || empty($guest_info['address'])) {
//             echo json_encode(["status" => 400, "message" => "Please fill in all required fields.", "msg_type" => "error"]);
//             exit;
//         }

//         // Insert or update guest information
//         if (!empty($guest_info['guest_id'])) {
//             $guest_id = $guest_info['guest_id'];
//         } else {
//             $query = "INSERT INTO tbl_acc_guest (first_name, last_name, date_of_birth, place_of_birth, phone_number, nationality, email_address, residence, address, profession, passport_number, passport_expiration_date, identification)
//                       VALUES (:first_name, :last_name, :date_of_birth, :place_of_birth, :phone_number, :nationality, :email_address, :residence, :address, :profession, :passport_number, :passport_expiration_date, :identification)";
//             $stmt = $db->prepare($query);
//             $stmt->bindParam(':first_name', $guest_info['first_name']);
//             $stmt->bindParam(':last_name', $guest_info['last_name']);
//             $stmt->bindParam(':date_of_birth', $guest_info['date_of_birth']);
//             $stmt->bindParam(':place_of_birth', $guest_info['place_of_birth']);
//             $stmt->bindParam(':phone_number', $guest_info['phone']);
//             $stmt->bindParam(':nationality', $guest_info['nationality']);
//             $stmt->bindParam(':email_address', $guest_info['email']);
//             $stmt->bindParam(':residence', $guest_info['residence']);
//             $stmt->bindParam(':address', $guest_info['address']);
//             $stmt->bindParam(':profession', $guest_info['profession']);
//             $stmt->bindParam(':passport_number', $guest_info['passport_number']);
//             $stmt->bindParam(':passport_expiration_date', $guest_info['passport_expiration_date']);
//             $stmt->bindParam(':identification', $guest_info['identification']);
//             if ($stmt->execute()) {
//                 $guest_id = $db->lastInsertId();
//             } else {
//                 echo json_encode(["status" => 500, "message" => "An error occurred while saving the guest information.", "msg_type" => "error"]);
//                 exit;
//             }
//         }

//         // Insert booking information
//         $query = "INSERT INTO tbl_acc_booking (guest_id, booking_type, guest_type, checkin_date, checkout_date, duration, num_adults, num_children, coming_from, going_to, notify_guest, room_price, vat_amount, booking_amount, payment_status_id, mode_of_payment_id, booked_from, company, booking_status_id, booking_comment)
//                   VALUES (:guest_id, :booking_type, :guest_type, :checkin_date, :checkout_date, :duration, :num_adults, :num_children, :coming_from, :going_to, :notify_guest, :room_price, :vat_amount, :booking_amount, :payment_status_id, :mode_of_payment_id, :booked_from, :company, :booking_status_id, :booking_comment)";
//         $stmt = $db->prepare($query);
//         $stmt->bindParam(':guest_id', $guest_id);
//         $stmt->bindParam(':booking_type', $booking_type);
//         $stmt->bindParam(':guest_type', $guest_type);
//         $stmt->bindParam(':checkin_date', $checkin_date);
//         $stmt->bindParam(':checkout_date', $checkout_date);
//         $stmt->bindParam(':duration', $duration);
//         $stmt->bindParam(':num_adults', $num_adults);
//         $stmt->bindParam(':num_children', $num_children);
//         $stmt->bindParam(':coming_from', $coming_from);
//         $stmt->bindParam(':going_to', $going_to);
//         $stmt->bindParam(':notify_guest', $notify_guest);
//         $stmt->bindParam(':room_price', $room_price);
//         $stmt->bindParam(':vat_amount', $vat_amount);
//         $stmt->bindParam(':booking_amount', $total_booking_amount);
//         $stmt->bindParam(':payment_status_id', $payment_status_id); // or null
//         $stmt->bindParam(':mode_of_payment_id', $mode_of_payment_id); // or null
//         $stmt->bindParam(':booked_from', $booked_from);
//         $stmt->bindParam(':company', $company);
//         $stmt->bindParam(':booking_status_id', $booking_status_id);
//         $stmt->bindParam(':booking_comment', $guest_info['booking_comment']);

//         if ($stmt->execute()) {
//             $booking_id = $db->lastInsertId();

//             // Insert into tbl_acc_booking_room
//             $query = "INSERT INTO tbl_acc_booking_room (booking_id, room_id) VALUES (:booking_id, :room_id)";
//             $stmt = $db->prepare($query);
//             $stmt->bindParam(':booking_id', $booking_id);
//             $stmt->bindParam(':room_id', $room_id);
//             if (!$stmt->execute()) {
//                 echo json_encode(["status" => 500, "message" => "An error occurred while saving the booking room information.", "msg_type" => "error"]);
//                 exit;
//             }

//             // Insert room options
//             $query = "INSERT INTO tbl_acc_booking_room_options (booking_id, option_id) VALUES (:booking_id, :option_id)";
//             $stmt = $db->prepare($query);
//             $stmt->bindParam(':booking_id', $booking_id);
//             $stmt->bindParam(':option_id', $room_option);
//             if (!$stmt->execute()) {
//                 echo json_encode(["status" => 500, "message" => "An error occurred while saving the booking room options information.", "msg_type" => "error"]);
//                 exit;
//             }

//             // Update room status to "Reserved"
//             $query = "UPDATE tbl_acc_room SET status_id = 2 WHERE id = :room_id"; // 2 = Reserved (currently in the table tbl_acc_room_status)
//             $stmt = $db->prepare($query);
//             $stmt->bindParam(':room_id', $room_id);
//             if (!$stmt->execute()) {
//                 echo json_encode(["status" => 500, "message" => "An error occurred while updating the room status.", "msg_type" => "error"]);
//                 exit;
//             }

//             echo json_encode(["status" => 201, "message" => "Booking confirmed successfully!", "msg_type" => "success"]);
//         } else {
//             echo json_encode(["status" => 500, "message" => "An error occurred while confirming the booking.", "msg_type" => "error"]);
//         }
//     } catch (Exception $e) {
//         echo json_encode(["status" => 500, "message" => "An error occurred: " . $e->getMessage(), "msg_type" => "error"]);
//     }
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Extract and validate input
        $booking_type = $input['booking_type'];
        $guest_type = $input['guest_type'];
        $notify_guest = $input['notify_guest'];
        $guest_info = $input['guest_info'];
        $checkin_date = $input['checkin_date'];
        $checkout_date = $input['checkout_date'];
        $num_adults = $input['num_adults'];
        $num_children = $input['num_children'];
        $coming_from = $input['coming_from'];
        $going_to = $input['going_to'];
        $booked_from = $input['booked_from'];
        $company = $input['company'];
        $booking_status_id = $input['booking_status'];
        $duration = $input['duration'];
        $room_id = $input['room_id'];
        $room_option = $input['room_option'];
        $room_price = $input['room_price'];
        $vat_amount = $input['vat_amount'];
        $total_booking_amount = $input['total_booking_amount'];

        if (
            empty($guest_info['first_name']) || empty($guest_info['last_name']) || empty($guest_info['phone']) ||
            empty($guest_info['nationality']) ||
            empty($guest_info['residence']) || empty($guest_info['address'])
        ) {
            echo json_encode(["status" => 400, "message" => "Please fill in all required fields.", "msg_type" => "error"]);
            exit;
        }

        // Insert or reuse guest
        if (!empty($guest_info['guest_id'])) {
            $guest_id = $guest_info['guest_id'];
        } else {
            if ($guest_info['passport_expiration_date'] === '') {
                $guest_info['passport_expiration_date'] = NULL;
            }

            if ($guest_info['passport_number'] === '') {
                $guest_info['passport_number'] = NULL;
            }

            if ($guest_info['identification'] === '') {
                $guest_info['identification'] = NULL;
            }

            // Handle empty email by setting it to NULL to allow duplicates
            if (empty($guest_info['email']) || $guest_info['email'] === '') {
                $guest_info['email'] = NULL;
            }

            $query = "INSERT INTO tbl_acc_guest (first_name, last_name, date_of_birth, place_of_birth, phone_number, nationality, email_address, residence, address, profession, passport_number, passport_expiration_date, identification)
                      VALUES (:first_name, :last_name, :date_of_birth, :place_of_birth, :phone_number, :nationality, :email_address, :residence, :address, :profession, :passport_number, :passport_expiration_date, :identification)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':first_name', $guest_info['first_name']);
            $stmt->bindParam(':last_name', $guest_info['last_name']);
            $stmt->bindParam(':date_of_birth', $guest_info['date_of_birth']);
            $stmt->bindParam(':place_of_birth', $guest_info['place_of_birth']);
            $stmt->bindParam(':phone_number', $guest_info['phone']);
            $stmt->bindParam(':nationality', $guest_info['nationality']);
            $stmt->bindParam(':email_address', $guest_info['email']);
            $stmt->bindParam(':residence', $guest_info['residence']);
            $stmt->bindParam(':address', $guest_info['address']);
            $stmt->bindParam(':profession', $guest_info['profession']);
            $stmt->bindParam(':passport_number', $guest_info['passport_number']);
            $stmt->bindParam(':passport_expiration_date', $guest_info['passport_expiration_date']);
            $stmt->bindParam(':identification', $guest_info['identification']);
            if ($stmt->execute()) {
                $guest_id = $db->lastInsertId();
            } else {
                echo json_encode(["status" => 500, "message" => "An error occurred while saving the guest information.", "msg_type" => "error"]);
                exit;
            }
        }

        // Insert booking
        $query = "INSERT INTO tbl_acc_booking (guest_id, booking_type, guest_type, checkin_date, checkout_date, duration, num_adults, num_children, coming_from, going_to, notify_guest, room_price, vat_amount, booking_amount, payment_status_id, mode_of_payment_id, booked_from, company, booking_status_id, booking_comment)
                  VALUES (:guest_id, :booking_type, :guest_type, :checkin_date, :checkout_date, :duration, :num_adults, :num_children, :coming_from, :going_to, :notify_guest, :room_price, :vat_amount, :booking_amount, :payment_status_id, :mode_of_payment_id, :booked_from, :company, :booking_status_id, :booking_comment)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':guest_id', $guest_id);
        $stmt->bindParam(':booking_type', $booking_type);
        $stmt->bindParam(':guest_type', $guest_type);
        $stmt->bindParam(':checkin_date', $checkin_date);
        $stmt->bindParam(':checkout_date', $checkout_date);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':num_adults', $num_adults);
        $stmt->bindParam(':num_children', $num_children);
        $stmt->bindParam(':coming_from', $coming_from);
        $stmt->bindParam(':going_to', $going_to);
        $stmt->bindParam(':notify_guest', $notify_guest);
        $stmt->bindParam(':room_price', $room_price);
        $stmt->bindParam(':vat_amount', $vat_amount);
        $stmt->bindParam(':booking_amount', $total_booking_amount);
        $stmt->bindParam(':payment_status_id', $payment_status_id);
        $stmt->bindParam(':mode_of_payment_id', $mode_of_payment_id);
        $stmt->bindParam(':booked_from', $booked_from);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':booking_status_id', $booking_status_id);
        $stmt->bindParam(':booking_comment', $guest_info['booking_comment']);

        if ($stmt->execute()) {
            $booking_id = $db->lastInsertId();

            // Check before inserting into tbl_acc_booking_room
            $stmt = $db->prepare("SELECT COUNT(*) FROM tbl_acc_booking_room WHERE booking_id = :booking_id AND room_id = :room_id");
            $stmt->execute([':booking_id' => $booking_id, ':room_id' => $room_id]);
            if ($stmt->fetchColumn() == 0) {
                $query = "INSERT INTO tbl_acc_booking_room (booking_id, room_id) VALUES (:booking_id, :room_id)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':booking_id', $booking_id);
                $stmt->bindParam(':room_id', $room_id);
                if (!$stmt->execute()) {
                    echo json_encode(["status" => 500, "message" => "An error occurred while saving the booking room information.", "msg_type" => "error"]);
                    exit;
                }
            }

            // Check before inserting room options
            $stmt = $db->prepare("SELECT COUNT(*) FROM tbl_acc_booking_room_options WHERE booking_id = :booking_id AND option_id = :option_id");
            $stmt->execute([':booking_id' => $booking_id, ':option_id' => $room_option]);
            if ($stmt->fetchColumn() == 0) {
                $query = "INSERT INTO tbl_acc_booking_room_options (booking_id, option_id) VALUES (:booking_id, :option_id)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':booking_id', $booking_id);
                $stmt->bindParam(':option_id', $room_option);
                if (!$stmt->execute()) {
                    echo json_encode(["status" => 500, "message" => "An error occurred while saving the booking room options information.", "msg_type" => "error"]);
                    exit;
                }
            }

            // Update room status to Reserved (status_id = 2)
            $query = "UPDATE tbl_acc_room SET status_id = 2 WHERE id = :room_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':room_id', $room_id);
            if (!$stmt->execute()) {
                echo json_encode(["status" => 500, "message" => "An error occurred while updating the room status.", "msg_type" => "error"]);
                exit;
            }

            echo json_encode(["status" => 201, "message" => "Booking confirmed successfully!", "msg_type" => "success"]);
        } else {
            echo json_encode(["status" => 500, "message" => "An error occurred while confirming the booking.", "msg_type" => "error"]);
        }

    } catch (Exception $e) {
        echo json_encode(["status" => 500, "message" => "An error occurred: " . $e->getMessage(), "msg_type" => "error"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Read all room bookings
    try {
        // check if bookingId is available and inititialize it
        $bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : null;

        // Define the query
        $query = "
        SELECT
            b.id AS booking_id,
            b.booking_type,
            b.guest_type,
            b.checkin_date,
            b.checkout_date,
            b.duration,
            b.num_adults,
            b.num_children,
            b.coming_from,
            b.going_to,
            b.notify_guest,
            b.room_price,
            b.vat_amount,
            b.booking_amount,
            b.payment_status_id,
            b.mode_of_payment_id,
            p.payment_status_name,
            b.booked_from,
            b.company,
            b.booking_status_id,
            b.day_closed_at,
            bs.status_name AS booking_status_name,
            b.booking_comment,
            g.id AS guest_id,
            g.first_name,
            g.last_name,
            g.email_address,
            g.phone_number,
            g.date_of_birth,
            g.place_of_birth,
            g.nationality,
            g.residence,
            g.address,
            g.profession,
            g.identification,
            g.passport_number,
            g.passport_expiration_date,
            r.id AS room_id,
            r.room_number,
            r.capacity,
            rc.class_name,
            rs.status_name AS room_status,
            ro.option_code AS room_option_name
        FROM
            tbl_acc_booking b
        LEFT JOIN
            tbl_acc_payment_status p ON b.payment_status_id = p.id
        JOIN
            tbl_acc_booking_status bs ON b.booking_status_id = bs.id
        JOIN
            tbl_acc_guest g ON b.guest_id = g.id
        JOIN
            tbl_acc_booking_room br ON b.id = br.booking_id
        JOIN
            tbl_acc_room r ON br.room_id = r.id
        JOIN
            tbl_acc_room_class rc ON r.room_class_id = rc.id
        JOIN
            tbl_acc_room_status rs ON r.status_id = rs.id
        LEFT JOIN
            tbl_acc_booking_room_options bro ON b.id = bro.booking_id
        LEFT JOIN
            tbl_acc_room_options ro ON bro.option_id = ro.id
        ";

        // Add condition if bookingId is provided
        if ($bookingId) {
            $query .= " WHERE b.id = :bookingId ";
        }else{
            $query .= "WHERE b.booking_status_id !=5 and b.booking_status_id !=4 and b.booking_status_id !=3 ORDER BY b.checkin_date DESC";
            
        }

        // Prepare and execute the query
        $stmt = $db->prepare($query);

        if ($bookingId) {
            $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
        }
        $stmt->execute();

        // Fetch all results
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the data as JSON
        echo json_encode(["status" => 200, "data" => $bookings, "message" => "Success!", "msg_type" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => 500, "message" => "An error occurred while fetching the booking details: " . $e->getMessage(), "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Update an existing room booking
    $id = $input['id'];
    $guest_id = $input['guest_id'];
    $payment_status_id = $input['payment_status_id'];
    $checkin_date = $input['checkin_date'];
    $checkout_date = $input['checkout_date'];
    $duration = $input['duration'];
    $num_adults = $input['num_adults'];
    $num_children = $input['num_children'];
    $booking_amount = $input['booking_amount'];
    $coming_from = $input['coming_from'];
    $going_to = $input['going_to'];
    $mode_of_payment_id = $input['mode_of_payment_id'];
    $booked_from = $input['booked_from'];
    $company = $input['company'];
    $other_note = $input['other_note'];
    $updated_at = date('Y-m-d H:i:s');

    $query = "UPDATE tbl_acc_booking SET guest_id = :guest_id, payment_status_id = :payment_status_id, checkin_date = :checkin_date, checkout_date = :checkout_date, duration = :duration, num_adults = :num_adults, num_children = :num_children, booking_amount = :booking_amount, coming_from = :coming_from, going_to = :going_to, mode_of_payment_id = :mode_of_payment_id, booked_from = :booked_from, company = :company, other_note = :other_note, updated_at = :updated_at WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':guest_id', $guest_id, PDO::PARAM_INT);
    $stmt->bindParam(':payment_status_id', $payment_status_id, PDO::PARAM_INT);
    $stmt->bindParam(':checkin_date', $checkin_date, PDO::PARAM_STR);
    $stmt->bindParam(':checkout_date', $checkout_date, PDO::PARAM_STR);
    $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
    $stmt->bindParam(':num_adults', $num_adults, PDO::PARAM_INT);
    $stmt->bindParam(':num_children', $num_children, PDO::PARAM_INT);
    $stmt->bindParam(':booking_amount', $booking_amount, PDO::PARAM_STR);
    $stmt->bindParam(':coming_from', $coming_from, PDO::PARAM_STR);
    $stmt->bindParam(':going_to', $going_to, PDO::PARAM_STR);
    $stmt->bindParam(':mode_of_payment_id', $mode_of_payment_id, PDO::PARAM_INT);
    $stmt->bindParam(':booked_from', $booked_from, PDO::PARAM_STR);
    $stmt->bindParam(':company', $company, PDO::PARAM_STR);
    $stmt->bindParam(':other_note', $other_note, PDO::PARAM_STR);
    $stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Room booking updated successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while updating the room booking.", "msg_type" => "error"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Delete an existing room booking
    // parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $input['id'];

    $query = "DELETE FROM tbl_acc_booking WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Room booking deleted successfully!", "msg_type" => "success"]);
    } else {
        echo json_encode(["status" => 500, "message" => "An error occurred while deleting the room booking.", "msg_type" => "error"]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

$db = null;
