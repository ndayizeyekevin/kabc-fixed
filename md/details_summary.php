<?php date_default_timezone_set('GMT');
//$last= lastday();
// if(!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])){
//     $from = date('Y-m-d');
//     $to = date("Y-m-d");
//    }
//    else{
//        $from = $_SESSION['date_from'];
//        $to = $_SESSION['date_to'];
//    }

function  getadvances($date,$type){
include '../inc/conn.php';
$amount = 0;

$sql = "SELECT * FROM advances where advance_type= '$type'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

       if(isset($_SESSION['fromdate'])){
          $from = $_SESSION['fromdate'];
          $to = $_SESSION['todate'];
       }else{

       }

     if($date==date('Y-m-d',$row['created_at'])){

     $amount = $amount  + $row['amount'];

     }
  }
}


return $amount;
}





function getServantName($id){
    include '../inc/conn.php';

  $sql = "SELECT * FROM `tbl_users` WHERE user_id ='$id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
return  $row['f_name']. " ".$row['l_name'];

  }}}

function getTotalCreditAmount($id){
  include '../inc/conn.php';
  $amount = 0;
  $sql = "SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty` INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item WHERE cmd_code ='$id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {



       $amount = $amount + $row['cmd_qty'] * $row['menu_price'];

  }
}


  return $amount;
}

function getEbmPaymentMode($id){
  include '../inc/conn.php';

  $sql = "SELECT * FROM tbl_vsdc_sales where transaction_id ='$id'";
  $result = $conn->query($sql);
  $sale=0;
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      return $row['pmtTyCd'];
    }}


}


function getcreditDetails($id){
include '../inc/conn.php';
$names = "";
$sql = "SELECT * FROM tbl_vsdc_sales where pmtTyCd ='02'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {


      $trx =  substr((string)$row['transaction_id'], 0, 10);
if (ctype_digit($trx)) {


     if($id==date('Y-m-d',$trx)){

      $names = $names.$row['custNm']." ".$row['totAmt']."<br> ";

     }


}



  }
}

return strtoupper($names);

}

function getcredits($id){
include '../inc/conn.php';
$amount = 0;

$sql = "SELECT * FROM  tbl_vsdc_sales WHERE pmtTyCd ='02'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
while($row = $result->fetch_assoc()) {
$trx =  substr((string)$row['transaction_id'], 0, 10);
if (ctype_digit($trx)) {
     if($id==date('Y-m-d',$trx)){

       $amount = $amount + $row['totAmt'];

     }
}


  }
}


return strtoupper($amount);













}





function getInvoiceNo($id){

        global $db;
        include '../inc/conn.php';

                                    $sqli = $db->prepare("SELECT * FROM `tbl_cmd` WHERE OrderCode='$id'");
                                    $sqli->execute();
                                    while($row = $sqli->fetch()){
                                      return  $row['id'];
                                    }

    }



function getCreditNames($id){

global $db;
    include '../inc/conn.php';

                                $sqli = $db->prepare("SELECT * FROM `creadit_id` WHERE id='$id'");
                        		$sqli->execute();
                        		while($row = $sqli->fetch()){
                        		  return  $name =  $row['f_name']." ".$row['l_name'];
                        		}




}

function getNames($id){

global $db;
    include '../inc/conn.php';

                                $sqli = $db->prepare("SELECT * FROM creadit_id where id ='$id'");
                        		$sqli->execute();
                        		while($row = $sqli->fetch()){
                        		  return  $name =  $row['f_name']." ".$row['l_name'];
                        		}




}


function  getAdvanceDetails($to,$type){

include '../inc/conn.php';
$names = "";

$sql = "SELECT * FROM advances where advance_type= '$type'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     if($to==date('Y-m-d',$row['created_at'])){

     $names = $names.$row['advance_by']." - ".$row['amount']." RWF <br>";

     }
  }
}


return strtoupper($names);



}



function  getCollectionDetails($to){

    include '../inc/conn.php';
$names = "";

$sql = "SELECT * FROM collection";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     if($to==date('Y-m-d',$row['created_at'])){

     $names = $names. getNames($row['names'])." - ".$row['amount']." RWF <br>";

     }
  }
}


