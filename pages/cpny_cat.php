    <section>
		<div class="container">
		    <div class="row main_content">
		        <div class="column">
		           
		            <?php include('temp/sidebar.php')?>
		        </div>
			<div class="col-sm-9 padding-right">
			     <?php
        		$sql1 = $db->prepare("SELECT * FROM `tbl_company` INNER JOIN `tbl_company_category` ON tbl_company.categ_ID=tbl_company_category.category_ID WHERE categ_ID = '".$_REQUEST['cpnyCateg']."' ");
        		$sql1->execute();
        		$fetch1 = $sql1->fetch()
             	?>
			     <h2 class="title text-center"><?php echo $fetch1['categ_name']; ?></h2>
			    
		    <?php
        		$sql = $db->prepare("SELECT * FROM `tbl_company` INNER JOIN `tbl_company_category` ON tbl_company.categ_ID=tbl_company_category.category_ID WHERE categ_ID = '".$_REQUEST['cpnyCateg']."' ");
        		$sql->execute();
        		$rowcount = $sql->rowCount();
        		if($rowcount > 0){
        		while($fetch = $sql->fetch()){
        		    $image = $fetch['cpny_bunner'];
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
				  
				<button class="btn btn-primary btn-show" style="border-radius: 4px;" onClick="javascript:window.open('?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>', '_blank');">Show Prices</button>
				 
				</div>
			  <div class="col-sm-2 aside">
				<h4 class="hot-categ"><?php echo $fetch['categ_name']; ?></h4> 
				
				<a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank" class="rel">
			      <small><i class="fa fa-phone"></i> <?php echo $fetch['cpny_phone']; ?></small>
			    </a>
			   
			   <p>
    			   <a href="?page=details&&cpny_id=<?php echo $fetch['cpny_ID'] ?>" target="_blank" class="rel">
    			     <label class="label label-success">Check Availablity</label>
    			    </a>
			    </p>
			  </div>
			 </div>
			<?php 
        		    
        		}
        		}
        		else{
        		    ?>
        		    <center>
        		    <div class="card">
        		        <label>No data Found!</label>
        		    </div>
                    </center>
                    <?php
        		}
			?>
			
			<?php 
			 include('pagination.php');
			?>
			</div>
		</div>
		</div>
	</section>