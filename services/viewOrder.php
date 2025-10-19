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
                 <strong>Oooh Snap!</strong> <?php echo htmlentities($msge); ?>
              </div>
            <?php
              }else if($snp){
            ?>

             <div class="alert alert-info alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <strong>Sorry!</strong> <?php echo htmlentities($snp); ?>
              </div>
            <?php } ?>

              <div class="row">

                  <div class="col-lg-4">





                  </div>

                    <div class="col-lg-12">


                  <?php
                        		$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                        		INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                        		WHERE tbl_cmd_qty.cmd_status != '12' AND  tbl_cmd_qty.Serv_id= '".$Oug_UserID."'
                        		GROUP BY tbl_cmd_qty.cmd_code");
                        		$sql->execute();
                        		$rowcount = $sql->rowCount();
                        		if($rowcount > 0){
                        		while($fetch = $sql->fetch()){
                        		    $reservation_id = $fetch['cmd_table_id'];
                        		    $room_id = $fetch['table_id'];
                        		    $status_id = $fetch['cmd_status'];
                                $codeorder = $fetch['cmd_code'];

                        		    $GetSts = $db->prepare("SELECT *FROM tbl_status WHERE id = '".$status_id."'");
                                    $GetSts->execute();
                                    $fsts = $GetSts->fetch();


                        		    $stmtss = $db->prepare("SELECT * FROM tbl_tables WHERE table_id = '".$room_id."'");
                        		    $stmtss->execute();
                        		    $rooms = $stmtss->fetch();
                        		    $room_no = $rooms['table_no'];





                                $stmtssclient = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$codeorder."'");
                        		    $stmtssclient->execute();
                        		    $client = $stmtssclient->fetch();
                        		    $clientname = $client['room_client'];

                                      if($status_id == 3){
                                          $bullet ='<i class="fa fa-circle" aria-hidden="true" style="color: #dd5252;"></i>';
                                          $text = $fsts['status_name'];
                                      }
                                       elseif($status_id == 7){
                                         $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #fe8b03;"></i>';
                                         $text = $fsts['status_name'];
                                      }
                                       elseif($status_id == 5){
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
                                          $text = $fsts['status_name'];
                                      }else{
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
                                          $text = $fsts['status_name'];
                                      }
                             	?>
                            <a href="?resto=prcsOrder_prcssng&m=<?php echo $reservation_id ?>&s=<?php echo $fetch['cmd_code']; ?>&st=<?php echo $fetch['cmd_status']; ?>" onclick="if(!confirm('Do you really want to View?'))return false;else return true;">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" id="alert">
                              <div class="menu-box">
                               <!-- Food title -->
                                <div class="menu-box-head">
                                  <div class="pull-left">
                                      <?php echo $fetch['created_at']; ?></div>
                                  <div class="clearfix"></div>
                                </div>
                                <div class="menu-box-content referrer">
                                  <!-- Widget content -->
                                 <div class="table-responsive">
                                  <table class="table table-striped table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                      <td align="center"><h4><?php echo $bullet." Table No".$room_no;?></h4></td>
                                    </tr>
                                  </tbody>
                                 </table>
                                 </div>
                                  <div class=".menu-box-foot text-center">
                                  <h5><b> <?php echo $clientname;?> </b></h5>
                                    <h5><small> <?php echo $text;?> </small></h5>
                                  </div>
                                </div>
                              </div>
                           </div>
                           </a>
                          <?php
                        		   }
                        		    }
                        		    else{
                        		        echo "<div class='alert alert-warning'>
                                      <strong>Info!</strong> No Order Found!
                                    </div>";
                        		    }
                          ?>


                  </div>
                  </div>

                    <div class="data-table-list">
                          <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->