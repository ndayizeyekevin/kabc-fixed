<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Group Booking</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    select option {
        font-weight: bold;
        color: blue;
    }

    a {
        text-decoration: none;
    }   

    a:hover {
        text-decoration: underline;
        color: #044de0ff !important;
    }
    
    .guest-search-container {
        position: relative;
    }
    
    .guest-search-results {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: none;
    }
    
    .guest-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }
    
    .guest-item:hover {
        background-color: #f5f5f5;
    }
    
    .guest-name {
        font-weight: bold;
    }
    
    .guest-details {
        font-size: 0.9em;
        color: #666;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-radius: 50%;
        border-top: 3px solid #3498db;
        animation: spin 1s linear infinite;
        margin-left: 10px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
<?php
ini_set("display_errors", 1);
include "../inc/conn.php";

// --- AJAX HANDLERS --- //
if (isset($_POST['class_id'])) {
    $class_id = (int)$_POST['class_id'];
    $checkin  = $_POST['checkin_date'] ?? '';
    $checkout = $_POST['checkout_date'] ?? '';

    echo "<option value=''>-- Select Room Number --</option>";

    // If both dates are provided, exclude rooms with overlapping bookings
    if (!empty($checkin) && !empty($checkout)) {
        $sql = "SELECT r.id, r.room_number
                FROM tbl_acc_room r
                WHERE r.room_class_id = ?
                  AND NOT EXISTS (
                      SELECT 1 FROM tbl_acc_booking_room br
                      JOIN tbl_acc_booking b ON b.id = br.booking_id
                      WHERE br.room_id = r.id
                        AND ? < b.checkout_date
                        AND ? > b.checkin_date
                )
                ORDER BY r.room_number";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('iss', $class_id, $checkin, $checkout);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                echo "<option value='".$row['id']."'>".htmlspecialchars($row['room_number'])."</option>";
            }
        }
    } else {
        // Fallback to previous behavior when dates are not provided
        $stmt = $conn->prepare("SELECT id, room_number FROM tbl_acc_room WHERE room_class_id = ? ORDER BY room_number");
        $stmt->bind_param('i', $class_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            echo "<option value='".$row['id']."'>".htmlspecialchars($row['room_number'])."</option>";
        }
    }
    exit;
}

if (isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];
    $query = mysqli_query($conn, "SELECT rc.base_price FROM tbl_acc_room r INNER JOIN tbl_acc_room_class rc ON r.room_class_id = rc.id WHERE r.id = '$room_id' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
    echo $data ? $data['base_price'] : "0";
    exit;
}

// Handle guest search
if (isset($_POST['search_guest'])) {
    $searchTerm = '%' . $_POST['search_guest'] . '%';
    
    $query = "SELECT * FROM tbl_acc_guest 
              WHERE first_name LIKE ? OR last_name LIKE ? OR email_address LIKE ? OR phone_number LIKE ?
              LIMIT 10";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $guests = array();
    while ($row = $result->fetch_assoc()) {
        $guests[] = $row;
    }
    
    echo json_encode($guests);
    exit();
}

if (isset($_POST['get_room_classes'])) {
    $classes = mysqli_query($conn, "SELECT * FROM tbl_acc_room_class");
    echo "<option value=''>-- Select Room Class --</option>";
    while ($row = mysqli_fetch_assoc($classes)) {
        $price = number_format($row['base_price'], 2);
        echo "<option value='{$row['id']}' data-price='{$row['base_price']}'>{$row['class_name']} <b>({$price} RWF)</b></option>";
    }
    exit;
}

