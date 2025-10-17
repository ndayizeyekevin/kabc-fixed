<?php
// Report all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set default dates if not provided with validation
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Validate dates
if (!strtotime($start_date) || !strtotime($end_date)) {
    $start_date = $end_date = date('Y-m-d');
}

// Ensure end date is not before start date
if (strtotime($end_date) < strtotime($start_date)) {
    $end_date = $start_date;
}

// Calculate the day before start date for opening balance
$opening_date = date('Y-m-d', strtotime($start_date . ' -1 day'));

try {
    // Single efficient query with proper opening balance calculation
    $query = $db->prepare("
        SELECT 
            i.item_id,
            i.item_name,
            u.unit_name,
            
            -- Calculate Opening Balance, Total In, and Total Out
            SUM(CASE 
                WHEN DATE(op.date) <= ? THEN COALESCE(op.in_qty, 0) - COALESCE(op.out_qty, 0)
                ELSE 0 
            END) AS opening_balance,
            
            SUM(CASE 
                WHEN DATE(op.date) BETWEEN ? AND ? THEN COALESCE(op.in_qty, 0)
                ELSE 0 
            END) AS total_in,
            
            SUM(CASE 
                WHEN DATE(op.date) BETWEEN ? AND ? THEN COALESCE(op.out_qty, 0)
                ELSE 0 
            END) AS total_out,
            
            -- Current Price subquery
            COALESCE((
                SELECT price.new_price 
                FROM tbl_progress price 
                WHERE price.item = i.item_id 
                ORDER BY price.date DESC, price.prog_id DESC 
                LIMIT 1
            ), i.price) AS current_price
            
        FROM 
            tbl_items i
        LEFT JOIN 
            tbl_progress op ON i.item_id = CAST(op.item AS UNSIGNED)
        LEFT JOIN 
            tbl_unit u ON i.item_unit = u.unit_id
        GROUP BY
            i.item_id, i.item_name, u.unit_name
        ORDER BY 
            i.item_name
    ");
    
    $query->execute([$opening_date, $start_date, $end_date, $start_date, $end_date]);
    $items = $query->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate closing balance and inventory value in PHP
    $totals = ['total_in' => 0, 'total_out' => 0, 'total_closing' => 0];
    $total_value = 0;
    $processed_items = [];
    
    foreach ($items as $item) {
        $closing_balance = $item['opening_balance'] + $item['total_in'] - $item['total_out'];
        $inventory_value = $closing_balance * $item['current_price'];
        
        $processed_items[] = [
            'item_id' => $item['item_id'],
            'item_name' => $item['item_name'],
            'unit_name' => $item['unit_name'],
            'opening_balance' => (float)$item['opening_balance'],
            'total_in' => (float)$item['total_in'],
            'total_out' => (float)$item['total_out'],
            'closing_balance' => $closing_balance,
            'current_price' => (float)$item['current_price'],
            'inventory_value' => $inventory_value
        ];
        
        // Update totals
        $totals['total_in'] += (float)$item['total_in'];
        $totals['total_out'] += (float)$item['total_out'];
        $totals['total_closing'] += $closing_balance;
        $total_value += $inventory_value;
    }
    
    $store_items = [
        'items' => $processed_items,
        'totals' => $totals,
        'start_date' => $start_date,
        'end_date' => $end_date
    ];
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error fetching data: " . htmlspecialchars($e->getMessage()) . "</div>";
    $store_items = [
        'items' => [], 
        'totals' => ['total_in' => 0, 'total_out' => 0, 'total_closing' => 0], 
        'start_date' => $start_date, 
        'end_date' => $end_date
    ];
    $total_value = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Inventory Balance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background-color: cornflowerblue;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .table-responsive {
            border-radius: 0 0 10px 10px;
        }
        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .in-stock {
            background-color: #1cc88a;
        }
        .low-stock {
            background-color: #f6c23e;
        }
        .out-of-stock {
            background-color: #e74a3b;
        }
        .search-box {
            position: relative;
        }
        .search-box i {
            position: absolute;
            top: 12px;
            left: 13px;
            color: #d1d3e2;
        }
        .search-box input {
            padding-left: 35px;
            border-radius: 5px;
            border: 1px solid #d1d3e2;
        }
        .value-cell {
            font-weight: 600;
        }
        .summary-card {
            border-left: 4px solid;
        }
        .summary-card.in {
            border-left-color: #1cc88a;
        }
        .summary-card.out {
            border-left-color: #e74a3b;
        }
        .summary-card.balance {
            border-left-color: #4e73df;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-box-seam me-2"></i>Store Inventory Balance
                </h1>
                <p class="text-muted">Period: <?= date('M j, Y', strtotime($store_items['start_date'])) ?> to <?= date('M j, Y', strtotime($store_items['end_date'])) ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Filter Report</h6>
                <div class="d-flex align-items-center">
                    <button id="print" class="btn btn-info me-2">
                        <i class="bi bi-printer me-1"></i> Print Table
                    </button>
                    <!-- Hide generate pdf link -->
                    <!-- <a href="balance_pdf.php?start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" 
                       class="btn btn-primary" target="_blank">
                       <i class="bi bi-file-earmark-pdf"></i> Generate PDF
                    </a> -->
                </div>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <input type="hidden" name="resto" value="stock_balance">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?= htmlspecialchars($start_date) ?>" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?= htmlspecialchars($end_date) ?>" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-1"></i> Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card summary-card in h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted mb-2">Total In</h6>
                                <h2 class="mb-0"><?= number_format($store_items['totals']['total_in'], 2) ?></h2>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-box-arrow-in-down text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card summary-card out h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted mb-2">Total Out</h6>
                                <h2 class="mb-0"><?= number_format($store_items['totals']['total_out'], 2) ?></h2>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-box-arrow-up text-danger" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card summary-card balance h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted mb-2">Closing Balance</h6>
                                <h2 class="mb-0"><?= number_format($store_items['totals']['total_closing'], 2) ?></h2>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-clipboard2-data text-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Inventory Items</h6>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search items...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="data-table-basic">
                    <thead class="thead-light">
                        <tr>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Opening</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Balance</th>
                            <th>Price</th>
                            <th>Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($store_items['items'])): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                                    No inventory items found for the selected period.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($store_items['items'] as $item): ?>
                                <?php
                                $status_class = '';
                                if ($item['closing_balance'] > 20) {
                                    $status_class = 'in-stock';
                                    $status_text = 'In Stock';
                                } elseif ($item['closing_balance'] > 0) {
                                    $status_class = 'low-stock';
                                    $status_text = 'Low Stock';
                                } else {
                                    $status_class = 'out-of-stock';
                                    $status_text = 'Out of Stock';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <a href="index.php?resto=cumurative&item_id=<?= (int)$item['item_id'] ?>" 
                                           class="text-dark text-decoration-none">
                                            <?= htmlspecialchars($item['item_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($item['unit_name']) ?></td>
                                    <td><?= number_format($item['opening_balance'], 2) ?></td>
                                    <td class="text-success">
                                        <?php 
                                        if ($item['total_in'] > 0) {
                                            echo '+' . number_format($item['total_in'], 2);
                                        } else {
                                            echo number_format($item['total_in'], 2);
                                        }
                                        ?>
                                    </td>
                                    <td class="text-danger">
                                        <?php
                                        if ($item['total_out'] > 0) {
                                            echo '-' . number_format($item['total_out'], 2);
                                        } else {
                                            echo number_format($item['total_out'], 2);
                                        }
                                        ?>
                                    </td>
                                    <td class="value-cell"><?= number_format($item['closing_balance'], 2) ?></td>
                                    <td><?= number_format($item['current_price'], 2) ?></td>
                                    <td class="value-cell"><?= number_format($item['inventory_value'], 2) ?></td>
                                    <td>
                                        <span class="status-indicator <?= $status_class ?>"></span>
                                        <?= $status_text ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-active">
                                <td colspan="7"><strong>Total Value</strong></td>
                                <td colspan="2"><strong><?= number_format($total_value, 2) ?></strong></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Determine the current user's full name for the print footer based on logged-in user ID
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        
        const printContents = `
            <html>
            <head>
                <title>Inventory Table</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    h2 { text-align: center; margin-bottom: 20px; }
                    
                    /* The key change is here: remove the general 'a' selector from this rule */
                    #printx, .search-box, .btn { display: none; } 

                    @media print {
                        /* This rule ensures the link for the item name remains visible, but un-styled */
                        a { text-decoration: none; color: inherit; } 
                        
                        /* Hide the other elements you don't want to print */
                        .btn, .search-box, form { display: none; }
                        
                        /* Ensure the table is visible */
                        table { display: table; }
                    }

                    /* Signature section styles */
                    .signature-section { margin-top: 40px; }
                    .signature-row { display: flex; justify-content: space-between; gap: 20px; }
                    .signature-box { width: 32%; text-align: center; }
                    .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
                </style>
                <center>
                    <img src='<?= htmlspecialchars($company_logo ?? 'logo.png', ENT_QUOTES, 'UTF-8'); ?>' alt='Company Logo' style='max-width:150px;'><br>
                    <div><?= htmlspecialchars($company_name ?? 'Centre Saint Paul Kigali Ltd', ENT_QUOTES, 'UTF-8'); ?><br>
                    <?= $company_address ?>
                    TIN/VAT Number: <?= $company_tin ?><br>
                    <br>
                    Phone: <?= $company_phone ?><br>
                    </div>
                </center>
                <br>
            </head>
            <body>
                <h2>Inventory Table</h2>
                ${printTable.outerHTML}
                <div class="signature-section">
                    <div class="signature-row">
                        <div class="signature-box" style="text-align:left;">
                            <strong>Printed by:</strong><br>
                            <?= htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
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

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const input = this.value.toLowerCase();
        const rows = document.querySelectorAll('#data-table-basic tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Date validation
    document.getElementById('start_date').addEventListener('change', function() {
        const endDate = document.getElementById('end_date');
        if (this.value > endDate.value) {
            endDate.value = this.value;
        }
    });
});
</script>
</body>
</html>
