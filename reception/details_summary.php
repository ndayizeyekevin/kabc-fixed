<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script>
    function openPdfDompdf() {
        var selectday = document.querySelector('input[name="selectday"]').value || new Date().toISOString().split('T')[0];
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'details_summary_pdf.php';
        form.target = '_blank';
        
        var serverInput = document.createElement('input');
        serverInput.type = 'hidden';
        serverInput.name = 'server';
        serverInput.value = '1';
        form.appendChild(serverInput);
        
        var dayInput = document.createElement('input');
        dayInput.type = 'hidden';
        dayInput.name = 'selectday';
        dayInput.value = selectday;
        form.appendChild(dayInput);
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

</script>
<?php 
date_default_timezone_set('GMT');
error_reporting(E_ALL);
ini_set('display_errors', 1);


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
     // When $last = 0, we're showing data after the last closed_at timestamp
     // Check if $from is a timestamp (contains time) or just a date
     if (strpos($from, ':') !== false) {
         // It's a timestamp - use timestamp comparison
         $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE created_at > '$from' AND cmd_status = '12'";
     } else {
         // It's a date - use date comparison (fallback for old logic)
         $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE DATE(created_at) >= '$from' AND cmd_status = '12'";
     }
    }else{
     // When $last is set, it's a date range query
     $sql = "SELECT cmd_qty,cmd_item,cmd_code FROM `tbl_cmd_qty` WHERE DATE(created_at) >= '$from' AND DATE(created_at) <= '$last' AND cmd_status = '12'";
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



include_once '../inc/close_open_day.php';



if (isset($_POST['close'])) {

    // Ensure the connection is established before any database operations
    include '../inc/conn.php'; 
    
    // Check if the connection object ($conn) is available and valid.
    if (!$conn) {
        die("Database connection failed.");
    }

    // --- 1. Get the last transaction ID (SAFE) ---
    // The query itself is simple and doesn't use external input, but we'll use prepare for best practice.
    $sql_last_id = "SELECT cmd_qty_id FROM tbl_cmd_qty ORDER BY cmd_qty_id DESC LIMIT 1";
    $lastid = null;

    if ($stmt_last_id = $conn->prepare($sql_last_id)) {
        $stmt_last_id->execute();
        $result_last_id = $stmt_last_id->get_result();
        
        if ($result_last_id->num_rows > 0) {
            $row = $result_last_id->fetch_assoc();
            $lastid = $row['cmd_qty_id'];
        }
        $stmt_last_id->close();
    } else {
        // Handle error if statement preparation fails
        error_log("Failed to prepare last ID query: " . $conn->error);
    }
    
    // --- End of last transaction ID retrieval ---


    if (isset($_POST['approve'])) {

        // --- 2. Check for unpaid orders (SAFE) ---
        $sql_unpaid = "SELECT COUNT(tbl_cmd.id) as un_paid
                       FROM tbl_cmd
                       LEFT JOIN payment_tracks ON tbl_cmd.OrderCode = payment_tracks.order_code
                       WHERE payment_tracks.order_code IS NULL
                       AND tbl_cmd.room_client IS NULL";

        if ($stmt_unpaid = $conn->prepare($sql_unpaid)) {
            $stmt_unpaid->execute();
            $result_unpaid = $stmt_unpaid->get_result();
            $row_unpaid = $result_unpaid->fetch_assoc();
            $stmt_unpaid->close();

            if ($row_unpaid['un_paid'] > 0) {
                // Block closure if unpaid orders exist
                $unpaid_count = $row_unpaid['un_paid'];
                echo "<script>alert('There are {$unpaid_count} orders that are not paid. Make sure to checkout all commands.');
                      window.location.href='index?resto=detailedsmumary';
                      </script>";
                exit;
            }
        } else {
             error_log("Failed to prepare unpaid orders query: " . $conn->error);
             echo "<script>alert('A system error occurred during the unpaid check.');</script>";
             exit;
        }
        // --- End of unpaid orders check ---


        // --- 3. Define closing variables ---
        $date = $_POST['closedate']; // NOTE: This variable is not used in the final UPDATE, but kept for context.
        $time = date('Y-m-d H:i:s'); // The formal closing time
        // $from = lastday(); // Assuming lastday() retrieves the open day's date, but this is redundant with WHERE closed_at IS NULL
        $user_id = $_SESSION['user_id']; 


        // --- 4. CHECK IF THERE IS AN OPEN DAY TO CLOSE (SAFE) ---
        $checkOpenSql = "SELECT created_at FROM days WHERE closed_at IS NULL ORDER BY created_at DESC LIMIT 1";
        $openResult = $conn->query($checkOpenSql); // Simple SELECT with no vars can use query()

        if ($openResult->num_rows === 0) {
            // Block closure if no open day is found
            echo "<script>alert('Error: There is no active day found to close. Please open a new day first.')</script>";
            echo "<script>window.location.href='index?resto=detailedsmumary';</script>";
            exit;
        }
        // --- End of open day check ---


        // --- 5. CORRECT ACTION: UPDATE the existing open day record (SECURE WITH PREPARED STATEMENT) ---
        $sql_update = "UPDATE `days`
                       SET `closed_at` = ?,
                           `last_item` = ?,
                           `closed_by` = ?  
                       WHERE `closed_at` IS NULL
                       ORDER BY `created_at` DESC
                       LIMIT 1";

        if ($stmt_update = $conn->prepare($sql_update)) {

            // Bind parameters: 's' for string (time), 'i' for integer (lastid), 'i' for integer (user_id)
            $stmt_update->bind_param("sii", $time, $lastid, $user_id);
            
            if ($stmt_update->execute()) {
                if ($stmt_update->affected_rows > 0) {
                     echo "<script>alert('Day Successfully closed')</script>";
                } else {
                     echo "<script>alert('Error: Day closing failed. No records were updated.')</script>";
                }
                
                $stmt_update->close();

                // redirect to avoid further processing
                echo "<script>window.location.href='index?resto=detailedsmumary';</script>";
            } else {
                // Execution failed
                echo "Error executing update: " . $stmt_update->error;
            }

        } else {
            // Preparation failed
            echo "Error preparing statement: " . $conn->error;
        }
        
        // --- FLAWED CODE SECTION (COMMENTED OUT) ---
        /* ... your original commented out code ... */


    } else {
        // --- 6. Handle 'close' button click without 'approve' check ---
        echo "<script>alert('Please confirm data before closing the day')</script>";
        echo "<script>window.location.href='index?resto=detailedsmumary';</script>";
    }
}

?>
<style>
    /* Default styles (screen) */
    .print-header, .signature-section {
        display: none !important;
    }

    /* Print styles */
    @media print {
        @page {
            size: landscape;
        }
        body * {
            visibility: hidden;
        }
        #content, #content * {
            visibility: visible;
        }
        #content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 1rem;
        }
        .no-print {
            display: none !important;
        }
        .table, .table th, .table td {
            border: 1px solid black !important;
            font-size: 12px !important;
            border: 1px solid #666 !important; /* Lighter border */
            font-size: 9px !important; /* Smaller font */
            padding: 2px 4px !important; /* Reduced padding */
            line-height: 1.2 !important; /* Tighter line height */
        }
        .table {
            border-collapse: collapse;
        }
        /* Signature area visible on print */
        .signature-section { display: block !important; margin-top: 40px; }
        .signature-row { display: flex; justify-content: space-between; gap: 20px; padding: 0 20px; }
        .signature-box { width: 32%; text-align: center; }
        .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
    }
