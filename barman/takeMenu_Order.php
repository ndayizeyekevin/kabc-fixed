<script>
    // Auto-refresh the page every 60 seconds
    setTimeout(function() {
        window.location.reload();
    }, 60000);
</script>

<!-- Data Table area Start-->
<div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="data-table-list">
                <?php
                // Main query: get all active orders for category 2
                $sql = $db->prepare("
                    SELECT tbl_cmd_qty.*, menu.*, category.*
                    FROM tbl_cmd_qty
                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                    INNER JOIN category ON menu.cat_id = category.cat_id
                    WHERE tbl_cmd_qty.cmd_status = '13' AND category.cat_id = '2'
                    GROUP BY tbl_cmd_qty.cmd_table_id
                ");
                $sql->execute();
                $rowcount = $sql->rowCount();

                if ($rowcount > 0) {
                    // Fetch both associative and numeric indexes
                    while ($fetch = $sql->fetch(PDO::FETCH_BOTH)) {

                        // Get basic info from main query
                        $reservation_id = $fetch['cmd_table_id'];
                        $code = $fetch['cmd_code'];
                        $status_id = $fetch['cmd_status'];
                        $Serv_id = $fetch['Serv_id'];

                        // Get service user details
                        $GetServ = $db->prepare("SELECT * FROM tbl_users WHERE user_id = ?");
                        $GetServ->execute([$Serv_id]);
                        $fServ = $GetServ->fetch(PDO::FETCH_BOTH);

                        // Get table info
                        $stmtss = $db->prepare("SELECT * FROM tbl_tables WHERE table_id = ?");
                        $stmtss->execute([$reservation_id]);
                        $rooms = $stmtss->fetch(PDO::FETCH_BOTH);
                        $room_no = $rooms['table_no'];

                        // Display info
                        $badge = '<h5><small><span class="badge" style="background-color: #dd5252;">New</span></small></h5>';
                        $service = $fServ['f_name'] . " " . $fServ['l_name'];
                        ?>
                        
                        <!-- Order card -->
                        <a href="?resto=prcsOrder_list&m=<?php echo $reservation_id; ?>&c=<?php echo $code; ?>" 
                           onclick="return confirm('Do you really want to view this order?');">
                            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 menu-box-container">
                                <div class="menu-box">
                                    <!-- Header -->
                                    <div class="menu-box-head">
                                        <div class="pull-left">
                                            <?php echo htmlspecialchars($fetch[10]); ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <!-- Body -->
                                    <div class="menu-box-content referrer">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td align="center">
                                                            <h4>Table No <?php echo htmlspecialchars($room_no) . " " . $badge; ?></h4>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Footer -->
                                        <div class="menu-box-foot text-center">
                                            <h5><small><?php echo htmlspecialchars($service); ?></small></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <?php
                    }
                } else {
                    echo "
                        <div class='alert alert-info'>
                            <strong>Info!</strong> No Order Found!
                        </div>
                    ";
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!-- Data Table area End-->
