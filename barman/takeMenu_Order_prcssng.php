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
                    <div class="data-table-list">
                          <div class="row">
                            <?php
                        		
                        		$sql = $db->prepare("SELECT * FROM `tbl_cmd`
                        		INNER JOIN menu ON menu.menu_id = tbl_cmd.menu_id
                                INNER JOIN category ON menu.cat_id=category.cat_id
                        		INNER JOIN tbl_tables ON tbl_cmd.reservat_id = tbl_tables.table_id
                        		WHERE tbl_cmd.status_id = '7' AND category.cat_id != '2'
                        		GROUP BY tbl_cmd.reservat_id");
                        	    $sql->execute();
                        		$rowcount = $sql->rowCount();
                        		if($rowcount > 0){
                        		while($fetch = $sql->fetch()){
                        		    $reservation_id = $fetch['reservat_id'];
                        		    $status_id = $fetch['status_id'];
                        		    $Serv_id = $fetch['Serv_id'];
                        		    
                        		    $GetServ = $db->prepare("SELECT *FROM tbl_users WHERE user_id = '".$Serv_id."'");
                                    $GetServ->execute();
                                    $fServ = $GetServ->fetch();
                                   
                        		    $room_no = $fetch['table_no'];
                        		    
                                    $badge ='<h5><small><span  class="badge" style="background-color: #F4A460;">New</span></small></h5>';
                                    $service = $fServ['f_name']." ".$fServ['l_name'];
                        		    
                             	?>
                            <div>
                             <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" id="alert">
                                  <a href="?resto=prcsOrder_prcssng_view&m=<?php echo $reservation_id ?>" onclick="if(!confirm('Do you really want to View?'))return false;else return true;" class="menu-box">
                                   <!-- Food title -->
                                    <div class="menu-box-head">
                                      <div class="pull-left">
                                          <?php echo $fetch['dTrm']; ?></div>
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
                                  </a>
                                </div>
                           </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->