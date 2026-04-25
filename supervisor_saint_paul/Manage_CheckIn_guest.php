<?php
// include "../reception/Manage_CheckIn_guest.php";

// Cancel Reservation
if (isset($_GET['b'])) {
    try {
        $b = 4;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE `tbl_reservation` SET `status` = :status WHERE `reservation_id` = :id";
        $didq = $db->prepare($sql);
        $didq->execute([':status' => $b, ':id' => $_GET['b']]);
        $msge = "Reservation Cancelled successfully";
        echo '<meta http-equiv="refresh" content="2;URL=?resto=mngeResrv">';
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

// View Reservation Details
else if (isset($_GET['d'])) {
    try {
        $reservation_ID = $_GET['d'];
        $todaysdate = date("Y-m-d H:i:s");

        $stmt_room = $db->prepare("SELECT * FROM `tbl_reservation` WHERE reservation_id = :id");
        $stmt_room->execute([':id' => $reservation_ID]);
        $row_room = $stmt_room->fetch();
        $rrr = $row_room['roomID'];
        $ggg = $row_room['guest_id'];

        $stmt_r = $db->prepare("SELECT * FROM `tbl_rooms` WHERE room_id = :id");
        $stmt_r->execute([':id' => $rrr]);
        $row_r = $stmt_r->fetch();
        $price = $row_r['price'];
        $price_fbu = $row_r['price_fbu'];

        $stmt_guest = $db->prepare("SELECT * FROM `guest` WHERE guest_id = :id");
        $stmt_guest->execute([':id' => $ggg]);
        $row_guest = $stmt_guest->fetch();
        $country = $row_guest['country'];

        $f_price = ($country != 15) ? $price : $price_fbu;

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>

<!-- Data Table area Start-->
<div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="data-table-list">
                    <div class="basic-tb-hd">
                        <h2><small><strong><i class="fa fa-users"></i> Manage Our Guest</strong></small></h2>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="">
                        <div class="row">
                            <div class="form-group col">
                                <label for="start_date">Start Date:</label>
                                <input class="form-control" type="date" id="start_date" name="start_date"
                                    value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                            </div>

                            <div class="form-group col">
                                <label for="end_date">End Date:</label>
                                <input class="form-control" type="date" id="end_date" name="end_date"
                                    value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
                            </div>

                            <div class="form-group col">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                                    <button type="button" class="btn btn-info"
                                        onclick="window.location.href='<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>'">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <?php
                    $totprice = 0;
                    $where_clause = "";

                    // Get the last closed day from the days table
                    $sql_last_day = $db->prepare("SELECT * FROM days WHERE closed_at IS NOT NULL ORDER BY closed_at DESC LIMIT 1");
                    $sql_last_day->execute();
                    $last_day = $sql_last_day->fetch(PDO::FETCH_ASSOC);

                    // Check if user selected a date range
                    $user_selected_date = !empty($_GET['start_date']) && !empty($_GET['end_date']);

                    if ($user_selected_date) {
                        // User selected a date range - find the day boundaries (opened_at and closed_at)
                        $start_date = $_GET['start_date'];
                        $end_date = $_GET['end_date'];

                        // Find the earliest opened_at before or on the start date
                        $sql_start_day = $db->prepare("
                            SELECT opened_at
                            FROM days
                            WHERE DATE(opened_at) <= :start_date
                            AND opened_at IS NOT NULL
                            ORDER BY opened_at ASC
                            LIMIT 1
                        ");
                        $sql_start_day->execute([':start_date' => $start_date]);
                        $start_day = $sql_start_day->fetch(PDO::FETCH_ASSOC);

                        // Find the latest closed_at after or on the end date
                        $sql_end_day = $db->prepare("
                            SELECT closed_at
                            FROM days
                            WHERE DATE(closed_at) >= :end_date
                            AND closed_at IS NOT NULL
                            ORDER BY closed_at DESC
                            LIMIT 1
                        ");
                        $sql_end_day->execute([':end_date' => $end_date]);
                        $end_day = $sql_end_day->fetch(PDO::FETCH_ASSOC);

                        // Build the where clause based on day boundaries
                        if ($start_day && $end_day) {
                            // Both boundaries found - filter between opened_at and closed_at
                            $time_start = $start_day['opened_at'];
                            $time_end = $end_day['closed_at'];
                            echo "<p><strong>Showing records from " . date('Y-m-d H:i:s', strtotime($time_start)) . " to " . date('Y-m-d H:i:s', strtotime($time_end)) . "</strong></p>";
                            $where_clause = "tbl_cmd_qty.created_at BETWEEN '$time_start' AND '$time_end'";
                        } elseif ($start_day) {
                            // Only start boundary found - filter from opened_at onwards
                            $time_start = $start_day['opened_at'];
                            echo "<p><strong>Showing records from " . date('Y-m-d H:i:s', strtotime($time_start)) . " onwards</strong></p>";
                            $where_clause = "tbl_cmd_qty.created_at >= '$time_start'";
                        } elseif ($end_day) {
                            // Only end boundary found - filter up to closed_at
                            $time_end = $end_day['closed_at'];
                            echo "<p><strong>Showing records up to " . date('Y-m-d H:i:s', strtotime($time_end)) . "</strong></p>";
                            $where_clause = "tbl_cmd_qty.created_at <= '$time_end'";
                        } else {
                            // No day boundaries found - fallback to date-only filter
                            echo "<p><strong>Showing records from $start_date to $end_date (no day boundaries found)</strong></p>";
                            $where_clause = "DATE(tbl_cmd_qty.created_at) BETWEEN '$start_date' AND '$end_date'";
                        }
                    } else {
                        // No date selected - show only data AFTER the last closed_at
                        if ($last_day && $last_day['closed_at']) {
                            $time_filter_start = $last_day['closed_at'];
                            echo "<p><strong>Showing records after last closing: " . date('Y-m-d H:i:s', strtotime($time_filter_start)) . "</strong></p>";
                            $where_clause = "tbl_cmd_qty.created_at > '$time_filter_start'";
                        } else {
                            // No day closed yet - show all data
                            echo "<p><strong>Showing all records (no day closed yet)</strong></p>";
                            $where_clause = "1=1";
                        }
                    }

                    $sql_rooms = $db->prepare("SELECT *, tbl_cmd_qty.created_at,
                        (SELECT status_id FROM tbl_cmd WHERE OrderCode = tbl_cmd_qty.cmd_code LIMIT 1) as payment_status
                        FROM `tbl_cmd_qty`
                        INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                        WHERE $where_clause
                        GROUP BY cmd_code
                        ORDER BY
                            CASE WHEN (SELECT status_id FROM tbl_cmd WHERE OrderCode = tbl_cmd_qty.cmd_code LIMIT 1) = 12 THEN 1 ELSE 0 END ASC,
                            tbl_cmd_qty.created_at ASC");

                    $i = 0;
                    $sql_rooms->execute();

                    // Initialize totals
                    $total_all = 0;
                    $total_paid = 0;
                    $total_credit = 0;
                    ?>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Table No</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($fetrooms = $sql_rooms->fetch()) {
                                    $i++;
                                    $totprice += $fetrooms['menu_price'];

                                    $stmts = $db->prepare("SELECT * FROM tbl_status WHERE id = :id");
                                    $stmts->execute([':id' => $fetrooms['cmd_status']]);
                                    $fsts = $stmts->fetch();

                                    // Check if this order has been paid (invoice generated - status_id = 12)
                                    $cmd_code = $fetrooms['cmd_code'];
                                    $check_paid = $db->prepare("SELECT status_id, creadit_user, room_client FROM tbl_cmd WHERE OrderCode = :code LIMIT 1");
                                    $check_paid->execute([':code' => $cmd_code]);
                                    $paid_row = $check_paid->fetch();
                                    $is_paid = ($paid_row && $paid_row['status_id'] == 12) ? true : false;

                                    // Check if this is a credit order (posted on credit or added to room without actual payment)
                                    $is_credit = false;
                                    if ($is_paid) {
                                        // Check if order was added to room or credit client
                                        if ($paid_row['room_client'] || $paid_row['creadit_user']) {
                                            // Check if there are any actual payments (non-credit) in payment_tracks
                                            $check_payment = $db->prepare("SELECT COUNT(*) as payment_count FROM payment_tracks WHERE order_code = :code AND (remark IS NULL OR remark != 'creadit')");
                                            $check_payment->execute([':code' => $cmd_code]);
                                            $payment_row = $check_payment->fetch();
                                            $is_credit = ($payment_row['payment_count'] == 0);
                                        }
                                    }

                                    // Calculate total amount for this order
                                    $order_total = 0;
                                    $sql_total = $db->prepare("SELECT SUM(menu_price * cmd_qty) as total FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item WHERE cmd_code = :code");
                                    $sql_total->execute([':code' => $cmd_code]);
                                    $row_total = $sql_total->fetch();
                                    $order_total = $row_total['total'] ?? 0;

                                    // Add to totals
                                    $total_all += $order_total;
                                    if ($is_paid) {
                                        if ($is_credit) {
                                            $total_credit += $order_total;
                                        } else {
                                            $total_paid += $order_total;
                                        }
                                    }
                                ?>
                                    <tr class="gradeU">
                                        <td><?php echo $i; ?></td>
                                        <td>
                                            <?php echo $fetrooms['table_no']; ?> |
                                            <?php
                                            $d = $fetrooms['Serv_id'];
                                            $sql_roomss = $db->prepare("SELECT * FROM tbl_users WHERE user_id = :id");
                                            $sql_roomss->execute([':id' => $d]);
                                            $fetroomss = $sql_roomss->fetch();
                                            echo $fetroomss ? $fetroomss['f_name'] : '';
                                            ?>
                                        </td>
                                        <td><?php echo $fetrooms['created_at']; ?></td>
                                        <td>
                                            <?php
                                            $sql_roomsss = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = :code GROUP BY OrderCode");
                                            $sql_roomsss->execute([':code' => $fetrooms['cmd_code']]);
                                            $fetroomsss = $sql_roomsss->fetch();
                                            echo $fetroomsss ? $fetroomsss['id'] : '';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($is_paid && $is_credit) { ?>
                                                <!-- Order is on credit - show Credit button -->
                                                <span class="label label-warning">Room Credit</span>
                                            <?php } else if ($is_paid) { ?>
                                                <!-- Order is paid - show Paid button -->
                                                <span class="label label-success">Paid</span>
                                            <?php } else if ($fetrooms['cmd_status']) { ?>
                                                <!-- Order is not paid - show Details and Hand over buttons -->
                                                <a href="?resto=gstDet&res=<?php echo $fetrooms['cmd_table_id']; ?>&c=<?php echo $fetrooms['cmd_code']; ?>"
                                                    onclick="return confirm('Do you really want to View Details?');"
                                                    class="label label-primary">Details</a>

                                                <a href="?resto=transfer&res=<?php echo $fetrooms['Serv_id']; ?>&c=<?php echo $fetrooms['cmd_code']; ?>"
                                                    class="label label-primary">Hand over</a>
                                                <a href="?resto=gstInvce&resrv=<?php echo $fetrooms['cmd_table_id'];?>&c=<?php echo $fetrooms['cmd_code'];?>" onclick="if(!confirm('Do you really want to Checkout This Order?'))return false;else return true;" class="label label-info">Checkout</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #f0f0f0; font-weight: bold;">
                                    <td colspan="3" style="text-align: right;">Total (All Orders):</td>
                                    <td colspan="2"><?php echo number_format($total_all, 2); ?></td>
                                </tr>
                                <tr style="background-color: #d4edda; font-weight: bold;">
                                    <td colspan="3" style="text-align: right;">Total (Paid Orders):</td>
                                    <td colspan="2"><?php echo number_format($total_paid, 2); ?></td>
                                </tr>
                                <tr style="background-color: #fff3cd; font-weight: bold;">
                                    <td colspan="3" style="text-align: right;">Total (Room Credit):</td>
                                    <td colspan="2"><?php echo number_format($total_credit, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- End Table -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Data Table area End-->

<!-- Example Modal (kept for reference) -->
<div id="update<?php echo isset($fetrooms['reservation_id']) ? $fetrooms['reservation_id'] : ''; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h2 class="modal-title"><i class="fa fa-pencil"></i> <?php echo isset($cpny_ID) ? $cpny_ID : ''; ?> Profile</h2>
            </div>

            <div class="modal-body">
                <form id="settings_form" method="POST" enctype="multipart/form-data" class="form-horizontal form-bordered">
                    <fieldset style="margin-top: 10px;">
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="">Firstname</label>
                            <div class="col-md-8">
                                <input type="text" id="cpname" name="cpname" class="form-control"
                                    value="<?php echo isset($cmp_full) ? $cmp_full : ''; ?>" placeholder="Enter Company Name..">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="">Company Email</label>
                            <div class="col-md-8">
                                <input type="Email" id="cpemail" name="cpemail" class="form-control"
                                    value="<?php echo isset($cpny_email) ? $cpny_email : ''; ?>" placeholder="Enter Email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="">Phone</label>
                            <div class="col-md-8">
                                <input type="text" id="cphone" name="cphone" class="form-control"
                                    value="<?php echo isset($cpny_phone) ? $cpny_phone : ''; ?>" placeholder="Company Phone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="">Address</label>
                            <div class="col-md-8">
                                <input type="text" id="cpny_address" name="cpny_address" class="form-control"
                                    value="<?php echo isset($cpny_address) ? $cpny_address : ''; ?>" placeholder="Company Address">
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group form-actions" style="margin-top: 10px;">
                        <div class="col-xs-12 text-right">
                            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" name="btn-profile" class="btn btn-sm btn-primary">
                                <i class="fa fa-pencil-square-o"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
