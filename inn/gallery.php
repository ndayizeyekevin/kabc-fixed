 <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
 <link rel="stylesheet" href="https://unpkg.com/dropzone/dist/dropzone.css" />
<link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
<script src="https://unpkg.com/dropzone"></script>
<script src="https://unpkg.com/cropperjs"></script>
 <style>

		.image_area {
		  position: relative;
		}

		img {
		  	display: block;
		  	max-width: 100%;
		}

		.preview {
  			overflow: hidden;
  			width: 200px; 
  			height: 200px;
  			margin: 10px;
  			border: 1px solid red;
		}

		.modal-lg{
  			max-width: 1000px; !important;
		}

		.overlay {
		  position: absolute;
		  bottom: 10px;
		  left: 0;
		  right: 0;
		  background-color: rgba(255, 255, 255, 0.5);
		  overflow: hidden;
		  height: 0;
		  transition: .5s ease;
		  width: 100%;
		}

		.image_area:hover .overlay {
		  height: 50%;
		  cursor: pointer;
		}

		.text {
		  color: #333;
		  font-size: 20px;
		  position: absolute;
		  top: 50%;
		  left: 50%;
		  -webkit-transform: translate(-50%, -50%);
		  -ms-transform: translate(-50%, -50%);
		  transform: translate(-50%, -50%);
		  text-align: center;
		}
		.column {
          float: left;
          padding:5px;
          margin: 5px;
          width: 31.33%;
        }
        
        /* Clear floats after the columns */
        .row:after {
          content: "";
          display: table;
          clear: both;
        }
		

