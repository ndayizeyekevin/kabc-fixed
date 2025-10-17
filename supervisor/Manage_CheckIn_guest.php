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
                    // ✅ Default to show only today's records
                    $totprice = 0;
                    $where_clause = "tbl_cmd_qty.cmd_status != '12'";

                    if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
                        $start_date = $_GET['start_date'];
                        $end_date = $_GET['end_date'];
                        echo "<p><strong>Showing records from $start_date to $end_date</strong></p>";
                        $where_clause .= " AND DATE(tbl_cmd_qty.created_at) BETWEEN '$start_date' AND '$end_date'";
                    } else {
                        $today = date('Y-m-d');
                        echo "<p><strong>Showing today's records ($today)</strong></p>";
                        $where_clause .= " AND DATE(tbl_cmd_qty.created_at) = '$today'";
                    }

                    $sql_rooms = $db->prepare("SELECT *, tbl_cmd_qty.created_at 
                        FROM `tbl_cmd_qty`
                        INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                        WHERE $where_clause
                        GROUP BY cmd_code
                        ORDER BY cmd_table_id ASC");

                    $i = 0;
                    $sql_rooms->execute();
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
                                            <?php if ($fetrooms['cmd_status']) { ?>
                                                <a href="?resto=gstDet&res=<?php echo $fetrooms['cmd_table_id']; ?>&c=<?php echo $fetrooms['cmd_code']; ?>"
                                                    onclick="return confirm('Do you really want to View Details?');"
                                                    class="label label-primary">Details</a>
                                            <?php } ?>

                                            <a href="?resto=transfer&res=<?php echo $fetrooms['Serv_id']; ?>&c=<?php echo $fetrooms['cmd_code']; ?>"
                                                class="label label-primary">Hand over</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
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
