    <?php 
    ob_start();
    $_SESSION['chkin_ava'] = $_REQUEST['chkin_ava'];
    $_SESSION['chkout_ava'] = $_REQUEST['chkout_ava'];
    $_SESSION['company'] = $_REQUEST['company'];
    
    ?>
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
            		    $comp = $db->prepare("SELECT * FROM `tbl_company` WHERE cpny_ID = '".$_SESSION['company']."'");
            		    $comp->execute();
            		    $rows = $comp->fetch();
            		  ?>
            		      <div class="row form-av">
            		      <form action="" method="POST" class="">
            		          <p>All Available Rooms  At <?php echo $rows['cpny_name']; ?> </p>
            		          <div class="col-sm-6">
            		              <label>From</label>
            		              <input type="date" class="form-control" value="<?php echo $_SESSION['chkin_ava']; ?>" disabled placeholder="checkin date">
            		          </div>
            		          <div class="col-sm-6">
            		              <label>To</label>
            		              <input type="date" class="form-control" value="<?php echo $_SESSION['chkout_ava']; ?>" disabled placeholder="checkout date">
            		          </div>
            		      
            		      </form>
            		      </div>
            		  
            		  <div class="response-area">
						<h2>3 RESPONSES</h2>
						<ul class="media-list">
						    <?php
						    $sql = $db->prepare("SELECT * FROM `tbl_rooms` 
                        	INNER JOIN `tbl_room_type` ON tbl_rooms.room_type_id=tbl_room_type.type_ID
                        	INNER JOIN `tbl_room_gallery` ON tbl_rooms.room_id=tbl_room_gallery.room_id 
                            WHERE company_id = '".$_SESSION['company']."' ");
                        	$sql->execute();
                        	while($fetch = $sql->fetch()){
                             	?>
							<li class="media inn">
								<a class="pull-left" href="#">
									<img class="media-object" src="../room_gallery/<?php echo $fetch['img_name']; ?>" class="image-responsive rounded" alt="" style="width:120px;height:120px;">
								</a>
								<div class="media-body">
									<ul class="sinlge-post-meta">
										<li><i class="fa fa-user"></i><?php echo $fetch['type_name']; ?></li>
										<li><i class="fa fa-dollar"></i><?php echo $fetch['price']; ?></li>
										<li><i class="fa fa-calendar"></i> <?php echo $fetch['cpny_name']; ?></li>
									</ul>
									<p><?php echo $fetch['desc_type']; ?></p>
								</div>
							</li>
							<?php } ?>
						</ul>					
					</div><!--/Response-area-->
				</div>
				</div>
		</div>
		</div>
	</section>