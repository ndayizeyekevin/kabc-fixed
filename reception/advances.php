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
   
   
  if(isset($_POST['addPaymentTocredit'])){
      
      include '../inc/conn.php';

      $method = $_POST['method'] ; 
      $type = $_REQUEST['type'] ; 
      $clientname = $_POST['clientname'] ; 
      $amount = $_POST['amount'] ;
      $remark = $_POST['desc'] ;
        
        
$date = date('Y-m-d');
        
$time = time();

$sql = "INSERT INTO `advances` (`id`, `advance_type`, `amount`, `advance_by`, `paid_by`, `description`, `created_at`, `advance_date`) VALUES (NULL, '$type', '$amount', '$clientname', '$method', '$remark', '$time', '$date');";

if ($conn->query($sql) === TRUE) {

} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
if($type==1){
   $types  = "less advance";
}else{
   $types  = "advance"; 
} 

$sql = "INSERT INTO `payment_tracks` (`id`, `amount`, `method`, `order_code`, `service`, `created_at`,remark) VALUES (NULL, '$amount', '$method', '0', '$types','$time','$remark');";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Payment Added')</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
		 
}
       
        
        
        
        
     
      
      
  
   
   
   function getTotalPaidByMethod($code,$method){
       
       include '../inc/conn.php';
       
 
$sql = "SELECT * FROM payment_tracks where  order_code = '$code' AND method = '$method' ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     $sale = $sale + $row['amount'] ;
        
    
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
                              <h2><small><strong><i class="fa fa-refresh"></i> Today's Advances  </strong></small></h2>
                              
                            
                            	 <li> <a href="#" class='btn btn-info' data-toggle="modal" data-target="#creditclient" data-placement="left" title="Add Order to room">New Advance</a>
   	 </li> 
                        </div>
                     <hr>
          
            <br>
            <br>
            <div class="table-responsive">
                              <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                         <th>DATE</th>
                                        <th>Advance for </th>
                                        <th>Names</th>
                                        <th>Description</th>
                                        <th>AMOUNT</th>
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $i = 0;
                                    $sql = $db->prepare("SELECT * FROM advances where advance_type='".$_REQUEST['type']."'");
                            		$sql->execute(array());
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                                 
                                 	?>
                                    <tr>
                                        <td><?php echo $i ?></td>
                                        <td><?php echo date('Y-m-d h:i:s',$fetch['created_at']); ?></td>
                                        
                                            <td><?php if($fetch['advance_type']==1){
                                                echo "Less advance";
                                            }else{
                                            
                                             echo "Advance for event and service";
                                            }?></td>
                                    
                                        <td><?php echo $fetch['description']; ?></td>
                                         <td><?php echo $fetch['advance_by']; ?></td>
                                        <td><?php echo $fetch['amount']; ?></td>
                              
                                    </tr>
                                    <?php 
                            		    } 
                            		    
                            		}
                                    ?>
                                    
                                       
                                      
                             
                                    
                                    
                                </tbody>
                            </table>
                            
                            
                                <div class="modal fade" id="creditclient" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form  enctype="multipart/form-data" method="POST">
            <div class="row">
            <div class="form-group">
                <div class="col-md-12">
                    
        
                      <br> 
                   
            Names <br> 
           <input type="text" name="clientname" class="form-control" placeholder="Names" required>
                        
        
  <br>
    
            Amount<br>
           <input type="number"  min="1"  class="form-control" name="amount" placeholder="Amount" required>
           
             <br>
           Method<br>
           <select name="method"  class="form-control"  required>
         
                                             	<?php $cuntries = getInfoTable(7);
														foreach ($cuntries as $key => $value) {
														?>
														<option value="<?php echo trim($value[1]) ?>"><?php echo $value[3] ?></option>
														<?php }
														?>
            
               <select>
                   
                   
                   <br>
                     Additional info<br>
     <textarea type="text"  class="form-control"  name="desc"></textarea>
     <br>
            </div>
            </div>
             <button type="submit" name="addPaymentTocredit" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save </button>
            </div>
            
            
            </form>
            </div>
        </div>
    </div>
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