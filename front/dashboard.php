<?php session_start();
if(!$_SESSION['loggedIn']){
	echo "<script>window.location='../../index.php'</script>";
}


$breadcrumb_items = [
  ['name' => '', 'link' => '', 'active' => true]
];




?>
<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-free">
<!-- header -->
<?php include_once "../../partials/header.php";

if($role!=1){
	echo "<script>window.location='../reservations/room_booking_list.php'</script>";
}



$sql = "SELECT * FROM tbl_acc_booking WHERE payment_status_id = 1 ";
$result = $conn->query($sql);
$room_booking = $result->num_rows;

$sql = "SELECT * FROM tbl_ev_venue_reservations";
$result = $conn->query($sql);
$event_booking = $result->num_rows;


$sql = "SELECT * FROM  tbl_acc_guest";
$result = $conn->query($sql);
$customers = $result->num_rows;


$sql = "SELECT * FROM tbl_ev_customers";
$result = $conn->query($sql);
$venue_customer = $result->num_rows;



$room_sum = 0;
$sql = "SELECT * FROM  payments ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	   
$room_sum  =  $room_sum + $row['amount'];
	  	
}}





$venue_payments = 0;
$sql = "SELECT * FROM 	venue_payments ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	   
$venue_payments  =  $venue_payments + $row['amount'];
	  	
}}



$expenses_payments = 0;
$sql = "SELECT * FROM 	expenses ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	   
$expenses_payments  =  $expenses_payments + $row['price'];
	  	
}}


$income = $room_sum+$venue_payments;
$profit = $income - $expenses_payments;



	?>
	






