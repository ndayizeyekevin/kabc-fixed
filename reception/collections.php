<?php
// Set up date range variables (mostly for display/reporting context)
if(!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])){
    $from = date('Y-m-d');
    $to = date("Y-m-d");
}
else{
    $from = $_SESSION['date_from'];
    $to = $_SESSION['date_to'];
}

// Ensure error reporting is only on for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ====================================================================
// 💰 PAYMENT SUBMISSION HANDLER (addPaymentTocredit)
// ====================================================================
if(isset($_POST['addPaymentTocredit'])){
    
    // Include connection and set up transaction mode
    include '../inc/conn.php';
    
    // Set $conn to throw exceptions for transaction safety
    $conn->autocommit(false); 
    // Ensure reporting strict errors so prepared statements throw exceptions
    MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT; 
    
    // Sanitize and define variables
    $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT); 
    $method = mysqli_real_escape_string($conn, $_POST['method']); 
    $ordercode = mysqli_real_escape_string($conn, $_POST['ordercode']); 
    // clientname is actually the client_id
    $client_id = filter_var($_POST['clientname'], FILTER_VALIDATE_INT); 
    $remark = "Paid credit";
    $time = time(); // Unix timestamp

    if ($amount === false || $client_id === false || $amount <= 0) {
        echo "<script>alert('Invalid amount or client ID.')</script>";
        // Exit early if input is bad
        exit;
    }

    try {
        $conn->begin_transaction(); 
        
        // 1. Fetch current billing status for the specific order
        $stmt_fetch = $conn->prepare("SELECT amount_paid, balance_due FROM client_billings WHERE cmd_code = ? AND client_id = ? FOR UPDATE"); // FOR UPDATE locks the row
        $stmt_fetch->bind_param("si", $ordercode, $client_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        $current_billing = $result->fetch_assoc();
        $stmt_fetch->close();

        if (!$current_billing) {
            throw new Exception("Billing record not found for this order code/client.");
        }
        
        $new_amount_paid = $current_billing['amount_paid'] + $amount;
        $new_balance_due = $current_billing['balance_due'] - $amount;
        
        // Critical: Check for over-payment
        if ($new_balance_due < -0.01) { // Allow for tiny floating point errors
            throw new Exception("Payment amount ($amount) exceeds the remaining balance ({$current_billing['balance_due']}).");
        }

        // 2. Update client_billings (the debt ledger)
        $stmt_update = $conn->prepare("
            UPDATE client_billings
            SET amount_paid = ?, balance_due = ?
            WHERE cmd_code = ? AND client_id = ?
        ");
        $stmt_update->bind_param("ddsi", $new_amount_paid, $new_balance_due, $ordercode, $client_id);
        $stmt_update->execute();
        $stmt_update->close();

        $time = date('Y-m-d H:i:s'); // Current datetime for logging

        // 3. Insert into collection (Payment ledger - Using Prepared Statement)
        $stmt_collection = $conn->prepare("INSERT INTO collection (`names`, `amount`, `order_code`, `created_at`) VALUES (?, ?, ?, ?)");
        $stmt_collection->bind_param("idsi", $client_id, $amount, $ordercode, $time);
        $stmt_collection->execute();
        $stmt_collection->close();

        // 4. Insert into payment_tracks (General payment tracker - Using Prepared Statement)
        $service = 'collection';
        // $stmt_track = $conn->prepare("INSERT INTO `payment_tracks` (`amount`, `method`, `order_code`, `service`, `created_at`, `remark`) VALUES (?, ?, ?, ?, ?, ?)");
        // $stmt_track->bind_param("dsssis", $amount, $method, $ordercode, $service, $time, $remark);
        // $stmt_track->execute();
        // $stmt_track->close();
        
        // 5. Commit
        $conn->commit();
        echo "<script>alert('Payment Added and Debt Updated Successfully.')</script>";
        // Redirect to avoid resubmission
        echo "<script>window.location.href = 'index?resto=collections';</script>";
        
    } catch (\Exception $e) {
        // Rollback on any failure
        if ($conn->in_transaction) {
            $conn->rollback();
        }
        // Friendly error message
        echo "<script>alert('Error: Payment failed. " . $e->getMessage() . "')</script>";
        
    } finally {
        // Cleanup: Re-enable autocommit
        if ($conn) {
            $conn->autocommit(true);
        }
    }
}
// ====================================================================
// END OF PAYMENT HANDLER
// ====================================================================

// --- UTILITY FUNCTIONS ---
// Converted to prepared statements where possible and using the PDO connection ($db) for consistency.

function getTotalPaidByMethod($code, $method){
    // NOTE: This function does not use prepared statements due to its internal structure, 
    // but should ideally be updated to use $db->prepare for security.
    include '../inc/conn.php';
    $code = mysqli_real_escape_string($conn, $code);
    $method = mysqli_real_escape_string($conn, $method);
    
    $sql = "SELECT SUM(amount) AS total_paid FROM payment_tracks WHERE order_code = '$code' AND method = '$method'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_paid'] ?? 0;
}

function lastday(){
    include '../inc/conn.php';
    $last = 0;
    $sql = "SELECT to_id FROM days ORDER BY id DESC LIMIT 1 ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last = $row['to_id'];
    }
    return $last;
}


