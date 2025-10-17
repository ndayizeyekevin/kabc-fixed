<?php
// Enhanced Sales Report with PDF Generation
// Requires: composer require tecnickcom/tcpdf

// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration file for database connection
require_once ("../inc/config.php");

// Include TCPDF library
require_once '../reception/receipt/dompdf/vendor/autoload.php';

// Set default date range
$from = isset($_SESSION['date_from']) ? $_SESSION['date_from'] : date('Y-m-d');
$to = isset($_SESSION['date_to']) ? $_SESSION['date_to'] : date('Y-m-d');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['date_from'], $_POST['date_to'])) {
        $_SESSION['date_from'] = $from = $_POST['date_from'];
        $_SESSION['date_to'] = $to = $_POST['date_to'];
    }
}

// Handle PDF generation
if (isset($_GET['action']) && $_GET['action'] === 'generate_pdf') {
    generatePDFReport($db, $from, $to);
    exit;
}

// Database query to fetch data
$data = [];
$totalAmount = 0;
$totalQuantity = 0;

try {
    // Set SQL modes to avoid strict restrictions
    $db->query('SET SESSION SQL_BIG_SELECTS=1');
    $db->exec("SET SESSION sql_mode=''");

    $resto = isset($_GET['resto']) ? $_GET['resto'] : 'report';
    $cat_id_condition = ($resto === 'reportb') ? "= '2'" : "!= '2'";
    
    $sql = $db->prepare("SELECT menu.menu_name, menu.item_code, menu.menu_desc, 
        menu.menu_price, SUM(cmd_qty) AS totqty,
        COUNT(DISTINCT DATE(tbl_cmd_qty.created_at)) as days_sold
        FROM `tbl_cmd_qty`
        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
        WHERE DATE(tbl_cmd_qty.created_at) BETWEEN :from AND :to 
        AND cmd_status = '12' 
        AND menu.cat_id $cat_id_condition
        GROUP BY cmd_item
        ORDER BY totqty DESC");
    
    $sql->execute(['from' => $from, 'to' => $to]);
    
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $menu_price = floatval(str_replace(',', '', $row['menu_price']));
        $qty = floatval($row['totqty']);
        $amount = $menu_price * $qty;
        
        $data[] = [
            'item_code' => $row['item_code'],
            'menu_name' => $row['menu_name'],
            'menu_desc' => $row['menu_desc'],
            'menu_price' => $menu_price,
            'totqty' => $qty,
            'amount' => $amount,
            'days_sold' => $row['days_sold'],
            'avg_daily_sales' => $qty / max(1, $row['days_sold'])
        ];
        
        $totalAmount += $amount;
        $totalQuantity += $qty;
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $data = [];
}

// PDF Generation Function
function generatePDFReport($db, $from, $to) {
    $data = getSalesData($db, $from, $to);
    
    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Sales Report System');
    $pdf->SetAuthor('Restaurant Management');
    $pdf->SetTitle('Sales Report - ' . date('M d, Y', strtotime($from)) . ' to ' . date('M d, Y', strtotime($to)));
    $pdf->SetSubject('Sales Report');
    
    // Set margins
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(15);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 25);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', '', 10);
    
    // Company Header
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'CENTRE SAINT-PAUL KIGALI Ltd', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'TEL: +250 785 285 341 / +250 789 477 745', 0, 1, 'C');
    $pdf->Cell(0, 6, 'www.centrestpaul.com | TIN/VAT: 111477597', 0, 1, 'C');
    $pdf->Ln(8);
    
    // Report Header
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 8, 'SALES REPORT', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 6, 'Period: ' . date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('M d, Y H:i:s'), 0, 1, 'C');
    $pdf->Ln(8);
    
    // Summary Section
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'SUMMARY', 0, 1, 'L');
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(3);
    
    $totalAmount = array_sum(array_column($data, 'amount'));
    $totalQty = array_sum(array_column($data, 'totqty'));
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Total Items Sold:', 0, 0, 'L');
    $pdf->Cell(50, 6, number_format($totalQty, 0), 0, 1, 'L');
    $pdf->Cell(50, 6, 'Total Amount:', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(50, 6, 'RWF ' . number_format($totalAmount, 0), 0, 1, 'L');
    $pdf->Ln(8);
    
    // Top Performers
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'TOP PERFORMING ITEMS', 0, 1, 'L');
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(3);
    
    $topItems = array_slice($data, 0, 3);
    foreach ($topItems as $index => $item) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, ($index + 1) . '. ' . $item['menu_name'] . ': ' . number_format($item['totqty'], 0) . ' units, RWF ' . number_format($item['amount'], 0), 0, 1, 'L');
    }
    $pdf->Ln(8);
    
    // Detailed Report Table
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'DETAILED SALES REPORT', 0, 1, 'L');
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(3);
    
    // Table header
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(25, 8, 'Item Code', 1, 0, 'C', 1);
    $pdf->Cell(50, 8, 'Item Name', 1, 0, 'C', 1);
    $pdf->Cell(40, 8, 'Description', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Price', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Qty', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Amount', 1, 1, 'C', 1);
    
    // Table data
    $pdf->SetFont('helvetica', '', 9);
    foreach ($data as $item) {
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(25, 8, 'Item Code', 1, 0, 'C', 1);
            $pdf->Cell(50, 8, 'Item Name', 1, 0, 'C', 1);
            $pdf->Cell(40, 8, 'Description', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Price', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'Qty', 1, 0, 'C', 1);
            $pdf->Cell(30, 8, 'Amount', 1, 1, 'C', 1);
            $pdf->SetFont('helvetica', '', 9);
        }
        
        $pdf->Cell(25, 6, $item['item_code'], 1, 0, 'C');
        $pdf->Cell(50, 6, substr($item['menu_name'], 0, 30), 1, 0, 'L');
        $pdf->Cell(40, 6, substr($item['menu_desc'], 0, 25), 1, 0, 'L');
        $pdf->Cell(25, 6, 'RWF ' . number_format($item['menu_price'], 0), 1, 0, 'R');
        $pdf->Cell(20, 6, number_format($item['totqty'], 0), 1, 0, 'C');
        $pdf->Cell(30, 6, 'RWF ' . number_format($item['amount'], 0), 1, 1, 'R');
    }
    
    // Total row
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(140, 8, 'TOTAL:', 1, 0, 'R');
    $pdf->Cell(20, 8, number_format($totalQty, 0), 1, 0, 'C');
    $pdf->Cell(30, 8, 'RWF ' . number_format($totalAmount, 0), 1, 1, 'R');
    
    // Output PDF
    $filename = 'Sales_Report_' . date('Y-m-d', strtotime($from)) . '_to_' . date('Y-m-d', strtotime($to)) . '.pdf';
    $pdf->Output($filename, 'D');
}

