<script>
     setTimeout(function() {
    window.location.reload();
}, 60000);
 </script>
<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                          <div class="row">
                            <?php
                        		$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                        		INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                INNER JOIN category ON menu.cat_id=category.cat_id
                        		INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                        		WHERE tbl_cmd_qty.cmd_status = '13' AND category.cat_id != '2'
                        		GROUP BY tbl_cmd_qty.cmd_table_id");
                        	    $sql->execute();
                        		$rowcount = $sql->rowCount();
                        		if($rowcount > 0){
                        		while($fetch = $sql->fetch()){
                        		    $reservation_id = $fetch['cmd_table_id'];
                                $code = $fetch['cmd_code'];
                        		    $status_id = $fetch['cmd_status'];
                        		    $Serv_id = $fetch['Serv_id'];
                        		    
                        		    $GetServ = $db->prepare("SELECT *FROM tbl_users WHERE user_id = '".$Serv_id."'");
                                    $GetServ->execute();
                                    $fServ = $GetServ->fetch();
                                   
                        		    
                        		    $stmtss = $db->prepare("SELECT * FROM tbl_tables WHERE table_id = '".$reservation_id."'");
                        		    $stmtss->execute();
                        		    $rooms = $stmtss->fetch();
                        		    $room_no = $rooms['table_no'];
                        		    
                                    $badge ='<h5><small><span  class="badge" style="background-color: #dd5252;">New</span></small></h5>';
                                    $service = $fServ['f_name']." ".$fServ['l_name'];
                                    
                                      
                             	?>
                                <a href="?resto=prcsOrder_list&m=<?php echo $reservation_id; ?>&c=<?php echo $code; ?>" onclick="if(!confirm('Do you really want to View this order?'))return false;else return true;">
                                 <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" id="alert">
                                  <div class="menu-box">
                                   <!-- Food title -->
                                    <div class="menu-box-head">
                                      <div class="pull-left">
                                          <?php echo $fetch['created']; ?></div>
                                      <div class="clearfix"></div>
                                    </div>
                                    <div class="menu-box-content referrer">
                                      <!-- Widget content -->
                                     <div class="table-responsive">
                                      <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                        <tr>
                                          <td align="center"><h4>Table No<?php echo $room_no." ".$badge;?></h4></td>
                                        </tr>
                                      </tbody>
                                     </table>
                                     </div>
                                      <div class=".menu-box-foot text-center">
                                        <h5><small> <?php echo $service;?> </small></h5>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                               </a>
                              <?php
                            		}
                            		}
                            	else{
                    		        echo "<div class='alert alert-info'>
                                          <strong>Info!</strong> No Order Found!
                                        </div>";
                    		    }	   
                               
                              ?>             
                        </div>
                        <center>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Data Table area End-->