return strtoupper($names);



  }





 function  getcollection($date){
include '../inc/conn.php';
$amount = 0;

$sql = "SELECT * FROM collection";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

       if(isset($_SESSION['fromdate'])){
          $from = $_SESSION['fromdate'];
          $to = $_SESSION['todate'];
       }else{

       }

     if($date==date('Y-m-d',$row['created_at'])){

     $amount = $amount  + $row['amount'];

     }
  }
}


return $amount;
}




















function  getPartners($category,$last){
 include '../inc/conn.php';
$names = "";
$sql = "SELECT cmd_code FROM `tbl_cmd_qty` INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item WHERE cmd_qty_id > '$last' AND cmd_status = '12'  AND cat_id = '$category' group by  cmd_code";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     $names  = $names." ".getCreditUserId($row['cmd_code']);


  }
}

return $names;
}

  function  getCreditUserId($code){
           include '../inc/conn.php';
    $names = "";
       $sql = "SELECT creadit_user FROM  tbl_cmd  WHERE OrderCode = '$code'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

    return  getAccount($row['creadit_user']);


  }
}



}

  function  getAccount($code){
           include '../inc/conn.php';
    $names = "";
       $sql = "SELECT  l_name,f_name FROM  creadit_id  WHERE id = '$code'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

     $names = $row['l_name']." ".$row['f_name'];


  }
}



      return $names;



   }




function getOrderTotal($order){

include '../inc/conn.php';
$sql = "SELECT cmd_qty,cmd_item FROM `tbl_cmd_qty` WHERE  cmd_code = '$order'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {



         $sale = $sale + get_price($row['cmd_item']) * $row['cmd_qty'];


}
}




   return $sale;


}

function getifpaid($code){
  include '../inc/conn.php';
  $sql = "SELECT order_code FROM payment_tracks where order_code = '$code'";
  $result = $conn->query($sql);
  return $result->num_rows;

    }






      function getTotalByServicebycode($code, $category,$from,$last){
      include '../inc/conn.php';



      if($last==0){
       $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE  cmd_qty_id > '$from'   AND cmd_status = '12'  AND cmd_code = '$code'";
      }else{
      $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE cmd_qty_id > '$from' AND  cmd_qty_id <= '$last'  AND cmd_status = '12' AND cmd_code = '$code'";
      }
      $result = $conn->query($sql);
      $sale=0;
      if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {

         if(getifpaid($row['cmd_code'])>0){
         if(get_category($row['cmd_item'])==$category){

               $sale = $sale + get_price($row['cmd_item']) * $row['cmd_qty'];
         }}




      }
      }




         return $sale;


      }








function getTotalByService($category,$from,$last){
include '../inc/conn.php';
if($last==0){
 $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE  cmd_qty_id > '$from'   AND cmd_status = '12' ";
}else{
$sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE cmd_qty_id > '$from' AND  cmd_qty_id <= '$last'  AND cmd_status = '12'";

}
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

   if(getifpaid($row['cmd_code'])>0){
   if(get_category($row['cmd_item'])==$category){

         $sale = $sale + get_price($row['cmd_item']) * $row['cmd_qty'];
   }}




}
}




   return $sale;


}





   function  get_category($id){
    include '../inc/conn.php';


$sql = "SELECT cat_id FROM `menu` WHERE menu_id = '$id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

 return $row['cat_id'];


}
}


}


