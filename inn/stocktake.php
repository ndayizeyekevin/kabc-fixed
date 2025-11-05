<?php
include '../inc/conn.php';

// Check if user is logged in
// session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">You must be logged in to perform this action</div>';
    exit();
}

// echo $_SESSION['user_id'];


if(isset($_POST['save_stocktake'])) {
    $item_id = $_POST['item_id'];
    $counted_qty = $_POST['counted_qty'];
    $notes = $_POST['notes'];

    $stocktake_date = date("Y-m-d");
    $stocktake_by = $_SESSION['user_id'];

    // Verify user exists in tbl_users
    $check_user = $db->prepare("SELECT user_id FROM tbl_users WHERE user_id = ?");
    $check_user->execute([$stocktake_by]);
    if (!$check_user->fetch()) {
        echo '<div class="alert alert-danger">Invalid user session. Please log in again.</div>';
        exit();
    }

    try {
        // begin transaction
        $db->beginTransaction();

        // select current qty with row lock
        $sql = "SELECT qty FROM tbl_item_stock WHERE item = ? FOR UPDATE";
        $stmt = $db->prepare($sql);
        $stmt->execute([$item_id]);
        $current_stock = $stmt->fetch(PDO::FETCH_ASSOC);
        $system_qty = $current_stock ? $current_stock['qty'] : 0;

        // Calculate variance
        $variance = $counted_qty - $system_qty;

        // Insert stock take record
        $sql = "INSERT INTO tbl_stocktake (item_id, system_qty, counted_qty, variance, stocktake_date, notes, stocktake_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$item_id, $system_qty, $counted_qty, $variance, $stocktake_date, $notes, $stocktake_by]);

        // Update or insert stock row if needed
        if ($variance != 0) {
            if ($current_stock) {
                $sql = "UPDATE tbl_item_stock SET qty = ? WHERE item = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$counted_qty, $item_id]);
            } else {
                $sql = "INSERT INTO tbl_item_stock (item, qty, date_tkn) VALUES (?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$item_id, $counted_qty, $stocktake_date]);
            }

            // prepare progress entry
            $remark = ($variance > 0) ? "Stock take adjustment (increase)" : "Stock take adjustment (decrease)";
            if ($variance > 0) {
                $in_qty = $variance; 
                $out_qty = 0;
            } else {
                $in_qty = 0; 
                $out_qty = abs($variance);
            }
            $sql = "INSERT INTO tbl_progress (date, in_qty, last_qty, out_qty, item, end_qty, remark) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$stocktake_date, $in_qty, $system_qty, $out_qty, $item_id, $counted_qty, $remark]);
        }

        $db->commit();

        $msg = "Stock take successfully recorded!";
        echo '<meta http-equiv="refresh" content="1;URL=index?resto=stock_takes">';
    } catch (Exception $e) {
        if ($db->inTransaction()) { $db->rollBack(); }
        error_log('Stocktake transaction error: ' . $e->getMessage());
        echo '<div class="alert alert-danger">An error occurred while saving the stock take. Please try again later.</div>';
    }
}
?>

<!-- Breadcomb area Start-->
<div class="breadcomb-area">
    <div class="container">
        <?php if(isset($msg)) { ?>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
        </div>
        <?php } ?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="breadcomb-list">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="breadcomb-wp">
                                <div class="breadcomb-icon">
                                    <i class="fa fa-balance-scale"></i>
                                </div>
                                <div class="breadcomb-ctn">
                                    <h2>Stock Take</h2>
                                    <p>Record physical stock count and reconcile with system quantities</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
                            <div class="breadcomb-report">
                                <button type="button" data-toggle="modal" data-target="#stockTakeModal" class="btn btn-success">
                                    <i class="fa fa-plus-circle"></i> New Stock Take
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Take Modal -->
<div class="modal fade" id="stockTakeModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Stock Take Entry</h4>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="stockTakeForm">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label"><strong>Select Item <span class="text-danger">*</span></strong></label>
                        <div class="col-md-8">
                            <select name="item_id" id="item_id" class="form-control chosen" required>
                                <option value="">Select Item...</option>
                                <?php
                                $stmt = $db->query('SELECT i.item_id, i.item_name, s.qty as current_qty, u.unit_name 
                                                  FROM tbl_items i 
                                                  LEFT JOIN tbl_item_stock s ON i.item_id = s.item
                                                  LEFT JOIN tbl_unit u ON i.item_unit = u.unit_id
                                                  ORDER BY i.item_name');
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="'.$row['item_id'].'" data-current="'.$row['current_qty'].'" data-unit="'.$row['unit_name'].'">'
                                         .$row['item_name'].' (Current: '.$row['current_qty'].' '.$row['unit_name'].')</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label"><strong>Current System Qty</strong></label>
                        <div class="col-md-8">
                            <input type="text" id="system_qty" class="form-control" readonly>
                            <small class="text-muted">This is the current quantity in the system</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label"><strong>Counted Quantity <span class="text-danger">*</span></strong></label>
                        <div class="col-md-8">
                            <input type="number" name="counted_qty" id="counted_qty" class="form-control" step="0.001" required>
                            <small class="text-muted">Enter the physically counted quantity</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label"><strong>Variance</strong></label>
                        <div class="col-md-8">
                            <input type="text" id="variance" class="form-control" readonly>
                            <small class="text-muted">Difference between system and counted quantities</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label"><strong>Notes</strong></label>
                        <div class="col-md-8">
                            <textarea name="notes" class="form-control" rows="3" placeholder="Enter any notes about the stock take..."></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12 text-center">
                            <button type="submit" name="save_stocktake" class="btn btn-success" onclick="return confirm('Are you sure you want to save this stock take?');">
                                <i class="fa fa-save"></i> Save Stock Take
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize chosen select
    $('.chosen').chosen();

    // Update system quantity when item is selected
    $('#item_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var currentQty = selectedOption.data('current') || 0;
        $('#system_qty').val(currentQty);
        calculateVariance();
    });

    // Calculate variance when counted quantity changes
    $('#counted_qty').on('input', function() {
        calculateVariance();
    });

    function calculateVariance() {
        var systemQty = parseFloat($('#system_qty').val()) || 0;
        var countedQty = parseFloat($('#counted_qty').val()) || 0;
        var variance = countedQty - systemQty;
        $('#variance').val(variance.toFixed(3));
        
        // Change variance color based on value
        if(variance < 0) {
            $('#variance').css('color', 'red');
        } else if(variance > 0) {
            $('#variance').css('color', 'green');
        } else {
            $('#variance').css('color', 'black');
        }
    }
});
</script>