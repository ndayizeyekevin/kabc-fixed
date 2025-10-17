<?php
require_once ("../inc/config.php");
$sql = $db->prepare("SELECT * FROM tbl_rooms WHERE room_id = '".$_REQUEST['room']."' AND company_id = '".$company."'");
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);
$adlt = $row['Adults'];
$chld = $row['Children'];

?>
<label class="col-md-2 control-label" for=""><strong><small>Adults</small></strong><span class="text-danger">*</span></label>
 <div class="col-md-3">
    <input type="number" min="0" max="10" name="adults" class="form-control input-sm" value="<?php echo $adlt; ?>" readonly>
  </div>
<label class="col-md-2 control-label" for=""><strong><small>Children</small></strong></label>
 <div class="col-md-3">
  <input type="number" min="0" max="10" name="children" class="form-control input-sm" value="<?php echo $chld; ?>" readonly>
 </div>