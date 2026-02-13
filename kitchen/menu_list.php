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
                        <!-- Live Search Form -->
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <input type="text" id="liveSearchInput" class="form-control" placeholder="Search by menu name or subcategory..." style="font-size: 16px; padding: 10px;">
                                <small style="color: #666; margin-top: 5px; display: block;">Results update as you type</small>
                            </div>
                        </div>
                        <div class="row" id="menuContainer">
                            <?php
                        		$sql = "SELECT * FROM `menu`
                        		INNER JOIN category ON category.cat_id = menu.cat_id
                        		INNER JOIN subcategory ON subcategory.subcat_id = menu.subcat_ID
                        		WHERE category.cat_id != '2'
                        		ORDER BY menu.subcat_id ASC";
                        		
                        		$stmt = $db->prepare($sql);
                        		$stmt->execute();
                        		$prevSubcatId = null;
                        		while($fetch = $stmt->fetch()){
                        		    $menu_id = $fetch['menu_id'];
                        		    if($fetch['subcat_id'] !=3){
                        		        $textStyle = "color: #43c76b;font-size:11px;";
                        		    }else{
                        		        $textStyle = "color: #43a0c7;font-size:11px;";
                        		    }
                        		    
                        		    // Display subcategory header when it changes
                        		    if($prevSubcatId != $fetch['subcat_id']){
                        		        if($prevSubcatId !== null){
                        		            echo '</div>'; // Close previous row
                        		        }
                        		        echo '<div class="row"><div class="col-lg-12"><h4 style="margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;"><strong>' . $fetch['subcat_name'] . ' (ID: ' . $fetch['subcat_id'] . ')</strong></h4></div></div>';
                        		        echo '<div class="row">';
                        		        $prevSubcatId = $fetch['subcat_id'];
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
                                      <td class="menu-item-name" data-menu-name="<?php echo strtolower($fetch['menu_name']); ?>" data-subcat-name="<?php echo strtolower($fetch['subcat_name']); ?>"><?php echo $fetch['menu_name'];?></td>
                                    </tr>
                                  </tbody>
                                 </table>
                                 </div>
                                </div>
                              </div>
                           </div>
                          <?php
                           }
                           echo '</div>'; // Close the last row div
                          ?>             
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->

    <script>
    document.getElementById('liveSearchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const menuContainer = document.getElementById('menuContainer');
        const allRows = menuContainer.querySelectorAll('.row');
        const menuBoxes = menuContainer.querySelectorAll('.col-lg-2');
        
        let visibleCount = 0;
        const displayedSubcats = new Set();
        
        if (searchTerm === '') {
            // If search is empty, show everything
            allRows.forEach(row => row.style.display = '');
            menuBoxes.forEach(box => box.style.display = '');
        } else {
            // Hide all rows initially
            allRows.forEach(row => row.style.display = 'none');
            
            // First pass: determine which items match
            menuBoxes.forEach(box => {
                const menuItemName = box.querySelector('.menu-item-name');
                if (!menuItemName) return;
                
                const menuName = menuItemName.getAttribute('data-menu-name') || '';
                const subcatName = menuItemName.getAttribute('data-subcat-name') || '';
                
                const matches = menuName.includes(searchTerm) || subcatName.includes(searchTerm);
                
                if (matches) {
                    box.style.display = '';
                    box.closest('.row').style.display = '';
                    visibleCount++;
                    // Show header for this subcategory
                    const headerRow = box.closest('.row').previousElementSibling;
                    if(headerRow && headerRow.querySelector('h4')) {
                        headerRow.style.display = '';
                    }
                } else {
                    box.style.display = 'none';
                }
            });
        }
        
        // Show "no results" message if needed
        let noResultsMsg = document.getElementById('noResultsMsg');
        if(visibleCount === 0 && searchTerm !== '') {
            if(!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.id = 'noResultsMsg';
                noResultsMsg.className = 'alert alert-info';
                noResultsMsg.style.marginTop = '20px';
                noResultsMsg.textContent = 'No menus found matching your search.';
                menuContainer.parentElement.insertBefore(noResultsMsg, menuContainer);
            }
            noResultsMsg.style.display = '';
        } else if(noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    });
    </script>