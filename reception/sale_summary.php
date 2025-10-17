<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
    
    return $amount;
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
function getTotalCollectionPaidByMethod($date, $method) {
    include '../inc/conn.php';
    
    $sql = "SELECT * FROM payment_tracks WHERE service = 'collection' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($date == date('Y-m-d', $row['created_at'])) {
                $sale += $row['amount'];
            }
        }
    }
    
    return $sale;
}

/**
 * Get total advance paid by method (less advance)
 */
function getTotalAdvancePaidByMethodLess($date, $method) {
    include '../inc/conn.php';
    
    $sql = "SELECT * FROM payment_tracks WHERE service = 'less advance' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($date == date('Y-m-d', $row['created_at'])) {
                $sale += $row['amount'];
            }
        }
    }
    
    return $sale;
}

/**
 * Get total advance paid by method
 */
function getTotalAdvancePaidByMethod($date, $method) {
    include '../inc/conn.php';
    
    $sql = "SELECT * FROM payment_tracks WHERE service = 'advance' AND method = '$method'";
    $result = $conn->query($sql);
    $sale = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($date == date('Y-m-d', $row['created_at'])) {
                $sale += $row['amount'];
            }
        }
    }
    
    return $sale;
}

/**
 * Set default date range if not set in session
 */
if (!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])) {
    $from = date('Y-m-d');
    $to = date("Y-m-d");
} else {
    $from = $_SESSION['date_from'];
    $to = $_SESSION['date_to'];
}

include '../inc/conn.php';

// Handle date range submission similar to inn/sales_report.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date_from'], $_POST['date_to'])) {
    $postedFrom = trim($_POST['date_from']);
    $postedTo = trim($_POST['date_to']);
    // Normalize to Y-m-d; fallback to today if invalid
    $normFrom = date('Y-m-d', strtotime($postedFrom ?: 'today'));
    $normTo = date('Y-m-d', strtotime($postedTo ?: $normFrom));
    $_SESSION['date_from'] = $from = $normFrom;
    $_SESSION['date_to'] = $to = $normTo;
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
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">

                    <!-- Date Range Filter (two distinct calendars like in inn/sales_report.php) -->

                    <form action="" method="POST" style="padding:30px; border: 1px solid #eee; border-radius: 6px; margin-bottom: 10px;">
                        <div class="row" style="gap: 20px; align-items: end;">
                            <div class="col-md-4">
                                <label class="form-label"><strong>Date From</strong></label>
                                <input type="text" id="date_from" name="date_from" class="form-control" value="<?php echo htmlspecialchars($from); ?>" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><strong>Date To</strong></label>
                                <input type="text" id="date_to" name="date_to" class="form-control" value="<?php echo htmlspecialchars($to); ?>" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary" name="apply_range">Apply Range</button>
                            </div>
                        </div>
                    </form>

                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var defaultFrom = '<?php echo $from; ?>';
                        var defaultTo = '<?php echo $to; ?>';

                        var fpFrom = flatpickr('#date_from', {
                            dateFormat: 'Y-m-d',
                            defaultDate: defaultFrom,
                            allowInput: true,
                            onChange: function(selectedDates, dateStr) {
                                if (fpTo) {
                                    fpTo.set('minDate', dateStr || defaultFrom);
                                    // Auto-correct if current to < from
                                    var toVal = document.getElementById('date_to').value;
                                    if (toVal && dateStr && toVal < dateStr) {
                                        document.getElementById('date_to').value = dateStr;
                                        fpTo.setDate(dateStr, true);
                                    }
                                }
                            }
                        });

                        var fpTo = flatpickr('#date_to', {
                            dateFormat: 'Y-m-d',
                            defaultDate: defaultTo,
                            allowInput: true,
                            onReady: function() {
                                // Ensure minDate respects initial from
                                this.set('minDate', document.getElementById('date_from').value || defaultFrom);
                            },
                            onChange: function(selectedDates, dateStr) {
                                // If to < from, snap to from
                                var fromVal = document.getElementById('date_from').value;
                                if (fromVal && dateStr && dateStr < fromVal) {
                                    document.getElementById('date_to').value = fromVal;
                                    this.setDate(fromVal, true);
                                }
                            }
                        });

                        // Single-date picker for the existing Select Day field
                        if (document.getElementById('myDatePicker')) {
                            flatpickr('#myDatePicker', {
                                dateFormat: 'Y-m-d',
                                defaultDate: defaultFrom
                            });
                        }
                    });
                    </script>

                    <button onclick="printInvoice()">Print</button>
                    <hr>

                    <br><br>
                    <div id="content">

                        <?php include '../holder/printHeader.php'?>

                        <?php
                        $last = lastDay();
                        
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

