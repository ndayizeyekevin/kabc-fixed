
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// session_start();
include '../inc/conn.php';

$from = isset($_POST['date_from']) && !empty($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-d');
$to   = isset($_POST['date_to']) && !empty($_POST['date_to']) ? $_POST['date_to'] : date("Y-m-d");
$selectedDep = isset($_POST['department']) ? $_POST['department'] : '0';

function getDep($id){
    $departments = [
        4 => "Kitchen",
        5 => "Bar",
        7 => "Stock",
        10 => "Cahier",
        12 => "Supervisor",
        13 => "House Keeper",
    ];
    return isset($departments[$id]) ? $departments[$id] : "Unknown";
}


function getItemPrice($id){
    global $db;
    return StoreController::getUnitePrice($db, $id);
	
// include '../inc/conn.php';	

// $item = "SELECT * FROM tbl_items where item_id='$id' ";
// $result = $conn->query($item);

// $tbl_progress = $conn ->query ("SELECT * FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1;");

// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {
//     while($row2 = $tbl_progress->fetch_assoc()){
//         return  $row2['new_price'] > 0 ? $row2['new_price'] : $row['price'];
//     }
//   }
// }

}



?>

<style>
    .no-print { }
    .printHeader { display: none !important; }
    .print-logo { display: none !important; }
    .signature-section { display: none; }
    @media print {
        body * { visibility: hidden; }
        .print-section, .print-section *,
        .printHeader, .printHeader * {
            visibility: visible;
        }
        .print-section {
            position: absolute;
            left: 0;
            top: 80px;
            width: 100%;
        }
        .printHeader {
            display: block !important;
            position: absolute;
            top: -15rem;
            left: 0;
            width: 100%;
            text-align: center;
            margin-bottom: 2rem;
            border:none !important;
        }
        .print-logo { display: block !important; margin: 0 auto 10px auto; max-width: 120px; }
        .signature-section { display: block !important; margin-top: 40px; }
        .signature-row { display: flex; justify-content: space-between; gap: 20px; }
        .signature-box { width: 32%; text-align: center; }
        .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
        .no-print { display: none !important; }
        table, td { border: 1px solid black !important; font-size: 12px !important; }
        a { text-decoration: none !important; text-transform: capitalize !important; color: #000 !important; }
        a[href]:after { content: none !important; }
        h4 { display: block !important; text-align: center; padding: 5px 0; }
    }
    .print-actions { text-align: right; margin: 10px 0; }
</style>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                    <h2><small><strong><i class="fa fa-refresh"></i> Stock Report Per Day</strong></small></h2>
                </div>
                <hr>
                <form action="" method="POST" class="no-print">
                    <div class="row">
                        <label class="col-md-1 control-label"><strong>Department </strong></label>
                        <div class="col-md-3">
                            <select class="form-control" name="department">
                                <option value="0">All Departments</option>
                                <option value="4" <?php if($selectedDep == 4) echo 'selected'; ?>>Kitchen</option>
                                <option value="5" <?php if($selectedDep == 5) echo 'selected'; ?>>Bar</option>
                                <option value="5" <?php if($selectedDep == 7) echo 'selected'; ?>>Stock</option>
                                <option value="5" <?php if($selectedDep == 10) echo 'selected'; ?>>Cahier</option>
                                <option value="5" <?php if($selectedDep == 12) echo 'selected'; ?>>Supervisor</option>
                                <option value="13" <?php if($selectedDep == 13) echo 'selected'; ?>>House Keeping</option>
                            </select>
                        </div>
                        <label class="col-md-1 control-label"><strong>Date From </strong></label>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control" value="<?php echo $from; ?>">
                        </div>
                        <label class="col-md-1 control-label"><strong>Date To </strong></label>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control" value="<?php echo $to; ?>">
                        </div>
                        <div class="col-md-3 mt-2">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
                <br><br>
                <!-- Print header (only visible when printing) -->

                <div class="printHeader">
                    <img src="../assets/img/logo.png" alt="Logo" class="print-logo" />
                    <h2>Stock Report Per Day</h2>
                    <p><strong>From: <?php echo htmlspecialchars($from); ?> To: <?php echo htmlspecialchars($to); ?></strong></p>
                    <p>Generated on: <?php echo date('d-m-Y H:i:s'); ?></p>
                </div>

                <!-- In-page Print button -->
                <div class="print-actions no-print">
                        <button id="print" type="button" class="btn btn-success btn-sm">
                            <i class="fa fa-print"></i> Print Report
                        </button>
                    </div>
                <div class="print-section">
                    <?php include '../holder/printHeader.php'; ?>
                    <?php
                    $depName = $selectedDep != 0 ? getDep($selectedDep) : "All Departments";
                    $titleText = "Stock Report from " . date("M d, Y", strtotime($from)) . " to " . date("M d, Y", strtotime($to)) . " for " . $depName;
                    echo "<h4 class='text-center'><strong>$titleText</strong></h4><br>";
                    ?>
                    <div class="table-responsive">
                        <table id="data-table-basic" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                    <!--<th>Average Cost</th>-->
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            // ...existing code...
                            $filterDep = $selectedDep != 0 ? "AND tbl_requests.department = '$selectedDep'" : "";
                            $query = "SELECT tbl_requests.requested_date, tbl_items.cat_id, category.cat_name, tbl_requests.department, tbl_items.item_id AS item_id, tbl_items.item_name,
            tbl_items.price, tbl_request_details.quantity, SUM(tbl_request_details.quantity) AS total_quantity
            FROM tbl_requests INNER JOIN tbl_request_details ON tbl_requests.req_code = tbl_request_details.req_code
            INNER JOIN tbl_items ON tbl_request_details.items = tbl_items.item_id INNER JOIN category ON category.cat_id = tbl_items.cat_id
            WHERE DATE(tbl_requests.requested_date) >= '$from' AND DATE(tbl_requests.requested_date) <= '$to' AND tbl_requests.status !=3 $filterDep GROUP BY tbl_requests.department, tbl_items.cat_id, tbl_items.item_id 
			ORDER BY tbl_requests.department, category.cat_name, tbl_items.item_name
                            ";
                            $result = $db->prepare($query);
                            $result->execute();

                            $last_department = '';
$last_category = '';
$sub_qty = 0;
$sub_total = 0;
$inAmounttotal = 0;

while ($row = $result->fetch()) {
    $itemId = $row['item_id'];
    $department = getDep($row['department']);
    $category = $row['cat_name'];
    $itemName = $row['item_name'];
    $unitPrice = getItemPrice($itemId);
    $quantity = $row['total_quantity'];

    // Average cost calculation
    $avgQuery = $db->prepare("
        SELECT in_qty, new_price 
        FROM tbl_progress 
        WHERE item = ? AND in_qty > 0 AND new_price IS NOT NULL
    ");
    $avgQuery->execute([$itemId]);

    $total_qty = 0;
    $total_value = 0;

    while ($tx = $avgQuery->fetch()) {
        $qty = floatval($tx['in_qty']);
        $price = floatval($tx['new_price']);
        $total_qty += $qty;
        $total_value += ($qty * $price);
    }

    $avg_cost = $total_qty > 0 ? $total_value / $total_qty : 0;
    $amount = $unitPrice * $quantity;
    $inAmounttotal += $amount;

    // Add subtotal row before switching departments
    if ($last_department != '' && $last_department != $department) {
        echo "<tr style='background:#e2e3e5'><td colspan='2'><strong>Subtotal for $last_department</strong></td>
                <td><strong>" . number_format($sub_qty) . "</strong></td>
                <td></td>
                <td><strong>" . number_format($sub_total) . " RWF</strong></td>
              </tr>";
        // Reset subtotal counters
        $sub_qty = 0;
        $sub_total = 0;
    }

    // Start new department section
    if ($last_department != $department) {
        echo "<tr style='background:#d1ecf1'><td colspan='6'><strong>Department: $department</strong></td></tr>";
        $last_department = $department;
        $last_category = '';
    }

    if ($last_category != $category) {
        echo "<tr style='background:#f1f1f1'><td colspan='6'><strong>Category: $category</strong></td></tr>";
        $last_category = $category;
    }

    echo "<tr>
            <td></td>
            <td>$itemName</td>
            <td>$quantity</td>
            <td>" . number_format((float)$unitPrice, 2) . "</td>
            <td>" . number_format((float)$amount, 2) . " RWF</td>
        </tr>";

    // Update subtotals
    $sub_qty += $quantity;
    $sub_total += $amount;
}

// Final subtotal row for the last department
if ($last_department != '') {
    echo "<tr style='background:#e2e3e5'><td colspan='2'><strong>Subtotal for $last_department</strong></td>
            <td><strong>" . number_format($sub_qty) . "</strong></td>
            <td></td>
            <td><strong>" . number_format($sub_total) . " RWF</strong></td>
          </tr>";
}

echo "<tr><td colspan='4' class='text-left'><b>TOTAL: </b></td> <td><b>" . number_format((float)$inAmounttotal,2) . " RWF</b></td></tr>";
?>
                            </tbody>
                        </table>

                        <?php
                        // ...existing code...
                        $printedBy = '';
                        try {
                            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                                $stmtPB = $db->prepare("SELECT u.f_name, u.l_name FROM tbl_users u INNER JOIN tbl_user_log l ON u.user_id = l.user_id WHERE l.user_id = ? LIMIT 1");
                                $stmtPB->execute([$_SESSION['user_id']]);
                                $rowPB = $stmtPB->fetch(PDO::FETCH_ASSOC);
                                if ($rowPB) {
                                    $printedBy = trim(($rowPB['f_name'] ?? '') . ' ' . ($rowPB['l_name'] ?? ''));
                                }
                            }
                        } catch (Exception $e) {
                            // ignore and fallback below
                        }
                        if ($printedBy === '') {
                            if (isset($_SESSION['user_fullname'])) { $printedBy = $_SESSION['user_fullname']; }
                            elseif (isset($_SESSION['fullname'])) { $printedBy = $_SESSION['fullname']; }
                            elseif (isset($_SESSION['name'])) { $printedBy = $_SESSION['name']; }
                            elseif (isset($_SESSION['username'])) { $printedBy = $_SESSION['username']; }
                            elseif (isset($_SESSION['f_name']) || isset($_SESSION['l_name'])) { $printedBy = trim(($_SESSION['f_name'] ?? '') . ' ' . ($_SESSION['l_name'] ?? '')); }
                        }
                        ?>

                        <!-- Print-only signature footer placed right after the table -->
                        <div class="signature-section">
                            <div class="signature-row">
                                <div class="signature-box" style="text-align:left;">
                                    <strong>Printed by:</strong><br>
                                    <?php echo htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box">
                                    <strong>Received by:</strong><br>
                                    <br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box" style="text-align:right;">
                                    <strong>Approved by:</strong><br>
                                    <br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("print").addEventListener("click", function () {
        const table = document.getElementById("data-table-basic");

        if (!table) {
            alert("Table not found!");
            return;
        }

        const printTable = table.cloneNode(true);
        const headerRow = printTable.querySelector('thead tr');
        const bodyRows = printTable.querySelectorAll('tbody tr');
        // Add a new header cell for the numbering column
        const numHeader = document.createElement('th');
        numHeader.textContent = '#';
        headerRow.prepend(numHeader);
        // Add a new cell with the number to each body row
        bodyRows.forEach((row, index) => {
            // Check to make sure it's not the "No data" or "Total" row
            if (row.cells.length > 2) {
                const numCell = document.createElement('td');
                numCell.textContent = index + 1;
                row.prepend(numCell);
            }
        });

        const fromDate = document.querySelector('input[name="date_from"]').value || '';
        const toDate = document.querySelector('input[name="date_to"]').value || '';
        const printedDate = new Date().toLocaleString();
        const printContents = `
            <html>
            <head>
                <title>Cost Report by Department</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    h2 { text-align: center; margin-bottom: 20px; }
                    #printx, .search-box, .btn { display: none; }
                    @media print {
                        a { text-decoration: none; color: inherit; }
                        .btn, .search-box, form { display: none; }
                        table { display: table; }
                    }
                    .signature-section { margin-top: 40px; }
                    .signature-row { display: flex; justify-content: space-between; gap: 20px; }
                    .signature-box { width: 32%; text-align: center; }
                    .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
                </style>
                <center>
                     <img src='<?= $logo_png ?>' style='height:80px; margin-bottom:10px;'>
                    <div><?= $company_name ?><br>
                    TIN/VAT :<?= $company_tin ?><br>
                    Tel: <?= $company_phone ?><br>
                    <?= $company_email ?><br>
                    </div>
                </center>
                <br>
            </head>
            <body>
                <h2>Cost Of Goods Sold Report by Department</h2>
                <div style='text-align:center; font-size:15px; margin-bottom:5px;'>Date Range: ${fromDate} to ${toDate}</div>
                <div style='text-align:center; font-size:13px;'>Printed on: ${printedDate}</div>
                ${printTable.outerHTML}
                <div class="signature-section">
                    <div class="signature-row">
                        <div class="signature-box" style="text-align:left;">
                            <strong>Printed by:</strong><br>
                            <?php echo htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                            <div class="sig-line"></div>
                            <small>Name & Signature</small>
                        </div>
                        <div class="signature-box">
                            <strong>Received by:</strong><br>
                            <br>
                            <div class="sig-line"></div>
                            <small>Name & Signature</small>
                        </div>
                        <div class="signature-box" style="text-align:right;">
                            <strong>Approved by:</strong><br>
                            <br>
                            <div class="sig-line"></div>
                            <small>Name & Signature</small>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `;

        const printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.open();
        printWindow.document.write(printContents);
        printWindow.document.close();

        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
        };
    });
});
</script>


