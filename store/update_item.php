	<?php
// add new member;




    function getItemPrice($id) {
    global $db;
    return StoreController::getUnitePrice($db, $id);

    // include '../inc/conn.php';	
    // // Get item default price
    // $itemQuery = "SELECT * FROM tbl_items WHERE item_id='$id'";
    // $itemResult = $conn->query($itemQuery);
    // // Get latest progress record
    // $progressQuery = "SELECT * FROM tbl_progress WHERE item='$id' ORDER BY prog_id DESC LIMIT 1";
    // $progressResult = $conn->query($progressQuery);
    // if ($itemResult->num_rows > 0) {
    //     $item = $itemResult->fetch_assoc(); 
    //     if ($progressResult->num_rows > 0) {
    //         $progress = $progressResult->fetch_assoc(); 
    //         return ($progress['new_price'] >= 0) ? $progress['new_price'] : $item['price'];
    //     }
    //     return $item['price'];
    // }
    // return 0;
}

if(ISSET($_POST['add'])){
		$item = $_POST['item'];
		//$unit = $_POST['unit'];
		$price = $_POST['price'];
		$category =$_POST['category'];
		$status = '1';
        $today = date("Y-m-d");
        
        include '../inc/conn.php';  
          
          
$sql = "UPDATE tbl_items SET cat_id='$category',item_name='$item',price='$price' WHERE item_id='".$_REQUEST['myId']."'";
$sqls = "UPDATE tbl_progress SET new_price='$price' WHERE item='".$_REQUEST['myId']."' ORDER BY prog_id DESC LIMIT 1";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('updated successfully')</script>";
} else {
  echo "Error updating record: " . $conn->error;
}
          
            
    
	
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
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
								
								
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
                    <div class="data-table-list">
                        <div class="table-responsive">
                          
                           
                            <?php
     $stmt = $db->query("SELECT * FROM tbl_items where item_id='".$_REQUEST['myId']."'");
                    try {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      
                        
                        $name =  $row['item_name'];
                        $item_unit =  $row['item_unit'];
                        $price =  $row['price'];
                        $cat_id =  $row['cat_id'];
                            
                          
                        }
                    }
                    catch (PDOException $ex) {
                        //Something went wrong rollback!
                        echo $ex->getMessage();
                    }
                ?> 
                           
                                 
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
                    <input type="text" name="item" value="<?php echo $name?>" class="form-control" required>
                </div>
                
                
                <br>
                
                 <label class="col-md-2 control-label" for=""><strong>Category</strong></label>
                <div class="col-md-4">
                        <select name="category" class="form-control chosen" data-placeholder="Select category...">
                            
                            
                              <?php
                    $stmt = $db->query("SELECT * FROM category where cat_id='$cat_id'");
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
                </div>
                
                
                
            
                
            </div>
            </div>
            <label class="col-md-2 control-label" for=""><strong>Item Price</strong><span class="text-danger">*</span></label>
                 <div class="col-md-4">
                    <input type="text" name="price" value="<?php echo $price?>" placeholder="Price" class="form-control" required>
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
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog">
     
   
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