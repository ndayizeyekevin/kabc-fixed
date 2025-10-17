<?php //error_reporting(0);
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

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['price'];
  }
}

}



function getAverage($id){
	
include '../inc/conn.php';	
$avarage = 0;

$sql = "SELECT * FROM request_store_item  WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($rows = $result->fetch_assoc()){
      
      $id = $rows['item_id'];
  
      
  }}



$sql = "SELECT * FROM tbl_progress INNER JOIN tbl_items ON tbl_items.item_id=tbl_progress.item WHERE item_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($rows = $result->fetch_assoc()) {
      
      
      
                            $openning =  getItemPrice($rows['item_id']) * $rows['last_qty'];
                            $in = $rows['new_price'] * $rows['in_qty'];
                            
                            $tatalQty = $rows['last_qty'] + $rows['in_qty'];
                            
                            $avarage = $openning +  $in; 
                            $avarage = $avarage/$tatalQty;
      
      
  
  }
}
  
if($avarage==0){
    
      $avarage= getItemPrice($id);
}else{

   $avarage = $avarage;
}




$sql = "UPDATE tbl_items SET price='$avarage' WHERE item_id='$id'";
if ($conn->query($sql) === TRUE) {
  
 return $id;
} else {
  return "Error: " . $sql . "<br>" . $conn->error;
}

    
}
		
		
		

if(ISSET($_GET['ac'])){
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
        $chars = "003232303232023232023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;
        while ($i <= 7) {

            $num = rand() % 33;

            $tmp = substr($chars, $num, 1);

            $pass = $pass . $tmp;

            $i++;

        }
        return $pass;
    }
    $pcode='REQ-'.productcode();
    ?>
	
	
	<?php if(isset($_POST['addRequest'])){
		
		
		ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
		
		$qty = $_POST['qty'];
		$price= $_POST['price'];
		
		
		$item = $_POST['item'];
	
	
	
$sql = "INSERT INTO `request_store_item` (`id`, `item_id`, `qty`, `req_id`, `del_qty`, `del_price`) VALUES (NULL, '$item', '0', '".$_REQUEST['req']."', '$qty', '$price');";
if ($conn->query($sql) === TRUE) {
  //  $id= $_REQUEST['delivery'];
    
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
		
			// $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           // $sql = "";
//$conn->exec($sql);
            
            
            
            $sql = "INSERT INTO `tbl_item_stock`(`item`, `qty`,`date_tkn`) 
            VALUES ('$item', '$qty','$date')";
if ($conn->query($sql) === TRUE) {
  
// return $id;
} else {
  return "Error: " . $sql . "<br>" . $conn->error;
}

        }
        

//echo getAverage($_REQUEST['item']);
		
		
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
                
                
                 <label class="col-md-2 control-label" for=""><strong>Item</strong></label>
                <div class="col-md-4">
                        <select name="item" class="form-control chosen" data-placeholder="Choose Unit...">
                            
                            <option value="">Select Product</option>
                    <?php
                    $stmt = $db->query('SELECT * FROM tbl_items');
                    try {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                           
                            ?>
                            <option value="<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></option>
                            <?php
                        
                        }
                    }
                    catch (PDOException $ex) {
                        //Something went wrong rollback!
                        echo $ex->getMessage();
                    }
                ?>   
					</select>
					</div>
                
                
                
                <label class="col-md-2 control-label" for=""><strong>Delivery Qunatity</strong><span class="text-danger">*</span></label>


	
                <div class="col-md-4">
<input type="number" name="qty" placeholder="enter Quantity" class="form-control">	
	
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Unit price</strong></label>
                <div class="col-md-4">
                             <input type="number" name="price" placeholder="enter Unit Price" class="form-control">	
		
  </select>	
                </div>
            </div>
            </div>
            <br>
       
              <input type="submit" name="addRequest" Value="Save">
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
