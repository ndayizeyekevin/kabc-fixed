<style>
    table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 0px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
</style>




<?php include '../inc/conn.php';
 include '../inc/config.php';

function getItemName($id){

include '../inc/conn.php';

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['item_name'];
  }
}

}

function getItemPrice($id){

include '../inc/conn.php';

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['price'];
  }
}

}


function getItemUnitId($id){

include '../inc/conn.php';

$sql = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['item_unit'];
  }
}

}


function getItemUnitName($id){

include '../inc/conn.php';

$sql = "SELECT * FROM tbl_unit where unit_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['unit_name'];
  }
}

}



	function getSupplierName($id){

include '../inc/conn.php';

$sql = "SELECT * FROM suppliers where id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['name'];
  }
}

}

?>
<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <style type="text/css"> * {margin:0; padding:0; text-indent:0; }
         p { color: black; font-family:Tahoma, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt; margin:0pt; }
         .s1 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 12pt; }
         .s2 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 7.5pt; }
         .s3 { color: black; font-family:Verdana, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 7.5pt; }
         table, tbody {vertical-align: top; overflow: visible; }
      </style>
   </head>
   <body style="background-color:#e1e1e1">

  <center>     <button onclick="printInvoice()">Print To PDF </button></center>

    <div  id="content" style="background-color:white;margin-top:50px;margin-left:10%;padding:20px;margin-right:10%;height:100%;padding-top:100px">
   <?php include '../holder/printHeader.php'?>


          <table border="0" style="width:100%">
          <tr><td>

      </td>

      <td>



<p class="s1" style="padding-top: 6pt;text-indent: 0pt;text-align: center;border-width: thin;padding:20px">
<center>Good Received Note No : <?PHP ECHO $_REQUEST['id']?>  - On<?PHP ECHO date('Y-m-d')?>
     </p>

      </td>

            <td>

            </td>
      </tr></table>

      <br>     <br>    <br>    <br>

              <table  class="table table-striped">
       <thead>

                                             <tr>
                                        <th colspan="3"></th>
                                        <th colspan="3">PURCHASE </th>
                                         <th colspan="3">RECEIVING</th>





                                    </tr>


                                    <tr>
                                        <th>#</th>
                                        <th>Item </th>
                                         <th>Unit</th>
                                        <th>Qty</th>
                                        <th>P.U</th>
                                        <th>T.P</th>
                                          <th>Quantity</th>
                                          <th>P.U</th>
                                          <th>T.P</th>





                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;

								     	$amount = 0;
									   $del_amount =0;
                                		$result = $db->prepare("SELECT * FROM  request_store_item WHERE req_id='".$_REQUEST['id']."'");
                                        $result->execute();
                                		while($fetch = $result->fetch()){
                                		    $i++;
											$total =  getItemPrice($fetch['item_id']) * $fetch['qty'];
                      if($fetch['del_qty'] ){
                        $del_total = $fetch['pur_price'] * $fetch['del_qty'];
                      }else{
                        $del_total =0;
                      }


						$amount = $amount + $total;
							$del_amount = $del_amount + $del_total;

                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><?php echo getItemName($fetch['item_id']); ?></td>
                                             <td><?php echo getItemUnitName(getItemUnitId($fetch['item_id'])); ?></td>
                                            <td><?php echo $fetch['qty']; ?> </td>
                                            <td><?php echo number_format(getItemPrice($fetch['item_id'])); ?> </td>
                                            <td><?php echo number_format($fetch['qty'] * getItemPrice($fetch['item_id'])); ?> </td>



                                             <td><?php if($fetch['del_qty'] ){
                                              echo number_format($fetch['del_qty']);
                                             }else{
                                              echo 0;
                                             } ?> </td>
                                             <td><?php if($fetch['del_qty'] ){ echo number_format($fetch['pur_price']);}else{
                                              echo 0;
                                             } ?></td>
                                              <td><?php if($fetch['del_qty']){
                                                echo number_format($fetch['del_qty'] * $fetch['pur_price']);
                                              }else{
                                                echo 0;
                                              } ?></td>



                                     	</tr>
         <?php
	}
?>




  	<tr>
	<td colspan="2"><H4>Total</H4></td>
	<td></td>
	<td></td>

	<td></td>
	<td><H4><?php echo number_format($amount)?> </H4></td>

		<td></td>

	<td></td>
	<td><H4><?php echo number_format($del_amount)?> </H4></td>
	</tr>

	  	<tr>
	<th colspan="8"><H4>Balance</H4></th>
	<th><H4><?php echo number_format($amount - $del_amount)?> </H4></th>
	</tr>



                            </tbody>

                        </table>




                   <br>   <br>      <br>      <br>

            <table border="0" style="width:100%">
          <tr>


      <td>



      <center>   Received by: <br>(Names and signature) <br> <?php echo $_SESSION['f_name']." ".$_SESSION['l_name']?> </center>
      </td>

            <td>






      <center>   Logistics: <br>(Names and signature) <br>........................ </center>
            </td>




      </tr></table>



    </div>
   </body>
</html>

<script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>




<!DOCTYPE html>