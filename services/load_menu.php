   <?php 
  require_once ("../inc/config.php");
  $checkMenu = $_REQUEST['checkMenu'];
  
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  
  ?>
  
  

  
    <div class="row">
                  
    <div class="col-lg-3" hidden>
      <?php $sql = $db->prepare("SELECT * FROM category");
		$sql->execute();
		while($fetch = $sql->fetch()){
		    
		    if($fetch['cat_id']==1 || $fetch['cat_id']==2  || $fetch['cat_id']==17 || $fetch['cat_id']==18 || $fetch['cat_id']==19){
		  ?>  <p class="btn" id="<?php echo $row['cat_id']?>" onclick="alert(this.id)"><?php echo $fetch['cat_name']?></p>
		  <hr>
		<?php }}
		  
		  ?>

                  
                  </div> 
                  
                     <div class="col-lg-12">




  
   
   




                
                     
    <div class="row">
        
        
               <div class="col-lg-4">
                      <div class="row">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
<?PHP


$i = 0;


		$sql = $db->prepare("SELECT menu_desc FROM `menu` where menu_desc !='' group by menu_desc");
		$sql->execute();
		while($fetch = $sql->fetch()){
		    
		    $i= $i + 1;
		    
		  ?>
		  
		  
		     <li class="nav-item" role="presentation"  style="margin:10px" >
        <a class="nav-link  clickable"   id="id<?php echo $i?>"  data-custom="<?php echo $fetch['menu_desc'];?>"   data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?php echo $fetch['menu_desc'];?> </a>
      </li>
		  
		  
	<?php
		    
		}



?>
    </ul>
</div></div>
        
        
          <div class="col-lg-8">
 

<input  class="form-control" type="text" id="searchInput" placeholder="Search for Menu / Drink..">
<br>
<div class="form-group">
    <div class="form-actions col-md-12">
        <center>								
            <button type="submit" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-send"></i> Order Now!</button>
            <button type="reset" class="btn btn-sm label-secondary margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
        </center>
    </div>
</div>
<br>
           <div class="table-responsive">
          <table  id="myTable" class="table table-striped table-bordered table-hover">
            <tbody>
    <?php
		$sql = $db->prepare("SELECT * FROM `menu`
		INNER JOIN category ON category.cat_id = menu.cat_id
		INNER JOIN subcategory ON subcategory.subcat_id = menu.subcat_ID
		ORDER BY menu.menu_name ASC");
		$sql->execute();
		while($fetch = $sql->fetch()){
		    $menu_id = $fetch['menu_id'];
		    if($fetch['subcat_id'] !=3){
		        $textStyle = "color: #43c76b;font-size:11px;";
		    }else{
		        $textStyle = "color: #43a0c7;font-size:11px;";
		    }
     	?>
   
      
            <tr>
              <td> <input type="checkbox" name="menu_id[]" class="i-checks" value="<?php echo $menu_id;?>"> <?php echo $fetch['menu_name'];?>    <?php echo number_format($fetch['menu_price'])." RWF"?>  <br>
              <?php echo $fetch['menu_desc']?><br>
           
              </td>
            </tr>
            
    
  <?php
   }
  ?> 
  
        </tbody>
         </table>
         </div>
         
</div>

		  
                     
                     
                  
                  </div> 
                  
                  
                  
                  
                  </div>
<script>

$('.clickable').click(function() {
//  alert('Clicked item ID: ' + $(this).attr('id')); 
  
  
var div = document.getElementById($(this).attr('id'));
var value = div.dataset.custom;  // "someValue"



  var input = value.toLowerCase();
  var rows = document.querySelectorAll("#myTable tr");

  rows.forEach(function(row, index) {
    //if (index === 0) return; // Skip header row

    var cells = row.getElementsByTagName("td");
    var match = false;

    for (var i = 0; i < cells.length; i++) {
      if (cells[i].innerText.toLowerCase().indexOf(input) > -1) {
        match = true;
        break;
      }
    }

    row.style.display = match ? "" : "none";
  });
  
  
});



document.getElementById("searchInput").addEventListener("keyup", function() {
    
   // alert("d");
  var input = this.value.toLowerCase();
  var rows = document.querySelectorAll("#myTable tr");

  rows.forEach(function(row, index) {
    //if (index === 0) return; // Skip header row

    var cells = row.getElementsByTagName("td");
    var match = false;

    for (var i = 0; i < cells.length; i++) {
      if (cells[i].innerText.toLowerCase().indexOf(input) > -1) {
        match = true;
        break;
      }
    }

    row.style.display = match ? "" : "none";
  });
});
</script>

