<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Group Booking</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include "../inc/conn.php";



// --- AJAX HANDLERS --- //
if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    $query = mysqli_query($conn, "SELECT * FROM tbl_acc_room WHERE room_class_id = '$class_id' AND (status_id = 3 OR status_id = 12)");
    echo "<option value=''>-- Select Room Number --</option>";
    while ($row = mysqli_fetch_assoc($query)) {
        echo "<option value='{$row['id']}'>{$row['room_number']}</option>";
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





// Handle group booking
if(isset($_POST['addBook'])){
include "../inc/conn.php";

// Ensure the form was submitted and all required fields are present
    $first_name     = $_POST['first_name'];
    $last_name      = $_POST['last_name'];
    $checkin_date   = $_POST['checkin_date'];
    $checkout_date  = $_POST['checkout_date'];
    $room_class_id  = $_POST['room_class'];
    $room_id        = $_POST['room_number'];
    $room_status = $_POST['room_status'];
    $group_id       = $_GET['id'];

    $created_at     = date("Y-m-d H:i:s");
    
    // Select roomamount
    $sqll = mysqli_fetch_assoc(mysqli_query($conn, "SELECT base_price FROM tbl_acc_room_class WHERE id = '$room_class_id'"));

    // 1. Insert into tbl_acc_guest
    $insert_guest_sql = "INSERT INTO tbl_acc_guest (first_name, last_name, created_at, updated_at) VALUES (?, ?, ?, ?)";
    $stmt_guest = $conn->prepare($insert_guest_sql);
    $stmt_guest->bind_param("ssss", $first_name, $last_name, $created_at, $created_at);
    if (!$stmt_guest->execute()) {
        die("Failed to insert guest: " . $stmt_guest->error);
    }

    $guest_id = $stmt_guest->insert_id;
    
    

    // 2. Calculate duration
    $date1 = new DateTime($checkin_date);
    $date2 = new DateTime($checkout_date);
    $interval = $date1->diff($date2);
    $duration = $interval->days;

    // 3. Calculate booking amount
    // $booking_amount = $price_per_night * $duration;
    $booking_amount = $sqll['base_price'] * $duration;
    $price_per_night = $sqll['base_price'];
    
    
    // 4. Insert into tbl_acc_booking
    $insert_booking_sql = "INSERT INTO tbl_acc_booking 
        (guest_id, checkin_date, checkout_date, duration, num_adults, booking_amount, 
         booking_status_id, booking_type, room_price, created_at, group_id) 
         VALUES (?, ?, ?, ?, 1, ?, ?, 'Group', ?, ?, ?)";

    $stmt_booking = $conn->prepare($insert_booking_sql);
$stmt_booking->bind_param(
    "issididss", // ✅ only 8 type definitions 
    $guest_id,
    $checkin_date,
    $checkout_date,
    $duration,
    $booking_amount,
    $room_status,
    $price_per_night,
    $created_at,
    $group_id
);

    if (!$stmt_booking->execute()) {
        die("Failed to insert booking: " . $stmt_booking->error);
    }
$booking_id = $stmt_booking->insert_id;


    // Insert into tbl_acc_booking_room
    $insertBoking = "INSERT INTO tbl_acc_booking_room (booking_id, room_id ) VALUES(?,?)";
    $stmt_insertBooking = $conn->prepare($insertBoking);
    $stmt_insertBooking->bind_param("ii",$booking_id ,$room_id);
 if (!$stmt_insertBooking->execute()) {
        die("Failed to insert tbl_acc_booking_room: " . $stmt_insertBooking->error);
    }



    // Optionally, mark room as booked or update room status
    $update_room_sql = "UPDATE tbl_acc_room SET status_id = 2 WHERE id = ?";
    $stmt_update_room = $conn->prepare($update_room_sql);
    $stmt_update_room->bind_param("i", $room_id);
    $stmt_update_room->execute();

    // Redirect back to the group page
    echo "<script>window.location='?resto=group_bookings&&id=$group_id'</script>";
    exit();
} 


?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center">
    <h5>Group Booking</h5>
    <button id="showFormBtn" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addBookingModal">Add Booking</button>
  </div>

  <div class="table-responsive mt-3">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>Guest Names</th>
          <th>Nationality</th>
          <th>Room Number</th>
          <th>Room Class</th>
          <th>Payment Status</th>
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
            <td>" . ($row['payment_status_id'] == 2 ? '<b class="text-success">Paid</b>' : '<b class="text-danger">Unpaid</b>') . "</td>
            <td><a href='?resto=room_booking_details&&booking_id={$row['id']}'>View Booking</a></td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Popup Form -->
  
  <!-- Modal -->
<!-- Modal -->
        <div class="modal fade" id="addBookingModal" tabindex="-1" aria-labelledby="addBookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="" method="POST" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBookingModalLabel">Add Booking to Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="checkin_date" class="form-label">Check-in Date</label>
                                <?php $today = date('d-m-Y'); ?>
                                <input type="date" name="checkin_date" value="<?php echo $today;?>" max="<?php echo $today;?>" class="form-control" id="checkin_date" required >
                                <small id="formatted_date" class='text-info'></small>
                            </div>
                            <div class="col-md-6">
                                <label for="checkout_date" class="form-label">Check-out Date</label>
                                <input type="date" name="checkout_date" value="<?php echo $today;?>" max="<?php echo $today;?>" class="form-control"  id="checkout_date" required>
                                <small id="formattedd_date" class='text-info'></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="room_class" class="form-label">Room Class</label>
                                <select name="room_class" id="room_class" class="form-select" required>
                                    <option value="">-- Select Room Class --</option>
                                    <?php
                                    $classes = mysqli_query($conn, "SELECT * FROM tbl_acc_room_class");
                                    while ($row = mysqli_fetch_assoc($classes)) {
                                        echo "<option value='{$row['id']}'>{$row['class_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="room_number" class="form-label">Room Number</label>
                                <select name="room_number" id="room_number" class="form-select" required>
                                    <option value="">-- Select Room Number --</option>
                                </select>
                            </div>
                        </div>
                            <div class="col-md-12">
                                <label for="room_status" class="form-label">Room Status</label>
                                <select name="room_status" id="room_status" class="form-select" required>
                                    <option value="">-- Select Status --</option>
                                    <?php
                                    $classes = mysqli_query($conn, "SELECT * FROM tbl_acc_room_status");
                                    while ($row = mysqli_fetch_assoc($classes)) {
                                        echo "<option value='{$row['id']}'>{$row['status_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                        <!--<div class="mb-3">-->
                        <!--    <label for="price" class="form-label">Price Per Night</label>-->
                        <!--    <input type="text" name="price" id="price" class="form-control" readonly>-->
                        <!--</div>-->

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="addBook" class="btn btn-primary">Create Booking</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
 document.getElementById('showFormBtn').addEventListener('click', function () {
    var bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
    bookingModal.show();
  });

  // Existing AJAX
  $(document).ready(function() {
    $('#room_class').change(function() {
      var classId = $(this).val();
      if (classId !== '') {
        $.ajax({
          type: 'POST',
          data: {class_id: classId},
          success: function(data) {
            $('#room_number').html(data);
            $('#price').val('');
          }
        });
      }
    });

    $('#room_number').change(function() {
      var roomId = $(this).val();
      if (roomId !== '') {
        $.ajax({
          type: 'POST',
          data: {room_id: roomId},
          success: function(data) {
            $('#price').val(data.trim()); // .trim() removes whitespace
          }
        });
      }
    });
  });
  
  
  
  document.getElementById('checkin_date').addEventListener('change', function() {
    const date = new Date(this.value);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    document.getElementById('formatted_date').textContent = `Selected date: ${day}-${month}-${year}`;
});
  document.getElementById('checkout_date').addEventListener('change', function() {
    const date = new Date(this.value);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    document.getElementById('formattedd_date').textContent = `Selected date: ${day}-${month}-${year}`;
});
  
</script>
</body>
</html>