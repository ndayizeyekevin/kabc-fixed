<style>
    table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 0px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
</style>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Africa/Kigali');
error_reporting(0);
 include '../../partials/header.php';

$today = date('Y-m-d');

?>
<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <style type="text/css"> * {margin:0; padding:0; text-indent:0; }
         p { color: black; font-family:Tahoma, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt; margin:0pt; }
         .s1 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 12pt; }
         .s2 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 7.5pt; }
         .s3 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 7.5pt; }
         table, tbody {vertical-align: top; overflow: visible; }
      </style>
   </head>
   <body style="background-color:#e1e1e1">
       
  <center>     <button onclick="printInvoice()">Print To PDF </button></center>
       
    <div  id="content" style="background-color:white;margin-top:50px;margin-left:10%;padding:20px;margin-right:10%;height:100%;padding-top:100px">
       <table border="0">
          <tr><td>
    <center>  <br><img width="75" height="54" src="data:image/jpg;base64,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"/>
     </center>
      </td>
      
      <td>
          <p style="padding-top: 6pt;padding-left: 128pt;text-indent: 0pt;line-height: 11pt;text-align: left;">
        <center> 
            KABC HOTEL<br>
            TEL: +250 XXX XXX XXX / +250 XXX XXX XXX<br>
            Website<br>
            TIN/VAT: XXXXXXX</p>
        </center>
      </td>
      
      
            <td>
          <center>   <p  style="padding-top: 6pt;text-indent: 0pt;text-align: center;"> </p></center>
            </td>
      </tr></table>
      
      
      
      
      
      <?php if($_REQUEST['page']=='inhouse'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>IN-HOUSE REPORT ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
            <table class="table">
                  <thead>
                    <tr>
                          <th>No</th>
                      <th>Guest Names</th>
                      <th colspan="2">Room Type</th>
                      <th>Check In</th>
                      <th>Check Out</th>
                      <th>Count</th>
                    
                      <th>Room Rate</th>
                      <th>Pax</th>
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
                        <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                     
                   
                       <td><?php echo $row['duration']?></td>
                       <td><?php echo number_format($row['room_price'])?></td>
                      <td>
				0
					  </td>
                
                    </tr>		
		
		 <?php
	  }
	  
	  
}}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
                                    
                                    
            <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>                   
                                    
     
      
      <?php } ?>
      
      
            
      <?php if($_REQUEST['page']=='expected'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>EXPECTED GUESTS ARRIVAL REPORT ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
      
      
      
      
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
                      <th>Depitor</th>
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
	  if(time() <= strtotime($row['checkin_date'])){
	      
	      
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
	  
	  
}}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
                
                
                 <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>    
                
                
            <?php }    ?>
      
      
      
      
      
      
      
      
    <?php if($_REQUEST['page']=='breakfast'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>BREAKFAST  REPORT ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
      
      
      
      
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
$sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 1 OR  booking_status_id  = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  if(time() <= strtotime($row['checkin_date'])){
   if(getGuestBookingOption($row['id'])==1){
		 ?>
   <tr>
       <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no = $no+1?></strong></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                      
                           <td><?php echo $row['company']?></td>
                        <td><?php echo getGuestNationality($row['guest_id'])?></td>
                      
                      
                      <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                   
                     
                     
                   
                 
                       <td>Adult: <?php echo $row['num_adults']?></td>
                      <td>Children: <?php echo $row['num_children']?> </td>
                
                    </tr>		
		
		 <?php
	  }}
	  
	  
}}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
                
                
                 <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>    
                
            <?php }    ?>
      
         
      
      
      
         
    <?php if($_REQUEST['page']=='dep'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>(EXPECTED) DEPARTURE ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
      
      
      
      
        <table class="table">
                  <thead>
                    <tr>
                         <th>No</th>
                      <th>Guest Names/ Company</th>
                   
                      <th>Check In</th>
                      <th>Check Out</th>
                      <th>Rate</th>
                       <th>Days</th>
                       <th>Extra</th>
                      <th>Total Amount</th>
                      <th>Credit</th>
                      <th>Debtor</th>
                    </tr>
                  </thead>
<tbody class="table-border-bottom-0">		  
<?php $sql = "SELECT * FROM tbl_acc_booking where booking_status_id = 2 OR booking_status_id=1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  if($today==$row['checkout_date']){
		 ?>
   <tr>
       <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no = $no + 1;?></strong></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                      <td><?php echo number_format($row['room_price'])?></td>
                      
                       <td><?php echo number_format($row['duration'])?></td>
                         <td><?php echo number_format(getExtraTotal($row['id']))?></td>
                   
                    <td><?php  $total =  $row['booking_amount'] * $row['duration'] + getExtraTotal($row['id']); 
                    echo number_format($total)?></td>
                   
                      <td><?php echo number_format($total - getSingleBookingDueTotal($row['id']))?></td>
                      <td>
				       Him/Herself
					  </td>
                    
                    </tr>		
		
		 <?php
	  }
	  
	  
}}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
                
                
                 <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>    
                
            <?php }    ?>
            
            
            
            
            
              <?php if($_REQUEST['page']=='booking'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>BOOKING LIST ON ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
      
      
      
      
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
                
                 <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>    
                
            <?php }    ?>
      
      
      
      
                   <?php if($_REQUEST['page']=='rent'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>RENTAL REPORT ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
      
      
      
      
   <table class="table">
                  <thead>
                    <tr>
                        <th>No</th>
                        <th colspan="2">Room Type</th>
                      <th>Guest Names</th>
                          <th>Company</th>
                   
                      <th>Check In</th>
                      <th>Check Out</th>
                      <th>Count</th>
                    
                      <th>Room Rate</th>
                      <th>Debtor </th>
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
        <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                     <td><?php echo $row['company']?></td>
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                     
                   
                       <td><?php echo number_format($row['duration'])?></td>
                       <td><?php echo number_format($row['room_price'])?></td>
                      <td>
				Him/herself
					  </td>
                
                    </tr>		
		
		 <?php
	  }
	  
	  
}}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
                
                 <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>    
                
            <?php }    ?>
            
            
            
            
            
            
            
            
            
            
            
              <?php if($_REQUEST['page']=='room'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>ROOMING REPORT ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
      
      
      <div class="table-responsive">
      
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
                 <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>    
               </div> 
            <?php }    ?>
      
      
    
            
            
            
              <?php if($_REQUEST['page']=='roomstatus'){?>
      
        <br>  <br>
      <br>
          <table border="0" class="table">
          <tr>
     
     
   
      <td colspan="8">

<center>ROOM STATUS REPORT ON <?php echo date('Y-m-d')?>

     </center> </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    
      
      
      
      
      
      
      <div class="table-responsive">
      
   <table class="table">
                  <thead>
                    <tr>
                          <th>No</th>
                           <th>Room Type</th>
                      <th>Guest Names</th>
        <th>check in </th>
              <th>check out</th>    
                    
                      <th>Room Rate</th>
                        <th>Company</th>
                         <th>Telephone</th>
                          <th>Email</th>
                       <th>Status</th>
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
        <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                 
                      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo getGuestNames($row['guest_id'])?></strong></td>
                     
                      <td><?php echo $row['checkin_date']?></td>
                      <td><?php echo $row['checkout_date']?></td>
                     
                       <td><?php echo number_format($row['room_price'])?></td>
                  <td><?php echo $row['company'] ?></td>
                   <td><?php echo getGuestDetail($row['guest_id'],'phone_number')?></td>
                       <td><?php echo getGuestDetail($row['guest_id'],'email_address')?></td>
                         <td><?php echo getRoomStatusName(getRoomStatus(getRoomClass(getBookedRoom($row['id']))))?></td>
                       
                    </tr>		
		
		 <?php
	  }
	  
	  
}}
			?>	  
				  
				  
                 
                 
                   
                  </tbody>
                </table>
                 <td> Printed By: Admin <br> Printed on:<?php echo date('Y-m-d: h:i:s')?>                     
                                    
             </td>    
               </div> 
            <?php }    ?>
    
    
   </body>
</html>

<script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>




<!DOCTYPE html>