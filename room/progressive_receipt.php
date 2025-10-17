<?php
// progressive_receipt.php
include "../inc/conn.php";

// Get booking ID from URL
$booking_id = isset($_REQUEST['booking_id']) ? intval($_REQUEST['booking_id']) : 0;

if ($booking_id <= 0) {
    die('Invalid booking ID');
}

// Fetch booking data
$sql = $db->prepare("SELECT * FROM tbl_acc_booking WHERE id = ?");
$sql->execute([$booking_id]);
$booking = $sql->fetch();

if (!$booking) {
    die('Booking not found');
}

$guest_id = $booking['guest_id'];

// Fetch guest data
$sql = $db->prepare("SELECT * FROM tbl_acc_guest WHERE id = ?");
$sql->execute([$guest_id]);
$guest = $sql->fetch();

// Fetch room name
$room_name = getRoomName(getBookedRoom($booking_id));

// Calculate amounts (reuse your existing logic)
$amountToPay = 0;
$paidAmount = 0;
$menutotal = 0;

// Get orders
$sql = $db->prepare("SELECT * FROM orders WHERE booking_id = ?");
$sql->execute([$booking_id]);
while ($row = $sql->fetch()) {
    $amountToPay += ($row['price'] * $row['qty']);
}

// Get guest booking
$sql = $db->prepare("SELECT * FROM guest_booking WHERE booking_id = ?");
$sql->execute([$booking_id]);
while ($row = $sql->fetch()) {
    $amountToPay += $row['amount'];
}

// Get menu total
$code = getClientOrder($booking_id);
if ($code) {
    $sql = $db->prepare("SELECT * FROM tbl_cmd_qty WHERE cmd_code = ?");
    $sql->execute([$code]);
    while ($fetch = $sql->fetch()) {
        $GetStsmenu = $db->prepare("SELECT * FROM menu WHERE menu_id = ?");
        $GetStsmenu->execute([$fetch['cmd_item']]);
        $fstsmenu = $GetStsmenu->fetch();
        $menutotal += ($fstsmenu['menu_price'] * $fetch['cmd_qty']);
    }
}

// Calculate booking amount
$ckin = date_create($booking['checkin_date']);
$ckout = date_create($booking['checkout_date']);
$diff = date_diff($ckin, $ckout);
$night = $diff->format("%a");
$booking_amount = $booking['room_price'] * $night;

$amountToPay += $booking_amount + $menutotal;

// Get payments
$sql = $db->prepare("SELECT * FROM payments WHERE booking_id = ?");
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

// Function to get client order code
function getClientOrder($booking_id)
{
    include '../inc/conn.php';

    $sql = "SELECT * FROM `tbl_cmd` WHERE room_client='" . $booking_id . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            return $row["OrderCode"];
        }
    } else {
        return "";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - Booking #<?php echo $booking_id; ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        body {
            font-family: 'Courier New', monospace;
            margin: 0 auto;
            padding: 5px;
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
        <?= $company_name ?><br>
                    TIN/VAT :<?= $company_tin ?><br>
                    Tel: <?= $company_phone ?><br>
                    <?= $company_email ?><br>
    </div>
    <div class="receipt-title center">PAYMENT RECEIPT</div>
    <div class="center">Booking ID: <?php echo $booking_id; ?></div>
</div>

<div class="line"></div>

<div class="left-align">
    <div class="bold">GUEST INFORMATION</div>
    <div>Name: <?php echo htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']); ?></div>
    <div>Room: <?php echo htmlspecialchars($room_name); ?></div>
    <div>Check-in: <?php echo htmlspecialchars($booking['checkin_date']); ?></div>
    <div>Check-out: <?php echo htmlspecialchars($booking['checkout_date']); ?></div>
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
    <?php 
    $sum = 0;
    foreach ($payments as $payment): 
        $sum++;
    ?>
    <div class="payment-item">
        <?php echo $sum; ?>. RWF <?php echo number_format($payment['amount']); ?> 
        (<?php echo htmlspecialchars($payment['method']); ?>) 
        <?php echo date('d/M/Y', $payment['payment_time']); ?>
    </div>
    <?php endforeach; ?>
</div>

<div class="line"></div>

<div class="center" style="font-size: 9px;">
    Printed: <?php echo date('d/M/Y H:i:s'); ?><br>
    By: <?php echo htmlspecialchars($printedBy); ?><br>
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
