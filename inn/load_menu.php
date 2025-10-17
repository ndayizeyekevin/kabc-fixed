   <?php 
  require_once ("../inc/config.php");
  $checkMenu = $_REQUEST['checkMenu'];
  ?>
  
  <div class="row">
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
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
      <div class="menu-box">
       <!-- Food title -->
        <div class="menu-box-head">
          <div class="pull-left">
              <?php echo $fetch['cat_name']." ".number_format($fetch['menu_price'])." Rwf"?></div>
          <div class="clearfix"></div>
        </div>
        <div class="menu-box-content referrer">
          <!-- Widget content -->
         <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover">
            <tbody>
            <tr>
              <td><?php echo $fetch['menu_name'];?></td>
            </tr>
          </tbody>
         </table>
         </div>
          <div class="menu-box-foot text-center">
              <input type="checkbox" name="menu_id[]" class="i-checks" value="<?php echo $menu_id;?>"><div class="pull-left" style="<?php echo $textStyle;?>"><?php echo $fetch['subcat_name'];?></div>
          </div>
        </div>
      </div>
   </div>
  <?php
   }
  ?>             
</div>

<div class="form-group">
    <div class="form-actions col-md-12">
        <br />
        <center>								
            <button type="submit" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-send"></i> Order Now!</button>
            <button type="reset" class="btn btn-sm label-secondary margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
        </center>
    </div>
</div>