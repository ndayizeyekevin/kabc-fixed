<?php


// Get values from get request

$max = isset($_GET['max']) ? $_GET['max'] : 0;
$price = isset($_GET['price']) ? $_GET['price'] : 0;
 
 
ini_set('display_startup_errors', 1);

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


function getCurrentStock($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM tbl_item_stock where item='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['qty'];
  }
}

}



function getItemPrice($id){
	
include '../inc/conn.php';	

$item = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($item);

$tbl_progress = $conn ->query ("SELECT * FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1;");

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    while($row2 = $tbl_progress->fetch_assoc()){
        return  $row2['new_price'] > 0 ? $row2['new_price'] : $row['price'];
    }
  }
}

}




function getAverage($id, $ne_price, $ne_qty) {
    include '../inc/conn.php';
    $totalValue = 0;
    $totalQty = 0;

    // 1. Get item_id from request_store_item
    $sql = "SELECT item_id FROM request_store_item WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) return false;

    $row = $result->fetch_assoc();
    $item_id = $row['item_id'];

    // 2. Get first stock entry of this month
    $firstDayOfMonth = date('Y-m-01');
    $sql = "SELECT * FROM tbl_progress 
            WHERE item = '$item_id' AND date >= '$firstDayOfMonth' 
            ORDER BY prog_id ASC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $entry = $result->fetch_assoc();

        $open_qty = $entry['last_qty'];
        $open_price = $entry['new_price'];
        $open_value = $open_qty * $open_price;

        $in_value = $ne_price * $ne_qty;

        $totalValue = $open_value + $in_value;
        $totalQty = $open_qty + $ne_qty;

        $average = ($totalQty > 0) ? ($totalValue / $totalQty) : $open_price;

        // Get last end_qty to use as last_qty for this new entry
        $last_sql = "SELECT end_qty FROM tbl_progress WHERE item = '$item_id' ORDER BY prog_id DESC LIMIT 1";
        $last_result = $conn->query($last_sql);
        $last_qty = 0;
        if ($last_result->num_rows > 0) {
            $last_row = $last_result->fetch_assoc();
            $last_qty = $last_row['end_qty'];
        }

        $new_end_qty = $last_qty + $ne_qty;
        $date = date('Y-m-d');

        // Insert new entry with calculated average cost
        $insert_sql = "INSERT INTO tbl_progress (date, in_qty, last_qty, out_qty, item, end_qty, new_price) 
                       VALUES ('$date', '$ne_qty', '$last_qty', 0, '$item_id', '$new_end_qty', '$average')";

        if ($conn->query($insert_sql)) {
            return $item_id;
        } else {
            return "Error inserting stock transaction: " . $conn->error;
        }
    } else {
        return "No opening stock found for this month.";
    }
}



		
		
		

