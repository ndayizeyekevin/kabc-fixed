<?php //error_reporting(0);
include '../inc/conn.php';	
?>
	
	
	<?php if(isset($_POST['addRequest'])){
		
	
		$category = $_POST['category'];
	
$sql = "INSERT INTO `category` (`cat_id`, `cat_name`) VALUES (NULL, '$category');";
if ($conn->query($sql) === TRUE) {
   
  //echo "<script>window.location='index?resto=viewDelivery&&id=$id'</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
}

if (isset($_GET['resto']) && $_GET['resto'] === 'categories' && isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = intval($_GET['delete']); // sanitize the ID

    $sql = "DELETE FROM category WHERE cat_id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Category deleted successfully'); window.location.href='index?resto=categories';</script>";
    } else {
        echo "<script>alert('Error deleting category: " . $conn->error . "');</script>";
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
          
                    <label class="col-md-2 control-label" for=""><strong>Category Name</strong></label>
                <div class="col-md-4">
                             <input type="text" name="category" placeholder="Enter category name" class="form-control">	
		
 
                </div>
                
          
                
            </div>
            </div>
            <br>
       
              <input type="submit" name="addRequest" Value="Save">
            </form>
            
             <div class="table-responsive">
						
                            <table id="data-table-basic" class="table table-striped">
                                      <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category</th>
                                        <!--<th>Action</th>-->
                                       
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                		$result = $db->prepare("SELECT * FROM category");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
                                		    $i++;
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                     	     <td><?php echo $fetch['cat_name'] ?>
                                           
                                     
								
                                      <!--<td >-->
                                             <!--<a  href="#" hidden>Edit</a>-->
                                             <!--<a href="index?resto=categories&delete=<?php echo $fetch['cat_id'] ?>" onclick="return confirm('Are you sure you want to delete This Category?')">Delete</a>-->
											<!--</td>-->
                                     	</tr>
         <?php
	}
?>
                            </tbody>
                          
                        </table>
			
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
