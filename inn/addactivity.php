<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../inc/config.php");

$msg = '';
$msge = '';

// Handle new activity creation
if (isset($_POST['add'])) {
    $service_name = trim($_POST['service_name']);
    $service_price = trim($_POST['service_price']);

    if ($service_name === '' || $service_price === '') {
        $msge = "Error: All fields are required.";
    } else {
        try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Check for duplicate service name
            $stmt = $db->prepare("SELECT * FROM services WHERE service_name = ?");
            $stmt->execute([$service_name]);
            if ($stmt->fetch()) {
                $msge = "Error: Service name already exists.";
            } else {
                $sql = "INSERT INTO services (service_name, service_price) VALUES (?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$service_name, $service_price]);
                $msg = "Successfully Added!";
                echo '<meta http-equiv="refresh" content="0;URL=index?resto=addactivity">';
            }
        } catch (PDOException $e) {
            $msge = "Error: " . $e->getMessage();
        }
    }
}

// Handle activity deletion
if (isset($_GET['deleteId'])) {
    $id = $_GET['deleteId'];
    try {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $db->prepare("DELETE FROM services WHERE service_id = ?");
        $sql->execute([$id]);
        $msg = "Deleted Successfully!";
        echo '<meta http-equiv="refresh" content="0;URL=index?resto=addactivity">';
    } catch (PDOException $e) {
        $msge = "Error: " . $e->getMessage();
    }
}
?>
<style>
    .form-ic-cmp{ margin-bottom:10px; }
</style>
<div class="breadcomb-area">
    <div class="container">
        <?php if($msg){?>
          <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
          </div>
        <?php } 
         else if($msge){?>
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
                                    <h2>Manage Activities</h2>
                                    <p>Add new activities/services for rooms</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
                            <div class="breadcomb-report">
                                <button type="button" data-toggle="modal" data-target="#addActivityModal" class="btn"><i class="fa fa-plus-circle"></i> Add Activity</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="data-table-list">
                    <div class="table-responsive">
                        <table id="data-table-basic" class="table table-striped">
                             <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Activity Name</th>
                                    <th>Price</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                $sql = $db->prepare("SELECT * FROM services ORDER BY service_id DESC");
                                $sql->execute();
                                while($fetch = $sql->fetch()){
                                    $i++;
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo htmlentities($fetch['service_name']); ?></td>
                                    <td><?php echo number_format($fetch['service_price']); ?></td>
                                    <td><?php echo htmlentities($fetch['created_at']); ?></td>
                                    <td>
                                        <a class="btn-sm" href="?resto=addactivity&deleteId=<?php echo $fetch['service_id'] ?>" onclick="return confirm('Do you really want to Delete This Activity?');">Delete</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add new activity modal -->
<div class="modal fade" id="addActivityModal" role="dialog">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title">Add New Activity</h4>
        </div>
        <div class="modal-body">
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="row">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for=""><strong>Activity Name</strong><span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="service_name" class="form-control" required>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for=""><strong>Price</strong><span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" name="service_price" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-actions col-md-12">
                        <br />
                        <center>
                            <button type="submit" name="add" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
                            <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