// Helper function to get sales data
function getSalesData($db, $from, $to) {
    $data = [];
    try {
        $resto = isset($_GET['resto']) ? $_GET['resto'] : 'report';
        $cat_id_condition = ($resto === 'reportb') ? "= '2'" : "!= '2'";
        
        $sql = $db->prepare("SELECT menu.menu_name, menu.item_code, menu.menu_desc, 
            menu.menu_price, SUM(cmd_qty) AS totqty
            FROM `tbl_cmd_qty`
            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
            WHERE DATE(tbl_cmd_qty.created_at) BETWEEN :from AND :to 
            AND cmd_status = '12' 
            AND menu.cat_id $cat_id_condition
            GROUP BY cmd_item
            ORDER BY totqty DESC");
        
        $sql->execute(['from' => $from, 'to' => $to]);
        
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $menu_price = floatval(str_replace(',', '', $row['menu_price']));
            $qty = floatval($row['totqty']);
            $amount = $menu_price * $qty;
            
            $data[] = [
                'item_code' => $row['item_code'],
                'menu_name' => $row['menu_name'],
                'menu_desc' => $row['menu_desc'],
                'menu_price' => $menu_price,
                'totqty' => $qty,
                'amount' => $amount
            ];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .table-responsive { 
            margin-top: 20px; 
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: #fff;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stats-card h4 {
            font-size: 2rem;
            font-weight: bold;
        }
        .btn-export {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .card-modern {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            background: #fff;
        }
        #loader { 
            display: none; 
            text-align: center; 
            margin-top: 10px; 
        }
        .top-performer {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 12px;
        }
        .table thead th {
            background: #343a40;
            color: white;
            font-size: 14px;
        }
        .table tfoot th {
            background: #e9ecef;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Main Report Card -->
            <div class="card card-modern p-4">
                <div class="card-header bg-transparent border-0">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h3 class="text-center mb-0">Sales Report</h3>
                            <p class="text-center text-muted mt-2">
                                <?php echo date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Date Range Form -->
                    <form action="" method="POST" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date From <span class="text-danger">*</span></label>
                                <input type="date" id="date_from" name="date_from" class="form-control" value="<?php echo htmlspecialchars($from); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date To <span class="text-danger">*</span></label>
                                <input type="date" id="date_to" name="date_to" class="form-control" value="<?php echo htmlspecialchars($to); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="fas fa-search me-2"></i>Update Report
                                </button>
                            </div>
                        </div>
                        <div id="loader" class="mt-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Updating report...</p>
                        </div>
                    </form>

                    <!-- Export Options -->
                    <div class="mb-4">
                        <div class="btn-group" role="group">
                            <button  onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </button>
                            <button onclick="exportToCSV()">
                                <i class="fas fa-file-csv me-2"></i>Export CSV
                            </button>
                            <button  onclick="printReport()">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                        </div>
                    </div>

                    <!-- Top Performers -->
                    <?php if (!empty($data)): ?>
                    <div class="top-performer">
                        <h5 class="mb-3"><i class="fas fa-trophy me-2"></i>Top Performing Items</h5>
                        <div class="row">
                            <?php 
                            $topItems = array_slice($data, 0, 3);
                            foreach ($topItems as $index => $item): 
                            ?>
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['menu_name']); ?></strong><br>
                                        <small>Sold: <?php echo number_format($item['totqty'], 0); ?> units | Revenue: RWF <?php echo number_format($item['amount'], 0); ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="salesTable">
                            <thead>
                                <tr>
                                    <th style="color: white;">ITEM NO</th>
                                    <th style="color: white;">ITEM NAME</th>
                                    <th style="color: white;">DESCRIPTION</th>
                                    <th style="color: white;">PRICE</th>
                                    <th style="color: white;">QTY SOLD</th>
                                    <th style="color: white;">AMOUNT</th>
                                    <th style="color: white;">AVG DAILY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $item): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($item['item_code']); ?></code></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($item['menu_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['menu_desc']); ?></td>
                                        <td>RWF <?php echo number_format($item['menu_price'], 0); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo number_format($item['totqty'], 0); ?></span>
                                        </td>
                                        <td class="fw-bold text-success">RWF <?php echo number_format($item['amount'], 0); ?></td>
                                        <td><?php echo number_format($item['avg_daily_sales'], 1); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <th colspan="4">TOTAL:</th>
                                    <th><?php echo number_format($totalQuantity, 0); ?></th>
                                    <th class="text-success">RWF <?php echo number_format($totalAmount, 0); ?></th>
                                    <th>-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {
    // Date range change handler with loading indicator
    $('form').on('submit', function(e) {
        const from = $('#date_from').val();
        const to = $('#date_to').val();
        if (from && to && from <= to) {
            $('#loader').show();
        } else {
            e.preventDefault();
            alert('Please select a valid date range');
        }
    });

    // Search functionality
    $('#tableSearch').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#salesTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Sort functionality
    $('#sortBy').on('change', function() {
        const sortBy = $(this).val();
        if (sortBy) {
            const tbody = $('#salesTable tbody');
            const rows = tbody.find('tr').toArray();
            rows.sort(function(a, b) {
                let aVal, bVal;
                switch(sortBy) {
                    case 'name':
                        aVal = $(a).find('td').eq(1).text();
                        bVal = $(b).find('td').eq(1).text();
                        return aVal.localeCompare(bVal);
                    case 'qty':
                        aVal = parseInt($(a).find('td').eq(4).text()) || 0;
                        bVal = parseInt($(b).find('td').eq(4).text()) || 0;
                        return bVal - aVal;
                    case 'total':
                        aVal = parseFloat($(a).find('td').eq(5).text().replace('RWF ', '')) || 0;
                        bVal = parseFloat($(b).find('td').eq(5).text().replace('RWF ', '')) || 0;
                        return bVal - aVal;
                    case 'price':
                        aVal = parseFloat($(a).find('td').eq(3).text().replace('RWF ', '')) || 0;
                        bVal = parseFloat($(b).find('td').eq(3).text().replace('RWF ', '')) || 0;
                        return bVal - aVal;
                }
            });
            tbody.empty().append(rows);
        }
    });
});

// PDF Generation Function
function generatePDF() {
    const from = $('#date_from').val();
    const to = $('#date_to').val();
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
    button.disabled = true;
    
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '';
    
    form.appendChild(createHiddenInput('action', 'generate_pdf'));
    form.appendChild(createHiddenInput('from', from));
    form.appendChild(createHiddenInput('to', to));
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 3000);
}

// Helper function to create hidden input
function createHiddenInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}

// Export to Excel Function
function exportToExcel() {
    const table = document.getElementById('salesTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Sales Report"});
    
    const summaryData = [
        ['Sales Report Summary'],
        ['Report Period', '<?php echo date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)); ?>'],
        ['Generated On', new Date().toLocaleString()],
        [''],
        ['Total Items Sold', <?php echo number_format($totalQuantity, 0); ?>],
        ['Total Amount', <?php echo number_format($totalAmount, 0); ?>],
        [''],
        ['Top Performing Items'],
        <?php foreach (array_slice($data, 0, 3) as $index => $item): ?>
            ['<?php echo $index + 1; ?>', '<?php echo addslashes($item['menu_name']); ?>', '<?php echo number_format($item['totqty'], 0); ?> units', 'RWF <?php echo number_format($item['amount'], 0); ?>'],
        <?php endforeach; ?>
    ];
    
    const summaryWs = XLSX.utils.aoa_to_sheet(summaryData);
    XLSX.utils.book_append_sheet(wb, summaryWs, 'Summary');
    
    const filename = 'Sales_Report_' + $('#date_from').val() + '_to_' + $('#date_to').val() + '.xlsx';
    XLSX.writeFile(wb, filename);
}

// Export to CSV Function
function exportToCSV() {
    const table = document.getElementById('salesTable');
    const wb = XLSX.utils.table_to_book(table);
    const filename = 'Sales_Report_' + $('#date_from').val() + '_to_' + $('#date_to').val() + '.csv';
    XLSX.writeFile(wb, filename, {bookType: 'csv'});
}

// Print Report Function
function printReport() {
    const printWindow = window.open('', '_blank');
    const from = $('#date_from').val();
    const to = $('#date_to').val();
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Sales Report - Print</title>
            <style>
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    margin: 20mm;
                    font-size: 12pt;
                    line-height: 1.4;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20mm;
                    border-bottom: 2px solid #333;
                    padding-bottom: 10mm;
                }
                .header h1 {
                    font-size: 24pt;
                    margin: 0;
                    color: #333;
                }
                .header p {
                    margin: 5px 0;
                    font-size: 12pt;
                    color: #555;
                }
                .summary {
                    background: #f8f9fa;
                    padding: 15mm;
                    border-radius: 5px;
                    margin-bottom: 15mm;
                    border: 1px solid #ddd;
                }
                .summary h3 {
                    margin: 0 0 10px;
                    font-size: 16pt;
                }
                .summary-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    font-size: 12pt;
                }
                .summary-grid strong {
                    color: #000;
                }
                .top-performer {
                    background: #fff3e0;
                    padding: 15mm;
                    border-radius: 5px;
                    margin-bottom: 15mm;
                    border: 1px solid #ddd;
                }
                .top-performer h3 {
                    margin: 0 0 10px;
                    font-size: 16pt;
                }
                .top-performer-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 10px;
                    font-size: 12pt;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 11pt;
                    margin-bottom: 20mm;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                }
                th {
                    background: #343a40;
                    color: white;
                    font-weight: bold;
                    font-size: 12pt;
                }
                .total-row {
                    background: #e9ecef;
                    font-weight: bold;
                    font-size: 12pt;
                }
                .text-right {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .no-print {
                    margin-top: 20mm;
                    text-align: center;
                }
                .no-print button {
                    padding: 10px 20px;
                    margin: 0 10px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 12pt;
                }
                .no-print button:first-child {
                    background: #007bff;
                    color: white;
                }
                .no-print button:last-child {
                    background: #6c757d;
                    color: white;
                }
                @media print {
                    body {
                        margin: 10mm;
                    }
                    .no-print {
                        display: none;
                    }
                    .header, .summary, .top-performer, table {
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>CENTRE SAINT-PAUL KIGALI Ltd</h1>
                <p>TEL: +250 785 285 341 / +250 789 477 745</p>
                <p>www.centrestpaul.com | TIN/VAT: 111477597</p>
                <h2>SALES REPORT</h2>
                <p>Period: ${new Date(from).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })} - ${new Date(to).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</p>
                <p>Generated on: ${new Date().toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' })}</p>
                <p>Printed by: <?php echo htmlspecialchars($_SESSION['f_name'] ?? 'Unknown User'); ?></p>
            </div>
            
            <div class="summary">
                <h3>Summary</h3>
                <div class="summary-grid">
                    <div>Total Items Sold: <strong><?php echo number_format($totalQuantity, 0); ?></strong></div>
                    <div>Total Amount: <strong>RWF <?php echo number_format($totalAmount, 0); ?></strong></div>
                </div>
            </div>
            
            <div class="top-performer">
                <h3>Top Performing Items</h3>
                <div class="top-performer-grid">
                    <?php foreach (array_slice($data, 0, 3) as $index => $item): ?>
                        <div>
                            <strong>#<?php echo $index + 1; ?> <?php echo htmlspecialchars($item['menu_name']); ?></strong><br>
                            Sold: <?php echo number_format($item['totqty'], 0); ?> units<br>
                            Revenue: RWF <?php echo number_format($item['amount'], 0); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Item No</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Qty Sold</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_code']); ?></td>
                            <td><?php echo htmlspecialchars($item['menu_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['menu_desc']); ?></td>
                            <td class="text-right">RWF <?php echo number_format($item['menu_price'], 0); ?></td>
                            <td class="text-center"><?php echo number_format($item['totqty'], 0); ?></td>
                            <td class="text-right">RWF <?php echo number_format($item['amount'], 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th colspan="4">TOTAL:</th>
                        <th class="text-center"><?php echo number_format($totalQuantity, 0); ?></th>
                        <th class="text-right">RWF <?php echo number_format($totalAmount, 0); ?></th>
                    </tr>
                </tfoot>
            </table>
            
            <div class="no-print">
                <button onclick="window.print()">Print Report</button>
                <button onclick="window.close()">Close</button>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
}
</script>

<?php
// Function to get sales trends
function getSalesTrends($db, $from, $to) {
    $trends = [];
    try {
        $sql = $db->prepare("SELECT DATE(created_at) as sale_date, 
            SUM(cmd_qty * (SELECT menu_price FROM menu WHERE menu_id = cmd_item)) as daily_sales
            FROM tbl_cmd_qty 
            WHERE DATE(created_at) BETWEEN :from AND :to 
            AND cmd_status = '12'
            GROUP BY DATE(created_at)
            ORDER BY sale_date");
        
        $sql->execute(['from' => $from, 'to' => $to]);
        
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $trends[] = [
                'date' => $row['sale_date'],
                'sales' => floatval($row['daily_sales'])
            ];
        }
    } catch (PDOException $e) {
        error_log("Trends query error: " . $e->getMessage());
    }
    return $trends;
}

// Function to get category performance
function getCategoryPerformance($db, $from, $to) {
    $categories = [];
    try {
        $sql = $db->prepare("SELECT c.cat_name, 
            SUM(cmd_qty) as total_qty,
            SUM(cmd_qty * m.menu_price) as total_sales
            FROM tbl_cmd_qty cq
            INNER JOIN menu m ON m.menu_id = cq.cmd_item
            INNER JOIN categories c ON c.cat_id = m.cat_id
            WHERE DATE(cq.created_at) BETWEEN :from AND :to 
            AND cq.cmd_status = '12'
            GROUP BY c.cat_id
            ORDER BY total_sales DESC");
        
        $sql->execute(['from' => $from, 'to' => $to]);
        
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = [
                'category' => $row['cat_name'],
                'quantity' => floatval($row['total_qty']),
                'sales' => floatval($row['total_sales'])
            ];
        }
    } catch (PDOException $e) {
        error_log("Category performance query error: " . $e->getMessage());
    }
    return $categories;
}
?>

</body>
</html>
