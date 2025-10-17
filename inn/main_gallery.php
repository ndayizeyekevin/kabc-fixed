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
        .preview_banner {
  			overflow: hidden;
  			width: 300px; 
  			height: 300px;
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
		.logo_banner{
		    display: inline-flex;
		   text-align: center;
		   margin-left: 20%;
		}
		.rounded{
		    height: 300px;
		}
		.card{
		    height: 300px;
		    margin-left: 7%;
		    top: 5%;
		     margin: 20px;
		}
		@media screen and (max-width:600px){
		    	.card{
		    height: 300px;
		    margin-left: 7%;
		    top: 5%;
		     margin: 20px;
		}
		   	.rounded{
		    height: auto;
		    margin-left: 2%;
		} 
		}
		
</style>

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
										<h2>Manage Photo Appearance</h2>
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

<div class="container">
 <div class="row logo_banner">
    <div class="card">
       <div class="panel panel-default">
         <div class="panel-heading">Update Image</div>
           <div class="panel-body">
            <div class="image_area">
            	<form action="" method="post">
            	    
    		        <?php
    		            $sql = $conn->prepare("SELECT cpny_bunner FROM `tbl_company` WHERE cpny_ID = '".$cpny_ID."' ");
                        $sql->execute(array()); 
                        $row = $sql->fetch(PDO::FETCH_ASSOC);
                        $img = $row['cpny_bunner'];
                        if ($img != "")
                        {
                         ?>
                         <label for="upload_image">
    		               <div class="text-center">
                         <img src="../bunnerCpny/<?php echo $img; ?>" id="uploaded_image" class="img-responsive rounded" />
                         	<div class="overlay">
            				<div class="text">Click to Update Image</div>
            			</div>
            		  <input type="file" name="image" class="image" id="upload_image" style="display:none" />
            		</div>
            		</label>
                         <?php
                        }
                         else{
                         ?>
                     <label for="upload_image">
    		          <div class="text-center">
                        <img src="../bunnerCpny/default-thumbnail.jpg" id="uploaded_image" class="img-responsive rounded" />
                         	<div class="overlay">
            				<div class="text">Click to add Image</div>
            			</div>
            			<input type="file" name="image" class="image" id="upload_image" style="display:none" />
            		   </div>
            		</label>
            		<?php
                         }
    		        ?>
    			
    		
            	</form>
              </div>
             </div>
            </div>
          </div>
     
      </div>
    </div>

<!-- modal for cropping logo-->
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
<!--end-->

<!-- script for updating image-->
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
					url:'upload_banner.php',
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

