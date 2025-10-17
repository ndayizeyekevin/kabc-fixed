	<?php
// add new member;

if(ISSET($_POST['add'])){
		$item = $_POST['item'];
		$unit = $_POST['unit'];
		$price = $_POST['price'];
		$category =$_POST['category'];
			$supplier =$_POST['supplier'];
		$status = '1';
        $today = date("Y-m-d");
            $sql = "INSERT INTO `tbl_items`(`item_name`, `item_unit`, `item_status`,price,cat_id,supplier) 
            VALUES ('$item', '$unit','$status','$price','$category','$supplier')";
            $conn->exec($sql);
            $lastID = $conn->lastInsertId();

            $stmt = $db->prepare("INSERT INTO `tbl_item_stock`( `item`, `qty`, `date_tkn`)
            VALUES ('$lastID','0','$today')");
            $stmt->execute();
            
            $msg = "Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=stock">';
	
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


function getCategoryName($id){
	
include '../inc/conn.php';	

$sql = "SELECT * FROM category where cat_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['cat_name'];
  }
}

}





function update_price($amount,$id){
 include '../inc/conn.php';   
    
    
// $sql = "UPDATE tbl_items SET price='$amount' WHERE item_id=$id";

// if ($conn->query($sql) === TRUE) {
 
//  return 0;
// } else {
//   echo "Error updating record: " . $conn->error;
// }
    
}
    
    
error_reporting(0);
    
function getAverage($id){
	

  return  getItemPrice($id);

    
}
    
    


    
    if(ISSET($_POST['record'])){
        $item = $_POST['item_id'];
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
                
            $stmts = $db->prepare("INSERT INTO `tbl_progress` (`date`,`in_qty`,`last_qty`,`item`, `end_qty`) 
            VALUES ('$date','$qty','$lastqty', '$item','$tot')");
            $stmts->execute();


        }
        else{
		
			 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_item_stock`(`item`, `qty`,`date_tkn`) 
            VALUES ('$item', '$qty','$date')";
            $conn->exec($sql);
        }  
            $msg = "Successfully Recorded!";
			
			echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=stock">';
	
    }
			
?>

<!-- delete -->
<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_items` WHERE `item_id`='$id' ");
		$sql->execute();
		
		$sql = $conn->prepare("DELETE from `tbl_item_stock` WHERE `item`='$id' ");
		$sql->execute();

		$msge = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=stock">';
	}
?>

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
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="fa fa-cog"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Manage Stock</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
										
										  <center><h4> Stock as on <?php echo date('Y-m-d');?> </h4> </center>
									</div>
								</div>
							</div>
							<!--<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">-->
							<!--	<div class="breadcomb-report">-->
							<!--		<button type="button" data-toggle="modal" data-target="#myModalone1" data-placement="left" title="Record Stock" class="btn"><i class="fa fa-plus-circle"></i> Record Stock</button>-->
							<!--		<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Item" class="btn"><i class="fa fa-plus-circle"></i> Add Item</button>-->
							<!--	</div>-->
							<!--</div>-->
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
                    <div class="data-table-list">
                        <div class="table-responsive">
                            <div id="DataList">Loading stock please wait....</div>
                            <table id="data-table-basic" class="table table-striped" hidden>
                                 <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                           <th>Category</th>
                                        
                                        <th>Quantity</th>
                                           <th>Unit</th>
                                         <th>Price</th>
                                         <th>Total</th>
                                         <!--<th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                            
                                    <?php
                                    $i = 0;
                                    $amount =0; 
                                		$sql = $conn->prepare("SELECT * FROM `tbl_items` 
                                		INNER JOIN `tbl_item_stock` ON tbl_items.item_id=tbl_item_stock.item
                                		INNER JOIN tbl_unit ON tbl_items.item_unit=tbl_unit.unit_id LIMIT 5");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                		    $i++;
                                		    
                                		    $total = $fetch['price'] * $fetch['qty'];
                                		    $amount =  $amount  + $total;
                                		    
                                		    
                                		    
                                		    
                                		    
                                		    
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><?php echo $fetch['item_name']; ?></td>
                                            
                                             <td><?php echo getCategoryName($fetch['cat_id']); ?></td>
                                            
                                            <td><?php echo $fetch['qty']; ?> <?php if($fetch['qty']<=$fetch['limit_data']){
                                            echo "<span style='color:Red'>You have reached limit</span>";}?> </td>
                                            <td><?php echo $fetch['unit_name']; ?></td>
                                             <td><?php echo number_format(getAverage($fetch['item_id'])); 
                                             
                                    
                                             ?></td>
                                               <td><?php echo number_format(getAverage($fetch['item_id']) * $fetch['qty']); ?></td>
                                            
                                            <!-- <td>-->
                                                 
                                                 
                                            <!--             <a class="btn-sm" href="?resto=update_item&myId=<?php echo $fetch['item_id'] ?>">Update</a>-->
                                                 
                                                 
                                            <!--    <a class="btn-sm" href="?resto=stock&myId=<?php echo $fetch['item_id'] ?>" onclick="if(!confirm('Do you really want to Delete This Item?'))return false;else return true;">Delete</a>-->
                                            <!--</td>-->
                                     	</tr>
                                            <?php
                                        }
                                    ?>
                                    
                                    
                                         <tr>
                                <td colspan='5'>Total</td>
                          
                                       <td><?php echo number_format($amount)?></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add New Item</h4>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
          <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Item Name</strong><span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <input type="text" name="item" class="form-control" required>
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Unit</strong></label>
                <div class="col-md-4">
                    <br>
                        <select name="unit" class="form-control chosen" data-placeholder="Choose Unit...">
                    <?php
                    $stmt = $db->query('SELECT * FROM tbl_unit');
                    try {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($res_cntr==$row['unit_id'])
                            {
                            ?>
                            <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['unit_name']; ?></option>

                            <?php
                        }
                        else
                        {
                            ?>
                            <option value="<?php echo $row['unit_id']; ?>"><?php echo $row['unit_name']; ?></option>
                            <?php
                        }
                        }
                    }
                    catch (PDOException $ex) {
                        //Something went wrong rollback!
                        echo $ex->getMessage();
                    }
                ?>   
					</select>
                </div>
                
                <br><br>
                
                 <label class="col-md-2 control-label" for=""><strong>Category</strong></label>
                <div class="col-md-4">
                        <select name="category" class="form-control chosen" data-placeholder="Select category...">
                    <?php
                    $stmt = $db->query('SELECT * FROM category');
                    try {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                        
                            <option value="<?php echo $row['cat_id']; ?>"><?php echo $row['cat_name']; ?></option>
                            <?php
                        
                        }
                    }
                    catch (PDOException $ex) {
                        //Something went wrong rollback!
                        echo $ex->getMessage();
                    }
                ?>   
					</select>
					
					<br><br>
					
					                  			<select name="supplier" class="form-control" required>
								<option value=''>Select Supplier</option><?php
			$result = $db->prepare("SELECT * FROM suppliers");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
