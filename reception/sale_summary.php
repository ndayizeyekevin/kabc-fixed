<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script>
function printInvoice() {
  $("#headerprint").show();
  $("#printFooter").show();
  // The selector '#data-table-basic' might not exist on this page, but we'll leave it
  // in case it's part of a template. It's better to make selectors more specific if possible.
  $('#data-table-basic').removeAttr('id'); 
  var printContents = document.getElementById('content').innerHTML; 
  var originalContents = document.body.innerHTML; 
  document.body.innerHTML = printContents;
  window.print(); 
  document.body.innerHTML = originalContents; 
}

function openPdfDompdf() {
  var selectday = document.querySelector('input[name="selectday"]').value || new Date().toISOString().split('T')[0];
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = 'sale_summary_pdf.php';
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
<script>
  (function(){
    var btn = document.getElementById('savePdfSummary');
    if(btn){
      btn.addEventListener('click', function(){
        var el = document.getElementById('content') || document.body;
        var header = document.getElementById('headerprint');
        if(header){ header.style.display = 'block'; }
        var safeFrom = '<?php echo preg_replace("/[\\\\/:*?\"<>|]/", "-", $from ?? date('Y-m-d')); ?>';
        var safeTo   = '<?php echo preg_replace("/[\\\\/:*?\"<>|]/", "-", $to ?? date('Y-m-d')); ?>';
        var opt = {
          margin:       0.25,
          filename:     'Sales-Summary-' + safeFrom + '_to_' + safeTo + '.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true, scrollY: 0 },
          pagebreak:    { mode: ['css', 'legacy'] },
          jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(el).save().then(function(){
          if(header){ header.style.display = 'none'; }
        });
      });
    }
  })();
</script>
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Kigali');

function getAdvances($date, $type) {
    include '../inc/conn.php';
    
    $amount = 0;
    $sql = "SELECT * FROM advances WHERE advance_type = '$type'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (isset($_SESSION['fromdate'])) {
                $from = $_SESSION['fromdate'];
                $to = $_SESSION['todate'];
            }
            
            if ($date == date('Y-m-d', $row['created_at'])) {
                $amount += $row['amount'];
            }
            
        }
    }
    
    return strtoupper($amount);
}

/**
 * Get credit details for a specific date
 */
function getCreditDetails($id) {
    include '../inc/conn.php';
    
    $names = "";
    $sql = "SELECT * FROM tbl_vsdc_sales WHERE pmtTyCd = '02' AND has_refund = '0'";
    $result = $conn->query($sql);
    $grouped = []; // Group customer names and sum totals
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trx = substr((string)$row['transaction_id'], 0, 10);
            
            if (ctype_digit($trx)) {
                if ($id == date('Y-m-d', $trx)) {
                    $cust = $row['custNm'];
                    $amt = (float)$row['totAmt'];
                    
                    // Group and sum amounts by customer name
                    if (isset($grouped[$cust])) {
                        $grouped[$cust] += $amt;
                    } else {
                        $grouped[$cust] = $amt;
                    }
                }
            }
        }
        
        // Create output string after grouping
        foreach ($grouped as $cust => $sumAmt) {
            $names .= $cust . " " . number_format($sumAmt) . "<br>";
        }
    }
    
    return strtoupper($names);
}

/**
 * Get total credits for a specific date
 */
function getCredits($id) {
    include '../inc/conn.php';
    
    $amount = 0;
    $sql = "SELECT * FROM tbl_vsdc_sales WHERE pmtTyCd = '02' AND has_refund = '0'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trx = substr((string)$row['transaction_id'], 0, 10);
            
            if (ctype_digit($trx)) {
                if ($id == date('Y-m-d', $trx)) {
                    $amount += $row['totAmt'];
                }
            }
        }
    }
    
    return strtoupper($amount);
}

/**
 * Get credit names by ID
 */
function getCreditNames($id) {
    global $db;
    include '../inc/conn.php';
    
    $sql = $db->prepare("SELECT * FROM `creadit_id` WHERE id = '$id'");
    $sql->execute();
    
    while ($row = $sql->fetch()) {
        return $row['f_name'] . " " . $row['l_name'];
    }
}

/**
 * Get names by ID
 */