function get_price($id){
  include '../inc/conn.php';


$sql = "SELECT  menu_price FROM `menu` WHERE menu_id = '$id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

return $row['menu_price'];


}
}


}


      function getTotalCollectionPaidByMethod($date,$method){

       include '../inc/conn.php';



    $sql = "SELECT * FROM payment_tracks where service = 'collection'  AND method = '$method' ";


$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

          if($date==date('Y-m-d',$row['created_at'])){

              $sale = $sale + $row['amount'] ;


          }


  }
}


      return $sale;


   }




   function getTotaladvancePaidByMethodLess($date,$method){

    include '../inc/conn.php';



 $sql = "SELECT * FROM payment_tracks where service = 'less advance'  AND method = '$method' ";


$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

       if($date==date('Y-m-d',$row['created_at'])){

           $sale = $sale + $row['amount'] ;


       }


}
}


   return $sale;


}







      function getTotaladvancePaidByMethod($date,$method){

       include '../inc/conn.php';



    $sql = "SELECT * FROM payment_tracks where service = 'advance'  AND method = '$method' ";


$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

          if($date==date('Y-m-d',$row['created_at'])){

              $sale = $sale + $row['amount'] ;


          }


  }
}


      return $sale;


   }





   function getTotalPaidByMethod($code,$method){
    include '../inc/conn.php';

    $sql = "SELECT order_code, amount, method FROM payment_tracks  where order_code = '$code'  AND method = '$method'
    GROUP BY order_code, amount, method";
    $result = $conn->query($sql);
    $sale=0;
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {


                  $sale = $sale + $row['amount'] ;





      }
    }


          return $sale;


    }







 function lastday(){



 include '../inc/conn.php';


 $sql = "SELECT * FROM days ORDER BY id DESC LIMIT 1 ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

      $last = $row['to_id'];


  }}

   return $last;

}



if(isset($_POST['close'])){



 include '../inc/conn.php';


 $sql = "SELECT * FROM tbl_cmd_qty ORDER BY cmd_qty_id DESC LIMIT 1 ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

      $lastid = $row['cmd_qty_id'];


  }}

  if (isset($_POST['approve'])){


  $date = $_POST['closedate'];
   $time = time();
   $from = lastday();
   $sql = "INSERT INTO `days` (`id`, `date`, `from_id`, `to_id`, `created_at`) VALUES (NULL, '$date', '$from', '$lastid', '$time');";

if ($conn->query($sql) === TRUE) {
  echo "<script>Day Successfull closed</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}


} else {
echo "<script>alert('Please comfirm data before closing the day')</script>";
}







}


?>
<div class="container">
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">




           <form action="" method="POST" >
                 <div style="padding:30px">
                  Select Day:

                       <select name="selectday" class = "form-control">
                     <?php

                                    $sql = $db->prepare("SELECT *  FROM days order by date DESC");
                            		$sql->execute(array());

                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){


   ?><option value="<?php echo $fetch['date']?>"> <?php echo $fetch['date']?> </option><?php
  }
}
   ?>
                       </select>



                       <br>
                         <button name="check">Load</button>

            </form>
                        </div>



      <?php



if(isset($_POST['check'])){

    $to  =$_POST['selectday'];





                                   $sql = $db->prepare("SELECT * FROM days WHERE date ='$to'");
                            		$sql->execute(array());

                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){

                            		  $from = $fetch['from_id'];

                            		}}else{
                            		   $from = 'none';
                            		}



                            		  $sql = $db->prepare("SELECT * FROM days WHERE date ='$to'");
                            		$sql->execute(array());

                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){

                            		    // $fromId = $fetch['from_id'];
                            		     $last = $fetch['to_id'];
                            		}}else{
                            		    $last  = 'none';
                            		}






      $reportDate    = $to;

      $date = $to;


}else{





                                      $reportDate    =  date('Y-m-d');
                                      $to = date("Y-m-d");
                                      $from = lastday();
                                      $last = 0;












   }








      ?>


<button onclick =" printInvoice()"> Print </button>
                        <button onclick="exportTableToExcel('sales', 'Detailed sales of <?php echo $reportDate?>')">Export to Excel</button>
                     <hr>

            <br>
            <br>
             <div id = "content">


   <?php include '../holder/printHeader.php'?>

