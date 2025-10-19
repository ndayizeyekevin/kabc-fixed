<?php 


error_reporting(E_ALL);
ini_set("display_errors",1);


include '../inc/conn.php';	

function getItemName($id){
	
include '../inc/conn.php';		

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['item_name'];
  }
}

}

function getItemPrice($id) {
    include '../inc/conn.php';

    // Get the item from tbl_items
    $itemQuery = "SELECT * FROM tbl_items WHERE item_id = '$id'";
    $itemResult = $conn->query($itemQuery);

    if ($itemResult && $itemResult->num_rows > 0) {
        $item = $itemResult->fetch_assoc();
        $defaultPrice = $item['price'];

        // Check tbl_progress for the latest new_price
        $progressQuery = "SELECT new_price FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1";
        $progressResult = $conn->query($progressQuery);

        if ($progressResult && $progressResult->num_rows > 0) {
            $progress = $progressResult->fetch_assoc();
            if ($progress['new_price'] > 0) {
                return $progress['new_price'];
            }
        }

        // Fallback to default item price
        return $defaultPrice;
    }

    // If item not found, return null or any fallback value you want
    return null;
}


function getItemUnitId($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['item_unit'];
  }
}

}


function getItemUnitName($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM tbl_unit where unit_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['unit_name'];
  }
}

}




		
	function getSupplierName($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM suppliers where id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    
    return $row['name'];
  }
}

}
?>
<?php 
function fill_product($db){
  $output= '';

  $select = $db->prepare("SELECT * FROM tbl_items WHERE item_status = 1");
  $select->execute();
  $result = $select->fetchAll();

  foreach($result as $row){
    $output.='<option value="'.$row['item_id'].'">'.$row["item_name"].'</option>';
  }

  return $output;
}
?>

<style>
    .form-ic-cmp{
        margin-bottom:10px;
    }
    .table-bordered{
        border:1px;
        border-color:black;
    }
</style>

	<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				
				
                    <div class="data-table-list">
			
			
			<hr>
					<BR>
										<BR>
					
                        <?php if(empty($_GET['req'])){ ?>
                            <a href="#" class="btn btn-primary" onclick="printPDF()">Print PDF</a>
                        <div id="content">
                            <?php
                            include "../holder/printHeader.php";
                            ?>
                        <div class="table-responsive">

                        <?php $result = $db->prepare("SELECT * FROM  store_request where req_id='".$_REQUEST['id']."'");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
                                              $_SESSION['supp'] = getSupplierName($fetch["supplier"]);
                                    ?>
                            <center><h2>Good Received Note #: <?php echo $_REQUEST['id'] ?> | DATE: <?php echo $fetch['confirmed']?></h2></center>

                            <?php } ?>
                            <table id="data-table-basic" class="table table-striped table-bordered" id="content">
                                 <thead>
                                     
                                             <tr>
                                        <th colspan="3"></th>
                                        <th colspan="3">Purchase </th>
                                         <th colspan="3">Delivery</th>
                                    
                                   
                                       

                                     
                                    </tr>
                                     
                                     
                                    <tr>
                                        <th>#</th>
                                        <th>Item </th>
                                         <th>Unit</th>
                                        <th>Qty</th>
                                        <th>P.U</th>
                                        <th>T.P</th>
                                          <th>Quantity</th>
                                          <th>P.U</th>
                                          <th>T.P</th>
                                   
                                       

                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
									
								     	$amount = 0;
									   $del_amount =0;
                                		$result = $db->prepare("SELECT * FROM  request_store_item WHERE req_id='".$_REQUEST['id']."'");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
                                		    $i++;
											$total =  (float)getItemPrice($fetch['item_id']) * (float)$fetch['qty'];
											
												$del_total = $fetch['del_price'] * $fetch['del_qty'];
											
						$amount = $amount + $total;
							$del_amount = $del_amount + $del_total;
											 
                                     	?>
                                     	<tr>
                                     	    <td><h5><?php echo $i; ?></h5></td>
                                            <td><h5><?php echo getItemName($fetch['item_id']); ?></h5></td>
                                             <td><h5><?php echo getItemUnitName(getItemUnitId($fetch['item_id'])); ?></h5></td>
                                            <td><h5><?php echo number_format((float)$fetch['qty'],3); ?> </h5></td>
                                            <td><h5><?php echo number_format((float)getItemPrice($fetch['item_id']),3); ?> </h5></td>
                                            <td><h5><?php echo number_format(((float)$fetch['qty'] * (float)getItemPrice($fetch['item_id'])),3); ?> </h5></td>
                                       
                                           
                                           
                                             <td><h5><?php echo number_format((float)$fetch['del_qty'],3); ?> </h5></td>
                                             <td><h5><?php echo number_format((float)$fetch['del_price'],3); ?></h5></td>
                                              <td><h5><?php echo number_format((float)($fetch['del_qty'] * $fetch['del_price']),3); ?></h5></td>
                                          
                                         
                                     	</tr>
         <?php
	}
