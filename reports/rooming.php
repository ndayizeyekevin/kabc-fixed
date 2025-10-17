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



$room_booking = 0;
$today = date('Y-m-d');
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	  if(time() <= strtotime($row['checkout_date'])){
		 $room_booking = $room_booking + 1; 
	  }
	  
	  
}}


$active_amount = 0;
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	  if(time() <= strtotime($row['checkout_date'])){
		 $active_amount = $active_amount + $row['room_price']; 
	  }
	  
	  
}}









$event_booking = 0;
$sql = "SELECT * FROM tbl_ev_venue_reservations where status = 'Confirmed'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	  if($today==$row['reservation_date']){
		 $event_booking = $event_booking + 1; 
	  }
	  
	  
}}



$event_booking = 0;
$sql = "SELECT * FROM tbl_ev_venue_reservations where status = 'Confirmed'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	  if($today==$row['reservation_date']){
		 $event_booking = $event_booking + 1; 
	  }
	  
	  
}}




$room_sum = 0;
$sql = "SELECT * FROM  payments ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
 if($today==date('Y-m-d',$row['payment_time'])){   
$room_sum  =  $room_sum + $row['amount'];
	 }	
}}





$accoupied = 0;
$sql = "SELECT * FROM  tbl_acc_room  where status_id = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
	  
$accoupied  =  $accoupied + 1;
	 	
	   

	  	
}}



$available = 0;
$sql = "SELECT * FROM  tbl_acc_room  where status_id = 3";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
	  
$available  =  $available + 1;
	 	
	   

	  	
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

            <div class="row" hidden>
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
                        <span class="fw-semibold d-block mb-1">Active Booking</span>
                        <h3 class="card-title mb-2"><?php 
						


 echo number_format($room_booking);
						
						
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
                        <span>Accoupied Room</span>
                        <h3 class="card-title text-nowrap mb-1"><?php 
						
						echo number_format($accoupied);?> </h3>
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
                        <span class="d-block mb-1">Available Room</span>
                     <h3 class="card-title text-nowrap mb-2">
				  
				  <?php  echo number_format($available);?></h3>
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
                        <span class="fw-semibold d-block mb-1">Total Amount</span>
                        <h3 class="card-title mb-2"><?php 
						
						echo number_format($active_amount);
						
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
              <h5 class="card-header">ROOMING REPORT <a href="print.php?page=room" class="btn btn-info">Print</a ></h5>
              <div class="text-nowrap table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                          <th>No</th>
                      <th>Guest Names</th>
                      <th>Room</th>
                      <th>Check In</th>
                      <th>Check Out</th>
                        <th>Nationality </th>
                      <th>ID/PassPort </th>
                    
                      <th>Company </th>
                      <th>Contact </th>
                       <th>Email </th>
                    </tr>
                  </thead>
<tbody class="table-border-bottom-0">		  
<?php 
$no = 0;
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 1 OR  booking_status_id  = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  if(time() <= strtotime($row['checkout_date'])){
	      
	      
		 ?>
   <tr>
       <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no = $no+1?></strong></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                      <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                     
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                      <td><?php echo getGuestDetail($row['guest_id'],'nationality')?></td>
                     <td><?php echo getGuestDetail($row['guest_id'],'identification')?>
                      <?php echo getGuestDetail($row['guest_id'],'passport_number')?></td>
                      <td><?php echo $row['company']?></td>
                      <td><?php echo getGuestDetail($row['guest_id'],'phone_number')?></td>
                       <td><?php echo getGuestDetail($row['guest_id'],'email_address')?></td>
                
                      <td>
				
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
			
			
				<br>	<br>

			
			
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