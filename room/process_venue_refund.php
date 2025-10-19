<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    include_once '../inc/conn.php'; // make sure $db is your PDO connection
    include_once '../inc/function.php';

    // Sanitize and validate input
    $booking_id     = intval($_POST['booking_id'] ?? 0);
    $refund_amount  = floatval($_POST['refund_amount'] ?? 0);
    $reason         = trim($_POST['reason'] ?? '');
    $method         = trim($_POST['refund_method'] ?? '');
    $remark         = $reason ?: "Refund processed";
    $currency       = 1; // RWF
    $rate           = 1;
    $currency_amount = $refund_amount * $rate;
    $payment_time   = time();

    // Convert refund amount to negative
    if ($refund_amount > 0) {
        $refund_amount = -$refund_amount;
    }

    // Validation
    if ($booking_id === 0) {
        showError("Invalid booking ID.");
    }
    if ($refund_amount >= 0) {
        showError("Refund amount must be negative.");
    }
    if (empty($method)) {
        showError("Refund method is required.");
    }

    try {
        // Check if booking exists
        $stmt = $db->prepare("SELECT id, customer_id FROM `tbl_ev_venue_reservations` WHERE id = ?");
        $stmt->execute([$booking_id]);
        if ($stmt->rowCount() === 0) {
            showError("Booking not found.");
        }

        // Calculate total paid amount
        $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) AS total_paid FROM venue_payments WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $payment_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_paid = floatval($payment_data['total_paid'] ?? 0);

        // Validate refund amount
        $new_balance = $total_paid + $refund_amount; // refund_amount is negative
        if ($new_balance < 0) {
            showError("Refund amount exceeds available balance. Maximum refundable: " . number_format($total_paid) . " RWF");
        }

        // Start transaction
        $db->beginTransaction();

        // Insert refund into venue_payments
        $stmt = $db->prepare("INSERT INTO venue_payments 
                            (booking_id, amount, method, remark, currency, currency_amount, rate, payment_time)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$booking_id, $refund_amount, $method, $remark, $currency, $currency_amount, $rate, $payment_time]);

        // Update invoice if exists
        // $stmt = $db->prepare("
        //     UPDATE invoice 
        //     SET Paid = Paid + ?, balance = Total - (Paid + ?) 
        //     WHERE booking_id = ?
        // ");
        // $stmt->execute([$refund_amount, $refund_amount, $booking_id]);

        // Commit
        $db->commit();

        // Log success
        error_log("Refund processed: Booking $booking_id, Amount: $refund_amount, Method: $method");

        // Show success message
        showSuccess($booking_id, $refund_amount);

    } catch (Exception $e) {
        $db->rollBack();
        error_log("Refund transaction failed: " . $e->getMessage());
        showError("Transaction failed: " . $e->getMessage());
    }

} else {
    showError("Invalid request method or missing parameters.");
}

/**
 * SweetAlert error message
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
            body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
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
            }).then(() => {
                window.location.href = "./?resto=venue_refund&&booking_id=' . $booking_id . '&balance=' . abs($refund_amount) . '";
            });
        </script>
    </body>
    </html>';
    exit;
}

/**
 * SweetAlert success message
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
            body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Refund Processed Successfully",
                html: "Refund of <strong>' . $formatted_amount . ' RWF</strong> has been processed for booking <strong>#' . $booking_id . '</strong>.",
                confirmButtonText: "Return to Booking",
                confirmButtonColor: "#28a745"
            }).then(() => {
                window.location.href = "./?resto=venue_checkout&&booking_id=' . $booking_id . '";
            });
        </script>
    </body>
    </html>';
    exit;
}
?>
