<?php 
error_reporting(0);
// ini_set('display_errors', 1);
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

 function getDep($id){
include '../inc/conn.php';		
$sql = "SELECT * FROM category where cat_id='$id' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    return $row['cat_name'];
  }
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




   
?>
<style>
    .no-print { }
    .print-header { display: none; }
    .signature-section { display: none; }
    @media print {
        body * { visibility: hidden !important; }
        .print-header, .print-header * { visibility: visible !important; }
        .table-responsive, .table-responsive * { visibility: visible !important; }
        .signature-section, .signature-section * { visibility: visible !important; }
        .print-header {
            display: block;
            text-align: center;
            margin-bottom: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #fff;
            z-index: 9999;
        }
        .table-responsive { position: relative;
            top:0;
             margin-top: 0 !important; }
        .signature-section { display: block; margin-top: 40px; }
        .signature-row { display: flex; justify-content: space-between; gap: 20px; }
        .signature-box { width: 32%; text-align: center; }
        .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
        .table { font-size: 12px; }
    }
    .print-actions { text-align: right; margin: 10px 0; }
</style>

<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            
            <a href="?resto=ReportByCategory" class='btn btn-outline-success'>Report By Category</a>  | <a href="?resto=ReportByDepertment" class='btn btn-outline-success'>Report By Depertment</a> | <a href="?resto=staff_report" class='btn btn-outline-success'>Staff Report</a> | <a href="?resto=baqueting_report" class='btn btn-outline-success'>Baqueting</a>
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> Stock Report Per Day</strong></small></h2>
                        </div>
                     <hr>
            <form action="" method="POST" class="no-print">
                 <div class="row">
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
                            
    
             <a href="?resto=printStock&&s=<?php echo $from ?>&&to=<?php echo $to?>&&item=<?php echo $item?>"  class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o" hidden></i> PDF</a>
            </div> -->
            <!-- PDF replaced by in-page print -->
            <div class="print-actions no-print">
                <button type="button" class="btn btn-success btn-sm" onclick="printCategoryReport()">
                    <i class="fa fa-print"></i> Print Report
                </button>
            </div>
            <!-- <a href="generate_stock_pdf.php?s=<?php echo $from ?>&to=<?php echo $to?>&item=<?php echo $item?>" class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o"></i> PDF</a> -->
            <br>
            <br>
            <!-- Print-only header with logo and company details -->
            <div class="print-header">
                <div style="text-align:center;">
                    <img src="../img/logo.png" alt="Company Logo" style="height:80px; margin-bottom:10px;">
                    <h2 style="margin:0;">Saint Paul Hotel</h2>
                    <div style="font-size:16px; margin-bottom:5px;">TIN: 123456789 | Tel: +250 788 123 456 | Email: info@saintpaulhotel.com</div>
                    <div style="font-size:16px; margin-bottom:5px;">Address: Kigali, Rwanda</div>
                    <h3 style="margin:10px 0 0 0;">Cost Of Goods Sold Report By Category</h3>
                    <div style="font-size:15px; margin-bottom:5px;">Date Range: <?php echo htmlspecialchars($from); ?> to <?php echo htmlspecialchars($to); ?></div>
                    <div style="font-size:13px;">Generated on: <?php echo date('d-m-Y H:i:s'); ?></div>
                </div>
            </div>
            <div class="table-responsive">
                <table border="2" id="data-table-basic" class="table table-striped">
                <thead>
                        <tr>
                            <th> Date</th>
                            <th> Category</th>
                            <th> ITEM NAME </th>
                            <th> QTY </th>
                            <th> U.P </th>
                            <th> T.P </th>
                        </tr>
                    </thead>
                    <tbody>

<?php
$category_data = [];
$inAmounttotal = 0;
$qtyy = 0;