function getNames($id) {
    global $db;
    include '../inc/conn.php';
    
    $sql = $db->prepare("SELECT * FROM creadit_id WHERE id = '$id'");
    $sql->execute();
    
    while ($row = $sql->fetch()) {
        return $row['f_name'] . " " . $row['l_name'];
    }
}

/**
 * Get user who closed the day
 */
function getClosedByUser($to) {
    include '../inc/conn.php';
    
    $names = "............................";
    $sql = "SELECT * FROM days WHERE opened_at = '$to'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['created_by'];
        }
    }
    
    $id ??= $_SESSION['u_id'];
    
    $sql = "SELECT * FROM tbl_users WHERE user_id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $names = $row['f_name'] . " " . $row['l_name'];
        }
    }
    
    return $names;
}

/**
 * Get advance details by date and type
 */
function getAdvanceDetails($to, $type) {
    include '../inc/conn.php';
    
    $names = "";
    $sql = "SELECT * FROM advances WHERE advance_type = '$type'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
           
            if ($to == date('Y-m-d', $row['created_at'])) {
                $names .= $row['advance_by'] . " - " . $row['amount'] . " RWF <br>";
            }
        }
    }
    
    return strtoupper($names);
}

/**
 * Get collection details by date
 */
function getCollectionDetails($to) {
    include '../inc/conn.php';
    //  die(var_dump($to));
    
    $names = "";
    $sql = "SELECT * FROM collection WHERE DATE(created_at) = '$to'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            //   die(var_dump($row['created_at']));
            // if ($to == date('Y-m-d', (int)$row['created_at'])) {
                $names .= getNames($row['names']) . " - " . $row['amount'] . " RWF <br>";
            // }
        }
    }
    
    return strtoupper($names);
}

/**
 * Get total collection amount by date
 */
function getCollection($date) {
    include '../inc/conn.php';
    
    $amount = 0;
    $sql = "SELECT * FROM collection  WHERE DATE(created_at) = '$date'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (isset($_SESSION['fromdate'])) {
                $from = $_SESSION['fromdate'];
                $to = $_SESSION['todate'];
            }
            
            // if ($date == date('Y-m-d', $row['created_at'])) {
                $amount += $row['amount'];
            // }
        }
    }
    
    return $amount;
}

/**
 * Get partners by category and last ID
 */
function getPartners($category, $last) {
    include '../inc/conn.php';
    $names = "";
    $sql = "SELECT cmd_code FROM `tbl_cmd_qty` 
            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item 
            WHERE cmd_qty_id > '$last' AND cat_id = '$category' 
            GROUP BY cmd_code";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $names .= " " . getCreditUserId($row['cmd_code']);
        }
    }
    return $names;
}

/**
 * Get credit user ID by order code
 */
function getCreditUserId($code) {
    include '../inc/conn.php';
    
    $sql = "SELECT creadit_user FROM tbl_cmd WHERE OrderCode = '$code'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return getAccount($row['creadit_user']);
        }
    }
}

/**
 * Get account details by code
 */
function getAccount($code) {
    include '../inc/conn.php';
    
    $names = "";
    $sql = "SELECT l_name, f_name FROM creadit_id WHERE id = '$code'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $names = $row['l_name'] . " " . $row['f_name'];
        }
    }
    
    return $names;
}

/**
 * Get total by service category
 */
function getTotalByService($category, $from, $last) {
    include '../inc/conn.php';
    
    if ($last == 0) {
        $sql = "SELECT cmd_qty, cmd_item FROM `tbl_cmd_qty` 
                WHERE cmd_qty_id > '$from'";
    } else {
        $sql = "SELECT cmd_qty, cmd_item FROM `tbl_cmd_qty` 
                WHERE cmd_qty_id > '$from' AND cmd_qty_id <= '$last'";
    }
    
    $result = $conn->query($sql);
    $sale = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (getCategory($row['cmd_item']) == $category) {
                $sale += getPrice($row['cmd_item']) * $row['cmd_qty'];
            }
        }
    }
    
    return $sale;
}

/**
 * Get category by menu item ID
 */
function getCategory($id) {
    include '../inc/conn.php';
    
    $sql = "SELECT cat_id FROM `menu` WHERE menu_id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['cat_id'];
        }
    }
}

