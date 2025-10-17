
<?php 
// error_reporting (E_ALL);
// ini_set("display_errors",1);

include_once '../inc/conn.php';

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
	






           
             <div class="colr-area">
        <div class="container">
              <h5 class="card-header">(EXPECTED) DEPARTURE <a href="print.php?page=dep" class="btn btn-info">Print</a ></h5>
              <div class="text-nowrap table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                    <th>No</th>
                    <th>Guest Names/ Company</th>
                    <th>Room No</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Rate</th>
                    <th>Days</th>
                    <th>Extra</th>
                    <th>Total Amount</th>
                    <th>Balance</th>
                    <th>Debtor</th>
                    </tr>
                  </thead>
<tbody class="table-border-bottom-0">		  
<?php
$no=0;
$todays = Date('Y-m-d');
$sql = $db->prepare("SELECT b.*, r.room_number 
                            FROM tbl_acc_booking b 
                            LEFT JOIN tbl_acc_booking_room br ON b.id = br.booking_id 
                            LEFT JOIN tbl_acc_room r ON br.room_id = r.id 
                            WHERE b.booking_status_id = 6 AND b.checkout_date = '$todays'");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
// 	  if($today==$row['checkout_date']){
		 ?>
   <tr>
       <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no = $no + 1;?></strong></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                      <td><strong><?php echo $row['room_number'] ? $row['room_number'] : 'N/A'; ?></strong></td>
                
                      <td><?php echo $ckin = $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                      <td><?php echo number_format($row['room_price'])?></td>
                      
                     <td><?php 
                     
                     
                     
$today=date_create($todays);
$ckin=date_create($ckin);
$diff=date_diff($ckin,$today);
$night = $diff->format("%a");
                     
                     echo number_format($night)?></td>
                    <td><?php echo number_format(getExtraTotal($row['id']))?></td>
                   
                    <td><?php  $total =  $row['room_price'] * $night + getExtraTotal($row['id']); 
                    echo number_format($total)?></td>
                   
                      <td><?php echo number_format($total - getSingleBookingDueTotal($row['id']))?></td>
                      <td>
				       Him/Herself
					  </td>
                    
                    </tr>		
		
		 <?php
// 	  }
	  
	  
}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
              </div>
            </div>
			
			
				<br>	<br>
			
		


</html>
