<?php
ini_set("display_errors", 1);
include "../inc/conn.php";
include_once "../inc/function.php";

// Preload guests for simple JS (no AJAX)
$all_guests = [];
try {
    $gstmt = $db->prepare("SELECT id, first_name, last_name, email_address, phone_number, address, date_of_birth, place_of_birth, nationality, residence, profession, identification, passport_number, passport_expiration_date FROM tbl_acc_guest ORDER BY id DESC LIMIT 1000");
    $gstmt->execute();
    $all_guests = $gstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $all_guests = [];
}

$booking_id = $_GET['booking_id'] ?? null;
$booking = null;
$guest = null;
$room = null;
$room_class_id = null;

if ($booking_id) {
    // Fetch booking details
    $booking_stmt = $db->prepare("SELECT * FROM tbl_acc_booking WHERE id = ?");
    $booking_stmt->execute([$booking_id]);
    $booking = $booking_stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $guest_id = $booking['guest_id'] ?? null;

        // Fetch guest if exists
        if ($guest_id) {
            $guest_stmt = $db->prepare("SELECT * FROM tbl_acc_guest WHERE id = ?");
            $guest_stmt->execute([$guest_id]);
            $guest = $guest_stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Fetch booked room
        $room_id = getBookedRoom($booking_id);
        $room_stmt = $db->prepare("SELECT * FROM tbl_acc_room WHERE id = ?");
        $room_stmt->execute([$room_id]);
        $room = $room_stmt->fetch(PDO::FETCH_ASSOC);
        $room_class_id = $room['room_class_id'] ?? null;
    }
}

