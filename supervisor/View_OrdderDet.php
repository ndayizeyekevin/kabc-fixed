<?php
include '../inc/conn.php';




function getPrice($id){
  include '../inc/conn.php';


$sql = "SELECT menu_price FROM `menu` WHERE menu_id = '$id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

return $row['menu_price'];


}
}


}



function getcmditem($id){

include '../inc/conn.php';
$sql = "SELECT * FROM `menu` WHERE menu_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

return $row['cat_id'];
}
}

}

  function getcategory($id){

    include '../inc/conn.php';


$sql = "SELECT * FROM `menu` WHERE menu_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

return $row['cat_id'];
  }
}





  }


function getName($id){
  include '../inc/conn.php';


$sql = "SELECT  menu_name FROM `menu` WHERE menu_id = '$id'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

return $row['menu_name'];


}
}


}

if(isset($_POST['addtotable'])){


    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$table = $_POST['table'];
$OrderCode  = 0;

$sql = "SELECT * FROM tbl_cmd_qty where cmd_table_id  ='$table' and cmd_status !=12";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
$OrderCode = $row['cmd_code'];
  }
}



                                 $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd`  WHERE OrderCode = '".$_GET['c']."'

                                    ");
                                        $sql_rooms->execute();

                                        while($fetrooms = $sql_rooms->fetch()) {

                                            $reservat_id = $fetrooms['reservat_id'];
                                            $Serv_id = $fetrooms['Serv_id'];
                                            $company_id = $fetrooms['company_id'];
                                            $menu_id = $fetrooms['menu_id'];
                                            $status_id= $fetrooms['status_id'];
                                            $room_client = $fetrooms['room_client'];
                                            $dTrm = $fetrooms['dTrm'];
                                            if($OrderCode==0){
                                                $OrderCode = $fetrooms['OrderCode']."1";
                                            }



                                        }


    if(!$room_client){
        $room_client = 0;
    }


$table = $_POST['table'];
$sql = "INSERT INTO `tbl_cmd` (`id`, `reservat_id`, `discount`, `Serv_id`, `status_id`, `company_id`, `dTrm`, `OrderCode`, `client`, `menu_id`, `room_client`)
VALUES (NULL, '$reservat_id', NULL, '$Serv_id', '$status_id', '$company_id', '$dTrm', '$OrderCode', NULL, '$menu_id ', ' $room_client');";

if ($conn->query($sql) === TRUE) {



    if(isset($_POST['menu_id'])){
           $menu_id = $_POST['menu_id'];


         for($i=0; $i<count($menu_id); $i++){

			$menu_id[$i];
			$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_code` = '".$OrderCode."', cmd_table_id = '$table'  WHERE `cmd_qty_id` = '".$menu_id[$i]."' AND
			`cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			$msg = "Successfully Delivered!";
           // echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
		  }
    }



} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

}
?>

<style>
        /* Style for screen display */
        #printbar {
            width: 100%; /* Approximate width for 58mm thermal paper */
            font-family: Arial, sans-serif;
        }

        /* Remove styles & format properly for printing */
        @media print {
            body {
                visibility: hidden; /* Hide everything else */
            }

            #printbar {
                visibility: visible;
                width: 100%; /* Set to match POS printer width (or 80mm) */
                font-size: 15px; /* Small font for thermal printing */
                text-align: center; /* Center align for receipts */
                margin: 0;
                padding: 0;
            }

            /* Optional: Remove default margins and padding */
            @page {
                size: auto;
                margin: 0;
            }
        }
    </style>

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                   <div class="data-table-list">
                         <div class="basic-tb-hd">
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    </div>
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                  <?php
                    if(isset($_POST['ConfimVoid'])){




                                 $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd`  WHERE OrderCode = '".$_GET['c']."'

                                    ");
                                        $sql_rooms->execute();

                                        while($fetrooms = $sql_rooms->fetch()) {

                                            $reservat_id = $fetrooms['reservat_id'];
                                            $Serv_id = $fetrooms['Serv_id'];
                                            $company_id = $fetrooms['company_id'];
                                            $menu_id = $fetrooms['menu_id'];
                                            $status_id= $fetrooms['status_id'];
                                            $room_client = $fetrooms['room_client'];
                                            $dTrm = $fetrooms['dTrm'];
                                            $OrderCode = $fetrooms['OrderCode']."-".time();


                                        }


    if(!$room_client){
        $room_client = 0;
    }

    $voideddata = "";

    for ($i = 0; $i < count($_POST['id']); $i++) {
    $id = $_POST['id'][$i];
    $qty = $_POST['qtx'][$i];
    $cid = $_POST['qid'][$i];
    $currentqty = $_POST['nqtx'][$i];
    $remain = $currentqty - $qty;






$voideddata = $voideddata. '<h4>Item Name:'.getName($id).'</h4>
<h4>Quantity: '.$qty.'</h4>
<h4>Unity Price:'.number_format(getPrice($id)).'</h4>
<h4>Total Price:'.number_format(getPrice($id)).'</h4>';



// $time=time();

$sql = "INSERT INTO `voided_items` (`id`, `item`, `qty`, `table_id`, `servant`,order_code) VALUES (NULL, '$id', '$qty', '$reservat_id', '$Serv_id', '$OrderCode');";
if ($conn->query($sql) === TRUE) {

}else{

}

     if($remain==0){




   	$sql2 = "DELETE FROM  `tbl_cmd_qty` WHERE `cmd_item` = '$id' and `cmd_qty_id` = '$cid'  AND `cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();


     }else{

         	$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_qty` = '".$remain."' WHERE `cmd_item` = '$id' and `cmd_qty_id` = '$cid'  AND `cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			//$msg = "Successfully Delivered!";
     }

     }