if (isset($_POST['get_room_statuses'])) {
    $statuses = mysqli_query($conn, "SELECT * FROM tbl_acc_room_status");
    echo "<option value=''>-- Select Status --</option>";
    while ($row = mysqli_fetch_assoc($statuses)) {
        echo "<option value='{$row['id']}'>{$row['status_name']}</option>";
    }
    exit;
}
// Handle single booking
if(isset($_POST['addBook'])){
    // Collect form data
    $first_name     = $_POST['first_name'];
    $last_name      = $_POST['last_name'];
    $checkin_date   = $_POST['checkin_date'];
    $checkout_date  = $_POST['checkout_date'];
    $room_class_id  = $_POST['room_class'];
    $room_status    = $_POST['room_status'];
    $room_id        = $_POST['room_number'];
    $group_id       = $_GET['id'] ?? null; // Optional for single booking
    $email          = $_POST['email'] ?? '';
    $phone          = $_POST['phone'];
    $address        = $_POST['address'];
    $dob            = $_POST['dob'];
    $pob            = $_POST['pob'] ?? '';
    $nationality    = $_POST['nationality'];
    $residence      = $_POST['residence'];
    $occupation     = $_POST['occupation'] ?? '';
    $form_price     = $_POST['price'];
    $id_type        = $_POST['id_type'];

    // Handle ID/passport logic
    if ($id_type === 'passport') {
        $passport_number = $_POST['id_number'];
        $passport_expiry = $_POST['passport_expiry'];
        $id_number = '';
    } else {
        $id_number = $_POST['id_number'];
        $passport_number = '';
        $passport_expiry = '';
    }

    // Validate ID/Passport
    if (empty($id_number) && empty($passport_number)) {
        echo "<script>alert('Error: Please provide either an ID number or a Passport number.'); window.history.back();</script>";
        exit();
    }

    $created_at = date("Y-m-d H:i:s");

    // Determine existing guest by unique identifiers with ID vs Passport exclusivity
    $guest_id = null;

    // 1) Prefer exact match on identification if provided (do not mix with passport)
    if (!empty($id_number)) {
        $sql = "SELECT id FROM tbl_acc_guest WHERE identification = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $id_number);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) { $guest_id = (int)$row['id']; }
        }
    // 2) Else prefer exact match on passport if provided (only when ID not provided)
    } elseif (!empty($passport_number)) {
        $sql = "SELECT id FROM tbl_acc_guest WHERE passport_number = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $passport_number);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) { $guest_id = (int)$row['id']; }
        }
    }

    // 3) Fallback to email if still not found
    if (empty($guest_id) && !empty($email)) {
        $sql = "SELECT id FROM tbl_acc_guest WHERE email_address = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) { $guest_id = (int)$row['id']; }
        }
    }

    // 4) Fallback to phone if still not found
    if (empty($guest_id) && !empty($phone)) {
        $sql = "SELECT id FROM tbl_acc_guest WHERE phone_number = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) { $guest_id = (int)$row['id']; }
        }
    }

    // Create a new guest only if none matched
    if (empty($guest_id)) {
        $insert_guest_sql = "INSERT INTO tbl_acc_guest 
            (first_name, last_name, email_address, phone_number, address, date_of_birth, place_of_birth, nationality, residence, profession, identification, passport_number, passport_expiration_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_guest = $conn->prepare($insert_guest_sql);
        $stmt_guest->bind_param(
            "sssssssssssss",
            $first_name, $last_name, $email, $phone, $address, $dob, $pob, $nationality, $residence, $occupation, $id_number, $passport_number, $passport_expiry
        );
        if (!$stmt_guest->execute()) {
            die("Failed to insert guest: " . $stmt_guest->error);
        }
        $guest_id = $stmt_guest->insert_id;
    }

    $check_room_sql = "SELECT b.id FROM tbl_acc_booking b
        INNER JOIN tbl_acc_booking_room br ON b.id = br.booking_id
        WHERE br.room_id = ?
          AND ? < b.checkout_date
          AND ? > b.checkin_date";
    $stmt_check_room = $conn->prepare($check_room_sql);
    $stmt_check_room->bind_param(
        "iss",
        $room_id, $checkin_date, $checkout_date
    );
    $stmt_check_room->execute();
    $room_result = $stmt_check_room->get_result();

    if ($room_result->num_rows > 0) {
        echo "<script>alert('Error: This room is already booked for the selected dates. Please choose another room or dates.'); window.history.back();</script>";
        exit();
    }

    // Calculate duration
    $date1 = new DateTime($checkin_date);
    $date2 = new DateTime($checkout_date);
    $interval = $date1->diff($date2);
    $duration = $interval->days ?: 1; // Default to 1 day if same day

    // Calculate booking amount
    $sqll = mysqli_fetch_assoc(mysqli_query($conn, "SELECT base_price FROM tbl_acc_room_class WHERE id = '$room_class_id'"));
    $price_per_night = $form_price > 0 ? $form_price : $sqll['base_price'];
    $booking_amount = $price_per_night * $duration;

    // Insert into tbl_acc_booking
    $day_closed_at_value = $checkout_date;
    $insert_booking_sql = "INSERT INTO tbl_acc_booking 
        (guest_id, checkin_date, checkout_date, duration, num_adults, booking_amount, booking_status_id, booking_type, room_price, created_at, group_id, day_closed_at) 
        VALUES (?, ?, ?, ?, 1, ?, ?, 'Single', ?, ?, ?, ?)";
    $stmt_booking = $conn->prepare($insert_booking_sql);
    $stmt_booking->bind_param(
        "issdidisis",
        $guest_id, $checkin_date, $checkout_date, $duration, $booking_amount, $room_status, $price_per_night, $created_at, $group_id, $day_closed_at_value
    );
    if (!$stmt_booking->execute()) {
        die("Failed to insert booking: " . $stmt_booking->error);
    }
    $booking_id = $stmt_booking->insert_id;

    // Insert into tbl_acc_booking_room
    $stmt_insertBooking = $conn->prepare("INSERT INTO tbl_acc_booking_room (booking_id, room_id) VALUES (?, ?)");
    $stmt_insertBooking->bind_param("ii", $booking_id, $room_id);
    if (!$stmt_insertBooking->execute()) {
        die("Failed to insert tbl_acc_booking_room: " . $stmt_insertBooking->error);
    }

    // Update room status
    $stmt_update_room = $conn->prepare("UPDATE tbl_acc_room SET status_id = 2 WHERE id = ?");
    $stmt_update_room->bind_param("i", $room_id);
    $stmt_update_room->execute();

    // Redirect back to the same group bookings page
    $redir_group_id = $group_id ?: ($_GET['id'] ?? '');
    echo "<script>window.location='?resto=group_bookings&&id={$redir_group_id}';</script>";
    exit();
}

