  <?php
include "switch_role.php";

?>

    <!-- Mobile Menu start -->
    <div class="mobile-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">
                          <?php
                            if($_SESSION['log_role'] == '0'){
                          ?>
                            <ul class="mobile-menu-nav">
                                <li><a data-toggle="collapse" data-target="#Charts" href="#">Home</a></li>
                                <li><a data-toggle="collapse" data-target="#settings" href="#">Settings</a>
                                    <ul id="settings" class="collapse dropdown-header-top">
                                        <li><a href="?resto=cpny">Manage Company</a></li>
                                        <li><a href="?resto=cpny_admin">System Admin</a></li>
                                        <li><a href="?resto=cpny_role">User Role</a></li>
                                    </ul>
                                </li>
                            </ul>
                         <?php
                          }
                         ?>

                    <?php
                    if($_SESSION['log_role'] == '1'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="mobile-menu-nav">
                      <li class="active"><a href="?resto=home">Dashboard</a></li>

                          <li><a data-toggle="collapse" data-target="#acc" href="#">Table</a>
                            <ul id="acc" class="collapse dropdown-header-top">
                                <li><a href="?resto=table">Manage Table</a></li>
                            </ul>
                          </li>

                           <li><a data-toggle="collapse" data-target="#resto" href="#">Restaurant </a>
                            <ul id="resto" class="collapse dropdown-header-top">
                                <li><a href="?resto=menu">Menu</a></li>
                                <li><a href="?resto=menu_drink">Drinks</a></li>
                                <li><a href="?resto=categ">Category</a></li>
                                <li><a href="?resto=subcateg">Sub-Category</a></li>
                            </ul>
                          </li>
        	             <li><a href="?resto=users" >Users</a></li>

                         <li><a data-toggle="collapse" data-target="#acccc" href="#">Reports</a>
                            <ul id="acccc" class="collapse dropdown-header-top">
                                <li><a href="?resto=report">Restaurant</a></li>
                                <li><a href="?resto=reportb">Bar</a></li>
                                <li><a href="?resto=reportc">Stock</a></li>
                                  <!--<li><a href="?resto=reportc">Stock</a></li>-->


                            </ul>
                          </li>
                    </ul>

                    <?php
                    }
                    ?>

                    <!--finance-->
                    <?php
                    if($_SESSION['log_role'] == '2'){
                    ?>
                    <ul class="mobile-menu-nav">
                        <li><a data-toggle="collapse" data-target="#Charts" href="#">Home</a></li>
                        <!-- <li><a data-toggle="collapse" data-target="#settings" href="#">Expenses</a>
                            <ul id="settings" class="collapse dropdown-header-top">
                                <li><a href="?resto=expenses">Record Expenses</a></li>
                            </ul>
                        </li> -->
                    </ul>

                    <?php } ?>

                    <!--MD-->
                    <?php
                    if($_SESSION['log_role'] == '9'){
                    ?>
                    <ul class="mobile-menu-nav">
                        <li><a data-toggle="collapse" data-target="#Charts" href="#">Home</a></li>
                    </ul>

                    <?php } ?>

                     <?php
                    if($_SESSION['log_role'] == '4'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="mobile-menu-nav">
                        <li><a data-toggle="collapse" data-target="#dashboard" href="#">Dashboard</a></li>
                        <li><a data-toggle="collapse" data-target="#order" href="?resto=prcsOrder">Order
                        <div class="spinner4 spinner-4 scrn-budge-spinner"></div>
                        <div class="ntd-ctn scrn-budge">
                        <span style="color: #f2f2f2;"><?php echo $tot; ?></span>
            		        </div></a></li>

            		  <li><a data-toggle="collapse" data-target="#order" href="?resto=prcsOrder_prcssng">Processing
                        <div class="spinner4 spinner-4 scrn-budge-spinner"></div>
                        <div class="ntd-ctn scrn-budge">
                        <span style="color: #fff;"><?php echo $tots; ?></span>
            		        </div></a></li>
            		    <li><a href="?resto=menu"><i class="fa fa-file"></i> Menu</a></li>
            		    <li><a data-toggle="collapse" data-target="#req" href="#">Request </a>
                            <ul id="req" class="collapse dropdown-header-top">
                                <li><a href="?resto=request">Request Raw Materials</a></li>
                            </ul>
                          </li>
            		  <li><a href="?resto=report"><i class="fa fa-sticky-note-o"></i> Report</a></li>
                    </ul>

                    <?php } ?>

                     <?php
                    if($_SESSION['log_role'] == '5'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="mobile-menu-nav">
                        <li><a data-toggle="collapse" data-target="#dashboard" href="#">Dashboard</a></li>
                        <li><a data-toggle="collapse" data-target="#order" href="?resto=prcsOrder">Order
                        <li><a href="?resto=lorder"><i class="fa fa-list"></i>Order List <div class="spinner4 spinner-4 budge-spinner" style="margin-left:-15px;"></div><div class="ntd-ctn budge" style="margin-left:-15px;"><span style="color: #f2f2f2;"><?php echo $ordercount; ?></span></div></a></li>
                        <li><a href="?resto=norder">New Order</a></li>
                        <div class="spinner4 spinner-4 scrn-budge-spinner"></div>
                        <div class="ntd-ctn scrn-budge">
                        <span style="color: #f2f2f2;"><?php echo $tot2; ?></span>
            		        </div></a></li>

            		  <li><a href="?resto=menu"><i class="fa fa-file"></i> Menu</a></li>
            		  <li><a href="?resto=report"><i class="fa fa-sticky-note-o"></i> Report</a></li>
                    </ul>

                    <?php } ?>

                     <?php
                    if($_SESSION['log_role'] == '6'){
                    ?>
                    <ul class="mobile-menu-nav">
                        <li><a href="?resto=home">Dashoard</a></li>
                        <li><a href="?resto=norder">New Order</a></li>
                        <li><a href="?resto=lorder">Order List  <div class="spinner4 spinner-4 budge-spinner" style="margin-left:-15px;"></div><div class="ntd-ctn budge" style="margin-left:-15px;"><span style="color: #f2f2f2;"><?php echo $ordercount; ?></span></div></a></li>
                        <li><a href="?resto=menu">Menu</a></li>
                        <li><a data-toggle="collapse" data-target="#rprt1" href="#">Report</a>
                        <ul id="rprt1" class="collapse dropdown-header-top">
                            <li><a href="?resto=report">Restaurent</a></li>
                        </ul>
                    </li>
                    </ul>
                    <?php
                    }
                    ?>

                     <?php
                    if($_SESSION['log_role'] == '7'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="mobile-menu-nav">
                        <li><a href="?resto=home">Dashoard</a></li>
                        <li><a href="?resto=stock"><i class="fa fa-home"></i> Sotck</a></li>
                    </ul>
                    <?php
                    }
                    ?>

                    <?php
                    if($_SESSION['log_role'] == '10' || $_SESSION['log_role']==12){
                    ?>
                    <ul class="mobile-menu-nav">
                    <li><a data-toggle="collapse" data-target="#Dash" href="?resto=home">Dashboard</a></li>
                    <!--<li><a data-toggle="collapse" data-target="#reserv" href="?resto=mngeResrv">Reservation <div class="spinner4 spinner-4 budge-spinner"></div><div class="ntd-ctn budge"><span style="color: #f2f2f2;"><?php echo $tot_reserv; ?></span></div></a></li>-->
                    <li><a data-toggle="collapse" data-target="#Guest" href="?resto=OurGste">Our Guest <div class="spinner4 spinner-4 budge-spinner"></div><div class="ntd-ctn budge"><span style="color: #f2f2f2;"><?php echo $num; ?></span></div></a></li>
                    <li><a data-toggle="collapse" data-target="#rprt" href="#">Report</a>
                        <ul id="rprt" class="collapse dropdown-header-top">
                            <li><a href="?resto=report">Restaurent</a></li>
                        </ul>
                    </li>
                    </ul>
                    <?php
                    }
                    ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Mobile Menu end -->