<center><h2>Detailed club: <?php echo $reportDate?> </h2></center>
<div id="sales" class="table-responsive">
                              <table  class="table table-striped">
                                <thead>


                                    <tr>


                                        <th>Invoice No</th>
                                        <th>Waiter/ess</th>
                                        <th>Order Total</th>
                                        <th>Cash</th>
                                        <th>MOMO</th>
                                        <th>POS</th>
                                        <th>CREDIT</th>
                                        <th>CHEQUE</th>
                                        <th>PAYMENT METHOD TOTAL</th>
                                        <th>BALANCE</th>
                                        <th>EBM INVOICE</th>
                                    </tr>
                                </thead>
                                <tbody>



             <?php


                                       $cash =  0;
                            		   $card = 0;
                            		   $momo = 0;
                            		   $credit= 0;
                            		    $transfer= 0;
                            		     $cheque= 0;

                             $cashcollection = 0;
                              $cardcollection = 0;
                               $momocollection = 0;
                                $creditcollection = 0;
                                 $chequecollection = 0;

                                 $cashadvanceless = 0;
                                 $cardadvanceless = 0;
                                 $momoadvanceless = 0;
                            		   $creditadvanceless= 0;
                            		   $chequeadvanceless= 0;

                             $cashadvance = 0;
                              $cardadvance = 0;
                               $momoadvance = 0;
                                $creditadvance = 0;
                                 $chequeadvance = 0;

                                 $cash =  0;
                                 $card = 0;
                                 $momo = 0;
                                 $credit= 0;
                                  $transfer= 0;
                                   $cheque= 0;

                                   $no= 0;

 // $d =  "SELECT cmd_code FROM  `tbl_cmd_qty` WHERE cmd_qty_id > '$from' AND  cmd_qty_id <= '$last' AND cmd_status = '12' group by  cmd_code";
if($last== 0){
 $sql = $db->prepare("SELECT * FROM  `tbl_cmd_qty` WHERE cmd_qty_id > '$from'  AND cmd_status = '12' group by  cmd_code ");
}else{
  $sql = $db->prepare("SELECT * FROM  `tbl_cmd_qty` WHERE cmd_qty_id > '$from' AND  cmd_qty_id <= '$last' AND cmd_status = '12' group by  cmd_code");
} $sql->execute(array());
                              if($sql->rowCount()){
                              while($fetch = $sql->fetch()){


                           if($fetch['cmd_code']){

                            $order = $order + getOrderTotal($fetch['cmd_code']);


                            ?>
                           <tr>

                            <td><a href="https://saintpaul.gope.rw/reception/index?resto=gstDet&c=<?php echo  $fetch['cmd_code']?>"><?php echo getInvoiceNo($fetch['cmd_code']) ?></a></td>
                            <td><?php echo getServantName($fetch['Serv_id']); ?></td>
                            <td><?php echo getOrderTotal($fetch['cmd_code']); ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'01') ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'06'); ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'05'); ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'02'); ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'04'); ?></td>
                            <td><?php echo number_format($tt= getTotalPaidByMethod($fetch['cmd_code'],'04') +
                             getTotalPaidByMethod($fetch['cmd_code'],'02') +
                             getTotalPaidByMethod($fetch['cmd_code'],'05')+
                             getTotalPaidByMethod($fetch['cmd_code'],'06') +
                             getTotalPaidByMethod($fetch['cmd_code'],'01')) ?></td>
                             <td><?php echo getOrderTotal($fetch['cmd_code']) - $tt;  ?></td>

<td><?php
if(getEbmPaymentMode($fetch['cmd_code'])=='01'){
echo "Cash";
}

if(getEbmPaymentMode($fetch['cmd_code'])=='02'){
  echo "Credit";
}

  if(getEbmPaymentMode($fetch['cmd_code'])=='06'){
    echo "Mobile Money";
    }


    if(getEbmPaymentMode($fetch['cmd_code'])=='04'){
      echo "Cheque";
      }

      if(getEbmPaymentMode($fetch['cmd_code'])=='05'){
        echo "POS";
        }


