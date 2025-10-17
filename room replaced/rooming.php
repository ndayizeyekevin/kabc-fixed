
<!-- header -->
<?php 
include '../inc/conn.php';

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
	





      
             <div class="colr-area">
        <div class="container">
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
$sql = $db->prepare("SELECT * FROM tbl_acc_booking where booking_status_id = 6");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
	   if(date('Y-m-d') >= $row['checkin_date']){
	      
	      
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
	  
	  
}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
              </div>
            </div>
			
			
		