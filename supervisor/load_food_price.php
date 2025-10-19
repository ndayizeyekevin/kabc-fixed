 <?php
  require_once ("../inc/config.php");
  $combo = $_REQUEST['combo'];
  
  $loadroom = $db->prepare("SELECT * FROM tbl_combo WHERE combo_id = '".$combo."' ");
  $loadroom->execute();
  if($loadroom->rowCount()> 0){
      $fetch_data = $loadroom->fetch();
      $price= $fetch_data['combo_price'];
      
      
  }
?>
 <div class="form-example-int form-horizental mg-t-15">
    <div class="form-group">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                <label class="hrzn-fm">Food Price</label>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                <div class="nk-int-st">
                    <input type="text" name="food_price" class="form-control" value="<?php echo $price; ?>" readonly >
                </div>
            </div>
        </div>
    </div>
    <div class="form-example-int form-horizental">
    <div class="form-group">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                <label class="hrzn-fm">Select Drink</label>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                <div class="nk-int-st">
                    <select name="drink" id="drink" class="form-control" onchange=AjaxFunction();>
                        <option value="">Select Drink</option>
                    <?php 
                    $stmt = $db->prepare("SELECT * FROM `tbl_drinks` ");
                    $stmt->execute();
                    while($fetch = $stmt->fetch()){
                    ?>
					<option value="<?php echo $fetch['drink_id']; ?>"><?php echo $fetch['drink_name']; ?></option>
					<?php } ?>
			        </select>
			        </div>
            </div>
        </div>
    </div>
</div>
<div name='display_res3' id='display_res3'></div>
    <div class="form-example-int mg-t-15">
    <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="breadcomb-report">
            <button type="submit" name="add_order" title="save reservation" class="btn"><i class="fa fa-upload"></i>  Submit</button>
            </div>
        </div>
    </div>
</div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#drink").change(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_drink_price.php?drink=' + $(this).val() , function (data) {
             $("#display_res3").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>