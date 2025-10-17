<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">

<!-- Bootstrap Select CSS -->
<link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
<?php

$selected_date = $_GET['date'] ?? 'CURDATE()';


$stmt_req = $db->prepare("SELECT * FROM tbl_item_stock WHERE item=1");
$stmt_req->execute();
$getrows = $stmt_req->fetch();
$limit = $getrows['qty'] ?? 0;
if (isset($_POST['add'])) {
  $request = $_POST['requested'];
  $required = $_POST['required'];
  $item = $_POST['items'];
  $quantity = $_POST['quantity'];
  $remark = $_POST['remark'];
  $user = $_SESSION['user_id'];
  $dept = $_SESSION['log_role'];
  $req_code = $_POST['req_code'];


  $sql = $db->prepare("INSERT INTO `tbl_requests`(`req_code`,`requested_date`, `required_date`, `remark`,`department`,`user_id`,`status`)
    VALUES ('$req_code','$request','$required','$remark','$dept','$user','3')");
  $sql->execute();

  for ($x = 0; $x < count($item); $x++) {

    $sql = $db->prepare("INSERT INTO `tbl_request_details`(`req_code`, `items`, `quantity`)
    VALUES ('$req_code','$item[$x]','$quantity[$x]')");
    $sql->execute();
  }
  $msg = "Requested Successfuly!";
  echo '<meta http-equiv="refresh"' . 'content="2;URL=?resto=request">';

}
?>
<?php
if (isset($_POST['add_item'])) {
  $item = $_POST['item'];
  $qty = $_POST['quantity'];
  $code = $_POST['code'];

  $stmt = $db->prepare("SELECT * FROM tbl_request_details WHERE items = '" . $item . "'");
  $stmt->execute();
  $count = $stmt->rowCount();
  if ($count > 0) {
    $msge = "Item Is On List!";
    echo '<meta http-equiv="refresh"' . 'content="2;URL=?resto=request">';
  } else {
    $sql = $db->prepare("INSERT INTO `tbl_request_details`(`req_code`, `items`, `quantity`)
    VALUES ('$code','$item','$qty')");
    $sql->execute();
    $msg = "Added Successfuly!";
    echo '<meta http-equiv="refresh"' . 'content="2;URL=?resto=request">';
  }
}
?>
<?php
if (isset($_GET['rmv'])) {
  $detail_id = $_GET['rmv'];
  $sql_rmv = $db->prepare("DELETE FROM tbl_request_details WHERE detail_id = '" . $detail_id . "'");
  $sql_rmv->execute();
  $msge = "Removed Successfully";
  echo '<meta http-equiv="refresh"' . 'content="2;URL=?resto=request">';
}
?>
<?php
if (isset($_GET['del'])) {
  $del_code = $_GET['del'];

  $sql_del = $db->prepare("DELETE FROM tbl_requests WHERE req_code = '" . $del_code . "'");
  $sql_del->execute();

  $sql_del2 = $db->prepare("DELETE FROM tbl_request_details WHERE req_code = '" . $del_code . "'");
  $sql_del2->execute();
  $msge = "Removed Successfully";
  echo '<meta http-equiv="refresh"' . 'content="2;URL=?resto=request">';
}
?>
<?php
function fill_product($db)
{
  $output = '';

  $select = $db->prepare("SELECT * FROM tbl_items WHERE item_status = 1");
  $select->execute();
  $result = $select->fetchAll();

  foreach ($result as $row) {
    $output .= '<option value="' . $row['item_id'] . '">' . $row["item_name"] . '</option>';
  }

  return $output;
}
?>
<?php
function productcode()
{
  return getReqCode($_SESSION['user_id']);
}
$pcode = productcode();
?>
<style>
  .form-ic-cmp {
    margin-bottom: 10px;
  }
</style>


<!-- Breadcomb area Start-->
<div class="breadcomb-area">
  <div class="container">

    <?php if ($msg) { ?>
      <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
      </div>
    <?php } else if ($msge) { ?>

        <div class="alert alert-danger alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
        </div>
    <?php } ?>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="breadcomb-list">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <div class="breadcomb-wp">
                <div class="breadcomb-icon">
                  <i class="fa fa-cog"></i>
                </div>
                <div class="breadcomb-ctn">
                  <h2>Request Raw Materials</h2>
                  <p>Welcome to <?php echo $Rname; ?> <span class="bread-ntd">Panel</span></p>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
              <div hidden class="breadcomb-report">
                <button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left"
                  title="Add Request" class="btn"><i class="fa fa-plus-circle"></i> Add Request</button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- <div class="container my-3">
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#addRequestForm"
    aria-expanded="false" aria-controls="addRequestForm">
    <i class="fa fa-plus-circle"></i> Add New Request
  </button>
</div> -->

<!-- <div class="collapse" id="addRequestForm"> -->
  <div class="card p-3 mb-4 container border-1 border-secondary shadow-lg">
    <h4>Add New Request</h4>
    <form action="" method="POST">
      <div class="form-row mb-3">
        <div class="form-group col-md-4">
          <label><strong>Requested Date</strong></label>
          <input type="date" max="<?php echo date("Y-m-d") ?>" class="form-control" name="requested" required>
          <input type="hidden" name="req_code" value="<?php echo $pcode; ?>">
        </div>
        <div class="form-group col-md-4">
          <label><strong>Required Date</strong></label>
          <input type="date" min="<?php echo date("Y-m-d") ?>" class="form-control" name="required" required>
        </div>
        <div class="form-group col-md-4">
          <label><strong>Remark</strong></label>
          <textarea class="form-control" name="remark" required></textarea>
        </div>
      </div>

      <!-- Item selection -->
      <div class="form-row align-items-end mb-3">
        <div class="form-group col-md-6">
          <label>Select Item</label>
          <select id="itemSelect" class="form-control" data-live-search="true">
            <option value="">--Choose Item--</option>
            <?php echo fill_product($db); ?>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label>Quantity</label>
          <input type="number" id="itemQty" class="form-control" min="1" placeholder="0">
        </div>
        <div class="form-group col-md-3">
          <button type="button" id="addItem" class="btn btn-success btn-block">Add</button>
        </div>
      </div>

      <!-- Selected items -->
      <table class="table table-bordered" id="orderList">
        <thead>
          <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <!-- Hidden inputs container -->
      <div id="hiddenInputs"></div>

      <div class="text-center my-2">
        <button type="submit" name="add" class="btn btn-info"><i class="fa fa-save"></i> Save Request</button>
      </div>
    </form>
  </div>
<!-- </div> -->


<div class="divider mb-4">

</div>


<script>
  $(document).ready(function () {

    $("#addItem").click(function () {
      var itemId = $("#itemSelect").val();
      var itemText = $("#itemSelect option:selected").text();
      var qty = $("#itemQty").val();

      if (itemId && qty > 0) {
        // Add to table
        var row = '<tr>';
        row += '<td>' + itemText + '</td>';
        row += '<td>' + qty + '</td>';
        row += '<td><button type="button" class="btn btn-danger btn-sm removeItem">Remove</button></td>';
        row += '</tr>';

        $("#orderList tbody").append(row);

        // Add hidden inputs for PHP
        $("#hiddenInputs").append('<input type="hidden" name="items[]" value="' + itemId + '">');
        $("#hiddenInputs").append('<input type="hidden" name="quantity[]" value="' + qty + '">');

        // Reset selection
        $("#itemSelect").val('').selectpicker('refresh');
        $("#itemQty").val('');
      } else {
        alert("Please select an item and enter a valid quantity.");
      }
    });

    // Remove item
    $(document).on("click", ".removeItem", function () {
      var index = $(this).closest("tr").index();

      // Remove corresponding hidden inputs
      $("#hiddenInputs input").eq(index * 2).remove(); // item
      $("#hiddenInputs input").eq(index * 2).remove(); // qty

      // Remove row
      $(this).closest("tr").remove();
    });

  });
</script>






<?php
$date_from = $_POST['date_from'] ?? null;
$date_to = $_POST['date_to'] ?? null;
if (isset($_POST['date_from']) & isset($_POST['date_to'])) {
  $q = "DATE(tbl_requests.requested_date) >= '$date_from'
        AND DATE(tbl_requests.requested_date) <= '$date_to'";
} else {
  $q = "DATE(tbl_requests.requested_date) = '$selected_date'";
}
?>
<!-- Data Table area Start-->
<div class="data-table-area">
  <div class="container">
    <div class="d-flex gap-4">
      <form method="POST" class="form-inline">
        <label for="date_from">Start date:</label>
        <input type="date" id="date_from" name="date_from" class="form-control"
          value="<?= htmlspecialchars($date_from) ?>">
        <label for="date_to">End date:</label>
        <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>">
        <button type="submit" name="process" id="process" class="btn btn-info btn-sm">Search</button>
      </form>
      <?php include "../inc/date_filter.php"; ?>
    </div>

    <button id="printH"> Print </button>


    <div class="text-nowrap table-responsive">
      <div id="content">
        <?php include '../holder/printHeader.php' ?>
        <div class="table-responsive">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="data-table-list">
                <?php if (empty($_GET['req'])) { ?>
                  <div class="table-responsive">
                    <table id="data-table-basic" class="table table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Request By</th>
                          <th>Requested Date</th>
                          <th>Required Date</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $department = $_SESSION['log_role'] ?? '';

                        $i = 0;
                        $sql = "
    SELECT *
    FROM tbl_requests
    INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
    INNER JOIN tbl_status ON tbl_requests.status = tbl_status.id
    WHERE department = :department
    AND $q
    ORDER BY req_id DESC
";

                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(':department', $department);


                        $stmt->execute();
                        while ($fetch = $stmt->fetch()) {
                          $i++;
                          ?>
                          <tr>
                            <td><?php echo $i; ?></td>
                            <td><a
                                href="?resto=request&req=<?php echo $fetch['req_code'] ?>"><?php echo $fetch['f_name'] . " " . $fetch['l_name']; ?></a>
                            </td>
                            <td><?php echo $fetch['requested_date']; ?></td>
                            <td><?php echo $fetch['required_date']; ?></td>
                            <td><?php echo $fetch['status_name']; ?></td>
                            <td>
                              <?php if ($fetch['status'] == '3') { ?>
                                <a class="btn-sm" href="?resto=request&del=<?php echo $fetch['req_code'] ?>"
                                  onclick="if(!confirm('Do you really want to Delete This Request?'))return false;else return true;">Delete</a>
                              <?php } ?>
                            </td>
                          </tr>
                          <?php
                        }
                        ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th>#</th>
                          <th>Request By</th>
                          <th>Requested Date</th>
                          <th>Required Date</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>

                  <script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>


                  <!-- <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script> -->
                  <script type="text/javascript">
                    $(document).ready(function () {

                      $("#printH").click(function () {
                        $("#headerprint").show();
                        var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents;
                      });
                      $("#headerprint").hide();
                      //      $("#date_to").change(function () {
                      //          var from = $("#date_from").val();
                      //          var to = $("#date_to").val();
                      // $(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');
                      //          $.post('load_sales_report.php',{from:from,to:to} , function (data) {
                      //           $("#display_res").html(data);
                      //              $('#loader').slideUp(910, function () {
                      //                  $(this).remove();
                      //              });
                      //              location.reload();
                      //          });
                      //      });

                    });
                  </script>

                  <?php
                } elseif (!empty($_GET['req'])) {
                  $code = $_GET['req'];
                  $stmt_req = $db->prepare("SELECT * FROM tbl_requests
                            INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
                            WHERE req_code = '" . $_GET['req'] . "'");
                  $stmt_req->execute();
                  $getrows = $stmt_req->fetch();
                  $status = $getrows['status'];
                  ?>
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <div class="panel-title pull-left">
                        Type: Raw Materials <br>
                        Date Requested: <?php echo $getrows['requested_date']; ?>
                      </div>
                      <div class="panel-title pull-right">
                        Requested By: <?php echo $getrows['f_name'] . " " . $getrows['l_name']; ?><br>
                        Date Required: <?php echo $getrows['required_date']; ?>
                      </div>
                      <div class="clearfix"></div>
                    </div>
                  </div>
                  <hr>
                  <div class="panel panel-default" style="padding:10px;">
                    <div class="panel-heading">
                      <div class="panel-title pull-left">
                        <?php if ($getrows['status'] == 3) { ?>
                          Requested Items <label class="label label-info"><b><i>PENDING</i></b></label>
                        <?php
                        } else {
                          ?>
                          Requested Items <label class="label label-success"><b><i>COMPLETED</i></b></label>
                          <?php
                        }
                        ?>
                      </div>

                      <div class="panel-title pull-right">
                        <?php
                        if ($status == 3) {
                          ?><button class="btn btn-default" data-toggle="modal" data-target="#adds">
                            Add Item
                          </button>
                          <button class="btn btn-default" data-toggle="modal" data-target="#rmv">
                            Issues Item
                          </button>
                        <?php } ?>
                      </div>

                      <div class="clearfix"></div>
                    </div>
                    <br>
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                      <thead>
                        <tr>
                          <th> # </th>
                          <th> Item Name </th>
                          <th> Quantity </th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php
                        $ii = 0;
                        $result = $db->prepare("SELECT * FROM tbl_request_details
                        INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
                        WHERE req_code = '" . $_GET['req'] . "'");
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                          $ii++;
                          ?>
                          <tr>
                            <td><?php echo $ii; ?></td>
                            <td><?php echo $row['item_name']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                          </tr>
                          <?php
                        }
                        ?>

                      </tbody>
                    </table>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add Request</h4>
            </div>
            <div class="modal-body">
              <form action="" enctype="multipart/form-data" method="POST">
                <div class="row">
                  <div class="form-group">
                    <label class="col-md-2 control-label" for=""><strong>Requested Date</strong><span
                        class="text-danger">*</span></label>
                    <div class="col-md-4">
                      <input autocomplete="off" type="date" max="<?php echo date("Y-m-d") ?>" class="form-control"
                        name="requested" required>
                      <input autocomplete="off" type="hidden" class="form-control" name="req_code"
                        value="<?php echo $pcode; ?>" required>
                    </div>
                    <label class="col-md-2 control-label" for=""><strong>Required Date</strong></label>
                    <div class="col-md-4">
                      <input autocomplete="off" type="date" min="<?php echo date("Y-m-d") ?>" class="form-control"
                        name="required" required>
                    </div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="form-group">
                    <label class="col-md-2 control-label" for=""><strong>Remark</strong></label>
                    <div class="col-md-4">
                      <textarea class="form-control" name="remark" required></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12">
                      <table class="table table-border" id="myOrder">
                        <thead>
                          <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>
                              <button type="button" name="addOrder" class="btn btn-success btn-sm btn_addOrder"
                                required><span>
                                  <i class="fa fa-plus"></i>
                                </span></button>
                            </th>
                          </tr>

                        </thead>
                        <tbody>

                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="form-actions col-md-12">
                      <br />
                      <center>
                        <button type="submit" name="add" id="" class="btn btn-sm label-info margin"
                          style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
                        <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i>
                          Reset</button>
                      </center>
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- add item -->
      <div class="modal fade" id="adds" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST" action="">
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Item Name</label>
                  <select class="form-control selectpicker" data-live-search="true" name="item" required>
                    <?php
                    $sql = $db->prepare("SELECT * FROM tbl_items");
                    $sql->execute();
                    for ($i = 0; $row = $sql->fetch(); $i++) {
                      ?>
                      <option value="<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="message-text" class="col-form-label">Quantity</label>
                  <input type="number" name="quantity" class="form-control" required>
                  <input type="hidden" name="code" class="form-control" value="<?php echo $_GET['req']; ?>">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="add_item" class="btn btn-success">Add</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="rmv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Item Issues</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST" action="">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                  <thead>
                    <tr>
                      <th> # </th>
                      <th> Item Namee </th>
                      <th> Quantity </th>
                      <th> Action </th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $ii = 0;
                    $result = $db->prepare("SELECT * FROM tbl_request_details
                        INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
                        WHERE req_code = '" . $_GET['req'] . "'");
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                      $ii++;
                      ?>
                      <tr>
                        <td><?php echo $ii; ?></td>
                        <td><?php echo $row['item_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><a href="?resto=request&req=<?php echo $code; ?>&rmv=<?php echo $row['detail_id']; ?>">Remove
                        </td>
                      </tr>
                      <?php
                    }
                    ?>

                  </tbody>
                </table>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <!-- <button type="submit" name="add_item" class="btn btn-success">Add</button> -->
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>  -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJ+Y5D3aQ5JxG2sGZ8qKJq7CXr3x9t6k0C2QA=" crossorigin="anonymous"></script>
      <!-- Bootstrap Bundle (includes Popper.js, required for modals) -->
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2LcRWqEvMZ5YhSjcYx04Ec+mUYjqD1p6J8V8WvCWP+"
        crossorigin="anonymous"></script>
      <!-- Bootstrap Select -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

      <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

      <!-- Bootstrap Bundle (includes Popper.js, required for modals) -->
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2LcRWqEvMZ5YhSjcYx04Ec+mUYjqD1p6J8V8WvCWP+"
        crossorigin="anonymous"></script>

      <!-- Bootstrap Select -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

      <!-- <script>
$(document).ready(function () {

    // ✅ Print function
    $("#printH").click(function(){
        $("#headerprint").show();
        var printContents = document.getElementById('content').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $("#headerprint").hide();
    });

    // ✅ Add new order row dynamically
    $(document).on('click','.btn_addOrder', function(){
        var html = '';
        html += '<tr>';
        html += '<td><select class="form-control selectpicker productid" data-live-search="true" name="items[]" required>';
        html += '<option value="">--Select Item--</option>';
        html += '<?php //echo fill_product($db); ?>';
        html += '</select></td>';
        html += '<td><input autocomplete="off" type="number" min="0" class="form-control quantity" name="quantity[]" required placeholder="0"></td>';
        html += '<td><button type="button" name="remove" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-remove"></i></button></td>';
        html += '</tr>';

        $('#myOrder tbody').append(html);

        // refresh bootstrap-select after adding
        $('.selectpicker').selectpicker('refresh');
    });

    // ✅ Remove row
    $(document).on('click','.btn-remove', function(){
        $(this).closest('tr').remove();
    });

    // ✅ Handle product change (ajax example)
    $(document).on('change','.productid', function(){
        var productid = this.value;
        var tr = $(this).closest('tr');
        $.ajax({
            url: "getproduct.php",
            method: "get",
            data: {id: productid},
            success: function(data){
                tr.find(".quantity").val('');
                tr.find("#unit").text(data["unit_name"]);
            }
        });
    });

});
</script> -->