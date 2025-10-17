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
	  
	  if($today==$row['checkin_date']){
		 $room_booking = $room_booking + 1; 
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





$venue_payments = 0;
$sql = "SELECT * FROM 	venue_payments ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
	  
	  	 if($today==date('Y-m-d',$row['payment_time'])){   
$venue_payments  =  $venue_payments + $row['amount'];
	 }	
	   

	  	
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

        

            <!-- Basic Bootstrap Table -->
            <div class="card">
              <h5 class="card-header">BOOKING LIST <a href="print.php?page=booking" class="btn btn-info">Print</a ></h5>
              <div class="text-nowrap">
                <table class="table">
                  <thead>
                    <tr>
                         <th>No</th>
    
                      <th colspan="2">Room (Account)</th>
                        <th>Guest Name</th>
                      <th>Check In</th>
                      <th>Check Out</th>
                      <th>Rate</th>
                      <th>Days</th>
                      <th>Company</th>
                      <th>Residence</th>
                    </tr>
                 <tbody class="table-border-bottom-0">		  
<?php 
$no = 0;



$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 1 OR  booking_status_id  = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
$date = $row['created_at'];
$createDate = new DateTime($date);
$strip = $createDate->format('Y-m-d');

	  if($today==$strip){
	      
	      
		 ?>
   <tr>
       <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no = $no+1?></strong></td>
        <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                      
                
                      
                      
                     
                   
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                     
                   
                 
                       <td><?php echo number_format($row['room_price'])?></td>
                        <td><?php echo number_format($row['duration'])?></td>
                      <td>
			-
					  </td>
					   <td>
			-
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
			
			    <!-- Basic Bootstrap Table -->
            <div class="card" hidden>
              <h5 class="card-header">Today Venue reservations</h5>
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
  <?php include_once "../../partials/footer.php"; ?>
</body>

</html>