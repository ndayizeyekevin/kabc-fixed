
 <?php
if (isset($_POST['item'])) {
    
    $data = $_POST;

    $dataArray = array('item','spplrItemClsCd');
    $j = 0;
    for($i = 0; $i < count($_POST['item']); $i++){
    
    

    $sts = $db->prepare("SELECT itemCd,itemClsCd,itemNm,bcd,pkgUnitCd,qtyUnitCd,dftPrc FROM tbl_vsdc_items WHERE itemCd='".$_POST['item'][$i]."'");
    $sts->execute();
    $count = $sts->rowCount();
    unset($data["item"]);
    $itemSeq=1;
    $pkg=1;
    $row = $sts->fetch(PDO::FETCH_ASSOC);
        $row['pkg'] = "$pkg";
        $row['itemSeq'] = "$itemSeq";
        $row['qty'] =$_POST['qty'][$i];
        $row['prc'] =$_POST['prc'][$i];
        $row['spplrItemClsCd'] = $_POST['spplrItemClsCd'][$i];
        $row['spplrItemCd'] = $_POST['spplrItemCd'][$i];
        $row['spplrItemNm'] = $_POST['spplrItemNm'][$i];
        $row['splyAmt'] = $_POST['splyAmt'][$i];
        $row['dcRt'] = $_POST['dcRt'][$i];
        $row['dcAmt'] = $_POST['dcAmt'][$i];
        $row['taxblAmt'] = $_POST['taxblAmt'][$i];
        $row['taxTyCd'] = $_POST['taxTyCd'][$i];
        $row['taxAmt'] = $_POST['taxAmt'];
        $row['totAmt'] = $_POST['totAmt'];
        $row['itemExprDt'] = $_POST['itemExprDt'][$i];
        $data['itemList'][] = $row;

        // print_r(json_encode($row));
    
    }

    $jsonData = json_encode($data);
   print_r($jsonData);
  print_r(rra_function($jsonData, 'trnsPurchase/savePurchases'));

}
 ?>

<?php 
function fill_product($db){
  $output= '';

  $sts = $db->prepare("SELECT * FROM tbl_vsdc_items");
                                    $sts->execute();
  $result = $sts->fetchAll();

  foreach($result as $rowd){
    $output.='<option value="'.$rowd['itemCd'].'">'.$rowd['itemNm'].'</option>';
  }

  return $output;
}
?>
	<div class="breadcomb-area">
		<div class="container">
<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">APTC</h4>
      </div>
       <div class="modal-body">
        <p>Inserted Successfully!</p>
      </div>
      <div class="modal-footer">
        <a href="agroRegister" class="btn btn-default">Ok</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <!-- Page Content -->
    <div id="page-wrapper">
    <br>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Purchase registration</h1>
                </div>
                <form method="POST" action="">
                <div class="panel-body">
                        <div class="row">

                        <div class="col-lg-2">
                            <label for="inputAddress">Origin Invoice Number</label>
                            <input type="hidden" name="tin" value="999900823" class="form-control">
                            <input type="hidden" name="bhfId" value="00" class="form-control">
                            <input type="hidden" name="invcNo" value="1" class="form-control" autocomplete="off">
                            <input type="hidden" name="orgInvcNo" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="spplrTin" class="form-control" autocomplete="off">
                            <input type="hidden" name="spplrBhfId" class="form-control" autocomplete="off">
                            <input type="hidden" name="spplrNm" class="form-control" autocomplete="off">
                            <input type="hidden" name="spplrInvcNo" class="form-control" autocomplete="off">
                            <input type="hidden" name="totItemCnt" value="1" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxblAmtA" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxblAmtB" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxblAmtC" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxblAmtD" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxRtA" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxRtB" value="18" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxRtC" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxRtD" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxAmtA" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxAmtB" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxAmtC" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxAmtD" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="totTaxblAmt" value="550" class="form-control" autocomplete="off">
                            <input type="hidden" name="totTaxAmt" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="totAmt" value="550" class="form-control" autocomplete="off">
                            <input type="hidden" name="spplrItemCd" value="550" class="form-control" autocomplete="off">

                            <input type="hidden" name="prc" value="550" class="form-control" autocomplete="off">
                            <input type="hidden" name="splyAmt" value="550" class="form-control" autocomplete="off">
                            <input type="hidden" name="dcRt" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="dcAmt" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxblAmt" value="550" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxTyCd" value="A" class="form-control" autocomplete="off">
                            <input type="hidden" name="taxAmt" value="0" class="form-control" autocomplete="off">
                            <input type="hidden" name="itemExprDt" class="form-control" autocomplete="off">
                            <input type="hidden" name="cfmDt" class="form-control" autocomplete="off">
                            <input type="hidden" name="wrhsDt" class="form-control" autocomplete="off">
                            <input type="hidden" name="cnclReqDt" class="form-control" autocomplete="off">
                            <input type="hidden" name="cnclDt" class="form-control" autocomplete="off">
                            <input type="hidden" name="rfdDt" class="form-control" autocomplete="off">
                            <input type="hidden" name="remark" class="form-control" autocomplete="off">
                            <input type="hidden" name="pchsDt" value="<?php echo date("Ymd"); ?>" class="form-control" autocomplete="off">
                            <input type="hidden" name="regrNm" value="TESTING COMPANY 19 LTD" class="form-control">
                            <input type="hidden" name="regrId" value="01" class="form-control">
                            <input type="hidden" name="modrNm" value="TESTING COMPANY 19 LTD" class="form-control">
                            <input type="hidden" name="modrId" value="01" class="form-control">
                        </div>
                        <div class="col-lg-2">
                            <label for="inputAddress">Registration Type Code</label>
                            <input type="text" name="regTyCd" value="M" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-lg-2">
                            <label for="inputAddress">Purchase Type Code</label>
                            <input type="text" name="pchsTyCd" value="N" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-lg-2">
                            <label for="inputAddress">Receipt Type Code</label>
                            <input type="text" name="rcptTyCd" value="P" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-lg-2">
                            <label for="inputAddress">Payment Type Code</label>
                            <input type="text" name="pmtTyCd" value="01" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-lg-2">
                            <label for="inputAddress">Purchase Status Code</label>
                            <input type="text" name="pchsSttsCd" value="02" class="form-control" autocomplete="off">
                        </div>
                        </div>
                         <div class="row">
                         <div class="form-group col-md-12">
                            <table class="table table-border" id="myOrder">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>U.Price</th>
                                        <th>Tax Rate</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>
                                            <button type="button" name="addOrder" class="btn btn-success btn-sm btn_addOrder" required>
                                                <span>
                                                    <i class="fa fa-plus"></i>
                                                </span>
                                            </button>
                                        </th>
                                    </tr>

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                         </div>
                         <div id="btn"></div>
            
        </div>
        </form>
        <script src="js/jquery.js"></script>
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script type="text/javascript">
    $(document).ready(function () {
        $("#type").change(function () {
            var type = $('#type').val();
			$(this).after('<div id="loader"><img src="../images/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
   
             $.post("load_details.php", { type:type}, function(data){
                 //alert(type);
                 $("#display_results").html(data);
                 $('.selectpicker').selectpicker();
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
				});
        });
    })

      $(document).ready(function(){
    $('#select_all').on('click',function(){
        if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;
            });
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
        }
    });
    
    $('.checkbox').on('click',function(){
        if($('.checkbox:checked').length == $('.checkbox').length){
            $('#select_all').prop('checked',true);
        }else{
            $('#select_all').prop('checked',false);
        }
    });
});
</script>
<script>
 $(function() {
  $('.selectpicker').selectpicker();
});
</script>


