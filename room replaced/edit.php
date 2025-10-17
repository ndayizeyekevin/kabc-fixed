<?php 
error_reporting(E_ALL);
ini_set("display_error", 1);
include '../inc/conn.php';

// Function to calculate days between dates and VAT
function calculateBookingDetails($checkin, $checkout, $room_price) {
    $checkin = date_create($checkin);
    $checkout = date_create($checkout);
    $days = date_diff($checkin, $checkout);
    $days = $days->format("%a");
    $b_amount = $room_price * $days;
    $vat = $b_amount * 0.1;
    
    return [
        'days' => $days,
        'b_amount' => $b_amount,
        'vat' => $vat
    ];
}

if (isset($_POST['add'])) {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $new_room = $_POST['new_room'];
    $room_price = $_POST['amount'];
    $company = $_POST['company'];
    $guest_type = $_POST['guest_type'];
    $booking_id = $_REQUEST['booking'];
    $firstname = $_REQUEST['fname'];
    $lastname = $_REQUEST['lname'];
    $guest_id = $_REQUEST['guest_id'];

    // Calculate days, total amount, and VAT
    $calculation = calculateBookingDetails($checkin, $checkout, $room_price);
    $days = $calculation['days'];
    $b_amount = $calculation['b_amount'];
    $vat = $calculation['vat'];

    try {
        // Start transaction
        $db->beginTransaction();

        // Get the currently assigned room before updating
        $stmtOld = $db->prepare("SELECT room_id FROM tbl_acc_booking_room WHERE booking_id = ?");
        $stmtOld->execute([$booking_id]);
        $oldRoom = $stmtOld->fetch(PDO::FETCH_ASSOC);
        $oldRoomId = $oldRoom ? $oldRoom['room_id'] : null;

        // Update booking details with calculated values
        $stmt1 = $db->prepare("UPDATE tbl_acc_booking 
            SET checkin_date = ?, checkout_date = ?, booking_amount = ?, room_price = ?, company = ?, guest_type = ?, duration = ?, vat_amount = ? 
            WHERE id = ?");
        $stmt1->execute([$checkin, $checkout, $b_amount, $room_price, $company, $guest_type, $days, $vat, $booking_id]);

        // Update the assigned room in tbl_acc_booking_room
        $stmt2 = $db->prepare("UPDATE tbl_acc_booking_room SET room_id = ? WHERE booking_id = ?");
        $stmt2->execute([$new_room, $booking_id]);

        // Free up the previous room (status_id = 3 for Available)
        if ($oldRoomId && $oldRoomId != $new_room) {
            $stmt3 = $db->prepare("UPDATE tbl_acc_room SET status_id = 3 WHERE id = ?");
            $stmt3->execute([$oldRoomId]);
        }

        // Mark the new room as booked (status_id = 2)
        $stmt4 = $db->prepare("UPDATE tbl_acc_room SET status_id = 2 WHERE id = ?");
        $stmt4->execute([$new_room]);

        // Update guest name
        $stmt5 = $db->prepare("UPDATE tbl_acc_guest SET first_name = ?, last_name = ? WHERE id = ?");
        $stmt5->execute([$firstname, $lastname, $guest_id]);

        // Commit all changes
        $db->commit();

        echo "<script>window.location='?resto=room_booking_details&&booking_id=".$booking_id."'</script>";
    } catch (PDOException $e) {
        // Rollback all changes if something fails
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

// Get booking details for form population
$sql = $db->prepare("SELECT *, first_name, last_name FROM tbl_acc_booking INNER JOIN tbl_acc_guest ON tbl_acc_guest.id = tbl_acc_booking.guest_id WHERE tbl_acc_booking.id = ?");
$sql->execute([$_REQUEST['booking']]);
$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $guest_id = $row['guest_id'];
    $checkin_date = $row['checkin_date'];
    $checkout_date = $row['checkout_date'];
    $company = $row['company'];
    $amount = $row['room_price'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $guest_type = $row['guest_type'];
    
    // Calculate initial values for display
    $initialCalculation = calculateBookingDetails($checkin_date, $checkout_date, $amount);
}
?>

<!-- Rest of your HTML and CSS code remains the same -->

<div class="colr-area">
    <div class="container">
        <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Booking List -->
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
                                                <form method="POST">
                                                    <input type="hidden" name="guest_id" value="<?php echo $guest_id; ?>" />
                                                    <input type="hidden" name="booking" value="<?php echo $_REQUEST['booking']; ?>" />
                                                    
                                                    <p>Guest: <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></p>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" class="form-control" name="fname" value="<?php echo htmlspecialchars($first_name); ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" class="form-control" name="lname" value="<?php echo htmlspecialchars($last_name); ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Check In Date</label>
                                                        <input type="date" class="form-control" name="checkin" value="<?php echo $checkin_date; ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Check Out Date</label>
                                                        <input type="date" class="form-control" name="checkout" value="<?php echo $checkout_date; ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Select Room</label>
                                                        <select class="form-control" name="new_room" required>
                                                            <?php
                                                            if (isset($_GET['booking']) && is_numeric($_GET['booking'])) {
                                                                $bookingId = (int)$_GET['booking'];

                                                                // Get the currently booked room and its class
                                                                $stmt = $db->prepare("
                                                                    SELECT r.id AS room_id, r.room_number, tr.id AS room_class_id
                                                                    FROM tbl_acc_room r
                                                                    JOIN tbl_acc_booking_room br ON br.room_id = r.id
                                                                    JOIN tbl_acc_room_class tr ON tr.id = r.room_class_id
                                                                    WHERE br.booking_id = :booking_id
                                                                    LIMIT 1
                                                                ");
                                                                $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
                                                                $stmt->execute();
                                                                $currentRoom = $stmt->fetch(PDO::FETCH_ASSOC);

                                                                if ($currentRoom) {
                                                                    $currentRoomId = $currentRoom['room_id'];
                                                                    $roomClassId = $currentRoom['room_class_id'];

                                                                    // Display current booked room as selected
                                                                    echo "<option value='{$currentRoomId}' selected>{$currentRoom['room_number']} (Currently Booked)</option>";

                                                                    // Fetch other available rooms in the same class
                                                                    $stmt = $db->prepare("
                                                                        SELECT r.id, r.room_number
                                                                        FROM tbl_acc_room r
                                                                        WHERE r.status_id IN (3, 12) 
                                                                          AND r.room_class_id = :room_class_id 
                                                                          AND r.id != :current_room_id
                                                                    ");
                                                                    $stmt->bindParam(':room_class_id', $roomClassId, PDO::PARAM_INT);
                                                                    $stmt->bindParam(':current_room_id', $currentRoomId, PDO::PARAM_INT);
                                                                    $stmt->execute();

                                                                    $availableRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    if ($availableRooms) {
                                                                        foreach ($availableRooms as $room) {
                                                                            echo "<option value='{$room['id']}'>{$room['room_number']}</option>";
                                                                        }
                                                                    } else {
                                                                        echo "<option disabled>No other available rooms in this class</option>";
                                                                    }
                                                                } else {
                                                                    echo "<option disabled>No current room found for this booking</option>";
                                                                }
                                                            } else {
                                                                echo "<option disabled>Invalid booking ID</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Room Price (per night)</label>
                                                        <input type="number" class="form-control" name="amount" value="<?php echo $amount; ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Company</label>
                                                        <input type="text" class="form-control" name="company" value="<?php echo htmlspecialchars($company); ?>">
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Guest Type</label>
                                                        <select class="form-control" name="guest_type" required>
                                                            <option value="individual_guest" <?php echo $guest_type == 'individual_guest' ? 'selected' : ''; ?>>Individual Guest</option>
                                                            <option value="company_guest" <?php echo $guest_type == 'company_guest' ? 'selected' : ''; ?>>Company Guest</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- Display calculated values -->
                                                    <div class="alert alert-info">
                                                        <p><strong>Calculation Preview:</strong></p>
                                                        <p>Duration: <?php echo $initialCalculation['days']; ?> days</p>
                                                        <p>Total Amount: $<?php echo number_format($initialCalculation['b_amount'], 2); ?></p>
                                                        <p>VAT (10%): $<?php echo number_format($initialCalculation['vat'], 2); ?></p>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-outline-info text-info" name="add">Update Booking</button>
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
</div>

<!-- JavaScript for real-time calculation preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkinInput = document.querySelector('input[name="checkin"]');
    const checkoutInput = document.querySelector('input[name="checkout"]');
    const priceInput = document.querySelector('input[name="amount"]');
    const calculationDiv = document.querySelector('.alert-info');
    
    function updateCalculationPreview() {
        if (checkinInput.value && checkoutInput.value && priceInput.value) {
            const checkin = new Date(checkinInput.value);
            const checkout = new Date(checkoutInput.value);
            const timeDiff = checkout.getTime() - checkin.getTime();
            const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
            const roomPrice = parseFloat(priceInput.value);
            const totalAmount = days * roomPrice;
            // const vat = totalAmount * 0.1;
            
            calculationDiv.innerHTML = `
                <p><strong>Calculation Preview:</strong></p>
                <p>Duration: ${days} days</p>
                <p>Total Amount: ${totalAmount.toFixed(2)} RWF</p>
                
            `;
        }
    }
    
    checkinInput.addEventListener('change', updateCalculationPreview);
    checkoutInput.addEventListener('change', updateCalculationPreview);
    priceInput.addEventListener('input', updateCalculationPreview);
});
</script>

<!-- Rest of your HTML and JavaScript code -->

    <!-- modals -->
    <!-- change price modal -->
    <div class="modal fade" id="changePriceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookingLabel">Change Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" class="form-control" id="changed-price">
                </div>
                <button class="btn btn-primary" id="save-price-btn">Change</button>
            </div>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>

    <!-- Footer -->

    <script src="js/room_booking_list.js"></script>
    <script src="js/room_booking_custom.js"></script>
    <!-- <script>src="js/handle_bookings.js"</script> -->
    <script>
        // toggle passport / id field
        $(document).ready(function () {
            $("#toggleSwitch").change(function () {
                if ($(this).is(":checked")) {
                    $("#identificationField").hide(); // Hide Identification Field
                    $("#passportField").show(); // Show Passport Fields
                } else {
                    $("#passportField").hide(); // Hide Passport Fields
                    $("#identificationField").show(); // Show Identification Field
                }
            });
        });
    </script>
    </body>

    </html>