<?php
error_reporting(0);
include '../inc/conn.php';

// Add new corporate
if (isset($_POST['addCorporate'])) {
    $tin = mysqli_real_escape_string($conn, $_POST['tin']);
    $corporate = mysqli_real_escape_string($conn, $_POST['corporate']);

    $sql = "INSERT INTO corporates (`id`, `name`, `tin_number`) VALUES (NULL, '$corporate', '$tin')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Corporate Added')</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<style>
    .form-ic-cmp {
        margin-bottom: 10px;
    }
</style>

<div class="breadcomb-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Corporates Management</h2>
                <p>Add and manage corporate clients for invoicing and bookings.</p>
            </div>
        </div>
    </div>
</div>

<div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Corporate</h4>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><strong>Corporate Name</strong><span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input autocomplete="off" name="corporate" type="text" class="form-control" placeholder="Corporate Name" required>
                                    </div>
                                    <label class="col-md-2 control-label"><strong>TIN Number</strong></label>
                                    <div class="col-md-4">
                                        <input autocomplete="off" name="tin" type="text" placeholder="TIN Number" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <input type="submit" name="addCorporate"  value="Add Corporate" class="btn btn-primary text-dark">
                        </form>
                    </div>
                </div>

                <hr>

                <div class="data-table-list">
                    <div class="table-responsive">
                        <table id="data-table-basic" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Corporate Name</th>
                                    <th>TIN Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                $result = $conn->query("SELECT * FROM corporates");
                                while ($fetch = $result->fetch_assoc()) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo htmlspecialchars($fetch['name']); ?></td>
                                        <td><?php echo htmlspecialchars($fetch['tin_number']); ?></td>
                                        <td>
                                            <a href="corporates.php?delete=<?php echo $fetch['id']; ?>" onclick="return confirm('Delete this corporate?')">Delete</a>
                                            | <a href="corporates_edit.php?id=<?php echo $fetch['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php
                                // Handle delete
                                if (isset($_GET['delete'])) {
                                    $del_id = intval($_GET['delete']);
                                    $conn->query("DELETE FROM corporates WHERE id = $del_id");
                                    echo "<script>window.location='corporates.php';</script>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
