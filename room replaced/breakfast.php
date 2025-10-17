

<!-- header -->
<?php 
error_reporting(E_ALL);
ini_set("display_errors",1);

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$start_datetime = $start_date . ' 00:00:00';
$end_datetime = $end_date . ' 23:59:59';
$today = date('Y-m-d');


$inhouse_condition = "booking_status_id = 6";

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
          <div class="colr-area">
        <div class="container">
              <h5 class="card-header">Breakfast Report <a href="print.php?page=breakfast" class="btn btn-info">Print</a ></h5>
              <div class="text-nowrap table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                          <th>No</th>
                      <th>Guest Names</th>
                         <th>Company</th>
                           <th>Nationality</th>
                      <th colspan="2">Room Type</th>
                    
                      <th>Pax</th>
                    </tr>
                  </thead>
<tbody class="table-border-bottom-0">		  
<?php 
$no = 0;
$total_adult = 0;
$total_child = 0;
$sql = ""; 
$sql = $db->prepare("SELECT * FROM tbl_acc_booking 
                        WHERE booking_status_id = 6");
// $sql = $db->prepare("SELECT * FROM tbl_acc_booking WHERE $inhouse_condition");
                        		$sql->execute();
                        		while($row = $sql->fetch()){
                        		    $no++;
                        	$total_adult +=	$row['num_adults'];
                        	$total_child += $row['num_children'];
// 	 if(date('Y-m-d') > $row['checkin_date']){
  if(getGuestBookingOption($row['id'])==1){
      
		 ?>
   <tr>
       <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no ?></strong></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                      
                           <td><?php echo $row['company']?></td>
                        <td><?php echo getGuestNationality($row['guest_id'])?></td>
                      
                      
                      <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                   
                     
                     
                   
                 
                       <td>Adult: <?php echo $row['num_adults']?></td>
                      <td>Children: <?php echo $row['num_children']?> </td>
                
                    </tr>		
		
		 <?php
	  }
    // }
	  
	  
}
        ?>
        <tr><td colspan='6'><strong>TOTAL</strong></td> <td><strong><?echo number_format($total_adult) ?></strong></td> <td><strong><?echo number_format($total_child) ?></strong></td></tr>
        <tr><td colspan='7'><strong>ALL TOTAL</strong></td><td><strong><?echo number_format($total_child + $total_adult) ?></strong></td></tr>
        
        
         
				  
				  
                 
                 
                   
                  </tbody>
                </table>
              </div>
            </div>
			
			

  <div id="showToast" class="toast-container position-relative"></div>

  <!-- Footer -->

</body>

</html>