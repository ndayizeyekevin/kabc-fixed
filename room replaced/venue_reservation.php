
<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-free">
<!-- header -->
<?php 

$room_booking = 0;
$today = date('Y-m-d');
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	
		 $room_booking = $room_booking + 1; 
	 
	  
	  
}}

$event_booking = 0;
$sql = "SELECT * FROM tbl_ev_venue_reservations where status = 'Confirmed'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	
		 $event_booking = $event_booking + 1; 
	 
	  
	  
}}



$event_booking = 0;
$sql = "SELECT * FROM tbl_ev_venue_reservations where status = 'Confirmed'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	
		 $event_booking = $event_booking + 1; 
	  
	  
	  
}}




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


$customers = 0;
$sql = "SELECT * FROM tbl_acc_guest ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
$date = strtotime($row['created_at']);
if($today==date('Y-m-d',$date)){   
$customers  =  $customers + 1;
	 }	
	   

	  	
}}

$venue_customer = 0;
$sql = "SELECT * FROM tbl_ev_customers ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
  $date = strtotime($row['created_at']);
	  	 if($today==date('Y-m-d',$date)){   
$venue_customer  =  $venue_customer + 1;
	 }	
	   

	  	
}}





$expenses_payments = 0;
$sql = "SELECT * FROM 	expenses ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
if($today==date('Y-m-d',$row['create_time'])){      
$expenses_payments  =  $expenses_payments + $row['price'];
		 }
	  	
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
						


echo number_format($event_booking);
						
						
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
						
						echo number_format($venue_payments);
						
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
				  
				  <?php  echo number_format($venue_customer);?></h3>
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
						
						echo number_format($venue_payments);
						
						?> RWF</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

       
			    <!-- Basic Bootstrap Table -->
            <div class="card">
              <h5 class="card-header">All Venue reservations</h5>
              <div class="text-nowrap">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Room Type</th>
                      <th>Reservation Date</th>
                      
                      <th>Total Amount</th>
                
                      <th>Due amount</th>
                      <th>Payment Status</th>
                    </tr>
                  </thead>
<tbody class="table-border-bottom-0">		  
<?php $sql = "SELECT * FROM tbl_ev_venue_reservations where status = 'Confirmed'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  if($today==$row['reservation_date']){
		 ?>
   <tr>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getCustomerNames($row['customer_id'])?></strong></td>
                      <td><?php echo getVenueName($row['venue_id']); ?></td>
                      <td><?php echo $row['reservation_date']?></td>
                      
                      <td>RWF <?php echo number_format(getSingleReservationTotal($row['venue_id']))?></td>
                   
                      <td>RWF <?php echo number_format(getSingleReservationTotal($row['venue_id']) - getSingleReservationDueTotal($row['id']))?></td>
                      <td>
					  <?php if(getSingleReservationTotal($row['venue_id']) - getSingleReservationDueTotal($row['id'])==0){?>
					  <span class="badge bg-label-success me-1">Paid</span>
					  <?php }else{?>
					    <span class="badge bg-label-primary me-1">Pending Payment</span>
					  <?php }  ?>
					  </td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="single_reservation.php?booking_id=<?php echo $row['id']?>"><i class="bx bx-edit-alt me-1"></i> View</a>
                           
                          </div>
                        </div>
                      </td>
                    </tr>		
		
		 <?php
	  }
	  
	  
}}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
              </div>
            </div>
            <!--/ Basic Bootstrap Table -->
			
			
			
            <!--/ Basic Bootstrap Table -->
          </div>
          <!-- / Content -->
        </div>
      </div>
    </div>
  </div>

  <div id="showToast" class="toast-container position-relative"></div>

  <!-- Footer -->

</body>

</html>