if ($last == 0 || $from == $to) {
    // Same day or last = 0 (single day report). Match both DATETIME and TIMESTAMP-like storage.
    $fromStart = $from . ' 00:00:00';
    $toEnd = $to . ' 23:59:59';
    $sql = $db->prepare("SELECT cmd_code FROM `tbl_cmd_qty` 
                         WHERE (
                             (created_at BETWEEN :fromStart AND :toEnd)
                             OR (DATE(created_at) = :from)
                             OR (DATE(FROM_UNIXTIME(created_at)) = :from)
                             OR (DATE(FROM_UNIXTIME(created_at/1000)) = :from)
                         )
                         
                         GROUP BY cmd_code");
    $sql->execute(['fromStart' => $fromStart, 'toEnd' => $toEnd, 'from' => $from]);
} else {
    // Range filtering
    $fromStart = $from . ' 00:00:00';
    $toEnd = $to . ' 23:59:59';
    $sql = $db->prepare("SELECT cmd_code FROM `tbl_cmd_qty` 
                         WHERE (
                             (created_at BETWEEN :fromStart AND :toEnd)
                             OR (DATE(created_at) BETWEEN :from AND :to)
                             OR (DATE(FROM_UNIXTIME(created_at)) BETWEEN :from AND :to)
                             OR (DATE(FROM_UNIXTIME(created_at/1000)) BETWEEN :from AND :to)
                         )
                        
                         GROUP BY cmd_code");
    $sql->execute(['fromStart' => $fromStart, 'toEnd' => $toEnd, 'from' => $from, 'to' => $to]);
}

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
$cashAdvance     += getTotalAdvancePaidByMethod($to, '01');
$cardAdvance     += getTotalAdvancePaidByMethod($to, '05');
$momoAdvance     += getTotalAdvancePaidByMethod($to, '06');
$creditAdvance   += getTotalAdvancePaidByMethod($to, '02');
$chequeAdvance   += getTotalAdvancePaidByMethod($to, '04');

// Calculate advance less totals
$cashAdvanceLess     += getTotalAdvancePaidByMethodLess($to, '01');
$cardAdvanceLess     += getTotalAdvancePaidByMethodLess($to, '05');
$momoAdvanceLess     += getTotalAdvancePaidByMethodLess($to, '06');
$creditAdvanceLess   += getTotalAdvancePaidByMethodLess($to, '02');
$chequeAdvanceLess   += getTotalAdvancePaidByMethodLess($to, '04');

// Calculate collection totals
$cashCollection     += getTotalCollectionPaidByMethod($to, '01');
$cardCollection     += getTotalCollectionPaidByMethod($to, '05');
$momoCollection     += getTotalCollectionPaidByMethod($to, '06');
$creditCollection   += getTotalCollectionPaidByMethod($to, '02');
$chequeCollection   += getTotalCollectionPaidByMethod($to, '04');
?>

                        <hr>
                        <h4><center>Report for <?php echo lastDay(); ?></center></h4>
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
                                                // Compute category total for selected date range
                                                $categoryTotal = 0;
                                                try {
                                                    $sumStmt = $db->prepare("SELECT SUM(CAST(REPLACE(menu.menu_price, ',', '') AS DECIMAL(18,2)) * tbl_cmd_qty.cmd_qty) AS total
                                                        FROM tbl_cmd_qty
                                                        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                                        WHERE (
                                                            (tbl_cmd_qty.created_at BETWEEN :fromStart AND :toEnd)
                                                            OR (DATE(tbl_cmd_qty.created_at) BETWEEN :from AND :to)
                                                            OR (DATE(FROM_UNIXTIME(tbl_cmd_qty.created_at)) BETWEEN :from AND :to)
                                                            OR (DATE(FROM_UNIXTIME(tbl_cmd_qty.created_at/1000)) BETWEEN :from AND :to)
                                                        )
                                                    
                                                        AND menu.cat_id = :val");
                                                    $sumStmt->execute(['fromStart' => $from . ' 00:00:00', 'toEnd' => $to . ' 23:59:59', 'from' => $from, 'to' => $to, 'val' => $val]);
                                                    $rowSum = $sumStmt->fetch(PDO::FETCH_ASSOC);
                                                    $categoryTotal = (float)($rowSum['total'] ?? 0);
                                                } catch (Exception $e) { $categoryTotal = 0; }
                                                echo number_format($categoryTotal);
                                                ?>
                                                        <?php
                                                        $i = 0;
                                                        
                                                        // Build items query strictly using the selected date range
                                                        // Get only fully paid orders' items for this category
                                                        $cmdSql = $db->prepare("SELECT cmd_code FROM tbl_cmd_qty WHERE (created_at BETWEEN :fromStart AND :toEnd OR DATE(created_at) BETWEEN :from AND :to OR DATE(FROM_UNIXTIME(created_at)) BETWEEN :from AND :to OR DATE(FROM_UNIXTIME(created_at/1000)) BETWEEN :from AND :to) GROUP BY cmd_code");
                                                        $cmdSql->execute(['fromStart' => $from . ' 00:00:00', 'toEnd' => $to . ' 23:59:59', 'from' => $from, 'to' => $to]);
                                                        $groupedData = [];
                                                        $total = 0;
                                                        while ($cmdRow = $cmdSql->fetch(PDO::FETCH_ASSOC)) {
                                                            $cmd = $cmdRow['cmd_code'];
                                                            // Get all items for this order and category
                                                            $itemSql = $db->prepare("SELECT menu.*, tbl_cmd_qty.cmd_qty FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item WHERE cmd_code = :cmd AND menu.cat_id = :val");
                                                            $itemSql->execute(['cmd' => $cmd, 'val' => $val]);
                                                            $items = $itemSql->fetchAll(PDO::FETCH_ASSOC);
                                                            // Calculate total order amount
                                                            $orderTotal = 0;
                                                            foreach ($items as $item) {
                                                                $price = (float)str_replace(',', '', $item['menu_price']);
                                                                $qty = (int)$item['cmd_qty'];
                                                                $orderTotal += $price * $qty;
                                                            }
                                                            // Sum all payment methods for this order
                                                            $paidAmount = getTotalPaidByMethod($cmd, '01') + getTotalPaidByMethod($cmd, '05') + getTotalPaidByMethod($cmd, '06') + getTotalPaidByMethod($cmd, '02') + getTotalPaidByMethod($cmd, '04') + getTotalPaidByMethod($cmd, '03') + getTotalPaidByMethod($cmd, '07');
                                                            // Only show if fully paid
                                                            if ($paidAmount >= $orderTotal && $orderTotal > 0) {
                                                                foreach ($items as $item) {
                                                                    $menuDesc = $item['menu_desc'];
                                                                    if (!isset($groupedData[$menuDesc])) {
                                                                        $groupedData[$menuDesc] = [];
                                                                    }
                                                                    $groupedData[$menuDesc][] = $item;
                                                                }
                                                            }
                                                        }
                                                        // Output grouped data as before
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
                                                                    foreach ($item as $k => $val) {
                                                                        $price = (float) str_replace(',', '', (string)$val['menu_price']);
                                                                        $qty = (int)$val['cmd_qty'];
                                                                        $amount = $price * $qty;
                                                                        $total += $amount;
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $val['menu_name']; ?></td>
                                                                            <!--<td><?php echo $val['menu_desc']; ?></td>-->
                                                                            <td><?php echo number_format($price); ?></td>
                                                                            <td><?php echo $qty; ?></td>
                                                                            <td><?php echo number_format($amount); ?></td>
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
                                    <!--        <?php echo getCollectionDetails($to);?>-->
                                    <!--    </td>-->
                                    <!--    <td><?php echo number_format($totalCol = getCollection($to)) ?></td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <td>Less Advance<br><?php echo getAdvanceDetails($to, 1);?></td>-->
                                    <!--    <td><?php echo number_format($totalLess = getAdvances($to, 1)) ?></td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <td>Advance <br>-->
                                    <!--        <?php echo getAdvanceDetails($to, 2);?>-->
                                    <!--    </td>-->
                                    <!--    <td><?php echo number_format($totalLad = getAdvances($to, 2)) ?></td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <td>Credits: <br>-->
                                    <!--        <?php echo getCreditDetails($to);?>-->
                                    <!--    </td>-->
                                    <!--    <td>(<?php echo number_format(getCredits($to))?> )</td>-->
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
                            <h5>Bank Cheque: <?php echo number_format($cheque + $chequeCollection + $chequeAdvance - $chequeAdvanceLess)?> RWF</h5>
                            <h5>CASH/CREDIT: <?php $cashCredit ??= 0; echo number_format($cashCredit)?> RWF</h5>
                            <h5>OTHERS: <?php $others ??= 0; echo number_format($others)?> RWF</h5>

                            <hr>
                            <h4>Total: <?php 
                            echo number_format($cash + $card + $momo + $cheque + $credit + $cashCredit + $others +
                                             $cashCollection + $cardCollection + $momoCollection + $chequeCollection + $creditCollection +
                                             $cashAdvance + $cardAdvance + $momoAdvance + $chequeAdvance + $creditAdvance - 
                                             $cashAdvanceLess - $cardAdvanceLess - $momoAdvanceLess - $creditAdvanceLess - $chequeAdvanceLess) 
                            ?> RWF</h4>

                            <hr>
                            <br><br>

                            <h2>Remarks</h2>
                            <textarea height="500px" class="form-control"></textarea>
                            <br><br><br><br>

                            <table class="table">
                                <tr>
                                    <td>
                                        <br>Cashier<br><br>Names: <?php echo getClosedByUser($to)?><br>
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

<script>
function printInvoice() {
$("#headerprint").show();
var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>



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

function printInvoice() {
$("#headerprint").show();
$("#printFooter").show();
$('#data-table-basic').removeAttr('id');
  var printContents = document.getElementById('content').innerHTML; 
  var originalContents = document.body.innerHTML; document.body.innerHTML = printContents;
  window.print(); document.body.innerHTML = originalContents; }
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

