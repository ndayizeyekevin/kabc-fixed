  <?php
  if(ISSET($_POST['btn_save'])){
    $sql_check = $db->prepare("SELECT * FROM tbl_swimming WHERE swim_name = ?");
    try {
        
        $s_name = $_POST['s_name'];
        $s_descr = $_POST['s_descr'];
        $price = $_POST['price'];
        
        $sql_check->execute(array($s_name));
        $row_count_check = $sql_check->rowCount();
                if ($row_count_check >= 1)
                {
                $msge="This swim Type Already Registered!";
                   
                }else{
             
      	try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_swimming` (`swim_name`,`swim_descr`,`price`,`company_id`,`swim_status`)
            VALUES ('$s_name','$s_descr','$price','$cpny_ID',1)";
            $db->exec($sql);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
             }
            $msg= "Swimming Type registered Successfully!";
            echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=swimming">';
                }catch(PDOException $e){
                echo $e->getMessage();
            }
  }
  ?>
  
  <?php
  if(ISSET($_POST['btn-update'])){
		try{
			$s_id = $_POST['sid'];
			$sname = $_POST['sname'];
			$sdescr = $_POST['sdescr'];
			$sprice = $_POST['sprice'];

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "UPDATE `tbl_swimming` SET `swim_name` = '$sname', `swim_descr` = '$sdescr',`price` = '$sprice' WHERE swim_id = '$s_id'";
			$db->exec($sql);
			
			$msg = "Updated Successfully";
	
			echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=swimming">';
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
 }
?>
<!--delete-->

<?php
        if(ISSET($_GET['myId'])){
		$id = $_GET['myId'];
		$sql = $conn->prepare("DELETE from `tbl_swimming` WHERE `swim_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=swimming">';
	}
?>
  
  <div class="inbox-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="inbox-left-sd">
                        <div class="inbox-status">
                            <ul class="nav nav-tabs inbox-st-nav inbox-ft">
                                <li class="active"><a data-toggle="tab" href="#view"><i class="notika-icon notika-user"></i> View All Swimming Pool</a></li>
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
							<th>Swim Pool Name</th>
							<th>Descriptions</th>
							<th>Price</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                        <?php

                            $sql_rooms = $db->prepare("SELECT * FROM tbl_swimming
                            WHERE company_id = '".$cpny_ID."'");
                            $sql_rooms->execute();
                           
                            $i = 0;
                            while($fetrooms = $sql_rooms->fetch()) {
                                $i += 1;
                        ?>
                                     <tr class="gradeU">
							<td><?php echo $i; ?></td>
            				<td class="center">
                				<span><?php echo $fetrooms['swim_name']; ?></span><br/>
            				</td>
            				<td><?php echo $fetrooms['swim_descr']; ?></td>
            				<td><?php echo $fetrooms['price']; ?></td>
                            <td class="cell-icon text-center">
                                <a href="" data-toggle="modal" data-target="#update<?php echo $fetrooms['swim_id'];?>" class="edit_data"><i class="icon-edit"></i> Edit</a>|
                                <a href="?resto=swimming&&myId=<?php echo $fetrooms['swim_id']; ?>" onclick="if(!confirm('Do you really want to Delete This swim Type?'))return false;else return true;" title="Delete swim"><span style="color: #870813;"><i class="icon-trash"></i> Delete</span></a>
                            </td>
						</tr>
					<div class="modal fade" id="update<?php echo $fetrooms['swim_id']; ?>" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="POST">
                <div class="modal-body">
                    <h2>Update swims</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" value="<?php echo $fetrooms['swim_name']; ?>" name="sname">
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="sdescr" value="<?php echo $fetrooms['swim_descr']; ?>" >
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="sprice" value="<?php echo $fetrooms['price']; ?>" >
                                    </div>
                                </div>
                            </div>
                    </div>
                        <hr>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default" name="btn-update">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="hidden" value="<?php echo $fetrooms['swim_id'];?>" name="sid"/>
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
                                                    <h2>Swimming Pool</h2>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <input type="text" name="s_name" class="form-control" placeholder="Swim Pool Name" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <textarea name="s_descr" class="form-control" placeholder="Description" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <div class="form-group ic-cmp-int">
                                                            <div class="form-ic-cmp">
                                                            </div>
                                                            <div class="nk-int-st">
                                                                <textarea name="price" class="form-control" placeholder="Price" required></textarea>
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
    