?>
   <a class="btn btn-info" data-toggle="modal" data-target="#item" >Print</a>

        							     <!-- Modal -->
<div id="item" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Void Preview</h4>
      </div>
      <div class="modal-body" id="printbar">
<?php
$sql = "SELECT * FROM tbl_cmd WHERE OrderCode ='".$_GET['c']."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $no = $row["id"];
  }
}


    $tableno = $_GET['res'];
?>




<h1> <center>VOIDED</center></h1>
<BR>
<h4> <center>Date <?php echo date('Y-m-d H:i:s'); ?></center></h4>
<h4> <center>Invoice Number: <?php echo $no; ?></center></h4>
<h4> <center>Table Name: <?php echo $tableno; ?></center></h4>
<center>--------------------------------------------------------


<?php echo $voideddata; ?>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="idkit"  class="btn btn-info" onclick="printDiv('kit')">Print</button>
      </div>
    </div>

  </div>
</div>





<?php




    }


if(isset($_POST['void'])){

//echo "w";
                                include '../inc/conn.php';
                                $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd`  WHERE OrderCode = '".$_GET['c']."'");
                                        $sql_rooms->execute();
                                        while($fetrooms = $sql_rooms->fetch()) {
                                            $reservat_id = $fetrooms['reservat_id'];
                                            $Serv_id = $fetrooms['Serv_id'];
                                            $company_id = $fetrooms['company_id'];
                                            $menu_id = $fetrooms['menu_id'];
                                            $status_id= $fetrooms['status_id'];
                                            $room_client = $fetrooms['room_client'];
                                            $dTrm = $fetrooms['dTrm'];
                                            $OrderCode = $fetrooms['OrderCode']."-".time();
                                        }
    if(!$room_client){
        $room_client = 0;
    }
?>
<div style=" width:300px">
<center><form method="POST">
<p>Quantity of item write bellow will be voided</p>
 <?php $menu_id = $_POST['menu_id'];

       for($i=0; $i<count($menu_id); $i++){

$sql = "SELECT *,SUM(cmd_qty) AS qty FROM `tbl_cmd_qty` INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item WHERE tbl_cmd_qty.cmd_qty_id = '$menu_id[$i];' and tbl_cmd_qty.cmd_code = '".$_GET['c']."'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    ?>
     <?php echo getName($row['cmd_item']);?><br>
      <input type="hidden" name="qid[]" value="<?php echo $row['cmd_qty_id']?>"  class="form-control">
     <input type="hidden" name="id[]" value="<?php echo $row['cmd_item']?>"  class="form-control">
     <input type="text" name="qtx[]" value="<?php echo $row['qty']?>"  class="form-control">
     <input type="hidden" name="nqtx[]" value="<?php echo $row['qty']?>"  class="form-control">
 <?php 
}
}
}
?>
<br></br>
<input type="submit" name="ConfimVoid" value="Comfirm Void">
</form>

<?php
}
if(isset($_POST['separate'])){

//echo "w";
include '../inc/conn.php';
        $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd`  WHERE OrderCode = '".$_GET['c']."'");
        $sql_rooms->execute();
        while($fetrooms = $sql_rooms->fetch()) {
        $reservat_id = $fetrooms['reservat_id'];
        $Serv_id = $fetrooms['Serv_id'];
        $company_id = $fetrooms['company_id'];
        $menu_id = $fetrooms['menu_id'];
        $status_id= $fetrooms['status_id'];
        $room_client = $fetrooms['room_client'];
        $dTrm = $fetrooms['dTrm'];
        $OrderCode = $fetrooms['OrderCode']."-".time();
        }
        if(!$room_client){
            $room_client = 0;
        }
?>


<div style=" width:300px">
<center><form method="POST">
<p>Quantity of item write bellow will be moved to separate invoice</p>
 <?php $menu_id = $_POST['menu_id'];

       for($i=0; $i<count($menu_id); $i++){

$sql = "SELECT *,SUM(cmd_qty) AS qty FROM `tbl_cmd_qty`  INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_qty_id = '$menu_id[$i];' and tbl_cmd_qty.cmd_code = '".$_GET['c']."'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    ?>

     <?php echo $row['menu_name'];?><br>
      <input type="hidden" name="qid[]" value="<?php echo $row['cmd_qty_id']?>"  class="form-control">
     <input type="hidden" name="id[]" value="<?php echo $row['cmd_item']?>"  class="form-control">
     <input type="text" name="qtx[]" value="<?php echo $row['qty']?>"  class="form-control">
     <input type="hidden" name="nqtx[]" value="<?php echo $row['qty']?>"  class="form-control">


 <?php }
}



       }
