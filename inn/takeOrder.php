 <?php
 if(!empty($_POST['menu_id'])){
     try{
         function productcode() {
        return getReqCode($_SESSION['user_id']);
    }
    $Order_code=productcode();
     $reservID = $_POST['reservID'];
     $menu_id = $_POST['menu_id'];
     $today = date("Y-m-d H:i:s");
     $sts = 3;
     
      
          
         $sql = $db->prepare("SELECT * FROM tbl_cmd WHERE reservat_id = '".$reservID."' AND OrderCode = '$Order_code' AND status_id != '12'");
         $sql->execute();
         $count = $sql->rowCount();
         if($count > 0){
             $get = $sql->fetch();
             $ordcode = $get['OrderCode'];

             $order = $conn->prepare("INSERT INTO `tbl_cmd`(`reservat_id`,`Serv_id`, `status_id`,`company_id`, `dTrm`,`OrderCode`) 
            VALUES('$reservID','$menu_id[$i]','$Oug_UserID','$sts','$cpny_ID','$today','$ordcode')");
            $order->execute();
             
             for($i=0; $i<count($menu_id); $i++){
         
         $sql = $db->prepare("INSERT INTO `tbl_cmd_qty`(,`Serv_id`, `cmd_table_id`,`cmd_item`, `cmd_qty`, `cmd_code`, `cmd_status`) 
         VALUES ('$Oug_UserID','$reservID','$menu_id[$i]','1','$ordcode','$sts')");
         $sql->execute();
         
         $msg = "Successfully Ordered!";
         echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
      } 
         }
         else{
             $order = $conn->prepare("INSERT INTO `tbl_cmd`(`reservat_id`,`Serv_id`, `status_id`,`company_id`, `dTrm`,`OrderCode`) 
            VALUES('$reservID','$Oug_UserID','$sts','$cpny_ID','$today','$Order_code')");
            $order->execute();
             for($i=0; $i<count($menu_id); $i++){
         
         $sql = $db->prepare("INSERT INTO `tbl_cmd_qty`(`Serv_id`, `cmd_table_id`,`cmd_item`, `cmd_qty`, `cmd_code`, `cmd_status`) 
         VALUES ('$Oug_UserID','$reservID','$menu_id[$i]','1','$Order_code','$sts')");
         $sql->execute();
         
         $msg = "Successfully Ordered!";
         echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
      } 
         }
     
         
     }
        catch (PDOException $ex) {
        //Something went wrong rollback!
        echo $ex->getMessage();
    }                          
  }
 ?>
  
<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?php if($msg){?>
                    <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
                  </div>
                <?php } 
                  else if($msge){?>
                     
                 <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Oooh Snap!</strong> <?php echo htmlentities($msge); ?>
                  </div>
                 <?php
                  }else if($snp){
                ?>
                 
                 <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Sorry!</strong> Access Denied!<?php echo htmlentities($snp); ?>
                  </div>
                <?php } ?>
            <div class="form-example-wrap mg-t-30">
            <form action="" method="POST">
                 <div class="row">
                   <label class="col-md-2 control-label" for=""><strong>Select Table </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <select name="rmID" id="rmID" class="form-control chosen" data-live-search="true" onchange=AjaxFunction(); required placeholder="Choose One">
                         <option></option>
                            <?php 
                            $stmt = $db->prepare("SELECT * FROM tbl_tables 
                            WHERE status = '1' AND table_id NOT IN(SELECT reservat_id FROM tbl_cmd WHERE status_id != '12')
                            ORDER BY table_id ASC");
                            $stmt->execute();
                            while($fetch = $stmt->fetch()){
                            ?>
    						<option value="<?php echo $fetch['table_id']; ?>"><?php echo $fetch['table_no']; ?></option>
    						<?php 
    						 }
    						?>
    				     </select>
                    </div>
                
                      <div name='display_res' id='display_res'>
                          
                      </div>
                   </div> <!-- /Row -->
                   
                   <br>
                   <div name="menuResult" id="menuResult">
     
                  </div>
                 <br>
                     
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#rmID").change(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_room?rmID=' + $(this).val() , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>