/**
 * Get price by menu item ID
 */
function getPrice($id) {
    include '../inc/conn.php';
    
    $sql = "SELECT menu_price FROM `menu` WHERE menu_id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['menu_price'];
        }
    }
}

/**
 * Get total collection paid by method
 */
function getTotalCollectionPaidByMethod($from, $to, $method) {
    global $db;
    
    $sql = $db->prepare("SELECT SUM(amount) as total FROM payment_tracks WHERE service = 'collection' AND method = :method AND created_at >= :from_time AND created_at <= :to_time");
    $sql->execute(['method' => $method, 'from_time' => $from, 'to_time' => $to]);
    $sale = 0;
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['total']) {
        $sale = $result['total'];
    }
    
    return $sale;
}

/**
 * Get total advance paid by method (less advance)
 */
function getTotalAdvancePaidByMethodLess($from, $to, $method) {
    global $db;
    
    $sql = $db->prepare("SELECT SUM(amount) as total FROM payment_tracks WHERE service = 'less advance' AND method = :method AND created_at >= :from_time AND created_at <= :to_time");
    $sql->execute(['method' => $method, 'from_time' => $from, 'to_time' => $to]);
    $sale = 0;
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['total']) {
        $sale = $result['total'];
    }
    
    return $sale;
}

/**
 * Get total advance paid by method
 */
function getTotalAdvancePaidByMethod($from, $to, $method) {
    global $db;
    
    $sql = $db->prepare("SELECT SUM(amount) as total FROM payment_tracks WHERE service = 'advance' AND method = :method AND created_at >= :from_time AND created_at <= :to_time");
    $sql->execute(['method' => $method, 'from_time' => $from, 'to_time' => $to]);
    $sale = 0;
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['total']) {
        $sale = $result['total'];
    }
    
    return $sale;
}

/**
 * Set default date range if not set in session
 */
include '../inc/conn.php';

/**
 * Get last day closed date
 */
function lastDay() {
    include '../inc/conn.php';
    
    $sql = "SELECT DATE(closed_at) AS closed_at FROM days ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lastDate = $row['closed_at'];
        }
    }
    
    return $lastDate ?? date('Y-m-d');
}

