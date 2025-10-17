<?php session_start();
$last= lastday();
if(!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])){
    $from = date('Y-m-d');
    $to = date("Y-m-d");
   }
   else{
       $from = $_SESSION['date_from'];
       $to = $_SESSION['date_to'];
   }
   ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
   
   
  
  
  
       


 function  getadvances($date,$type){
include '../inc/conn.php';
$amount = 0;

$sql = "SELECT * FROM advances where advance_type= '$type'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

       if(isset($_SESSION['fromdate'])){
          $from = $_SESSION['fromdate'];
          $to = $_SESSION['todate'];
       }else{
    
       }

     if($date==date('Y-m-d',$row['created_at'])){
    
     $amount = $amount  + $row['amount'];
        
     }
  }
}


return $amount;
}


function getTotalCreditAmount($id){
  include '../inc/conn.php';
  $amount = 0; 
  $sql = "SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty` INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item WHERE cmd_code ='$id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

  
    
       $amount = $amount + $row['cmd_qty'] * $row['menu_price'];
 
  }
}
  
  
  return $amount;
}

function getcreditDetails($id){
include '../inc/conn.php';
$names = "";
$sql = "SELECT * FROM payment_tracks inner join tbl_cmd on payment_tracks.order_code=tbl_cmd.OrderCode WHERE payment_tracks.method ='02'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      
        if($id==date('Y-m-d',substr((string)$row['order_code'], 0, 10))){
    
         $names = $names.getCreditNames($row['creadit_user']) ." ".number_format(getTotalCreditAmount($row['OrderCode']))."<br>";
    
        
     }
      
  
  
  }
}

return strtoupper($names);
    
}

function getcredits($id){
include '../inc/conn.php';
$amount = 0;

$sql = "SELECT * FROM payment_tracks WHERE method ='02'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     if($id==date('Y-m-d',substr((string)$row['order_code'], 0, 10))){
    
       $amount = $amount + $row['amount'];
        
     }
  }
}


return strtoupper($amount);












  
}





function getCreditNames($id){

global $db;
    include '../inc/conn.php';
                               
                                $sqli = $db->prepare("SELECT * FROM `creadit_id` WHERE id='$id'");
                        		$sqli->execute();
                        		while($row = $sqli->fetch()){
                        		  return  $name =  $row['f_name']." ".$row['l_name'];
                        		}
                        		
                        		
                        	
                        		
}

function getNames($id){

global $db;
    include '../inc/conn.php';
                               
                                $sqli = $db->prepare("SELECT * FROM creadit_id where id ='$id'");
                        		$sqli->execute();
                        		while($row = $sqli->fetch()){
                        		  return  $name =  $row['f_name']." ".$row['l_name'];
                        		}
                        		
                        		
                        	
                        		
}         		
         
         
function  getAdvanceDetails($to,$type){

include '../inc/conn.php';
$names = "";

$sql = "SELECT * FROM advances where advance_type= '$type'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     if($to==date('Y-m-d',$row['created_at'])){
    
     $names = $names.$row['advance_by']." - ".$row['amount']." RWF <br>";
        
     }
  }
}


return strtoupper($names);
    
    
      
}
         
                        		

function  getCollectionDetails($to){
    
    include '../inc/conn.php';
$names = "";

$sql = "SELECT * FROM collection";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     if($to==date('Y-m-d',$row['created_at'])){
    
     $names = $names. getNames($row['names'])." - ".$row['amount']." RWF <br>";
        
     }
  }
}


return strtoupper($names);
    
    
      
  }
  
  
  
  
 
 function  getcollection($date){
include '../inc/conn.php';
$amount = 0;

$sql = "SELECT * FROM collection";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

       if(isset($_SESSION['fromdate'])){
          $from = $_SESSION['fromdate'];
          $to = $_SESSION['todate'];
       }else{
    
       }

     if($date==date('Y-m-d',$row['created_at'])){
    
     $amount = $amount  + $row['amount'];
        
     }
  }
}


