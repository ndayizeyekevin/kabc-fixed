
 <?php


 $count = 0; 
if (isset($_POST['save'])) {
 
  
   
$tin = $_POST['tinnumber'];
$client_name = $_POST['client_name'];
$totalamount = $_POST['totalamount'];

$purchase_code= $_POST['purchasecode'];
$salestype= $_POST['salestype'];
$rectype= $_POST['rectype'];
$productList = $_POST['productList'];
$curl = curl_init();
// $prdct = $productList;
$prdct = '[{"itemSeq":1,"itemCd":"RW1NTXU0000011","itemClsCd":"5059690800","itemNm":"Somen","bcd":null,"pkgUnitCd":"HH","pkg":1,"qtyUnitCd":"BG","qty":1,"prc":1000,"splyAmt":1000,"dcRt":0,"dcAmt":0,"isrccCd":null,"isrccNm":null,"isrcRt":null,"isrcAmt":null,"taxTyCd":"B","taxblAmt":0,"taxAmt":0,"totAmt":1000}]';
//$prdct  = json_decode($prdct);
$json =  '{"tin":"999900823",
  "bhfId":"00",
  "invcNo":"'.$lastID.'",
  "orgInvcNo":0,
  "custTin":"'.$tin.'",
  "prcOrdCd":"'.$purchase_code.'",
  "custNm":"'.$client_name.'",
  "salesTyCd":"'.$salestype.'",
  "rcptTyCd":"'.$rectype.'",
  "pmtTyCd":"01",
  "salesSttsCd":"02",
  "cfmDt":"20230825120300",
  "salesDt":"20230824",
  "stockRlsDt":"20230825120300",
  "cnclReqDt":null,
  "cnclDt" :null,
  "rfdDt":null,
  "rfdRsnCd":null,
  "totItemCnt":1,
  "taxblAmtA":0,
  "taxblAmtB":0,
  "taxblAmtC":0,
  "taxblAmtD":0,
  "taxRtA":0,
  "taxRtB":0,
  "taxRtC":0,
  "taxRtD":0,
  "taxAmtA":0,
  "taxAmtB":0,
  "taxAmtC":0,
  "taxAmtD":0,
  "totTaxblAmt":0,
  "totTaxAmt":0,
  "totAmt":"'.$totalamount.'",
  "prchrAcptcYn":"N",
  "remark":null,
  "regrId":"01",
  "regrNm":"admin",
  "modrId":"01",
  "modrNm":"admin",
  "receipt":{"custTin":"'.$tin.'","custMblNo":null,"rptNo":1,"trdeNm":"","adrs":"","topMsg":"Shopwithus","btmMsg":"Welcome","prchrAcptcYn":"N"},
  "itemList":'.$prdct.'
}';



echo $json;






$url = getenv('VSDC_URL');