if(isset($_POST['check'])){
    $selected_day  = $_POST['selectday'];

    // Find a business day that was active at any point during the selected calendar day.
    // This handles cases where a business day starts on one calendar day and ends on the next.
    $sql = $db->prepare("SELECT opened_at, closed_at FROM days 
                         WHERE 
                            -- The business day started on the selected day
                            DATE(opened_at) = :selected_date 
                            -- OR the business day was still open at the beginning of the selected day
                            OR (:selected_date_start BETWEEN opened_at AND COALESCE(closed_at, NOW()))
                         ORDER BY opened_at DESC LIMIT 1");
    $sql->execute(['selected_date' => $selected_day, 'selected_date_start' => $selected_day . ' 00:00:00']);
    
    if($sql->rowCount() > 0){
        $fetch = $sql->fetch(PDO::FETCH_ASSOC);
        $from = $fetch['opened_at'];
        // If day is not closed, use current time as the end point.
        $to = $fetch['closed_at'] ?? date('Y-m-d H:i:s');
    } else {
        // Fallback if a date is selected that has no entry in `days` table.
        $from = $selected_day . ' 00:00:00';
        $to = $selected_day . ' 23:59:59';
    }
    $reportDate = $selected_day;
    $_SESSION['user_selected_date'] = true;
} else {
    // Default behavior: show data for the current open business day.
    // If no day is open, show a blank report.
    $sql_last_day = $db->prepare("SELECT opened_at, closed_at FROM days WHERE closed_at IS NULL ORDER BY opened_at DESC LIMIT 1");
    $sql_last_day->execute();
    $last_day = $sql_last_day->fetch(PDO::FETCH_ASSOC);

    if ($last_day) {
        // A business day is currently open. Show data from its start time until now.
        $from = $last_day['opened_at'];
        $to = date('Y-m-d H:i:s');
        $reportDate = date('Y-m-d', strtotime($last_day['opened_at']));
    } else {
        // No business day is currently open. Default to a blank report for the current date.
        $reportDate = date('Y-m-d');
        $from = null; // Setting from/to to null will result in no data being fetched.
        $to = null;
    }
    unset($_SESSION['user_selected_date']);
}

/**
 * Get total paid by method for specific order
 */
function getTotalPaidByMethod($code, $method) {
    include '../inc/conn.php';
    
    $sql = "SELECT * FROM payment_tracks WHERE order_code = '$code' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sale += $row['amount'];
        }
    }
    
    return $sale;
}
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">

                    <?php
                    // Display what data is being shown
                    if (isset($_SESSION['user_selected_date']) && $_SESSION['user_selected_date'] === true) {
                        echo "<div class='alert alert-info'>Showing data for business day: <strong>" . date('Y-m-d', strtotime($from)) . "</strong> (from $from to $to)</div>";
                    } else {
                        echo "<div class='alert alert-success'>Showing data for current business day: <strong>" . date('Y-m-d H:i:s', strtotime($from)) . " to NOW</strong></div>";
                    }
                    ?>

                    <form action="" method="POST" >
                        <div style="padding:30px">
                        Select Day:
                        <input type="text" id="myDatePicker" name="selectday" placeholder="filter by date here" class="form-control" value="<?php echo htmlspecialchars($reportDate); ?>">
                            <br>
                        <button name="check" class="btn btn-primary">Load</button>
                    </form>

                    <!--<button onclick="printInvoice()">Print</button>-->
                    <!--<button id="savePdfSummary" class="btn btn-warning" onclick="printInvoice()">Print / Save as PDF</button>-->
                    <button id="generateDompdfBtn" class="btn btn-success" onclick="openPdfDompdf()">Print Report</button>
                    <hr>

                    <br><br>
                    <div id="content">

                        <?php include '../holder/printHeader.php'?>

                        <?php
                        
                        // Initialize payment method variables
                        $cash = 0;
                        $card = 0;
                        $momo = 0;
                        $credit = 0;
                        $transfer = 0;
                        $cheque = 0;

                        $cashCollection = 0;
                        $cardCollection = 0;
                        $momoCollection = 0;
                        $creditCollection = 0;
                        $chequeCollection = 0;

                        $cashAdvanceLess = 0;
                        $cardAdvanceLess = 0;
                        $momoAdvanceLess = 0;
                        $creditAdvanceLess = 0;
                        $chequeAdvanceLess = 0;

                        $cashAdvance = 0;
                        $cardAdvance = 0;
                        $momoAdvance = 0;
                        $creditAdvance = 0;
                        $chequeAdvance = 0;

                        // Get payment totals by method

// Make sure you have a valid PDO connection in $db before this code
$cash = $card = $momo = $credit = $cheque = $cashCredit = $others = 0;
$cashAdvance = $cardAdvance = $momoAdvance = $creditAdvance = $chequeAdvance = 0;
$cashAdvanceLess = $cardAdvanceLess = $momoAdvanceLess = $creditAdvanceLess = $chequeAdvanceLess = 0;
$cashCollection = $cardCollection = $momoCollection = $creditCollection = $chequeCollection = 0;
$roomCreditTotal = 0;

$sql = $db->prepare(
    "SELECT q.cmd_code
        FROM tbl_cmd_qty q
        LEFT JOIN payment_tracks pt ON q.cmd_code = pt.order_code
        LEFT JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
        WHERE q.created_at >= :from_time AND q.created_at <= :to_time
        AND (pt.order_code IS NOT NULL OR c.room_client IS NOT NULL)
        GROUP BY q.cmd_code"
);
$sql->execute(['from_time' => $from, 'to_time' => $to]);


// Process each order
if ($sql->rowCount()) {
    while ($fetch = $sql->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($fetch['cmd_code'])) {
            $cmd = $fetch['cmd_code'];
            $cash        += getTotalPaidByMethod($cmd, '01');
            $card        += getTotalPaidByMethod($cmd, '05');
            $momo        += getTotalPaidByMethod($cmd, '06');
            $credit      += getTotalPaidByMethod($cmd, '02');
            $cheque      += getTotalPaidByMethod($cmd, '04');
            $cashCredit  += getTotalPaidByMethod($cmd, '03');
            $others      += getTotalPaidByMethod($cmd, '07');
        }
    }
}