if(isset($_GET['ac'])){
    $code = $_GET['ac'];
    $date = date("Y-m-d");
    
    $sql = $db->prepare("SELECT * FROM tbl_request_details WHERE req_code = '".$code."'");
    $sql->execute();
    while($rows = $sql->fetch()){
       $currqty = $rows['quantity'];
       $stmt = $db->prepare("SELECT * FROM tbl_item_stock WHERE item = '".$rows['items']."'");
       $stmt->execute();
       $rowss = $stmt->fetch();
       $stkqty = $rowss['qty'];
       
       $remainqty = $stkqty-$currqty;
       
       $sql_upd_qty = $db->prepare("UPDATE tbl_item_stock SET `qty`='$remainqty' WHERE `item`='".$rows['items']."' ");
	    $sql_upd_qty->execute();
	    
	    $sql_upd = $db->prepare("UPDATE tbl_requests SET `status`='8' WHERE `req_code`='".$code."' ");
	    $sql_upd->execute();

      $sql_chk2 = $db->prepare("SELECT * FROM tbl_progress WHERE item = '".$rows['items']."' ORDER BY prog_id DESC LIMIT 1");
      $sql_chk2->execute();
      $rowcount = $sql_chk2->rowCount();
          $fetch = $sql_chk2->fetch();
          $lastqty = $fetch['end_qty'];
          $tot = $lastqty-$currqty;
          
      $stmts = $db->prepare("INSERT INTO `tbl_progress` (`date`,`out_qty`,`last_qty`,`item`, `end_qty`) 
      VALUES ('$date','$currqty','$lastqty', '".$rows['items']."','$tot')");
      $stmts->execute();
    }
    $msg = "Confirmed Successfully";
    echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=request">';
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
<?php
    function productcode() {
    
        return getReqCode($_SESSION['user_id']);
    }
    $pcode='REQ-'.productcode();
    ?>
	
	
	<?php 
	if(isset($_POST['addRequest'])){
		
		
		
		$qty = $_POST['qty'];
		$price= $_POST['price'];
		
	
	
	
  $sql_chk = $db->prepare("SELECT * FROM request_store_item WHERE id = '".$_REQUEST['item']."'");
        $sql_chk->execute();
        if($sql_chk->rowCount() > 0){
            $rows = $sql_chk->fetch();
            $item = $rows['item_id'];
        }


	

	
      $sql = "UPDATE request_store_item SET del_qty= del_qty + $qty, del_price = $price WHERE id='".$_REQUEST['item']."'";
      if ($conn->query($sql) === TRUE) {
          $id= $_REQUEST['delivery'];
          
        // echo "<script>window.location='index?resto=viewDelivery&&id=$id'</script>";
        
        //echo "<script>alert('$item')</script>";
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }


    
		$qty = $_POST['qty'];
		$date = date("Y-m-d");
		
        $sql_chk = $db->prepare("SELECT * FROM tbl_item_stock WHERE item = '".$item."'");
        $sql_chk->execute();
        if($sql_chk->rowCount() > 0){
            $rows = $sql_chk->fetch();
            $quantity = $rows['qty'];
            $totqty = $qty+$quantity;
            
            $sqlqty = $db->prepare("UPDATE `tbl_item_stock` SET `qty` = '$totqty' WHERE `item` = '".$item."'");
            $sqlqty->execute();

            $sql_chk2 = $db->prepare("SELECT * FROM tbl_progress WHERE item = '".$item."' ORDER BY prog_id DESC LIMIT 1");
            $sql_chk2->execute();
            $rowcount = $sql_chk2->rowCount();
                $fetch = $sql_chk2->fetch();
                $lastqty = $fetch['end_qty'];
                $tot = $lastqty+$qty;
                
            $stmts = $db->prepare("INSERT INTO `tbl_progress` (`date`,`in_qty`,`last_qty`,`item`, `end_qty`,new_price) 
            VALUES ('$date','$qty','$lastqty', '$item','$tot','$price')");
            $stmts->execute();
            
        }else{
		
			 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_item_stock`(`item`, `qty`,`date_tkn`) 
            VALUES ('$item', '$qty','$date')";
            $conn->exec($sql);
        }
        
        // echo getAverage($_REQUEST['item']);
        // echo "<script>window.location=history.go(-1);</scrript>";
        echo "<script>window.location='index?resto=viewDelivery&&id=".$_REQUEST['pitem']."'</script>";
 
 
//  echo "<script>window.location=history.go(-1);</scrript>";
//  echo "<script>window.location='index?resto=viewDelivery&&id=$id'</script>";
		
		
	}?>
	
<style>
    .form-ic-cmp{
        margin-bottom:10px;
    }
</style>
<!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
		    
		    <?php if($msg){?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
              </div>
            <?php } 
             else if($msge){?>
                 
             <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
              </div>
            <?php } ?>
			<div class="row" hidden>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="fa fa-cog"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Request Raw Materials</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				
				   <div class="modal-content">
				   <br><br>
          
			<br><br>
            <div class="modal-body">
              <form action="" enctype="multipart/form-data" method="POST">
          <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Delivery Quantity</strong><span class="text-danger">*</span></label>


	
                <div class="col-md-4">
                  <input type="number" name="qty" value="<?php echo $max; ?>" placeholder="enter Quantity" step=".01" class="form-control" min='0' max="<?php echo $max; ?>">	
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Unit price</strong>
                    <span class="text-danger">*</span></label>
                <div class="col-md-4">
                             <input type="number" required name="price" value="<?php echo $price; ?>" step=".01" placeholder="enter Unit Price" min="0" class="form-control">	
		
  </select>	
                </div>
            </div>
            </div>
            <br>

              <input type="submit" class="btn bg-info text-dark" name="addRequest" Value="Save" onclick="return confirm('Are you sure you want to save this delivery?')">
            </form>
			
            </div>
     
    </div>
			
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalone" role="dialog">
 
    </div>
    
    <!-- add item -->
<div class="modal fade" id="adds" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Item Name</label>
            <select class="form-control selectpicker" data-live-search="true" name="item" required>
            <?php
            $sql = $db->prepare("SELECT * FROM tbl_items");
            $sql->execute();
            for($i=0; $row = $sql->fetch(); $i++){
                ?>
                <option value="<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></option>
                <?php
                }
                ?>
            </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required>
            <input type="hidden" name="code" class="form-control" value="<?php echo $_GET['req']; ?>">
          </div>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="add_item" class="btn btn-success">Add</button>
      </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rmv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Item Issues</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Item Name </th>
                            <th> Quantity </th>
                            <th> Action </th>
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
                                <td><a href="?resto=request&req=<?php echo $code; ?>&rmv=<?php echo $row['detail_id']; ?>">Remove</td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!-- <button type="submit" name="add_item" class="btn btn-success">Add</button> -->
      </div>
        </form>
      </div>
    </div>
  </div>
</div>
   <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
    <script>
 $(document).ready(function(){
      $(document).on('click','.btn_addOrder', function(){
        var html='';
        html+='<tr>';
        html+='<td><select class="form-control selectpicker productid" data-live-search="true" name="items[]" required><option value="">--Select Item--</option><?php
        echo fill_product($db)?></select></td>';
        html+='<td><input autocomplete="off" type="number" class="form-control quantity" name="quantity[]" required placeholder="0"></td>';
        html+='<td><input autocomplete="off" type="text" class="unit" disabled></td>';
        html+='<td><button type="button" name="remove" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-remove"></i></button></td>'

        $('#myOrder').append(html);

        $('.productid').on('change', function(e){
          var productid = this.value;
          var tr=$(this).parent().parent();
          $.ajax({
            url:"getproduct.php",
            method:"get",
            data:{id:productid},
            success:function(data){
              $("#btn").html('<button type="submit" class="btn btn-success" name="add">Request</button>');
              tr.find(".quantity").val();
              tr.find(".unit").val(data["unit_name"]);
            }
          })
        })
         $('.selectpicker').selectpicker();
      })

      $(document).on('click','.btn-remove', function(){
        $(this).closest('tr').remove();
        calculate(0,0);
        $("#paid").val(0);
      })
    });
  </script>
