

    <style>
        a{
            text-decoration: none;
            color:black;

        }
        a:hover{
            cursor: pointer;
            color:blue
        }
    </style>





<?php  

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
       $itemname = $getname2['item_name'];
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



?>

	

<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            
            <a href="?resto=ReportByCategory">Report By Category</a>  | <a href="?resto=ReportByDepertment">Report By Depertment</a> | <a href="?resto=staff_report">Staff Report</a>
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> General Stock Report</strong></small></h2>
                        </div>
                     <hr>
            <form action="" method="POST">
                 <div class="row">
                 <label class="col-md-1 control-label" for=""><strong>Product </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                    <select class="form-control selectpicker" data-live-search="true" id="selectedItem">
                          <option value="0">ALL</option>
                                <?php 
                              
                                $sql = $db->prepare("SELECT * FROM tbl_items WHERE item_status = '1'");
                                $sql->execute();
                                while($row = $sql->fetch()){
                                ?>
                                <option value="<?php echo $row['item_id'];  ?>"><?php echo $row['item_name'];  ?></option>
                                <?php } ?>
                            </select>
                    </div>
                   <label class="col-md-1 control-label" for=""><strong>Date From </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <input type="date" id="date_from" name="date_from" class="form-control">
                    </div>
                    <label class="col-md-1 control-label" for=""><strong>Date To </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <input type="date" id="date_to" name="date_to" class="form-control">
                    </div>
                    
                      <div class="col-md-3">
                       <input type="button" id="checkBtn" value="Search">
                    </div>
            </form>
            <form method="POST" action="export_excel.php" target="_blank">
    <input type="hidden" name="item" value="<?php echo $item; ?>">
    <input type="hidden" name="from" value="<?php echo $from; ?>">
    <input type="hidden" name="to" value="<?php echo $to; ?>">
    <button type="submit">Export to Excel</button>
</form>
            <div class="panel-title pull-right">
                            
    
             <a hidden href="?resto=printStock&&s=<?php echo $from ?>&&to=<?php echo $to?>&&item=<?php echo $item?>"  class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o"></i> PDF</a>
            </div>
            <br>
            <br>
            <div class="table-responsive" id="reportDocument">
                
           <?php include '../holder/printHeader.php'?>
        
            
              <table id="reportTable" class="table table-striped" style="width:100%">
                
                  
                  
                <thead>
             <tr><td colspan='7'><center><h4> Stock Report </h4> </center></td></tr>
                        <tr>
                            
                            <th> ITEM NAME </th>
                            <th> OPENING </th>
                            <th> NEW S. </th>
                            <th> QTY OUT </th>
                            <th> CLOSING </th>
                              <th> U.P </th>
                               <th> T.P </th>
                        </tr>
                    </thead>
                    <tbody id="data-container">
     
            
                    </tbody>
                    <tbody id="total-container"></tbody>
                </table>
  
    
    
   
    
        </div>
          <button id="load-more">Load More</button>
         <button id="print" class="btn btn-info" >Print</button>
    </div>
</div>
</div>



    <script>
let page = 1;
let totalAppended = false; // flag to make sure we append total only once

function loadData(page) {
    document.getElementById('load-more').innerHTML = '';

    $.ajax({
        url: 'load_data.php',
        type: 'GET',
        data: { page: page },
        success: function(response) {
            if (response != 0) {
                // Extract and separate the total row (if exists)
                let responseHTML = $('<div>').html(response);
                let totalRow = responseHTML.find('#grand-total-row');

                // Remove total row from rest of content and append data rows
                responseHTML.find('#grand-total-row').remove();
                $('#data-container').append(responseHTML.html());

                // Append total only once
                if (totalRow.length && !totalAppended) {
                    $('#total-container').append(totalRow);
                    $('#grand-total-row').show();
                    totalAppended = true;
                }

                page++;
                // loadData(page); // keep loading
            } else {
                $('#load-more').hide();
                $('#print').show();
            }
        }
    });
}

loadData(page);

    </script>


 <script> 
 
 
 $('#print').click(function(){
     
     	 //alert('Print'); 
 $('#printHeader').show();




  var divContents = document.getElementById('reportDocument').innerHTML;
  var printWindow = window.open('', '', 'height=600,width=800');
  
  printWindow.document.write('<html><head><title>Print</title>');
  printWindow.document.write('<link rel="stylesheet" type="text/css" href="your-stylesheet.css">'); // Add your stylesheet if necessary
  printWindow.document.write('</head><body>');
  printWindow.document.write(divContents);
  printWindow.document.write('</body></html>');
  printWindow.document.close();
  printWindow.print();


     
     
     
     
 });
		 
		 
		 </script>	
	
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
$("#checkBtn").click(function(){
    var item = $('#selectedItem').val();
    var from = $('#date_from').val();
    var to = $('#date_to').val();

    var data = new FormData();
    data.append("item", item);
    data.append("from", from);
    data.append("to", to);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "closing.php");
    xhr.send(data);

    xhr.onload = function () {
        document.getElementById('data-container').innerHTML = this.responseText;
    }
});



$(document).ready(function () {
    // Trigger default report load
    $("#checkBtn").click();
});
</script>