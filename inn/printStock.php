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

<?php

error_reporting(0);
$sql = $db->prepare("SELECT * from generated_report WHERE from_date='".$_REQUEST['s']."' AND  to_date='".$_REQUEST['to']."'  AND report_type='Stock'");
$sql->execute(array());
                                    
if($sql->rowCount()){
while($fetch = $sql->fetch()){
    
    $comment = $fetch['comment'];

}}else{
    error_reporting(0);
  $time = time();
$sql = "INSERT INTO `generated_report` (`id`, `report_type`, `generated_by`, `from_date`, `to_date`, `comment`, `create_at`) 
VALUES (NULL, 'Stock', '".$_SESSION['f_name']."', '".$_REQUEST['s']."', '".$_REQUEST['to']."', '', '$time');";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
//  echo "Error: " . $sql . "<br>" . $conn->error;
}  
}



if(isset($_POST['comment'])){
     error_reporting(0);
    $comment = $_POST['message'];
    
$sql = "UPDATE generated_report SET comment='$comment' WHERE from_date='".$_REQUEST['s']."' AND  to_date='".$_REQUEST['to']."' and report_type='Stock'";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Comment Added')</script>";
} else {
  //echo "Error updating record: " . $conn->error;
}
    
    
}

if(!isset($_SESSION['from']) && !isset($_SESSION['to']) && !isset($_SESSION['item'])){
    $from = date('Y-m-d');
    $to = date("Y-m-d");
    $item = '';
   }
   else{
       $from = $_SESSION['from'];
       $to = $_SESSION['to'];
       $item = $_SESSION['item'];

       $sqlware2 = $db->prepare("SELECT item_name FROM tbl_items WHERE item_id = '".$item."'");
       $sqlware2->execute();
       $getname2 = $sqlware2->fetch();
     // $itemname = $getname2['item_name'];
   }

date_default_timezone_set('Africa/Kigali');

//PDO Connection 
$db = new PDO('mysql:host=panatechrwanda.com;dbname=panatech_st_resto;charset=utf8', 'panatech_st_resto','panatech_st_resto');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->query('SET SESSION SQL_BIG_SELECTS=1');
$db->exec("SET SESSION sql_mode=''");
//2and PDO CONNECTION

    $db_username = 'panatech_st_resto';
	$db_password = 'panatech_st_resto';
	$conn = new PDO( 'mysql:host=panatechrwanda.com;dbname=panatech_st_resto', $db_username, $db_password );
	if(!$conn){
		die("Fatal Error: Connection Failed!");
	}
	

?>


<?php include '../inc/conn.php';

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
       
        <div class="row"> 
     <div class="col-md-3">
         </div>
     <div class="col-md-6"><form method="POST">
           <textarea class="form-control" name="message"></textarea>
           <br>
           <input type="submit" value="Post comment" class="btn btn-info" name="comment" placeholder="Any comment on this report? ">
           
           </form>
           </div>
           </div>
       
  <center>     <button onclick="printInvoice()">Print To PDF </button></center>
       
    <div  id="content" style="background-color:white;margin-top:50px;margin-left:10%;padding:20px;margin-right:10%;height:100%;padding-top:100px">
       <table border="0">
          <tr><td>
    <center>  <br><img width="75" height="54" src="data:image/jpg;base64,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"/>
     </center>
      </td>
      
      <td>
          <p style="padding-top: 6pt;padding-left: 128pt;text-indent: 0pt;line-height: 11pt;text-align: left;">
        <center> <?= $company_address ?><br>
        TEL: <?= $company_phone ?><br>
        TIN/VAT: <?= $company_tin ?></p>
</center>
      </td>
      
            <td>
          <center>   <p class="" style="padding-top: 6pt;text-indent: 0pt;text-align: center;">Day : <?php echo date('d')?></p></center>
            <center>   <p class="" style="padding-top: 6pt;text-indent: 0pt;text-align: center;">Month : <?php echo date('M')?></p></center>
            <center>   <p class="" style="padding-top: 6pt;text-indent: 0pt;text-align: center;">Year : <?php echo date('Y')?></p></center>
            </td>
      </tr></table>
      
      
          <table border="0">
          <tr><td>
    
      </td>
      
      <td>
          <p class="s1" style="padding-top: 6pt;text-indent: 0pt;text-align: center;border-width: thin;padding:20px">
 <center>STORE REPORT <?PHP ECHO $from ?> TO <?php echo $to ?>  </center>
     </p>
      </td>
      
            <td>
          
            </td>
      </tr></table>
      
      <br>     <br>    <br>    <br>
      
           <table id="" class="table table-striped">
                <thead>
                        <tr>
                            <th> Date</th>
                            <th> ITEM NAME </th>
                            <th> OPENING </th>
                            <th> NEW S. </th>
                            <th> QTY OUT </th>
                            <th> CLOSING </th>
                              <th> U.P </th>
                               <th> T.P </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        
                        $openning = 0;
                         $in = 0;
                        if($item != 'all'){
                        $result2 = $db->prepare("SELECT * FROM tbl_progress INNER JOIN tbl_items ON tbl_items.item_id=tbl_progress.item WHERE date BETWEEN '$from' AND '$to' AND item='".$item."' ");
                        $result2->execute();
                        }else{
                        $result2 = $db->prepare("SELECT * FROM tbl_progress INNER JOIN tbl_items ON tbl_items.item_id=tbl_progress.item WHERE date BETWEEN '$from' AND '$to' ");
                        $result2->execute();    
                        }
                        for($i=1;$rows = $result2->fetch(); $i++){
                            
                            
                            $openning =  getItemPrice($rows['item_id']) * $rows['last_qty'];
                            $in = $rows['new_price'] * $rows['in_qty'];
                            
                            
                            $tatalQty = $rows['last_qty'] + $rows['in_qty'];
                            
                            $avarage = $openning +  $in; 
                            $avarage = $avarage/$tatalQty;
                            
                            
                            ?>
                            <tr class="record">
                                <td><?php echo $rows['date']; ?></td>
                                <td><?php echo $rows['item_name']; ?></td>
                                <td>
                                    <?php 
                                    if($rows['last_qty'] == ''){
                                        echo "-";
                                    }
                                    else{
                                        echo $rows['last_qty']; 
                                    }
                                    ?>
                                    <br>
                                    Total: <?php echo $openning?>
                                </td>
                                <td>
                                    <?php 
                                    if($rows['in_qty'] == ''){
                                        echo "-";
                                    }
                                    else{
                                        echo $rows['in_qty']; 
                                    }
                                    ?>
                                     <br>
                                    Total: <?php echo $in?>
                                </td>
                                
                                <td>
                                    <?php 
                                    if($rows['out_qty'] == ''){
                                        echo "-";
                                    }
                                    else{
                                        echo $rows['out_qty']; 
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    echo $rows['end_qty'];
                                    ?>
                                    
                                </td>
                                    <td>
                                    <?php 
                                    echo number_format($avarage);
                                    ?>
                                    
                                </td>
                                
                                    <td>
                                    <?php 
                                    echo number_format($avarage * $rows['end_qty']);
                                    ?>
                                    
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>
                                    
                                    
                                    
                                       
                   <br>   <br>      <br>      <br>                    
                                     <?php if($comment){ ?>
   <H6>Comment by <?php echo $_SESSION['f_name'];?> <br><br> <?php echo $comment; ?> 
   
    <?php } ?></H6>
    <br>
    <H6>Printed by <?php echo $_SESSION['f_name'];?> at <?php echo date('Y-m-d h:i:s')?> </h6>                 
 
      
      
      
    
   </body>
</html>

<script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>




<!DOCTYPE html>