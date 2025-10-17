<?php
include '../inc/conn.php';

// Base date (default: today)
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>
<div class="colr-area">
    <div class="container">
        <h5 class="card-header">ROOMS STATUS ON <?php echo htmlspecialchars($date); ?></h5>

        <!-- Date Picker -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" id="date-search-form" class="row g-3">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Select Date</label>
                        <input type="date" class="form-control" id="date" name="date"
                               value="<?php echo htmlspecialchars($date); ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="?resto=room_inventory" class="btn btn-secondary ms-2">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Room Grid -->
        <div id="room-grid">
            <?php include 'room_inventory_table.php'; ?>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-light-pink { background-color: #f8d7da !important; }
    .bg-steel-blue { background-color: #4682b4 !important; color: #fff !important; }
    .bg-golden-yellow { background-color: #ffd700 !important; }
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function () {
    $("#date-search-form").on("submit", function (e) {
        e.preventDefault(); // stop normal submit

        let date = $("#date").val();

        $.ajax({
            url: "room_inventory_table.php",
            type: "GET",
            data: { date: date },
            success: function (response) {
                $("#room-grid").html(response);
            },
            error: function () {
                alert("Failed to load room data.");
            }
        });
    });
});
</script>
