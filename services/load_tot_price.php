 <?php
  require_once ("../inc/config.php");
  $drink = $_SESSION['drink'];
  
  $loadroom = $db->prepare("SELECT * FROM tbl_drinks WHERE drink_id = '".$_SESSION['drink']."' ");
  $loadroom->execute();
  if($loadroom->rowCount()> 0){
      $fetch_data = $loadroom->fetch();
      $drink_price= $fetch_data['drink_price'];
      $tot = $drink_price * $tot;
      
  }
?>

 <div class="form-example-int form-horizental mg-t-15">
    <div class="form-group">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                <label class="hrzn-fm">Drink Price</label>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                <div class="nk-int-st">
                    <input type="text" name="drink_price" class="form-control drink_price" value="<?php echo $drink_price; ?>" readonly >
                </div>
            </div>
        </div>
    </div>
</div>