<!-- / header -->

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- side Menu -->
      <?php include_once "../../partials/side_nav.php"; ?>
      <!-- / side Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- top Navbar -->
        <?php include_once "../../partials/top_nav.php"; ?>
        <!-- / top Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y">
            <div id="showAlert"></div>
            <!-- breadcrumbs -->
            <p><?php include '../../partials/breadcrumb.php'; ?></p>

            <div class="row">
              <div class="col-lg-12 col-md-12 order-1">
                <div class="row">
                  <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <!-- <img
                                src="../../assets/img/icons/unicons/chart-success.png"
                                alt="chart success"
                                class="rounded" 
                              /> -->
                            <span class="text-success"><i style="font-size: 30px;" class="bx bxs-bookmarks"></i></span>
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt3"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Booking</span>
                        <h3 class="card-title mb-2"><?php 
						

	echo number_format($room_booking+$event_booking);
						
						
						?></h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <img
                              src="../../assets/img/icons/unicons/wallet-info.png"
                              alt="Credit Card"
                              class="rounded" />
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt6"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span>Total Amount</span>
                        <h3 class="card-title text-nowrap mb-1"><?php 
						
						echo number_format($room_sum+$venue_payments);
						
						?> RWF</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <!-- <img src="../../assets/img/icons/unicons/paypal.png" alt="Credit Card" class="rounded" /> -->
                            <span class="text-warning "><i style="font-size: 30px;" class="bx bxs-user"></i></span>
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt4"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span class="d-block mb-1">Total Customer</span>
                     <h3 class="card-title text-nowrap mb-2">
				  
				  <?php  echo number_format($customers +$venue_customer);?></h3>
                        <small class="text-warning fw-semibold"><i class="bx bx-up-arrow-alt"></i> +3%</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <img src="../../assets/img/icons/unicons/cc-primary.png" alt="Credit Card" class="rounded" />
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt1"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="cardOpt1">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Revenue</span>
                        <h3 class="card-title mb-2"><?php 
						
						echo number_format($room_sum+$venue_payments);
						
						?> RWF</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12 col-lg-12 order-1 mb-4">
                <div class="card h-100">
                  <div class="card-header">
                    <ul class="nav nav-pills" role="tablist">
                      <li class="nav-item">
                        <button
                          type="button"
                          class="nav-link active"
                          role="tab"
                          data-bs-toggle="tab"
                          data-bs-target="#navs-tabs-line-card-income"
                          aria-controls="navs-tabs-line-card-income"
                          aria-selected="true">
                          Income
                        </button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab"
						 data-bs-toggle="tab"
                          data-bs-target="#navs-tabs-line-card-expenses"
                          aria-controls="navs-tabs-line-card-expenses"
						>Expenses</button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab"
						 data-bs-toggle="tab"
                          data-bs-target="#navs-tabs-line-card-profit"
                          aria-controls="navs-tabs-line-card-profit">Profit</button>
                      </li>
                    </ul>
                  </div>
                  <div class="card-body px-0">
                    <div class="tab-content p-0">
                      <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
                        <div class="d-flex p-4 pt-3" id="">
                          <div class="avatar flex-shrink-0 me-3">
                            <img src="../../assets/img/icons/unicons/wallet.png" alt="User" />
                          </div>
                          <div>
                            <small class="text-muted d-block">Total Balance</small>
                            <div class="d-flex align-items-center">
                              <h6 class="mb-0 me-1">RWF <?PHP ECHO number_format($income)?></h6>
                             
                            </div>
                          </div>
                        </div>
                        <div id="incomeChart"></div>
                        <div class="d-flex justify-content-center pt-4 gap-2">
                          <div class="flex-shrink-0">
                            <div id="expensesOfWeek"></div>
                          </div>
                          <div>
                            <p class="mb-n1 mt-1">Income This Week</p>
                            
                          </div>
                        </div>
                      </div>
					             <div class="tab-pane fade show " id="navs-tabs-line-card-expenses" role="tabpanel">
                        <div class="d-flex p-4 pt-3" id="">
                          <div class="avatar flex-shrink-0 me-3">
                            <img src="../../assets/img/icons/unicons/wallet.png" alt="User" />
                          </div>
                          <div>
                            <small class="text-muted d-block">Total Balance</small>
                            <div class="d-flex align-items-center">
                              <h6 class="mb-0 me-1">RWF <?PHP ECHO number_format($expenses_payments)?></h6>
                           
                            </div>
                          </div>
                        </div>
                        <div id="incomeChart"></div>
                        <div class="d-flex justify-content-center pt-4 gap-2">
                          <div class="flex-shrink-0">
                            <div id="expensesOfWeek"></div>
                          </div>
                          <div>
                            <p class="mb-n1 mt-1">Expenses This Week</p>
                    
                          </div>
                        </div>
                      </div>
					             <div class="tab-pane fade show " id="navs-tabs-line-card-profit" role="tabpanel">
                        <div class="d-flex p-4 pt-3" id="">
                          <div class="avatar flex-shrink-0 me-3">
                            <img src="../../assets/img/icons/unicons/wallet.png" alt="User" />
                          </div>
                          <div>
                            <small class="text-muted d-block">Total Balance</small>
                            <div class="d-flex align-items-center">
                              <h6 class="mb-0 me-1">RWF <?PHP ECHO number_format($profit)?></h6>
                           
                            </div>
                          </div>
                        </div>
                        <div id="incomeChart"></div>
                        <div class="d-flex justify-content-center pt-4 gap-2">
                          <div class="flex-shrink-0">
                            <div id="expensesOfWeek"></div>
                          </div>
                          <div>
                            <p class="mb-n1 mt-1">Tatal profit This Week</p>
                           
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Basic Bootstrap Table -->
            <div class="card">
              <h5 class="card-header">Today booking List</h5>
              <div class="text-nowrap">
                <table class="table" hidden>
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Room Type</th>
                      <th>Check In</th>
                      <th>Check Out</th>
                      <th>Paid Amount</th>
                      <th>Due amount</th>
                      <th>Payment Status</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    <tr>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Frank Baker</strong></td>
                      <td>Deluxe</td>
                      <td>2022-01-01</td>
                      <td>2022-01-05</td>
                      <td>RWF 100000</td>
                      <td>RWF 0</td>
                      <td><span class="badge bg-label-success me-1">Paid</span></td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Phil Glover</strong></td>
                      <td>Queen</td>
                      <td>2022-01-05</td>
                      <td>2022-01-08</td>
                      <td>RWF 0</td>
                      <td>RWF 250000</td>
                      <td><span class="badge bg-label-warning me-1">Pending</span></td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Frank Baker</strong></td>
                      <td>Deluxe</td>
                      <td>2022-01-01</td>
                      <td>2022-01-05</td>
                      <td>RWF 100000</td>
                      <td>RWF 0</td>
                      <td><span class="badge bg-label-success me-1">Paid</span></td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Phil Glover</strong></td>
                      <td>Queen</td>
                      <td>2022-01-05</td>
                      <td>2022-01-08</td>
                      <td>RWF 0</td>
                      <td>RWF 250000</td>
                      <td><span class="badge bg-label-warning me-1">Pending</span></td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!--/ Basic Bootstrap Table -->
          </div>
          <!-- / Content -->
        </div>
      </div>
    </div>
  </div>

  <div id="showToast" class="toast-container position-relative"></div>

  <!-- Footer -->
  <?php include_once "../../partials/footer.php"; ?>
</body>

</html>