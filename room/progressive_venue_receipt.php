<?php
// progressive_venue_receipt.php - Venue Progressive Payment Receipt
include "../inc/conn.php";

// Get booking ID from URL
$booking_id = isset($_REQUEST['booking_id']) ? intval($_REQUEST['booking_id']) : 0;

if ($booking_id <= 0) {
    die('Invalid booking ID');
}

// Fetch venue reservation data
$sql = $db->prepare("SELECT * FROM tbl_ev_venue_reservations WHERE id = ?");
$sql->execute([$booking_id]);
$venue_reservation = $sql->fetch();

if (!$venue_reservation) {
    die('Venue reservation not found');
}

$customer_id = $venue_reservation['customer_id'];
$venue_id = $venue_reservation['venue_id'];

// Fetch customer data
$sql = $db->prepare("SELECT * FROM tbl_ev_customers WHERE id = ?");
$sql->execute([$customer_id]);
$customer = $sql->fetch();

// Fetch venue name
$venue_name = getVenueName($venue_id);

// Calculate amounts for venue
$amountToPay = 0;
$paidAmount = 0;
$items_total = 0;

// Get venue reservation amount (daily rate * number of days)
$res_start = $venue_reservation['reservation_date'];
$res_end = $venue_reservation['reservation_end_date'];
$start_date = date_create($res_start);
$end_date = date_create($res_end);
$diff = date_diff($start_date, $end_date);
$num_days = max(1, (($diff->format("%a")) + 1));
$venue_daily_rate = $venue_reservation['amount'];
$venue_cost = $venue_daily_rate * $num_days;

$amountToPay += $venue_cost;

// Get additional items from venu_orders
$sql = $db->prepare("SELECT * FROM venu_orders WHERE booking_id = ?");
$sql->execute([$booking_id]);
while ($row = $sql->fetch()) {
    $item_total = $row['qty'] * $row['price'];
    $items_total += $item_total;
}

$amountToPay += $items_total;

// Get venue payments from venue_payments table
$sql = $db->prepare("SELECT * FROM venue_payments WHERE booking_id = ?");
$sql->execute([$booking_id]);
$payments = $sql->fetchAll();

foreach ($payments as $payment) {
    $paidAmount += $payment['amount'];
}

$due_amount = $amountToPay - $paidAmount;

