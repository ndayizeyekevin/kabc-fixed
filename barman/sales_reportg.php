<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])){
    $from = date('Y-m-d');
    $to = date("Y-m-d");
   }
   else{
       $from = $_SESSION['date_from'];
       $to = $_SESSION['date_to'];
   }
   
   if (isset($_POST['from'])) {
        $from = $_SESSION['from'];
       $to = $_SESSION['to'];
   }
   
  
   
    function mostSales($id){
       include '../inc/conn.php';
       $cmd_qty = 0;
       $sql = "SELECT cmd_qty FROM tbl_cmd_qty where cmd_item ='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
   
   $cmd_qty = $cmd_qty + $row['cmd_qty'];
   
  }
} 
return $cmd_qty;
   }
?>
<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> Report Per Day</strong></small></h2>
                        </div>
                     <hr>
            <form action="" method="POST">
                 <div class="row">
                   <label class="col-md-2 control-label" for=""><strong>Date From </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <input type="date" name="from" id="date_from" value="<?php echo $from?>" name="date_from" class="form-control">
                    </div>
                    <label class="col-md-2 control-label" for=""><strong>Date To </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <input type="date" name="to" id="date_to" value="<?php echo $to?>" name="date_to" class="form-control">

                    </div>
                    <div class="col-md-2">
                        <!-- <button type="submit" class="btn btn-success"> Search</button> -->
                    </div>

                    
                   </div>
            </form>
            <br>
            <a href="?resto=printBar" class="btn btn-info"> Print</a>
            <br>
            <div class="table-responsive">
                              <table id="data-table-basic" class="table table-striped">
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
                            		if($_GET['resto'] == 'report')
                                    {
                                        $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE DATE(created) BETWEEN '".$_SESSION['date_from']."' AND '".$_SESSION['date_to']."' AND cmd_status = '12' AND menu.cat_id != '2'
                                    GROUP BY cmd_item");
                            		$sql->execute(array());
                                    }elseif($_GET['resto'] == 'reportb')
                                    {

                                        $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE DATE(created) BETWEEN '".$from."' AND '".$to."' AND cmd_status = '12' AND menu.cat_id = '2'
                                    GROUP BY cmd_item");
                            		$sql->execute(array());
                                    }
                            		
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                                   $OrderCode = $fetch['cmd_code'];

                                   $amount = $fetch['menu_price']*$fetch['totqty'];
                            	   $tottax = (int)$amount*(int)$fetch['tax'];
                                 	?>
                                    <tr>
                                        <td><?php echo $fetch['item_code']; ?></td>
                                        <td><?php echo $fetch['menu_name']; ?></td>
                                        <td><?php echo $fetch['menu_desc']; ?></td>
                                        <td><?php echo number_format($fetch['menu_price']); ?></td>
                                        <td><?php echo $fetch['totqty']; ?></td>
                                        <td><?php echo number_format($amount);?></td>
                                
                                        <td><?php echo number_format($amount * $fetch['totqty']);?></td>
                                    </tr>
                                    <?php 
                            		    } 
                            		    
                            		
                                    ?>
                                </tbody>
                            </table>
        </div>
        
        
        
        
        
        
        
        
        
        
        
        <h3>Most Sold Items</h3>
            <br>
            <div class="table-responsive">
                              <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ITEM NO</th>
                                        <th>ITEM NAME</th>
                                        <th>QTY</th>
                                        <th>PRICE</th>
                                        <th>Total Price</th>
                                     
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                   
                                    $sql = $db->prepare("SELECT * From menu where cat_id = '2'");
                            		$sql->execute(array());
                                    
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                                        if(mostSales($fetch['menu_id']) > 0){
                                 	?>
                                    <tr>
                                        <td><?php echo $fetch['item_code']; ?></td>
                                        <td><?php echo $fetch['menu_name']; ?> </td>
                                            
                                             <td><?php echo mostSales($fetch['menu_id'])?> </td>
                                           <td><?php echo $fetch['menu_price']; ?> </td>  
                                           
                                                <td><?php echo mostSales($fetch['menu_id']) *$fetch['menu_price']; ?>  RWF</td>
                                    </tr>
                                    <?php 
                                    }
                            		    } 
                            		    
                            		}
                                    ?>
                                </tbody>
                            </table>
        </div>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        

        $("#date_to").on("change", function () {
            console.log("change date");
            
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