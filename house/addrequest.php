<?php
if(ISSET($_POST['add'])){
    $request = $_POST['requested'];
    $required = $_POST['required'];
    $item = $_POST['items'];
    $quantity = $_POST['quantity'];
    $remark = $_POST['remark'];
    $user = $_SESSION['user_id'];

    // Display an error message in a sweet alert if the $_SESSION['user_id'] is not set
    // if not set, redirect to login page
    if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "0"){
        echo '<script type="text/javascript">
        alert("Invalid User id! Id can not be 0 or null. Please login again.");
        </script>';
        echo '<meta http-equiv="refresh"'.'content="2;URL=./index">';
        exit();
    }

    $dept = $_SESSION['log_role'];
    $req_code = $_POST['req_code']; 
    $req_type = $_POST['req_type'];

    // Begin transaction
    $db->beginTransaction();
    try{
      $sql = $db->prepare("INSERT INTO tbl_requests 
      (req_code, requested_date, required_date, remark, department, user_id, req_type, status) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $sql->execute([$req_code, $request, $required, $remark, $dept, $user, $req_type, 3]);

      for($x=0;$x<count($item);$x++){
        
      $sql = $db->prepare("INSERT INTO `tbl_request_details`(`req_code`, `items`, `quantity`) 
      VALUES ('$req_code','$item[$x]','$quantity[$x]')");
      $sql->execute();
      }
      $msg="Requested Successfuly!";
      $db->commit(); // Commit the transaction
      echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=request">';
    } catch (Exception $e) {
      $db->rollBack(); // Rollback the transaction on error
      $msge = "Failed to add request: " . $e->getMessage();
      echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=request">';
    }
      
}
?>


<?php
if (isset($_POST['add_item'])) {
    $item = $_POST['item'];
    $qty = $_POST['quantity'];
    $code = $_POST['code'];

    // Check if the item already exists in the request details
    $stmt = $db->prepare("SELECT * FROM tbl_request_details WHERE req_code = ? AND items = ?");
    $stmt->execute([$code, $item]);
    $count = $stmt->rowCount();

    if ($count > 0) {
        // --- Display ERROR message with JavaScript alert ---
        echo '<script type="text/javascript">';
        echo 'alert("Item is already on the list!");';
        echo '</script>';
        // Set the error message variable for the main page logic (optional)
        $msge = "Item is already on the list!"; 

        // Redirect after a short delay (changed 2 to 1 second)
        echo '<meta http-equiv="refresh" content="1;URL=?resto=request&req='.$code.'">'; 

    } else {
        try {
            // Insert the new item into the request details
            $sql = $db->prepare("INSERT INTO tbl_request_details (req_code, items, quantity) VALUES (?, ?, ?)");
            $sql->execute([$code, $item, $qty]);

            // --- Display SUCCESS message with JavaScript alert ---
            echo '<script type="text/javascript">';
            echo 'alert("Added Successfully!");';
            echo '</script>';
            // Set the success message variable for the main page logic (optional)
            $msg = "Added Successfully!"; 

            // Redirect after a short delay (changed 2 to 1 second)
            echo '<meta http-equiv="refresh" content="1;URL=?resto=request&req='.$code.'">'; 

        } catch (Exception $e) {
            // This is a catch for a database execution failure
            echo '<script type="text/javascript">';
            echo 'alert("Database Error: Failed to add item.");';
            echo '</script>';
            $msge = "Failed to add item."; 
            echo '<meta http-equiv="refresh" content="1;URL=?resto=request&req='.$code.'">'; 
        }
    }
}
?>


<?php
if(ISSET($_GET['rmv'])){
    $detail_id = $_GET['rmv'];
    $sql_rmv = $db->prepare("DELETE FROM tbl_request_details WHERE detail_id = '".$detail_id."'");
    $sql_rmv->execute();
   $msge = "Removed Successfully";
    echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=request">'; 
}
?>
<?php
if(ISSET($_GET['del'])){
    $del_code = $_GET['del'];

    $sql_del = $db->prepare("DELETE FROM tbl_requests WHERE req_code = '".$del_code."'");
    $sql_del->execute();

    $sql_del2 = $db->prepare("DELETE FROM tbl_request_details WHERE req_code = '".$del_code."'");
    $sql_del2->execute();
    $msge = "Removed Successfully";
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
    $pcode=productcode();
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
										<h2>Request Raw Materials</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Request" class="btn"><i class="fa fa-plus-circle"></i> Add Request</button>
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
                        <?php if(empty($_GET['req'])){ ?>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                 <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request By</th>
                                        <th>Requested Date</th>
                                        <th>Required Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                  $i = 0;
                  // Show only requests for the logged-in user
                  $result = $db->prepare("SELECT * FROM tbl_requests
                    INNER JOIN tbl_users ON tbl_requests.user_id = tbl_users.user_id
                    INNER JOIN tbl_status ON tbl_requests.status = tbl_status.id
                    WHERE department = '".$_SESSION['log_role']."' AND tbl_requests.user_id = '".$_SESSION['user_id']."'
                    ORDER BY req_id DESC");
                  $result->execute();
                  while($fetch = $result->fetch()){
                    $i++;
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><a  href="?resto=request&req=<?php echo $fetch['req_code'] ?>"><?php echo $fetch['f_name']." ".$fetch['l_name']; ?></a></td>
                                            <td><?php echo $fetch['requested_date']; ?></td>
                                            <td><?php echo $fetch['required_date']; ?></td>
                                            <td><?php echo $fetch['status_name']; ?></td>
                                            <td>
                                                <?php if($fetch['status'] == '3'){ ?>
                                                <a class="btn-sm" href="?resto=request&del=<?php echo $fetch['req_code'] ?>" onclick="if(!confirm('Do you really want to Delete This Request?'))return false;else return true;">Delete</a>
                                            <?php } ?>
                                            </td>
                                     	</tr>
         <?php
	}
?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Request By</th>
                                    <th>Requested Date</th>
                                    <th>Required Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
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
                     
                      <a  class="btn btn-info" href="?resto=printInternal&&req=<?php echo $_REQUEST['req']?>&&dep=<?php echo $getrows['f_name']?>&&req_date=<?php echo $getrows['requested_date']; ?>">Print</a>
                        </div>
                        
                        <div class="panel-title pull-right">
                        <?php
                        if($status == 3){
                        ?><button class="btn btn-default" data-toggle="modal" data-target="#adds">
                        Add Item
                        </button>
                        <button class="btn btn-default" data-toggle="modal" data-target="#rmv">
                        Issues Item
                        </button>
                        <?php } ?>
                        </div>
                        
                        <div class="clearfix"></div>
                    </div>
                    <br>
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Item Name </th>
                            <th> Quantity </th>
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
                                <td><?php echo number_format($row['quantity'],3); ?></td>
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
    <div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add Request</h4>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
          <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label" for=""><strong>Requested Date</strong><span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <input autocomplete="off" type="date" max="<?php echo date("Y-m-d") ?>" class="form-control" name="requested" required>
                    <input autocomplete="off" type="hidden" class="form-control" name="req_code" value="<?php echo $pcode;?>" required>
                </div>
                    <label class="col-md-2 control-label" for=""><strong>Required Date</strong></label>
                <div class="col-md-4">
                    <input autocomplete="off" type="date" min="<?php echo date("Y-m-d") ?>" class="form-control" name="required" required>
                </div>
            </div> 
            </div>
            <br>  
            <div class="row">
                    <div class="form-group">
                        <label class="col-md-2 control-label" for=""><strong>Remark</strong></label>
                    <div class="col-md-4">
                     <textarea class="form-control" name="remark" required></textarea>
                    </div> 
                    <label class="col-md-2 control-label" for=""><strong>Request Type</strong></label>
                <div class="col-md-4">
                 <input type="text" name="req_type" value="House Keeping" class="form-control" required readonly>
                </div>
                    </div>
            <div class="form-group">
                <div class="col-md-12">
                 <table class="table table-border" id="myOrder">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>
                          <button type="button" name="addOrder" class="btn btn-success btn-sm btn_addOrder" required><span>
                            <i class="fa fa-plus"></i>
                          </span></button>
                        </th>
                    </tr>
        
                </thead>
                <tbody>
        
                </tbody>
              </table>
            </div>
            </div>
            <div class="form-group">
    		    <div class="form-actions col-md-12">
    		        <br />
    		        <center>								
    			        <button type="submit" name="add" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save" onclick="if(!confirm('Do you really want to add this request?'))return false;else return true;"></i> Save</button>
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
    
    <!-- add item -->
        <div class="modal fade" id="adds" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Item On This Requisition</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form method="POST" action="">
                  <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Item Name</label>
                    <select class="form-control selectpicker" data-live-search="true" name="item" required>
                      <option value="">--- Select Item ---</option>
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
                                <td>
                                    <a href="?resto=request&req=<?php echo $code; ?>&rmv=<?php echo $row['detail_id']; ?>" 
                                      onclick="return confirm('Are you sure you want to remove this item?');">
                                        Remove
                                    </a>
                                </td>
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
        html+='<td><input autocomplete="off" type="number" class="form-control quantity" step="any" min="0" name="quantity[]" required placeholder="0"></td>';
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
              tr.find("#unit").text(data["unit_name"]);
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
