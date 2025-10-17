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



?>

<div class="colr-area">
    <div class="container">
        <!-- Side Menu -->

        <!-- / Side Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Top Navbar -->

            <!-- / Top Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Breadcrumbs -->


                    <!-- booking off canvas -->
                    <!-- add booking -->

                    <div class="d-flex align-items-start">
                        <b class="btn btn-outline-warning"><a href="index?resto=credit_account">Credit Sales</a></b>
                        <b class="btn btn-success"><a href="index?resto=corporate">Corporate</a></b>
                    </div>


                    <div id="getBookingAlert"></div>


                    <!-- Booking List -->
                    <div class="row">
                        <?php
                        // Include the header file if it exists
                        // if (isset($_GET['resto']) == "credit_account"):
                        if (isset($_GET['resto']) && $_GET['resto'] == "credit_account"):

                        ?>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Credits Customers</h5>

                                    </div>
                                    <div class="card-body">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <button class="btn btn-secondary btn-sm" onclick="printCorporateTable()">🖨️ Print</button>
                                        </div>
                                        <div class="table-responsive" id="corporatePrintArea">
                                            <div class="hidden">
                                                <?php
                                                require_once "../holder/printHeader.php";
                                                ?>
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

                                                    // Execute the query properly
                                                    $stmt = $conn->prepare("SELECT p.*, b.*, r.* FROM `payments` p 
                                        INNER JOIN `tbl_acc_booking` b ON p.booking_id = b.id
                                        INNER JOIN `tbl_acc_booking_room` r ON r.booking_id = b.id 
                                        WHERE p.method = 'Credit'");
                                                    $stmt->execute();

                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $counter++;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $counter; ?></td>
                                                            <td><?php echo getGuestNames($row['guest_id']); ?></td>
                                                            <td><?php echo getRoomName($row['room_id']) ?></td>
                                                            <td><?php echo $row['duration']; ?> days</td>
                                                            <td><?php echo $row['amount'] . " " . getCurrencyName($row['currency']); ?></td>
                                                            <td><?php echo $row['method']; ?></td>
                                                            <td>
                                                                <a href="?resto=credit_account&markaspaid&payment_id=<?php echo $row['payment_id']; ?>" class="btn btn-sm btn-success no-print" onclick="return confirm('Are you sure you want to mark this payment as paid?');">
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
                        if (isset($_GET['resto']) && $_GET['resto'] == "corporate") :
                        ?>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Corporate Customers</h5>

                                    </div>
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <button class="btn btn-secondary btn-sm" onclick="printCorporateTable()">🖨️ Print</button>
                                    </div>
                                    <div class="card-body" id="corporatePrintArea">
                                        <div class="hidden">
                                            <?php
                                            require_once "../holder/printHeader.php";
                                            ?>
                                            <h2 class="mb-0">Corporate Customers</h2>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">No</th>
                                                        <th scope="col">Names</th>
                                                        <th scope="col">Company</th>
                                                        <th scope="col">Room</th>
                                                        <th scope="col">Duration</th>
                                                        <th scope="col">Amount</th>
                                                        <th scope="col">Method</th>
                                                        <th scope="col" class="no-print">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $counte = 0;

                                                    // Execute the query properly
                                                    $stmt = $conn->prepare("SELECT p.*, b.*, r.* FROM `payments` p 
                                        INNER JOIN `tbl_acc_booking` b ON p.booking_id = b.id
                                        INNER JOIN `tbl_acc_booking_room` r ON r.booking_id = b.id
                                        WHERE p.method = 'Credit' AND p.remark = 'Corporate' AND b.corporate = 1 ORDER BY b.company ASC");
                                                    $stmt->execute();

                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $counte++;

                                                        $guest_name = getGuestNames($row['guest_id']);
                                                        $room_name = getRoomName($row['room_id']);
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $counte; ?></td>
                                                            <td><?php echo getGuestNames($row['guest_id']); ?></td>
                                                            <td><?php echo $row['company']?? getGuestNames($row['guest_id']); ?></td>
                                                            <td><?php echo getRoomName($row['room_id']) ?></td>
                                                            <td><?php echo $row['duration']; ?> days</td>
                                                            <td><?php echo $row['amount'] . " " . getCurrencyName($row['currency']); ?></td>
                                                            <td><?php echo $row['method']; ?></td>
                                                            <td class="no-print">
                                                                <a href="?resto=credit_account&markaspaid&payment_id=<?php echo $row['payment_id']; ?>" class="btn btn-sm btn-success no-print" onclick="return confirm('Are you sure you want to mark this payment as paid?');">
                                                                    Mark As Paid
                                                                </a>
                                                                <button class="btn btn-sm btn-info" onclick='printInvoice(<?php echo json_encode([
    "guest_name" => $guest_name,
    "company" => $row['company'],
    "tin" => $row['tin_number'],
    "room_name" => $room_name,
    "duration" => $row['duration'],
    "amount" => $row['amount'],
    "currency" => getCurrencyName($row['currency']),
    "method" => $row['method']
]); ?>)'>🖨️ Invoice</button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }

                                                    if ($counte == 0) {
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
                        endif; // End of if statement for 'credit_account'
                        ?>
                    </div>
                </div>
                <!-- / Content -->
            </div>
        </div>
    </div>
</div>



<script>
    function printInvoice(data) {
        const printWindow = window.open('', '', 'height=600,width=800');
            const logoSection = `
        <div style="text-align:center;">
            <?php ob_start();
            include "../holder/printHeader.php";
            echo str_replace("\n", "", ob_get_clean()); ?>
        </div>
    `;
        
        const invoiceHtml = `
        <html>
        <head>
            <title>Invoice</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { padding: 10px; border: 1px solid #ccc; text-align: left; font-size:30px; }
            </style>
        </head>
        <body>
            ${logoSection}
            <h2>Invoice</h2>
            <table>
            <tr><th>Guest</th><td>${data.guest_name}</td></tr>
            <tr><th>Company</th><td>${data.company}</td></tr>
            <tr><th>Tin Number</th><td>${data.tin}</td></tr>
            <tr><th>Room</th><td>${data.room_name}</td></tr>
            <tr><th>Duration</th><td>${data.duration} days</td></tr>
            <tr><th>Amount</th><td>${data.amount} ${data.currency}</td></tr>
            <tr><th>Method</th><td>${data.method}</td></tr>
            </table>
        </body>
        </html>
    `;

        printWindow.document.write(invoiceHtml);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }


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
        /* Show only in print */
        .hidden {
            display: block !important;
        }

        .no-print {
            display: none !important;
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-header img {
            max-height: 100px;
        }
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
        location.reload(); // reload to return to normal view
    }
</script>

</body>

</html>