return $amount;
}
   
   
  
      
   
   
   
   
   
   
   
   
   
   
   
   
   
   
  
   
function  getPartners($category,$last){
           include '../inc/conn.php';
$names = "";   
$sql = "SELECT cmd_code FROM `tbl_cmd_qty` INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item WHERE cmd_qty_id > '$last' AND cmd_status = '12'  AND cat_id = '$category' group by  cmd_code";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     $names  = $names." ".getCreditUserId($row['cmd_code']);
        
    
  }
}

return $names;
}

  function  getCreditUserId($code){
           include '../inc/conn.php';
    $names = "";   
       $sql = "SELECT creadit_user FROM  tbl_cmd  WHERE OrderCode = '$code'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

    return  getAccount($row['creadit_user']);
        
    
  }
}



}

  function  getAccount($code){
           include '../inc/conn.php';
    $names = "";   
       $sql = "SELECT  l_name,f_name FROM  creadit_id  WHERE id = '$code'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     $names = $row['l_name']." ".$row['f_name'];
        
    
  }
}



      return $names;
       
       
       
   }
   
  
   function getTotalByService($category,$from,$last){
       
       include '../inc/conn.php';
       
 
 if($last==0){
    $sql = "SELECT menu_price,cmd_qty FROM `tbl_cmd_qty` INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item WHERE cmd_qty_id > '$from'   AND cmd_status = '12'  AND cat_id = '$category'"; 
 }else{
$sql = "SELECT menu_price,cmd_qty FROM `tbl_cmd_qty` INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item WHERE cmd_qty_id > '$from' AND cmd_qty_id <= '$last'  AND cmd_status = '12'  AND cat_id = '$category'";
}
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     $sale = $sale + $row['menu_price']*$row['cmd_qty'];
        
    
  }
}




      return $sale;
       
       
   }
   
   
   
   
   
     
      function getTotalCollectionPaidByMethod($date,$method){
       
       include '../inc/conn.php';
       
       

    $sql = "SELECT * FROM payment_tracks where service = 'collection'  AND method = '$method' "; 
      

$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
          if($date==date('Y-m-d',$row['created_at'])){

              $sale = $sale + $row['amount'] ;
     
     
          }
        
    
  }
}


      return $sale;
       
       
   }
   
   
   
   
   
   
   
      function getTotaladvancePaidByMethod($date,$method){
       
       include '../inc/conn.php';
       
       

    $sql = "SELECT * FROM payment_tracks where service = 'advance'  AND method = '$method' "; 
      

$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
          if($date==date('Y-m-d',$row['created_at'])){

              $sale = $sale + $row['amount'] ;
     
     
          }
        
    
  }
}


      return $sale;
       
       
   }
   
   
   
   
   
function getTotalPaidByMethod($code,$method){
       
include '../inc/conn.php';
$sql = "SELECT * FROM payment_tracks where method = '$method' "; 
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
        
        
        if($row['order_code']){
        if($code==date('Y-m-d',substr((string)$row['order_code'], 0, 10))){

              $sale =  $sale + $row['amount'];
     
          }}
  
  
        
    
  }
}


      return $sale;
       
       
}
   
   
   




 function lastday(){
     
     
     
 include '../inc/conn.php';

 
 $sql = "SELECT * FROM days ORDER BY id DESC LIMIT 1 ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
      $last = $row['to_id'];
      
      
  }}
  
   return $last;

}



if(isset($_POST['close'])){
     
     
     
 include '../inc/conn.php';

 
 $sql = "SELECT * FROM tbl_cmd_qty ORDER BY cmd_qty_id DESC LIMIT 1 ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
      $lastid = $row['cmd_qty_id'];
      
      
  }}
  
  if (isset($_POST['approve'])){
  
  
  $date = $_POST['closedate'];
   $time = time();
   $from = lastday();
   $sql = "INSERT INTO `days` (`id`, `date`, `from_id`, `to_id`, `created_at`) VALUES (NULL, '$date', '$from', '$lastid', '$time');";

if ($conn->query($sql) === TRUE) {
  echo "<script>Day Successfull closed</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
  
  
} else {
echo "<script>alert('Please comfirm data before closing the day')</script>";
}




   
   

}


