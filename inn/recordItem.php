<?php
include('auth.php');
include('link.php');
include('links.php');
include('rra.php');

 ?>

<?php 
$itemCd = '';

if(isset($_POST['tin'])){
  extract($_POST);

$stmt = $db->prepare("SELECT itemCd FROM tbl_vsdc_items ORDER BY itemId DESC LIMIT 1 ");
$stmt->execute();
$count = $stmt->rowCount();
if($count > 0){

    $row = $stmt->fetch();
    $curCd = $row['itemCd'];

    $cut = substr($curCd,7);

    $next = str_pad(intval($cut) + 1, strlen($cut), '0', STR_PAD_LEFT);

    $itemCd = $orgnNatCd.''.$itemTyCd.''.$pkgUnitCd.''.$qtyUnitCd.''.$next;


}else{
    $itemCd = $orgnNatCd.''.$itemTyCd.''.$pkgUnitCd.''.$qtyUnitCd.'0000001';
}
$arr = array(
  "itemCd" => $itemCd
);

$ab = json_encode(array_merge($_POST,$arr));

  print_r(rra_function($ab, 'items/saveItems'));

  extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			$v = htmlentities(str_replace("'","/",$v));
				
      if(empty($data)){
        			$data .= " $k='$v' ";
        		}else{
        			$data .= ", $k='$v' ";
        		}
			}
      $data .= ", itemCd='".$itemCd."'"; 
      
      $stmt = $db->prepare("SELECT * FROM tbl_vsdc_items WHERE itemCd = '$itemCd'");
      $stmt->execute();
      $count = $stmt->rowCount();
      if($count > 0){

        $save = $db->prepare("UPDATE tbl_vsdc_items set $data WHERE itemCd = '$itemCd'");
        $save->execute();

      }else{
        $save = $db->prepare("INSERT INTO tbl_vsdc_items set $data");
			$save->execute();

      }
    
}

?>
<body>
    <?php include('navfixed.php');?>

    <!-- Page Content -->
    <div id="page-wrapper">
    <br>
    <div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
            <p>Sales recorded successfully!</p>
          </div>
          <div class="modal-footer">
            <a href="of_sales" class="btn btn-default">Ok</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
        <div class="container-fluid">
            <div class="row">
            <div class="col-lg-12">
                    <h3 class="page-header"><b>Record New Item</b></h3>
                </div>
                <div class="panel-body">
                   <form method="POST" id="manage-items">
                      <div class="col-lg-12">
                      <div id="msg"></div>
                            <div class="col-lg-4">
                                <label>Item Name</label>
                                <input type="text" name="itemNm" autocomplete="off" class="form-control" required>
                                <input type="hidden" name="tin" value="999900823" class="form-control">
                                <input type="hidden" name="bhfId" value="00" class="form-control">
                                <input type="hidden" name="itemStdNm" class="form-control">
                                <input type="hidden" name="btchNo" class="form-control">
                                <input type="hidden" name="bcd" class="form-control">
                                <input type="hidden" name="orgnNatCd" value="RW" class="form-control">
                                <input type="hidden" name="pkgUnitCd" value="HH" class="form-control">
                                <input type="hidden" name="qtyUnitCd" value="BG" class="form-control">
                                <input type="hidden" name="grpPrcL1" value="0" class="form-control">
                                <input type="hidden" name="grpPrcL2" value="0" class="form-control">
                                <input type="hidden" name="grpPrcL3" value="0" class="form-control">
                                <input type="hidden" name="grpPrcL4" value="0" class="form-control">
                                <input type="hidden" name="grpPrcL5" value="0" class="form-control">
                                <input type="hidden" name="addInfo" value="" class="form-control">
                                <input type="hidden" name="sftyQty" class="form-control">
                                <input type="hidden" name="isrcAplcbYn" value="N" class="form-control">
                                <input type="hidden" name="useYn" value="Y" class="form-control">
                                <input type="hidden" name="regrNm" value="TESTING COMPANY 19 LTD" class="form-control">
                                <input type="hidden" name="regrId" value="01" class="form-control">
                                <input type="hidden" name="modrNm" value="TESTING COMPANY 19 LTD" class="form-control">
                                <input type="hidden" name="modrId" value="01" class="form-control">
                            </div>
                            <div class="col-lg-4">
                                <label>Item Classification Code</label>
                                <input type="text" name="itemClsCd" value="86101500" autocomplete="off" class="form-control" required>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Item Type</label>
                                <select name="itemTyCd" class="form-control selectpicker" data-live-search="true" required>
                                    <option value=""></option>
                                    <option value="1">Raw Material</option>
                                    <option value="2">Finished Product</option>
                                    <option value="3">Service without stock</option>
                                </select>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Unit Price</label>
                                <input type="number" step="any" name="dftPrc" autocomplete="off" class="form-control" required>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Tax Type</label>
                                <select name="taxTyCd" class="form-control selectpicker" data-live-search="true" required>
                                    <option value=""></option>
                                    <option value="A">A-EX</option>
                                    <option value="B">B-18.00% </option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            </div>
                            <br>
                            <center><button type="submit" class="btn btn-success">Save</button>  
                            <a href="allItems" class="btn btn-default"><i class="fa fa-backward"></i> Back</a></center>
                        </form>

            <div class="clearfix"></div>
        </div>
        <script src="js/jquery.js"></script>
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<?php include('scripts.php'); ?>

<script>
	$(document).ready(function(){
		$('#manage-items1').submit(function(e){
      //return false;
			e.preventDefault();
			$('#msg').html('')
			$.ajax({
				url:'ajax.php?action=save_items',
				method:'POST',
				data:$(this).serialize(),
				success:function(resp){
          console.log(resp);
					if(resp == 1){
						$('#msg').html('<div class="alert alert-success"><i class="fa fa-thumbs-up"></i> Item recorded successfully.</div>')
						setTimeout(function(){
							location.reload()	
						},1750)
					}else if(resp == 2){
						$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Department already exist.</div>')
						end_load()
					}
				}
			})
		})
	})

</script>