?>

    <br></br>
  <input type="submit" name="saveseparate" value="Comfirm Separate">
</form>

<?php



}



if(isset($_POST['saveseparate'])){



                                 $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd`  WHERE OrderCode = '".$_GET['c']."'

                                    ");
                                        $sql_rooms->execute();

                                        while($fetrooms = $sql_rooms->fetch()) {

                                            $reservat_id = $fetrooms['reservat_id'];
                                            $Serv_id = $fetrooms['Serv_id'];
                                            $company_id = $fetrooms['company_id'];
                                            $menu_id = $fetrooms['menu_id'];
                                            $status_id= $fetrooms['status_id'];
                                            $room_client = $fetrooms['room_client'];
                                            $dTrm = $fetrooms['dTrm'];
                                            $OrderCode = $fetrooms['OrderCode']."-".'1';


                                        }


    if(!$room_client){
        $room_client = 0;
    }


$date =date('Y-m-d H:i:s');
$sql = "INSERT INTO `tbl_cmd` (`id`, `reservat_id`, `discount`, `Serv_id`, `status_id`, `company_id`, `dTrm`, `OrderCode`, `client`, `menu_id`, `room_client`)
VALUES (NULL, '$reservat_id', NULL, '$Serv_id', '$status_id', '$company_id', '$dTrm', '$OrderCode', NULL, '$menu_id ', ' $room_client');";
if ($conn->query($sql) === TRUE) {

}else{

}

    for ($i = 0; $i < count($_POST['id']); $i++) {
    $id = $_POST['id'][$i];
    $qty = $_POST['qtx'][$i];
    $cid = $_POST['qid'][$i];
    $currentqty = $_POST['nqtx'][$i];
    $remain = $currentqty - $qty;


$sql = "INSERT INTO `tbl_cmd_qty` (`cmd_qty_id`, `Serv_id`, `cmd_table_id`, `cmd_item`, `cmd_qty`, `discount`, `cmd_code`, `cmd_status`)
VALUES (NULL, '$Serv_id', '".$_GET['res']."', '$id', '$qty', '0', '$OrderCode', '13');";
if ($conn->query($sql) === TRUE) {

}else{

}

     if($remain==0){
	$sql2 = "DELETE FROM  `tbl_cmd_qty` WHERE `cmd_item` = '$id' and `cmd_qty_id` = '$cid'  AND `cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
     }else{

         	$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_qty` = '".$remain."' WHERE `cmd_item` = '$id' and `cmd_qty_id` = '$cid'  AND `cmd_code` = '".$_REQUEST['c']."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			//$msg = "Successfully Delivered!";
    }
 }
}

?>
</div>
</center>

                 </div>
                 </div>
                </div>


                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                         <div class="basic-tb-hd">
                          <h2><small><strong><i class="fa fa-list"></i> Order Details - Table No<?php echo $_GET['res']; ?></strong></small></h2>
                         </div>
                          <form method="POST">

        						<input class="pull-right" type="submit" name="separate" value="Separate invoice ">
        						<input class="pull-right" type="submit" name="void" value="Void Items" style="margin-right:20px; background-color:red,color:white">
        						<select name="table">
        						<option value="">Select table</option>
        						<?php


$sql = "SELECT * FROM tbl_tables where table_id !='".$_GET['res']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {


?>

	<option value="<?php echo $row['table_id'] ?>"> <?php echo $row['table_no'] ?></option>
<?php
  }
}



        						?>

        							<select>



        					<input type="submit" name="addtotable" value="Add to table ">


                         <hr>
                        <br>

                        <?php

                        $ebmdata="";

                                    $sql_rooms = $db->prepare("SELECT *,SUM(cmd_qty) AS qty FROM `tbl_cmd_qty`
                                    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_code = '".$_REQUEST['c']."'
                                    GROUP BY cmd_item
                                    ");
                                        $sql_rooms->execute();
                                        $i = 0;
                                        $totprice ??=0;
                                        while($fetrooms = $sql_rooms->fetch()) {

                                             $totprice = $totprice+($fetrooms['qty']*$fetrooms['menu_price']);
                                             $ebmdata = $ebmdata . '<tr><td align="left" style="padding-left: 0px;font-size:13px">'.$fetrooms['qty']." x ".$fetrooms['menu_name'].' '.$fetrooms['menu_price'].'. </td><td align="center">'.$fetrooms['qty']*$fetrooms['menu_price'].'</td></tr>';


                                        }

?>






                        <?php




                          $bon="";
                          $bonbar ="";
                          $printedIdbar = '';
                         $printedIdresto = '';
                         $printedIdcoffee = '';
                         $coffe="";
                                        $sql_rooms = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    WHERE cmd_code='".$_REQUEST['c']."'");
                                        $sql_rooms->execute();
                                        $i = 0;
                                        while($fetrooms = $sql_rooms->fetch()) {
                                            $i++;


                                               $mid = $fetrooms['cmd_item'];

                                               $no = $fetrooms['cmd_code'];

                                               $itemslistss = $fetrooms['cmd_qty_id'];

                                                 $printed = $fetrooms['printed'];



                                              $cat = getcategory($mid);


                                            if($cat==2){


                                               //  echo "<script>alert($cat)</script>";


                                               if($printed==null){
                                                $printedIdbar = $printedIdbar.$itemslistss.",";
                                                  $bonbar = $bonbar.getname($mid).'<br> Qty: '.$fetrooms['cmd_qty'].'<br> '.$fetrooms['message'].'<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';




                                               }
                                            }
                                               if($cat==1){
                                               if($printed==null){
                                                $printedIdresto = $printedIdresto.$itemslistss.",";


                                                $bon = $bon.getname($mid).'<br> Qty: '.$fetrooms['cmd_qty'].'<br> '.$fetrooms['message'].'<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';
                                            }}



                                               if($cat==32){

                                                    if($printed==null){
                                                $printedIdcoffee = $printedIdcoffee.$itemslistss.",";


                                                $coffe = $coffe.getname($mid).'<br> Qty: '.$fetrooms['cmd_qty'].'<br> '.$fetrooms['message'].'<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';


                                               }}






                                        }
                                        
                                        
$count ??= 0;
$serv ??= 0;
$totprices ??= 0;
$count ??= 0;
$no ??= 0;
$_SESSION['no'] =$no;
$_SESSION['tin'] =$count;
$_SESSION['printedIdbar'] =$printedIdbar;
$_SESSION['printedIdcoffee'] =$printedIdcoffee;
$_SESSION['printedIdresto'] =$printedIdresto;
$_SESSION['servant'] = $serv;
$_SESSION['ebmdata']= $ebmdata;
$_SESSION['bon']= $bon;
$_SESSION['bonbar']= $bonbar;
$_SESSION['coffe']= $coffe;
$_SESSION['total']= $totprice;
//$_SESSION['tax']= $totprice* 0.18 ;
$_SESSION['servant_name']= $_SESSION['f_name']. " ".$_SESSION['l_name'];


//echo $_SESSION['bonbar'];
?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item </th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Date-Time</th>

                                    </tr>
                                </thead>
                                <tbody>
                                 <?php
                                    $i = 0;
                                    $tot = array();
                            	  $sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    WHERE tbl_cmd_qty.cmd_code='".$_GET['c']."'");
                            		$sql->execute(array());
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                                    $tot[] = $fetch['cmd_status'];

                                   $OrderCode = $fetch['cmd_code'];
                                   $status = $fetch['cmd_status'];



                                   $GetStsqty = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$OrderCode."'");
                                   $GetStsqty->execute();
                                   $fstsqty = $GetStsqty->fetch();

                                   $GetStsmenu = $db->prepare("SELECT * FROM menu WHERE menu_id = '".$fetch['cmd_item']."'");
                                   $GetStsmenu->execute();
                                   $fstsmenu = $GetStsmenu->fetch();

                                   $rsv_ID = $fstsqty['reservat_id'];
                            		   $cat_id= $fstsmenu['cat_id'];
                            		   $subcat_ID = $fstsmenu['subcat_ID'];
                            		   $menu_id = $fetch['cmd_qty_id'];
                            		   $OrderCode = $fstsqty['OrderCode'];
                                   $serv = $fstsqty['Serv_id'];

                            		   $GetSts = $db->prepare("SELECT *FROM tbl_status WHERE id = '".$status."'");
                                    $GetSts->execute();
                                    $fsts = $GetSts->fetch();


                            		 $res_categ = $db->prepare("SELECT * FROM category WHERE cat_id ='".$cat_id."'");
                            		 $res_categ->execute();
                            		 $fCateg = $res_categ->fetch();
                            		 $Cname =  $fCateg['cat_name'];

                            		 $res_sub_categ = $db->prepare("SELECT *FROM subcategory WHERE subcat_id ='".$subcat_ID."'");
                            		 $res_sub_categ->execute();
                            		 $fsub_Categ = $res_sub_categ->fetch();
                            		 $Subname =  $fsub_Categ['subcat_name'];
                                 	?>
                                    <tr>
                                        <?php
                                        if($status == '5'){
                                            ?>


                                        <?php }else{?>
                                             <td>
                                          <input type="checkbox" name="menu_id[]" id="<?php echo $fetch['cmd_qty_id'];?>" value="<?php echo $fetch['cmd_qty_id'];?>"/>
                                          <label for="<?php echo $fetch['cmd_qty_id'];?>"></label>
                                        </td>
                                        <?php }?>
                                        <td><?php echo $fstsmenu['menu_name']; ?></td>
                                        <td>
                                        <?php
                                        echo $fetch['cmd_qty']
                                        ?>
                                      </td>
                                        <td><?php echo   getPrice($fetch['cmd_item']); ?></td>
                                        <td><?php echo   getPrice($fetch['cmd_item'])* $fetch['cmd_qty'];?></td>
                                        <td><?php echo $fetch['created_at'];?></td>
                                    </tr>
                                    <?php
                            		    }
                            		}
                                    ?>
                                </tbody>
                                    <?php 
                                    $ebmdata="";
                                    $sql_rooms = $db->prepare("SELECT *,SUM(cmd_qty) AS qty FROM `tbl_cmd_qty`
                                    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_code = '".$_GET['c']."'
                                    GROUP BY cmd_item
                                    ");
                                        $sql_rooms->execute();
                                        $i = 0;
                                        while($fetrooms = $sql_rooms->fetch()) {
                                            $i++;

                                          $totprices = $totprices+($fetrooms['qty']*$fetrooms['menu_price']);
                                          $ebmdata = $ebmdata . '<tr><td align="left" style="padding-left: 15px;">'.$fetrooms['menu_name'].'<br>'.$fetrooms['menu_price'].'.00x </td><td align="center"><br>'.$fetrooms['qty'].'.00</td><td align="center"><br>'.$fetrooms['qty']*$fetrooms['menu_price'].'</td></tr>';




                                        }


$_SESSION['tin'] =$count;
$_SESSION['servant'] = $serv;
$_SESSION['ebmdata']= $ebmdata;
$_SESSION['total']= $totprices;
//$_SESSION['tax']= $totprice* 0.18 ;


?>


        						<br>
        						</form>
    					</tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3"></th>
                                        <th colspan="1">Total: <?php echo number_format($totprice); ?></th>
                                        <th> <a href="https://saintpaul.gope.rw/reciept/pro-forma.php?ref=<?php echo $_REQUEST['c']?>" class="btn btn-secondary btn-sm" onclick="if(!confirm('Do you really generate invice?'))return false;else return true;"><i class="fa fa-step-invoice"></i>Invoice</a></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->

    <div id="update<?php echo $fetrooms['reservation_id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header text-center">
            <h2 class="modal-title"><i class="fa fa-pencil"></i> <?php echo $cpny_ID;?> Profile</h2>
          </div>
          <!-- END Modal Header -->

          <!-- Modal Body -->
          <div class="modal-body">

                <form id="settings_form" method="POST" enctype="multipart/form-data" class="form-horizontal form-bordered">

                  <fieldset style="margin-top: 10px;">

                     <div class="form-group">
                      <label class="col-md-4 control-label" for="">Firstname</label>
                      <div class="col-md-8">
                        <input type="text" id="cpname" name="cpname" class="form-control" value="<?php echo $cmp_full;?>" placeholder="Enter Company Name..">
                      </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Company Email</label>
                        <div class="col-md-8">
                            <input type="Email" id="cpemail" name="cpemail" class="form-control" value="<?php echo $cpny_email;?>" placeholder="Enter Email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Phone</label>
                        <div class="col-md-8">
                            <input type="text" id="cphone" name="cphone" class="form-control" value="<?php echo $cpny_phone;?>" placeholder="Company Phone">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Address</label>
                        <div class="col-md-8">
                            <input type="text" id="cpny_address" name="cpny_address" class="form-control" value="<?php echo $cpny_address;?>" placeholder="Company Phone">
                        </div>
                    </div>

                  </fieldset>
                    <div class="form-group form-actions" style="margin-top: 10px;">
                        <div class="col-xs-12 text-right">
                            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" name="btn-profile" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>
          <!-- END Modal Body -->
        </div>
      </div>
    </div>



    <script>



  function printDiv(id){

    //   const buttons = document.querySelectorAll('.btn');

    //   // Loop through each button and disable it
    //   buttons.forEach(button => {
    //     button.disabled = true;
    //   });

//       window.onafterprint = function () {
//     console.log("Printing completed!");
//   window.location.reload();

// };

     window.print();
 var xhr = new XMLHttpRequest();
  if(id=='kit'){

      var cmd_qty_id = document.getElementById('cmd_qty_id').value;
      const button = document.getElementById('idkit');
      button.disabled = true; // Disable the button

xhr.open("GET", "void.php?id=" + cmd_qty_id, true);
}


xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
        if(xhr.responseText==11){


           //alert(xhr.responseText);


            //window.print();
        }else{
         //   window.print();

        }
    }
};
xhr.send();
}
</script>