// Get logged-in user name
$printedBy = '';
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $stmtPB = $db->prepare('SELECT u.f_name, u.l_name FROM tbl_users u INNER JOIN tbl_user_log l ON u.user_id = l.user_id WHERE l.user_id = ? LIMIT 1');
    $stmtPB->execute([$_SESSION['user_id']]);
    $rowPB = $stmtPB->fetch(PDO::FETCH_ASSOC);
    if ($rowPB) {
        $printedBy = trim(($rowPB['f_name'] ?? '') . ' ' . ($rowPB['l_name'] ?? ''));
    }
}
if ($printedBy === '') {
    if (isset($_SESSION['user_fullname'])) { $printedBy = $_SESSION['user_fullname']; }
    elseif (isset($_SESSION['fullname'])) { $printedBy = $_SESSION['fullname']; }
    elseif (isset($_SESSION['name'])) { $printedBy = $_SESSION['name']; }
    elseif (isset($_SESSION['username'])) { $printedBy = $_SESSION['username']; }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Venue Receipt - Reservation #<?php echo $booking_id; ?></title>
    
    <style>
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        body {
            font-family: 'Courier New', monospace;
            margin: 0 auto;
            padding: 10vw;
            line-height: 1.2;
            font-size: 11px;
            width: 80mm;
            max-width: 80mm;
            text-align: center;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 1px dashed #333;
            padding-bottom: 5px;
        }
        .company-info {
            margin-bottom: 5px;
            font-size: 10px;
        }
        .logo {
            width: 60px;
            height: auto;
            margin: 5px auto;
            display: block;
        }
        .receipt-title {
            font-size: 12px;
            font-weight: bold;
            margin: 5px 0;
        }
        .line {
            border-bottom: 1px dashed #333;
            margin: 5px 0;
            padding: 2px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            text-align: left;
        }
        .left-align {
            text-align: left;
        }
        .bold {
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .payment-item {
            margin: 2px 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin: 5px 0;
        }
        table th, table td {
            border: 1px solid #333;
            padding: 3px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .signature-section {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            margin-top: 15px;
            font-size: 9px;

        }
        .signature-box {
            width: 30%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 20px;
            margin-top: 20px;
        }
        .no-print {
            margin: 10px 0;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                width: auto;
                max-width: none;
            }
        }
    </style>
<div class="no-print">
    <button onclick="window.print()" style="padding: 5px 10px; font-size: 12px;">Print</button>
    <button onclick="window.close()" style="padding: 5px 10px; font-size: 12px; margin-left: 5px;">Close</button>
</div>

<div class="header">
    <img src="../img/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
    <div class="company-info center bold">
        <?= $company_address ?><br>
        TIN: <?= $company_tin ?><br>
        Tel: <?= $company_phone ?><br>
        Email: <?= $company_email ?><br>
    </div>
    <div class="receipt-title center">VENUE PAYMENT RECEIPT</div>
    <div class="center">Reservation ID: <?php echo $booking_id; ?></div>
</div>

<div class="line"></div>

<div class="left-align">
    <div class="bold">CLIENT INFORMATION</div>
    <div>Name: <?php echo htmlspecialchars($customer['names']); ?></div>
    <div>Phone: <?php echo htmlspecialchars($customer['phone']); ?></div>
    <div>TIN: <?php echo htmlspecialchars($customer['tin']); ?></div>
    <div>Venue: <?php echo htmlspecialchars($venue_name); ?></div>
    <div>Check-in: <?php echo htmlspecialchars($res_start); ?></div>
    <div>Check-out: <?php echo htmlspecialchars($res_end); ?></div>
    <div>Days: <?php echo $num_days; ?></div>
    <div>PAX: <?php echo htmlspecialchars($venue_reservation['pax']); ?></div>
</div>

<div class="line"></div>

<div>
    <div class="bold">CHARGES BREAKDOWN</div>
    <div class="row">
        <span>Venue Rental (<?php echo $num_days; ?> days):</span>
        <span>RWF <?php echo number_format($venue_cost); ?></span>
    </div>
    <?php if ($items_total > 0): ?>
    <div class="row">
        <span>Additional Items:</span>
        <span>RWF <?php echo number_format($items_total); ?></span>
    </div>
    <?php endif; ?>
</div>

<div class="line"></div>

<div>
    <div class="bold">PAYMENT SUMMARY</div>
    <div class="row">
        <span>Total Amount:</span>
        <span>RWF <?php echo number_format($amountToPay); ?></span>
    </div>
    <div class="row">
        <span>Amount Paid:</span>
        <span>RWF <?php echo number_format($paidAmount); ?></span>
    </div>
    <div class="row bold">
        <span>Balance:</span>
        <span>RWF <?php echo number_format($due_amount); ?></span>
    </div>
    <div class="row">
        <span>Status:</span>
        <span><?php echo $due_amount === 0 ? 'PAID' : 'PARTIAL'; ?></span>
    </div>
</div>

<div class="line"></div>

<div class="left-align">
    <div class="bold">PAYMENT HISTORY</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sum = 0;
            foreach ($payments as $payment):
                $sum++;
            ?>
            <tr>
                <td><?php echo $sum; ?></td>
                <td>RWF <?php echo number_format($payment['amount']); ?></td>
                <td><?php echo htmlspecialchars($payment['method']); ?></td>
                <td><?php echo $payment['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="line"></div>

<div class="signature-section">
    
        <p><strong>Printed By :</strong> <?php echo htmlspecialchars($printedBy); ?></p>
        <p style="margin-top: 4em;">signature: __________________________</p>
    
</div>

<div class="line"></div>

<div class="center" style="font-size: 9px;">
    Printed: <?php echo date('d/M/Y H:i:s'); ?><br>
    Thank you for choosing us!
</div>
    <script>
        // Auto-print on load if requested
        if (window.location.search.includes('auto_print=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</head>
<body>
</body>
</html>