// Calculate advance totals
$cashAdvance     += getTotalAdvancePaidByMethod($from, $to, '01');
$cardAdvance     += getTotalAdvancePaidByMethod($from, $to, '05');
$momoAdvance     += getTotalAdvancePaidByMethod($from, $to, '06');
$creditAdvance   += getTotalAdvancePaidByMethod($from, $to, '02');
$chequeAdvance   += getTotalAdvancePaidByMethod($from, $to, '04');

// Calculate advance less totals
$cashAdvanceLess     += getTotalAdvancePaidByMethodLess($from, $to, '01');
$cardAdvanceLess     += getTotalAdvancePaidByMethodLess($from, $to, '05');
$momoAdvanceLess     += getTotalAdvancePaidByMethodLess($from, $to, '06');
$creditAdvanceLess   += getTotalAdvancePaidByMethodLess($from, $to, '02');
$chequeAdvanceLess   += getTotalAdvancePaidByMethodLess($from, $to, '04');

// Calculate collection totals
$cashCollection     += getTotalCollectionPaidByMethod($from, $to, '01');
$cardCollection     += getTotalCollectionPaidByMethod($from, $to, '05');
$momoCollection     += getTotalCollectionPaidByMethod($from, $to, '06');
$creditCollection   += getTotalCollectionPaidByMethod($from, $to, '02');
$chequeCollection   += getTotalCollectionPaidByMethod($from, $to, '04');

