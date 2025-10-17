	<?php
// add new member;

if(ISSET($_POST['add'])){
		$item =$_REQUEST['myId'];
		$price = $_POST['price'];
		$price1 = $_POST['price1'];
		$date = date('Y-m-d');
		
		
		
		if($price > $price1){
		    
		    $q= $price - $price1;
		    $status = 1;
		    
		    $total = $price1  + $q;
		
		 $sql = "INSERT INTO `tbl_progress` (`prog_id`, `date`, `in_qty`, `last_qty`, `out_qty`, `item`, `end_qty`, `new_price`) VALUES (NULL, '$date','$q', NULL, NULL,'$item', '$total', NULL);";

		}else{
		  $q= $price1 - $price ;
		  $total = $price1  - $q;
		  $status = 2;
		   $sql = "INSERT INTO `tbl_progress` (`prog_id`, `date`, `in_qty`, `last_qty`, `out_qty`, `item`, `end_qty`, `new_price`) VALUES (NULL, '$date','0', NULL, '$q','$item', '$total', NULL);";
		}

        include '../inc/conn.php';  
          
          

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('updated successfully')</script>";
} else {
  echo "Error updating record: " . $conn->error;
}
         



$sql = "UPDATE  tbl_item_stock SET qty='$total' WHERE item='$item'";

if ($conn->query($sql) === TRUE) {
  echo "Record updated successfully";
} else {
  echo "Error updating record: " . $conn->error;
}



          
$sql = "INSERT INTO `stock_take` (`id`, `item`, `qty`,status, `created_at`) VALUES (NULL, '$item', '$q', '$status','$date');";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
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
     $stmt = $db->query("SELECT * FROM tbl_item_stock where item='".$_REQUEST['myId']."'");
                    try {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      
                        
          
                        $price =  $row['qty'];
                     
                     
                            
                          
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
              <h4 class="modal-title">Stock Take</h4>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
    
            <label class="col-md-2 control-label" for=""><strong>Item Quatinty</strong><span class="text-danger">*</span></label>
                 <div class="col-md-4">
                    <input type="text" name="price" value="<?php echo $price?>" placeholder="Price" class="form-control" required>
                        <input type="hidden" name="price1" value="<?php echo $price?>" placeholder="Price" class="form-control" required>
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