?>
<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                            
                            
                            
                           
           <form action="" method="POST" >
                 <div style="padding:30px">
                  Select Day: 
                    
                       <select name="selectday" class = "form-control">
                     <?php

                                    $sql = $db->prepare("SELECT *  FROM days");
                            		$sql->execute(array());
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){


   ?><option value="<?php echo $fetch['date']?>"> <?php echo $fetch['date']?> </option><?php 
  }
}
   ?>                        
                       </select>
                       
                       
                       
                       <br>
                         <button name="check">Load</button>
                  
            </form>
                        </div>
                        
                        <button onclick =" printInvoice()"> Print </button>
                     <hr>
          
            <br>
            <br>
             <div id = "content">  
             
             
   <?php include '../holder/printHeader.php'?>
      
      <?php 
      
      
                                  
if(isset($_POST['check'])){
    
    $to  =$_POST['selectday'];
    
    
    
    

                                   $sql = $db->prepare("SELECT * FROM days WHERE date ='$to'");
                            		$sql->execute(array());
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		    
                            		  $from = $fetch['from_id'];
                            		    
                            		}}else{
                            		   $from = 'none';
                            		}    
                            		
                            		
                            		
                            		  $sql = $db->prepare("SELECT * FROM days WHERE date ='$to'");
                            		$sql->execute(array());
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		    
                            		    // $fromId = $fetch['from_id'];
                            		     $last = $fetch['to_id'];
                            		}}else{
                            		    $last  = 'none'; 
                            		} 
    
                  
                                    
                                    
                                     
                                    
      $reportDate    = $to; 
      
      $date = $to;
    
  
}else{
    

       
                             
                                    
                                      $reportDate    =  date('Y-m-d');    
                                      $to = date("Y-m-d");
                                      $from = lastday();
                                      $last = 0;
                                      
                                      
                                      
                                      
                                      
                                      
                                      
                                      
                                      
                              
                      
                      
   }
                            
         
      
      
      
      
      
      
      ?>
             
            
             
             <?php
                           
                            
                                       $cash =  0;
                            		   $card = 0;
                            		   $momo = 0;
                            		   $credit= 0;
                            		    $transfer= 0;
                            		     $cheque= 0;
                            
                             $cashcollection = 0;
                              $cardcollection = 0;
                               $momocollection = 0;
                                $creditcollection = 0;
                                 $chequecollection = 0;
                                 
                                 
                                 
                             $cashadvance = 0;
                              $cardadvance = 0;
                               $momoadvance = 0;
                                $creditadvance = 0;
                                 $chequeadvance = 0;

                            		   $cash = $cash + getTotalPaidByMethod($to,'01');
                            		   $card = $card + getTotalPaidByMethod($to,'05');
                            		   $momo = $momo + getTotalPaidByMethod($to,'06');
                            		   $credit= $credit + getTotalPaidByMethod($to,'02');
                            		   $cheque= $cheque + getTotalPaidByMethod($to,'04');
                            		   
                            		   $cashadvance = $cashadvance + getTotaladvancePaidByMethod($to,'01');
                            		   $cardadvance = $cardadvance + getTotaladvancePaidByMethod($to,'05');
                            		   $momoadvance = $momoadvance + getTotaladvancePaidByMethod($to,'06');
                            		   $creditadvance= $creditadvance + getTotaladvancePaidByMethod($to,'02');
                            		   $chequeadvance= $chequeadvance + getTotaladvancePaidByMethod($to,'04');
                            		   
                            		   
                            		   $cashcollection = $cashcollection + getTotalCollectionPaidByMethod($to,'01');
                            		   $cardcollection = $cardcollection + getTotalCollectionPaidByMethod($to,'05');
                            		   $momocollection = $momocollection + getTotalCollectionPaidByMethod($to,'06');
                            		   $creditcollection= $creditcollection + getTotalCollectionPaidByMethod($to,'02');
                            		   $chequecollection= $chequecollection + getTotalCollectionPaidByMethod($to,'04');
                            
                            
                            ?>
                            
                      <hr>
                
                        <h4><center>Report for <?php echo $to ?></center><h4> <hr>
           
            <div class="table-responsive">
                              <table  class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Total</th>
                                      
                                    
                                      
                                    </tr>
                                </thead>
                                <tbody>
                              
                                    <tr>
                                        <td>Bar Sales</td>
                                        <td><?php echo number_format ($totalbar=getTotalByService(2,$from,$last)) ?></td>
                                      
                                    </tr>
                                       
                                          <tr>
                                        <td>Resto Sales</td>
                                         <td><?php echo number_format ($totalresto=getTotalByService(1,$from,$last)) ?></td>
                                        
                                         </tr>
                                         
                                            <tr>
                                        <td>Coffe Shop</td>
                                         <td><?php echo number_format ($totalcoffe=getTotalByService(32,$from,$last)) ?></td>
                                        
                                         </tr>
                                        <tr>
                                        <td>Collections <br> 
                                                <?php echo getCollectionDetails($to);?></td>
                                          <td> <?php echo number_format ($totalcol=getcollection($to)) ?> </td>
                                         </tr>
                             
                             
                                        <tr>
                                        <td>Less Advance<br><?php echo getAdvanceDetails($to,1);?></td>
                                        
                                          <td> <?php echo number_format ($totalless=getadvances($to,1)) ?> </td>
                                         </tr>
                                        <tr>
                                                                                <td>Advance <br>
                                         <?php echo getAdvanceDetails($to,2);?> </td>
                                          <td> <?php echo number_format ($totallad=getadvances($to,2)) ?> <br>
                                         
                                         
                                         
                                          
                                          
                                          </td>
                                         </tr>
                                         
                                         
                                         <tr hidden>
                                         <td>Credits: <br>
                                         <?php echo getcreditDetails($to);?> </td>
                                          <td>   (<?php echo number_format (getcredits($to))?> )<br>
                                         
                                         
                                         
                                          
                                          
                                          </td>
                                         </tr>
                                         
                                         
                                         
                                          <td>Total:</td>
                                          <td rowspan="2"> <?php echo number_format ($totalresto +  $totallad + $totalcol + $totalcoffe + $totalbar  - $totalless) ?> </td>
                                         </tr>
                                   
                                    
                                    
                                </tbody>
                            </table>
                            
                            <table>
                              
                                    
                                 
                                <hr>
                                          
                               
                                    <h5>Cash:  <?php echo number_format($cash  + $cashcollection  + $cashadvance )?> RWF </h5>
                                    <h5>POS: <?php echo number_format($card + $cardcollection  + $cardadvance)?> RWF </h5>
                                    <h5>Momo: <?php echo number_format($momo + $momocollection  + $momoadvance)?> RWF</h5>
                                     <h5>Credit: <?php echo number_format($credit + $creditcollection  + $creditadvance)?> RWF </h5>
                                     <h5>Bank Cheque: <?php echo number_format($cheque + $chequecollection  + $chequeadvance)?> RWF </h5>
                                  
                                        <hr>
                                  <h4> Total:  <?php echo number_format($cash + $card + $momo +  $cheque  + $credit + 
                                  $cashcollection + $cardcollection + $momocollection +  $chequecollection  + $creditcollection +
                                  $cashadvance + $cardadvance + $momoadvance +  $chequeadvance + $creditadvance ) ?> RWF</h4> 
                                     
                                
                            

                           
                            <hr>
                        <br>  <br>  <br>  <br>
                            
                  <?php include '../holder/printFooter.php'?>
                       </div> </div> </div> </div>
                            
        </div>  </div>
    </div>
    </div></div></div>
</div>
</div>
<script> function printInvoice() { 
$("#headerprint").show();
var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>



<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

$("#headerprint").hide();
        $("#date_to").change(function () {
            var from = $("#date_from").val();
            var to = $("#date_to").val();
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.post('load_sales_report.php',{from:from,to:to} , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
                location.reload();
            });
        });

    });
</script>