if (isset($_POST['update_booking'])) {
    $booking_id_to_update = $_POST['booking_id'];
    $guest_id_to_update = $_POST['guest_id'] ?? null;

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $room_class_id_new = $_POST['room_class'];
    $room_id_new = $_POST['room_number'];
    $room_status = $_POST['room_status'];
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $pob = $_POST['pob'] ?? '';
    $nationality = $_POST['nationality'];
    $residence = $_POST['residence'];
    $occupation = $_POST['occupation'] ?? '';
    $form_price = $_POST['price'];
    $id_type = $_POST['id_type'];
    $passport_expiry = $_POST['passport_expiry'] ?? '1900-01-01';

    if ($id_type === 'passport') {
        $passport_number = $_POST['id_number'];
        $id_number = '';
    } else {
        $id_number = $_POST['id_number'];
        $passport_number = '';
        $passport_expiry = '1900-01-01';
    }

 
    if (empty($guest_id_to_update)) {
        $detect_sql = "SELECT id FROM tbl_acc_guest WHERE 
                        (email_address = ? AND email_address <> '')
                        OR (phone_number = ? AND phone_number <> '')
                        OR (identification = ? AND identification <> '')
                        OR (passport_number = ? AND passport_number <> '')
                        LIMIT 1";
        if ($stmt_detect = $conn->prepare($detect_sql)) {
            $stmt_detect->bind_param('ssss', $email, $phone, $id_number, $passport_number);
            if ($stmt_detect->execute()) {
                $res = $stmt_detect->get_result();
                if ($row = $res->fetch_assoc()) {
                    $guest_id_to_update = (int)$row['id'];
                }
            }
        }
    }

    // --- Create guest if still not resolved ---
    if (empty($guest_id_to_update)) {
        $insert_guest_sql = "INSERT INTO tbl_acc_guest 
            (first_name, last_name, email_address, phone_number, address, date_of_birth, place_of_birth, nationality, residence, profession, identification, passport_number, passport_expiration_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_guest = $conn->prepare($insert_guest_sql);
        $stmt_guest->bind_param("sssssssssssss", $first_name, $last_name, $email, $phone, $address, $dob, $pob, $nationality, $residence, $occupation, $id_number, $passport_number, $passport_expiry);
        if (!$stmt_guest->execute()) {
            die("Failed to create guest: " . $stmt_guest->error);
        }
        $guest_id_to_update = $stmt_guest->insert_id;
    } else {
        // --- Update existing guest ---
        $update_guest_sql = "UPDATE tbl_acc_guest SET 
            first_name=?, last_name=?, email_address=?, phone_number=?, address=?, 
            date_of_birth=?, place_of_birth=?, nationality=?, residence=?, profession=?, 
            identification=?, passport_number=?, passport_expiration_date=? 
            WHERE id=?";
        $stmt_guest = $conn->prepare($update_guest_sql);
        $stmt_guest->bind_param("sssssssssssssi", $first_name, $last_name, $email, $phone, $address, $dob, $pob, $nationality, $residence, $occupation, $id_number, $passport_number, $passport_expiry, $guest_id_to_update);
        if (!$stmt_guest->execute()) {
            die("Failed to update guest: " . $stmt_guest->error);
        }
    }

    // Ensure booking is associated with the resolved guest
    if (!empty($guest_id_to_update)) {
        $stmt_update_booking_guest = $conn->prepare("UPDATE tbl_acc_booking SET guest_id = ? WHERE id = ?");
        $stmt_update_booking_guest->bind_param("ii", $guest_id_to_update, $booking_id_to_update);
        $stmt_update_booking_guest->execute();
    }

    // --- Update Booking ---
    $date1 = new DateTime($checkin_date);
    $date2 = new DateTime($checkout_date);
    $duration = max(1, $date1->diff($date2)->days);
    $booking_amount = $form_price * $duration;
    $price_per_night = $form_price;

    $update_booking_sql = "UPDATE tbl_acc_booking SET 
        checkin_date=?, checkout_date=?, duration=?, booking_amount=?, booking_status_id=?, room_price=? WHERE id=?";
    $stmt_booking = $conn->prepare($update_booking_sql);
    $stmt_booking->bind_param("ssididi", $checkin_date, $checkout_date, $duration, $booking_amount, $room_status, $price_per_night, $booking_id_to_update);
    $stmt_booking->execute();

    // --- Update Room Assignment ---
    $update_booking_room_sql = "UPDATE tbl_acc_booking_room SET room_id=? WHERE booking_id=?";
    $stmt_booking_room = $conn->prepare($update_booking_room_sql);
    $stmt_booking_room->bind_param("ii", $room_id_new, $booking_id_to_update);
    $stmt_booking_room->execute();

    $group_id_redirect = $_POST['group_id'];
    echo "<script>alert('Booking updated successfully!'); window.location='?resto=group_bookings&&id=$group_id_redirect'</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Group Booking</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h5>Edit Booking</h5>

    <?php if ($booking && $room): ?>
        <form action="" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
            <input type="hidden" name="guest_id" value="<?php echo $guest['id'] ?? ''; ?>">
            <input type="hidden" name="group_id" value="<?php echo $booking['group_id']; ?>">

            <!-- Guest Information -->
            <h6 class="mb-3 text-primary">Guest Information</h6>
            <!-- Simple JS Search with HTML5 datalist (no AJAX) -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="guest_search" class="form-label">Search Guest (type name, email, phone, ID or passport)</label>
                    <input type="text" id="guest_search" class="form-control" list="guest_list" placeholder="Start typing to search existing guests...">
                    <datalist id="guest_list">
                        <?php foreach ($all_guests as $g): 
                            $name = trim(($g['first_name'] ?? '').' '.($g['last_name'] ?? ''));
                            $sub = array_filter([$g['email_address'] ?? '', $g['phone_number'] ?? '', ($g['identification'] ?? '') ?: ($g['passport_number'] ?? '')]);
                            $label = trim($name) !== '' ? $name : 'Unnamed guest';
                            $suffix = implode(' • ', $sub);
                            $value = $label.' (ID:'.$g['id'].')'.($suffix ? ' - '.$suffix : '');
                        ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($guest['first_name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($guest['last_name'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($guest['phone_number'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($guest['email_address'] ?? ''); ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($guest['address'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($guest['date_of_birth'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="pob" class="form-label">Place of Birth</label>
                    <input type="text" name="pob" class="form-control" value="<?php echo htmlspecialchars($guest['place_of_birth'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                    <input type="text" name="nationality" class="form-control" value="<?php echo htmlspecialchars($guest['nationality'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="residence" class="form-label">Residence <span class="text-danger">*</span></label>
                    <input type="text" name="residence" class="form-control" value="<?php echo htmlspecialchars($guest['residence'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="occupation" class="form-label">Occupation</label>
                    <input type="text" name="occupation" class="form-control" value="<?php echo htmlspecialchars($guest['profession'] ?? ''); ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="id_type" class="form-label">ID Type <span class="text-danger">*</span></label>
                    <select name="id_type" class="form-select" required>
                        <option value="national_id" <?php echo (!empty($guest['identification'])) ? 'selected' : ''; ?>>National ID</option>
                        <option value="passport" <?php echo (!empty($guest['passport_number'])) ? 'selected' : ''; ?>>Passport</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="id_number" class="form-label">ID Number <span class="text-danger">*</span></label>
                    <input type="text" name="id_number" class="form-control" value="<?php echo htmlspecialchars(!empty($guest['identification']) ? $guest['identification'] : $guest['passport_number'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="row mb-3" id="passport_expiry_group" style="<?php echo (!empty($guest['passport_number'])) ? '' : 'display:none;'; ?>">
                <div class="col-md-6">
                    <label for="passport_expiry" class="form-label">Passport Expiry Date</label>
                    <input type="date" name="passport_expiry" class="form-control" value="<?php echo htmlspecialchars($guest['passport_expiration_date'] ?? ''); ?>">
                </div>
            </div>

            <hr>

            <!-- Booking Information -->
            <h6 class="mb-3 text-primary">Booking Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="checkin_date" class="form-label">Check-in Date <span class="text-danger">*</span></label>
                    <input type="date" name="checkin_date" class="form-control" value="<?php echo $booking['checkin_date']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="checkout_date" class="form-label">Check-out Date <span class="text-danger">*</span></label>
                    <input type="date" name="checkout_date" class="form-control" value="<?php echo $booking['checkout_date']; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="room_class" class="form-label">Room Class <span class="text-danger">*</span></label>
                    <select name="room_class" id="room_class" class="form-select" required>
                        <option value="">-- Select Room Class --</option>
                        <?php
                        $classes = mysqli_query($conn, "SELECT * FROM tbl_acc_room_class");
                        while ($row = mysqli_fetch_assoc($classes)) {
                            $selected = ($row['id'] == $room_class_id) ? 'selected' : '';
                            $price = number_format($row['base_price'], 2);
                            echo "<option value='{$row['id']}' {$selected}>{$row['class_name']} ({$price} RWF)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                    <select name="room_number" id="room_number" class="form-select" required>
                        <?php
                        $rooms_q = mysqli_query($conn, "SELECT * FROM tbl_acc_room WHERE room_class_id = '$room_class_id' AND (status_id = 3 OR status_id = 12 OR id = '".getBookedRoom($booking_id)."')");
                        while ($row = mysqli_fetch_assoc($rooms_q)) {
                            $selected = ($row['id'] == getBookedRoom($booking_id)) ? 'selected' : '';
                            echo "<option value='{$row['id']}' {$selected}>{$row['room_number']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label for="room_status" class="form-label">Booking Status <span class="text-danger">*</span></label>
                    <select name="room_status" class="form-select" required>
                        <?php
                        $statuses = mysqli_query($conn, "SELECT * FROM tbl_acc_room_status");
                        while ($row = mysqli_fetch_assoc($statuses)) {
                            $selected = ($row['id'] == $booking['booking_status_id']) ? 'selected' : '';
                            echo "<option value='{$row['id']}' {$selected}>{$row['status_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Room Price per Night (RWF)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo $booking['room_price']; ?>" placeholder="Room Price">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="update_booking" class="btn btn-primary">Update Booking</button>
                <a href="?resto=group_bookings&&id=<?php echo $booking['group_id']; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">Booking not found.</div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('select[name="id_type"]').change(function() {
        if ($(this).val() === 'passport') {
            $('#passport_expiry_group').show();
            $('input[name="passport_expiry"]').prop('required', true);
        } else {
            $('#passport_expiry_group').hide();
            $('input[name="passport_expiry"]').prop('required', false);
        }
    });

    $('#room_class').change(function() {
        const classId = $(this).val();
        if (classId) {
            $.ajax({
                type: 'POST',
                url: 'group_bookings.php',
                data: { class_id: classId },
                success: function(data) {
                    $('#room_number').html(data);
                }
            });
        } else {
            $('#room_number').html('<option value="">-- Select Room Number --</option>');
        }
    });

    // Simple JS populate from datalist selection
    const AllGuests = <?php echo json_encode($all_guests, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
    const $search = $('#guest_search');

    function findGuestFromInput(val) {
        const idMatch = val.match(/ID\s*:(\d+)/i);
        if (idMatch) {
            const gid = parseInt(idMatch[1], 10);
            return AllGuests.find(g => parseInt(g.id,10) === gid) || null;
        }
        // fallback: exact label match against generated value
        for (let i=0;i<AllGuests.length;i++) {
            const g = AllGuests[i];
            const name = `${(g.first_name||'').trim()} ${(g.last_name||'').trim()}`.trim() || 'Unnamed guest';
            const sub = [g.email_address||'', g.phone_number||'', (g.identification||'') || (g.passport_number||'')].filter(Boolean).join(' • ');
            const label = `${name} (ID:${g.id})${sub ? ' - '+sub : ''}`;
            if (label === val) return g;
        }
        return null;
    }

    function fillGuest(g){
        if (!g) return;
        $('input[name="first_name"]').val(g.first_name || '');
        $('input[name="last_name"]').val(g.last_name || '');
        $('input[name="phone"]').val(g.phone_number || '');
        $('input[name="email"]').val(g.email_address || '');
        $('input[name="address"]').val(g.address || '');
        $('input[name="dob"]').val(g.date_of_birth || '');
        $('input[name="pob"]').val(g.place_of_birth || '');
        $('input[name="nationality"]').val(g.nationality || '');
        $('input[name="residence"]').val(g.residence || '');
        $('input[name="occupation"]').val(g.profession || '');
        if (g.passport_number) {
            $('select[name="id_type"]').val('passport').trigger('change');
            $('input[name="id_number"]').val(g.passport_number || '');
            $('input[name="passport_expiry"]').val(g.passport_expiration_date || '');
        } else {
            $('select[name="id_type"]').val('national_id').trigger('change');
            $('input[name="id_number"]').val(g.identification || '');
            $('input[name="passport_expiry"]').val('');
        }
        $('input[name="guest_id"]').val(g.id);
    }

    $search.on('change blur', function(){
        const val = $(this).val().trim();
        const g = findGuestFromInput(val);
        if (g) fillGuest(g);
    });
});
</script>
</body>
</html>