?>




  	<tr>
	<td colspan="2"><H4>Total</H4></td>
	<td></td>
	<td></td>

	<td></td>
	<td><H4><?php echo number_format($amount)?> </H4></td>
	
		<td></td>

	<td></td>
	<td><H4><?php echo number_format($del_amount)?> </H4></td>
	</tr>
	
	  	<tr>
	<th colspan="8"><H4>Balance</H4></th>
	<th><H4><?php echo number_format($amount - $del_amount)?> </H4></th>
	</tr>
	


                            </tbody>
                            <br><br><br>
                             <tr>
            <td colspan='3' style="text-align:center;">Supplied by: <br> (Names and signature) <br> <?php echo $_SESSION['supp'] ?> </td>
            <td colspan='2' style="text-align:center;">Received by: <br> (Names and signature) <br>
                <?= htmlspecialchars($_SESSION['f_name'] ?? '') . " " . htmlspecialchars($_SESSION['l_name'] ?? '') ?>
            </td>
            <td colspan='2' style="text-align:center;">DAF: <br> (Names and signature) <br> _______________ </td>
            <td colspan='2' style="text-align:center;">GENERAL MANAGER: <br> (Names and signature) <br> _______________ </td>
        </tr>
                          
                        </table>
						   </div>
						 </div>

						
                        </div>
                        <?php
                        }
                        elseif(!empty($_GET['req'])){
                            $code = $_GET['req'];
                            $stmt_req = $db->prepare("SELECT * FROM tbl_requests 
                            INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
                            WHERE req_code = '".$_GET['req']."'");
                            $stmt_req->execute();
                            $getrows = $stmt_req->fetch();
                            $status = $getrows['status'];
                        ?>
                        <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                        Type: Raw Materials <br>
                        Date Requested: <?php echo $getrows['requested_date']; ?>
                        </div>
                        <div class="panel-title pull-right">
                        Requested By: <?php echo $getrows['f_name']." ".$getrows['l_name']; ?><br>
                        Date Required: <?php echo $getrows['required_date']; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <hr>
                <div class="panel panel-default" style="padding:10px;">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                        <?php if($getrows['status'] == 3){ ?>
                        Requested Items <label class="label label-info"><b><i>PENDING</i></b></label>
                        <?php 
                    }
                    else{
                        ?>
                        Requested Items <label class="label label-success"><b><i>COMPLETED</i></b></label>
                        <?php
                    }
                     ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <br>
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Item Name </th>
                            <th> Quantity (TESTED) </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $ii = 0;
                        $result = $db->prepare("SELECT * FROM tbl_request_details
                        INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id
                        WHERE req_code = '".$_GET['req']."'");
                        $result->execute();
                        for($i=0; $row = $result->fetch(); $i++){
                            $ii++;
                            ?>
                            <tr>
                                <td><?php echo $ii; ?></td>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>
                    </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
    <script>

	

function printPDF() {
    var printContents = document.getElementById('content').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;


}

</script>
