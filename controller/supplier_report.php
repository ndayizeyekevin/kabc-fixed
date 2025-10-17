<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include '../inc/conn.php';

// --- Helper functions ---
function getItemName($id) {
    global $db;
    $sql = $db->prepare("SELECT item_name FROM tbl_items WHERE item_id=?");
    $sql->execute([$id]);
    $row = $sql->fetch();
    return $row ? $row['item_name'] : '';
}

function getItemPrice($id) {
    global $db;
    $sql = $db->prepare("SELECT price FROM tbl_items WHERE item_id=?");
    $sql->execute([$id]);
    $row = $sql->fetch();
    return $row ? $row['price'] : 0;
}

function getItemUnitId($id) {
    global $db;
    $sql = $db->prepare("SELECT item_unit FROM tbl_items WHERE item_id=?");
    $sql->execute([$id]);
    $row = $sql->fetch();
    return $row ? $row['item_unit'] : 0;
}

function getItemUnitName($id) {
    global $db;
    $sql = $db->prepare("SELECT unit_name FROM tbl_unit WHERE unit_id=?");
    $sql->execute([$id]);
    $row = $sql->fetch();
    return $row ? $row['unit_name'] : '';
}

function getSupplierName($id) {
    global $db;
    $sql = $db->prepare("SELECT name FROM suppliers WHERE id=?");
    $sql->execute([$id]);
    $row = $sql->fetch();
    return $row ? $row['name'] : '';
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
}</style>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-example-wrap mg-t-30">
                <!-- Navigation/header elements (stk, Logout, Dashboard, Stock, Report Per Supplier) removed for clean report view -->

                <!-- 🔷 Filter Form -->
                <form action="" method="POST" class="no-print">
    <div class="row">
        <label class="col-md-1 control-label"><strong>Supplier </strong></label>
        <div class="col-md-3">
            <select class="form-control selectpicker" data-live-search="true" name="supplier">
                <option value="">-- All Suppliers --</option>
                <?php 
                $sql = $db->prepare("SELECT * FROM suppliers");
                $sql->execute();
                while($row = $sql->fetch()) {
                ?>
                    <option value="<?= $row['id']; ?>"<?= (isset($_POST['supplier']) && $_POST['supplier'] == $row['id']) ? ' selected' : '' ?>><?= $row['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <label class="col-md-1 control-label"><strong>Date Range</strong></label>
        <div class="col-md-3">
            <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($_POST['date_from'] ?? ''); ?>">
        </div>
        <label class="col-md-1 control-label text-center" style="margin-top:8px;">to</label>
        <div class="col-md-3">
            <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($_POST['date_to'] ?? ''); ?>">
        </div>
        <div class="col-md-3 mt-2">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </div>
</form>

                <br><br>

                <!-- Print header (only visible when printing) -->
                <div class="printHeader">
                    <img src="<?= $logo_png; ?>" alt="Logo" class="print-logo" />
                    <h2>Supplier Report</h2>
                    <div style="font-size:15px; margin:8px 0;">
                        <strong>Date Range: <?php echo htmlspecialchars($_POST['date_from'] ?? '') . ' to ' . htmlspecialchars($_POST['date_to'] ?? ''); ?></strong>
                    </div>
                    <p><strong>Generated on: <?php echo date('d-m-Y H:i:s'); ?></strong></p>
                </div>

                <!-- Print button -->
                <div class="print-actions no-print">
                    <button id="print" type="button" class="btn btn-success btn-sm">
                        <i class="fa fa-print"></i> Print Report
                    </button>
                </div>
                <div class="print-section">

                <!-- 🔷 Table -->
                <div class="table-responsive">
                    <table id="data-table-basic" class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>U.P</th>
                                <th>T.P</th>
                                <th>Paid</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier = $_POST['supplier'] ?? '';
    $date_from = $_POST['date_from'] ?? '';
    $date_to = $_POST['date_to'] ?? '';

    $has_supplier = !empty($supplier);
    $has_date_range = !empty($date_from) && !empty($date_to);

    $start = $has_date_range ? strtotime($date_from) : null;
    $end = $has_date_range ? strtotime($date_to) : null;

    $i = 0;
    $grand_total = 0;
    $data_exists = false;

    if (!$has_supplier) {
        $suppliers = $db->query("SELECT * FROM suppliers");
        while ($s = $suppliers->fetch()) {
            $supplier_id = $s['id'];
            $supplier_name = $s['name'];
            $total = 0;

            $stmt = $db->prepare("SELECT * FROM store_request WHERE supplier = ?");
            $stmt->execute([$supplier_id]);

            while ($req = $stmt->fetch()) {
                $req_date = strtotime($req['request_date']);
                if ($has_date_range && ($req_date < $start || $req_date > $end)) continue;

                $req_id = $req['req_id'];
                $items = $db->prepare("SELECT * FROM request_store_item WHERE req_id = ?");
                $items->execute([$req_id]);

                while ($item = $items->fetch()) {
                    $total += $item['del_qty'] * $item['del_price'];
                }
            }

            if ($total > 0) {
                $i++;
                $grand_total += $total;
                $data_exists = true;
?>
<tr>
    <td><?= $i ?></td>
    <td>-</td>
    <td><strong><?= $supplier_name ?></strong></td>
    <td colspan="4">Summary</td>
    <td><?= number_format($total) ?></td>
    <td>0</td>
    <td><?= number_format($total) ?></td>
</tr>
<?php
            }
        }

        if (!$data_exists) {
            echo '<tr><td colspan="10" class="text-center text-danger">No data found.</td></tr>';
        } else {
?>
<tr>
    <th colspan="7"><h4>Total</h4></th>
    <th><h4><?= number_format($grand_total) ?></h4></th>
    <th><h4>0</h4></th>
    <th><h4><?= number_format($grand_total) ?></h4></th>
</tr>
<?php
        }
    } else {
        $stmt = $db->prepare("SELECT * FROM store_request WHERE supplier = ?");
        $stmt->execute([$supplier]);

        while ($request = $stmt->fetch()) {
            $request_date = strtotime($request['request_date']);
            if ($has_date_range && ($request_date < $start || $request_date > $end)) continue;

            $req_id = $request['req_id'];
            $date = $request['request_date'];
            $supplier_name = getSupplierName($supplier);

            $items = $db->prepare("SELECT * FROM request_store_item WHERE req_id = ?");
            $items->execute([$req_id]);

            while ($item = $items->fetch()) {
                $i++;
                $item_id = $item['item_id'];
                $qty = $item['del_qty'];
                $price = $item['del_price'];
                $total = $qty * $price;
                $grand_total += $total;
                $data_exists = true;
?>
<tr>
    <td><?= $i ?></td>
    <td><?= $date ?></td>
    <td><?= $supplier_name ?></td>
    <td><?= getItemName($item_id) ?></td>
    <td><?= getItemUnitName(getItemUnitId($item_id)) ?></td>
    <td><?= $qty ?></td>
    <td><?= $price ?></td>
    <td><?= $total ?></td>
    <td>0</td>
    <td><?= number_format($total) ?></td>
</tr>
<?php
            }
        }

        if ($data_exists) {
?>
<tr>
    <th colspan="7"><h4>Total</h4></th>
    <th><h4><?= number_format($grand_total) ?></h4></th>
    <th><h4>0</h4></th>
    <th><h4><?= number_format($grand_total) ?></h4></th>
</tr>
<?php
        } else {
            echo '<tr><td colspan="10" class="text-center text-danger">No data found for selected filter.</td></tr>';
        }
    }

    // We now provide in-page print; external print link removed intentionally
}
?>
                        </tbody>
                    </table>

                    <?php
                    // Determine the current user's full name based on logged-in user ID
                    $printedBy = '';
                    try {
                        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                            $stmtPB = $db->prepare("SELECT u.f_name, u.l_name 
                                                    FROM tbl_users u 
                                                    INNER JOIN tbl_user_log l ON u.user_id = l.user_id 
                                                    WHERE l.user_id = ? LIMIT 1");
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

        const fromDateInput = document.querySelector('input[name="date_from"]');
        const toDateInput = document.querySelector('input[name="date_to"]');
        const fromDate = fromDateInput && fromDateInput.value ? fromDateInput.value : '';
        const toDate = toDateInput && toDateInput.value ? toDateInput.value : '';
        let dateRangeText = '';
        if (fromDate && toDate) {
            dateRangeText = `${fromDate} to ${toDate}`;
        } else if (fromDate) {
            dateRangeText = `${fromDate}`;
        } else if (toDate) {
            dateRangeText = `${toDate}`;
        } else {
            dateRangeText = 'No date selected';
        }
        const printedDate = new Date().toLocaleString();
        const printContents = `
            <html>
            <head>
                <title>Supplier Report</title>
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
                    <img src='<?= $logo_png; ?>'>
                    <div><?= $company_name ?><br>
                    <?= $company_address ?><br>
                    TIN/VAT Number: <?= $company_tin ?><br>
                    <br>
                    Phone: <?= $company_phone ?><br>
                    </div>
                </center>
                <br>
            </head>
            <body>
                <h2>Supplier Report</h2>
                <div style='text-align:center; font-size:15px; margin-bottom:5px;'>Date Range: ${dateRangeText}</div>
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