</style>

<div class="container">
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">



            <div class="no-print">
            <form action="" method="POST" >
                <div style="padding:30px">
                Select Day:
                <input type="text" id="myDatePicker" name="selectday" placeholder="filter by date here" class="form-control">
                    <br>
                <button name="check" class="btn btn-primary">Load</button>
            </form>
            </div>
            </div>

      <?php



if (isset($_POST['check'])) {
    $selected_day = $_POST['selectday'];

    // Find a business day that was active at any point during the selected calendar day.
    $sql = $db->prepare("SELECT opened_at, closed_at FROM days 
                         WHERE 
                            DATE(opened_at) = :selected_date 
                            OR (:selected_date_start BETWEEN opened_at AND COALESCE(closed_at, NOW()))
                         ORDER BY opened_at DESC LIMIT 1");
    $sql->execute(['selected_date' => $selected_day, 'selected_date_start' => $selected_day . ' 00:00:00']);
    
    if ($sql->rowCount() > 0) {
        $fetch = $sql->fetch(PDO::FETCH_ASSOC);
        $from = $fetch['opened_at'];
        $last = $fetch['closed_at']; // $last will be the closed_at timestamp or null
    } else {
        // Fallback: Use the selected day's calendar boundaries if no business day is found.
        $from = $selected_day . ' 00:00:00';
        $last = $selected_day . ' 23:59:59';
    }
    $reportDate = $selected_day;
    $to = $selected_day; // Keep for compatibility with some functions
    $_SESSION['user_selected_date'] = true;
} else {
    // Default: Show data for the current open business day.
    $sql_last_day = $db->prepare("SELECT * FROM days WHERE closed_at IS NOT NULL ORDER BY closed_at DESC LIMIT 1");
    $sql_last_day->execute();
    $last_day = $sql_last_day->fetch(PDO::FETCH_ASSOC);

    if ($last_day && $last_day['closed_at']) {
        $from = $last_day['closed_at']; // Start from the timestamp the last day was closed
        $reportDate = date('Y-m-d', strtotime($last_day['closed_at']));
    } else {
        // No day has ever been closed, so start from the beginning of time.
        $from = '2000-01-01 00:00:00';
    }
    $reportDate = date('Y-m-d');
    $to = date("Y-m-d"); // 'to' date for display
    $last = null; // No specific end time, so it runs up to the present.
    unset($_SESSION['user_selected_date']);
}

      ?>
            <div class="no-print">
            <!-- <button onclick ="window.print()" class="btn btn-success"> Print </button> -->
                <div class="px-3 ms-3">
                  <button onclick="exportTableToExcel('sales', 'Detailed sales of <?php echo $reportDate?>')" class="btn btn-info">Export to Excel</button>
                  <a href="details_summary_pdf.php?selected_date=<?php echo $selected_day; ?>" target="_blank" class="btn btn-success text-white">Print Report</a>
                </div>
            <hr>
            <br>
            <br>
            </div>
            <div id = "content">
            <div class="print-header">
                <?php include '../holder/printHeader.php'?>
            </div>
                        <center><h2>Detailed Report: <?php echo $reportDate ?> </h2></center>
                        <div id="sales" class="table-responsive">
                              <table  class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Waiter(ess)</th>
                                        <th>Order Total</th>
                                        <th>Cash</th>
                                        <th>MOMO</th>
                                        <th>POS</th>
                                        <th>CREDIT</th>
                                        <th>ROOM CREDIT</th>
                                        <th>CHEQUE</th>
                                        <th>PAYMENT METHOD TOTAL</th>
                                        <th>BALANCE</th>
                                        <!--<th>EBM INVOICE</th>-->
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
                    $room = 0;
                    $no= 0;

                // $d =  "SELECT cmd_code FROM  `tbl_cmd_qty` WHERE cmd_qty_id > '$from' AND  cmd_qty_id <= '$last' AND cmd_status = '12' group by  cmd_code";
                if ($last) {
                  // A specific business day was selected, so use the exact start and end timestamps.
                  $sql = $db->prepare("SELECT q.*, c.room_client FROM tbl_cmd_qty q
                                       LEFT JOIN payment_tracks pt ON q.cmd_code = pt.order_code
                                       LEFT JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                                       WHERE q.created_at >= :from_time
                                       AND q.created_at <= :to_time
                                       AND (pt.order_code IS NOT NULL OR c.room_client IS NOT NULL)
                                       GROUP BY q.cmd_code");
                  $sql->execute(['from_time' => $from, 'to_time' => $last]);
                } else {
                  // No specific end time, so query from the start time to now.
                  $sql = $db->prepare("SELECT q.*, c.room_client FROM tbl_cmd_qty q
                                       LEFT JOIN payment_tracks pt ON q.cmd_code = pt.order_code
                                       LEFT JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                                       WHERE q.created_at >= :from_time
                                       AND (pt.order_code IS NOT NULL OR c.room_client IS NOT NULL)
                                       GROUP BY q.cmd_code");
                  $sql->execute(['from_time' => $from]);
                }
                $order = 0;
            if($sql->rowCount()){
                while($fetch = $sql->fetch()){


                    if($fetch['cmd_code']){

                            $order = $order + getOrderTotal($fetch['cmd_code']);

                            // Compute per-order totals and balance with room allocation
                            $code_for_calc = $fetch['cmd_code'];
                            $order_total = getOrderTotal($code_for_calc);
                            $amount_paid = 0;

                            // Sum cashier payments (payment_tracks)
                            $stmt_pt = $db->prepare("SELECT SUM(amount) AS paid FROM payment_tracks WHERE order_code = :code");
                            $stmt_pt->execute(['code' => $code_for_calc]);
                            $row_pt = $stmt_pt->fetch(PDO::FETCH_ASSOC);
                            if ($row_pt && isset($row_pt['paid'])) { $amount_paid += (float)$row_pt['paid']; }

                            // Allocate room payments if attached to a room
                            if (!empty($fetch['room_client'])) {
                                $booking_id = $fetch['room_client'];

                                // Sum room payments
                                $stmt_rp = $db->prepare("SELECT SUM(amount) AS room_paid FROM payments WHERE booking_id = :bid");
                                $stmt_rp->execute(['bid' => $booking_id]);
                                $room_payments = (float)($stmt_rp->fetch(PDO::FETCH_ASSOC)['room_paid'] ?? 0);

                                if ($room_payments > 0) {
                                    // Accommodation cost
                                    $stmt_acc = $db->prepare("SELECT booking_amount FROM tbl_acc_booking WHERE id = :bid");
                                    $stmt_acc->execute(['bid' => $booking_id]);
                                    $accommodation_cost = (float)($stmt_acc->fetch(PDO::FETCH_ASSOC)['booking_amount'] ?? 0);

                                    // Total of all orders for this booking
                                    $stmt_all = $db->prepare("SELECT SUM(m.menu_price * q.cmd_qty) AS total_orders
                                        FROM tbl_cmd c
                                        INNER JOIN tbl_cmd_qty q ON c.OrderCode = q.cmd_code
                                        INNER JOIN menu m ON q.cmd_item = m.menu_id
                                        WHERE c.room_client = :bid");
                                    $stmt_all->execute(['bid' => $booking_id]);
                                    $total_all_orders = (float)($stmt_all->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0);

                                    $total_bill = $accommodation_cost + $total_all_orders;

                                    if ($room_payments >= $total_bill) {
                                        $amount_paid = max($amount_paid, $order_total);
                                    } elseif ($room_payments > $accommodation_cost && $total_all_orders > 0) {
                                        $payment_for_orders = $room_payments - $accommodation_cost;
                                        $this_order_share = ($order_total / $total_all_orders) * $payment_for_orders;
                                        $amount_paid += $this_order_share;
                                    }
                                }
                            }

                            $balance = $order_total - $amount_paid;

                            ?>
                           <tr>

                            <td><a href="index?resto=gstDet&res=<?php echo  $fetch['cmd_table_id']?>&c=<?php echo  $fetch['cmd_code']?>" class="text-decoration-underline text-info"><?php echo getInvoiceNo($fetch['cmd_code']) ?></a></td>
                            <td><?php echo getServantName($fetch['Serv_id']); ?></td>
                            <td><?php echo $order_total; ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'01') ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'06'); ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'05'); ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'02'); ?></td>
                            <td><?php echo ($roomCredit = (!empty($fetch['room_client'])) ? max($balance, 0) : 0); ?></td>
                            <td><?php echo getTotalPaidByMethod($fetch['cmd_code'],'04'); ?></td>
                            <td><?php echo number_format($tt= getTotalPaidByMethod($fetch['cmd_code'],'04') +
                             getTotalPaidByMethod($fetch['cmd_code'],'02') +
                             getTotalPaidByMethod($fetch['cmd_code'],'05')+
                             getTotalPaidByMethod($fetch['cmd_code'],'06') +
                             getTotalPaidByMethod($fetch['cmd_code'],'01')) ?></td>
                             <td><?php echo getOrderTotal($fetch['cmd_code']) - $tt;  ?></td>

                            <td>
                            <?php


                            $br = 0;
                            $rs = 0;
                            $cf = 0;


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
                                 $room = $room + ($roomCredit);
                                 $br = $br + getTotalByServicebycode($fetch['cmd_code'],2,$from,$last);
                                 $rs = $rs + getTotalByServicebycode($fetch['cmd_code'],1,$from,$last);
                                 $cf = $cf + getTotalByServicebycode($fetch['cmd_code'],32,$from,$last);


                    }


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
                               <th ><?php echo number_format($room) ?></th>
                               <th ><?php echo number_format($cheque) ?></th>
                               <th colspan="3"><?php echo number_format( $py = $cheque + $cash + $momo + $card + $credit+ $room) ?> <br>
                               <span  class="text-danger fs-5">(  <?php echo number_format($order - $py ); ?> )</span>
                              </th>
                           </tr>



                                <tr>




                                    </tr>

                                    <tr>
                                        <th colspan="10"><strong>Title</strong></th>
                                        <th><strong>Total</strong></th>



                                    </tr>


                                    <tr>
                                        <td colspan="10">Bar Sales</td>
                                        <td><?php echo number_format ($totalbar=getTotalByService(2,$from,$last)) ?> </td>

                                    </tr>

                                          <tr>
                                        <td colspan="10">Resto Sales</td>
                                         <td><?php echo number_format ($totalresto=getTotalByService(1,$from,$last)) ?></td>

                                         </tr>

                                            <tr>
                                        <td colspan="10">Coffe Shop</td>
                                         <td><?php echo number_format ($totalcoffe=getTotalByService(32,$from,$last)) ?></td>

                                         </tr>


                                         <tr>
                                        <td colspan="10">Transport Fees</td>
                                         <td><?php echo number_format ($transport=getTotalByService(33,$from,$last)) ?></td>

                                         </tr>
                                         <tr>
                                        <td colspan="10">Rooms</td>
                                         <td><?php echo number_format ($room) ?></td>

                                         </tr>


                                         <tr>

                                          <th colspan="10"><strong>Total:</strong></th>
                                          <th><strong> <?php echo number_format ($totalresto  + $totalcoffe + $totalbar  + $transport + $room) ?> </strong></th>
                                         </tr>






                                         <tr>
                                     <td colspan="11"><center><h4> CREDITS LIST</h4> </center></td>

                                    </tr>


                                    <?PHP
                              $no = 0;
                              $totalcredi = 0;
                              $sql = $db->prepare("SELECT * FROM tbl_vsdc_sales where pmtTyCd ='02' AND has_refund='0' ");
                              $sql->execute(array());
                              if($sql->rowCount()){ 
                                while($fetch = $sql->fetch()){



                                 


                                     

                                        $totalcredi = $totalcredi + $fetch['totAmt'];

                                        ?>
                                        <tr>
                                        <td><?php echo $no = $no + 1 ?> </td>
                                        <td>Invoice at <?php echo $fetch['cfmDt'] ?></td>
                                        <td>Posted At:  <br></td>
                                        <td><?php echo getInvoiceNo($fetch['transaction_id'])."<br>".$fetch['transaction_id'] ?></td>
                                        <td colspan="7"><?php echo $fetch['custNm'] ?></td>
                                        <td><?php echo $fetch['totAmt'] ?></td>
                                      </tr>

                                        <?php

                                      
                                      ?>

                                    <?php  }


                                  


                                      
                              }



?>
  <tr>
                                <td colspan="10">Total:</td>
                                <td>Total: <?php echo number_format($totalcredi) ?> <br> <br> (Balance: <?php echo $totalcredi ?> )</td>
                              </tr>

                                </tbody>
                            </table>

                            <?php
                            // Resolve Printed by full name from logged-in user ID with safe fallbacks
                            $printedBy = '';
                            try {
                                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                                    $stmtPB = $db->prepare("SELECT u.f_name, u.l_name FROM tbl_users u WHERE u.user_id = ? LIMIT 1");
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
                                </div>
                            </div>





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
                    <div class="no-print">
					<form method="POST">
						<br>
						<input type="checkbox" required id="approve" name="approve">I hereby confirm all information above are correct<br>
						<br>
						<input type="datetime-local" min="<?php  ?>" class="form-control" name="closedate" required>
						<br>
						<button type="submit" required value="Close day" name="close" class="btn btn-info" onclick = "return confirm('Are you sure you want to close the day?');"> Close day </button>
					</form>
                    </div>
				<?php
				else:
				    	?>
                    <div class="no-print">
				    <form method="POST">
						<br>
						<input type="checkbox" required id="approve" name="approve">I hereby confirm all information above are correct<br>
						<br>
						<input type="datetime-local" class="form-control" name="opendate" required>
						<br>
						<button type="submit" required name="open" class="btn btn-info" onclick = "return confirm('Are you sure you want to open the day?');"> Open day </button>
					</form>
                    </div>
				<?php
				endif
				?>


        </div>  </div>
    </div>
    </div></div></div>
</div>
</div>

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
