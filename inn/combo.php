<?php
if(ISSET($_POST['add'])){
    $name = $_POST['name'];
	$price = $_POST['price'];
	$menu = $_POST['menu'];
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "INSERT INTO `tbl_combo`(`combo_name`,`combo_price`)
			VALUES ('$name','$price')";
			$db->exec($sql);
			
			$lastId = $db->lastInsertId();
			
    			foreach ($menu as $value)
    	{
    		if ($value<>0)
    		{
    		$stmt = $db->prepare("INSERT INTO tbl_combo_details(combo_id,menu_id) VALUES('$lastId','$value')");  
    		$stmt->execute();
    		}
    	}
			$msg = "Successfully Added!";
			
			echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=menu_combo">';
}
?>
	<?php
// 	update

 if(ISSET($_POST['update'])){
		try{
			$c_id = $_POST['c_id'];
			$com_name = $_POST['c_name'];
			$com_price = $_POST['c_price'];
			$menu = $_POST['menu'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `tbl_combo` SET `combo_name` = '$com_name', `combo_price` = '$com_price' WHERE combo_id = '$c_id'";
			$db->exec($sql);
			
			
        	foreach ($menu as $value)
        	{
        		if ($value<>0)
        		{
        		$sql = "UPDATE `tbl_combo_details` SET `menu_id` = '$value' WHERE combo_id = '$c_id'";
    			$db->exec($sql);
        		}
        	}
    
    			$msg = "Updated Successfully";
    	
    			echo'<meta http-equiv="refresh"'.'content="1;URL=?resto=menu_combo">';
    			
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_combo` WHERE combo_id='$id' ");
		$sql->execute();
		
		$sql2 = $conn->prepare("DELETE from `tbl_combo_details` WHERE combo_id = '$id' ");
		$sql2->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="1;URL=?resto=menu_combo">';
	}
?>

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
										<h2>Manage Combo</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#addModal" data-placement="left" title="Add Combo" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->

     <div class="animation-area">
        <div class="container">
            <div class="row">
                <?php
            		$sql = $db->prepare("SELECT * FROM `tbl_combo` ORDER BY combo_name ASC");
            		$sql->execute();
            		while($fetch = $sql->fetch()){
                 	?>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="animation-single-int" style="background-color:white;padding:5px;margin-bottom:5px;">
                        <div class="animation-ctn-hd pull-left">
                            <h5><?php echo $fetch['combo_name']." - ".$fetch['combo_price']?></h5>
                            </div>
                            <div class="widget-icons pull-right">
                            <a href="#update" data-target="#update<?php echo $fetch['combo_id']?>" data-toggle="modal"><i class="fa fa-pencil "></i></a>
                            <a href="#delete" data-target="#delete<?php echo $fetch['combo_id']?>" data-toggle="modal"><i class="fa fa-times"></i></a>
                          </div>
                        
                        <div class="animation-action">
                            <table class="table table-striped table-bordered table-hover">
                    <tbody>
<?php

     $com_id = $fetch['combo_id'];
    $stmt = $db->prepare("SELECT * FROM `tbl_combo_details` 
    INNER JOIN `menu` ON tbl_combo_details.menu_id=menu.menu_id 
    WHERE combo_id = '".$com_id."'");
    $stmt->execute(array());
    $row = $stmt->fetchAll();
                            
        
?>                        
                    
                        <?php foreach($row as $row1)
                            {
                                ?>
                                <tr>
                      <td>
                          <?php
                            echo $row1['menu_name'];
                            ?>
                            </td>
                    </tr> 
                    <?php
                            }
                            ?>
                            
                   </tbody></table>
                 </div>
                    </div>
                </div>
<div class="modal fade" id="update<?php echo $fetch['combo_id']?>" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Update Combo Meals</h4>
            </div>
            <div class="modal-body" style="height:300px">
              <!--start form-->
              <form class="form-horizontal" method="post" action="combo_update.php" enctype='multipart/form-data'>
                  <!-- Title -->
                  <input type="hidden" name="id" value="<?php echo $fetch['combo_id']?>">
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-4" for="title">Combo Name</label>
                      <div class="col-lg-8"> 
                        <input type="text" class="form-control" name="name" id="title" placeholder="Combo Name" value="<?php echo $fetch['combo_name'];?>">
                      </div>
                  </div> 
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-4" for="title">Price</label>
                      <div class="col-lg-8"> 
                        <input type="text" class="form-control" name="price" id="title" placeholder="Price of Combo Meal" value="<?php echo $fetch['combo_price'];?>">
                      </div>
                  </div> 
                  <!-- Title -->
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-4" for="username">Menu</label>
                      <div class="col-lg-8"> 
                        <select class="chosen" id="exampleSelect1" name="menu[]" multiple='multiple' style="width: 100%;height:200px" placeholder="Select multiple members">
                        <?php
                            $stmts = $db->prepare("SELECT * FROM `tbl_combo_details` 
                            INNER JOIN `menu` ON tbl_combo_details.menu_id=menu.menu_id 
                            WHERE tbl_combo_details.combo_id = '".$com_id."'");
                            $stmts->execute(array());
                            while($rowm = $stmts->fetch(PDO::FETCH_ASSOC));
                         {
                         ?>
                         <option value="<?php echo $row['menu_id']; ?>"><?php echo $row['menu_name']; ?></option>
                         <?php
                         }
                         ?>
                         <?php
                                $stmt = $db->query('SELECT * FROM menu');
                                try {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        if($res_cntr==$row['menu_id'])
                                        {
                                        ?>
                                        <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['menu_name']; ?></option>
    
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <option value="<?php echo $row['menu_id']; ?>"><?php echo $row['menu_name']; ?></option>
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
                              
                  <!-- Buttons -->
                  <div class="col-lg-offset-4 col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary" name="update">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                  </div>  
              </form>
              <!--end form-->
            </div>
           
        </div><!--modal content-->
    </div><!--modal dialog-->
</div>
<?php }?>                    
                    
                  
                       
            </div>
        </div>
    </div>
    
    <!-- add combo -->
    <div id="addModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add New Combo Meal</h4>
            </div>
            <div class="modal-body">
              <!--start form-->
              <form class="form-horizontal" method="post" action="">
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-3" for="title">Combo Name</label>
                      <div class="col-lg-8"> 
                        <input type="text" class="form-control" name="name" id="title" placeholder="Combo Name">
                      </div>
                  </div> 
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-3" for="title">Price</label>
                      <div class="col-lg-8"> 
                        <input type="text" class="form-control" name="price" id="title" placeholder="Price of Combo Meal">
                      </div>
                  </div> 
                  <!-- Title -->
                  <div class="form-group">
                      <label class="control-label col-lg-3" for="username">Menu</label>
                      <div class="col-lg-8"> 
                        <select class="chosen" id="exampleSelect1" name="menu[]" multiple='multiple' style="width: 100%;height:200px" placeholder="Select multiple members">
                       
                         <?php
                              $result = $db->prepare("SELECT * FROM menu"); 
                              $result->execute();
                                  while ($row = $result->fetch()){

                                ?>
                                <option value="<?php echo $row['menu_id'];?>"><?php echo $row['menu_name'];?></option>
                        <?php } ?>
                        </select>
                      </div>
                  </div>                                      
                  <!-- Buttons -->
                  <div class="form-group">
                      <!-- Buttons -->
                      <div class="col-lg-offset-3 col-lg-6">
                        <button type="submit" name="add" class="btn btn-sm btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                       </div>
                  </div>
              </form>
              <!--end form-->
            </div>
            
        </div><!--modal content-->
    </div><!--modal dialog-->
</div>