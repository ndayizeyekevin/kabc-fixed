
<?php 

include '../inc/conn.php';
$room_booking = 0;
$today = date('Y-m-d');
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	  if(time() <= strtotime($row['checkin_date'])){
		 $room_booking = $room_booking + 1; 
	  }
	  
	  
}}


$active_amount = 0;
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	  if(time() <= strtotime($row['checkin_date'])){
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



            <div class="colr-area">
        <div class="container">
              <h5 class="card-header">Expected Arriaval <a href="print.php?page=expected" class="btn btn-info">Print</a ></h5>
              <div class="text-nowrap table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                          <th>No</th>
                        <th>Guest Names</th>
                        <th>Company</th>
                        <th>Nationality</th>
                      <th colspan="2">Room Type</th>
                      <th>Check In</th>
                      <th>Check Out</th>
                      <th>Room Rate</th>
                      <th>Debtor</th>
                    </tr>
                  </thead>
<tbody class="table-border-bottom-0">		  
<?php 
$no = 0;
$today = Date('Y-m-d');

// $sql = $db->prepare("SELECT * FROM tbl_acc_booking where booking_status_id =2");
$sql = $db->prepare("SELECT * FROM tbl_acc_booking WHERE checkin_date = '$today' AND booking_status_id NOT IN (3,5)");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
	  if(date('Y-m-d') == $row['checkin_date']){
	      
	      
		 ?>
   <tr>
       <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no = $no+1?></strong></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                      
                           <td><?php echo $row['company']?></td>
                     <td><?php echo getGuestNationality($row['guest_id'])?></td>
                      
                      
                      <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                   
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                     
                   
                 
                       <td><?php echo number_format($row['room_price'])?></td>
                      <td>
				Himself/herself
					  </td>
                
                    </tr>		
		
		 <?php
	  }
	  
	  
}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
              </div>
            </div>
			
			


  <div id="showToast" class="toast-container position-relative"></div>
