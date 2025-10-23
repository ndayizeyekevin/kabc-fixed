<?php
if (!isset($_GET['booking_id']) || !isset($_GET['balance'])) {
    die("Error: Missing booking ID or balance.");
}

$booking_id = $_GET['booking_id'];
$balance = $_GET['balance'];
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bx bx-refresh bx-lg text-primary bg-light rounded-circle p-3"></i>
                    </div>
                    <h3 class="card-title text-dark mb-2">Process Refund</h3>
                    <p class="text-muted mb-0">Refund overpayment for booking #<?php echo htmlspecialchars($booking_id); ?></p>
                </div>
            </div>

            <!-- Refund Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="process_refund.php" id="refundForm">
                        <!-- Refund Amount -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-3">Refund Amount</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bx bx-money text-primary"></i>
                                </span>
                                <input 
                                    type="number" 
                                    class="form-control border-start-0 ps-3" 
                                    name="refund_amount" 
                                    id="refund_amount" 
                                    value="<?php echo htmlspecialchars($balance); ?>" 
                                    readonly 
                                    required
                                />
                                <span class="input-group-text bg-light border-start-0 fw-bold text-dark">RWF</span>
                            </div>
                            <div class="form-text text-success mt-2">
                                <i class="bx bx-check-circle me-1"></i>
                                Maximum refundable amount displayed
                            </div>
                        </div>

                        <!-- Refund Method -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-3">Refund Method</label>
                            <div class="input-group ">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bx bx-credit-card text-primary"></i>
                                </span>
                                <select class="form-control border-start-0 ps-3" name="refund_method" required>
                                    <option value=''>Select refund method...</option>
                                    <option value='cash'>💵 Cash Refund</option>
                                    <option value='momo'>📱 MTN Mobile Money</option>
                                    <option value='airtelmoney'>📶 Airtel Money</option>
                                </select>
                            </div>
                        </div>

                        <!-- Remark -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-3">Transaction Remark</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bx bx-note text-primary"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control border-start-0 ps-3" 
                                    name="reason" 
                                    value="Refund" 
                                    id="reason"
                                    readonly 
                                    required 
                                />
                            </div>
                            <div class="form-text text-muted mt-2">
                                This remark will appear on the transaction record
                            </div>
                        </div>

                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">

                        <!-- Action Buttons -->
                        <div class="d-grid gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-semibold" name="process_refund" id="submitBtn">
                                <i class="bx bx-check-shield me-2"></i>
                                Process Refund
                            </button>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg py-3">
                                <i class="bx bx-arrow-back me-2"></i>
                                Go Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Card -->
            <div class="card shadow-sm border-0 mt-4 bg-light">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-info-circle bx-sm text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1 text-dark">Refund Information</h6>
                            <p class="small text-muted mb-0">
                                This refund will be processed immediately. Please ensure all details are correct before proceeding.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add some custom CSS for better styling -->
<style>
.card {
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

.input-group-text {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
}

.form-control, .form-select {
    border-color: #e3e6f0;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
}

.shadow-sm {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1) !important;
}
</style>

<!-- SweetAlert Confirmation Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('refundForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    const form = this;
    const submitBtn = document.getElementById('submitBtn');
    const refundAmount = Math.abs(<?php echo $balance; ?>);
    const refundMethod = document.querySelector('[name="refund_method"]').value;
    
    if (!refundMethod) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please select a refund method.',
            confirmButtonColor: '#dc3545'
        });
        return false;
    }
    
    Swal.fire({
        title: 'Confirm Refund',
        html: `You are about to process a refund of <strong>${refundAmount.toLocaleString()} RWF</strong> via <strong>${getMethodDisplayName(refundMethod)}</strong>.<br><br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Process Refund',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        reverseButtons: true,
        backdrop: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader bx-spin me-2"></i>Processing Refund...';
            
            // Submit the form after confirmation
            form.submit();
        }
    });
});

// Helper function to get display name for refund method
function getMethodDisplayName(method) {
    const methods = {
        'cash': 'Cash Refund',
        'momo': 'MTN Mobile Money',
        'airtelmoney': 'Airtel Money'
    };
    return methods[method] || method;
}
</script>

<!-- Add animate.css for smooth animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">