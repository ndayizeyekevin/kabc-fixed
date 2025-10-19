 <?php
  require_once ("../inc/config.php");
  $drink = $_REQUEST['drink'];
  
  $loadroom = $db->prepare("SELECT * FROM tbl_drinks WHERE drink_id = '".$drink."' ");
  $loadroom->execute();
  if($loadroom->rowCount()> 0){
      $fetch_data = $loadroom->fetch();
      $drink_price= $fetch_data['drink_price'];
      
      
  }
?>
<!--<div class="form-example-int form-horizental mg-t-15">-->
<!--    <div class="form-group">-->
<!--        <div class="row">-->
<!--            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">-->
<!--                <label class="hrzn-fm">Quantity</label>-->
<!--            </div>-->
<!--            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">-->
<!--                <div class="nk-int-st">-->
<!--                    <select name="quantity" id="quantity" class="form-control" onchange=AjaxFunction(); required>-->
<!--                    <option value="1">1</option>-->
<!--                    <option value="2">2</option>-->
<!--                    <option value="3">3</option>-->
<!--                    <option value="4">4</option>-->
<!--                    <option value="5">5</option>-->
<!--                    <option value="6">6</option>-->
<!--                    <option value="7">7</option>-->
<!--                    <option value="8">8</option>-->
<!--                    <option value="9">9</option>-->
<!--                    <option value="10">10</option>-->
<!--			        </select>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
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
<!--<div name='display_res4' id='display_res4'></div>-->
<!--<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>-->
<!--<script type="text/javascript">-->
<!--    $(document).ready(function () {-->

<!--        $("#quantity").change(function () {-->
<!--			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           -->
<!--            $.get('load_tot_price.php?quantity=' + $(this).val() , function (data) {-->
<!--             $("#display_res4").html(data);-->
<!--                $('#loader').slideUp(910, function () {-->
<!--                    $(this).remove();-->
<!--                });-->
<!--            });-->
<!--        });-->

<!--    });-->
<!--</script>-->