?><option value='<?php echo $fetch['id']?>'><?php echo $fetch['name']?></option>
 <?php }

	?>			
		
</select>	
					
                </div>
                
                
                
            
                
            </div>
            </div>
            <label class="col-md-2 control-label" for=""><strong>Item Price</strong><span class="text-danger">*</span></label>
                 <div class="col-md-4">
                    <input type="text" name="price"  placeholder="Price" class="form-control" required>
                </div>
            <br>
            <br>
            <div class="row">
            <div class="form-group">
    		    <div class="form-actions col-md-12">
    		        <br />
    		        <center>								
    			        <button type="submit" name="add" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
    			        <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
    		        </center>
    		    </div>
            </div>
            </div>
            
            </form>
            </div>
        </div>
    </div>
    </div>
    
    <div class="modal fade" id="myModalone1" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Record Item Quantity</h4>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
          <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Item Name</strong><span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <select name="item_id" class="form-control chosen" data-placeholder="Choose Item...">
                    <?php
                    $stmt = $db->query('SELECT * FROM tbl_items');
                    try {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($res_cntr==$row['item_id'])
                            {
                            ?>
                            <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['item_name']; ?></option>

                            <?php
                        }
                        else
                        {
                            ?>
                            <option value="<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></option>
                            <?php
                        }
                        }
                    }
                    catch (PDOException $ex) {
                        //Something went wrong rollback!
                        echo $ex->getMessage();
                    }
                ?>   
					</select>
                    
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Quantity</strong></label>
                <div class="col-md-4">
                    <input type="number" name="qty" class="form-control" required>    
                </div>
            </div>
            </div>
            <br>
            <br>
            <div class="row">
            <div class="form-group">
    		    <div class="form-actions col-md-12">
    		        <br />
    		        <center>								
    			        <button type="submit" name="record" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
    			        <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
    		        </center>
    		    </div>
            </div>
            </div>
            
            </form>
            </div>
        </div>
    </div>
    </div>
    
    
    <script>
    
    getData();
    
    function getData(){
       
var data = new FormData();
data.append("type", "daily_occupancy_rate");

var xhr = new XMLHttpRequest();
xhr.withCredentials = true;

xhr.addEventListener("readystatechange", function() {
  if(this.readyState === 4) {
   document.getElementById('DataList').innerHTML=this.responseText;
   
 // alert(this.responseText);
      
  }
});

xhr.open("POST", "stock_items.php");

xhr.send(data);

    }    
    </script>