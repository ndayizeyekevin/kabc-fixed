<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> Menus</strong></small></h2>
                        </div>
                        <hr>
                        <div class="row">
                            <?php
                        		$sql = $db->prepare("SELECT * FROM `menu`
                        		INNER JOIN category ON category.cat_id = menu.cat_id
                        		INNER JOIN subcategory ON subcategory.subcat_id = menu.subcat_ID
                        		WHERE category.cat_id != '2'
                        		ORDER BY menu.menu_name ASC");
                        		$sql->execute();
                        		while($fetch = $sql->fetch()){
                        		    $menu_id = $fetch['menu_id'];
                        		    if($fetch['subcat_id'] !=3){
                        		        $textStyle = "color: #43c76b;font-size:11px;";
                        		    }else{
                        		        $textStyle = "color: #43a0c7;font-size:11px;";
                        		    }
                             	?>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <div class="menu-box">
                               <!-- Food title -->
                                <div class="menu-box-head">
                                  <div class="pull-left">
                                      <?php echo $fetch['cat_name']." ".number_format($fetch['menu_price'])." Rwf"?></div>
                                  <div class="clearfix"></div>
                                </div>
                                <div class="menu-box-content referrer">
                                  <!-- Widget content -->
                                 <div class="table-responsive">
                                  <table class="table table-striped table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                      <td><?php echo $fetch['menu_name'];?></td>
                                    </tr>
                                  </tbody>
                                 </table>
                                 </div>
                                </div>
                              </div>
                           </div>
                          <?php
                           }
                          ?>             
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->