curl_setopt_array($curl, array(
  CURLOPT_URL => $url .'/trnsSales/saveSales',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$json,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

$data = json_decode($response);
$code = $data->resultCd;
if($code=='000'){

  $res = json_encode($data->data);
  $res = json_decode($res);
  
  $rcptNo =  $res->rcptNo;
  $totRcptNo=  $res->totRcptNo;
  $vsdcRcptPbctDate=  $res->vsdcRcptPbctDate;
  $sdcId=  $res->sdcId;
  $mrcNo=  $res->mrcNo;
  $int=  $res->intrlData;
  $rcptSign= $res->rcptSign;

  $amount= 100;


  $lin = 'no='.$rcptNo.'&total='
  .$totRcptNo.'&vs='.$vsdcRcptPbctDate.'&sdc='. $sdcId.'&mrc='.$mrcNo.'&amount='.$amount.'&product='.$prdct.'&int='.$int.'&sign='. $rcptSign;
  $salestype = 'T';
  if($salestype=='T'){
echo "<script>window.location='receipt/training.php?$lin'</script>";
echo $lin;
  }


}else{
  
  echo "<script>alert('$data->resultMsg')</script>";
  
}



    
    echo "<script>
        $(document).ready(function(){
        $('#modal3').modal('show'); 
        });
        </script>";
    }
    
// }

 ?>

	<div class="breadcomb-area">
		<div class="container">

    <div id="page-wrapper">
    <br>
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
            <p>Sorry! Amount is greater than balance!</p>
          </div>
          <div class="modal-footer">
            <a href="of_sales" class="btn btn-default">Ok</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">APTC</h4>
          </div>
           <div class="modal-body">
            <p>Sorry! Quantity is greater than stock balance!</p>
          </div>
          <div class="modal-footer">
            <a href="of_sales" class="btn btn-default">Ok</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
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
                   
                </div>
                <div class="panel-body">
                   <form method="POST" action="" id="form">
                      <div class="col-lg-12">
                                <div class="col-lg-4">
                                <label for="inputAddress">Date</label>
                                <input type="date" name="salesDate" autocomplete="off" class="form-control">
                            </div>
                            <div class="col-lg-4">
                                <label for="inputAddress">Item</label>
                                <select class="form-control" id="item" name="type" required>
                                <option value="">--Select Product--</option>
                                <?php
                                $sql = $db->prepare("SELECT * FROM tbl_vsdc_items");
                                $sql->execute();
                                while($rowss = $sql->fetch()){
                                ?>
                                <option value="<?php echo $rowss['itemCd'];?>"><?php echo $rowss['itemNm']; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                            
                            <div class="col-lg-4">
                                <label for="inputAddress">Price</label>
                                <input type="number" name="price" id="price" autocomplete="off" class="form-control" required>
                                <input type="hidden" id="bal" value="<?php echo $_SESSION['balance'];?>" class="form-control" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="inputAddress">Transport</label>
                                <input type="number" name="transport" id="transport" autocomplete="off" class="form-control transport" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="inputAddress">Discount</label>
                                <input type="number" name="discount" id="discount" autocomplete="off" class="form-control discount" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="inputAddress">Quantity</label>
                                <input type="number" name="quantity" autocomplete="off" class="form-control quantity" required>
                                
                            </div>
                            
                            <div class="col-lg-4">
                                <label for="inputAddress">Amount</label>
                                <input type="number" name="amount" autocomplete="off" class="form-control amount" required readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="inputAddress">APTC Margin</label>
                                <input type="number" name="aptc_margin" id="aptc_margin" autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>


  <hr>
  
                             <div class="col-lg-6">
                                <label for="inputAddress">Client Tin Number</label>
                                <input type="text" name="tinnumber" id="tinnumber" autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>
                            <div class="col-lg-6">
                                <label for="inputAddress">Client name</label>
                                <input type="text" name="client_name" id="client_name" autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>
                            <div class="col-lg-6">
                                <label for="inputAddress">Purchase code</label>
                                <input type="text" name="purchasecode" id="purchasecode" autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>

                            <div class="col-lg-6">
                                <label for="inputAddress">Sales Type</label>
                                <input type="text" name="salestype" id="salestype" placeholder="C,N,P,T" autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>

                            <div class="col-lg-6">
                                <label for="inputAddress">Recipient type</label>
                                <input type="text" name="rectype" id="rectype" placeholder="S for sale or R of Refund" autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>


                            </div>

                            <hr>


                            <h4>List of items </h4>



                               <select class="form-control" id="itemselect"  onchange="selectitemvsd()">
                                <option value="">--Add item--</option>
                                <?php
                                $sql = $db->prepare("SELECT * FROM tbl_vsdc_items");
                                $sql->execute();
                                while($rowss = $sql->fetch()){
                                ?>
                                <option value="<?php echo $rowss['itemId'];?>"><?php echo $rowss['itemNm']; ?></option>
                                <?php } ?>
                                </select>

                                <table class="table">
                                <tr><td>Item</td><td>Price</td><td>Quantity</td><td>Price</td><td>Action</td></tr>
<?php $id = $_SESSION['agro_id'];

$item_list= "";
$sum = 0;
$no = 0;
$sql = "SELECT * FROM cart_sales where session_id='$id' and status='0'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $no  = $no + 1;
$sum = $sum + getCartItemprice($row['item_id'])* $row['qty'];
$item_list = $item_list . getitemlist($row['item_id'],$no);
echo "<tr><td>".getCartItemname($row['item_id']). "</td><td>".getCartItemprice($row['item_id'])."</td><td>".$row['qty']."</td><td>".getCartItemprice($row['item_id'])* $row['qty']."</td><td>Delete</td></tr>";

  }
} 

