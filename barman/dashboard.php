<div class="container-fluid">
    <!-- Start Status area -->
    <div class="notika-status-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <p>Company : <?php echo ""; ?></p>
                        </div>
                        <div class="sparkline-bar-stats1">9,4,8,6,5,6,4,8,3,5,9,5</div>
                    </div>
                </div>
                <?php
                $sql1 = $db->prepare("SELECT * FROM tbl_tables");
                $sql1->execute();
                $rowcount1 = $sql1->rowCount();
                ?>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?php echo $rowcount1; ?></span></h2>
                            <p>Total Tables</p>
                        </div>
                    </div>
                </div>
                <?php
                $sql = $db->prepare("SELECT * FROM tbl_cmd WHERE status_id != '12' GROUP BY OrderCode");
                $sql->execute();
                $rowcount = $sql->rowCount();
                ?>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?php echo $rowcount; ?></span></h2>
                            <p>Pending Reservation</p>
                        </div>
                    </div>
                </div>
                <?php
                $sql2 = $db->prepare("SELECT * FROM tbl_cmd WHERE status_id = '12' GROUP BY OrderCode");
                $sql2->execute();
                $rowcount2 = $sql2->rowCount();
                ?>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?php echo $rowcount2; ?></span></h2>
                            <p>Total Reservation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Status area-->  
</div>