<script>
     function myFunction() {
         window.print();
     }
  </script>
<script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });
</script>

<script>
 $(document).ready(function(){
      $(document).on('click','.btn_addOrder', function(){
        var html='';
        html+='<tr>';
        html+=`<td><select class="form-control productid" name="item[]" required>
        <option value="">--Select Product--</option><?php echo fill_product($db)?></select></td>`;
        html+='<td><input type="number" class="form-control price" required>\
        <input type="hidden" class="form-control rate">\
        <input type="hidden" class="form-control splyAmt" name="splyAmt[]" required>\
        <input type="hidden" name="dcRt[]" class="form-control dcRt">\
        <input type="hidden" name="dcAmt[]" class="form-control dcAmt">\
        <input type="hidden" name="taxblAmt[]" class="form-control taxblAmt">\
        <input type="hidden" name="itemExprDt[]" class="form-control">\
        <input type="hidden" class="form-control spplrItemClsCd" name="spplrItemClsCd[]">\
        <input type="hidden" class="form-control spplrItemNm" name="spplrItemNm[]">\
        <input type="hidden" class="form-control totAmt" name="totAmt[]">\
        <input type="hidden" class="form-control spplrItemCd" name="spplrItemCd[]">\
        <input type="hidden" class="form-control pkg" name="pkg[]"></td>';
        html+='<td><input type="text" class="form-control taxTyCd"" required readonly>';
        html+='<td><input type="number" class="form-control quantity" name="qty[]" required autocomplete="off">';
        html+='<td><input type="number" class="form-control tot" readonly>';
        html+='<td><button type="button" name="remove" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-remove"></i></button></td>'
        $('#myOrder').append(html);
        
        $('.productid').on('change', function(e){
          var productid = this.value;
          var tr=$(this).parent().parent();
          $.ajax({
            url:"getcisitems.php",
            method:"GET",
            data:{id:productid},
            success:function(data){
              $('#btn').html('<button type="submit" class="btn btn-primary pull-right" name="add">Add</button>');
                tr.find(".quantity").val(0);
                tr.find(".rate").val(data["tax_value"]);
                tr.find(".taxTyCd").val(data["taxTyCd"]);
                tr.find(".price").val(data["dftPrc"]);
                tr.find(".itemClsCd").val(data["itemClsCd"]);
                tr.find(".itemNm").val(data["itemNm"]);
            }
          })
        })
      })

      $(document).on('click','.btn-remove', function(){
        $(this).closest('tr').remove();
      })

    //  $("#myOrder").delegate(".quantity","keyup change", function(){
    //     var quantity = $(this);
    //     var tr=$(this).parent().parent();
    //     if((quantity.val()-0)>(tr.find(".stock").val()-0)){
    //       alert("Sorry! quantity exceeds maximum");
    //       quantity.val(0);
    //     }
    //     else{
    //       tr.find(".qty").val(tr.find(".stock").val()-quantity.val());
    //       calculate(0,0);  
    //     }
    //   })
      
      $("#myOrder").delegate(".quantity","keyup change", function(){
        var quantity = $(this);
        var tr=$(this).parent().parent();
        
        tr.find(".splyAmt").val(quantity.val() * tr.find(".price").val());
        tr.find(".taxblAmt").val(quantity.val() * tr.find(".price").val());
        tr.find(".totAmt").val(quantity.val() * tr.find(".price").val());
        tr.find(".tot").val(quantity.val() * tr.find(".price").val());
        tr.find(".pkg").val(quantity.val());
        calculate(0,0);
      })
    });
  </script>