?></table>
<?php
function getCartItemname($id){
   include 'link.php';
  $sql = "SELECT * FROM tbl_vsdc_items where itemId='$id'";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
   
      return $row['itemNm'];
  
  
    }
  }

}


function getCartItemprice($id){
  include 'link.php';
 $sql = "SELECT * FROM tbl_vsdc_items where itemId='$id'";
 $result = $conn->query($sql);
 
 if ($result->num_rows > 0) {
   // output data of each row
   while($row = $result->fetch_assoc()) {
  
     return $row['dftPrc'];
 
 
   }
 }

}

function getquantity($id){
  include 'link.php';

  $sum= 0;

  $sql = "SELECT * FROM cart_sales where item_id='$id'";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
   
      $sum = $sum + $row['qty'];
  
  
    }
  }
return $sum;
}


function getitemlist($id,$no){
  include 'link.php';

$list = "";

 $sql = "SELECT * FROM tbl_vsdc_items where itemId='$id'";
 $result = $conn->query($sql);
 
 if ($result->num_rows > 0) {
   // output data of each row
   while($row = $result->fetch_assoc()) {
     
     $qty = getquantity($id);
     $sum =  getCartItemprice($id) * $qty;
  
     $list = '{"itemSeq":'.$no.',"itemCd":"'.$row['itemCd'].'","itemClsCd":"'.$row['itemClsCd'].'","itemNm":"'.$row['itemNm'].'","bcd":null","pkgUnitCd":"'.$row['pkgUnitCd'].'","pkg":1,"qtyUnitCd":"'.$row['qtyUnitCd'].'","qty":"'.$qty.'","prc":"'.$row['dftPrc'].'","splyAmt":0,"dcRt":0,"dcAmt":0,"isrccCd":null,"isrccNm":null,"isrcRt":null,"isrcAmt":null,"taxTyCd":"B","taxblAmt":0,"taxAmt":0,"totAmt":"'.$sum.'"}';
 
 
   }
 }
 return '['.$list.']';
}

echo $item_list;
?>
 <div class="col-lg-6">
                                <label for="inputAddress">Total amount</label>
                                <input type="text" name="totalamount" id="totalamount" value="<?php echo $sum ?>" autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>

                            <div class="col-lg-6">
                                <label for="inputAddress">Product</label>
                                <input type="text" name="productList" id="productList" value='<?php echo $item_list; ?>' autocomplete="off" class="form-control aptc_margin" required>
                                <br><br>
                            </div>

                            <br>
                            <center><button class="btn btn-success" name="save" id="save">Save</button>  <a href="lstsales" class="btn btn-default"><i class="fa fa-backward"></i> Back</a></center>
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

<script>
function selectitemvsd() {
  var x = document.getElementById("itemselect").value;

var id = "<?php echo $id ?>";
var data = new FormData();
data.append("item",x);
data.append("user", id);
var xhr = new XMLHttpRequest();
xhr.withCredentials = true;

xhr.addEventListener("readystatechange", function() {
  if(this.readyState === 4) {
    console.log(this.responseText);
    location.reload();
  }
});

xhr.open("POST", "addtocart.php");

xhr.send(data);

}
</script>

<?php include('scripts.php'); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#branch").change(function () {
            var branch = $('#branch').val();
             $.post("load_of_sales.php", { branch:branch}, function(data){
                 //alert(type);
                 $("#display").html(data);
                 location.reload();
                });
        });
        
        });
  </script>           
    <script type="text/javascript">
    $(document).ready(function () {
        $("#item").change(function () {
            var item = $("#item").val();
            $.ajax({
            url:"load_price.php",
            method:"POST",
            data:{item:item},
            dataType:"json",
            success:function(data)
    {
        $("#price").val(data);
    }
            });
        });
        $("#form").delegate(".quantity","keyup change", function(){
        var quantity = $(this);
        var bal = parseInt($("#bal").val());
        var qty = $(this).val();
        $("#form").find(".amount").val(quantity.val()*($("#price").val()-$("#discount").val()-$("#transport").val()));
        var amount = parseInt($('.amount').val());
        if (bal >= amount) {
        $("#error").html("<label class='label label-success'>Balance Enough!</label>");
      } else {
        $("#error").html("<label class='label label-danger'>Balance not Enough!</label>");
      }
      })
        });


    
  </script> 
