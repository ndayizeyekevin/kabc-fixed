<div class="col-sm-3">
  <div class="left-sidebar">
		
			  <h2>Reservation</h2>
				<div class="brands-name marg">
				  <form  method="POST" action="?page=details">
				    <div class="form-group">
            		 <div class="row">
            			<div class="col-xs-12 col-sm-12">
            			  <label class="control-label" for="from">Check In</label>
		                    <input type="text" onfocus=(type='date') class="form-control" value="<?php echo (isset($_REQUEST['from'])) ? $_REQUEST['from'] : ''; ?>" id="sd" name="from" placeholder="Arrival" required>
	              		</div>
	            	  </div>
		             </div>
		          
		            <div class="form-group">
	            	   <div class="row">
            			<div class="col-xs-12 col-sm-12">
            			  <label class="control-label" for="from">Check Out</label>
		                    <input type="text" onfocus=(type='date') class="form-control" value="<?php echo (isset($_REQUEST['to'])) ? $_REQUEST['to'] : ''; ?>"  name="to" id="ed" placeholder="Departure" required>
	              		 </div>
		            	</div>
		               </div>
		            
		            <div class="form-group">
		            	<div class="row">
	            			<div class="col-xs-12 col-sm-12">
	            				<label class="control-label" for="to">Adult</label>
		              			 <select class="form-control select2 checkDate" name="adult" required>
		              			 	<option>- Select -</option>
		              			 	<?php

		              			 		$sql = $conn->prepare("SELECT DISTINCT(Adults) FROM `tbl_rooms` ORDER BY Adults ASC");
		              			 		$sql->execute();
	              			 			while($cur = $sql->fetch()){ 
	              			 				echo '<option value='.$cur['Adults'].'>'.$cur['Adults'].'</option>';
	              			 			}
		              			 	?>
		              			 </select>
		              		</div>
		            	</div>
		            </div>
		            
		            <div class="form-group">
		            	<div class="row">
	            			<div class="col-xs-12 col-sm-12">
	            				<label class="control-label" for="to">Children</label>
		              			 <select name="children" class="form-control select2 checkDate" required>
		              			 		<option>- Select -</option>
		              			 	<?php

		              			 		$sql2 = $conn->prepare("SELECT DISTINCT(Children) FROM `tbl_rooms` ORDER BY Adults ASC");
		              			 		$sql2->execute();
	              			 			while($cur2 = $sql2->fetch()){ 
	              			 				echo '<option value='.$cur2['Children'].'>'.$cur2['Children'].'</option>';
	              			 			}
		              			 	?>
		              			 </select>
		              		</div>
		            	</div>
		            </div>
		            
		            <div class="form-group">
		            	<div class="row">
	            			<div class="col-xs-12 col-sm-12">
		           				 <button type="submit" class="btn btn-primary btn-show" style="border-radius: 4px;" align="right" name="avail">Check Availability</button>
		           			 </div>
		            	</div>
		            </div>
		           </form>
		            
				 </div>
		
			
			
		
		</div>
	</div>