?></td>

                           </tr>


                            <?php

                                 $cash = $cash + getTotalPaidByMethod($fetch['cmd_code'],'01');
                                 $card = $card + getTotalPaidByMethod($fetch['cmd_code'],'05');
                                 $momo = $momo + getTotalPaidByMethod($fetch['cmd_code'],'06');
                                 $credit= $credit + getTotalPaidByMethod($fetch['cmd_code'],'02');
                                 $cheque= $cheque + getTotalPaidByMethod($fetch['cmd_code'],'04');



                                 $br = $br + getTotalByServicebycode($fetch['cmd_code'],2,$from,$last);
                                 $rs = $rs + getTotalByServicebycode($fetch['cmd_code'],1,$from,$last);
                                 $cf = $cf + getTotalByServicebycode($fetch['cmd_code'],32,$from,$last);


                           }


                              }

                              }

                              ?>
                              <tr>
                               <th colspan="3">Total:</th>
                               <th><?php echo  number_format($order);?></th>
                               <th ><?php echo number_format($cash) ?></th>
                               <th ><?php echo number_format($momo) ?></th>
                               <th ><?php echo number_format($card) ?></th>
                               <th ><?php echo number_format($credit) ?></th>
                               <th ><?php echo number_format($cheque) ?></th>
                               <th ><?php echo number_format( $py = $cheque + $cash + $momo + $card + $credit) ?> <br>
                               (  <?php echo number_format($order - $py ); ?>  )
                              </th>
                           </tr>



                                <tr>




                                    </tr>

                                    <tr>
                                        <th colspan="9">Title</th>
                                        <th>Total</th>



                                    </tr>


                                    <tr>
                                        <td colspan="9">Bar Sales</td>
                                        <td><?php echo number_format ($totalbar=getTotalByService(2,$from,$last)) ?> </td>

                                    </tr>

                                          <tr>
                                        <td colspan="9">Resto Sales</td>
                                         <td><?php echo number_format ($totalresto=getTotalByService(1,$from,$last)) ?></td>

                                         </tr>

                                            <tr>
                                        <td colspan="9">Coffe Shop</td>
                                         <td><?php echo number_format ($totalcoffe=getTotalByService(32,$from,$last)) ?></td>

                                         </tr>


                                         <tr>
                                        <td colspan="9">Transport Fees</td>
                                         <td><?php echo number_format ($transport=getTotalByService(33,$from,$last)) ?></td>

                                         </tr>


                                         <tr>

                                          <th colspan="9">Total:</th>
                                          <th> <?php echo number_format ($totalresto  + $totalcoffe + $totalbar  + $transport) ?> </th>
                                         </tr>






                                         <tr>
                                     <td colspan="10"><center><h4> CREDITS LIST</h4> </center></td>

                                    </tr>


                                    <?PHP
                              $no = 0;
                              $totalcredi = 0;
                              $sql = $db->prepare("SELECT * FROM tbl_vsdc_sales where pmtTyCd ='02' AND has_refund='0'");
                              $sql->execute(array());
                              if($sql->rowCount()){
                              while($fetch = $sql->fetch()){



                                $trx =  substr((string)$fetch['transaction_id'], 0, 10);
                                if (ctype_digit($trx)) {


                                     if($reportDate==date('Y-m-d',$trx)){

                                      $totalcredi = $totalcredi + $fetch['totAmt'];

                                      ?>
                                      <tr>
                                      <td><?php echo $no = $no + 1 ?> </td>
                                      <td>Invoice at <?php echo $fetch['cfmDt'] ?></td>
                                      <td>Posted At:  <br></td>
                                      <td><?php echo getInvoiceNo($fetch['transaction_id'])."<br>".$fetch['transaction_id'] ?></td>
                                      <td colspan="6"><?php echo $fetch['custNm'] ?></td>
                                      <td><?php echo $fetch['totAmt'] ?></td>
                                    </tr>

                                      <?php

                                     }else{
                                     ?>

                                   <?php  }


                                }









                              }}



?>
  <tr>
                                <td colspan="9">Total:</td>
                                <td>Total: <?php echo number_format($totalcredi) ?> <br> <br> (Balance: <?php echo $totalcredi - $credit?> )</td>
                              </tr>

                                </tbody>
                            </table>





                       </div> </div> </div> </div>

        </div>  </div>
    </div>
    </div></div></div>
</div>
</div>

<script> function printInvoice() {
$("#headerprint").show();
var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

$("#headerprint").hide();
        $("#date_to").change(function () {
            var from = $("#date_from").val();
            var to = $("#date_to").val();
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');
            $.post('load_sales_report.php',{from:from,to:to} , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
                location.reload();
            });
        });

    });
</script>
<script>
function exportTableToExcel(tableID, filename = ''){
    let downloadLink;
    const dataType = 'application/vnd.ms-excel';
    const tableSelect = document.getElementById(tableID);
    const tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = filename ? filename + '.xls' : 'excel_data.xls';

    // Create download link element
    downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    // File format
    downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

    // File name
    downloadLink.download = filename;

    // Trigger the download
    downloadLink.click();

    // Clean up
    document.body.removeChild(downloadLink);
}
</script>