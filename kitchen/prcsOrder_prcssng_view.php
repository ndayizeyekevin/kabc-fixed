<?php
  // for activate Subject
    if(isset($_GET['p']))
    {
        try{
    			$a = 5;
    			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_cmd` SET `status_id` = '".$a."' WHERE `reservat_id` = '".$_GET['p']."' AND company_id = '".$cpny_ID."' ";
    			$acidq = $db->prepare($sql);
    			$acidq->execute();
    			$msg="Proceeded successfully";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=prcsOrder_prcssng">';
    		}catch(PDOException $e){
    			echo $e->getMessage();
    		}
      }
      ?>
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
                        		INNER JOIN tbl_reservation ON tbl_cmd.reservat_id = tbl_reservation.reservation_id
                        		WHERE status_id=7 AND company_id = '".$cpny_ID."'
                        		GROUP BY tbl_cmd.reservat_id");
                        		$sql->execute();
                        		$rowcount = $sql->rowCount();
                        		if($rowcount > 0){
                        		while($fetch = $sql->fetch()){
                        		    
                        		    $reservation_id = $fetch['reservat_id'];
                        		    $stmts = $db->prepare("SELECT * FROM tbl_reservation WHERE reservation_id = '".$reservation_id."' AND resv_CpnyID = '".$cpny_ID."' ");
                        		    $stmts->execute();
                        		    $room = $stmts->fetch();
                        		    $room_id = $room['roomID'];
                        		    
                        		    $stmtss = $db->prepare("SELECT * FROM tbl_rooms WHERE room_id = '".$room_id."' AND company_id = '".$cpny_ID."' ");
                        		    $stmtss->execute();
                        		    $rooms = $stmtss->fetch();
                        		    $room_no = $rooms['room_no'];
                        		    
                             	?>
                            <a href="?resto=prcsOrder_list" onclick="if(!confirm('Do you really want to View?'))return false;else return true;">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <div class="menu-box">
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
                                      <td align="center"><h4><?php echo $room_no;?></h4></td>
                                    </tr>
                                  </tbody>
                                 </table>
                                 </div>
                                  <!--<div class=".menu-box-foot text-center">-->
                                  <!--    <input type="checkbox" name="menu_id[]" value="<php echo $menu_id;?>">-->
                                  <!--    <div class="pull-left" style="<php echo $textStyle;?>"><php echo $fetch['subcat_name'];?></div>-->
                                  <!--</div>-->
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->