</style>
		
		<?php
            if(isset($_GET['delete_id']))
             {
              // select image from db to delete
              $stmt_select = $db->prepare('SELECT * FROM tbl_room_gallery WHERE img_id =:uid');
              $stmt_select->execute(array(':uid'=>$_GET['delete_id']));
              $imgRow=$stmt_select->fetch(PDO::FETCH_ASSOC);
              unlink("user_images/".$imgRow['img_name']);
              
              // it will delete an actual record from db
              $stmt_delete = $db->prepare('DELETE FROM tbl_room_gallery WHERE img_id =:uid');
              $stmt_delete->bindParam(':uid',$_GET['delete_id']);
              $stmt_delete->execute();
              
              $msg = "Deleted Successfully!";
		      echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=galleries">';
             }
             ?>
             
    <!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
		    
		    <?php if($msg){?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
              </div>
            <?php } 
             else if($msge){?>
                 
             <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
              </div>
            <?php } ?>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="fa fa-cog"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Manage Room Gallery</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- Breadcomb area End-->
             
     <div class="tabs-info-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="widget-tabs-int">
                       
                        <div class="widget-tabs-list">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#home">Add New Image</a></li>
                                <li><a data-toggle="tab" href="#menu1">Manage Gallery</a></li>
                            </ul>
                            <div class="tab-content tab-custom-st">
                                <div id="home" class="tab-pane fade in active">
                                    <div class="tab-ctn">
                                        
                                        <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-2 col-form-label">Select Room</label>
                                            <div class="col-sm-4">
                                              <select id="room" name="room" class="form-control" style="width:100%" required>
                                                     <option>Select Room Type</option>
                                                         <?php
                                                             $sql = $db->query('SELECT * FROM tbl_rooms');
                                
                                                                    try
                                                                    {
                                                                        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>
                                                                                <option value="<?php echo $row['room_id']; ?>"><?php echo $row['room_no']; ?></option>
                                                                            <?php
                                                                             }
                                                                        }
                                                                        catch (PDOException $ex) {
                                                                        //Something went wrong rollback!
                                                                        echo $ex->getMessage();
                                                                }
                                                          ?>	
                                                </select>
                                            </div>
                                         </div>
                                         <br><br>
                                         <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-2 col-form-label">Click On Image To Uplaod New</label>
                                            <div class="col-sm-4">
                                                <div class="image_area">
                            						<form method="post">
                            							<label for="upload_image">
                            								<img src="../room_gallery/1611573578.png" id="uploaded_image" class="img-responsive img-thumbnail" />
                            								<div class="overlay">
                            									<div class="text">Click to Upload New Image</div>
                            								</div>
                            								<input type="file" name="image" class="image" id="upload_image" style="display:none" />
                            							</label>
                            						</form>
                            					</div>
                                            </div>
                                        </div>
                                        
                            	</div>	
                                    </div>
                                
                                <div id="menu1" class="tab-pane fade">
                                    <div class="tab-ctn">
                                        <form action="view_all.php" method="POST">
                                        <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Room No</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                		$sql = $conn->prepare("SELECT * FROM `tbl_rooms`");
                                		$sql->execute();
                                		while($fetch = $sql->fetch()){
                                		    $room_id = $fetch['room_id'];
                                     	?>
                        <tr >
                            <td><?php 
                                $cnt = $db->prepare("SELECT COUNT(*) AS num FROM `tbl_room_gallery` WHERE room_id=$room_id AND rm_gallery_cpny_id=$cpny_ID ");
                                $cnt->execute(array());
                                $rowcnt = $cnt->fetchAll();
                            echo $fetch['room_no']."<span class='badge badge-danger'>".($rowcnt['num'])."</span>"?></td>
                            <td>
                                <?php 
                                
                                $stmt = $db->prepare("SELECT * FROM `tbl_room_gallery` WHERE room_id=$room_id AND rm_gallery_cpny_id=$cpny_ID ORDER BY img_id DESC LIMIT 1 ");
                                $stmt->execute(array());
                                $row = $stmt->fetchAll();
                                $rowcount = $stmt->rowCount();
                                foreach($row as $row1)
                                {
                                    ?>
                                <img src="../room_gallery/<?php echo $row1['img_name']?>" class="image-responsive rounded" style="width:150px;">;
                                <?php
                                }
                                ?>
                            </td>
                            
                            <td>
                                 <a class="btn-sm" href="?resto=view&&myId=<?php echo $room_id; ?>" class="btn btn-default">View All</a>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Room No</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                                     </form>    
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
 
    <!-- modal for cropping images-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Crop Image Before Upload</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">×</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
	        		<div class="img-container">
	            		<div class="row">
	                		<div class="col-md-8">
	                    		<img src="" id="sample_image" />
	                		</div>
	                		<div class="col-md-4">
	                    		<div class="preview"></div>
	                		</div>
	            		</div>
	        		</div>
	      		</div>
	      		<div class="modal-footer">
	      			<button type="button" id="crop" class="btn btn-primary">Crop</button>
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
	      		</div>
	    	</div>
	  	</div>
	</div>
</div>


    <script>

$(document).ready(function(){

	var $modal = $('#modal');

	var image = document.getElementById('sample_image');

	var cropper;

	$('#upload_image').change(function(event){
		var files = event.target.files;

		var done = function(url){
			image.src = url;
			$modal.modal('show');
		};

		if(files && files.length > 0)
		{
			reader = new FileReader();
			reader.onload = function(event)
			{
				done(reader.result);
			};
			reader.readAsDataURL(files[0]);
		}
	});

	$modal.on('shown.bs.modal', function() {
		cropper = new Cropper(image, {
			aspectRatio: 1,
			viewMode: 3,
			preview:'.preview'
		});
	}).on('hidden.bs.modal', function(){
		cropper.destroy();
   		cropper = null;
	});

	$('#crop').click(function(){
	    var room = $("#room").val();
		canvas = cropper.getCroppedCanvas({
			width:300,
			height:300
		});

		canvas.toBlob(function(blob){
			url = URL.createObjectURL(blob);
			var reader = new FileReader();
			reader.readAsDataURL(blob);
			reader.onloadend = function(){
				var base64data = reader.result;
				var data = "id="+ room;
				$.ajax({
					url:'upload.php',
					method:'POST',
					data:{
					    image:base64data,
					    room:room
					},
					success:function(data)
					{
						$modal.modal('hide');
						$('#uploaded_image').attr('src', data);
					}
				});
			};
		});
	});
	
});
</script>