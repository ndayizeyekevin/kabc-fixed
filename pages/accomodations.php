    <section>
		<div class="container">
		    <div class="row">
		        <div class="column">
		            <?php include('temp/sidebar.php')?>
		        </div>
			    <div class="col-sm-9 padding-right">
					<div class="features_items">
					   
            		  <h2 class="title text-center">Accomodations</h2>
            		   <?php
                        	$sql = $db->prepare("SELECT * FROM `tbl_rooms` 
                        	INNER JOIN `tbl_room_type` ON tbl_rooms.room_type_id=tbl_room_type.type_ID
                        	INNER JOIN `tbl_room_gallery` ON tbl_rooms.room_id=tbl_room_gallery.room_id");
                        	$sql->execute();
                        	while($fetch = $sql->fetch()){
                        	  $cpy_id = $fetch['company_id'];  
                     	?>
						<div class="col-sm-4">
							<div class="product-image-wrapper">
								<div class="single-products">
									<div class="productinfo text-center">
										<img src="../room_gallery/<?php echo $fetch['img_name']; ?>" class="image-responsive rounded" alt="" style="border-radius:4px;">
										<h2>US$<?php echo $fetch['price']; ?></h2>
										<p><?php echo $fetch['type_name']; ?></p>
										<a href="#" class="btn btn-default cart"><i class="fa fa-shopping-cart"></i>  Reserve Now</a>
									</div>
									<div class="product-overlay">
										<div class="overlay-content">
											<h2>US$<?php echo $fetch['price']; ?></h2>
											<p><?php echo $fetch['type_name']; ?></p>
                                            <p><?php echo $fetch['desc_type']; ?></p>
											<a href="?page=reserve&&cid=<?php echo $cpy_id; ?>&&myId=<?php echo $fetch['room_id'];?>&&price=<?php echo $fetch['price']; ?>&&type=<?php echo $fetch['type_name']; ?>&&desc=<?php echo $fetch['desc_type']; ?>&&img=<?php echo $fetch['img_name']; ?>" class="btn btn-default add-to-cart" target="_blank"><i class="fa fa-shopping-cart"></i>Reserve Now</a>
										</div>
									</div>
								</div>
								
							</div>
						</div>
						
						<?php } ?>
				</div>
				</div>
		</div>
		</div>
	</section>