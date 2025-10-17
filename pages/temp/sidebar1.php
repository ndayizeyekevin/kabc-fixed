<div class="col-sm-3">
		<div class="left-sidebar">
		   <h2>Properties</h2>
				<div class="panel-group category-products" id="accordian"><!--Company Category-->
					
					<?php 
                        $mydb = $db->prepare("SELECT * FROM tbl_company_category");
                        $mydb->execute();
                        if($mydb->rowCount() > 0 ){
                            
                          while($result = $mydb->fetch()){
                              $Categ_ID = $result['category_ID'];
                              
                              $mydb2 = $db->prepare("SELECT COUNT(*) AS total FROM tbl_company WHERE categ_ID ='".$Categ_ID."' ");
                              $mydb2->execute();
                              $result2 = $mydb2->fetch();
                           
                           echo '	<div class="panel panel-default">
            						<div class="panel-heading">
            							<h4 class="panel-title"><a href="?page=cpny&cpnyCateg='.$result['category_ID'].'" target="_blank"> <span class="pull-right"> ('.$result2['total'].') </span>'.$result['categ_name'].'</a></h4>
            						</div>
            					</div>';
                              
                              
                          }
                        }
                    ?>
				
				</div><!--/Company Category-->
		    
			<div class="brands_products"><!--brands_products-->
			  <h2>Rooms</h2>
				<div class="brands-name">
					<ul class="nav nav-pills nav-stacked">
						<?php 
                            $PDO = $db->prepare("SELECT * FROM tbl_room_type");
                            $PDO->execute();
                            if($PDO->rowCount() > 0 ){
                                
                              while($row = $PDO->fetch()){
                                  $type_ID = $row['type_ID'];
                                  
                                  $PDO2 = $db->prepare("SELECT COUNT(*) AS num FROM tbl_rooms WHERE room_type_id ='".$type_ID."' ");
                                  $PDO2->execute();
                                  $row2 = $PDO2->fetch();
                                  
                              echo '<li><a href="?page=accomodation&rmtype='.$row['type_ID'].'" target="_blank"> <span class="pull-right"> ('.$row2['num'].') </span>'.$row['type_name'].'</a></li>';
                               }
                            }
                        ?>
					</ul>
				</div>
			</div><!--/brands_products-->
			
			<div class="price-range"><!--price-range-->
    			<h2>Price Range</h2>
    			<div class="well text-center price">
    				 <?php 
    				   $CRUD = $conn->prepare("SELECT MIN(price) AS min, MAX(price) AS max FROM tbl_rooms");
    				   $CRUD->execute();
    				   $avg = $CRUD->fetch();
    				 
    				 ?>
    				 <input type="text" class="span2" value="" data-slider-min="0" data-slider-max="<?php echo $avg['max'];?>" data-slider-step="5" data-slider-value="[<?php echo $avg['min'];?>,<?php echo $avg['max'];?>]" id="sl2" ><br />
    				 <b class="pull-left">$ <?php echo number_format($avg['min']);?></b> <b class="pull-right">$ <?php echo number_format($avg['max']);?></b>
    			</div>
    		</div><!--/price-range-->
		
		</div>
	</div>