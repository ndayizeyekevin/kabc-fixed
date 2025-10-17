<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
$conn_file = '../inc/conn.php';
if (!file_exists($conn_file)) {
    die("Error: Database connection file 'conn.php' not found in '../inc/'. Please check the file path.");
}
include $conn_file;

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Placeholder for getGuestNames function if not defined
if (!function_exists('getGuestNames')) {
    function getGuestNames($guest_id) {
        global $conn;
        $stmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM tbl_acc_guest WHERE id = ?");
        $stmt->bind_param("i", $guest_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['full_name'] : 'Unknown Guest';
    }
}

// Handle form submission
if (isset($_POST['add'])) {
    // Validate inputs
    $required_fields = ['checkin', 'checkout', 'new_room', 'amount', 'guest_type', 'fname', 'lname', 'guest_id', 'booking'];
    $errors = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if (empty($errors)) {
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];
        $new_room = (int)$_POST['new_room'];
        $price = (float)$_POST['amount'];
        $guest_type = $_POST['guest_type'];
        $booking_id = (int)$_POST['booking'];
        $firstname = $_POST['fname'];
        $lastname = $_POST['lname'];
        $guest_id = (int)$_POST['guest_id'];

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Get the currently assigned room
            $stmtOld = $conn->prepare("SELECT room_id FROM tbl_acc_booking_room WHERE booking_id = ?");
            $stmtOld->bind_param("i", $booking_id);
            $stmtOld->execute();
            $resultOld = $stmtOld->get_result();
            $oldRoom = $resultOld->fetch_assoc();
            $oldRoomId = $oldRoom ? $oldRoom['room_id'] : null;

            // Update booking details
            $stmt1 = $conn->prepare("
                UPDATE tbl_acc_booking 
                SET checkin_date = ?, checkout_date = ?, booking_amount = ?, room_price = ?, guest_type = ? 
                WHERE id = ?
            ");
            $stmt1->bind_param("ssddsi", $checkin, $checkout, $price, $price, $guest_type, $booking_id);
            $stmt1->execute();

            // Update the assigned room
            $stmt2 = $conn->prepare("UPDATE tbl_acc_booking_room SET room_id = ? WHERE booking_id = ?");
            $stmt2->bind_param("ii", $new_room, $booking_id);
            $stmt2->execute();

            // Free up the previous room (status_id = 3 for Available)
            if ($oldRoomId && $oldRoomId != $new_room) {
                $stmt3 = $conn->prepare("UPDATE tbl_acc_room SET status_id = 3 WHERE id = ?");
                $stmt3->bind_param("i", $oldRoomId);
                $stmt3->execute();
            }

            // Mark the new room as booked (status_id = 2)
            $stmt4 = $conn->prepare("UPDATE tbl_acc_room SET status_id = 2 WHERE id = ?");
            $stmt4->bind_param("i", $new_room);
            $stmt4->execute();

            // Update guest name
            $stmt5 = $conn->prepare("UPDATE tbl_acc_guest SET first_name = ?, last_name = ? WHERE id = ?");
            $stmt5->bind_param("ssi", $firstname, $lastname, $guest_id);
            $stmt5->execute();

            // Commit transaction
            $conn->commit();

            // Redirect to reservation page
            echo "<script>window.location='?resto=Reservation';</script>";
            exit;
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        // Display validation errors
        echo "<div class='alert alert-danger'>" . implode('<br>', array_map('htmlspecialchars', $errors)) . "</div>";
    }
}

// Fetch booking details
$booking_id = isset($_GET['booking']) && is_numeric($_GET['booking']) ? (int)$_GET['booking'] : 0;
if ($booking_id) {
    $sql = $conn->prepare("
        SELECT b.*, g.first_name, g.last_name
        FROM tbl_acc_booking b
        INNER JOIN tbl_acc_guest g ON g.id = b.guest_id
        WHERE b.id = ?
    ");
    $sql->bind_param("i", $booking_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $guest_id = $row['guest_id'];
        $checkin_date = $row['checkin_date'];
        $checkout_date = $row['checkout_date'];
        $amount = $row['room_price'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $guest_type = $row['guest_type'];
    } else {
        echo "<div class='alert alert-danger'>Booking not found.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>Invalid booking ID.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table td {
            vertical-align: middle;
        }
        .top-section, .room-params {
            position: relative;
        }
        hr {
            width: 100%;
            border-top: 2px solid #ccc;
            margin-top: 30px;
        }
        .custom-tabs-container {
            border-bottom: 1px solid #e5e5e5;
        }
        .custom-tabs .nav-link {
            border: none;
            background: none;
            color: #6c757d;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            padding: 10px 20px;
            position: relative;
            transition: color 0.3s ease;
        }
        .custom-tabs .nav-link:hover {
            color: #0d6efd;
        }
        .custom-tabs .nav-link.active {
            color: black;
            font-weight: bold;
        }
        .custom-tabs .nav-link.active::after {
            content: '';
            display: block;
            width: 100%;
            height: 3px;
            background-color: #0F7CBF;
            position: absolute;
            bottom: 0;
            left: 0;
        }
        .nav-tabs .nav-item .nav-link:not(.active) {
            background-color: transparent;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 54px;
            height: 28px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 34px;
            transition: .4s;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            border-radius: 50%;
            transition: .4s;
        }
        input:checked + .slider {
            background-color: #F2A341;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .clear-span {
            cursor: pointer;
            color: #dc3545;
            padding: 5px 10px;
            border: 1px solid transparent;
            border-radius: 5px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .clear-span:hover {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
        }
        .is-invalid {
            border-color: #dc3545;
            background-color: #f8d7da;
        }
        .is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .invalid-feedback {
            color: #dc3545;
            display: block;
        }
        .is-invalid ~ .form-check-label {
            color: #dc3545;
        }
        @media (max-width: 717px) {
            .current-transaction-tab {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                padding: 10px;
                font-size: 12px;
                background-color: #e7f3ff;
            }
            .current-transaction-tab span {
                display: block;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="colr-area">
        <div class="container">
            <div class="layout-page">
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div id="getBookingAlert"></div>
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Edit Booking</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <div class="card-body">
                                                <div class="col-md-12">
                                                    <form method="POST" id="editBookingForm">
                                                        <input type="hidden" name="guest_id" value="<?php echo htmlspecialchars($guest_id); ?>" />
                                                        <input type="hidden" name="booking" value="<?php echo htmlspecialchars($booking_id); ?>" />

                                                        <div class="mb-3">
                                                            <label class="form-label">Guest</label>
                                                            <p><?php echo htmlspecialchars(getGuestNames($guest_id)); ?></p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label" for="fname">First Name</label>
                                                            <input type="text" class="form-control" id="fname" name="fname" value="<?php echo htmlspecialchars($first_name); ?>" required />
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label" for="lname">Last Name</label>
                                                            <input type="text" class="form-control" id="lname" name="lname" value="<?php echo htmlspecialchars($last_name); ?>" required />
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label" for="checkin">Check-In Date</label>
                                                            <input type="date" class="form-control" id="checkin" name="checkin" value="<?php echo htmlspecialchars($checkin_date); ?>" required />
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label" for="checkout">Check-Out Date</label>
                                                            <input type="date" class="form-control" id="checkout" name="checkout" value="<?php echo htmlspecialchars($checkout_date); ?>" required />
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label" for="new_room">Select Room</label>
                                                            <div class="input-group input-group-merge">
                                                                <select class="form-control" id="new_room" name="new_room" required>
                                                                    <?php
                                                                    $stmt = $conn->prepare("
                                                                        SELECT r.id AS room_id, r.room_number, tr.id AS room_class_id
                                                                        FROM tbl_acc_room r
                                                                        JOIN tbl_acc_booking_room br ON br.room_id = r.id
                                                                        JOIN tbl_acc_room_class tr ON tr.id = r.room_class_id
                                                                        WHERE br.booking_id = ?
                                                                        LIMIT 1
                                                                    ");
                                                                    $stmt->bind_param("i", $booking_id);
                                                                    $stmt->execute();
                                                                    $result = $stmt->get_result();
                                                                    $currentRoom = $result->fetch_assoc();

                                                                    if ($currentRoom) {
                                                                        $currentRoomId = $currentRoom['room_id'];
                                                                        $roomClassId = $currentRoom['room_class_id'];
                                                                        echo "<option value='{$currentRoomId}' selected>" . htmlspecialchars($currentRoom['room_number']) . " (Currently Booked)</option>";

                                                                        $stmt = $conn->prepare("
                                                                            SELECT r.id, r.room_number
                                                                            FROM tbl_acc_room r
                                                                            WHERE r.status_id IN (3, 12) 
                                                                              AND r.room_class_id = ? 
                                                                              AND r.id != ?
                                                                        ");
                                                                        $stmt->bind_param("ii", $roomClassId, $currentRoomId);
                                                                        $stmt->execute();
                                                                        $result = $stmt->get_result();
                                                                        $availableRooms = $result->fetch_all(MYSQLI_ASSOC);

                                                                        if ($availableRooms) {
                                                                            foreach ($availableRooms as $room) {
                                                                                echo "<option value='{$room['id']}'>" . htmlspecialchars($room['room_number']) . "</option>";
                                                                            }
                                                                        } else {
                                                                            echo "<option disabled>No other available rooms in this class</option>";
                                                                        }
                                                                    } else {
                                                                        echo "<option disabled>No current room found for this booking</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label" for="amount">Amount</label>
                                                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($amount); ?>" required />
                                                        </div>


                                                        <div class="mb-3">
                                                            <label class="form-label" for="guest_type">Guest Type</label>
                                                            <select class="form-control" id="guest_type" name="guest_type" required>
                                                                <option value="individual_guest" <?php echo $guest_type == 'individual_guest' ? 'selected' : ''; ?>>Individual Guest</option>
                                                                <option value="company_guest" <?php echo $guest_type == 'company_guest' ? 'selected' : ''; ?>>Company Guest</option>
                                                            </select>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-outline-info" name="add">Edit</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="changePriceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Price</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="number" class="form-control" id="changed-price">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="save-price-btn">Change</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="showToast" class="toast-container position-relative"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/room_booking_list.js"></script>
    <script src="js/room_booking_custom.js"></script>
    <script>
        $(document).ready(function() {
            $("#toggleSwitch").change(function() {
                if ($(this).is(":checked")) {
                    $("#identificationField").hide();
                    $("#passportField").show();
                } else {
                    $("#passportField").hide();
                    $("#identificationField").show();
                }
            });

            $("#editBookingForm").on("submit", function(e) {
                let valid = true;
                $(this).find("[required]").each(function() {
                    if (!$(this).val()) {
                        $(this).addClass("is-invalid");
                        valid = false;
                    } else {
                        $(this).removeClass("is-invalid");
                    }
                });
                if (!valid) {
                    e.preventDefault();
                    alert("Please fill in all required fields.");
                }
            });
        });
    </script>
</body>
</html>
