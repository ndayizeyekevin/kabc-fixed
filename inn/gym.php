  <?php
  if(ISSET($_POST['btn_save'])){
    $sql_check = $db->prepare("SELECT * FROM tbl_gym WHERE gym_name = ?");
    try {
        
        $g_name = $_POST['g_name'];
        $g_descr = $_POST['g_descr'];
        $price = $_POST['price'];
        
        $sql_check->execute(array($t_name));
        $row_count_check = $sql_check->rowCount();
                if ($row_count_check >= 1)
                {
                $msge="This gym Type Already Registered!";
                   
                }else{
             
      	try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_gym` (`gym_name`,`gym_descr`,`price`,`company_id`,`gym_status`)
            VALUES ('$g_name','$g_descr','$price','$cpny_ID',1)";
            $db->exec($sql);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
             }
            $msg= "Gym Type registered Successfully!";
            echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=gyms">';
                }catch(PDOException $e){
                echo $e->getMessage();
            }
  }
  ?>
  
  <?php
  if(ISSET($_POST['btn-update'])){
		try{
			$g_id = $_POST['gid'];
			$gname = $_POST['gname'];
			$gdescr = $_POST['gdescr'];
			$gprice = $_POST['gprice'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `tbl_gym` SET `gym_name` = '$gname', `gym_descr` = '$gdescr',`price` = '$gprice' WHERE gym_id = '$g_id'";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=gyms">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_gym` WHERE `gym_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=gyms">';
	}
?>
  
  <div class="inbox-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="inbox-left-sd">
                        <div class="inbox-status">
                            <ul class="nav nav-tabs inbox-st-nav inbox-ft">
                                <li class="active"><a data-toggle="tab" href="#view"><i class="notika-icon notika-user"></i> View All gyms</a></li>
                                <li><a data-toggle="tab" href="#add"><i class="notika-icon notika-sent"></i> Add New</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    <div class="inbox-text-list sm-res-mg-t-30">
                        
                        <div class="widget-tabs-list">
                            <div class="tab-content tab-custom-st">
                                <div id="view" class="tab-pane fade in active">
                                    <div class="tab-ctn">
                                        <!-- Data Table area Start-->
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
                        
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>gym Name</th>
							<th>Descriptions</th>
							<th>Price</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                        <?php

                            $sql_rooms = $db->prepare("SELECT * FROM tbl_gym
                            WHERE company_id = '".$cpny_ID."'");
                            $sql_rooms->execute();
                           
                            $i = 0;
                            while($fetrooms = $sql_rooms->fetch()) {
                                $i += 1;
                        ?>
                                     <tr class="gradeU">
							<td><?php echo $i; ?></td>
            				<td class="center">
                				<span><?php echo $fetrooms['gym_name']; ?></span><br/>
            				</td>
            				<td><?php echo $fetrooms['gym_descr']; ?></td>
            				<td><?php echo $fetrooms['price']; ?></td>
                            <td class="cell-icon text-center">
                                <a href="" data-toggle="modal" data-target="#update<?php echo $fetrooms['gym_id'];?>" class="edit_data"><i class="icon-edit"></i> Edit</a>|
                                <a href="?resto=gyms&&myId=<?php echo $fetrooms['gym_id']; ?>" onclick="if(!confirm('Do you really want to Delete This gym Type?'))return false;else return true;" title="Delete gym"><span style="color: #870813;"><i class="icon-trash"></i> Delete</span></a>
                            </td>
						</tr>
					<div class="modal fade" id="update<?php echo $fetrooms['gym_id']; ?>" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="POST">
                <div class="modal-body">
                    <h2>Update gyms</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" value="<?php echo $fetrooms['gym_name']; ?>" name="gname">
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="gdescr" value="<?php echo $fetrooms['gym_descr']; ?>" >
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="gprice" value="<?php echo $fetrooms['price']; ?>" >
                                    </div>
                                </div>
                            </div>
                    </div>
                        <hr>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default" name="btn-update">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="hidden" value="<?php echo $fetrooms['gym_id'];?>" name="gid"/>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
                                     	<?php } ?>
					</tbody>
				</table>
                        </div>    
                        <!-- Data Table area End-->

                        </div>
                            </div>
                             <div id="add" class="tab-pane fade in">
                                <div class="tab-ctn">
                                    <div class="row">
                                        <form action="" method="POST">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-element-list">
                                                <div class="basic-tb-hd">
                                                    <h2>Gym Type</h2>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <input type="text" name="g_name" class="form-control" placeholder="Gym Type Name" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <textarea name="g_descr" class="form-control" placeholder="gym Description" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <textarea name="price" class="form-control" placeholder="gym price" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       <button type="submit" name="btn_save" class=".notika-btn-gray pull-right">Save changes</button>
                                       
                                        </form>
                                    </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    