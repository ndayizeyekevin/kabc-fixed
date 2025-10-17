<?php
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
   
   
function getTotalPaidByMethod($code,$method){
       
       include '../inc/conn.php';
       
       
       
 $sql = "SELECT * FROM payment_tracks where created_at >= '$code' AND method = '$method' "; 
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     $sale =  $row['amount'] ;
        
    
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


$date = $_POST['closedate'];
   $time = time();
   $from = lastday();
   $sql = "INSERT INTO `days` (`id`, `date`, `from_id`, `to_id`, `created_at`) VALUES (NULL, '$date', '$from', '$lastid', '$time');";

if ($conn->query($sql) === TRUE) {
  echo "<script>Day Successfull closed</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
   
   

}


?>
<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                           <?php  $last= lastday();?>
                        </div>
                
             <form action="" method="POST" >
                 <div style="padding:30px">
                  Select Day: 
                    
                       <select name="selectday" class = "form-control">
                     <?php

                                    $sql = $db->prepare("SELECT *  FROM days");
                            		$sql->execute(array());
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){


   ?><option value="<?php echo $fetch['id']?>"> <?php echo $fetch['date']?> </option><?php 
  }
}
   ?>                        
                       </select>
                       
                       
                       
                       <br>
                         <button name="check">Load</button>
                  
            </form>
            <br>
            <br>
            
            
            
              	 <button onclick ="printInvoice()"> Print </button>
           
                 <div id = "content">           
             
            
     <?php include '../holder/printHeader.php'?>
            
            <div class="table-responsive">
                <?php if(isset($_POST['check'])){
                    
                    
                                   $selectday = $_POST['selectday'];
                                   $sql = $db->prepare("SELECT *  FROM days WHERE id ='$selectday'");
                            		$sql->execute(array());
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		    
                            		     $fromId = $fetch['from_id'];
                            		     $lastId = $fetch['to_id'];
                            		}}
                
                
                ?>
                
                
                  	 
                  	 
                              <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ITEM NO</th>
                                        <th>ITEM NAME</th>
                                        <th>ITEM DESCRIPTION</th>
                                        <th>PRICE</th>
                                        <th>QTY</th>
                                        <th>AMOUNT</th>
                                     
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $i = 0;
                            
                                    $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE  cmd_qty_id > '$fromId' and cmd_qty_id <= '$lastId' AND cmd_status = '12' 
                                    GROUP BY cmd_item");
                            		$sql->execute(array());
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                                   $OrderCode = $fetch['cmd_code'];

                                   $amount = $fetch['menu_price']*$fetch['totqty'];
                            	   $tottax = $amount * (int)$fetch['tax'];
                            	   $n = 1;
                                 	?>
                                    <tr>
                                        <td><?php echo $i ; ?></td>
                                        <td><?php echo $fetch['menu_name']; ?></td>
                                        <td><?php echo $fetch['menu_desc']; ?></td>
                                        <td><?php echo number_format($fetch['menu_price']); ?></td>
                                        <td><?php echo $fetch['totqty']; ?></td>
                                        <td><?php echo number_format($amount);?></td>
                                      
                                        <td><?php echo number_format($amount);?></td>
                                    </tr>
                                    <?php 
                            		    } 
                            		    
                            		}
                                    ?>
                                    
                                       
                                      
                             
                                    
                                    
                                </tbody>
                            </table>
                            
                            
                            
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
                            
                                   
                                    
                                    
                                    $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE  cmd_qty_id > '$fromId' and cmd_qty_id <= '$lastId' AND cmd_status = '12' 
                                    GROUP BY cmd_code");
                                    
                                    
                            		$sql->execute(array());
                                    
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		    
                            		
                            		   
                            		   
                            		   
                            		    
                            		}}else{
                            		    //echo $last;
                            		}
                            		
                            		
                            		
                            		
                            		   $timestamp = strtotime($selectday . " 00:00:00");
                                       $cash = $cash + getTotalPaidByMethod($timestamp,'01');
                            		   $card = $card + getTotalPaidByMethod($timestamp,'05');
                            		   $momo = $momo + getTotalPaidByMethod($timestamp,'06');
                            		   $credit= $credit + getTotalPaidByMethod($timestamp,'02');
                            		   $cheque= $cheque + getTotalPaidByMethod($timestamp,'04');
                            		   
                            		   $cashadvance = $cashadvance + getTotaladvancePaidByMethod($selectday,'01');
                            		   $cardadvance = $cardadvance + getTotaladvancePaidByMethod($selectday,'05');
                            		   $momoadvance = $momoadvance + getTotaladvancePaidByMethod($selectday,'06');
                            		   $creditadvance= $creditadvance + getTotaladvancePaidByMethod($selectday,'02');
                            		   $chequeadvance= $chequeadvance + getTotaladvancePaidByMethod($selectday,'04');
                            		   
                            		   
                            		   $cashcollection = $cashcollection + getTotalCollectionPaidByMethod($selectday,'01');
                            		   $cardcollection = $cardcollection + getTotalCollectionPaidByMethod($selectday,'05');
                            		   $momocollection = $momocollection + getTotalCollectionPaidByMethod($selectday,'06');
                            		   $creditcollection= $creditcollection + getTotalCollectionPaidByMethod($selectday,'02');
                            		   $chequecollection= $chequecollection + getTotalCollectionPaidByMethod($selectday,'04');
                            
                            
                            ?>
                            
<!--                             
                            <h4> Collection:  <?php echo number_format(
                                  $cashcollection + $cardcollection + $momocollection +  $chequecollection  + $creditcollection) ?> RWF</h4> 


                                  
                                   <h4>Advance:  <?php echo number_format(
                                  $cashadvance + $cardadvance + $momoadvance +  $chequeadvance + $creditadvance ) ?> RWF</h4> 
                            
                            
                             <hr>
                                          
                               
                                    <h5>Cash:  <?php echo number_format($cash  + $cashcollection  + $cashadvance )?> RWF </h5>
                                    <h5>POS: <?php echo number_format($card + $cardcollection  + $cardadvance)?> RWF </h5>
                                    <h5>Momo: <?php echo number_format($momo + $momocollection  + $momoadvance)?> RWF</h5>
                                     <h5>Credit: <?php echo number_format($credit + $creditcollection  + $creditadvance)?> RWF </h5>
                                     <h5>Bank Cheque: <?php echo number_format($cheque + $chequecollection  + $chequeadvance)?> RWF </h5>
                                  
                                        <hr>
                                  <h4> Total:  <?php echo number_format($credit + $cash + $card + $momo +  $cheque  + $credit + 
                                  $cashcollection + $cardcollection + $momocollection +  $chequecollection  + $creditcollection +
                                  $cashadvance + $cardadvance + $momoadvance +  $chequeadvance + $creditadvance ) ?> RWF</h4> 
                            <?php  } ?>
                             -->
                            <?php include '../holder/printFooter.php'?>
                            
        </div>
    </div>
    
</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

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