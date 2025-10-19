<?php
require_once("../inc/conn.php");
require_once("../inc/DBController.php");

$success_message = $error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_currency"])) {
    $name = $_POST["name"];
    $exchange = $_POST["currency_exchange"];
    $sign = $_POST["currency_sign"];
    $created_at = date('Y-m-d H:i:s');

    try {
        $stmt = $conn->prepare("INSERT INTO currencies (name, currency_exchange, currency_sign, created_at) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sdss", $name, $exchange, $sign, $created_at);
            if ($stmt->execute()) {
                $success_message = "<strong>Currency added successfully.</strong>";
            } else {
                $error_message = "<strong>Failed to add currency.</strong>";
            }
        } else {
            $error_message = "<strong>Database error: " . $conn->error . "</strong>";
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_exchange"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $exchange = $_POST["currency_exchange"];
    $sign = $_POST["currency_sign"];

    try {
        $stmt = $conn->prepare("UPDATE currencies SET name = ?, currency_exchange = ?, currency_sign = ? WHERE currency_id = ?");
        if ($stmt) {
            $stmt->bind_param("sdsi", $name, $exchange, $sign, $id);
            if ($stmt->execute()) {
                $success_message = "<strong>Currency updated successfully.</strong>";
            } else {
                $error_message = "<strong>Failed to update currency.</strong>";
            }
        } else {
            $error_message = "<strong>Database error: " . $conn->error . "</strong>";
        }
    } catch (Exception $e) {
        $error_message = "<strong>" . $e->getMessage() . "</strong>";
    }
}

$result = $conn->query("SELECT * FROM currencies ORDER BY created_at DESC");
$currencies = $result->fetch_all(MYSQLI_ASSOC);
$counter = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Currencies Management</title>

  <!-- Bootstrap 3.4.1 -->
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->

  <!-- Font Awesome -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->

  <!-- DataTables -->
  <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap.min.css"> -->

  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> -->
  <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
  <!-- <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script> -->
  <!-- <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap.min.js"></script> -->
</head>
<body>
<div class="container" style="margin-top:50px;">
  <div class="text-center">
    <h2><strong>Currencies Management</strong></h2>
  </div>

  <?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <?= $success_message ?>
    </div>
  <?php elseif ($error_message): ?>
    <div class="alert alert-danger alert-dismissible">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <?= $error_message ?>
    </div>
  <?php endif; ?>

  <div class="text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addCurrencyModal">
      <i class="fa fa-plus"></i> Add New Currency
    </button>
  </div>

  <!-- Add Currency Modal -->
  <div class="modal fade" id="addCurrencyModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add New Currency</h4>
        </div>
        <div class="modal-body">
          <form method="POST">
            <div class="form-group">
              <label>Currency Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Exchange Rate</label>
              <input type="number" step="0.01" name="currency_exchange" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Currency Sign</label>
              <input type="text" name="currency_sign" class="form-control" required>
            </div>
            <div class="text-right">
              <button type="submit" name="add_currency" class="btn btn-primary">Add Currency</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Currencies Table -->
  <h3 class="text-center" style="margin-top:40px;">Currencies List</h3>
  <table id="currencyTable" class="table table-striped table-bordered">
    <thead>
    <tr class="success text-center">
      <th>#</th>
      <th>Name</th>
      <th>Exchange Rate</th>
      <th>Sign</th>
      <th>Created</th>
      <th>Updated</th>
      <th>Action</th>
    </tr>
    </thead>
    <tbody class="text-center">
    <?php foreach ($currencies as $row): ?>
      <tr>
        <td><?= $counter++ ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= number_format($row['currency_exchange'], 2) ?></td>
        <td><strong><?= htmlspecialchars($row['currency_sign']) ?></strong></td>
        <td><?= $row['created_at'] ?></td>
        <td><?= $row['updated_at'] ?></td>
        <td>
          <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $row['currency_id'] ?>">
            <i class="fa fa-pencil"></i> Edit
          </button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Edit Modals -->
<?php foreach ($currencies as $row): ?>
<div class="modal fade" id="editModal<?= $row['currency_id'] ?>" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Currency</h4>
      </div>
      <div class="modal-body">
        <form method="POST">
          <input type="hidden" name="id" value="<?= $row['currency_id'] ?>">
          <div class="form-group">
            <label>Currency Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
          </div>
          <div class="form-group">
            <label>Exchange Rate</label>
            <input type="number" step="0.01" name="currency_exchange" class="form-control" value="<?= $row['currency_exchange'] ?>" required>
          </div>
          <div class="form-group">
            <label>Currency Sign</label>
            <input type="text" name="currency_sign" class="form-control" value="<?= htmlspecialchars($row['currency_sign']) ?>" required>
          </div>
          <div class="text-right">
            <button type="submit" name="update_exchange" class="btn btn-success">Save Changes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<script>
$(document).ready(function() {
  $('#currencyTable').DataTable({
    "pageLength": 5,
    "lengthMenu": [5, 10, 25, 50, 100],
    "ordering": true
  });
});
</script>
</body>
</html>