$roomStmt = $db->prepare("SELECT q.cmd_code
        FROM tbl_cmd_qty q
        INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
        WHERE q.created_at >= :from_time AND q.created_at <= :to_time
          AND c.room_client IS NOT NULL
        GROUP BY q.cmd_code");
$roomStmt->execute(['from_time' => $from, 'to_time' => $to]);


if (isset($roomStmt) && $roomStmt->rowCount()) {
    while ($rc = $roomStmt->fetch(PDO::FETCH_ASSOC)) {
        $cmd = $rc['cmd_code'];

        // Get booking (room_client) for this order
        $cmdInfo = $db->prepare("SELECT room_client FROM tbl_cmd WHERE OrderCode = :code LIMIT 1");
        $cmdInfo->execute(['code' => $cmd]);
        $cmdRow = $cmdInfo->fetch(PDO::FETCH_ASSOC);
        if (!$cmdRow || empty($cmdRow['room_client'])) { continue; }
        $booking_id = $cmdRow['room_client'];

        // Order total
        $sumOrder = $db->prepare("SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) AS total
            FROM tbl_cmd_qty
            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
            WHERE tbl_cmd_qty.cmd_code = :cmd");
        $sumOrder->execute(['cmd' => $cmd]);
        $rowSum = $sumOrder->fetch(PDO::FETCH_ASSOC);
        $order_total = (float)($rowSum['total'] ?? 0);

        // Amount paid via cashier (payment_tracks)
        $cashierPay = $db->prepare("SELECT SUM(amount) AS paid FROM payment_tracks WHERE order_code = :code");
        $cashierPay->execute(['code' => $cmd]);
        $cashierRow = $cashierPay->fetch(PDO::FETCH_ASSOC);
        $amount_paid = (float)($cashierRow['paid'] ?? 0);

        // Room payments (sum over booking)
        $roomPay = $db->prepare("SELECT SUM(amount) AS room_paid FROM payments WHERE booking_id = :bid");
        $roomPay->execute(['bid' => $booking_id]);
        $roomPayments = (float)($roomPay->fetch(PDO::FETCH_ASSOC)['room_paid'] ?? 0);

        if ($roomPayments > 0) {
            // Accommodation cost
            $accStmt = $db->prepare("SELECT booking_amount FROM tbl_acc_booking WHERE id = :bid");
            $accStmt->execute(['bid' => $booking_id]);
            $accRow = $accStmt->fetch(PDO::FETCH_ASSOC);
            $accommodation_cost = (float)($accRow['booking_amount'] ?? 0);

            // Total of all orders for this booking
            $allOrders = $db->prepare("SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) AS total_orders
                FROM tbl_cmd
                INNER JOIN tbl_cmd_qty ON tbl_cmd.OrderCode = tbl_cmd_qty.cmd_code
                INNER JOIN menu ON tbl_cmd_qty.cmd_item = menu.menu_id
                WHERE tbl_cmd.room_client = :bid");
            $allOrders->execute(['bid' => $booking_id]);
            $total_all_orders = (float)($allOrders->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0);

            $total_bill = $accommodation_cost + $total_all_orders;

            if ($roomPayments >= $total_bill) {
                $amount_paid = max($amount_paid, $order_total);
            } elseif ($roomPayments > $accommodation_cost && $total_all_orders > 0) {
                $payment_for_orders = $roomPayments - $accommodation_cost;
                $this_order_share = ($order_total / $total_all_orders) * $payment_for_orders;
                $amount_paid = $amount_paid + $this_order_share;
            }
        }

        $balance = $order_total - $amount_paid;
        if ($balance > 0) {
            $roomCreditTotal += $balance;
        }
    }
}
?>

                        <hr>
                        <h4><center>Report for <?php echo $reportDate; ?></center></h4>
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Total</th>
                                        <th>All Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    
                                    $alltotal = 0;
                                    $departments = [
                                        "Bar Sales" => 2,
                                        "Resto Sales" => 1,
                                        // "Coffe Shop" => 32,
                                        // "Transport Fees" => 33,
                                    ];

                                    foreach ($departments as $key => $val) {
                                        ?>
                                        <tr>
                                            <td><h2><?= $key?></h2></td>
                                            <td>
                                                <?php 
                                                // Compute category total - respect user date selection or last closing time
                                                $categoryTotal = 0;
                                                try {
                                                    $sumStmt = $db->prepare("
                                                        SELECT SUM(CAST(REPLACE(m.menu_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS total
                                                        FROM tbl_cmd_qty q
                                                        INNER JOIN menu m ON m.menu_id = q.cmd_item
                                                        INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                                                        WHERE q.created_at BETWEEN :from_time AND :to_time
                                                          AND m.cat_id = :val
                                                          AND (
                                                              c.room_client IS NOT NULL 
                                                              OR (
                                                                  (SELECT SUM(amount) FROM payment_tracks WHERE order_code = c.OrderCode) >= 
                                                                  (SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) 
                                                                   FROM tbl_cmd_qty 
                                                                   INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item 
                                                                   WHERE tbl_cmd_qty.cmd_code = c.OrderCode)
                                                              )
                                                          )
                                                    ");
                                                    $sumStmt->execute(['from_time' => $from, 'to_time' => $to, 'val' => $val]);

                                                    $rowSum = $sumStmt->fetch(PDO::FETCH_ASSOC);
                                                    $categoryTotal = (float)($rowSum['total'] ?? 0);
                                                } catch (Exception $e) { $categoryTotal = 0; }
                                                echo number_format($categoryTotal);
                                                ?>
                                                        <?php
                                                        $i = 0;

                                                        // Build items query - respect user date selection or last closing time
                                                        // Get only fully paid or room-charged orders' items for this category
                                                        $cmdSql = $db->prepare("
                                                            SELECT DISTINCT c.OrderCode
                                                            FROM tbl_cmd c
                                                            WHERE c.created_at BETWEEN :from_time AND :to_time
                                                            AND (
                                                                c.room_client IS NOT NULL 
                                                                OR (
                                                                    (SELECT SUM(amount) FROM payment_tracks WHERE order_code = c.OrderCode) >= 
                                                                    (SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) 
                                                                     FROM tbl_cmd_qty 
                                                                     INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item 
                                                                     WHERE tbl_cmd_qty.cmd_code = c.OrderCode)
                                                                )
                                                            )
                                                        ");
                                                        $cmdSql->execute(['from_time' => $from, 'to_time' => $to]);

                                                        // Build grouped items by fetching all cmd_codes then batching room_client lookup
                                                        $groupedData = [];
                                                        $groupedByItemName = []; // Group by item name with aggregated qty and total
                                                        $total = 0;

                                                        // Get all cmd rows and build a room_client map in one query
                                                        $cmdRows = $cmdSql->fetchAll(PDO::FETCH_ASSOC);
                                                        $cmdCodes = array_column($cmdRows, 'OrderCode');

                                                        $roomMap = [];
                                                        if (!empty($cmdCodes)) {
                                                            $placeholders = implode(',', array_fill(0, count($cmdCodes), '?'));
                                                            $roomAllStmt = $db->prepare("SELECT OrderCode, room_client FROM tbl_cmd WHERE OrderCode IN ($placeholders)");
                                                            $roomAllStmt->execute($cmdCodes);
                                                            while ($r = $roomAllStmt->fetch(PDO::FETCH_ASSOC)) {
                                                                $roomMap[$r['OrderCode']] = $r['room_client'];
                                                            }
                                                        }

                                                        // Process each order and aggregate items for this category
                                                        foreach ($cmdRows as $cmdRow) {
                                                            $cmd = $cmdRow['OrderCode'];
                                                            // Get all items for this order and category
                                                            $itemSql = $db->prepare("SELECT menu.*, tbl_cmd_qty.cmd_qty FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item WHERE cmd_code = :cmd AND menu.cat_id = :val");
                                                            $itemSql->execute(['cmd' => $cmd, 'val' => $val]);
                                                            $items = $itemSql->fetchAll(PDO::FETCH_ASSOC);

                                                            // The main query for cmdSql already filters for paid/room orders,
                                                            // so we just need to process the items.
                                                            if (!empty($items)) {
                                                                foreach ($items as $item) {
                                                                    $menuDesc = $item['menu_desc'];
                                                                    if (!isset($groupedData[$menuDesc])) {
                                                                        $groupedData[$menuDesc] = [];
                                                                    }
                                                                    $groupedData[$menuDesc][] = $item;

                                                                    // Aggregate by item name
                                                                    $menuName = $item['menu_name'];
                                                                    $price = (float)str_replace(',', '', $item['menu_price']);
                                                                    $qty = (int)$item['cmd_qty'];
                                                                    $itemTotal = $price * $qty;

                                                                    if (!isset($groupedByItemName[$menuName])) {
                                                                        $groupedByItemName[$menuName] = [
                                                                            'price' => $price,
                                                                            'qty' => 0,
                                                                            'total' => 0
                                                                        ];
                                                                    }
                                                                    $groupedByItemName[$menuName]['qty'] += $qty;
                                                                    $groupedByItemName[$menuName]['total'] += $itemTotal;
                                                                }
                                                            }
                                                        }
                                                        // Output grouped data by category first
                                                        foreach ($groupedData as $key => $item) {
                                                            ?>
                                                            <h3><?php echo $key; ?></h3>
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ITEM NAME</th>
                                                                        <!--<th>ITEM DESCRIPTION</th>-->
                                                                        <th>PRICE</th>
                                                                        <th>QTY</th>
                                                                        <th>TOTAL</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    // Group items within this category by name
                                                                    $categoryItems = [];
                                                                    foreach ($item as $val) {
                                                                        $menuName = $val['menu_name'];
                                                                        $price = (float) str_replace(',', '', (string)$val['menu_price']);
                                                                        $qty = (int)$val['cmd_qty'];
                                                                        $amount = $price * $qty;

                                                                        if (!isset($categoryItems[$menuName])) {
                                                                            $categoryItems[$menuName] = [
                                                                                'price' => $price,
                                                                                'qty' => 0,
                                                                                'total' => 0
                                                                            ];
                                                                        }
                                                                        $categoryItems[$menuName]['qty'] += $qty;
                                                                        $categoryItems[$menuName]['total'] += $amount;
                                                                    }

                                                                    // Display grouped items
                                                                    foreach ($categoryItems as $itemName => $itemData) {
                                                                        $total += $itemData['total'];
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $itemName; ?></td>
                                                                            <!--<td><?php echo $val['menu_desc']; ?></td>-->
                                                                            <td><?php echo number_format($itemData['price']); ?></td>
                                                                            <td><?php echo $itemData['qty']; ?></td>
                                                                            <td><?php echo number_format($itemData['total']); ?></td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                            <?php
                                                        }
                                                        ?>
                                                        <tr>
                                                            <th></th>
                                                            <th colspan="3">Sub Total</th>
                                                            <th><?php echo number_format($total)?> RWF</th>
                                                        </tr>
                                                    
                                            </td>
                                        </tr>
                                        
                                        <?php
                                    $alltotal += $total;
                                    }
                                    ?>
                                    
                                    <tr>
                                                                <th colspan="4">Grand Total</th>
                                                                <th><?php echo number_format($alltotal)?> RWF</th>
                                                            </tr>

                                    <!--<tr>-->
                                    <!--    <td>Collections <br>-->
                                    <!--        <?php echo getCollectionDetails($reportDate);?>-->
                                    <!--    </td>-->
                                    <!--    <td><?php echo number_format($totalCol = getCollection($reportDate)) ?></td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <td>Less Advance<br><?php echo getAdvanceDetails($reportDate, 1);?></td>-->
                                    <!--    <td><?php echo number_format($totalLess = getAdvances($reportDate, 1)) ?></td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <td>Advance <br>-->
                                    <!--        <?php echo getAdvanceDetails($reportDate, 2);?>-->
                                    <!--    </td>-->
                                    <!--    <td><?php echo number_format($totalLad = getAdvances($reportDate, 2)) ?></td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <td>Credits: <br>-->
                                    <!--        <?php echo getCreditDetails($reportDate);?>-->
                                    <!--    </td>-->
                                    <!--    <td>(<?php echo number_format(getCredits($reportDate))?> )</td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <td>Total:</td>-->
                                    <!--    <td></td>-->
                                    <!--</tr>-->
                                </tbody>
                            </table>

                            <hr>

                            <h5>Cash: <?php echo number_format($cash + $cashCollection + $cashAdvance - $cashAdvanceLess)?> RWF</h5>
                            <h5>POS: <?php echo number_format($card + $cardCollection + $cardAdvance - $cardAdvanceLess)?> RWF</h5>
                            <h5>Momo: <?php echo number_format($momo + $momoCollection + $momoAdvance - $momoAdvanceLess)?> RWF</h5>
                            <h5>Credit: <?php echo number_format($credit + $creditCollection + $creditAdvance - $creditAdvanceLess)?> RWF</h5>
                            <h5>Room Credit: <?php echo number_format($roomCreditTotal)?> RWF</h5>
                            <h5>Bank Cheque: <?php echo number_format($cheque + $chequeCollection + $chequeAdvance - $chequeAdvanceLess)?> RWF</h5>
                            <h5>CASH/CREDIT: <?php $cashCredit ??= 0; echo number_format($cashCredit)?> RWF</h5>
                            <h5>OTHERS: <?php $others ??= 0; echo number_format($others)?> RWF</h5>

                            <hr>
                            <h4>Total: <?php 
                            $totalCash = $cash + $cashCollection + $cashAdvance - $cashAdvanceLess;
                            $totalCard = $card + $cardCollection + $cardAdvance - $cardAdvanceLess;
                            $totalMomo = $momo + $momoCollection + $momoAdvance - $momoAdvanceLess;
                            $totalCredit = $credit + $creditCollection + $creditAdvance - $creditAdvanceLess;
                            $totalCheque = $cheque + $chequeCollection + $chequeAdvance - $chequeAdvanceLess;
                            $totalCashCredit = $cashCredit ?? 0;
                            $totalOthers = $others ?? 0;
                            $grandTotal = $totalCash + $totalCard + $totalMomo + $totalCredit + $roomCreditTotal + $totalCheque + $totalCashCredit + $totalOthers;
                            echo number_format($grandTotal); ?> RWF</h4>

                            <hr>
                            <br><br>

                            <h2>Remarks</h2>
                            <textarea height="500px" class="form-control"></textarea>
                            <br><br><br><br>

                            <table class="table">
                                <tr>
                                    <td>
                                        <br>Cashier<br><br>Names: <?php echo getClosedByUser($reportDate)?><br>
                                        <br>Signature: .........................................
                                    </td>
                                    <td>
                                        <br>Reviewed By (F&B):<br><br>Names: .........................................<br>
                                        <br>Signature: .........................................
                                    </td>
                                    <td>
                                        <br>Received By (G. Cashier):<br><br>Names: .........................................<br>
                                        <br>Signature: .........................................
                                    </td>
                                </tr>
                            </table>

                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    dateFormat: "Y-m-d",
    allowInput: true
  });
</script>
