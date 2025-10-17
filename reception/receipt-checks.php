<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// include("../inc/DBController.php");

// Ensure $db is initialized properly
if (!isset($db)) {
    die("Database connection not established.");
}
?>
<div class="table-responsive container">
    <style>
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .table-sm th, .table-sm td {
            padding: 0.3rem;
        }
        .btn {
            margin-left: 10px;
        }
        .form-inline {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-control {
            margin-right: 10px;
        }
    </style>

    <?php if (!isset($_GET['details_for'])) : ?>
    <?php
    $date_from = $_GET['date_from'] ?? date('Y-m-d');
    $date_to = $_GET['date_to'] ?? date('Y-m-d');
    $date_to_inclusive = date('Y-m-d', strtotime($date_to . ' +1 day'));

    $sql = $db->prepare("
        SELECT * FROM `tbl_receipts`
        WHERE created_at >= :date_from
          AND created_at < :date_to AND rcptTyCd != 'R'
    ");
    $sql->execute([
        ':date_from' => $date_from,
        ':date_to' => $date_to_inclusive
    ]);
    ?>

    <button onclick="printTable()" class="btn btn-primary" style="margin-bottom:10px;">Print Table</button>
    <form method="GET" class="form-inline">
        <?php
        foreach ($_GET as $key => $value) {
            if (!in_array($key, ['date_from', 'date_to'])) {
                echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
            }
        }
        ?>
        <label for="date_from">Start date:</label>
        <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>">
        <label for="date_to">End date:</label>
        <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>">
        <button type="submit" name="process" id="process" class="btn btn-info btn-sm">Search</button>
    </form>

    <div id="printable-table">
    <div id="print-header" style="display:none; text-align:center; margin-bottom:10px;">
        <img src="/assets/img/logo.png" alt="Logo" style="height:60px; margin-bottom:10px;">
        <h3>Receipts Report</h3>
    </div>
    <table id="data-table-basic" class="table table-striped">
        <thead>
            <tr>
                <th>INVOICE NO</th>
                <th>Receipt No</th>
                <th>Total Amount</th>
                <th>Total Tax</th>
                <th>QTY</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $Rtotal = 0;
            $totalTax = 0;
            while ($fetch = $sql->fetch(PDO::FETCH_ASSOC)) : 
            $Rtotal += (float)$fetch['totAmt'];
            $totalTax += (float)$fetch['totTaxAmt']
            
            ?>
                <tr>
                    <td>
                        <a href="?resto=reception&details_for=<?= htmlspecialchars($fetch['invcNo']) ?>">
                            <?= htmlspecialchars($fetch['invcNo']) ?>
                        </a>
                    </td>
                    <td><?= number_format((int)$fetch['rcptNo']) ?></td>
                    <td><?= number_format((int)$fetch['totAmt'], 2) ?></td>
                    <td><?= number_format((int)$fetch['totTaxAmt'], 2) ?></td>
                    <td><?= htmlspecialchars((int)$fetch['totItemCnt']) ?></td>
                    <td><?= htmlspecialchars($fetch['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan='2'><strong>TOTAL</strong></td>
                <td><strong><?php echo number_format((float)$Rtotal,2); ?></strong></td>
                <td><strong><?php echo number_format($totalTax, 2); ?></strong></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    <div id="print-footer" style="display:none; margin-top:30px;">
        <table style="width:100%;">
            <tr>
                <td>
                    Printed by:
                    <?php
                    $printedBy = '';
                    if (!empty($_SESSION['user_name'])) {
                        $printedBy = $_SESSION['user_name'];
                    } elseif (!empty($_SESSION['username'])) {
                        $printedBy = $_SESSION['username'];
                    } elseif (!empty($_SESSION['name'])) {
                        $printedBy = $_SESSION['name'];
                    }
                    if ($printedBy) {
                        echo '<strong>' . htmlspecialchars($printedBy) . '</strong>';
                    }
                    ?>
                </td>
                <td>
                    Verified by: <strong>__________________________</strong>
                </td>
            </tr>
        </table>
    </div>
    </div>

    <?php else : ?>
    <?php
    $id = $_GET['details_for'];
    $sql = $db->prepare("
        SELECT * FROM `tbl_receipts`
        WHERE invcNo = :invcNo
    ");
    $sql->execute([
        ':invcNo' => $id
    ]);
    $data = $sql->fetch(PDO::FETCH_ASSOC);
    ?>
       
     <button onclick="printInvoice()" class="btn btn-primary">Print</button>

    <div class="card mb-4" id="content">
        <?php include '../holder/printHeader.php'?>
        <div class="card-header">
            <h4>Receipt Details</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-sm table-striped">
                        <tr><th>Invoice No</th><td><?= htmlspecialchars($data['invcNo'] ?? '') ?></td></tr>
                        <tr><th>Transaction ID</th><td><?= htmlspecialchars($data['transaction_id'] ?? '') ?></td></tr>
                        <tr><th>Sales Date</th><td><?= htmlspecialchars($data['salesDt'] ?? '') ?></td></tr>
                        <tr><th>Confirmed At</th><td><?= htmlspecialchars($data['cfmDt'] ?? '') ?></td></tr>
                        <tr><th>Served By</th><td><?php
                            // Try to fetch waiter/server name from tbl_cmd or tbl_cmd_qty if available
                            $waiter = '';
                            if (!empty($data['invcNo'])) {
                                $cmd = $db->prepare("SELECT Serv_id FROM tbl_cmd WHERE OrderCode = :orderCode LIMIT 1");
                                $cmd->execute([':orderCode' => $data['invcNo']]);
                                $servId = $cmd->fetchColumn();
                                if ($servId) {
                                    $userQ = $db->prepare("SELECT f_name, l_name FROM users WHERE user_id = :uid LIMIT 1");
                                    $userQ->execute([':uid' => $servId]);
                                    $user = $userQ->fetch(PDO::FETCH_ASSOC);
                                    if ($user) {
                                        $waiter = htmlspecialchars(($user['f_name'] ?? '') . ' ' . ($user['l_name'] ?? ''));
                                    }
                                }
                            }
                            echo $waiter ?: 'N/A';
                        ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <table class="table table-sm table-striped">
                        <tr><th>Customer Name</th><td><?= htmlspecialchars($data['custNm'] ?: 'N/A') ?></td></tr>
                        <tr><th>Customer TIN</th><td><?= htmlspecialchars($data['custTin'] ?: 'N/A') ?></td></tr>
                    </table>
                </div>
            </div>

            <?php
            $itemList = json_decode($data['itemList'], true);
            if (!empty($itemList)): 
                $item = $itemList[0]; // Only one item
            ?>
                <h5 class="mt-4">Sold Item</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th>Tax Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($item['itemCd']) ?></td>
                            <td><?= htmlspecialchars($item['itemNm']) ?></td>
                            <td><?= htmlspecialchars($item['qty']) ?></td>
                            <td><?= number_format($item['prc'], 2) ?></td>
                            <td><?= number_format($item['taxAmt'], 2) ?></td>
                            <td><?= number_format($item['totAmt'], 2) ?></td>
                            <td><?= htmlspecialchars($item['taxTyCd']) ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Payment Information</h5>
                    <table class="table table-sm table-striped">
            <tr><th>Payment Type</th><td><?php
$pid=$data['pmtTyCd'];

$q = "SELECT method FROM `payments` WHERE `payments`.`payment_id` = $pid";

             $d = $db->query($q);
                echo $d->fetch()['method'] ?? '-';
 ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Amount Details</h5>
                    <table class="table table-sm table-striped">
                        <tr><th>Total Amount</th><td><?= number_format($data['totAmt'], 2) ?></td></tr>
                        <tr><th>Total Tax</th><td><?= number_format($data['totTaxAmt'], 2) ?></td></tr>
                        <tr><th>Taxable Amount B</th><td><?= number_format($data['taxblAmtB'], 2) ?></td></tr>
                        <tr><th>Tax Amount B</th><td><?= number_format($data['taxAmtB'], 2) ?></td></tr>
                    </table>
                </div>
            </div>

            <h5 class="mt-4">System Information</h5>
            <table class="table table-sm table-striped">
                <tr><th>Created At</th><td><?= htmlspecialchars($data['created_at']) ?></td></tr>
                <tr><th>Registered By</th><td><?= htmlspecialchars($data['regrNm']) ?> (<?= htmlspecialchars($data['regrId']) ?>)</td></tr>
                <tr><th>Last Modified By</th><td><?= htmlspecialchars($data['modrNm']) ?> (<?= htmlspecialchars($data['modrId']) ?>)</td></tr>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function printInvoice() {
    $("#headerprint").show();
    var printContents = document.getElementById('content').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function printTable() {
    var header = document.getElementById('print-header').innerHTML;
    var table = document.getElementById('data-table-basic').outerHTML;
    var footer = document.getElementById('print-footer').innerHTML;
    var printWindow = window.open('', '', 'height=800,width=1000');
    printWindow.document.write('<html><head><title>Receipts Report</title>');
    printWindow.document.write('<style>body{font-family:sans-serif;} .table{width:100%;border-collapse:collapse;} .table th,.table td{border:1px solid #dee2e6;padding:0.75rem;} .table thead th{background:#f8f9fa;} .table-striped tbody tr:nth-of-type(odd){background-color:rgba(0,0,0,0.05);} </style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(header);
    printWindow.document.write(table);
    printWindow.document.write(footer);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    // Do not close the window automatically
}
</script>



<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

$("#headerprint").hide();
        $("#date_too").change(function () {
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

function printInvoice() {
$("#headerprint").show();
$("#printFooter").show();
$('#data-table-basic').removeAttr('id');
  var printContents = document.getElementById('content').innerHTML; 
  var originalContents = document.body.innerHTML; document.body.innerHTML = printContents;
  window.print(); document.body.innerHTML = originalContents; }
</script>
