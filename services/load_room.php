 <?php 
  require_once ("../inc/config.php");
  $rmID = $_REQUEST['rmID'];
 ?>
 
 <script type="text/javascript">
    $(document).ready(function () {

        $("#checkMenu").click(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_menu?checkMenu=' + $(this).val() , function (data) {
             $("#menuResult").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>
  <input type="text" hidden class="form-control" name="reservID" value="<?php echo $rmID; ?>">
    
  
<br>
         <a  id="checkMenu" name="checkMenu" style="border-radius: 4px;"><i class="fa fa-fw fa-check-circle-o"></i> Check Menu</a>

 
 

 