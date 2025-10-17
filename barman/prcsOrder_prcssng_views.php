<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

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
           font-size: 12px; /* Small font for thermal printing */
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
<?php
// session_start();



$bondate =   date('Y-m-d h:i:s');

$client =   getClientOrder();


function getRoomClientDetails($id){

include '../inc/conn.php';


$sql = "SELECT * FROM `tbl_acc_booking` WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {
return $client  =   getGuestNames($row['guest_id'])."  <br> Room ".getRoomName(getBookedRoom($row['id']));
}
}else{
return "";
}






}



function getprice($id){

include '../inc/conn.php';


$sql = "SELECT * FROM `menu` WHERE menu_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

return $row['menu_price'];
}
}





}


function getname($id){

include '../inc/conn.php';


$sql = "SELECT * FROM `menu` WHERE menu_id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {

return $row['menu_name'];
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










function getClientOrder(){
include '../inc/conn.php';

$sql = "SELECT * FROM `tbl_cmd` WHERE OrderCode='".$_REQUEST['s']."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {
return $client  = getRoomClientDetails($row["room_client"]);
}
}else{
return "";
}

}


if(isset($_POST['addclientssnames'])){
$message = $_POST['clientssnames'];

$sqlupd = $db->prepare("UPDATE tbl_cmd SET room_client = '$message' WHERE OrderCode = '".$_REQUEST['s']."'");
$sqlupd->execute();

$msg = "User Added!";
//echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
}




if(isset($_POST['addClients'])){
$messageid = $_POST['messageid'];
$ordermessage = $_POST['ordermessage'];


$sqlupd = $db->prepare("UPDATE tbl_cmd_qty SET message = '$ordermessage' WHERE cmd_qty_id = '$messageid'");
$sqlupd->execute();


$msg = "Extra info addedd!";
//echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
}




if(isset($_POST['update'])){
$orderqty = $_POST['orderqty'];
$orderqtyid = $_POST['orderqtyid'];
$orderCode = $_POST['orderCode'];

for($i=0; $i<count($orderqtyid); $i++){
$sqlupd = $db->prepare("UPDATE tbl_cmd_qty SET cmd_qty = '".$orderqty[$i]."',cmd_status='13' WHERE cmd_qty_id = '".$orderqtyid[$i]."'");
$sqlupd->execute();
}

$sqlupd = $db->prepare("UPDATE tbl_cmd SET status_id = '13' WHERE `OrderCode` = '".$orderCode."'");
$sqlupd->execute();
$msg = "Order quantity updated!";
//echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
}

