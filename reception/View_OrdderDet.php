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
                                        <th>Total: <?php echo number_format($totprice); ?></th>
                                        <th>
                                            <!-- Invoice Modal Button -->
                                            <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#invoiceModal"><i class="fa fa-file-text-o"></i> Invoice</button>
                                        </th>
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

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Invoice</h4>
          </div>
          <div class="modal-body" id="printbar-invoice">
            <table style="width:100%;border:2px solid #333;border-collapse:collapse;background:#fff;">
            <tr><td>
            <?php
            // Company details
            $logoUrl = 'https://kabc.gope.rw/img/logo.png'; // Change to your logo path if needed
  echo '<div style="text-align:center;margin-bottom:8px;"><img src="' . $logoUrl . '" alt="Logo" style="max-width:120px;max-height:120px;display:inline-block;" /></div>';
  $thankYou = '<div style="margin-top:5px;font-size:12px;text-align:center;color:#28a745;">Thank you, you are always welcome!</div>';
  $companyInfo = '<div style="font-size:14px;line-height:1.4;text-align:center;margin-bottom:4px;">
'.$company_name.'<br>
TIN/VAT : '.$company_tin.'<br>
Tel: '.$company_phone.'<br>
'.$company_email.'<br>
'.$website.'
</div>';
  $momoPay = $momo;
  $momo = '<div style="margin-top:8px;font-size:15px;text-align:center;"><strong>MOMO : '.$momoPay.'</strong></div>
  <div style="margin-top:8px;font-size:15px;text-align:center;">
  <strong>MOMO NAME: '.$momo_name.'</strong>
  </div>
  ';

            echo $companyInfo;
            echo '<hr style="border: none; border-top: 2px solid #333; width: 100%; margin:8px 0;" />';
            
            $order_code_for_invoice = $_GET['c'];
            $stmt_cmd_invoice = $db->prepare("SELECT id, reservat_id, Serv_id FROM tbl_cmd WHERE OrderCode = ? LIMIT 1");
            $stmt_cmd_invoice->execute([$order_code_for_invoice]);
            $cmd_invoice_data = $stmt_cmd_invoice->fetch(PDO::FETCH_ASSOC);

            $orderNo = $cmd_invoice_data ? $cmd_invoice_data['id'] : '';
            $tableId = $cmd_invoice_data ? $cmd_invoice_data['reservat_id'] : '';
            $servantId = $cmd_invoice_data ? $cmd_invoice_data['Serv_id'] : '';

            $tableNo = '';
            if ($tableId) {
                $stmt_table = $db->prepare("SELECT table_no FROM tbl_tables WHERE table_id = ?");
                $stmt_table->execute([$tableId]);
                $tableNo = $stmt_table->fetchColumn();
            }

            $dateTime = date('d-m-Y H:i');
            echo '<div style="text-align:center;font-size:16px;font-weight:bold;margin:8px 0;">Order Number: ' . htmlspecialchars($orderNo) . ' &nbsp; | &nbsp; Table: ' . htmlspecialchars($tableNo) . '</div>';
            echo '<div style="text-align:center;font-size:14px;margin:6px 0;">'
              .'<strong>Date:</strong> ' . htmlspecialchars($dateTime)
              .'</div>';
            echo '<hr style="border: none; border-top: 2px solid #333; width: 100%; margin:8px 0;" />';
            echo '<div style="text-align:center;font-size:15px;font-weight:bold;margin:10px 0;">SERVED ITEMS</div>';
            echo '<table style="width:100%;font-size:16px;border-collapse:collapse;margin:0;padding:0;border:1px solid #333;">';
            echo '<tr style="background:#f8f8f8;">'
              .'<th align="left" style="border:1px solid #333;padding:2px;">Item</th>'
              .'<th align="center" style="border:1px solid #333;padding:2px;">Qty</th>'
              .'<th align="right" style="border:1px solid #333;padding:2px 12px 2px 2px;">Price</th>'
              .'</tr>';
            $sql_items = $db->prepare("SELECT menu.menu_name, menu.menu_price, SUM(cmd_qty) AS qty FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item WHERE tbl_cmd_qty.cmd_code = ? GROUP BY cmd_item");
            $sql_items->execute([$order_code_for_invoice]);
            $total = 0;
            while($item = $sql_items->fetch()) {
              $lineTotal = $item['qty'] * $item['menu_price'];
              $total += $lineTotal;
              echo '<tr>'
              .'<td align="left" style="border:1px solid #333;padding:2px;">'.htmlspecialchars($item['menu_name']).'</td>'
              .'<td align="center" style="border:1px solid #333;padding:2px;">'.$item['qty'].'</td>'
              .'<td align="right" style="border:1px solid #333;padding:2px 12px 2px 2px;">'.(is_numeric($lineTotal) ? number_format($lineTotal) : '0').'</td>'
              .'</tr>';
            }
            echo '</table>';
            echo '<hr style="border: none; border-top: 2px solid #333; width: 100%; margin:8px 0;" />';
            echo '<div style="text-align:right;font-size:17px;font-weight:bold;margin:8px 0;">GRAND TOTAL: <span style="border-bottom:1px solid #333;">'.(is_numeric($total) ? number_format($total) : '0').'</span></div>';
            echo '<hr style="border: none; border-top: 2px solid #333; width: 100%; margin:8px 0;" />';
            
            $waiterName = 'N/A';
            if ($servantId) {
                $stmt_waiter = $db->prepare("SELECT f_name, l_name FROM tbl_users WHERE user_id = ?");
                $stmt_waiter->execute([$servantId]);
                $waiter = $stmt_waiter->fetch(PDO::FETCH_ASSOC);
                if ($waiter) {
                    $waiterName = htmlspecialchars($waiter['f_name'] . ' ' . $waiter['l_name']);
                }
            }
            echo '<div style="margin-top:8px;font-size:15px;text-align:center;"><strong>Served by: ' . $waiterName . '</strong></div>';
            
            $cashierName = isset($_SESSION['f_name'], $_SESSION['l_name']) ? htmlspecialchars($_SESSION['f_name'] . ' ' . $_SESSION['l_name']) : 'N/A';
            echo '<div style="margin-top:4px;font-size:14px;text-align:center;">Printed by: ' . $cashierName . '</div>';

            echo $momo;
            echo '<hr style="border: none; border-top: 1px solid #333; width: 100%; margin:8px 0;" />';
            echo $thankYou;
            echo '<hr style="border: none; border-top: 2px dashed #333; width: 100%; margin:8px 0;" />';
            ?>
            </td></tr>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" id="idinvoice" class="btn btn-info" onclick="printInvoice()">Print</button>
          </div>
        </div>
      </div>
    </div>
    <style>
@media print {
  body {
    visibility: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  #printbar-invoice {
    visibility: visible !important;
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    font-size: 12px !important; /* Small font for thermal printing */
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
    box-shadow: none !important;
  }
  #invoiceModal .modal-content, #invoiceModal .modal-dialog {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
  }
  @page {
    size: auto; /* auto size for receipt printers */
    margin: 0mm; /* remove default margins */
  }
    }
    </style>
    <script>
    function printInvoice() {
        window.print();
    }
    </script>
    
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