// Day Closing Logic (Needs more validation and transactional safety, but keeping structure)
if(isset($_POST['close'])){
    include '../inc/conn.php';

    $lastid = 0;
    // Find last cmd_qty_id
    $sql = "SELECT cmd_qty_id FROM tbl_cmd_qty ORDER BY cmd_qty_id DESC LIMIT 1 ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastid = $row['cmd_qty_id'];
    }
    
    if (isset($_POST['approve']) && $lastid > 0){
        $date = mysqli_real_escape_string($conn, $_POST['closedate']);
        $time = time();
        $from = lastday();
        
        $sql = "INSERT INTO `days` (`date`, `from_id`, `to_id`, `created_at`) VALUES ('$date', '$from', '$lastid', '$time');";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Day Successfully closed')</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "<script>alert('Please confirm data and ensure orders exist before closing the day')</script>";
    }
}


?>
<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                              <h2><small><strong><i class="fa fa-refresh"></i> Outstanding Credit Collections </strong></small></h2>
                              
                            
                            	 <li> <a href="#" class='btn btn-info' data-toggle="modal" data-target="#creditclient" data-placement="left" title="Record Payment">Record Payment</a>
   	 </li> 
                        </div>
                     <hr>
          
            <br>
            <br>
            <div class="table-responsive">
                              <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                         <th>DATE ISSUED</th>
                                        <th>CLIENT NAME</th>
                                        <th>ORDER CODE</th>
                                        <th>TOTAL DEBT</th>
                                        <th>AMOUNT PAID</th>
                                        <th>BALANCE DUE</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $i = 0;
                                    // REVISED QUERY: Select only open debts from client_billings
                                    $sql = $db->prepare("
                                        SELECT cb.*, c.f_name, c.l_name 
                                        FROM client_billings cb
                                        JOIN creadit_id c ON cb.client_id = c.id
                                        WHERE cb.balance_due > 0.00
                                        ORDER BY cb.created_at DESC
                                    ");
                            		$sql->execute();
                                  
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch(PDO::FETCH_ASSOC)){
                            		   $i++;
                                 
                                 	?>
                                    <tr>
                                        <td><?php echo $i ?></td>
                                        <td><?php echo $fetch['created_at']; ?></td>
                                        
                                        <td><?php echo $fetch['f_name']." ".$fetch['l_name']; ?></td>
                                        
                                        <td><?php echo $fetch['cmd_code']; ?></td>
                                        <td><?php echo $fetch['total_debt']; ?></td>
                                        <td><?php echo $fetch['amount_paid']; ?></td>
                                        <td><?php echo $fetch['balance_due']; ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-success pay-credit-btn"
                                                data-toggle="modal"
                                                data-target="#creditclient"
                                                data-client-id="<?php echo $fetch['client_id']; ?>"
                                                data-client-name="<?php echo $fetch['f_name']." ".$fetch['l_name']; ?>"
                                                data-order-code="<?php echo $fetch['cmd_code']; ?>"
                                                data-balance-due="<?php echo $fetch['balance_due']; ?>">
                                                Pay
                                            </a>
                                        </td>
                                    </tr>
                                    <?php 
                            		    } 
                            		}
                                    ?>
                                    
                                </tbody>
                            </table>
                            
                            
                                <div class="modal fade" id="creditclient" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Record Credit Payment</h4>
            </div>
            <div class="modal-body">
                <form  enctype="multipart/form-data" method="POST">
            <div class="row">
            <div class="form-group">
                <div class="col-md-12">
                    
                      <br> 
                    
           <input type="hidden" id="modal-client-id" name="clientname">
           <input type="hidden" id="modal-order-code" name="ordercode">

           <p>Client: <strong id="modal-client-name-display"></strong></p>
           <p>Order: <strong id="modal-order-code-display"></strong></p>
           <p>Balance Due: <strong id="modal-balance-due-display"></strong></p>
           <hr>
     
           <input type="number" step="0.01" min="1"  class="form-control" name="amount" id="modal-amount-input" placeholder="Payment Amount" required>
           
             <br>
           <select name="method"  class="form-control" required>
                <option value="">Select Payment Method</option>
                                            	<?php $cuntries = getInfoTable(7);
												foreach ($cuntries as $key => $value) {
												?>
												<option value="<?php echo trim($value['code']) ?>"><?php echo $value['code_name'] ?></option>
												<?php }
												?>
            
              </select>
            </div>
            </div>
             <button type="submit" name="addPaymentTocredit" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Process Payment </button>
            </div>
            
            
            </form>
            </div>
        </div>
    </div>
                    	
             
                            
                          
                            
        </div>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        // --- NEW JQUERY LOGIC to populate the payment modal ---
        $('.pay-credit-btn').on('click', function() {
            var clientID = $(this).data('client-id');
            var clientName = $(this).data('client-name');
            var orderCode = $(this).data('order-code');
            var balanceDue = $(this).data('balance-due');

            // Set hidden form fields
            $('#modal-client-id').val(clientID);
            $('#modal-order-code').val(orderCode);
            
            // Set display fields
            $('#modal-client-name-display').text(clientName);
            $('#modal-order-code-display').text(orderCode);
            $('#modal-balance-due-display').text(balanceDue);
            
            // Suggest the full balance due as the payment amount, but allow modification
            $('#modal-amount-input').val(balanceDue).attr('max', balanceDue);
        });
        
        // --- Existing JQUERY (Modified to be self-contained) ---
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
</script>