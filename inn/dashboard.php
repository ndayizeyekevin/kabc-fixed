<?php


$sql1 = $db->prepare("SELECT * FROM tbl_tables");
$sql1->execute();
$rowcount1 = $sql1->rowCount();

$sql = $db->prepare("SELECT * FROM tbl_cmd WHERE status_id != '12' GROUP BY OrderCode");
$sql->execute();
$rowcount = $sql->rowCount();

$sql2 = $db->prepare("SELECT * FROM tbl_cmd WHERE status_id = '12' GROUP BY OrderCode");
$sql2->execute();
$rowcount2 = $sql2->rowCount();

// $sql2 = $db->prepare("SELECT * FROM tbl_categories WHERE status_id = '12' GROUP BY OrderCode");
// $sql2->execute();
// $rowcount2 = $sql2->rowCount();
?>
<div class="colr-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="color-wrap">
                        <div class="row">
                            <a href="?resto=users" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink">
                                    <h1><i class="fa fa-user"></i></h1>
                                    <p>Users</p>
                                </div>
                            </a>
                            <a href="?resto=menu" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-bars"></i></h1>
                                    <p>Menu</p>
                                </div>
                            </a>
                            <a href="?resto=menu_drink" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-beer"></i></h1>
                                    <p>Drinks</p>
                                </div>
                            </a>
                            <a href="?resto=table" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-table"></i></h1>
                                    <p>Table</p>
                                </div>
                            </a>
                        </div>
                        <br>
                        <div class="row">
                        <a href="?resto=lorder" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0"> 
                                    <h1><i class="fa fa-cart-plus"></i></h1>
                                    <p>Orders</p>
                                </div>
                            </a>
                            <a href="?resto=report" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-utensils"></i></h1>
                                    <p>Restaurent</p>
                                </div>
                            </a>
                            <a href="?resto=reportb" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-martini-glass"></i></h1>
                                    <p>Bar</p>
                                </div>
                            </a>
                            <a href="?resto=reportc" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-boxes"></i></h1>
                                    <p>Stock</p>
                                </div>
                            </a>
                            
                           
                        
                            
                        </div>
                        <br>
                         <div class="row">
                            <a href="../room/index?resto=allRoom" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink">
                                    <h1><i class="fa-solid fa-door-open"></i></h1>
                                    <p>All Rooms</p>
                                </div>
                            </a>
                            <a href="../room/index?resto=guest" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-users"></i></h1>
                                    <p>Guests</p>
                                </div>
                            </a>
                            <a href="../room/index?resto=block" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-layer-group"></i></h1>
                                    <p>Room Block</p>
                                </div>
                            </a>
                            <a href="../room/index?resto=room_class_bed_type" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-table"></i></h1>
                                    <p>Room class type</p>
                                </div>
                            </a>
                        </div>
                        <br>
                        <div class="row">
                        <a href="../room/index?resto=roomstatus" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-info-circle"></i></h1>
                                    <p>Room status</p>
                                </div>
                            </a>
                            <a href="../room/index?resto=bedtype" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-bed"></i></h1>
                                    <p>Bed type</p>
                                </div>
                            </a>
                            <a href="../room/index?resto=room_class_feature" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-star"></i></h1>
                                    <p>Room features</p>
                                </div>
                            </a>
                            <a href="../room/index?resto=venue_list" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-location-dot"></i></h1>
                                    <p>Venue List</p>
                                </div>
                            </a>
                            
                           
                        
                            
                        </div>
                        <br>
                        
                        
                         <div class="row">
                        <a href="../room/index?resto=venueRates" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-tags"></i></h1>
                                    <p>Venue Rates</p>
                                </div>
                            </a>
                            <a href="../room/index?resto=eventType" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-champagne-glasses"></i></h1>
                                    <p>Event Type</p>
                                </div>
                            </a>
                           
                           
                        <a href="index?resto=addactivity" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-door-open"></i></h1>
                                    <p>Room Activities</p>
                                </div>
                            </a>
                            
                        </div>
                        
                        
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
