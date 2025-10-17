<?php error_reporting(0);
if(!isset($_SESSION['from']) && !isset($_SESSION['to']) && !isset($_SESSION['item'])){
    $from = date('Y-m-d');
    $to = date("Y-m-d");
    $item = 'all';
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
   
   
function getItemPrice($id) {
    include '../inc/conn.php';

    // Get the item from tbl_items
    $itemQuery = "SELECT * FROM tbl_items WHERE item_id = '$id'";
    $itemResult = $conn->query($itemQuery);

    if ($itemResult && $itemResult->num_rows > 0) {
        $item = $itemResult->fetch_assoc();
        $defaultPrice = $item['price'];

        // Check tbl_progress for the latest new_price
        $progressQuery = "SELECT new_price FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1";
        $progressResult = $conn->query($progressQuery);

        if ($progressResult && $progressResult->num_rows > 0) {
            $progress = $progressResult->fetch_assoc();
            if ($progress['new_price'] > 0) {
                return $progress['new_price'];
            }
        }

        // Fallback to default item price
        return $defaultPrice;
    }

    // If item not found, return null or any fallback value you want
    return null;
}

 function getDep($id){
	
if($id==4){
    return "Kitchen";
}
if($id==5){
    return "BAR";
}

if($id==13){
    return "House Keeper";
}
}


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

function getDepTotal($id){
   $inAmounttotal = 0;
   
   
   if(!isset($_SESSION['from']) && !isset($_SESSION['to'])){
    $froms = date('Y-m-d');
    $tos = date("Y-m-d");
   }
   else{
       $froms = $_SESSION['from'];
       $tos = $_SESSION['to'];
      
   }
  
   include '../inc/conn.php';		
$sql = "SELECT * FROM tbl_requests INNER JOIN tbl_request_details ON tbl_requests.req_code=tbl_request_details.req_code WHERE department='$id' AND requested_date BETWEEN '$froms' AND '$tos' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    
    
    
     $inAmounttotal = $inAmounttotal + getItemPrice($row['items']) * $row['quantity'];
    
  }
}

return $inAmounttotal;

} 
    
     
                            
                           
    



   
?>
<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            
            <a href="?resto=ReportByCategory">Report By Category</a>  | <a href="?resto=ReportByDepertment">Report By Depertment</a>
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> Stock Report Per Day</strong></small></h2>
                        </div>
                     <hr>
                     
                       <div class="chart" >
                     <canvas id="myChart" width="400" height="200"></canvas>
                    </div>
            <form action="" method="POST">
                 <div class="row">
                 <label class="col-md-1 control-label" for=""><strong>Depertment </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                    <select class="form-control
                
            " id="mySelect" onchange="myFunction()">
                         <option value="0">Select Dep</option>
                        <option value="4">kitchen</option>
                        <option value="5">Bar</option>
                        <option value="13">House Keeping</option>
                           
                            </select>
                            
                            <script>
function myFunction() {
  var x = document.getElementById("mySelect").value;
  window.location='index?resto=ReportByDepertment&&selection='+x;
}
</script>
                    </div>
                   <label class="col-md-1 control-label" for=""><strong>Date From </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <input type="date" id="date_from" name="date_from" class="form-control">
                    </div>
                    <label class="col-md-1 control-label" for=""><strong>Date To </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <input type="date" id="date_to" name="date_to" class="form-control">
                    </div>
                   </div>
            </form>
            <!-- <div class="panel-title pull-right">
                            
    
             <a href="?resto=printStock&&selection=<?php echo $_REQUEST['selection'] ?>&&to=<?php echo $to?>&&from=<?php echo $from?>"  class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o"></i> PDF</a>
            </div> -->
            <div class="panel-title pull-right">
                    <a href="generate_stock_report_pdf.php?selection=<?= $_GET['selection'] ?? '' ?>&from=<?= $from ?>&to=<?= $to ?>" 
                       class="btn btn-success btn-sm" target="_blank">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </a>
            </div>
            <br>
            <br>
            <div class="table-responsive">
                <table id="data-table-basic" styles="border:2px solid #000" class="table table-striped">
                <thead>
                        <tr>
                            <th> Date <?php echo getDepTotal(4)?>kjj</th>
                               <th> Depertment</th>
                            <th> ITEM NAME </th>
                            <th> QTY </th>
                            <th> U.P </th>
                               <th> T.P </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        
                      
                         $inAmounttotal = 0;
                        $no = 0;
                        
                        
                        if($_REQUEST['selection']){
                            $DEP = $_REQUEST['selection'];
                        $result2 = $db->prepare("SELECT * FROM tbl_requests INNER JOIN tbl_request_details ON tbl_requests.req_code=tbl_request_details.req_code WHERE department='$DEP' AND requested_date BETWEEN '$from' AND '$to'");
                        
                        }else{
                               $result2 = $db->prepare("SELECT * FROM tbl_requests INNER JOIN tbl_request_details ON tbl_requests.req_code=tbl_request_details.req_code WHERE requested_date BETWEEN '$from' AND '$to' ");
                        }
                        
                        $result2->execute();    
                        
                        for($i=1;$rows = $result2->fetch(); $i++){
                            
                            $inAmounttotal = $inAmounttotal + getItemPrice($rows['items']) * $rows['quantity'];
                            
                            ?>
                            <tr class="record">
                               
                                <td><?php echo $rows['requested_date']?></td>
                                 <td><?php echo getDep($rows['department'])?></td>
                                <td><?php echo getItemName($rows['items']); ?></td>
                               
                           
                                
                                <td>
                                    <?php 
                                    echo $rows['quantity'];
                                    ?>
                                    
                                </td>
                                    <td>
                                    <?php 
                                    echo number_format(getItemPrice($rows['items']));
                                    ?>
                                    
                                </td>
                                
                                    <td>
                                    <?php 
                                    echo number_format(getItemPrice($rows['items']) * $rows['quantity']);
                                    ?>
                                    
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
 <tr>
     <td colspan='6'><b class="pull-right" style="margin-right:90px">Total: <?php echo number_format($inAmounttotal)?> RWF<b></td>
     </tr>
            
                    </tbody>
                </table>
                
             
        </div>
    </div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- OPTIONAL SCRIPTS -->
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['Kitchen', 'Bar', 'House Keeper'],
        datasets: [{
            label: 'Cost of Good Sold',
            backgroundColor: 'gray',
            borderColor:'blue',
            data: [<?php echo getDepTotal(4)?>, 
            <?php echo  getDepTotal(5)?>
            , <?php echo getDepTotal(13)?>, 
         
            ]
        }]
    },

    // Configuration options go here
    options: {}
});
    </script>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#date_to").change(function () {
            var from = $("#date_from").val();
            var to = $("#date_to").val();
            var item = $('#item').val();
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.post('load_stock_report.php',{item:item,from:from,to:to} , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
                location.reload();
            });
        });

    });
</script>