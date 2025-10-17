

<?php
include "../inc/conn.php";
// Query to fetch all invoices
$sql = "SELECT `inv_id`, `booking_id`, `inv_date`, `inv_guest_id`, `room_no`, `description`, `Total`, `Paid`, `balance` FROM `invoice`";
$result = $conn->query($sql);
?>

<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>Invoice Management System</title>-->
    
    <!-- Bootstrap CSS -->
<!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">-->
    
    <style>
        .invoice-print-area {
            display: none;
        }
        @media print {
            .no-print {
                display: none;
            }
            .invoice-print-area {
                display: block;
            }
            .card{
                display: none;
            }
        }
    </style>


    <div class="container mt-5">
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">All Invoices</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>Booking ID</th>
                                <th>Invoice Date</th>
                                <th>Guest Name</th>
                                <th>Room Number</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <!--<th>Actions</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            $paid = 0;
                            if ($result && $result->num_rows > 0) {
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    $grandTotal += $row["Total"];
                                    $paid += $row["Paid"];
                            ?>
                            <tr>
                                <td><?php echo $row["inv_id"]; ?></td>
                                <td><?php echo $row["booking_id"]; ?></td>
                                <td><?php echo $row["inv_date"]; ?></td>
                                <td><?php echo getGuestNames($row["inv_guest_id"]); ?></td>
                                <td><?php echo $row["room_no"]; ?></td>
                                <td><?php echo number_format($row["Total"]); ?></td>
                                <td><?php echo number_format($row["Paid"]); ?></td>
                                <td><?php echo $row["balance"]; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary print-invoice" 
                                            data-inv-id="<?php echo $row["inv_id"]; ?>"
                                            data-booking-id="<?php echo $row["booking_id"]; ?>"
                                            data-inv-date="<?php echo $row["inv_date"]; ?>"
                                            data-guest-name="<?php echo getGuestNames($row["inv_guest_id"]); ?>"
                                            data-guest-id="<?php echo $row["inv_guest_id"]; ?>"
                                            data-room-no="<?php echo $row["room_no"]; ?>"
                                            data-description="<?php echo $row["description"]; ?>"
                                            data-total="<?php echo $row["Total"]; ?>"
                                            data-paid="<?php echo $row["Paid"]; ?>"
                                            data-balance="<?php echo $row["balance"]; ?>">
                                        <i class="bi bi-printer"></i> Print
                                    </button>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No invoices found</td></tr>";
                            }
                            ?>
                            <tr>
                                <td colspan=5><strong>Grand Total</strong></td> <td><strong> <?php echo number_format($grandTotal) ?></strong></td> <td><strong> <?php echo number_format($paid) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Print Template -->
    <div id="invoice-print-template" class="invoice-print-area">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2>INVOICE</h2>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-6">
                    <h4>Invoice Details</h4>
                    <p><strong>Invoice ID:</strong> <span id="print-inv-id"></span></p>
                    <p><strong>Booking ID:</strong> <span id="print-booking-id"></span></p>
                    <p><strong>Date:</strong> <span id="print-inv-date"></span></p>
                </div>
                <div class="col-6">
                    <h4>Guest Information</h4>
                    <p><strong>Guest Name:</strong> <span id="print-guest-name"></span></p>
                    <p><strong>Guest ID:</strong> <span id="print-guest-id"></span></p>
                    <p><strong>Room Number:</strong> <span id="print-room-no"></span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <h4>Description</h4>
                    <p id="print-description"></p>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Balance</th>
                        </tr>
                        <tr>
                            <td id="print-total"></td>
                            <td id="print-paid"></td>
                            <td id="print-balance"></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-6">
                    <p>Guest Signature</p>
                    <p>_______________________</p>
                </div>
                <div class="col-6 text-end">
                    <p>Authorized Signature</p>
                    <p>_______________________</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Print Invoice Script -->
    <script>
        // Add event listeners to all print buttons
        document.addEventListener('DOMContentLoaded', function() {
            const printButtons = document.querySelectorAll('.print-invoice');
            
            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get invoice data from data attributes
                    const invId = this.getAttribute('data-inv-id');
                    const bookingId = this.getAttribute('data-booking-id');
                    const invDate = this.getAttribute('data-inv-date');
                    const guestId = this.getAttribute('data-guest-id');
                    const guestName = this.getAttribute('data-guest-name');
                    const roomNo = this.getAttribute('data-room-no');
                    const description = this.getAttribute('data-description');
                    const total = this.getAttribute('data-total');
                    const paid = this.getAttribute('data-paid');
                    const balance = this.getAttribute('data-balance');
                    
                    // Fill the print template with invoice data
                    document.getElementById('print-inv-id').textContent = invId;
                    document.getElementById('print-booking-id').textContent = bookingId;
                    document.getElementById('print-inv-date').textContent = invDate;
                    document.getElementById('print-guest-name').textContent = guestName;
                    document.getElementById('print-guest-id').textContent = guestId;
                    document.getElementById('print-room-no').textContent = roomNo;
                    document.getElementById('print-description').textContent = description || 'No description provided';
                    document.getElementById('print-total').textContent = total;
                    document.getElementById('print-paid').textContent = paid;
                    document.getElementById('print-balance').textContent = balance;
                    
                    // Print the invoice
                    window.print();
                });
            });
        });
    </script>
<!--</body>-->
<!--</html>-->

<?php
// Close the database connection
$conn->close();
?>