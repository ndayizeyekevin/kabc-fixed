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
		      echo'<meta http-equiv="refresh"'.'content="2;URL=?ougami=galleries">';
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
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<a href="index?ougami=galleries" data-placement="left" title="Go Back" class="btn"><i class="fa fa-arrow-left"></i> Go Back</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- Breadcomb area End-->
             
 <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                                <div class="row">
                                    <?php
                                    
                                     $stmt = $db->prepare("SELECT * FROM tbl_room_gallery WHERE room_id= '".$_REQUEST['myId']."' ORDER BY img_id ASC");
                                     $stmt->execute();
                                     
                                     if($stmt->rowCount() > 0)
                                     {
                                      while($row=$stmt->fetch(PDO::FETCH_ASSOC))
                                      {
                                       extract($row);
                                       ?>
                                       <div class="column">
                                        <img src="../room_gallery/<?php echo $row['img_name']; ?>" class="img-rounded zoom">
                                        <p class="page-header">
                                        <span>
                                        <!--<a class="btn btn-info" href="editform.php?edit_id=<?php echo $row['img_id']; ?>" title="click for edit" onclick="return confirm('sure to edit ?')"><span class="glyphicon glyphicon-edit"></span> Edit</a> -->
                                        <a class="btn btn-danger" href="?ougami=galleries&&delete_id=<?php echo $row['img_id']; ?>" title="click for delete" onclick="return confirm('sure to remove ?')"><span class="glyphicon glyphicon-remove-circle"></span> Remove</a>
                                        </span>
                                        </p>
                                       </div>       
                                       <?php
                                      }
                                     }
                                     else
                                     {
                                      ?>
                                            <div class="col-xs-12">
                                             <div class="alert alert-warning">
                                                 <span class="glyphicon glyphicon-info-sign"></span> &nbsp; No Image Found ...
                                                </div>
                                            </div>
                                            <?php
                                     }
                                     
                                    ?>
                                         </div>
                            	</div>	
                             <div id="add" class="tab-pane fade in">
                                <!--<div class="container">-->
                                    
                                    </div>
                                <!--</div>-->
                        </div>
                    </div>
                </div>
            </div>
    