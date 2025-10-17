    <section>
		<div class="container">
		    <div class="row">
		        <div class="column">
		            <?php include('temp/sidebar.php')?>
		        </div>
			    <div class="col-sm-9 padding-right">
					<div class="features_items">	
            		  <h2 class="title text-center">Features Items</h2>
            		  <!--availability-->
            	       <?php
                        	$sql1 = $db->prepare("SELECT * FROM `tbl_rooms`
                        	INNER JOIN `tbl_company` ON tbl_rooms.company_id=tbl_company.cpny_ID
                        	INNER JOIN `tbl_room_type` ON tbl_rooms.room_type_id=tbl_room_type.type_ID
                        	INNER JOIN `tbl_room_gallery` ON tbl_rooms.room_id=tbl_room_gallery.room_id 
                            WHERE company_id = '".$_REQUEST['cpny_id']."' ");
                        	$sql1->execute();
                        	$rowcount = $sql1->rowCount();
                        	if($rowcount > 0){
                        	$fetch1 = $sql1->fetch();
                     	?>
            		      <div class="row form-av">
            		      <form action="?page=available" method="POST" class="">
            		          <p>Would you like to stay at <?php echo $fetch1['cpny_name']; ?> Hotel?</p>
            		          <div class="col-sm-4">
            		              <label>CheckIn date</label>
            		              <input type="hidden" class="form-control" name="company" value="<?php echo $_REQUEST['cpny_id']; ?>">
            		              <input type="date" class="form-control" name="chkin_ava" placeholder="checkin date">
            		          </div>
            		          <div class="col-sm-4">
            		              <label>CheckOut Date</label>
            		              <input type="date" class="form-control" name="chkout_ava" placeholder="checkout date">
            		          </div>
            		          <div class="col-sm-4">
            		              <button type="submit" class="btn btn-primary">Check Availability</button>
            		          </div>
            		      </form>
            		      </div>
            		  
            		   <?php
                	     }
                        	$sql = $db->prepare("SELECT * FROM `tbl_rooms` 
                        	INNER JOIN `tbl_room_type` ON tbl_rooms.room_type_id=tbl_room_type.type_ID
                        	INNER JOIN `tbl_room_gallery` ON tbl_rooms.room_id=tbl_room_gallery.room_id 
                            WHERE company_id = '".$_REQUEST['cpny_id']."' ");
                        	$sql->execute();
                        	$rowcount = $sql->rowCount();
                        	if($rowcount > 0){
                        	while($fetch = $sql->fetch()){
                     	?>
						<div class="col-sm-4">
							<div class="product-image-wrapper">
								<div class="single-products">
									<div class="productinfo text-center">
										<img src="../room_gallery/<?php echo $fetch['img_name']; ?>" class="image-responsive rounded" alt="" />
										<h2>$ <?php echo $fetch['price']; ?></h2>
										<p><?php echo $fetch['type_name']; ?></p>
										<a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Reserve Now</a>
									</div>
									<div class="product-overlay">
										<div class="overlay-content">
											<h2>$ <?php echo $fetch['price']; ?></h2>
											<p><?php echo $fetch['type_name']; ?></p>
                                             <p><?php echo $fetch['desc_type']; ?></p>
											<a href="?page=reserve&&cid=<?php 
                                              echo $_REQUEST['cpny_id'] ?>&&myId=<?php echo $fetch['room_id'];?>&&price=<?php echo $fetch['price']; ?>&&type=<?php echo $fetch['type_name']; ?>&&desc=<?php echo $fetch['desc_type']; ?>&&img=<?php echo $fetch['img_name']; ?>" class="btn btn-default add-to-cart" target="_blank"><i class="fa fa-shopping-cart"></i>Reserve Now</a>
										</div>
									</div>
								</div>
								
							</div>
						</div>
						
					<?php }
                	   }
                	 else{
        		    ?>
        		    <center>
        		    <div class="card">
        		        <label>No Record Found!</label>
        		    </div>
                    </center>
                    <?php
        		        }
					?>
				 </div>
				</div>
    		  </div>
    		</div>
	      </section>