// Correct query that includes cat_id
$result2 = $db->prepare("SELECT tbl_requests.requested_date, tbl_items.cat_id, tbl_items.item_id, tbl_items.item_name,
           tbl_items.price, tbl_request_details.quantity, SUM(tbl_request_details.quantity) AS total_qqty
    FROM tbl_requests INNER JOIN tbl_request_details ON tbl_requests.req_code = tbl_request_details.req_code
    INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id 
    WHERE DATE(tbl_requests.requested_date) >= ? AND DATE(tbl_requests.requested_date) <= ? AND tbl_requests.status !=3 GROUP BY tbl_items.cat_id, tbl_items.item_id");
$result2->execute([$from, $to]);

while ($row = $result2->fetch()) {
    $cat_id = $row['cat_id'];
    if (!isset($category_data[$cat_id])) {
        $category_data[$cat_id] = [
            'cat_name' => getDep($cat_id),
            'items' => [],
            'subtotal' => 0,
        ];
    }

    // $unit_price = $row['price'];
    $unit_price = StoreController::getUnitePrice($db, $row['item_id']);
    $total_price = $unit_price * $row['total_qqty'];
    // $inAmounttotal += $total_price;
    $qtyy += $row['total_qqty'];
    $category_data[$cat_id]['subtotal'] += $total_price;

    $category_data[$cat_id]['items'][] = [
        'requested_date' => $row['requested_date'],
        'item_name' => $row['item_name'],
        'quantity' => $row['total_qqty'],
        'unit_price' => $unit_price,
        'total_price' => $total_price,
    ];
    
    
}

// Output the grouped table
foreach ($category_data as $cat) {
    echo "<tr><td colspan='6' style='background:#f0f0f0;'><strong>Category: {$cat['cat_name']}</strong></td></tr>";
    foreach ($cat['items'] as $item) {
        $inAmounttotal += (float)$item['total_price'];
        echo "<tr class='record'>
            <td>{$item['requested_date']}</td>
            <td>{$cat['cat_name']}</td>
            <td>{$item['item_name']}</td>
            <td>{$item['quantity']}</td>
            <td>" . number_format((float)$item['unit_price'],2) . "</td>
            <td>" . number_format((float)$item['total_price'],2) . "</td>
        </tr>";
    }
    echo "<tr><td colspan='6' class='text-right'><b>Subtotal for {$cat['cat_name']}:" . number_format($cat['subtotal']) . " RWF</b></td></tr>";
}

echo "<tr><td colspan='3' class='text-left'><b>TOTAL: </b></td>  </b></td><td><b> $qtyy </b></td> <td colspan='2' class='text-right'><b>" . number_format((float)$inAmounttotal,2) . " RWF</b></td></tr>";
?>
 <!--<tr>-->
 <!--    <td colspan='6'><b class="pull-right" style="margin-right:90px">Total: <?php echo number_format($inAmounttotal)?> RWF<b></td>-->
 <!--    </tr>-->
            
                    </tbody>
                </table>

            <?php
            // Determine the current user's full name based on logged-in user ID
            $printedBy = '';
            try {
                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                    $stmtPB = $db->prepare("SELECT u.f_name, u.l_name FROM tbl_users u INNER JOIN tbl_user_log l ON u.user_id = l.user_id WHERE l.user_id = ? LIMIT 1");
                    $stmtPB->execute([$_SESSION['user_id']]);
                    $rowPB = $stmtPB->fetch(PDO::FETCH_ASSOC);
                    if ($rowPB) {
                        $printedBy = trim(($rowPB['f_name'] ?? '') . ' ' . ($rowPB['l_name'] ?? ''));
                    }
                }
            } catch (Exception $e) {
                // ignore and fallback below
            }
            if ($printedBy === '') {
                if (isset($_SESSION['user_fullname'])) { $printedBy = $_SESSION['user_fullname']; }
                elseif (isset($_SESSION['fullname'])) { $printedBy = $_SESSION['fullname']; }
                elseif (isset($_SESSION['name'])) { $printedBy = $_SESSION['name']; }
                elseif (isset($_SESSION['username'])) { $printedBy = $_SESSION['username']; }
                elseif (isset($_SESSION['f_name']) || isset($_SESSION['l_name'])) { $printedBy = trim(($_SESSION['f_name'] ?? '') . ' ' . ($_SESSION['l_name'] ?? '')); }
            }
            ?>

            <!-- Print-only signature footer placed right after the table -->
            <div class="signature-section">
                <div class="signature-row">
                    <div class="signature-box" style="text-align:left;">
                        <strong>Printed by:</strong><br>
                        <?php echo htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                        <div class="sig-line"></div>
                        <small>Name & Signature</small>
                    </div>
                    <div class="signature-box">
                        <strong>Received by:</strong><br>
                        <br>
                        <div class="sig-line"></div>
                        <small>Name & Signature</small>
                    </div>
                    <div class="signature-box" style="text-align:right;">
                        <strong>Approved by:</strong><br>
                        <br>
                        <div class="sig-line"></div>
                        <small>Name & Signature</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".print-actions button").addEventListener("click", function () {
        var table = document.getElementById("data-table-basic");
        if (!table) {
            alert("Table not found!");
            return;
        }
        var printTable = table.cloneNode(true);
        var printContents = `
            <html>
            <head>
                <title>Cost Of Goods Sold Report By Category</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    h2 { text-align: center; margin-bottom: 20px; }
                    .signature-section { margin-top: 40px; }
                    .signature-row { display: flex; justify-content: space-between; gap: 20px; }
                    .signature-box { width: 32%; text-align: center; }
                    .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
                    @media print {
                        .btn, .search-box, form { display: none; }
                        table { display: table; }
                    }
                </style>
                <center>
                    <img src='<?= $logo_png; ?>' style='height:80px; margin-bottom:10px;'>
                    <div><?= $company_name ?><br>
                    <?= $company_address ?><br>
                    TIN/VAT Number: <?= $company_tin ?><br>
                    <br>
                    Phone: <?= $company_phone ?><br>
                    </div>
                </center>
                <br>
            </head>
            <body>
                <h2>Cost Of Goods Sold Report By Category</h2>
                <div style='text-align:center; font-size:15px; margin-bottom:5px;'>Date Range: <?php echo htmlspecialchars($from); ?> to <?php echo htmlspecialchars($to); ?></div>
                <div style='text-align:center; font-size:13px;'>Generated on: <?php echo date('d-m-Y H:i:s'); ?></div>
                ${printTable.outerHTML}
                <div class="signature-section">
                    <div class="signature-row">
                        <div class="signature-box" style="text-align:left;">
                            <strong>Printed by:</strong><br>
                            <?php echo htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                            <div class="sig-line"></div>
                            <small>Name & Signature</small>
                        </div>
                        <div class="signature-box">
                            <strong>Received by:</strong><br>
                            <br>
                            <div class="sig-line"></div>
                            <small>Name & Signature</small>
                        </div>
                        <div class="signature-box" style="text-align:right;">
                            <strong>Approved by:</strong><br>
                            <br>
                            <div class="sig-line"></div>
                            <small>Name & Signature</small>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `;
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.open();
        printWindow.document.write(printContents);
        printWindow.document.close();
        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
        };
    });
});
</script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#date_to").change(function () {
        console.log("changed");
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