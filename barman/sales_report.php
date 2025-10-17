<?php
require_once 'SalesReportController.php';

$controller = new SalesReportController($db);
$controller->handleRequest();

$salesData = $controller->getSalesData();
// Sort salesData by menu_desc to group categories together
usort($salesData, function($a, $b) {
    return strcmp($a['menu_desc'], $b['menu_desc']);
});
$availableDays = $controller->getAvailableDays();
$availableProducts = $controller->getAvailableProducts();
$total = 0;

foreach($salesData as $row) {
    $total += $row['menu_price'] * $row['totqty'];
}
?>

<div class="container">
    <div class="printHeader" style="display:none; margin-top:0px !important;">
        <center>
            <img src='https://saintpaul.gope.rw/img/logo.png' style='max-height:80px;'>
            <div>Centre Saint Paul Kigali Ltd<br>
            KN 31 St, Kigali, Rwanda<br>
            TIN/VAT Number: 111477597<br>
            <br>
            Phone: +250 785 285 341 / +250 789 477 745 <br>
            </div>
            <h2 style="margin-top:20px;">Sales Report</h2>
            <div style="font-size:18px; margin-bottom:10px;">
                Date Range: <strong><?= htmlspecialchars($controller->getFromDate()) ?></strong> to <strong><?= htmlspecialchars($controller->getToDate()) ?></strong><br>
                Printed at: <strong><?= date('Y-m-d H:i:s') ?></strong>
            </div>
        </center>
        <br>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading bg-primary">
                    <h3 class="panel-title text-white">
                        <i class="fa fa-bar-chart"></i> SALES REPORT
                    </h3>
                </div>
                <div class="panel-body">
                    <!-- Filter Form -->
                    <form method="POST" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="<?= $controller->getFromDate() ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="<?= $controller->getToDate() ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Product</label>
                                    <select name="product_id" class="form-control">
                                        <option value="">All Products</option>
                                        <?php foreach($availableProducts as $product): ?>
                                            <option value="<?= $product['menu_id'] ?>"
                                                <?= ($controller->getProductId() == $product['menu_id']) ? 'selected' : '' ?>>
                                                <?= $product['menu_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 25px;">
                                    <button type="submit" name="filter" class="btn btn-primary">
                                        <i class="fa fa-filter"></i> Apply Filter
                                    </button>
                                    <button id="printSales" type="button" class="btn btn-info" style="margin-left:8px;">
                                        <i class="fa fa-print"></i> Print Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Report Summary -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Date Range:</strong> 
                                <?= date('M d, Y', strtotime($controller->getFromDate())) ?> - 
                                <?= date('M d, Y', strtotime($controller->getToDate())) ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Products:</strong> 
                                <?= $controller->getProductId() ? 'Selected Product' : 'All Products' ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Total Sales:</strong> 
                                <?= number_format($total, 2) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-success">
                                    <th>ITEM CODE</th>
                                    <th>PRODUCT NAME</th>
                                    <th>DESCRIPTION</th>
                                    <th>UNIT PRICE</th>
                                    <th>QTY SOLD</th>
                                    <th>AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($salesData)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fa fa-exclamation-circle"></i> No sales records found
                                        </td>
                                    </tr>
                                <?php else: ?>
<?php
$last_cat = null;
$last_cat_tot = 0;
$last_cat_qty = 0;

foreach ($salesData as $row): 
    $amount = $row['menu_price'] * $row['totqty'];

    if ($last_cat !== null && $last_cat !== $row['menu_desc']): ?>
        <!-- Show subtotal row when category changes -->
                <tr></tr>
        <tr>
            <td colspan="3"><strong>Subtotal for <?= htmlspecialchars($last_cat) ?></strong></td>
            <td class="text-right"></td>
            <td class="text-center"><?= $last_cat_qty ?></td>
            <td class="text-right"><strong><?= number_format($last_cat_tot, 2) ?></strong></td>
        </tr>
                <tr></tr>
    <?php 
        // Reset totals for the new category
        $last_cat_tot = 0;
        $last_cat_qty = 0;
    endif;

    // Show regular item row
    ?>
    <?php if ($last_cat !== $row['menu_desc']): ?>
        <!-- Category Title Row -->
        <tr class="bg-info">
            <td colspan="6" style="font-size: 1.1em; font-weight: bold; color: #222;">
                <?= htmlspecialchars($row['menu_desc']) ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td><?= $row['item_code'] ?></td>
        <td><?= $row['menu_name'] ?></td>
        <td><?= $row['menu_desc'] ?></td>
        <td class="text-right"><?= number_format($row['menu_price'], 2) ?></td>
        <td class="text-center"><?= $row['totqty'] ?></td>
        <td class="text-right"><?= number_format($amount, 2) ?></td>
    </tr>
    <?php

    // Accumulate total
    $last_cat_tot += $amount;
    $last_cat_qty += $row['totqty'];
    $last_cat = $row['menu_desc'];

endforeach;
?>

<!-- Final subtotal row for the last category -->
<?php if ($last_cat !== null): ?>
    <tr>
            <td colspan="3"><strong>Subtotal for <?= htmlspecialchars($last_cat) ?></strong></td>
            <td class="text-right"></td>
            <td class="text-center"><?= $last_cat_qty ?></td>
            <td class="text-right"><strong><?= number_format($last_cat_tot, 2) ?></strong></td>
    </tr>
<?php endif; ?>
<?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-warning">
                                    <th colspan="5" class="text-right">TOTAL SALES</th>
                                    <th class="text-right"><?= number_format($total, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- Single signature section at the bottom, for printing -->
                        <div class="signature-section" style="margin-top:40px;">
                            <div class="signature-row" style="display:flex;justify-content:space-between;gap:20px;">
                                <div class="signature-box" style="width:32%;text-align:left;">
                                    <strong>Printed by:</strong><br>
                                    <br>
                                    <div class="sig-line" style="margin-top:50px;border-top:1px solid #000;height:0;"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box" style="width:32%;text-align:center;">
                                    <strong>Received by:</strong><br><br>
                                    <div class="sig-line" style="margin-top:50px;border-top:1px solid #000;height:0;"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box" style="width:32%;text-align:right;">
                                    <strong>Approved by:</strong><br><br>
                                    <div class="sig-line" style="margin-top:50px;border-top:1px solid #000;height:0;"></div>
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

<style>
@media print {
    body * { visibility: hidden; }
    .printHeader, .printHeader * { visibility: visible !important; display: block !important; }
    .table-responsive, .table-responsive * { visibility: visible; }
    .signature-section { display: block !important; margin-top: 40px; }
    .signature-row { display: flex; justify-content: space-between; gap: 20px; }
    .signature-box { width: 32%; text-align: center; }
    .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
    .btn, .no-print, .form-horizontal, .form-inline, .alert-info { display: none !important; }
    table, td, th { border: 1px solid black !important; font-size: 18px !important; }
    h2 { text-align: center; }
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Auto-submit date filters
    $('input[type="date"]').change(function() {
        $('button[name="filter"]').click();
    });
    // Highlight active row
    $('tbody tr').hover(function() {
        $(this).toggleClass('bg-light');
    });
    // Print button
    $('#printSales').click(function(){
        window.print();
    });
});
</script>
