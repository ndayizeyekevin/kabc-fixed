<?php 
include '../inc/conn.php';

if(isset($_POST['addtotable'])){ 
    
    
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
    
   $table = $_POST['table'];
$OrderCode  = 0;  
$sql = "SELECT * FROM tbl_cmd_qty where cmd_table_id  ='$table' and cmd_status !=12";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
$OrderCode = $row['cmd_code'];
  }
}  

    
    
    

                                 $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd`  WHERE OrderCode = '".$_GET['c']."'
                            
                                    ");
                                        $sql_rooms->execute();
                                   
                                        while($fetrooms = $sql_rooms->fetch()) {
                                            
                                            $reservat_id = $fetrooms['reservat_id'];
                                            $Serv_id = $fetrooms['Serv_id'];
                                            $company_id = $fetrooms['company_id'];
                                            $menu_id = $fetrooms['menu_id'];
                                            $status_id= $fetrooms['status_id'];
                                            $room_client = $fetrooms['room_client'];
                                            $dTrm = $fetrooms['dTrm'];
                                            if($OrderCode==0){
                                                $OrderCode = $fetrooms['OrderCode']."1";
                                            }
                                            
                                            
                                            
                                        }
     
     
    if(!$room_client){
        $room_client = 0;
    }
     
$table = $_POST['table'];
$sql = "INSERT INTO `tbl_cmd` (`id`, `reservat_id`, `discount`, `Serv_id`, `status_id`, `company_id`, `dTrm`, `OrderCode`, `client`, `menu_id`, `room_client`) 
VALUES (NULL, '$reservat_id', NULL, '$Serv_id', '$status_id', '$company_id', '$dTrm', '$OrderCode', NULL, '$menu_id ', ' $room_client');";

if ($conn->query($sql) === TRUE) {



   $menu_id = $_POST['menu_id'];
  

         for($i=0; $i<count($menu_id); $i++){
			
			$menu_id[$i];
			$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_code` = '".$OrderCode."', cmd_table_id = '$table'  WHERE `cmd_item` = '".$menu_id[$i]."' AND
			`cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			$msg = "Successfully Delivered!";
           // echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
		  }



} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
     
    
    
    
    
    
    
    
    
    

}



if(isset($_POST['separate'])){

//echo "w";
include '../inc/conn.php';


     
                                 $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd`  WHERE OrderCode = '".$_GET['c']."'
                            
                                    ");
                                        $sql_rooms->execute();
                                   
                                        while($fetrooms = $sql_rooms->fetch()) {
                                            
                                            $reservat_id = $fetrooms['reservat_id'];
                                            $Serv_id = $fetrooms['Serv_id'];
                                            $company_id = $fetrooms['company_id'];
                                            $menu_id = $fetrooms['menu_id'];
                                            $status_id= $fetrooms['status_id'];
                                            $room_client = $fetrooms['room_client'];
                                            $dTrm = $fetrooms['dTrm'];
                                            $OrderCode = $fetrooms['OrderCode']."-".time();
                                            
                                            
                                        }
     
     
    if(!$room_client){
        $room_client = 0;
    }
     
     
$sql = "INSERT INTO `tbl_cmd` (`id`, `reservat_id`, `discount`, `Serv_id`, `status_id`, `company_id`, `dTrm`, `OrderCode`, `client`, `menu_id`, `room_client`) 
VALUES (NULL, '$reservat_id', NULL, '$Serv_id', '$status_id', '$company_id', '$dTrm', '$OrderCode', NULL, '$menu_id ', ' $room_client');";

if ($conn->query($sql) === TRUE) {



   $menu_id = $_POST['menu_id'];

         for($i=0; $i<count($menu_id); $i++){
			
			 $menu_id[$i];
			$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_code` = '".$OrderCode."' WHERE `cmd_item` = '".$menu_id[$i]."' AND
			`cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			$msg = "Successfully Delivered!";
           // echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
		  }



} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
     
     
  
   

}?>

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                         <div class="basic-tb-hd">
                          <h2><small><strong><i class="fa fa-list"></i> Order Details - Table No<?php echo $_GET['res']; ?></strong></small></h2> 
                         </div>
                         
                          <form method="POST">
                         
        						<input class="pull-right" type="submit" name="separate" value="Separate invoice ">
        						<select name="table">
        						<option value="0"> select table</option>
        						<?php 
        						
    		