// Handle group booking for multiple guests (unknown)
if(isset($_POST['addMultipleBookings'])){
    $group_id       = $_GET['id'];
    $checkin_date   = $_POST['multi_checkin_date'];
    $checkout_date  = $_POST['multi_checkout_date'];
    $created_at     = date("Y-m-d H:i:s");

    // Room status for reserved (assuming ID 2)
    $reserved_status_id = 2;  

    // Arrays from the form (room-specific data)
    $room_class_ids = $_POST['guest_room_class'];
    $room_ids       = $_POST['guest_room_number'];
    $prices         = $_POST['guest_price'];

    $num_guests = count($room_class_ids);

    for ($i = 0; $i < $num_guests; $i++) {
        $room_class_id  = $room_class_ids[$i];
        $room_id        = $room_ids[$i];
        $form_price     = $prices[$i];

        // Calculate duration
        $date1 = new DateTime($checkin_date);
        $date2 = new DateTime($checkout_date);
        $interval = $date1->diff($date2);
        $duration = $interval->days;
        if ($duration == 0) $duration = 1;

        // Calculate booking amount
        $booking_amount = $form_price * $duration;
        $price_per_night = $form_price;

        // Insert into tbl_acc_booking with guest_id as NULL
        $day_closed_at_value = $checkout_date;
        $insert_booking_sql = "INSERT INTO tbl_acc_booking 
            (guest_id, checkin_date, checkout_date, duration, num_adults, booking_amount, 
             booking_status_id, booking_type, room_price, created_at, group_id, day_closed_at) 
             VALUES (NULL, ?, ?, ?, 1, ?, ?, 'Group', ?, ?, ?, ?)";
        $stmt_booking = $conn->prepare($insert_booking_sql);
        $stmt_booking->bind_param(
            "ssdidisis", 
            $checkin_date,
            $checkout_date,
            $duration,
            $booking_amount,
            $reserved_status_id,
            $price_per_night,
            $created_at,
            $group_id,
            $day_closed_at_value
        );

        if (!$stmt_booking->execute()) {
            die("Failed to insert booking " . ($i + 1) . ": " . $stmt_booking->error);
        }
        $booking_id = $stmt_booking->insert_id;

        // Insert into tbl_acc_booking_room
        $insertBoking = "INSERT INTO tbl_acc_booking_room (booking_id, room_id) VALUES(?, ?)";
        $stmt_insertBooking = $conn->prepare($insertBoking);
        $stmt_insertBooking->bind_param("ii", $booking_id, $room_id);
        if (!$stmt_insertBooking->execute()) {
            die("Failed to insert booking room " . ($i + 1) . ": " . $stmt_insertBooking->error);
        }

        // Update room status to reserved
        $update_room_sql = "UPDATE tbl_acc_room SET status_id = 2 WHERE id = ?";
        $stmt_update_room = $conn->prepare($update_room_sql);
        $stmt_update_room->bind_param("i", $room_id);
        $stmt_update_room->execute();
    }

    echo "<script>alert('Multiple bookings successfully added!'); window.location='?resto=group_bookings&&id=$group_id';</script>";
    exit();
}

