<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    include_once '../inc/conn.php';
    include_once '../inc/function.php';

    // Helper function to safely prepare statements
    function prepareStmt($conn, $sql) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        return $stmt;
    }

    // Sanitize and validate input
    $booking_id   = intval($_POST['booking_id'] ?? 0);
    $refund_amount = floatval($_POST['refund_amount'] ?? 0);
    $reason       = mysqli_real_escape_string($conn, trim($_POST['reason'] ?? ''));
    $method       = mysqli_real_escape_string($conn, trim($_POST['refund_method'] ?? ''));
    $remark       = $reason ?: "Refund processed";
    $currency     = 1; // RWF
    $rate         = 1; // Rate for RWF
    $currency_amount = $refund_amount * $rate;
    $payment_time = time();

    // Validate inputs
    if ($booking_id === 0) {
        showError("Invalid booking ID.");
    }

    if ($refund_amount >= 0) {
        showError("Refund amount must be negative.");
    }

    if (empty($method)) {
        showError("Refund method is required.");
    }

    // Check if booking exists and has sufficient paid amount
    $check_booking = prepareStmt($conn, "SELECT id, guest_id FROM tbl_acc_booking WHERE id = ?");
    if (!$check_booking) showError("Database error: Unable to verify booking.");
    
    $check_booking->bind_param("i", $booking_id);
    $check_booking->execute();
    $booking_result = $check_booking->get_result();
    
    if ($booking_result->num_rows === 0) {
        showError("Booking not found.");
    }
    $check_booking->close();

    // Calculate total paid amount for this booking
    $check_payments = prepareStmt($conn, "SELECT COALESCE(SUM(amount), 0) as total_paid FROM payments WHERE booking_id = ?");
    if (!$check_payments) showError("Database error: Unable to check payment history.");
    
    $check_payments->bind_param("i", $booking_id);
    $check_payments->execute();
    $payment_result = $check_payments->get_result();
    $payment_data = $payment_result->fetch_assoc();
    $total_paid = floatval($payment_data['total_paid']);
    $check_payments->close();

    // Check if refund amount is valid (can't refund more than what was paid)
    $new_balance = $total_paid + $refund_amount; // refund_amount is negative
    if ($new_balance < 0) {
        showError("Refund amount exceeds available balance. Maximum refundable: " . number_format($total_paid) . " RWF");
    }

    // Start transaction for data consistency
    $conn->begin_transaction();

    try {
        // Insert refund record into payments table with negative amount
        $stmt = prepareStmt($conn, "INSERT INTO payments (booking_id, amount, method, remark, currency, currency_amount, rate, payment_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Failed to prepare payment insert statement.");
        }
        
        $stmt->bind_param("idsssssi", $booking_id, $refund_amount, $method, $remark, $currency, $currency_amount, $rate, $payment_time);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute payment insert: " . $stmt->error);
        }
        $stmt->close();

        // Update invoice balance if invoice table exists
        $update_invoice = prepareStmt($conn, "UPDATE invoice SET Paid = Paid + ?, balance = Total - (Paid + ?) WHERE booking_id = ?");
        if ($update_invoice) {
            $update_invoice->bind_param("ddi", $refund_amount, $refund_amount, $booking_id);
            $update_invoice->execute();
            $update_invoice->close();
        }

        // Commit transaction
        $conn->commit();

        // Log the refund action (optional)
        error_log("Refund processed: Booking $booking_id, Amount: $refund_amount, Method: $method");

        // Show success message
        showSuccess($booking_id, $refund_amount);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Refund transaction failed: " . $e->getMessage());
        showError("Transaction failed: " . $e->getMessage());
    }

} else {
    showError("Invalid request method or missing parameters.");
}

/**
 * Display error message using SweetAlert
 */
function showError($message) {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Refund Error</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body { 
                background: #f8f9fa; 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "error",
                title: "Refund Failed",
                html: "' . addslashes($message) . '",
                confirmButtonText: "Try Again",
                confirmButtonColor: "#dc3545",
                backdrop: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.back();
                }
            });
        </script>
    </body>
    </html>';
    exit;
}

/**
 * Display success message using SweetAlert
 */
function showSuccess($booking_id, $refund_amount) {
    $formatted_amount = number_format(abs($refund_amount));
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Refund Successful</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body { 
                background: #f8f9fa; 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Refund Processed Successfully",
                html: "Refund of <strong>' . $formatted_amount . ' RWF</strong> has been processed for booking <strong>#' . $booking_id . '</strong>.",
                confirmButtonText: "Return to Booking",
                confirmButtonColor: "#28a745",
                showClass: {
                    popup: "animate__animated animate__fadeInDown"
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOutUp"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "./?resto=room_booking_details&&booking_id=' . $booking_id . '";
                }
            });
        </script>
    </body>
    </html>';
    exit;
}
?>