$sql = "SELECT * FROM tbl_tables where table_id !='".$_GET['res']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
      
?>

	<option value="<?php echo $row['table_id'] ?>"> <?php echo $row['table_no'] ?></option>
<?php
  }
}
        						
        						
        						
        						?>
        						
        							<select>
        						
        					<input type="submit" name="addtotable" value="Add to table ">

        				
                         <hr>
                        <br>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                          <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                    
                                    <?php
                                    $totprice = 0; 
                                        $sql_rooms = $db->prepare("SELECT *,SUM(cmd_qty) AS qty FROM `tbl_cmd_qty`
                                    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_code = '".$_GET['c']."'
                                    GROUP BY cmd_item
                                    ");
                                        $sql_rooms->execute();
                                        $i = 0;
                                        while($fetrooms = $sql_rooms->fetch()) {
                                            $i++;
                                            $totprice = $totprice+($fetrooms['qty']*$fetrooms['menu_price']);
                                    ?>
                                 <tr class="gradeU">
                                    <td><input type="checkbox" name="menu_id[]"  value='<?php  echo $fetrooms['menu_id'] ?>' id='<?php  echo $fetrooms['menu_id'] ?>'></td>
        							<td><?php echo $i; ?></td>
                    				<td>x<?php echo $fetrooms['qty'].' '.$fetrooms['menu_name']; ?> <?php if($fetrooms['cmd_status']==100){
                    				echo "<b style='color:red'>Voided<b>" ;}?></td>
                    				<td><?php echo number_format($fetrooms['menu_price']); ?></td>
                    				<td><?php echo number_format($fetrooms['qty']*$fetrooms['menu_price']); ?></td>
        							<td>
        							     <?php echo $fetrooms['created']; ?>
        							</td>
        							
        								<td>
        							
        							    <a href="?resto=edit&&id=<?php echo $fetrooms['cmd_qty_id'] ?>">Edit Quantity</a>
        							      | <a href="void.php?id=<?php echo $fetrooms['cmd_qty_id'] ?>">Void Item</a>
        							    
        							</td>
        							
        							
        						</tr>
					            <?php   
                                        }  
        						?>
        						
        					
        						<br>
        						</form>
    					</tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3"></th>
                                        <th colspan="1">Total: <?php echo number_format($totprice); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->
    
    <div id="update<?php echo $fetrooms['reservation_id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header text-center">
            <h2 class="modal-title"><i class="fa fa-pencil"></i> <?php echo $cpny_ID;?> Profile</h2>
          </div>
          <!-- END Modal Header -->
    
          <!-- Modal Body -->
          <div class="modal-body">
             
                <form id="settings_form" method="POST" enctype="multipart/form-data" class="form-horizontal form-bordered">
                 
                  <fieldset style="margin-top: 10px;">
                     
                     <div class="form-group">
                      <label class="col-md-4 control-label" for="">Firstname</label>
                      <div class="col-md-8">
                        <input type="text" id="cpname" name="cpname" class="form-control" value="<?php echo $cmp_full;?>" placeholder="Enter Company Name..">
                      </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Company Email</label>
                        <div class="col-md-8">
                            <input type="Email" id="cpemail" name="cpemail" class="form-control" value="<?php echo $cpny_email;?>" placeholder="Enter Email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Phone</label>
                        <div class="col-md-8">
                            <input type="text" id="cphone" name="cphone" class="form-control" value="<?php echo $cpny_phone;?>" placeholder="Company Phone">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Address</label>
                        <div class="col-md-8">
                            <input type="text" id="cpny_address" name="cpny_address" class="form-control" value="<?php echo $cpny_address;?>" placeholder="Company Phone">
                        </div>
                    </div>
                    
                  </fieldset>
                    <div class="form-group form-actions" style="margin-top: 10px;">
                        <div class="col-xs-12 text-right">
                            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" name="btn-profile" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>
          <!-- END Modal Body -->
        </div>
      </div>
    </div>