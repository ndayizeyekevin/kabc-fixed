	<?php
// add new member;

if(ISSET($_POST['add'])){
		$item = $_POST['item'];
		$unit = $_POST['unit'];
		$status = '1';
			 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_items`(`item_name`, `item_unit`, `item_status`) 
            VALUES ('$item', '$unit','$status')";
            $conn->exec($sql);
            
            $msg = "Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=stock">';
	
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

<?php 
if(ISSET($_POST['update'])){
    $adminID = $_POST['staff_id'];
    $family_name2 = $_POST['family_name_upd'];
	$firstname2 = $_POST['firstname_upd'];
	$phone2 = $_POST['phone_upd'];
	$email2 = $_POST['email_upd'];
	$department2 = $_POST['department_upd'];
	$role2 = $_POST['role_upd'];
    $today = date("Y-m-d");
	
	$sql1 = "UPDATE `tbl_staff` SET `staff_family_name` = '$family_name2',`staff_first_name` = '$firstname2',`PhoneNumber` = '$phone2',`email` = '$email2',`Department` = '$department2',`role_id` = '$role2' WHERE item_id = '$adminID'";
	$db->exec($sql1);
	
	$sql2 = "UPDATE `tbl_user_log` SET `f_name` = '$family_name2',`l_name` = '$firstname2',`phone` = '$phone2' WHERE `item_id`='$adminID'";
	$db->exec($sql2);
	
	$sql3 = "UPDATE `tbl_admins` SET `firstName` = '$family_name2',`lastName` = '$firstname2',`email` = '$email2',`phone` = '$phone2', `company_id` = '$company' WHERE adm_id = '$adminID'";
	$db->exec($sql3);
	
	$msg = "Updated Successfully";

	echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=users">';
}
?>
<!-- delete -->
<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_staff` WHERE `item_id`='$id' ");
		$sql->execute();
		
		$sql = $conn->prepare("DELETE from `tbl_user_log` WHERE `item_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=users">';
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
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search items...">
                                </div>
                            </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                 <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <!--<th>Action</th>-->
                                    </tr>
                                </thead>
                                <tbody>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                var filter = searchInput.value.toLowerCase();
                var rows = document.querySelectorAll('#data-table-basic tbody tr');
                rows.forEach(function(row) {
                    var text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        }
    });
    </script>
                                    <?php
                                    $i = 0;
                                		$sql = $conn->prepare("SELECT * FROM `tbl_items` 
                                		INNER JOIN `tbl_item_stock` ON tbl_items.item_id=tbl_item_stock.item
                                		INNER JOIN tbl_unit ON tbl_items.item_unit=tbl_unit.unit_id");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                		    $i++;
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><?php echo $fetch['item_name']; ?></td>
                                            <td><?php echo $fetch['qty']; ?></td>
                                            <td><?php echo $fetch['unit_name']; ?></td>
                                            
                                     	</tr>
                                     	 <div class="modal fade" id="update<?php echo $fetch['item_id']; ?>" role="dialog">
                            <div class="modal-dialog modals-default">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  
                </div>
            </div>
                               
         <?php
	}
?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                   
                                </tr>
                            </tfoot>
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
            </div>
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
