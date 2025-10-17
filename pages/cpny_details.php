<?php
   $arrival= '';
   $departure= '';
   
 if (isset($_REQUEST['from'])){
    $_SESSION['arrival'] = $_REQUEST['from']; 
    $_SESSION['departure'] = $_REQUEST['to'];
    }
    if (isset($_REQUEST['adult'])) {
    	# code...
     	$adult =  $_REQUEST['adult'];
    }else{
    	$adult= '';
    }
    if (isset($_REQUEST['children'])) {
    	# code...
      $children =  $_REQUEST['children'];
    }else{
    	$children = '';
    }
    if(isset($_POST['btnbook'])){
    
    	if (!isset($_REQUEST['from']) || !isset($_REQUEST['to'])){
    		message("Please Choose check in Date and Check out Out date to continue reservation!", "error");
    		redirect("?page=home");
    	}
    		 if(isset($_POST['roomid'])){
        	 $_SESSION['roomid']=$_POST['roomid'];
        	 redirect('?page=yr_cart');
       
    	}
      }
?>

<section>
 <div class="container">
    <div class="row">
        <div class="column">
            <?php include('temp/sidebar.php')?>
        </div>
	    <div class="col-sm-9 padding-right">
			<div class="features_items">	
    		  <h2 class="title text-center">Room and Rates</h2>
    		  
    		  <nav aria-label="breadcrumb">
			    <ol class="breadcrumb">
				    <li class="breadcrumb-item"><a href="#">Step 1: Select Dates</a></li>
				    <li class="breadcrumb-item" active aria-current="page" >Step 2: Select Rooms</li>
			    </ol>
			  </nav>
    		 
    		   <?php
                	$sql = $db->prepare("SELECT * FROM `tbl_rooms`
                	INNER JOIN `tbl_room_type` ON tbl_rooms.room_type_id=tbl_room_type.type_ID
                	INNER JOIN `tbl_room_gallery` ON tbl_rooms.room_id=tbl_room_gallery.room_id 
                    WHERE tbl_rooms.Adults = '".$adult."' AND Children='".$children."' ");
                	$sql->execute();
                	$rowcount = $sql->rowCount();
                	if($rowcount > 0){
                ?>
                
                <p class="bg-warning">
					<?php 
					echo '<div class="alert alert-info" ><strong>From:'.$_SESSION['arrival']. ' To: ' .$_SESSION['departure'].'</strong>  </div>';
					?>
				</p>
                	    
                <?php    
                	while($fetch = $sql->fetch()){
                	    $SQuery = $conn->prepare("SELECT STATUS FROM reservation
                	    WHERE (('$_SESSION[arrival]' >= arrival AND  '$_SESSION[arrival]' <= departure 
                	    OR ('$_SESSION[departure]' >= arrival	AND  '$_SESSION[departure]' <= departure)
						OR (arrival >=  '$_SESSION[arrival]' AND arrival <=  '$_SESSION[departure]')) AND roomNo = '".$fetch['room_id']."' ");
						$SQuery->execute();
						
						$SingleRes = $SQuery->fetch();
						
						$diff = abs(strtotime($_SESSION['departure']) - strtotime($_SESSION['arrival']));
                        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						
						$_SESSION['img_name'] = $fetch['img_name'];
						$_SESSION['type_name'] = $fetch['type_name'];
						$_SESSION['Adults'] = $fetch['Adults'];
						$_SESSION['Children'] = $fetch['Children'];
						$_SESSION['price'] = $fetch['price'];
						$_SESSION['price_fbu'] = $fetch['price_fbu'];
						$_SESSION['nights'] = $days;
                	    
             	?>
				
				
				<div class="col-sm-6">
                    <div class="col-md-8">
                      <img src="../room_gallery/<?php echo $_SESSION['img_name']; ?>" class="rounded hot" alt="Ougami">
                    </div>
                    <div class="col-md-4" style=" margin-top: 10px;">
                        <h5 class="card-title">Card title</h5>
                        
                        <form name="book"  method="POST" action="?page=yr_cart">
                            <input type="hidden" name="roomid" value=""/>
				  		    <p>	
			  		         <strong><?php echo $_SESSION['type_name']; ?><br/> 
	  					 	 <strong>Max Adults: <strong><?php echo $_SESSION['Adults']; ?><br/>  
	  						 <strong>Max Children: <strong><?php echo $_SESSION['Children']; ?> <br/>
	  						 <strong>$ <?php echo $_SESSION['price']." - ".$_SESSION['price_fbu']."FBu"; ?> /Night </strong>
		  					</p>
		  					<?php 
                                $stat= $SingleRes['status'];
    							if($stat=='pending'){
    							  	echo '<div style="margin-top:10px; color: rgba(0,0,0,1); font-size:16px;"><strong>Reserve!</strong></div><br>';
    							}elseif($stat =='Confirmed'){
    								echo '<div style="margin-top:10px; color: rgba(0,0,0,1); font-size:16px;"><strong>Book!</strong></div><br>';
    							}else{
							  ?>
                            <div class="form-group">
			            	 <div class="row">
		            			<div class="col-xs-12 col-sm-12">
		            				<input type="submit" class="btn btn-primary btn-sm" name="btnbook" onclick="return validateBook();" value="Book Now!"/>
			           			 </div>
			            	 </div>
			              </div>
			              <?php }?>
                        </form>
                     
                    </div>
                  </div>
		  	  <?php 
               }
        	    }
        	 else{
		    ?>
		    <center>
		    <div class="card">
		        <label>No Room Found!</label>
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