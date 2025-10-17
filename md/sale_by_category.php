<?php

if(!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])){
    $from = date('Y-m-d');
    $to = date("Y-m-d");
   }
   else{
       $from = $_SESSION['date_from'];
       $to = $_SESSION['date_to'];
   }


  include '../inc/conn.php';
   function getTotalPaidByMethod($code,$method){

       include '../inc/conn.php';



$sql = "SELECT * FROM payment_tracks where  order_code = '$code' AND method = '$method' ";
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
 //   $lastDate = $row['date'];


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
                            <h2><small><strong><i class="fa fa-refresh"></i>Today's Sales <?php  $last= lastday();?></strong></small></h2>
                        </div>






                        <form action="" method="POST" >
                 <div style="padding:30px">
                  Select Day:

                       <select name="selectday" class = "form-control">
                     <?php

                                    $sql = $db->prepare("SELECT *  FROM days");
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





                                    $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE cmd_qty_id > '$from' AND cmd_qty_id  <='$last' AND cmd_status = '12' AND cat_id = '".$_REQUEST['type']."'
                                    GROUP BY cmd_item");

// echo $sq = "SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
// INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
// WHERE cmd_qty_id > '$from' AND cmd_qty_id  <='$last'  AND cmd_status = '12'
// GROUP BY cmd_item";


                                     $sql1 = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE  cmd_qty_id > '$from' AND cmd_qty_id <= '$to' AND cmd_status = '12' AND cat_id = '".$_REQUEST['type']."'
                                    GROUP BY cmd_code");


      $reportDate    = $to;


}else{

$sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE  cmd_qty_id > '$last' AND cat_id = '".$_REQUEST['type']."'
                                    GROUP BY cmd_item");



                                    $sql1 = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE  cmd_qty_id > '$last' AND cat_id = '".$_REQUEST['type']."'
                                    GROUP BY cmd_code");

                                      $reportDate    =  date('Y-m-d');


   }








      ?>

<br>	 <br>
   	 <button onclick =" printInvoice()"> Print </button>
     <button onclick="exportTableToExcel('sales', 'Sales of <?php echo $reportDate?>')">Export to Excel</button>
                 <div id = "content">

     <?php include '../holder/printHeader.php'?>

             <hr>

                        <h4><center><?php $type = $_REQUEST['type'];

                        if($type==1){
                            Echo "Restaurent";
                        }
                        if($type==2){
                            Echo "Bar";
                        }

                        if($type==32){
                            Echo "Coffee";
                        }
                        ?>  Report for <?php echo $reportDate ?></center><h4> <hr>

            <br>
            <br>
            <div id="sales" class="table-responsive">
                              <table  class="table table-striped">
                                <thead>
                                    <tr>

                                        <th>ITEM NAME</th>
                                        <th>ITEM DESCRIPTION</th>
                                        <th>PRICE</th>
                                        <th>QTY</th>

                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $i = 0;


                                    $total = 0;

                            		$sql->execute(array());

                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                                   $OrderCode = $fetch['cmd_code'];

                                   $amount = (int)$fetch['menu_price'] * (int)$fetch['totqty'];
                            	   $tottax = $amount* (int)$fetch['tax'];
                            	   $n = 1;
                                   $total =  $total + $amount;
                                 	?>
                                    <tr>

                                        <td><?php echo $fetch['menu_name']; ?></td>
                                        <td><?php echo $fetch['menu_desc']; ?></td>
                                        <td><?php echo number_format($fetch['menu_price']); ?></td>
                                        <td><?php echo $fetch['totqty']; ?></td>
                                        <td><?php echo number_format($amount);?></td>


                                    </tr>
                                    <?php
                            		    } ?>
                                           <tr>
                                        <th colspan="4">Grand Total</td>
                                        <th> <?php echo number_format($total)?> RWF</th>

                                    </tr>

                                        <?php

                            		}
                                    ?>






                                </tbody>
                            </table>



                            <?php


                                       $cash =  0;
                            		   $card = 0;
                            		   $momo = 0;
                            		   $credit= 0;
                                       $cheque=0;

                            		$sql1->execute(array());

                            		if($sql1->rowCount()){
                            		while($fetch = $sql1->fetch()){



                            		   $cash = $cash + getTotalPaidByMethod($fetch['cmd_code'],'01');
                            		   $card = $card + getTotalPaidByMethod($fetch['cmd_code'],'05');
                            		   $momo = $momo + getTotalPaidByMethod($fetch['cmd_code'],'06');
                            		   $credit= $credit + getTotalPaidByMethod($fetch['cmd_code'],'02');
                            		   $cheque= $cheque + getTotalPaidByMethod($fetch['cmd_code'],'04');




                            		}}else{
                            		    //echo $last;
                            		}





                            ?>




                                  <hr>
                        <br>  <br>  <br>  <br>



                        <?php include '../holder/printFooter.php'?>



                       </div>
                             </div>


                            <?php
                            if($n == 1):



$sql = "SELECT * FROM days ORDER BY id DESC LIMIT 1 ";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {

$lastDate = $row['date'];
$date = new DateTime($lastDate);
$date->modify('+1 day');
$lastDate = $date->format('Y-m-d');

  }
}

                            ?>

                            <?php
                            endif
                            ?>

        </div>
    </div>
</div>
</div>


<script> function printInvoice() {
$("#headerprint").show();
$("#printFooter").show();
$('#data-table-basic').removeAttr('id');
var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
$("#headerprint").hide();
$("#printFooter").hide();
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