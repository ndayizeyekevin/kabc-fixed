<?php if(isset($_POST['separate'])){

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../inc/conn.php';

 {
     
     
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
  //echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
     
     
     
    try{
        $menu_id = $_POST['menu_id'];

         for($i=0; $i<count($menu_id); $i++){
			
			echo $menu_id[$i];
			$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_code` = '".$OrderCode."' WHERE `cmd_item` = '".$menu_id[$i]."' AND
			`cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			$msg = "Successfully Delivered!";
           // echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
		  }

	}catch(PDOException $e){
	 echo $e->getMessage();
	 }
   }

}?>

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                         <div class="basic-tb-hd">
                                                       <h2><small><strong><i class="fa fa-list"></i> Order Details - Table No<?php $_SESSION['tableno'] =$_GET['res']; echo $_GET['res']; ?></strong></small></h2> 

                          
                         </div>
                                                 
                        
                    <?php    $ebmdata="";
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
                                            $totprice ??= 0;
                                            $totprice = $totprice+($fetrooms['qty']*$fetrooms['menu_price']);
                                                    //   die(var_dump($fetrooms));

                                                                                      $ebmdata = $ebmdata . '<tr><td align="left" style="padding-left: 15px;">'.$fetrooms['menu_name'].'<br>'.$fetrooms['menu_price'].'.00x </td><td align="center"><br>'.$fetrooms['qty'].'.00</td><td align="center">'.$fetrooms['menu_price'].'</td><td align="center"><br>'.((float)$fetrooms['qty']*(float)$fetrooms['menu_price']).'</td></tr>';
// die(var_dump($ebmdata));
                                        //   $ebmdata = $ebmdata . '<tr><td align="left" style="padding-left: 15px;">'.$fetrooms['menu_name'].'<br>'.$fetrooms['menu_price'].'.00x </td><td align="center"><br>'.$fetrooms['qty'].'.00</td><td align="center"><br>'.$fetrooms['qty']*$fetrooms['menu_price'].'</td></tr>';
            
                                                               
        						
        						
        						
        						
			
        						
 
                                        }  
                                        
$count ??=0;$serv??=0;
$_SESSION['tin'] =$count;
$_SESSION['servant'] = $serv;
$_SESSION['ebmdata']= $ebmdata;
$_SESSION['total']= $totprice;
//$_SESSION['tax']= $totprice* 0.18 ;


?>
                         <hr>
                        <br>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <form method="post">
                                    
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
        							<td><?php echo $i; ?></td>
                    				<td>x<?php echo $fetrooms['qty'].' '.$fetrooms['menu_name']; ?></td>
                    				<td><?php echo number_format($fetrooms['menu_price']); ?></td>
                    				<td><?php echo number_format($fetrooms['qty']*$fetrooms['menu_price']); ?></td>
        							<td>
        							     <?php echo $fetrooms['created_at']; ?>
        							</td>
        							
        								<td>
        							
        							     <input type="checkbox" name="menu_id[]"  value='<?php  echo $fetrooms['menu_id'] ?>' id='<?php  echo $fetrooms['menu_id'] ?>'>
        							    
        							</td>
        							
        							
        						</tr>
					            <?php   
                                        }  
        						?>
        						
        						<?php if($i>1){?>
        						<button hidden name="separate">Separate invoice </button>
        						<?php } ?>
        						</form>
    					</tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3"></th>
                                        <th colspan="1">Total: <?php echo number_format($totprice); ?></th>
                                        <th> <a href="https://saintpaul.gope.rw/reciept/pro-forma.php?ref=<?php echo $_REQUEST['c']?>" class="btn btn-secondary btn-sm" onclick="if(!confirm('Do you really generate invice?'))return false;else return true;"><i class="fa fa-step-invoice"></i>Invoice</a></th>
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