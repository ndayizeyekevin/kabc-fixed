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
            <?php } else if($msge){?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
                </div>
            <?php } ?>
            
            <div class="data-table-list">
                <div class="row">
                    <?php
                    $sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                        INNER JOIN category ON menu.cat_id=category.cat_id
                        INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                        WHERE tbl_cmd_qty.cmd_status = '7' AND category.cat_id != '2'
                        GROUP BY tbl_cmd_qty.cmd_table_id");
                    $sql->execute();
                    $rowcount = $sql->rowCount();
                    
                    if($rowcount > 0){
                        $counter = 0;
                        while($fetch = $sql->fetch()){
                            $reservation_id = $fetch['cmd_table_id'];
                            $code = $fetch['cmd_code'];
                            $status_id = $fetch['cmd_status'];
                            $Serv_id = $fetch['Serv_id'];
                            
                            $GetServ = $db->prepare("SELECT * FROM tbl_users WHERE user_id = '".$Serv_id."'");
                            $GetServ->execute();
                            $fServ = $GetServ->fetch();
                            
                            $room_no = $fetch['table_no'];
                            $badge = '<span class="badge" style="background-color: #F4A460; color: white; padding: 4px 8px; border-radius: 12px; font-size: 10px;">Processing</span>';
                            $service = $fServ['f_name']." ".$fServ['l_name'];
                            
                            $counter++;
                    ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 20px;">
                            <a href="?resto=prcsOrder_prcssng_view&m=<?php echo $reservation_id ?>&c=<?php echo $code ?>" 
                               onclick="return confirm('Do you really want to View?');" 
                               style="text-decoration: none; color: inherit;">
                               
                                <div class="menu-box" id="alert-<?php echo $counter; ?>" 
                                     style="border: 1px solid #e0e0e0; border-radius: 8px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); height: 180px; transition: all 0.3s ease;"
                                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'; this.style.borderColor='#F4A460';"
                                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'; this.style.borderColor='#e0e0e0';">
                                     
                                    <!-- Order timestamp -->
                                    <div class="menu-box-head" style="background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #e0e0e0; border-radius: 8px 8px 0 0;">
                                        <div style="text-align: left;">
                                            <small style="color: #666;"><?php echo date('H:i', strtotime($fetch['created_at'])); ?></small>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    
                                    <!-- Main content -->
                                    <div class="menu-box-content" style="padding: 20px; text-align: center; display: flex; flex-direction: column; justify-content: center; height: calc(100% - 45px);">
                                        <!-- Table number and badge -->
                                        <div style="margin-bottom: 10px;">
                                            <h4 style="margin-bottom: 5px; color: #333;">Table <?php echo $room_no; ?></h4>
                                            <?php echo $badge; ?>
                                        </div>
                                        
                                        <!-- Service info -->
                                        <div class="menu-box-foot">
                                            <h6><small style="color: #666;"><?php echo $service; ?></small></h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php
                        }
                    } else {
                        echo '<div class="col-12">
                                <div class="alert alert-info" style="text-align: center; margin: 20px 0;">
                                    <strong>Info!</strong> No Orders Found!
                                </div>
                              </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
    <!-- Data Table area End-->