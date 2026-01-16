<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Kigali');

ob_start();
include '../inc/conn.php';

// Determine report date range using sale_summary business-day logic (supports From/To)
$reportDateFrom = $reportDateTo = null;
if (isset($_POST['check'])) {
    $selected_from = !empty($_POST['select_from']) ? $_POST['select_from'] : ($_POST['selectday'] ?? null);
    $selected_to   = !empty($_POST['select_to']) ? $_POST['select_to'] : ($selected_from ?? null);

    $resolveStmt = $db->prepare("SELECT opened_at, closed_at FROM days 
                         WHERE DATE(opened_at) = :selected_date 
                         OR (:selected_date_start BETWEEN opened_at AND COALESCE(closed_at, NOW()))
                         ORDER BY opened_at DESC LIMIT 1");

    if ($selected_from) {
        $resolveStmt->execute(['selected_date' => $selected_from, 'selected_date_start' => $selected_from . ' 00:00:00']);
        if ($resolveStmt->rowCount() > 0) {
            $f = $resolveStmt->fetch(PDO::FETCH_ASSOC);
            $from = $f['opened_at'];
        } else {
            $from = $selected_from . ' 00:00:00';
        }
        $reportDateFrom = $selected_from;
    }

    if ($selected_to) {
        $resolveStmt->execute(['selected_date' => $selected_to, 'selected_date_start' => $selected_to . ' 00:00:00']);
        if ($resolveStmt->rowCount() > 0) {
            $t = $resolveStmt->fetch(PDO::FETCH_ASSOC);
            $to = $t['closed_at'] ?? date('Y-m-d H:i:s');
        } else {
            $to = $selected_to . ' 23:59:59';
        }
        $reportDateTo = $selected_to;
    }

    // If only one date supplied, use it for both ends
    if (empty($reportDateTo) && !empty($reportDateFrom)) {
        $reportDateTo = $reportDateFrom;
        // $to already set above accordingly
    }
    if (empty($reportDateFrom) && !empty($reportDateTo)) {
        $reportDateFrom = $reportDateTo;
    }

    // Ensure from <= to
    if (!empty($from) && !empty($to) && (strtotime($from) > strtotime($to))) {
        $tmp = $from; $from = $to; $to = $tmp;
        $tmpd = $reportDateFrom; $reportDateFrom = $reportDateTo; $reportDateTo = $tmpd;
    }

    $_SESSION['user_selected_date'] = true;
} else {
    $sql_last_day = $db->prepare("SELECT opened_at, closed_at FROM days WHERE closed_at IS NULL ORDER BY opened_at DESC LIMIT 1");
    $sql_last_day->execute();
    $last_day = $sql_last_day->fetch(PDO::FETCH_ASSOC);

    if ($last_day) {
        $from = $last_day['opened_at'];
        $to = date('Y-m-d H:i:s');
        $reportDateFrom = date('Y-m-d', strtotime($last_day['opened_at']));
        $reportDateTo = date('Y-m-d');
    } else {
        $reportDateFrom = date('Y-m-d');
        $reportDateTo = date('Y-m-d');
        $from = null;
        $to = null;
    }
    unset($_SESSION['user_selected_date']);
}

// Helper: returns boolean whether order is considered (room charged or fully paid)
function isOrderCounted($orderCode, $db) {
    $payStmt = $db->prepare("SELECT SUM(amount) as paid FROM payment_tracks WHERE order_code = :code");
    $payStmt->execute(['code' => $orderCode]);
    $paid = (float)($payStmt->fetch(PDO::FETCH_ASSOC)['paid'] ?? 0);

    $sumStmt = $db->prepare("SELECT SUM(CAST(REPLACE(unit_price, ',', '') AS DECIMAL(18,2)) * cmd_qty) AS total
                             FROM tbl_cmd_qty
                             WHERE cmd_code = :code");
    $sumStmt->execute(['code' => $orderCode]);
    $orderTotal = (float)($sumStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    return ($paid >= $orderTotal);
}

// Build category list for the selected range (only categories that had items in qualifying orders)
$categories = [];
if ($from && $to) {
    $catSql = $db->prepare(
        "SELECT DISTINCT q.cat_id, c.cat_name
         FROM tbl_cmd_qty q
         INNER JOIN category c ON c.cat_id = q.cat_id
         INNER JOIN tbl_cmd t ON t.OrderCode = q.cmd_code
         WHERE q.created_at BETWEEN :from_time AND :to_time
           AND (
             t.room_client IS NOT NULL
             OR (
               (SELECT SUM(amount) FROM payment_tracks WHERE order_code = t.OrderCode) >=
               (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty)
                FROM tbl_cmd_qty q2
                WHERE q2.cmd_code = t.OrderCode)
             )
           )
         GROUP BY q.cat_id"
    );
    $catSql->execute(['from_time' => $from, 'to_time' => $to]);
    while ($r = $catSql->fetch(PDO::FETCH_ASSOC)) {
        $categories[$r['cat_id']] = $r['cat_name'];
    }
}

// Define helper functions for payment/collection/advance totals (mirror logic from sale_summary.php)
function getTotalPaidByMethod($code, $method) {
    global $db;
    $sql = $db->prepare("SELECT SUM(amount) as paid FROM payment_tracks WHERE order_code = :code AND method = :method");
    $sql->execute(['code' => $code, 'method' => $method]);
    return (float)($sql->fetch(PDO::FETCH_ASSOC)['paid'] ?? 0);
}

function getTotalCollectionPaidByMethod($from, $to, $method) {
    global $db;
    $sql = $db->prepare("SELECT SUM(amount) as total FROM payment_tracks WHERE service = 'collection' AND method = :method AND created_at >= :from_time AND created_at <= :to_time");
    $sql->execute(['method' => $method, 'from_time' => $from, 'to_time' => $to]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    return (float)($result['total'] ?? 0);
}

function getTotalAdvancePaidByMethod($from, $to, $method) {
    global $db;
    $sql = $db->prepare("SELECT SUM(amount) as total FROM payment_tracks WHERE service = 'advance' AND method = :method AND created_at >= :from_time AND created_at <= :to_time");
    $sql->execute(['method' => $method, 'from_time' => $from, 'to_time' => $to]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    return (float)($result['total'] ?? 0);
}

function getTotalAdvancePaidByMethodLess($from, $to, $method) {
    global $db;
    $sql = $db->prepare("SELECT SUM(amount) as total FROM payment_tracks WHERE service = 'less advance' AND method = :method AND created_at >= :from_time AND created_at <= :to_time");
    $sql->execute(['method' => $method, 'from_time' => $from, 'to_time' => $to]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    return (float)($result['total'] ?? 0);
}

// Compute payment totals for qualifying orders (room charged or fully paid)
$cash = $card = $momo = $credit = $cheque = $cashCredit = $others = 0;
$cashAdvance = $cardAdvance = $momoAdvance = $creditAdvance = $chequeAdvance = 0;
$cashAdvanceLess = $cardAdvanceLess = $momoAdvanceLess = $creditAdvanceLess = $chequeAdvanceLess = 0;
$cashCollection = $cardCollection = $momoCollection = $creditCollection = $chequeCollection = 0;
$roomCreditTotal = 0;

$codeSql = $db->prepare(
    "SELECT q.cmd_code
     FROM tbl_cmd_qty q
     LEFT JOIN payment_tracks pt ON q.cmd_code = pt.order_code
     LEFT JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
     WHERE q.created_at BETWEEN :from_time AND :to_time
       AND (pt.order_code IS NOT NULL OR c.room_client IS NOT NULL)
     GROUP BY q.cmd_code"
);
$codeSql->execute(['from_time' => $from, 'to_time' => $to]);

if ($codeSql->rowCount()) {
    while ($fetch = $codeSql->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($fetch['cmd_code'])) {
            $cmd = $fetch['cmd_code'];
            $cash       += getTotalPaidByMethod($cmd, '01');
            $card       += getTotalPaidByMethod($cmd, '05');
            $momo       += getTotalPaidByMethod($cmd, '06');
            $credit     += getTotalPaidByMethod($cmd, '02');
            $cheque     += getTotalPaidByMethod($cmd, '04');
            $cashCredit += getTotalPaidByMethod($cmd, '03');
            $others     += getTotalPaidByMethod($cmd, '07');
        }
    }
}

// Advances and collections by method
$cashAdvance   += getTotalAdvancePaidByMethod($from, $to, '01');
$cardAdvance   += getTotalAdvancePaidByMethod($from, $to, '05');
$momoAdvance   += getTotalAdvancePaidByMethod($from, $to, '06');
$creditAdvance += getTotalAdvancePaidByMethod($from, $to, '02');
$chequeAdvance += getTotalAdvancePaidByMethod($from, $to, '04');

$cashAdvanceLess   += getTotalAdvancePaidByMethodLess($from, $to, '01');
$cardAdvanceLess   += getTotalAdvancePaidByMethodLess($from, $to, '05');
$momoAdvanceLess   += getTotalAdvancePaidByMethodLess($from, $to, '06');
$creditAdvanceLess += getTotalAdvancePaidByMethodLess($from, $to, '02');
$chequeAdvanceLess += getTotalAdvancePaidByMethodLess($from, $to, '04');

$cashCollection   += getTotalCollectionPaidByMethod($from, $to, '01');
$cardCollection   += getTotalCollectionPaidByMethod($from, $to, '05');
$momoCollection   += getTotalCollectionPaidByMethod($from, $to, '06');
$creditCollection += getTotalCollectionPaidByMethod($from, $to, '02');
$chequeCollection += getTotalCollectionPaidByMethod($from, $to, '04');

// Room credit calculation (mirror sale_summary.php logic)
$roomStmt = $db->prepare("SELECT q.cmd_code
    FROM tbl_cmd_qty q
    INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
    WHERE q.created_at BETWEEN :from_time AND :to_time
      AND c.room_client IS NOT NULL
    GROUP BY q.cmd_code");
$roomStmt->execute(['from_time' => $from, 'to_time' => $to]);

if ($roomStmt->rowCount()) {
    while ($rc = $roomStmt->fetch(PDO::FETCH_ASSOC)) {
        $cmd = $rc['cmd_code'];

        // Get booking (room_client) for this order
        $cmdInfo = $db->prepare("SELECT room_client FROM tbl_cmd WHERE OrderCode = :code LIMIT 1");
        $cmdInfo->execute(['code' => $cmd]);
        $cmdRow = $cmdInfo->fetch(PDO::FETCH_ASSOC);
        if (!$cmdRow || empty($cmdRow['room_client'])) { continue; }
        $booking_id = $cmdRow['room_client'];

        // Order total
        $sumOrder = $db->prepare("SELECT SUM(CAST(REPLACE(unit_price, ',', '') AS DECIMAL(18,2)) * cmd_qty) AS total
            FROM tbl_cmd_qty
            WHERE cmd_code = :cmd");
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
            $allOrders = $db->prepare("SELECT SUM(CAST(REPLACE(q.unit_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS total_orders
                FROM tbl_cmd_qty q
                INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                WHERE c.room_client = :bid");
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

// If export requested, create CSV
if (isset($_POST['export_excel']) && !empty($categories)) {
    if (ob_get_length()) { ob_clean(); }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=sales_by_category_' . date('Ymd_His') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Category', 'Item Name', 'Unit Price', 'Qty', 'Total']);

    foreach ($categories as $catId => $catName) {
        // Get aggregated items from tbl_cmd_qty directly (collapse across subcategories)
        $itemStmt = $db->prepare(
            "SELECT q.item_name, q.unit_price, SUM(q.cmd_qty) AS qty, SUM(CAST(REPLACE(q.unit_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS total
             FROM tbl_cmd_qty q
             INNER JOIN tbl_cmd t ON t.OrderCode = q.cmd_code
             WHERE q.created_at BETWEEN :from_time AND :to_time
               AND q.cat_id = :cat_id
               AND (
                 t.room_client IS NOT NULL
                 OR (
                   (SELECT SUM(amount) FROM payment_tracks WHERE order_code = t.OrderCode) >=
                   (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty)
                    FROM tbl_cmd_qty q2
                    WHERE q2.cmd_code = t.OrderCode)
                 )
               )
             GROUP BY q.cmd_item, q.item_name, q.unit_price"
        );
        $itemStmt->execute(['from_time' => $from, 'to_time' => $to, 'cat_id' => $catId]);

        while ($it = $itemStmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($out, [$catName, $it['item_name'], $it['unit_price'], $it['qty'], $it['total']]);
        }
        // Category subtotal
        $sumStmt = $db->prepare(
            "SELECT SUM(CAST(REPLACE(q.unit_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS cat_total, SUM(q.cmd_qty) as cat_qty
             FROM tbl_cmd_qty q
             INNER JOIN tbl_cmd t ON t.OrderCode = q.cmd_code
             WHERE q.created_at BETWEEN :from_time AND :to_time
               AND q.cat_id = :cat_id
               AND (
                 t.room_client IS NOT NULL
                 OR (
                   (SELECT SUM(amount) FROM payment_tracks WHERE order_code = t.OrderCode) >=
                   (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty)
                    FROM tbl_cmd_qty q2
                    WHERE q2.cmd_code = t.OrderCode)
                 )
               )"
        );
        $sumStmt->execute(['from_time' => $from, 'to_time' => $to, 'cat_id' => $catId]);
        $s = $sumStmt->fetch(PDO::FETCH_ASSOC);
        fputcsv($out, [$catName . ' - Sub Total', '', '', $s['cat_qty'] ?? 0, $s['cat_total'] ?? 0]);
    }
    fclose($out);
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales by Category</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
<div class="container">
    <h3>Sales by Category</h3>
    <div>
        <small class="form-text text-info ms-2">If no dates are selected, the current open business day will be used.</small>
    </div>
    <form method="POST" class="form-inline">
        <div class="form-group">
            <label>From: </label>
            <input type="text" id="fromDate" name="select_from" class="form-control" value="<?= htmlspecialchars($reportDateFrom) ?>">
        </div>
        <div class="form-group" style="margin-left:10px;">
            <label>To: </label>
            <input type="text" id="toDate" name="select_to" class="form-control" value="<?= htmlspecialchars($reportDateTo) ?>">
        </div>
        <br>
        <div class="col-md-9" style="margin-top:10px;">

            <button name="check" class="btn btn-primary">Load</button>
            <?php if (!empty($categories)) { ?>
                <!-- <button name="export_excel" value="1" class="btn btn-success">Export to Excel (HTML)</button> -->
                <!-- <button type="button" class="btn btn-info" onclick="openXlsxExport()">Export via Server (.xlsx)</button> -->
                <button type="button" class="ms-2 btn btn-secondary" onclick="exportDisplayedToXlsx()">Export To Excel</button>
                <button type="button" class=" ms-2 btn btn-info" onclick="exportDisplayedToCsv()">Export CSV</button>
            <?php } ?>
        </div>
    </form>

    <hr>

    <?php if (!$from || !$to) { ?>
        <div class="alert alert-warning">No business day is currently open and no date was selected.</div>
    <?php } else { ?>
        <?php include '../holder/printHeader.php'; ?>
        <h5>Report for <?= htmlspecialchars($reportDateFrom) ?> to <?= htmlspecialchars($reportDateTo) ?></h5>
        <p style="font-size:12px;">Business day: <?= $from ?> to <?= $to ?></p>
        <!-- <button id="generateDompdfBtn" class="btn btn-success" onclick="openPdfDompdf()">Print Report</button>   -->

        <?php if (empty($categories)) { ?>
            <div class="alert alert-info">No sales found for the selected period.</div>
        <?php } else { ?>
            <?php $alltotal = 0; ?>

            <div class="table-responsive">
                <table id="salesResultsTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $departments = [
                        "Bar Sales" => 2,
                        "Resto Sales" => 1,
                    ];

                    foreach ($departments as $key => $val) {
                        ?>
                        <tr>
                            <td><h2><?= htmlspecialchars($key) ?></h2></td>
                            <td>
                                <?php
                                $categoryTotal = 0;
                                try {
                                    $sumStmt = $db->prepare(
                                        "SELECT SUM(CAST(REPLACE(q.unit_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS total
                                         FROM tbl_cmd_qty q
                                         INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                                         WHERE q.created_at BETWEEN :from_time AND :to_time
                                           AND q.cat_id = :val
                                           AND (
                                               c.room_client IS NOT NULL
                                               OR (
                                                   (SELECT SUM(amount) FROM payment_tracks WHERE order_code = c.OrderCode) >=
                                                   (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty) FROM tbl_cmd_qty q2 WHERE q2.cmd_code = c.OrderCode)
                                               )
                                           )"
                                    );
                                    $sumStmt->execute(['from_time' => $from, 'to_time' => $to, 'val' => $val]);
                                    $rowSum = $sumStmt->fetch(PDO::FETCH_ASSOC);
                                    $categoryTotal = (float)($rowSum['total'] ?? 0);
                                } catch (Exception $e) { $categoryTotal = 0; }
                                echo number_format($categoryTotal);
                                ?>

                                <?php
                                // Aggregate items across the whole category (no subcategory grouping)
                                // Get qualifying orders
                                $cmdSql = $db->prepare(
                                    "SELECT DISTINCT q.cmd_code
                                     FROM tbl_cmd_qty q
                                     INNER JOIN tbl_cmd c ON c.OrderCode = q.cmd_code
                                     WHERE q.created_at BETWEEN :from_time AND :to_time
                                       AND q.cat_id = :val
                                       AND (
                                        c.room_client IS NOT NULL
                                        OR (
                                            (SELECT SUM(amount) FROM payment_tracks WHERE order_code = c.OrderCode) >=
                                            (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty) FROM tbl_cmd_qty q2 WHERE q2.cmd_code = c.OrderCode)
                                        )
                                       )"
                                );
                                $cmdSql->execute(['from_time' => $from, 'to_time' => $to, 'val' => $val]);

                                $categoryItems = [];
                                $cmdRows = $cmdSql->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($cmdRows as $cmdRow) {
                                    $cmd = $cmdRow['cmd_code'];
                                    $itemSql = $db->prepare("SELECT item_name, unit_price, cmd_qty FROM tbl_cmd_qty WHERE cmd_code = :cmd AND cat_id = :val");
                                    $itemSql->execute(['cmd' => $cmd, 'val' => $val]);
                                    while ($it = $itemSql->fetch(PDO::FETCH_ASSOC)) {
                                        $menuName = $it['item_name'];
                                        $price = (float)str_replace(',', '', $it['unit_price']);
                                        $qty = (int)$it['cmd_qty'];
                                        $amount = $price * $qty;
                                        if (!isset($categoryItems[$menuName])) {
                                            $categoryItems[$menuName] = ['price' => $price, 'qty' => 0, 'total' => 0];
                                        }
                                        $categoryItems[$menuName]['qty'] += $qty;
                                        $categoryItems[$menuName]['total'] += $amount;
                                    }
                                }

                                ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach ($categoryItems as $itemName => $itemData) {
                                            $total += $itemData['total'];
                                            ?>
                                            <tr data-price="<?= $itemData['price'] ?>" data-qty="<?= $itemData['qty'] ?>" data-total="<?= $itemData['total'] ?>">
                                                <td><?= htmlspecialchars($itemName) ?></td>
                                                <td><?= number_format($itemData['price']) ?></td>
                                                <td><?= $itemData['qty'] ?></td>
                                                <td><?= number_format($itemData['total']) ?></td>
                                            </tr>
                                            <?php
                                        }
                                        $categoryQty = array_sum(array_column($categoryItems, 'qty'));
                                        ?>
                                        <tr class="cat-subtotal" data-qty="<?= $categoryQty ?>" data-total="<?= $total ?>">
                                            <th colspan="3">Sub Total</th>
                                            <th><?= number_format($total) ?> RWF</th>
                                        </tr>
                                    </tbody>
                                </table>

                            </td>
                        </tr>
                        <?php $alltotal += $total; ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Hidden export table (built on demand from visible DOM) -->
            <div style="display:none">
                <table id="salesExportTable"></table>
            </div>

            <hr>
            <h4>Grand Total (by items): <?= number_format($alltotal) ?> RWF</h4>

            <hr>
            <h5>Cash: <?= number_format($cash + $cashCollection + $cashAdvance - $cashAdvanceLess)?> RWF</h5>
            <h5>POS: <?= number_format($card + $cardCollection + $cardAdvance - $cardAdvanceLess)?> RWF</h5>
            <h5>Momo: <?= number_format($momo + $momoCollection + $momoAdvance - $momoAdvanceLess)?> RWF</h5>
            <h5>Credit: <?= number_format($credit + $creditCollection + $creditAdvance - $creditAdvanceLess)?> RWF</h5>
            <h5>Room Credit: <?= number_format($roomCreditTotal)?> RWF</h5>
            <h5>Bank Cheque: <?= number_format($cheque + $chequeCollection + $chequeAdvance - $chequeAdvanceLess)?> RWF</h5>
            <h5>CASH/CREDIT: <?= number_format($cashCredit ?? 0)?> RWF</h5>
            <h5>OTHERS: <?= number_format($others ?? 0)?> RWF</h5>

            <hr>
            <h4>Total: <?php 
                $totalCash = $cash + $cashCollection + $cashAdvance - $cashAdvanceLess;
                $totalCard = $card + $cardCollection + $cardAdvance - $cardAdvanceLess;
                $totalMomo = $momo + $momoCollection + $momoAdvance - $momoAdvanceLess;
                $totalCredit = $credit + $creditCollection + $creditAdvance - $creditAdvanceLess;
                $totalCheque = $cheque + $chequeCollection + $chequeAdvance - $chequeAdvanceLess;
                $totalCashCredit = $cashCredit ?? 0;
                $totalOthers = $others ?? 0;
                $finalGrand = $totalCash + $totalCard + $totalMomo + $totalCredit + $roomCreditTotal + $totalCheque + $totalCashCredit + $totalOthers;
                echo number_format($finalGrand); ?> RWF</h4>

            <!-- Hidden raw sums for export (unformatted) -->
            <div id="exportSums" style="display:none"
                 data-grand="<?= $finalGrand ?>"
                 data-cash="<?= $totalCash ?>"
                 data-card="<?= $totalCard ?>"
                 data-momo="<?= $totalMomo ?>"
                 data-credit="<?= $totalCredit ?>"
                 data-room="<?= $roomCreditTotal ?>"
                 data-cheque="<?= $totalCheque ?>"
                 data-cashcredit="<?= $totalCashCredit ?>"
                 data-others="<?= $totalOthers ?>">
            </div>
        <?php } ?>
    <?php } ?>

    <hr>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
function openPdfDompdf() {
      var from = document.querySelector('input[name="select_from"]').value || document.querySelector('input[name="selectday"]') && document.querySelector('input[name="selectday"]').value || new Date().toISOString().split('T')[0];
      var to = document.querySelector('input[name="select_to"]').value || from;
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = '../reception/sale_summary_pdf.php';
      form.target = '_blank';

      var serverInput = document.createElement('input');
      serverInput.type = 'hidden';
      serverInput.name = 'server';
      serverInput.value = '1';
      form.appendChild(serverInput);

      var fromInput = document.createElement('input');
      fromInput.type = 'hidden';
      fromInput.name = 'select_from';
      fromInput.value = from;
      form.appendChild(fromInput);

      var toInput = document.createElement('input');
      toInput.type = 'hidden';
      toInput.name = 'select_to';
      toInput.value = to;
      form.appendChild(toInput);
  form.submit();
  document.body.removeChild(form);
}

// function openXlsxExport() {
//   var from = document.querySelector('input[name="select_from"]').value || document.querySelector('input[name="selectday"]') && document.querySelector('input[name="selectday"]').value || new Date().toISOString().split('T')[0];
//   var to = document.querySelector('input[name="select_to"]').value || from;
//   var form = document.createElement('form');
//   form.method = 'POST';
//   form.action = 'sales_export_xlsx.php';
//   form.target = '_blank';

//   var fromInput = document.createElement('input');
//   fromInput.type = 'hidden';
//   fromInput.name = 'select_from';
//   fromInput.value = from;
//   form.appendChild(fromInput);

//   var toInput = document.createElement('input');
//   toInput.type = 'hidden';
//   toInput.name = 'select_to';
//   toInput.value = to;
//   form.appendChild(toInput);

//   document.body.appendChild(form);
//   form.submit();
//   document.body.removeChild(form);
// }
function openXlsxExport() {
  // Get the dates from the inputs or default to today
  var from = document.querySelector('input[name="select_from"]').value || 
             (document.querySelector('input[name="selectday"]') && document.querySelector('input[name="selectday"]').value) || 
             new Date().toISOString().split('T')[0];
  var to = document.querySelector('input[name="select_to"]').value || from;

  // Encode parameters to avoid issues with special characters and log URL for debugging
  var url = 'sales_export_xlsx.php?select_from=' + encodeURIComponent(from) + '&select_to=' + encodeURIComponent(to);
  console.log('Export URL:', url);
  window.open(url, '_blank');
}

// Load SheetJS if not present
(function loadSheetJS(){
  if (typeof XLSX === 'undefined') {
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js';
    s.onload = function(){ console.log('SheetJS loaded'); };
    document.head.appendChild(s);
  }
})();

function buildExportTable() {
  var exportTable = document.getElementById('salesExportTable');
  if (!exportTable) { return null; }
  exportTable.innerHTML = '';

  // Create header
  var thead = document.createElement('thead');
  thead.innerHTML = '<tr><th>Category</th><th>Item Name</th><th>Unit Price</th><th>Qty</th><th>Total</th></tr>';
  exportTable.appendChild(thead);

  var tbody = document.createElement('tbody');
  // Iterate departments / category groups
  var deptRows = document.querySelectorAll('#salesResultsTable > tbody > tr');
  deptRows.forEach(function(row) {
    var titleCell = row.querySelector('td:first-child h2');
    if (!titleCell) return; // skip non-department rows
    var catTitle = titleCell.innerText.trim();

    var innerTable = row.querySelector('td table');
    if (!innerTable) return;

    // Category header row
    var catHeader = document.createElement('tr');
    catHeader.innerHTML = '<td colspan="5"><strong>' + catTitle + '</strong></td>';
    tbody.appendChild(catHeader);

    var innerRows = innerTable.querySelectorAll('tbody > tr');
    innerRows.forEach(function(ir) {
      if (ir.classList.contains('cat-subtotal')) {
        var qty = ir.getAttribute('data-qty') || '';
        var total = ir.getAttribute('data-total') || ir.querySelector('th:last-child').innerText.replace(/[^0-9.\-]/g,'');
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>' + catTitle + ' - Sub Total</td><td></td><td></td><td>' + qty + '</td><td>' + total + '</td>';
        tbody.appendChild(tr);
      } else {
        var itemName = (ir.cells[0] && ir.cells[0].innerText) ? ir.cells[0].innerText.trim() : '';
        var priceRaw = ir.getAttribute('data-price') || (ir.cells[1] && ir.cells[1].innerText.replace(/,/g,'').replace(/[^0-9.\-]/g,'')) || '0';
        var qtyRaw = ir.getAttribute('data-qty') || (ir.cells[2] && ir.cells[2].innerText.replace(/,/g,'').replace(/[^0-9.\-]/g,'')) || '0';
        var totalRaw = ir.getAttribute('data-total') || (ir.cells[3] && ir.cells[3].innerText.replace(/,/g,'').replace(/[^0-9.\-]/g,'')) || '';
        var price = parseFloat(priceRaw) || 0;
        var qty = parseInt(qtyRaw) || 0;
        var total = totalRaw !== '' ? (parseFloat(totalRaw) || (price * qty)) : (price * qty);
        var tr = document.createElement('tr');
        tr.innerHTML = '<td></td><td>' + itemName + '</td><td>' + price + '</td><td>' + qty + '</td><td>' + total + '</td>';
        tbody.appendChild(tr);
      }
    });
  });

  // Append totals
  var exportSums = document.getElementById('exportSums');
  if (exportSums) {
    // spacer
    var spacer = document.createElement('tr'); spacer.innerHTML = '<td colspan="5"></td>';
    tbody.appendChild(spacer);

    var addRow = function(label, value) {
      var tr = document.createElement('tr');
      tr.innerHTML = '<td></td><td>' + label + '</td><td></td><td></td><td>' + (value !== null && typeof value !== 'undefined' ? value : '') + '</td>';
      tbody.appendChild(tr);
    };

    addRow('Grand Total', exportSums.getAttribute('data-grand'));
    addRow('Cash', exportSums.getAttribute('data-cash'));
    addRow('POS', exportSums.getAttribute('data-card'));
    addRow('Momo', exportSums.getAttribute('data-momo'));
    addRow('Credit', exportSums.getAttribute('data-credit'));
    addRow('Room Credit', exportSums.getAttribute('data-room'));
    addRow('Bank Cheque', exportSums.getAttribute('data-cheque'));
    addRow('Cash/Credit', exportSums.getAttribute('data-cashcredit'));
    addRow('Others', exportSums.getAttribute('data-others'));
  }

  exportTable.appendChild(tbody);
  return exportTable;
}

function exportDisplayedToXlsx() {
  if (typeof XLSX === 'undefined') { alert('Export library not yet loaded. Try again in a moment.'); return; }
  var exportTable = buildExportTable();
  if (!exportTable) { alert('No results available to export.'); return; }
  var wb = XLSX.utils.table_to_book(exportTable, {sheet:"Sales"});
  var fname = 'sales_displayed_' + new Date().toISOString().slice(0,19).replace(/[:T]/g,'_') + '.xlsx';
  XLSX.writeFile(wb, fname);
}

function exportDisplayedToCsv() {
  var exportTable = buildExportTable();
  if (!exportTable) { alert('No results available to export.'); return; }
  var rows = Array.from(exportTable.querySelectorAll('tr')).map(function(tr){
    var cols = Array.from(tr.querySelectorAll('th, td')).map(function(cell){
      return '"' + cell.innerText.replace(/"/g,'""') + '"';
    });
    return cols.join(',');
  });
  var csv = rows.join('\r\n');
  var blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
  var fname = 'sales_displayed_' + new Date().toISOString().slice(0,19).replace(/[:T]/g,'_') + '.csv';
  if (navigator.msSaveBlob) { navigator.msSaveBlob(blob, fname); }
  else {
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = fname;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
}
</script>

<?php
// Provide flatpickr available dates similar to sale_summary.php
$dateq = "SELECT DATE(opened_at) as date FROM days ORDER BY created_at DESC;";
$stmt_date = $db->prepare($dateq);
$stmt_date->execute();
$row_date = $stmt_date->fetchAll(PDO::FETCH_ASSOC);

$dates = array_column($row_date, 'date');
$uniqueDates = array_unique($dates);
$uniqueDates = array_values($uniqueDates);
?>
<script>
const availableDates = <?= json_encode($uniqueDates) ?>;
  flatpickr("#fromDate", {
    enable: availableDates,
    dateFormat: "Y-m-d",
    allowInput: true
  });
  flatpickr("#toDate", {
    enable: availableDates,
    dateFormat: "Y-m-d",
    allowInput: true
  });
</script>

</body>
</html>
