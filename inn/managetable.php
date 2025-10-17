  <?php
  if(ISSET($_POST['btn_save'])){
      $table = $_POST['table'];
    $sql_check = $db->prepare("SELECT * FROM tbl_tables WHERE table_no = ?");
    try {
        $sql_check->execute(array($table));
        $row_count_check = $sql_check->rowCount();
                if ($row_count_check >= 1)
                {
                $msge="This Table is Already Registered!";
                   
                }else{
      	try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `tbl_tables` (`table_no`,`status`)
            VALUES ('$table','1')";
            $db->exec($sql);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
             }
            $msg= "Table with Number: <b>".$table."</b> has been registered Successfully!";
            //echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=table">';
                }catch(PDOException $e){
                echo $e->getMessage();
            }
  }
  // for activate Subject
    if(isset($_GET['acid']))
    {

        try{
    			$disable = 2;
    			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_tables` SET `status` = '".$disable."' WHERE `table_id` = '".$_GET['acid']."'";
    			$acidq = $db->prepare($sql);
    			$acidq->execute();
    			$msge="Table Deactivated successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=table">';
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
    		
      }
    
     // for Deactivate Subject
        else if(isset($_GET['did']))
          {
     try{
    			$enable = 1;
    			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_tables` SET `status` = '".$enable."' WHERE `table_id` = '".$_GET['did']."'";
    			$didq = $db->prepare($sql);
    			$didq->execute();
    			$msg="Table Activated successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=table">';
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
    		
    	}
     if(isset($_POST['update']))
    {

        try{
            $table_upd = $_POST['table_upd'];
            $roomid = $_POST['r_id'];
            
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql_upd = "UPDATE `tbl_tables` SET `table_no` = '$table_upd' WHERE `table_id` = $roomid ";
    			$conn->exec($sql_upd);
    			$msg="Table Updated successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=table">';
              
        }catch(PDOException $e){
    			echo $e->getMessage();
    		}
    }
    		
    	if(ISSET($_GET['delete_table'])){
    	    try{
    	        
		$id = $_GET['delete_table'];
		$sql = $conn->prepare("DELETE from `tbl_tables` WHERE `table_id`='$id' ");
		$sql->execute();

		$msg = "Deleted Successfully!";
		
		echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=table">';
		
	    }catch(PDOException $e){
			echo $e->getMessage();
		}
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
										<h2>Manage Table</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Table" class="btn"><i class="fa fa-plus-circle"></i> Add</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
            							<th>Table No.</th>
                                    </tr>
                                </thead>
                                	<tbody>
                                        <?php
                
                                            $sql_rooms = $db->prepare("SELECT * FROM tbl_tables ");
                                            $sql_rooms->execute();
                                            $i = 0;
                                            while($fetrooms = $sql_rooms->fetch()) {
                                                $stat = $fetrooms['status'];
                                                $rm_id = $fetrooms['table_id'];
                                                $sql_stat = $db->prepare("SELECT * FROM tbl_status where id='$stat'");
                                                $sql_stat->execute();
                                                $fetstat = $sql_stat->fetch();
                                                $i += 1;
                                        ?>
                                     <tr class="gradeU">
            							<td><?php echo $i; ?></td>
                        				<td class="center">
                            				<span><?php echo $fetrooms['table_no']; ?></span><br/>
                        				</td>
            						</tr>
						            <div class="modal fade" id="update<?php echo $rm_id; ?>" role="dialog">
                                        <div class="modal-dialog modals-default">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="" enctype="multipart/form-data" method="POST">
                                                    <input type="hidden" name="r_id" value="<?php echo $rm_id; ?>">
                                                <div class="modal-body">
                                                    <h2>Update Room</h2>
                                                    
                                                    <div class="row">
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                                <div class="form-group ic-cmp-int float-lb floating-lb">
                                                                    <div class="form-ic-cmp">
                                                                        
                                                                    </div>
                                                                    <div class="nk-int-st">
                                                                        <small>Table No</small>
                                                                        <input type="text" name="table_upd" class="form-control" value="<?php echo $fetrooms['table_no'] ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        <hr>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-default" name="update">Save changes</button>
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                            
                                                
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                     	<?php } ?>
					</tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
    							<th>Table No.</th>
                            </tr>
                        </tfoot>
                      </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Data Table area End-->

<div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" enctype="multipart/form-data" method="POST">
                <div class="modal-body">
                    <h2>Add Table</h2>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group ic-cmp-int float-lb floating-lb">
                                <div class="form-ic-cmp">
                                    
                                </div>
                                <div class="nk-int-st">
                                    <input type="text" name="table" class="form-control" placeholder="Table No" required>
                                </div>
                            </div>
                        </div>
                    </div>
                   </div>
                  <hr>
                <div class="modal-footer">
                    <button type="submit" name="btn_save" class="btn btn-default">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>