?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center">
    <h5>Group Booking</h5>
    <button id="showFormBtn" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addBookingModal">Add Booking</button>
  </div>

  <div class="table-responsive mt-3">
    <table class="table table-bordered" id="bookingsTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Guest Names</th>
          <th>Nationality</th>
          <th>Room Number</th>
          <th>Room Class</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include_once "../inc/function.php"; // Assume this file contains the helper functions
        $group_id = $_REQUEST['id'];
        $stmt = $db->prepare("SELECT * FROM tbl_acc_booking WHERE group_id = ?");
        $stmt->execute([$group_id]);
        $no = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $no++;
          echo "<tr>
            <td>$no</td>
            <td>" . getGuestNames($row['guest_id']) . "</td>
            <td>" . getGuestNationality($row['guest_id']) . "</td>
            <td>" . getRoomName(getBookedRoom($row['id'])) . "</td>
            <td>" . getRoomClassType(getRoomClass(getBookedRoom($row['id']))) . "</td>
            
            
            <td><a href='?resto=room_booking_details&&booking_id={$row['id']}' class='btn btn-sm btn-info'>View Booking</a> <a href='?resto=edit_group_booking&&booking_id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a></td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Popup Form -->
  
<!-- Modal -->
<div class="modal fade" id="addBookingModal" tabindex="-1" aria-labelledby="addBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBookingModalLabel">Add Booking to Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center mb-3 border-bottom pb-3">
                    <button type="button" id="showSingleBookingFormBtn" class="btn btn-primary active">Single Booking</button>
                    <button type="button" id="showMultipleBookingFormBtn" class="btn btn-outline-primary ms-2">Multiple Bookings</button>
                </div>

                <!-- Single Booking Form -->
                <div id="singleBookingForm">
                    <form action="" method="POST">
                        <!-- Guest Search -->
                        <h6 class="mb-3 text-primary">Guest Search</h6>
                        <div class="row mb-3">
                            <div class="col-md-12 guest-search-container">
                                <label for="guestSearch" class="form-label">Search Existing Guest</label>
                                <input type="text" id="guestSearch" class="form-control" placeholder="Search by name, email, or phone">
                                <div id="guestSearchResults" class="guest-search-results"></div>
                                <div class="form-text">Start typing to search for existing guests</div>
                            </div>
                        </div>

                        <!-- Guest Info -->
                        <h6 class="mb-3 text-primary">Guest Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter last name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email (optional)">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="Enter address" required>
                            </div>
                            <div class="col-md-6">
                                <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" id="dob" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pob" class="form-label">Place of Birth</label>
                                <input type="text" name="pob" id="pob" class="form-control" placeholder="Enter place of birth (optional)">
                            </div>
                            <div class="col-md-6">
                                <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                                <input type="text" name="nationality" id="nationality" class="form-control" placeholder="Enter nationality" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="residence" class="form-label">Residence <span class="text-danger">*</span></label>
                                <input type="text" name="residence" id="residence" class="form-control" placeholder="Enter residence" required>
                            </div>
                            <div class="col-md-6">
                                <label for="occupation" class="form-label">Occupation</label>
                                <input type="text" name="occupation" id="occupation" class="form-control" placeholder="Enter occupation (optional)">
                            </div>
                        </div>

                        <!-- ID Choice -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_type" class="form-label">ID Type <span class="text-danger">*</span></label>
                                <select name="id_type" id="id_type" class="form-select" required>
                                    <option value="">-- Select ID Type --</option>
                                    <option value="national_id">National ID</option>
                                    <option value="passport">Passport</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_number" class="form-label">ID Number <span class="text-danger">*</span></label>
                                <input type="text" name="id_number" id="id_number" class="form-control" placeholder="Enter ID number" required>
                            </div>
                        </div>

                        <div class="row mb-3" id="passport_expiry_group" style="display:none;">
                            <div class="col-md-6">
                                <label for="passport_expiry" class="form-label">Passport Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" name="passport_expiry" id="passport_expiry" class="form-control">
                            </div>
                        </div>

                        <hr>

                        <!-- Booking Info -->
                        <h6 class="mb-3 text-primary">Booking Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="checkin_date" class="form-label">Check-in Date <span class="text-danger">*</span></label>
                                <?php $today = date('Y-m-d'); ?>
                                <input type="date" name="checkin_date" value="<?php echo $today; ?>" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="checkout_date" class="form-label">Check-out Date <span class="text-danger">*</span></label>
                                <input type="date" name="checkout_date" value="<?php echo $today; ?>"  class="form-control" required>
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
                                        // Format price with 2 decimals and comma separator
                                        $price = number_format($row['base_price'], 2);  
                                        echo "<option value='{$row['id']}'>{$row['class_name']} <b>({$price} RWF)</b></option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                                <select name="room_number" id="room_number" class="form-select" required>
                                    <option value="">-- Select Room Number --</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                          <div class="col-md-6 mb-3">
                              <label for="room_status" class="form-label">Room Status <span class="text-danger">*</span></label>
                              <select name="room_status" id="room_status" class="form-select" required>
                                  <option value="">-- Select Status --</option>
                                  <?php
                                  $statuses = mysqli_query($conn, "SELECT * FROM tbl_acc_room_status");
                                  while ($row = mysqli_fetch_assoc($statuses)) {
                                      echo "<option value='{$row['id']}'>{$row['status_name']}</option>";
                                  }
                                  ?>
                              </select>
                          </div>
                          <div class="col-md-6 mb-3">
                              <label for="price" class="form-label">Room Price per Night (RWF)</label>
                              <!-- Display the price as base_price from the tbl_acc_room_class using the selected room class above-->
                              <input type="number" name="price" id="price" class="form-control" value="" placeholder="Room Price">
                          </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="addBook" class="btn btn-primary">Create Booking</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Multiple Booking Form -->
                <div id="multipleBookingForm" style="display:none;">
                    <form action="" method="POST">
                        <h6 class="mb-3 text-primary">Multiple Bookings</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="num_guests" class="form-label">Number of Guests</label>
                                <input type="number" name="num_guests" id="num_guests" class="form-control" min="1" value="1" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="multi_checkin_date" class="form-label">Check-in Date</label>
                                <input type="date" name="multi_checkin_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="multi_checkout_date" class="form-label">Check-out Date</label>
                                <input type="date" name="multi_checkout_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div id="guests-and-rooms-container">
                            <!-- Dynamic guest and room selection fields will be added here by JS -->
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="addMultipleBookings" class="btn btn-primary">Create Multiple Bookings</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script for conditional Passport Expiry -->
    <script>
    document.getElementById("id_type").addEventListener("change", function() {
        const expiryGroup = document.getElementById("passport_expiry_group");
        const expiryInput = document.getElementById("passport_expiry");
        if (this.value === "passport") {
            expiryGroup.style.display = "block";
            expiryInput.required = true;
        } else {
            expiryGroup.style.display = "none";
            expiryInput.required = false;
        }
    });
    </script>

    <script>
    $(document).ready(function() {
        // --- Single Booking Form Logic ---

        // Guest search functionality (fetch once, filter locally like room_booking_list.js)
        const guestsApi = '../api/guests/read.php';
        let allGuests = [];

        const fetchGuests = () => {
            $.ajax({
                url: guestsApi,
                method: 'GET',
                success: function (response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.status == 200) {
                            allGuests = data.guests || [];
                        } else {
                            allGuests = [];
                        }
                    } catch (e) {
                        // some servers may already return JSON; handle both cases
                        if (response && response.status == 200 && Array.isArray(response.guests)) {
                            allGuests = response.guests;
                        } else {
                            allGuests = [];
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error fetching guests:', textStatus, errorThrown);
                    allGuests = [];
                }
            });
        };

        const renderGuestResults = (guests) => {
            const resultsContainer = $('#guestSearchResults');
            resultsContainer.empty();
            if (guests.length > 0) {
                guests.slice(0, 10).forEach(function(guest) {
                    const fullName = `${guest.first_name ?? ''} ${guest.last_name ?? ''}`.trim();
                    const email = guest.email_address || 'No email';
                    const phone = guest.phone_number || 'No phone';
                    resultsContainer.append(`<div class="guest-item" data-guest-id="${guest.id}" data-first-name="${guest.first_name || ''}" data-last-name="${guest.last_name || ''}" data-email="${guest.email_address || ''}" data-phone="${guest.phone_number || ''}" data-dob="${guest.date_of_birth || ''}" data-pob="${guest.place_of_birth || ''}" data-nationality="${guest.nationality || ''}" data-residence="${guest.residence || ''}" data-address="${guest.address || ''}" data-occupation="${guest.profession || ''}" data-id-number="${guest.identification || ''}" data-passport-number="${guest.passport_number || ''}" data-passport-expiry="${guest.passport_expiration_date || ''}"><div class="guest-name">${fullName || 'Unnamed Guest'}</div><div class="guest-details">${email} | ${phone}</div></div>`);
                });
                resultsContainer.show();
            } else {
                resultsContainer.html('<div class="guest-item">No guests found</div>').show();
            }
        };

        fetchGuests(); // load guests once

        $('#guestSearch').on('input', function() {
            const searchTerm = $(this).val().trim().toLowerCase();
            if (searchTerm.length > 0) {
                const filtered = allGuests.filter(g => {
                    const fullName = `${(g.first_name || '')} ${(g.last_name || '')}`.toLowerCase();
                    const email = (g.email_address || '').toLowerCase();
                    const phone = (g.phone_number || '').toLowerCase();
                    return fullName.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm);
                });
                renderGuestResults(filtered);
            } else {
                $('#guestSearchResults').hide();
            }
        });
        
        // Handle guest selection
        $(document).on('click', '.guest-item', function() {
            $('#first_name').val($(this).data('first-name'));
            $('#last_name').val($(this).data('last-name'));
            $('#email').val($(this).data('email'));
            $('#phone').val($(this).data('phone'));
            $('#address').val($(this).data('address'));
            $('#dob').val($(this).data('dob'));
            $('#pob').val($(this).data('pob'));
            $('#nationality').val($(this).data('nationality'));
            $('#residence').val($(this).data('residence'));
            $('#occupation').val($(this).data('occupation'));
            if ($(this).data('passport-number')) {
                $('#id_type').val('passport');
                $('#id_type').trigger('change');
                $('#id_number').val($(this).data('passport-number'));
                $('#passport_expiry').val($(this).data('passport-expiry'));
            } else {
                $('#id_type').val('national_id');
                $('#id_type').trigger('change');
                $('#id_number').val($(this).data('id-number'));
            }
            $('#guestSearchResults').hide();
            $('#guestSearch').val('');
        });
        
        // Close search results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#guestSearch, #guestSearchResults').length) {
                $('#guestSearchResults').hide();
            }
        });
        
        // Existing AJAX for room selection (single booking)
        $('#room_class').change(function() {
            const classId = $(this).val();
            const selectedText = $(this).find('option:selected').text();
            const priceMatch = selectedText.match(/\(([\d,.]+) RWF\)/);
            if (priceMatch && priceMatch[1]) {
                const price = priceMatch[1].replace(/,/g, '');
                $('#price').val(price);
            } else {
                $('#price').val('');
            }
            if (classId !== '') {
                $.ajax({
                    type: 'POST',
                    data: { 
                        class_id: classId,
                        checkin_date: $('input[name="checkin_date"]').val(),
                        checkout_date: $('input[name="checkout_date"]').val()
                    },
                    success: function(data) {
                        $('#room_number').html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error:", textStatus, errorThrown);
                        $('#room_number').html('<option value="">Error loading rooms</option>');
                    }
                });
            } else {
                $('#room_number').html('<option value="">Select Room Number</option>');
            }
        });

        // Refresh room numbers when single booking dates change
        $('input[name="checkin_date"], input[name="checkout_date"]').on('change', function(){
            const classId = $('#room_class').val();
            if (classId) {
                $('#room_class').trigger('change');
            }
        });

        // --- Multiple Booking Form Logic ---

        $('#showSingleBookingFormBtn').on('click', function() {
            $('#singleBookingForm').show();
            $('#multipleBookingForm').hide();
            $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
            $('#showMultipleBookingFormBtn').removeClass('active btn-primary').addClass('btn-outline-primary');
        });

        $('#showMultipleBookingFormBtn').on('click', function() {
            $('#singleBookingForm').hide();
            $('#multipleBookingForm').show();
            $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
            $('#showSingleBookingFormBtn').removeClass('active btn-primary').addClass('btn-outline-primary');
            // Generate fields for 1 guest initially when switching to multiple booking form
            generateGuestAndRoomFields($('#num_guests').val());
        });

        // Function to fetch room classes (reusable)
        function fetchRoomClasses(targetSelectId) {
            $.ajax({
                url: '', // Current page to handle AJAX
                type: 'POST',
                data: { get_room_classes: true },
                success: function(response) {
                    $(targetSelectId).html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error fetching room classes:", textStatus, errorThrown);
                    $(targetSelectId).html('<option value="">Error loading room classes</option>');
                }
            });
        }

        // Function to fetch room numbers based on class_id (reusable)
        // Optionally pass check-in/out dates to filter out already booked rooms
        function fetchRoomNumbers(classId, targetSelectId, checkin = '', checkout = '') {
            if (classId !== '') {
                $.ajax({
                    type: 'POST',
                    data: { class_id: classId, checkin_date: checkin, checkout_date: checkout },
                    success: function(data) {
                        $(targetSelectId).html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error fetching room numbers:", textStatus, errorThrown);
                        $(targetSelectId).html('<option value="">-- Select Room Number --</option>');
                    }
                });
            } else {
                $(targetSelectId).html('<option value="">-- Select Room Number --</option>');
            }
        }

        // Function to fetch room price based on room_id (reusable)
        function fetchRoomPrice(roomId, targetInputId) {
            if (roomId !== '') {
                $.ajax({
                    url: '', // Current page to handle AJAX
                    type: 'POST',
                    data: { room_id: roomId },
                    success: function(price) {
                        $(targetInputId).val(price);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error fetching room price:", textStatus, errorThrown);
                        $(targetInputId).val('');
                    }
                });
            } else {
                $(targetInputId).val('');
            }
        }

        // Function to fetch room statuses (reusable)
        function fetchRoomStatuses(targetSelectId) {
            $.ajax({
                url: '', // Current page to handle AJAX
                type: 'POST',
                data: { get_room_statuses: true },
                success: function(response) {
                    $(targetSelectId).html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error fetching room statuses:", textStatus, errorThrown);
                    $(targetSelectId).html('<option value="">Error loading statuses</option>');
                }
            });
        }

        $('#num_guests').on('input change', function() {
            const numGuests = $(this).val();
            generateGuestAndRoomFields(numGuests);
        });

        function generateGuestAndRoomFields(numGuests) {
            const container = $('#guests-and-rooms-container');
            container.empty(); // Clear previous fields

            if (numGuests > 0) {
                for (let i = 0; i < numGuests; i++) {
                    const guestIndex = i + 1;
                    const guestHtml = `
                        <div class="guest-section border p-3 mb-3">
                            <h6 class="mb-3 text-primary">Guest ${guestIndex} Room Selection</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="guest_room_class_${i}" class="form-label">Room Class <span class="text-danger">*</span></label>
                                    <select name="guest_room_class[]" id="guest_room_class_${i}" class="form-select" required>
                                        <option value="">-- Select Room Class --</option>
                                        <?php
                                        $classes = mysqli_query($conn, "SELECT * FROM tbl_acc_room_class");
                                        while ($row = mysqli_fetch_assoc($classes)) {
                                            $price = number_format($row['base_price'], 2);
                                            echo "<option value='{$row['id']}' data-price='{$row['base_price']}'>{$row['class_name']} <b>({$price} RWF)</b></option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="guest_room_number_${i}" class="form-label">Room Number <span class="text-danger">*</span></label>
                                    <select name="guest_room_number[]" id="guest_room_number_${i}" class="form-select" required>
                                        <option value="">-- Select Room Number --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="guest_price_${i}" class="form-label">Room Price per Night (RWF)</label>
                                    <input type="number" name="guest_price[]" id="guest_price_${i}" class="form-control" value="" placeholder="Room Price" readonly>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(guestHtml);

                    // Attach event listener for room class change
                    $(`#guest_room_class_${i}`).on('change', function() {
                        const classId = $(this).val();
                        const selectedOption = $(this).find('option:selected');
                        const price = selectedOption.data('price');
                        $(`#guest_price_${i}`).val(price);
                        const ci = $('input[name="multi_checkin_date"]').val();
                        const co = $('input[name="multi_checkout_date"]').val();
                        fetchRoomNumbers(classId, `#guest_room_number_${i}`, ci, co);
                    });
                }
            }
        }

        // Refresh all guest room-number selects when multiple booking dates change
        $('input[name="multi_checkin_date"], input[name="multi_checkout_date"]').on('change', function(){
            // For each dynamic guest block, if a class is selected, refetch numbers with new dates
            $("select[id^='guest_room_class_']").each(function(){
                const idAttr = $(this).attr('id');
                const idx = idAttr ? idAttr.split('_').pop() : null;
                if (idx !== null) {
                    const classId = $(this).val();
                    if (classId) {
                        const ci = $('input[name="multi_checkin_date"]').val();
                        const co = $('input[name="multi_checkout_date"]').val();
                        fetchRoomNumbers(classId, `#guest_room_number_${idx}`, ci, co);
                    }
                }
            });
        });

        // --- DataTable Initialization ---
            $('#bookingsTable').DataTable({
                "paging": false,
                "searching": true,
                "info": false,
                "lengthChange": false,
                "columnDefs": [
                    { "orderable": true, "targets": 5 }
                ]
            });
    });
    </script>
</body>
</html>