<!-- Main Menu area start-->
    <div class="main-menu-area mg-tb-40">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                     <?php
                        if($_SESSION['log_role'] == '0'){
                        ?>
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                       <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro customize">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <li><a data-toggle="tab" href="#setting"><i class="fa fa-cog fa-spin"></i> Settings <i class="fa fa-angle-down"></i></a></li>
                    </ul>

                    <div class="tab-content custom-menu-content">
                        <div id="setting" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=cpny">Manage Company</a></li>
                                <li><a href="?resto=cpny_admin">System Admin</a></li>
                                <li><a href="?resto=cpny_role">User Role</a></li>
                            </ul>
                        </div>
                    </div>

                    <?php
                    }
                    ?>

                <?php
        if($_SESSION['log_role'] == '1'){
                ?>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <li><a data-toggle="tab" href="#accom"><i class="fa fa-table"></i> Table <i class="fa fa-angle-down"></i></a></li>
                        <li><a data-toggle="tab" href="#restrnt"><i class="fa fa-cutlery"></i> Restaurent <i class="fa fa-angle-down"></i></a></li>

        	             <li><a href="?resto=users"> <i class="fa fa-user"></i> Users</a></li>
        	                 <li><a href="?resto=suppliers" ><i class="fa fa-truck"></i> Suppliers</a></li>
                             <li><a href="?resto=corporates" ><i class="fa fa-building"></i> Corporates</a></li>
        	                       <!-- <li><a href="?resto=void" >Pending voided Items</a></li> -->
        	                 <li><a href="?resto=StoreRequests"> <i class="fa fa-store"></i> Store Requests</a></li>
                             <!-- new item for stock take -->
                         <li><a href="?resto=stock_takes"><i class="fa fa-calculator"></i> Stock Take </i></a></li>
                         <li><a data-toggle="tab" href="#rep"><i class="fa fa-cutlery"></i> Reports <i class="fa fa-angle-down"></i></a></li>
                         <!-- <li><a data-toggle="tab" href="#ebm"><i class="fa fa-cutlery"></i> EBM <i class="fa fa-angle-down"></i></a></li> -->
                         <li><a data-toggle="tab" href="#access"><i class="fa fa-chart-line"></i> Access As <i class="fa fa-angle-down"></i></a></li>
                          <li><a href="#" data-toggle="modal" data-target="#mySettingModel" data-placement="left" > <i class="fa fa-cog"></i> Settings</a></li>
                          <li class="py-2 ms-3"><a href="?resto=currencyIssue" class="btn btn-primary text-success py-2 px-3">💸 Manage Currencies</a></li>

        	          </ul>

                     <div class="tab-content custom-menu-content">
                        <div id="accom" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                 <li><a href="?resto=table">Manage Table</a></li>
                            </ul>
                        </div>

                        <div id="access" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                            <li class="dropdown">
                                <a href="?access_as=5" >
                                    Bar <i class="fa fa-angle-down"></i>
                                </a>

                            </li>
                        </ul>


                        <!--Receptionist Dropdown -->
                        <ul class="notika-main-menu-dropdown">
                        <li class="dropdown">
                        <a href="?access_as=3" class="dropdown-toggle" >
                            Receptionist </i>
                        </a>
                        </li>
                        </ul>


                        <!--Continue with other main menu items -->
                        <!--    Restaurant Menu -->
                        <ul class="notika-main-menu-dropdown">
                            <li class="dropdown">
                                <a href="?access_as=4" class="dropdown-toggle" >
                                    Restaurant </i>
                                </a>
                            </li>
                            </ul>


                        <ul class="notika-main-menu-dropdown">
                            <!--Store Dropdown -->
                            <li class="dropdown">
                                <a href="?access_as=7" class="dropdown-toggle">
                                    Store </i>
                                </a>
                            </li>
                        </ul>

                        <!--<li><a href="?../inn/index?resto=home">Admin</a></li>-->
                        <li><a href="?access_as=1">Admin</a></li>
                        </div>


                        <!-- RESTAURANT -->
                        <div id="restrnt" class="tab-pane notika-tab-menu-bg animated flipInX">
                                                    <ul class="notika-main-menu-dropdown">
                                                        <li><a href="?resto=menu">Menu</a></li>
                                                        <li><a href="?resto=menu_drink">Drinks</a></li>
                                                        <li><a href="?resto=categ">Category</a></li>
                                                        <li><a href="?resto=subcateg">Sub-Category</a></li>
                                                    </ul>
                        </div>


                        <!-- Reports -->
                        <div id="rep" class="tab-pane notika-tab-menu-bg animated flipInX">
                                                    <ul class="notika-main-menu-dropdown">
                                                        <li><a href="?resto=report">Restaurant</a></li>
                                                        <li><a href="?resto=reportb">Bar</a></li>
                                                        <li><a href="?resto=reportc">Stock</a></li>
                                                            <!--<li><a href="../room/index?resto=reportc">Rooms</a></li>-->
                                                        <li><a href="?resto=waiterReport">Report by waiter</a></li>
                                                        <li><a href="?resto=View_OrdderDet">Order Details</a></li>
                                                        <li><a href="?resto=active_booking">Active Booking</a></li>
                                                        <li><a href="?resto=booking_list">Booking List</a></li>
                                                        <li><a href="?resto=sales_report">Sales Report</a></li>
                                                        <li><a href="?resto=staff_report">Staff Report</a></li>
                                                        <li><a href="?resto=ReportByCategory">Report by Category</a></li>
                                                        <li><a href="?resto=ReportByDepertment">Report by Department</a></li>
                                                        <li><a href="?resto=addrequest">Add Request</a></li>
                                                        <li><a href="?resto=Manage_CheckIn_guest">Manage Check-In Guest</a></li>
                                                        <li><a href="?resto=receipt-checks">Receipt Checks</a></li>

                                                    </ul>
                        </div>





                        <!-- EBM -->
                          <div id="ebm" class="tab-pane notika-tab-menu-bg animated flipInX">
                              <ul class="notika-main-menu-dropdown">
                                  <li><a href="?resto=ebm_customer">EBM Customer</a></li>
                                  <li><a href="?resto=import"> Importation</a></li>
                                  <li><a href="?resto=purchase"> Purchase</a></li>
                                  <li><a href="?resto=stock"> Stock</a></li>
                                  <li><a href="?resto=stocktake"> Stock take</a></li>
                                  <li><a href="?resto=cumurative_stock">Cumulative Stock</a></li>
                              </ul>
                          </div>



                    </div>
                    <?php
                    }
                    ?>

                    <!--End Of Receiption-->

                    <!--finance-->
                    <?php
                    if($_SESSION['log_role'] == '2'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro customize">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <!-- View stock -->
                        <li><a href="?resto=stock_balance"><i class="fas fa-balance-scale"></i> Stock Balance</a></li>
                        <li><a href="?resto=sales"><i class="fa fa-cutlery"></i> Restaurant Sales</a></li>
                        <li><a href="?resto=room_sales"><i class="fa fa-bed"></i> View Room Sales</a></li>

                        <!-- <li><a data-toggle="tab" href="#setting"><i class="fas fa-cash-register"></i> Expenses <i class="fa fa-angle-down"></i></a></li> -->
                    </ul>

                    <!-- <div class="tab-content custom-menu-content">
                        <div id="setting" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=expenses">Record Expenses</a></li>
                            </ul>
                        </div>
                    </div> -->

                    <?php } ?>

                    <!--MD-->
                    <?php
                    if($_SESSION['log_role'] == '9'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro customize">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <li><a href="?resto=stock_balance"><i class="fas fa-balance-scale"></i> Stock Balance</a></li>
                        <li><a href="?resto=sales"><i class="fa fa-cutlery"></i> Restaurant Sales</a></li>
                        <li><a href="?resto=room_sales"><i class="fa fa-bed"></i> View Room Sales</a></li>
                    </ul>

                    <?php } ?>
                    <!--Admin-->

                     <?php
                     if($_SESSION['log_role'] == '4'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <li><a href="?resto=prcsOrder"><i class="fa fa-envelope"></i>Order
                        <div class="spinner4 spinner-4 scrn-budge-spinner"></div>
                        <div class="ntd-ctn scrn-budge">
                        <span style="color: #f2f2f2;"><?php echo $tot; ?></span>
            		        </div>
            		        </a>
            		        </li>
            		        <li><a href="?resto=prcsOrder_prcssng"><i class="fa fa-spinner fa-spin"></i> Processing
            		        <div class="spinner4 spinner-4 scrn-budge-spinner"  style="margin-left:30px;"></div>
            		        <div class="ntd-ctn scrn-budge" style="margin-left:30px;background-color:sandybrown;color:#fff;">
                                <span><?php echo $tots; ?></span>
            		        </div>
            		        </a>
            		        </li>
            		      <li><a href="?resto=menu"><i class="fa fa-file"></i> Menu</a></li>
            		      <li><a href="?resto=stock"><i class="fa fa-file"></i> Stock</a></li>

            		      <li><a data-toggle="tab" href="#chk"><i class="fa fa-shopping-cart"></i> Request <i class="fa fa-angle-down"></i></a></li>
            		      		  <li><a href="?resto=delivered"><i class="fa fa-truck"></i> Delivered</a></li>
            		      <li><a href="?resto=report"><i class="fa fa-sticky-note-o"></i> Report</a></li>

            		      <?php
            		      if (isset($_SESSION['real_admin_role'])): ?>
    <li><a href="?access_as=1" class="btn btn-outline-warning">Back to Admin</a></li>
<?php endif; ?>


	                </ul>

	                    <div class="tab-content custom-menu-content">
                        <div id="chk" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=request">Request Raw Materials</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content custom-menu-content">
                        <div id="controllers" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=active_booking">Active Booking</a></li>
                                <li><a href="?resto=addrequest">Add Request</a></li>
                                <li><a href="?resto=booking_list">Booking List</a></li>
                                <li><a href="?resto=breakfast">Breakfast</a></li>
                                <li><a href="?resto=cumurative_stock">Cumulative Stock</a></li>
                                <li><a href="?resto=details_summary">Details Summary</a></li>
                                <li><a href="?resto=Manage_CheckIn_guest">Manage Check-In Guest</a></li>
                                <li><a href="?resto=receipt-checks">Receipt Checks</a></li>
                                <li><a href="?resto=ReportByCategory">Report by Category</a></li>
                                <li><a href="?resto=ReportByDepertment">Report by Department</a></li>
                                <li><a href="?resto=sale_summary">Sale Summary</a></li>
                                <li><a href="?resto=sales_report">Sales Report</a></li>
                                <li><a href="?resto=staff_report">Staff Report</a></li>
                                <li><a href="?resto=stock_balance">Stock Balance</a></li>
                                <li><a href="?resto=supplier_report">Supplier Report</a></li>
                                <li><a href="?resto=suppliers">Suppliers</a></li>
                                <li><a href="?resto=View_OrdderDet">View Order Details</a></li>
                                <li><a href="?resto=void">Void</a></li>
                                <li><a href="?resto=waiter_report">Waiter Report</a></li>
                            </ul>
                        </div>
                    </div>
                     <?php
                    }
                    ?>
                     <?php
                     if($_SESSION['log_role'] == '13'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li><a href="?resto=request"><i class="fa fa-tachometer"></i> Dashboard</a></li>

            		      <li><a href="?resto=stock"><i class="fa fa-file"></i> Stock</a></li>

            		      <li><a data-toggle="tab" href="#chk"><i class="fa fa-shopping-cart"></i> Request <i class="fa fa-angle-down"></i></a></li>
            		      </ul>

	                    <div class="tab-content custom-menu-content">
                        <div id="chk" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=request">Request Raw Materials</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                     }
                    ?>





                       <?php
                     if($_SESSION['log_role'] == '3'){
                    ?>
                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro container px-5">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i>Dashboard</a></li>

            		      <li><a href="?resto=Guestsr"><i class="fa fa-file"></i> Guests </a></li>
                                <li><a href="?resto=ebm">EBM</a></li>
                            <li><a href="?resto=room_inventory"><i class="fa fa-bed"></i> Inventory view </a></li>
            
            		      <li><a href="?resto=Reservation"><i class="fa fa-file"></i> Room reservation </a></li>
            		      <li><a href="?resto=venue_booking_list"><i class="fa fa-sticky-note-o"></i> Venue reservation</a></li>
            		       <li><a href="?resto=groups"><i class="fa fa-sticky-note-o"></i>Groups</a></li>
            		           <li><a href="?resto=credit_account"><i class="fa fa-sticky-note-o"></i>Credits Sales</a></li>
            		        <li class="pt-4"><a href="?resto=customers"><i class="fa fa-sticky-note-o"></i> Venue Customers</a></li>
                            <!-- Make Internal Request -->
                            <li class="pt-4"><a href="?resto=request"><i class="fa fa-shopping-cart"></i> Request Materials</a></li>
            		      <li class="pt-4"><a data-toggle="tab" href="#chk"><i class="fa fa-file"></i> Report <i class="fa fa-angle-down"></i></a></li>
            		     <?php
            		        if (isset($_SESSION['real_admin_role'])): ?>
                            <li class="pt-4"><a href="?access_as=1" class="btn btn-outline-warning">Back to Admin</a></li>
                         <?php endif; ?>
	                </ul>

                    <div class="tab-content custom-menu-content">
                        <div id="chk" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=all_invoices"> All Invoice</a></li>
                                <li><a href="?resto=Booking_list"> Booking List</a></li>
                                <li><a href="?resto=inhouse"> In House</a></li>
                                <li><a href="?resto=checkutrepo"> Checked out</a></li>
                                  <li><a href="?resto=expected_arrival"> Expected Arrival</a></li>
                                <li><a href="?resto=breakfast"> Breakfast Report</a></li>
                                  <li><a href="?resto=rent"> Rental Report</a></li>
                                <li><a href="?resto=rooming"> Rooming</a></li>
                                  <li><a href="?resto=expected_dep"> (EXPECTED) DEPARTURE</a></li>
                                <!--<li><a href="?resto=room_reservations"> Room status</a></li>-->

                                 <li><a href="?resto=daily_occupancy_rate"> Daily occupancy rate</a></li>
                                <li><a href="?resto=average_room_rate"> Average room rate</a></li>
                                   <li><a href="?resto=room_occupancy_index"> Room occupancy index
</a></li>
                                <li><a href="?resto=revpar"> REVPAR
</a></li>
                                <li><a href="?resto=venuereports"> Venue Reports
</a></li>


                            </ul>
                        </div>
                    </div>



                    <?php
                     }
                    ?>











                    <?php
                     if($_SESSION['log_role'] == '5'){
                    ?>


                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                       <li><a href="?resto=waiter_report"><i class="fa fa-plus-circle"></i> Waiters report</a></li>
                       <li><a href="?resto=norder"><i class="fa fa-plus-circle"></i> New Order</a></li>
                       <li><a href="?resto=lorder"><i class="fa fa-list"></i>Order List <div class="spinner4 spinner-4 budge-spinner" style="margin-left:-15px;"></div><div class="ntd-ctn budge" style="margin-left:-15px;"><span style="color: #f2f2f2;"><?php echo $ordercount; ?></span></div></a></li>
                        <li><a href="?resto=prcsOrder"><i class="fa fa-envelope"></i>Order
                        <div class="spinner4 spinner-4 scrn-budge-spinner"></div>
                        <div class="ntd-ctn scrn-budge">
                        <span style="color: #f2f2f2;"><?php echo $tot2; ?></span>
            		        </div>
            		        </a>
            		        </li>
            		      <li><a href="?resto=menu"><i class="fa fa-file"></i> Menu</a></li>
                          <li><a data-toggle="tab" href="#chk"><i class="fa fa-shopping-cart"></i> Request <i class="fa fa-angle-down"></i></a></li>
            		      <li><a href="?resto=reportb"><i class="fa fa-sticky-note-o"></i> Report</a></li>
            		     <?php
            		      if (isset($_SESSION['real_admin_role'])): ?>
    <li><a href="?access_as=1" class="btn btn-outline-warning">Back to Admin</a></li>
<?php endif; ?>

                    <div class="tab-content custom-menu-content">
                        <div id="chk" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=request">Request Raw Materials</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                     }
                    ?>

                   <?php
                     if($_SESSION['log_role'] == '6'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <li><a href="?resto=norder"><i class="fa fa-edit"></i>New Order</a></li>
                        <li><a href="?resto=lorder"><i class="fa fa-list"></i>Order List <div class="spinner4 spinner-4 budge-spinner" style="margin-left:-15px;"></div><div class="ntd-ctn budge" style="margin-left:-15px;"><span style="color: #f2f2f2;"><?php echo $ordercount; ?></span></div></a></li>
                        <li><a href="?resto=report"><i class="fa fa-sticky-note-o"></i> Report</a></li>
                    </li>
	          </ul>
                     <?php
                    }
                    ?>

                    <?php
                     if($_SESSION['log_role'] == '7'){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <li><a href="?resto=stock"><i class="fa fa-home"></i> Stock</a></li>
                        <li><a href="?resto=stock_balance"><i class="fas fa-balance-scale"></i> Stock Balance</a></li>
                        <li><a href="?resto=suppliers"><i class="fa fa-file"></i> suppliers</a></li>
                         <!-- <li><a href="?resto=purchases"><i class="fa fa-home"></i> Purchase</a></li> -->
                         <li><a href="?resto=requestStore"><i class="fa fa-home"></i> Purchase</a></li>
                          <li><a href="?resto=delivery"><i class="fa fa-home"></i> Delivery Note</a></li>
                        <li><a data-toggle="tab" href="#chk"><i class="fa fa-shopping-cart"></i> Request <i class="fa fa-angle-down"></i></a></li>
                        <li><a href="?resto=reportc"><i class="fa fa-sticky-note-o"></i>Cost Report</a></li>
                        <li><a href="?resto=supplier_report"><i class="fa fa-sticky-note-o"></i> Supplier Report</a></li>
                                <li><a href="?resto=StockLimit"><i class="fa fa-sticky-note-o"></i> Stock Limits</a></li>
                                  <li><a href="?resto=WriteOff"><i class="fa fa-sticky-note-o"></i> Write off</a></li>

                                   <li><a href="?resto=categories"><i class="fa fa-sticky-note-o"></i>Category</a></li>
                                 <?php
            		      if (isset($_SESSION['real_admin_role'])): ?>
    <li><a href="?access_as=1" class="btn btn-outline-warning">Back to Admin</a></li>
<?php endif; ?>
                    </ul>
                    <div class="tab-content custom-menu-content">
                        <div id="chk" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=request">Requested Materials</a></li>
                                 <li><a href="?resto=requestStore">Request stock</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                    }
                    ?>

                     <?php
                     if($_SESSION['log_role'] == '10' || $_SESSION['log_role']==12  || $_SESSION['log_role']==11){
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li><a href="?resto=home"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                       <li><a href="?resto=waiter_report"><i class="fa fa-plus-circle"></i> Waiters report</a></li>
                       <?php
                     if($_SESSION['log_role'] == '12'){
                         ?>
                         <li><a href="?resto=norder"><i class="fa fa-edit"></i>New Order</a></li>
                        <li><a href="?resto=lorder"><i class="fa fa-list"></i>Order List <div class="spinner4 spinner-4 budge-spinner" style="margin-left:-15px;"></div><div class="ntd-ctn budge" style="margin-left:-15px;"><span style="color: #f2f2f2;"><?php echo $ordercount; ?></span></div></a></li>
                       <?php }?>



                        <?php if($_SESSION['log_role']==10){ ?>
                        

                       <!-- Hide order for cashier -->
                       <li><a href="?resto=norder"><i class="fa fa-plus-circle"></i> New Order</a></li>
                       <li><a href="?resto=lorder"><i class="fa fa-list"></i>Order List <div class="spinner4 spinner-4 budge-spinner" style="margin-left:-15px;"></div><div class="ntd-ctn budge" style="margin-left:-15px;"><span style="color: #f2f2f2;"><?php echo $ordercount; ?></span></div></a></li>
                        <li><a href="?resto=OurGste"><i class="fa fa-users"></i> Our Guest <div class="spinner4 spinner-4 budge-spinner"></div><div class="ntd-ctn budge"><span style="color: #f2f2f2;"><?php echo $num; ?></span></div></a></li>
                        <?php }?>
                        <li><a data-toggle="tab" href="#chk"><i class="fa fa-file"></i> Sales <i class="fa fa-angle-down"></i></a></li>
                       
                        <?php if($_SESSION['log_role']==10){ ?>
                         <li><a href="?resto=collections"><i class="fa fa-tachometer"></i>Collections</a></li>
                          <!--<li><a href="?resto=credit_account"><i class="fa fa-tachometer"></i>Credit Accounts</a></li>-->
                          <li><a data-toggle="tab" href="#chk2"><i class="fa fa-shopping-cart"></i> Request <i class="fa fa-angle-down"></i></a></li>
                         <!--<li><a href="?resto=advances&type=2" ><i class="fa fa-tachometer"></i>Advance Payment</a></li>-->
                         <!-- <li><a href="?resto=advances&type=1" ><i class="fa fa-tachometer"></i>Less Advance</a></li>-->
                          <li><a href="?resto=receipts" ><i class="fa fa-tachometer"></i>Receipts</a></li>
                          <li><a href="?resto=ebm" ><i class="fa fa-tachometer"></i>Reprint Receipts</a></li>
                          <!--<li><a href="?resto=credit_account" ><i class="fa fa-tachometer"></i>Customer credit account</a></li>-->
                           <!--<li><a href="?resto=creditreport" ><i class="fa fa-tachometer"></i>Credits Report</a></li>-->
                             <!--<li><a href="?resto=roomreport" ><i class="fa fa-tachometer"></i>Room consuption Report</a></li>-->
                          <?php } ?>
                         <?php if($_SESSION['log_role']==11){ ?>
                            <li><a href="?resto=void"> Pending voided Items</a></li>
                            <li><a data-toggle="tab" href="#front_office"><i class="fa fa-building"></i> Front Office <i class="fa fa-angle-down"></i></a></li>
                            <li><a data-toggle="tab" href="#stock_inventory"><i class="fa fa-cubes"></i> Stock/Inventory <i class="fa fa-angle-down"></i></a></li>

                     <?php     } ?>
                      <?php if($_SESSION['log_role']==12 || $_SESSION['log_role']==9){ ?>
                        <li><a href="?resto=requests" >Internal Requests</a></li>
                        <li><a data-toggle="tab" href="#chk2"><i class="fa fa-shopping-cart"></i> Request <i class="fa fa-angle-down"></i></a></li>

                        <?php } ?>
	                </ul>

                    <div class="tab-content custom-menu-content">
                        <div id="chk2" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=request">Request Raw Materials</a></li>
                            </ul>
                        </div>
                    </div>


                    <div class="tab-content custom-menu-content">
                        <div id="chk" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=reception">Sales</a></li>
                                <li><a href="?resto=sale_summary">Sales Summary</a></li>

                                 <li><a href="?resto=detailedsmumary" >Detailed Summary</a></li>
                                <li><a href="?resto=reports&type=2">Bar</a></li>
                                <li><a href="?resto=reports&type=1">Resto</a></li>
                                <li><a href="?resto=reports&type=32">Coffee</a></li>
                                <?php if($_SESSION['log_role']==11){ ?>
                                    <li><a href="?resto=detailedsmumary" >Detailed Summary</a></li>

                                <?php } ?>
                            </ul>
                        </div>

                        <!-- Front Office Dropdown for Role 11 -->
                        <div id="front_office" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=inhouse">Room Inhouse</a></li>
                                <li><a href="?resto=checkutrepo">Checked out</a></li>
                                <li><a href="?resto=venuereports">Venue Reports</a></li>
                                <li><a href="?resto=breakfast">Room Breakfast Report</a></li>
                            </ul>
                        </div>

                        <!-- Stock/Inventory Dropdown for Role 11 -->
                        <div id="stock_inventory" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="?resto=stock_balance">Stock Balance</a></li>
                                <li><a href="?resto=requests">Internal Requisitions</a></li>
                                <li><a href="?resto=requestStore">External Requisitions</a></li>
                                <li><a href="?resto=supplier_report">Supplier Report</a></li>
                                <li><a href="?resto=ReportByCategory">Cost Report By Category</a></li>
                                <li><a href="?resto=ReportByDepertment">Cost Report By Department</a></li>
                                <li><a href="?resto=staff_report">Staff Meal Report</a></li>
                                <li><a href="?resto=baqueting_report">Banqueting Report</a></li>
                                <li><a href="?resto=purchase">Purchase Request</a></li>
                            </ul>
                        </div>
                    </div>
                     <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>


    <style>
.dropdown-submenu > .dropdown-menu {
  columns: 2; /* or 3 for more */
  column-gap: 20px;
  width: 1000px;
}
.dropdown-menu{
    columns:1;
    width: 500px;
}
    </style>

    <script>
  $(document).ready(function() {
    $('.dropdown-submenu a.dropdown-toggle').on("click", function(e) {
      $(this).next('ul').toggle();
      e.stopPropagation();
      e.preventDefault();
    });
  });
</script>
<!-- Main Menu area End-->

<div class="modal fade" id="mySettingModel" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Settings</h4>
            </div>
            <div class="modal-body">
              <!--start form-->
              <form class="form-horizontal" method="post" onsubmit="validateForm(event)" action="" enctype='multipart/form-data'>
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">System name</label>
                      <div class="col-lg-8">
                      <input type="text" name="system_name" autocomplete="off" value="<?php echo $name; ?>" class="form-control">
                      </div>
                  </div>
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">Tin Number</label>
                      <div class="col-lg-8">
                      <input type="text" class="form-control" value="<?php echo $companyTin; ?>" name="tin" required>
                      </div>
                  </div>
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">MRC</label>
                      <div class="col-lg-8">
                      <input type="text" name="mrc" id="mrc" autocomplete="off" value="<?php echo $mrc; ?>" class="form-control" required>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">Receipt Message</label>
                      <div class="col-lg-8">
                      <input type="text" name="receipt_msg" autocomplete="off" value="<?php echo $msg; ?>" class="form-control transport" required>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">CIS Version</label>
                      <div class="col-lg-8">
                      <input type="number" step="any" name="version" autocomplete="off" value="<?php echo $version; ?>" class="form-control transport" required>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">EBM Version</label>
                      <div class="col-lg-8">
                      <input type="number" step="any" name="ebm_version" autocomplete="off" value="<?php echo $ebm_version; ?>" class="form-control transport" required>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="control-label col-lg-2" for="title">Printer</label>
                      <div class="col-lg-8">
                      <select class="form-control" name="printer" required>
                                    <option value="a4" <?php echo $printer=='a4'? 'selected':''  ?>>A4</option>
                                    <option value="a5" <?php echo $printer=='a5'? 'selected':''  ?>>A5</option>
                                    <option value="paper_roll" <?php echo $printer=='paper_roll'? 'selected':''  ?>>Paper roll</option>
                                </select>
                      </div>
                  </div>



                  <!-- Buttons -->
                  <div class="form-group">
                      <!-- Buttons -->
                      <div class="col-lg-offset-2 col-lg-6">
                        <button type="submit" name="saveConfig" class="btn btn-sm btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                       </div>
                  </div>
              </form>
              <!--end form-->
            </div>

        </div><!--modal content-->
    </div>
                </div>