if(isset($_POST['confirm']))
{
try{
   $menu_id = $_POST['menu_id'];
   $reservation_ID = $_POST['reservation_ID'];
   $orderCode = $_POST['orderCode'];
   $table = $_GET['m'];

   $a = 8;

   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $sql = "UPDATE `tbl_cmd` SET `status_id` = '".$a."' WHERE `OrderCode` = '".$orderCode."'";
 $acidq = $db->prepare($sql);
 $acidq->execute();


    for($i=0; $i<count($menu_id); $i++){


 $sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_status` = '".$a."' WHERE `cmd_qty_id` = '".$menu_id[$i]."' AND
 `cmd_code` = '".$orderCode."'";
 $acidqq = $db->prepare($sql2);
 $acidqq->execute();
 $msg = "Successfully Delivered!";
      // echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
 }

}catch(PDOException $e){
echo $e->getMessage();
}
}

$id = $_GET['m'];
$code = $_GET['s'];

$stmts = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$code."'");
$stmts->execute();
$getsts = $stmts->fetch();
$status = $getsts['status_id'];

$get_rsv = $db->prepare("SELECT * FROM tbl_tables
WHERE table_id = '".$id."'");
$get_rsv->execute();
$getFetch = $get_rsv->fetch();

$GetSts = $db->prepare("SELECT *FROM tbl_status WHERE id = '".$status."'");
$GetSts->execute();
$fsts = $GetSts->fetch();

$sttus = $fsts['status_name'];

if($status == 13){
$snp = "waiting Menu order to be completed";
}elseif($status == 5){
$msg = "Order completed";
}elseif($status == 7){
$msg = "Order is in process";
}elseif($status == 8){
$msg = "Order delivered";
}

if(isset($_GET['id'])){
  $item = $_GET['id'];
  $m=$_GET['m'];
  $s=$_GET['s'];
  $st=$_GET['st'];

 $sql = $db->prepare("DELETE FROM tbl_cmd WHERE id = '".$item."'");
 $sql->execute();
 $sql2 = $db->prepare("DELETE FROM tbl_cmd_qty WHERE cmd_qty_id = '".$item."'");
 $sql2->execute();

 $msge = "Item Removed Successfully!";
 echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=prcsOrder_prcssng&m='.$m.'&s='.$s.'&st='.$st.'">';
}






if(isset($_POST['add']))
{
$Order_code=$_POST['orderCode'];
$reservID = $_POST['reservation_ID'];
$menu_id = $_POST['items'];
$quantity = $_POST['quantity'];
$today = date("Y-m-d H:i:s");
$sts = 7;



    $sql = $db->prepare("SELECT * FROM tbl_cmd WHERE reservat_id = '".$reservID."' AND OrderCode = '$Order_code' AND status_id != '12'");
    $sql->execute();
    $count = $sql->rowCount();
    if($count > 0){
        $get = $sql->fetch();
        $ordcode = $get['OrderCode'];

        for($i=0; $i<count($menu_id); $i++){
            $order = $conn->prepare("INSERT INTO `tbl_cmd`(`reservat_id`, `menu_id`, `Serv_id`, `status_id`,`company_id`, `dTrm`,`OrderCode`)
    VALUES('$reservID','$menu_id[$i]','$Oug_UserID','$sts','$cpny_ID','$today','$ordcode')");
    $order->execute();

    $sql = $db->prepare("INSERT INTO `tbl_cmd_qty`(`Serv_id`, `cmd_table_id`,`cmd_item`, `cmd_qty`, `cmd_code`, `cmd_status`)
    VALUES ('$Oug_UserID','$reservID','$menu_id[$i]','$quantity[$i]','$ordcode','13')");
    $sql->execute();

    $msg = "Successfully Ordered!";
    //echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=norder">';
        }
       }
}
?>
<?php
function fill_product($db){
$output= '';

$select = $db->prepare("SELECT * FROM `menu`
INNER JOIN category ON category.cat_id = menu.cat_id
INNER JOIN subcategory ON subcategory.subcat_id = menu.subcat_ID
ORDER BY menu.menu_name ASC");
$select->execute();
$result = $select->fetchAll();
foreach($result as $row){
$output.='<option value="'.$row['menu_id'].'">'.$row["menu_name"].' '.' ( '.$row["menu_price"].' ) </option>';
}

return $output;
}
?>
<style>
 .rmNo {
 text-align: center;
 text-transform: uppercase;
 color: #4CAF50;
}
</style>
<div class="normal-table-area">
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

            <div class="alert alert-warning alert-dismissable">
               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Sorry!</strong> Access Denied!<?php echo htmlentities($snp); ?>
             </div>
           <?php } ?>


           <form  method="POST">
             <input type="text"  placeholder="Client Name"  name="clientssnames" >
             <input type="submit"  value="Add client Name"  name="addclientssnames" >
             </form>


               <div class="normal-table-list mg-t-30">
                   <form action="" method="POST">
                   <div class="bsc-tbl-st" id="print">
                       <div class="basic-tb-hd">


                       <h2><small> <i class="fa fa-refresh"></i> <?php echo $sttus; ;?><span class="rmNo"> Table No. <?php echo $_SESSION['tableno'] = $getFetch['table_no'];?></span></small> <?php echo $_SESSION['f_name']?> </h2>

                         <?php

                         echo $client;


                         ?>


                       <button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Items" class="btn pull-right"><i class="fa fa-plus-circle"></i> Add Items</button>

                   </div>


                  <?php

                   $ebmdata="";

                               $sql_rooms = $db->prepare("SELECT *,SUM(cmd_qty) AS qty FROM `tbl_cmd_qty`
                               INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                               INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                               WHERE tbl_cmd_qty.cmd_code = '".$_GET['s']."' AND  tbl_cmd_qty.cmd_status=13
                               GROUP BY cmd_item
                               ");
                                   $sql_rooms->execute();
                                   $i = 0;
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
                               WHERE cmd_code='".$code."'");
                                   $sql_rooms->execute();
                                   $i = 0;
                                   while($fetrooms = $sql_rooms->fetch()) {
                                       $i++;


                                          $mid = $fetrooms['cmd_item'];

                                          $no = $fetrooms['cmd_code'];

                                          $itemslistss = $fetrooms['cmd_qty_id'];

                                            $printed = $fetrooms['printed'];
                                            $status = $fetrooms['cmd_status'];



                                         $cat = getcategory($mid);


                                       if($cat==2){
                                          //  echo "<script>alert($cat)</script>";
                                          if($printed==null && $status != '3'){
                                           $printedIdbar = $printedIdbar.$itemslistss.",";
                                             $bonbar = $bonbar.getname($mid).' '.getprice($mid).'<br> Qty: '.$fetrooms['cmd_qty'].'<br> '.$fetrooms['message'].'<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';
                                          }
                                       }
                                          if($cat==1){
                                          if($printed==null && $status != '3'){
                                           $printedIdresto = $printedIdresto.$itemslistss.",";


                                           $bon = $bon.getname($mid).' '.getprice($mid).'<br> Qty: '.$fetrooms['cmd_qty'].'<br> '.$fetrooms['message'].'<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';
                                       }}


                                          if($cat==32){

                                               if($printed==null && $status != '3'){
                                           $printedIdcoffee = $printedIdcoffee.$itemslistss.",";


                                           $coffe = $coffe.getname($mid).' '.getprice($mid).'<br>: '.$fetrooms['cmd_qty'].'<br> '.$fetrooms['message'].'<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';

                                          }}
                                   }


$_SESSION['no'] = $no;
// $_SESSION['tin'] = $count;
$_SESSION['printedIdbar'] =$printedIdbar;
$_SESSION['printedIdcoffee'] =$printedIdcoffee;
$_SESSION['printedIdresto'] =$printedIdresto;
$_SESSION['servant'] = $serv;
$_SESSION['ebmdata']= $ebmdata;
$_SESSION['bon']= $bon;
$_SESSION['bonbar']= $bonbar;
$_SESSION['coffe']= $coffe;
// $_SESSION['total']= $totprice;
//$_SESSION['tax']= $totprice* 0.18 ;
$_SESSION['servant_name']= $_SESSION['f_name']. " ".$_SESSION['l_name'];


//echo $_SESSION['bonbar'];
?>
                       <table class="table table-striped">
                           <thead>
                               <tr>
                                   <th>#</th>
                                   <th>Menu Order</th>
                                   <th>Quantity</th>
                                   <th>Category</th>
                                   <th>Sub-Category</th>
                                   <th>Date-Time</th>
                                   <th>Status</th>
                               </tr>
                           </thead>
                           <tbody>
                            <?php
                               $i = 0;
                               $tot = array();
                           $sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                               WHERE tbl_cmd_qty.cmd_code='".$code."'");
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
                                   <td>
                                     <input type="checkbox" name="menu_id[]" id="<?php echo $menu_id;?>" value="<?php echo $menu_id;?>"/>
                                     <label for="<?php echo $menu_id;?>"></label>
                                   </td>

                                   <?php }else{?>
                                   <td><?php echo $i;?></td>
                                   <?php }?>
                                   <td><?php echo $fstsmenu['menu_name']; ?></td>
                                   <td>
                                   <?php
                                   if($status == '3'){ ?>
                                   <input type="number" name="orderqty[]" value="<?php echo $fetch['cmd_qty']; ?>" class="form-control">
                                   <input type="hidden" name="orderqtyid[]" value="<?php echo $fetch['cmd_qty_id']; ?>" class="form-control">
                                   <?php
                                   }else{
                                     echo $fetch['cmd_qty'];
                                   }
                                   ?>
                                 </td>
                                   <td><?php echo $Cname;?></td>
                                   <td><?php echo $Subname;?></td>
                                   <td><?php echo $fetch['created_at'];?></td>
                                   <td>
                                   <?php
                                   if($status == '3'){
                                       $bullet ='<i class="fa fa-circle" aria-hidden="true" style="color: #dd5252;"></i>';
                                     $text = $fsts['status_name'];
                                     echo $bullet." ".$text;
                                     ?>
                                     <a href="?resto=prcsOrder_prcssng&m=<?php echo $_GET['m'] ?>&s=<?php echo $_GET['s']; ?>&id=<?php echo $fetch['cmd_qty_id']; ?>&st=<?php echo $_GET['st']; ?>" class="btn btn-link btn-sm" style="color:#e46a76;" onclick="if(!confirm('Do you really want to remove this item on Order?'))return false;else return true;"><i class="fa fa-close"></i> Remove</a>
                                     <?php
                                   }
                                   elseif($status == '5'){
                                     $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
                                     $text = $fsts['status_name'];
                                     echo $bullet." ".$text;
                                   }
                                   elseif($status == '7'){
                                     $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
                                     $text = $fsts['status_name'];
                                     echo $bullet." ".$text;
                                   }
                                   elseif($status == '8'){
                                     $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
                                     $text = $fsts['status_name'];
                                     echo $bullet." ".$text;
                                   }
                                   elseif($status == '13'){
                                     $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
                                     $text = $fsts['status_name'];
                                     echo $bullet." ".$text;
                                   }
                                   ?>
                              <button type="button" data-toggle="modal"  data-target="#roomclient<?php echo $menu_id?>" data-placement="left" title="" class="btn pull-right"><i class="fa fa-plus-circle"></i> Add extre info</button>


                                     <div class="modal fade" id="roomclient<?php echo $menu_id?>" role="dialog">
   <div class="modal-dialog">
   <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         <h3 class="modal-title">Extra info</h3>
       </div>
       <div class="modal-body">
           <form method="POST">
       <div class="row">
       <div class="form-group">
           <div class="col-md-12">
            <input type="text" name="messageid" value="<?php echo $menu_id?>">
             <textarea name="ordermessage"  class="form-control" ></textarea>

       </div>
       </div>
        <button type="submit" name="addClients" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save </button>
       </div>

       </form>
       </div>
   </div>
</div>
</div>


                                   </td>

                               </tr>
                               <?php
                               }
                           }
                               ?>
                           </tbody>
                       </table>
                   </div>
                   <input type="hidden" name="orderCode" value="<?php echo $OrderCode;?>">
                   <input type="hidden" name="reservation_ID" value="<?php echo $id;?>">
                   <br/>
                   <?php
                   // if($_GET['st'] != '8' && $_GET['st'] != '7' && $_GET['st'] != '3' && $_GET['st'] != '13')
                   if(in_array('5',$tot))
                   {
                     ?>
                   <button type="submit" name="confirm" id="confirm" class="btn btn-info btn-sm" style="border-radius: 5px;" onclick="if(!confirm('Do you really want to Deliver?'))return false;else return true;"><i class="fa fa-thumbs-up"></i> Deliver </button>
                   <?php } ?>
                   <?php if($_GET['st']== '3'){ ?>
                   <button type="submit" name="update" id="update" class="btn btn-primary btn-sm" style="border-radius: 5px;" onclick="if(!confirm('Do you really want to update order quantity?'))return false;else return true;"><i class="fa fa-edit"></i> Confirm Order Quantity</button>
                   <?php } ?>
                   <a href="?resto=lorder" class="btn btn-secondary btn-sm" onclick="if(!confirm('Do you really want to go back?'))return false;else return true;"><i class="fa fa-step-backward"></i> Back</a>
                   <a  target="_blank" href="https://saintpaul.gope.rw/reciept/pro-forma.php?ref=<?php echo $_REQUEST['s']?>" class="btn btn-secondary btn;-sm" onclick="if(!confirm('Do you really generate invice?'))return false;else return true;"><i class="fa fa-step-invoice"></i>Invoice</a>

                    <?php if($bonbar){?>
                  <button type="button" class="btn btn-info " data-toggle="modal" data-target="#myModal">Bar</button>
                     <?php } ?>
                   <?php if($bon){?>
                 <button type="button" class="btn btn-info " data-toggle="modal" data-target="#myModalKitechen">Kitchen</button>
                  <?php } ?>

                  <?php if($coffe){?>
                 <button type="button" class="btn btn-info " data-toggle="modal" data-target="#myModalCoffe">Coffee Shop</button>
                  <?php } ?>
                 </form>






<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
 <div class="modal-header">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h3 class="modal-title">Bon Preview</h3>
 </div>
 <div class="modal-body" id="printbar">
<?php    $html = '';
// Sample data from session (modify this with actual session data)
$bonbar = $_SESSION['bonbar'];
$printedIdresto = $_SESSION['printedIdbar'];
$tax = $_SESSION['total'] * 18/100;
$total = $_SESSION['total'];
$tin = $_SESSION['tin'];
$no = $_SESSION['no'];
$table =  $_SESSION['no'];
$servant = $_SESSION['servant_name'];
$tableno = $_SESSION['tableno'];



include  '../inc/conn.php';


$sql = "SELECT * FROM tbl_cmd WHERE OrderCode = '$no'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {
$ino = $row["id"];
}
}


$img = '<img src="https://saintpaul.gope.rw/img/logo.png" style="width: 100px;height: 100px;">';

echo $html .= '<div id="container" style="width: 100%; border: 0px solid black; margin: 0;">
<table border="0" align="center" width="100%">


<tr>
   <td align="center" style="display: grid;">
      <h3>Date: '.$bondate.'</h3>
   </td>
</tr>
<tr>
   <td align="center" style="display: grid;">
      <h3>Order Number: '.$ino.'</h3>
   </td>
</tr>



<tr>
   <td align="center" style="display: grid;">
      <h3> Table  '.$tableno.'</h3>
   </td>
</tr>
<tr>
   <td align="center" style="display: grid;">
      <h3> BON DE COMMANDE (BAR)</h3>
   </td>
</tr>
<tr>
   <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>
<h3>
'.$bonbar.'

</h3>


<table border="0"  align="center" width="100%">
<tr>
  <td align="center"><h3>Requested by:  '.$servant.'<h3></td>
</tr>
</table>

</div>';
?>




 </div>
 <div class="modal-footer">
   <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
   <button type="button" id="idbar"  class="btn btn-info" onclick="printDiv('bar')">Print</button>
 </div>
</div>

</div>
</div>
















<!-- Modal -->
<div id="myModalKitechen" class="modal fade" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
 <div class="modal-header">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h3 class="modal-title">Bon kitchen Preview</h3>
 </div>
 <div class="modal-body" id="printbar">
<?php   $html = '';
// Sample data from session (modify this with actual session data)
$bon = $_SESSION['bon'];
$tax = $_SESSION['total'] * 18/100;
$total = $_SESSION['total'];
$no = $_SESSION['no'];
$printedIdresto = $_SESSION['printedIdresto'];
$tin = $_SESSION['tin'];
$servant = $_SESSION['servant_name'];
$tableno = $_SESSION['tableno'];

include  '../inc/conn.php';

//$printedIdresto = substr($printedIdresto, 0, -1);

$printedIdresto = substr($printedIdresto, 0, -1);


// $sql = "UPDATE tbl_cmd_qty SET printed = '1' WHERE  cmd_qty_id IN ($printedIdresto)";

// if ($conn->query($sql) === TRUE) {
//  // echo "Record updated successfully";
// } else {
//  // echo "Error updating record: " . $conn->error;
// }



$img = '<img src="https://saintpaul.gope.rw/img/logo.png" style="width: 100px;height: 100px;">';

echo $html .= '<div id="container" style="width: 100%; border: 0px solid black; margin: 0;">
<table border="0" align="center" width="100%">


<tr>
<td align="center" style="display: grid;">
<h3>Date: '.$bondate.'</h3>
</td>
</tr>
<tr>
<td align="center" style="display: grid;">
<h3>Order Number: '.$ino.'</h3>
</td>
</tr>

<tr>
   <td align="center" style="display: grid;">
      <h3> Table  '.$tableno.'</h3>
   </td>
</tr>

<tr>
   <td align="center" style="display: grid;">
      <h3> BON DE COMMANDE (KITCHEN) </h3>
   </td>
</tr>
<tr>
   <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>
<h3>
'.$bon.'

</h3>

<table border="0"  align="center" width="100%">
<tr>
   <td align="center"><h3>Requested by:  '.$servant.'<h3></td>
</tr>
</table>

</div>';
?>




 </div>
 <div class="modal-footer">
   <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
   <button type="button" id="idkit"  class="btn btn-info" onclick="printDiv('kit')">Print</button>
 </div>
</div>

</div>
</div>







<!-- Modal -->
<div id="myModalCoffe" class="modal fade" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
 <div class="modal-header">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h3 class="modal-title">Bon Coffee shop Preview</h3>
 </div>
 <div class="modal-body" id="printbar">
<?php   $html = '';
// Sample data from session (modify this with actual session data)
$coffe = $_SESSION['coffe'];
$tax = $_SESSION['total'] * 18/100;
$total = $_SESSION['total'];
$no = $_SESSION['no'];
$printedIdCoffee = $_SESSION['printedIdCoffee'];
$tin = $_SESSION['tin'];
$servant = $_SESSION['servant_name'];
$tableno = $_SESSION['tableno'];

include  '../inc/conn.php';




$img = '<img src="https://saintpaul.gope.rw/img/logo.png" style="width: 100px;height: 100px;">';

echo $html .= '<div id="container" style="width: 100%; border: 0px solid black; margin: 0;">
<table border="0" align="center" width="100%">


<tr>
<td align="center" style="display: grid;">
<h3>Date: '.$bondate.'</h3>
</td>
</tr>
<tr>
<td align="center" style="display: grid;">
<h3>Order Number: '.$ino.'</h3>
</td>
</tr>

<tr>
   <td align="center" style="display: grid;">
      <h3> Table  '.$tableno.'</h3>
   </td>
</tr>

<tr>
   <td align="center" style="display: grid;">
      <h3> BON DE COMMANDE (Coffee shop) </h3>
   </td>
</tr>
<tr>
   <td><hr style="border: none; border-top: 1px dashed black; width: 80%;" /></td>
</tr>
</table>
<h3>
'.$coffe.'

</h3>

<table border="0"  align="center" width="100%">
<tr>
<td align="center"><h3>Requested by:  '.$servant.'<h3></td>
</tr>
</table>

</div>';
?>




 </div>
 <div class="modal-footer">
   <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
   <button type="button"  id="idcoffee" class="btn btn-info" onclick="printDiv('coffee')">Print</button>
 </div>
</div>

</div>
</div>







                          <div class="card mb-4" id="invoice" hidden>
                               <div class="card-header d-flex justify-content-between align-items-center">


                               </div>
                               <div class="card-body">
                                   <!-- Venues List -->


                 <div class="col-md-6">
                <h5 class="mb-0">  <center>Invoice</center></h5>
     <center><img src="https://saintpaul.gope.rw/img/logo.png" style="width:60px">


               </center>
               <center>
               <P>St Paul
               <br>Kigali Rwanda
               <br>+250 78 12 000 00
               <br>wwww.yourwebsite.com</p>
               </center>
               ------------------------------------------

                      <table>
                     <?php echo $ebmdata ?>
                     </table>


               <div class="row">
               <div class="col-md-6">
               <p>VAT (18 %)</p>
               </div>

               <div class="col-md-6">
               <p class="pull-end"> RWF</p>
               </div>
               </div>


                               <div class="row">
               <div class="col-md-6">
               <p>TOTAL</p>
               </div>

               <div class="col-md-6">
               <p><?php echo number_format($no + $booking_amount);?> RWF</p>
               </div>
               </div>
               -------------------------------------------
<p>Date: <?php echo date("d-m-Y H:I")?></p>

               </div>







                                   <!-- Pagination -->
                                   <nav class="mt-2" aria-label="Page navigation">
                                       <ul class="pagination" id="pagination">
                                           <!-- Pagination links will be populated here -->
                                       </ul>
                                   </nav>
                               </div>
                           </div>








               </div>
           </div>
       </div>
   </div>
</div>





<div class="modal fade" id="myModalone" role="dialog">
   <div class="modal-dialog">
   <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         <h3 class="modal-title">Add Items</h3>
       </div>
       <div class="modal-body">
           <form action="" enctype="multipart/form-data" method="POST">
       <div class="row">
       <div class="form-group">
           <div class="col-md-12">
               <input type="hidden" name="orderCode" value="<?php echo $OrderCode;?>">
               <input type="hidden" name="reservation_ID" value="<?php echo $id;?>">
               <input type="hidden" name="serv_ID" value="<?php echo $serv;?>">
            <table class="table table-border" id="myOrder">
           <thead>
               <tr>
                   <th>Item Name</th>
                   <th>Quantity</th>
                   <th>
                     <button type="button" name="addOrder" class="btn btn-success btn-sm btn_addOrder" required><span>
                       <i class="fa fa-plus"></i>
                     </span></button>
                   </th>
               </tr>

           </thead>
           <tbody>

           </tbody>
         </table>
       </div>
       </div>
       <div class="form-group">
       <div class="form-actions col-md-12">
           <br />
           <center>
             <button type="submit" name="add" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
             <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>
           </center>
       </div>
       </div>
       </div>

       </form>
       </div>
   </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


<script>



function printDiv(id){

 const buttons = document.querySelectorAll('.btn');
 // Loop through each button and disable it
 buttons.forEach(button => {
   button.disabled = true;
 });

 window.onafterprint = function () {
console.log("Printing completed!");
// window.location.reload();
buttons.forEach(button => {
   button.enabled = true;
 });

};


var xhr = new XMLHttpRequest();
if(id=='bar'){

 const button = document.getElementById('idbar');
 button.disabled = true; // Disable the button

xhr.open("GET", "printbar.php", true);
}


if(id=='kit'){
 const button = document.getElementById('idkit');
 button.disabled = true; // Disable the button
xhr.open("GET", "printedkitchen.php", true);
}

if(id=='coffee'){
 const button = document.getElementById('idcoffee');
 button.disabled = true; // Disable the button
xhr.open("GET", "printcoffe.php", true);
}

xhr.onreadystatechange = function () {
if (xhr.readyState == 4 && xhr.status == 200) {
   if(xhr.responseText==1){
     window.print();
//alert(xhr.responseText);
       //window.print();
   }else{
    //   window.print();

   }
}
};
xhr.send();
}
$(document).ready(function(){
 $(document).on('click','.btn_addOrder', function(){
   var html='';
   html+='<tr>';
   html+='<td><select class="form-control selectpicker productid" data-live-search="true" name="items[]" required><option value="">--Select Item--</option><?php
   echo fill_product($db)?></select></td>';
   html+='<td><input autocomplete="off" type="number" class="form-control quantity" name="quantity[]" required placeholder="0"></td>';
   html+='<td><button type="button" name="remove" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-remove"></i></button></td>'

   $('#myOrder').append(html);

   $('.productid').on('change', function(e){
     var productid = this.value;
     var tr=$(this).parent().parent();
     $.ajax({
       url:"getproduct.php",
       method:"get",
       data:{id:productid},
       success:function(data){
         $("#btn").html('<button type="submit" class="btn btn-success" name="add">Request</button>');
         tr.find(".quantity").val();
         tr.find("#unit").text(data["unit_name"]);
       }
     })
   })
    $('.selectpicker').selectpicker();
 })

 $(document).on('click','.btn-remove', function(){
   $(this).closest('tr').remove();
   calculate(0,0);
   $("#paid").val(0);
 })
});
</script>