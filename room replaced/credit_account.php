<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

// Marking credit paid as Credit paid
if (isset($_GET['payment_id']) && !empty($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];

    try {
        // 1. Get the booking_id from the payments table
        $stmt = $conn->prepare("SELECT booking_id FROM payments WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $bookingData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bookingData) {
            $booking_id = $bookingData['booking_id'];

            // 2. Update the payments table
            $updatePayment = $conn->prepare("UPDATE payments SET method = 'Full' WHERE payment_id = ?");
            $paymentUpdated = $updatePayment->execute([$payment_id]);

            if ($paymentUpdated) {
                // 3. Update the tbl_acc_booking table
                $updateBooking = $conn->prepare("UPDATE tbl_acc_booking SET corporate = 0 WHERE id = ?");
                $bookingUpdated = $updateBooking->execute([$booking_id]);

                if ($bookingUpdated) {
                    echo "<div class='alert alert-success'>Successfully updated payment to 'Full' and booking to non-corporate.</div>";
                    echo "<script>
                            alert('Payment records were updated.');
                            window.location='?resto=credit_account';
                          </script>";
                } else {
                    echo "<div class='alert alert-danger'>Error updating booking corporate status.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error updating payment method.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No booking found for this payment.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>";
    }
}

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

?>

<div class="colr-area">
    <div class="container">
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="d-flex align-items-start">
                        <b class="btn btn-outline-warning"><a href="index?resto=credit_account">Credit Sales</a></b>
                        <b class="btn btn-success"><a href="index?resto=corporate">Corporate</a></b>
                    </div>
                    <div id="getBookingAlert"></div>
                    <div class="row">
                        <?php
                        // Credit Account Section
                        if (isset($_GET['resto']) && $_GET['resto'] == "credit_account"):
                            ?>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Credits Customers</h5>
                                        <form method="GET" class="d-flex">
                                            <input type="hidden" name="resto" value="credit_account">
                                            <input type="text" name="search" class="form-control me-2"
                                                placeholder="Search guest, room..."
                                                value="<?php echo htmlspecialchars($search); ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                                        </form>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <button class="btn btn-secondary btn-sm" onclick="printCorporateTable()">🖨️
                                                Print</button>
                                        </div>
                                        <div class="table-responsive" id="corporatePrintArea">
                                            <div class="hidden">
                                                <?php require_once "../holder/printHeader.php"; ?>
                                                <h2>Credit Sales</h2>
                                            </div>
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">No</th>
                                                        <th scope="col">Names</th>
                                                        <th scope="col">Room</th>
                                                        <th scope="col">Duration</th>
                                                        <th scope="col">Amount</th>
                                                        <th scope="col">Method</th>
                                                        <th scope="col" class="no-print">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $counter = 0;
                                                    $sql = "SELECT p.*, b.*, r.* FROM `payments` p 
                                                        INNER JOIN `tbl_acc_booking` b ON p.booking_id = b.id
                                                        INNER JOIN `tbl_acc_booking_room` r ON r.booking_id = b.id 
                                                        WHERE p.method = 'Credit'";
                                                    if ($search) {
                                                        $sql .= " AND (b.guest_id IN (SELECT id FROM tbl_acc_guest WHERE first_name LIKE ? OR last_name LIKE ?) 
                                                            OR r.room_id IN (SELECT id FROM tbl_acc_room WHERE room_number LIKE ?))";
                                                        $stmt = $conn->prepare($sql);
                                                        $searchTerm = "%$search%";
                                                        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
                                                    } else {
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->execute();
                                                    }
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $counter++;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $counter; ?></td>
                                                            <td><?php echo getGuestNames($row['guest_id']); ?></td>
                                                            <td><?php echo getRoomName($row['room_id']) ?></td>
                                                            <td><?php echo $row['duration']; ?> days</td>
                                                            <td><?php echo $row['amount'] . " " . getCurrencyName($row['currency']); ?>
                                                            </td>
                                                            <td><?php echo $row['method']; ?></td>
                                                            <td>
                                                                <a href="?resto=credit_account&markaspaid&payment_id=<?php echo $row['payment_id']; ?>"
                                                                    class="btn btn-sm btn-success no-print"
                                                                    onclick="return confirm('Are you sure you want to mark this payment as paid?');">
                                                                    Mark As Paid
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    if ($counter == 0) {
                                                        echo '<tr><td colspan="7" class="text-center">No credit payments found</td></tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endif;

                        // Corporate Section - Group by Corporate
                        if (isset($_GET['resto']) && $_GET['resto'] == "corporate"):
                            ?>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Corporate Customers</h5>
                                        <form method="GET" class="d-flex">
                                            <input type="hidden" name="resto" value="corporate">
                                            <input type="text" name="search" class="form-control me-2"
                                                placeholder="Search corporate, guest, room..."
                                                value="<?php echo htmlspecialchars($search); ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                                        </form>
                                    </div>
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <button class="btn btn-secondary btn-sm" onclick="printCorporateTable()">🖨️
                                            Print</button>
                                    </div>
                                    <div class="card-body" id="corporatePrintArea">
                                        <div class="hidden">
                                            <?php require_once "../holder/printHeader.php"; ?>
                                            <h2 class="mb-0">Corporate Customers</h2>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">No</th>
                                                        <th scope="col">Corporate</th>
                                                        <th scope="col">TIN</th>
                                                        <th scope="col">Guests</th>
                                                        <th scope="col">Rooms</th>
                                                        <th scope="col">Total Credit</th>
                                                        <th scope="col">Method</th>
                                                        <th scope="col" class="no-print">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $counte = 0;
                                                    $sql = "
                                                        SELECT c.id as corp_id, c.name as corp_name, c.tin_number,
                                                            SUM(p.amount) as total_credit
                                                        FROM corporates c
                                                        INNER JOIN tbl_acc_booking b ON b.corporate_id = c.id
                                                        INNER JOIN payments p ON p.booking_id = b.id
                                                        WHERE p.method = 'Credit'";
                                                    if ($search) {
                                                        $sql .= " AND (c.name LIKE ? OR c.tin_number LIKE ? OR b.guest_id IN (SELECT id FROM tbl_acc_guest WHERE first_name LIKE ? OR last_name LIKE ?) 
                                                            OR b.id IN (SELECT booking_id FROM tbl_acc_booking_room WHERE room_id IN (SELECT id FROM tbl_acc_room WHERE room_number LIKE ?)))";
                                                        $sql .= " GROUP BY c.id ORDER BY c.name ASC";
                                                        $stmt = $conn->prepare($sql);
                                                        $searchTerm = "%$search%";
                                                        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
                                                    } else {
                                                        $sql .= " GROUP BY c.id ORDER BY c.name ASC";
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->execute();
                                                    }
                                                    while ($corp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $counte++;
                                                        // Get all guests and rooms for this corporate
                                                        $guests = [];
                                                        $rooms = [];
                                                        $details_stmt = $conn->prepare("
                                                            SELECT b.guest_id, r.room_id
FROM tbl_acc_booking b
INNER JOIN tbl_acc_booking_room r 
    ON r.booking_id = b.id
LEFT JOIN payments p 
    ON p.booking_id = b.id
WHERE b.corporate_id = ?
  AND p.method = 'Credit';
                                                        ");
                                                        $details_stmt->execute([$corp['corp_id']]);
                                                        while ($detail = $details_stmt->fetch(PDO::FETCH_ASSOC)) {
                                                            $guests[] = getGuestNames($detail['guest_id']);
                                                            $rooms[] = getRoomName($detail['room_id']);
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $counte; ?></td>
                                                            <td><?php echo htmlspecialchars($corp['corp_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($corp['tin_number']); ?></td>
                                                            <td><?php echo implode(', ', array_unique($guests)); ?></td>
                                                            <td><?php echo implode(', ', array_unique($rooms)); ?></td>
                                                            <td><?php echo number_format($corp['total_credit']) . " RWF"; ?>
                                                            </td>
                                                            <td>Credit</td>
                                                            <td class="no-print">
                                                                <a href="?resto=corporate&print_invoice=1&corp_id=<?php echo $corp['corp_id']; ?>"
                                                                    class="btn btn-sm btn-info">
                                                                    🖨️ Invoice
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    if ($counte == 0) {
                                                        echo '<tr><td colspan="8" class="text-center">No corporate credit found</td></tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            // Print invoice for corporate
                            if (isset($_GET['print_invoice']) && isset($_GET['corp_id'])) {
                                $corp_id = intval($_GET['corp_id']);
                                $corp_stmt = $conn->prepare("SELECT * FROM corporates WHERE id = ?");
                                $corp_stmt->execute([$corp_id]);
                                $corp = $corp_stmt->fetch(PDO::FETCH_ASSOC);

                                $credit_stmt = $conn->prepare("
                                SELECT p.*, b.*, r.*, g.first_name, g.last_name
                                FROM payments p
                                INNER JOIN tbl_acc_booking b ON p.booking_id = b.id
                                INNER JOIN tbl_acc_booking_room r ON r.booking_id = b.id
                                INNER JOIN tbl_acc_guest g ON b.guest_id = g.id
                                WHERE p.method = 'Credit' AND b.corporate_id = ?
                            ");
                                $credit_stmt->execute([$corp_id]);
                                ?>
                                <script>
                                    const printWindow = window.open('', '', 'height=600,width=800');
                                    let invoiceHtml = `
                                <html>
                                <head>
                                    <title>Corporate Invoice</title>
                                    <style>
                                        body { font-family: Arial, sans-serif; padding: 20px; }
                                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                                        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; font-size:18px; }
                                    </style>
                                </head>
                                <body>
                                    <div style="text-align:center;">
                                        <?php ob_start();
                                        include "../holder/printHeader.php";
                                        echo str_replace("\n", "", ob_get_clean()); ?>
                                    </div>
                                    <h2>Corporate Invoice</h2>
                                    <p><strong>Company:</strong> <?php echo htmlspecialchars($corp['name']); ?></p>
                                    <p><strong>TIN:</strong> <?php echo htmlspecialchars($corp['tin_number']); ?></p>
                                    <table>
                                        <tr>
                                        <td>No</td>
                                            <th>Guest</th>
                                            <th>Room</th>
                                            <th>Duration</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                        </tr>
                                        <?php
                                        $total = 0;
                                        $i = 0;
                                        while ($row = $credit_stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $i++;
                                            $total += $row['amount'];
                                            ?>
                                            <tr>
                                            <td><?php echo $i; ?></td>
                                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                                <td><?php echo getRoomName($row['room_id']); ?></td>
                                                <td><?php echo $row['duration']; ?> days</td>
                                                <td><?php echo number_format($row['amount']) . " " . getCurrencyName($row['currency']); ?></td>
                                                <td><?php echo $row['method']; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <tr>
                                            <th colspan="3">Total</th>
                                            <th colspan="2"><?php echo number_format($total) . " RWF"; ?></th>
                                        </tr>
                                    </table>
                                </body>
                                </html>
                                `;
                            printWindow.document.write(invoiceHtml);
                            printWindow.document.close();
                            printWindow.focus();
                            printWindow.print();
                        </script>
                        <?php
                            }
                        endif; // End of if statement for 'corporate'
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printCorporateTable() {
        const printContents = document.getElementById("corporatePrintArea").innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
        <html>
        <head>
            <title>Print Corporate Customers</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .hidden{display: none;}
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                @media print {
                    .hidden { display: block !important; }
                    .no-print { display: none !important; }
                    .print-header { text-align: center; margin-bottom: 20px; }
                    .print-header img { max-height: 100px; }
                }
            </style>
        </head>
        <body>
            ${printContents}
        </body>
        </html>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }