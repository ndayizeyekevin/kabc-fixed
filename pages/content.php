<section id="slider"><!--slider-->
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div id="slider-carousel" class="carousel slide" data-ride="carousel">
					<ol class="carousel-indicators">
						<li data-target="#slider-carousel" data-slide-to="0" class="active"></li>
						<li data-target="#slider-carousel" data-slide-to="1"></li>
						<li data-target="#slider-carousel" data-slide-to="2"></li>
					</ol>
					
					<div class="carousel-inner">
						<div class="item active">
							<div class="col-sm-12">
								<img src="images/home/slide1.jpg" class="show img-responsive" alt="Ougami" />
							</div>
						</div>
						<div class="item">
							<div class="col-sm-12">
								<img src="images/home/slide2.jpg" class="show img-responsive" alt="Ougami" />
							</div>
						</div>
						
						<div class="item">
							<div class="col-sm-12">
								<img src="images/home/slide3.jpg" class="show img-responsive" alt="Ougami" />
							
							</div>
						</div>
						
					</div>
					
					<a href="#slider-carousel" class="left control-carousel hidden-xs" data-slide="prev">
						<i class="fa fa-angle-left"></i>
					</a>
					<a href="#slider-carousel" class="right control-carousel hidden-xs" data-slide="next">
						<i class="fa fa-angle-right"></i>
					</a>
				</div>
				
			</div>
		</div>
	</div>
</section><!--/slider-->


<section>
	<div class="container">
	    <div class="row main_content">
		<div class="col-sm-9 padding-right">
		 <h2 class="title text-center">Features</h2>
		    
	      <?php
    		$sql = $db->prepare("SELECT * FROM `tbl_company` INNER JOIN `tbl_company_category` ON tbl_company.categ_ID=tbl_company_category.category_ID ");
    		$sql->execute();
    		while($fetch = $sql->fetch()){
    		    $image = $fetch['cpny_bunner'];
    		    $cpny = $fetch['cpny_ID'];
          ?>
            
			<div class="features_items inn">
				<div class="col-sm-4">
				    <a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank">
				        <?php if($image != ""){?>
    					 <img src="../bunnerCpny/<?php echo $image; ?>" class="rounded hot" alt="Ougami">
    					<?php }else{?>
    					 <img src="../bunnerCpny/default-thumbnail.jpg" class="rounded hot" alt="Ougami">
					   <?php }?>
					</a>
				</div>
				
				<div class="col-sm-6">
					<h2 class="hotname">
					   <a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank">
					     <?php echo $fetch['cpny_name']; ?> <span> <img src="images/product-details/rating.png" alt="" /></span>
					   </a>
					</h2>
				  <p>
				     <a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank" class="rel">
				        <u>
    				        <i class="fa fa-map-marker"></i> <?php echo $fetch['cpny_address']; ?>
				        </u>
				      </a>
				      &bullet;
				     <a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank" class="rel">
				       <i class="fa fa-envelope"></i> <?php echo $fetch['cpny_email']; ?>
				     </a>
				  </p>
				  <p>
				   <?php echo $fetch['cpny_notes']; ?>
				  </p>
				  <?php 
    				   $CRUD = $conn->prepare("SELECT COUNT(*) AS tot, MIN(price) AS min, MAX(price) AS max ,MIN(price_fbu) AS min_fbu, MAX(price_fbu) AS max_fbu FROM tbl_rooms");
    				   $CRUD->execute();
    				   $avg = $CRUD->fetch();
    				 if($cpny == 1){
    				 ?>
    			<p>Number Of Rooms: <?php echo $avg['tot']; ?></p>
    			<p><?php echo "Single: $".number_format($avg['min'])." - ".number_format($avg['min_fbu'])." BIF";?></p>
    			<p><?php echo "Deluxe: $".number_format($avg['max'])." - ".number_format($avg['max_fbu'])." BIF";?></p>
    			<?php } 
    			else{
    			    echo "";
    			}
    			?>
				  
				<button class="btn btn-primary btn-show" style="border-radius: 4px;" title="View rooms" onClick="javascript:window.open('?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>', '_blank');"><i class="fa fa-eye"></i> View More</button><br>
				 
				</div>
			  <div class="col-sm-2 aside">
				<h4 class="hot-categ"><?php echo $fetch['categ_name']; ?></h4> 
				
				<a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank" class="rel">
			      <small><i class="fa fa-phone"></i> <?php echo $fetch['cpny_phone']; ?></small>
			    </a>
			   
			   <p>
    			   <a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank" class="rel">
    			     <label class="label label-success">Active</label>
    			    </a>
			    </p>
			  </div>
			 </div>
           
		   <?php } ?>
		   
    	<?php 
    	include('pagination.php');
    	?>
						
		</div>
		
		<div class="column">
	            <?php include('temp/sidebar.php')?>
	        </div>
	  </div>
	</div>
</section>