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
  
  
 }
 ?>
 <style>

.zoom {      
-webkit-transition: all 0.35s ease-in-out;    
-moz-transition: all 0.35s ease-in-out;    
transition: all 0.35s ease-in-out;     
cursor: -webkit-zoom-in;      
cursor: -moz-zoom-in;      
cursor: zoom-in;  
}     

.zoom:hover,  
.zoom:active,   
.zoom:focus {
/**adjust scale to desired size, 
add browser prefixes**/
-ms-transform: scale(2.5);    
-moz-transform: scale(2.5);  
-webkit-transform: scale(2.5);  
-o-transform: scale(2.5);  
transform: scale(2.5);    
position:relative;      
z-index:100;  
}

 </style>
 <div class="container">
<div class="row">
<?php
 
 $stmt = $db->prepare('SELECT * FROM tbl_room_gallery ORDER BY img_id ASC');
 $stmt->execute();
 
 if($stmt->rowCount() > 0)
 {
  while($row=$stmt->fetch(PDO::FETCH_ASSOC))
  {
   extract($row);
   ?>
   <div class="col-xs-3">
    <img src="../room_gallery/<?php echo $row['img_name']; ?>" class="img-rounded zoom" width="250px" height="250px" />
    <p class="page-header">
    <span>
    <!--<a class="btn btn-info" href="editform.php?edit_id=<?php echo $row['img_id']; ?>" title="click for edit" onclick="return confirm('sure to edit ?')"><span class="glyphicon glyphicon-edit"></span> Edit</a> -->
    <a class="btn btn-danger" href="?resto=view&&delete_id=<?php echo $row['img_id']; ?>" title="click for delete" onclick="return confirm('sure to delete ?')"><span class="glyphicon glyphicon-remove-circle"></span> Delete</a>
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