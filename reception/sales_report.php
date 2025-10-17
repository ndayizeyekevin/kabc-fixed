<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?php 

date_default_timezone_set('GMT');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
function  getadvances($date,$type){
include '../inc/conn.php';
//include "../inc/close_open_day.php";

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


function get_pricenew($cmd_item, $transaction_id) {
    include '../inc/conn.php';

$sql = "SELECT itemCd FROM `menu` WHERE menu_id = '$cmd_item'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {
$code = $row['itemCd'];
}
}


$sql = "SELECT  itemList FROM `tbl_vsdc_sales` WHERE transaction_id = '$transaction_id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

$item = $row['itemList'];
}
}
//return $item;
return  get_item_price($item, $code);
}


function get_item_price($itemsArray, $itemCd) {
  $items = json_decode($itemsArray, true);
    foreach ($items as $item) {
        if (isset($item['itemCd']) && $item['itemCd'] === $itemCd) {
            return $item['prc'];
        }
    }
    return 0;
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
$sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE  cmd_code = '$order'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

         $sale = $sale + get_price($row['cmd_item'],$row['cmd_code']) * $row['cmd_qty'];

}
}




   return $sale;


}




function getOrderTotalCategory($order, $category) {
    include '../inc/conn.php'; // Use PDO if available

    // Use prepared statements for security
    $stmt = $conn->prepare("
        SELECT cmd_qty, cmd_item, cmd_code
        FROM tbl_cmd_qty
        INNER JOIN menu ON cmd_item = menu_id
        WHERE cat_id = ? AND cmd_code = ?
    ");
    $stmt->bind_param("ss", $category, $order);
    $stmt->execute();
    $result = $stmt->get_result();

    $sale = 0;

    while ($row = $result->fetch_assoc()) {
        // Ensure get_price() is defined and returns correct price
        $price = get_price($row['cmd_item'], $row['cmd_code']);
        $sale += $price * $row['cmd_qty'];
    }

    $stmt->close();
    return $sale;
}




function getifpaid($code){
  include '../inc/conn.php';
  $sql = "SELECT order_code FROM payment_tracks where order_code = '$code'";
  $result = $conn->query($sql);
  return $result->num_rows;
}


      function getTotalByServicebycode($category,$from,$last){
      include '../inc/conn.php';

      if($last==0){
       $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE  cmd_qty_id > '$from'   AND cmd_status = '12' ";
      }else{
      $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE cmd_qty_id > '$from' AND  cmd_qty_id <= '$last' AND cmd_status = '12'  ";
      }
      $result = $conn->query($sql);
      $sale=0;
      if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {


         if(get_category($row['cmd_item'])==$category){

               $sale = $sale + get_price($row['cmd_item'],$row['cmd_code']) * $row['cmd_qty'];
         }




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


    $sale = $sale + get_price($row['cmd_item'],$row['cmd_code']) * $row['cmd_qty'];
   }else{


  if(get_category($row['cmd_item'])==1 || get_category($row['cmd_item'])==2  ||  get_category($row['cmd_item'])==32||  get_category($row['cmd_item'])==33){

   }else{
 $e =$row['cmd_item'];
 echo "<script>alert($e) </script>";
   }







   }
  }else{
    //echo "<script>alert('no') </script>";
  }




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
   // function getTotalPaidByMethod($code,$method){
   //  include '../inc/conn.php';
   //
   //  $sql = "SELECT order_code, amount, method FROM payment_tracks  where order_code = '$code'  AND method = '$method'
   //  GROUP BY order_code, amount, method";
   //  $result = $conn->query($sql);
   //  $sale=0;
   //  if ($result->num_rows > 0) {
   //    // output data of each row
   //    while($row = $result->fetch_assoc()) {
   //
   //
   //                $sale = $sale + $row['amount'] ;
   //
   //
   //
   //
   //
   //    }
   //  }
   //
   //
   //        return $sale;
   //
   //
   //  }
   //

include_once '../inc/close_open_day.php';

// if (isset($_POST['open'])) {
//
//
//
//    include '../inc/conn.php';
//
// $q = "SELECT COUNT(tbl_cmd.id) as un_paid
// FROM tbl_cmd
// LEFT JOIN payment_tracks
// ON tbl_cmd.OrderCode = payment_tracks.order_code
// WHERE payment_tracks.order_code IS NULL;";
//
// $d = $conn->query($q); // execute query
// $row = $d->fetch_assoc(); // fetch result
//
// if ($row['un_paid'] > 0) {
//     echo "<script>alert('There are {$row['un_paid']} orders that are not paid'); location.href=location.href</script>";
//
// }else{
// 	$sql = "SELECT * FROM tbl_cmd_qty ORDER BY cmd_qty_id DESC LIMIT 1 ";
// 	$result = $conn->query($sql);
// 	$sale = 0;
// 	if ($result->num_rows > 0) {
// 		// output data of each row
// 		while ($row = $result->fetch_assoc()) {
//
// 			$lastid = $row['cmd_qty_id'];
// 		}
// 	}
//
// 	if (isset($_POST['approve'])) {
//
//
// 		$date = $_POST['opendate'];
// // 		$time = time();
// 		$from = lastday();
// // 		die(var_dump($from));
//     $date = date('Y-m-d H:i:s', strtotime($date));
//     $uid= $_SESSION['u_id'];
//
// 		$sql = "INSERT INTO `days` (`reviewed_by`, `reviewed_at`, `opened_at`) VALUES ('$uid', CURRENT_TIMESTAMP,'$date')";
//
// 		if ($conn->query($sql) === TRUE) {
// 			echo "<script>Day Successfull closed</script>";
// 		} else {
// 			echo "Error: " . $sql . "<br>" . $conn->error;
//
// 		}
// 	} else {
// 		echo "<script>alert('Please comfirm data before closing the day')</script>";
// 	}
//   }
// }




//  function lastday(){
//
//
//
//  include '../inc/conn.php';
//
//
//  $sql = "SELECT DATE(opened_at) as date  FROM days  ORDER BY created_at DESC LIMIT 1 ";
// $result = $conn->query($sql);
// $sale=0;
// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {
//
//       $last = $row['date'];
//
//
//   }}
//
//    return $last;
//
// }
//


if (isset($_POST['close'])) {
    include '../inc/conn.php';
  $closedate = $_POST['closedate'];
  $d = "'$closedate'" ?? 'CURRENT_DATE()' ;
    if (isset($_POST['approve'])) {
        $sql = "UPDATE days 
                SET closed_at = $d 
                WHERE closed_at IS NULL 
                ORDER BY id DESC 
                LIMIT 1";
        $result = $conn->query($sql); // assuming you're using mysqli

        if ($result) {
            echo "<script>alert('Day successfully closed');</script>";
        } else {
            echo "Error: " . $conn->error;
        }

    } else {
        echo "<script>alert('Please confirm data before closing the day');</script>";
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
                 <label> Select Day:</label>
                  <input  type="text" id="myDatePicker" name="fromdate" value="<?php echo date('Y-m-d'); ?>"   min="2025-02-28" width="30%">

                  <button name="check">Check</button>
                  </div>

            </form>
                        </div>



      <?php


if (isset($_POST['check'])) {
    $selectedDate = $_POST['fromdate']; // You can ignore 'todate' now since you're working per day
} else {
    $selectedDate = date('Y-m-d');
}

// die(var_dump($selectedDate));
// Fetch opened_at and closed_at for the selected day
$sql = $db->prepare("SELECT * FROM days WHERE DATE(opened_at) = :selectedDate LIMIT 1");
$sql->execute([':selectedDate' => $selectedDate]);

if ($sql->rowCount()) {
    $day = $sql->fetch();
    $openedAt = $day['opened_at'];
    $closedAt = $day['closed_at'];

    // Set up query based on whether the day is closed
    if (!empty($closedAt)) {
        // Day is closed: get commands between opened and closed
        $cmdQuery = $db->prepare("
            SELECT * FROM tbl_cmd_qty
            WHERE created_at BETWEEN :openedAt AND :closedAt
            AND cmd_status = '12'
            GROUP BY cmd_code
        ");
        $cmdQuery->execute([
            ':openedAt' => $openedAt,
            ':closedAt' => $closedAt
        ]);
    } else {
        // Day is still open: get commands from opened_at onwards
        $cmdQuery = $db->prepare("
            SELECT * FROM tbl_cmd_qty
            WHERE created_at >= :openedAt
            AND cmd_status = '12'
            GROUP BY cmd_code
        ");
        $cmdQuery->execute([
            ':openedAt' => $openedAt
        ]);
    }

    $results = $cmdQuery->fetchAll();
} else {
    echo "<p>No data found for selected date.</p>";
}
?>



                        <button onclick =" printInvoice()"> Print </button>
                        <button onclick="exportTableToExcel('sales', 'Detailed sales of <?php echo $selectedDate?>')">Export to Excel</button>
                     <hr>

            <br>
            <br>
             <div id = "content">


   <?php include '../holder/printHeader.php'?>

<center><h2>Orders Reports <?php echo $selectedDate?> </h2></center>

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
                        foreach ($results as $fetch) {


                           if($fetch['cmd_code']){

                                 $order = $order + getOrderTotal($fetch['cmd_code']);
                                 $orderbars =  $orderbars  + getOrderTotalCategory($fetch['cmd_code'],2);
                                 $orderresto =  $orderresto  + getOrderTotalcategory($fetch['cmd_code'],1);
                                 $ordercoffee=  $ordercoffee  + getOrderTotalcategory($fetch['cmd_code'],32);




                            ?>
                           <tr>
                            <td><a href="../reception/index?resto=gstDet&c=<?php echo  $fetch['cmd_code']?>"><?php echo getInvoiceNo($fetch['cmd_code']) ?></a></td>
                            <td><?php echo getServantName($fetch['Serv_id']); ?></td>
                            <td><?php echo number_format(getOrderTotal($fetch['cmd_code'])); ?> </td>
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





                           }


                              }



                              ?>
                              <tr>
                               <th colspan="2">Total:</th>
                               <th><?php echo  number_format($order);?></th>
                               <th ><?php echo number_format($cash) ?></th>
                               <th ><?php echo number_format($momo) ?></th>
                               <th ><?php echo number_format($card) ?></th>
                               <th ><?php echo number_format($credit) ?></th>
                               <th ><?php echo number_format($cheque) ?></th>
                               <th ><?php echo number_format( $py = $cheque + $cash + $momo + $card + $credit) ?>

                              </th>
                           </tr>



                                <tr>




                                    </tr>









                                </tbody>
                            </table>





                       </div> </div> </div> </div>


                       <?php
				$stmt = $db->query("SELECT * FROM days ORDER BY id DESC LIMIT 1");
$lastDay = $stmt->fetch(PDO::FETCH_ASSOC);

if ($lastDay && is_null($lastDay['closed_at'])) {
    	$n = 1;
} else {
    	$n = 0;
}

				if ($n == 1):



				// 	$sql = "SELECT * FROM days ORDER BY id DESC LIMIT 1 ";
				// 	$result = $conn->query($sql);
				// 	$sale = 0;
				// 	if ($result->num_rows > 0) {
				// 		while ($row = $result->fetch_assoc()) {

				// 			$lastDate = $row['closed_at'];
				// 			$date = $row['opened_at'];
				// // 			$date->modify('+1 day');
				// // 			$lastDate = $date->format('Y-m-d');
				// 		}
				// 	}

				?>
					<form method="POST">
						<br>
						<input type="checkbox" id="approve" name="approve">I hereby confirm all imformation above are correct<br>
						<br>
						<input type="datetime-local" min="<?php  ?>" class="form-control" name="closedate" required>
						<br>
						<button type="submit" value="Close day" name="close" class="btn btn-info"> Close day </button>
					</form>
				<?php
				else:
				    	?>
				    <form method="POST">
						<br>
						<input type="checkbox" id="approve" name="approve">I hereby confirm all imformation above are correct<br>
						<br>
						<input type="datetime-local" class="form-control" name="opendate" required>
						<br>
						<button type="submit"  name="open" class="btn btn-info"> Open day </button>
					</form>
				<?php
				endif
				?>


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

<?php 

$dateq = "SELECT DATE(opened_at) as date  FROM days  ORDER BY created_at DESC;";
        $stmt_date = $db->prepare($dateq);
        $stmt_date->execute();
        $row_date = $stmt_date->fetchAll(PDO::FETCH_ASSOC);

$dates = array_column($row_date, 'date');
$uniqueDates = array_unique($dates);

$uniqueDates = array_values($uniqueDates);

?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const availableDates = <?= json_encode($uniqueDates) ?>;
  flatpickr("#myDatePicker", {
    enable: availableDates,
    dateFormat: "Y-m-d"
  });
</script>