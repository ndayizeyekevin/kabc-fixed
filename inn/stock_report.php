<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../inc/conn.php';

// Set default values
$from = isset($_SESSION['from']) ? $_SESSION['from'] : date('Y-m-d');
$to = isset($_SESSION['to']) ? $_SESSION['to'] : date('Y-m-d');
$item = isset($_SESSION['item']) ? $_SESSION['item'] : 'all';
$itemname = 'All Items';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['date_from'], $_POST['date_to'], $_POST['item'])) {
        $_SESSION['from'] = $from = $_POST['date_from'];
        $_SESSION['to'] = $to = $_POST['date_to'];
        $_SESSION['item'] = $item = $_POST['item'];
    }
}

// Get item name if specific item is selected
if ($item !== 'all') {
    try {
        $sql = $db->prepare("SELECT item_name FROM tbl_items WHERE item_id = :item_id");
        $sql->execute(['item_id' => $item]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $itemname = $row ? $row['item_name'] : 'Unknown Item';
    } catch (PDOException $e) {
        error_log("Error fetching item name: " . $e->getMessage());
        $itemname = 'Unknown Item';
    }
}

// Function to get item price
function getItemPrice($id, $db) {
    try {
        $sql = $db->prepare("SELECT price FROM tbl_items WHERE item_id = :id");
        $sql->execute(['id' => $id]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        return $row ? floatval($row['price']) : 0;
    } catch (PDOException $e) {
        error_log("Error fetching item price: " . $e->getMessage());
        return 0;
    }
}

// Fetch stock data
$stock_data = [];
$total_opening = 0;
$total_in = 0;
$total_out = 0;
$total_closing = 0;

try {
    $query = "SELECT p.*, i.item_name 
              FROM tbl_progress p 
              INNER JOIN tbl_items i ON i.item_id = p.item 
              WHERE p.date BETWEEN :from AND :to 
              AND i.item_status = '1'";
    if ($item !== 'all') {
        $query .= " AND p.item = :item";
    }
    $sql = $db->prepare($query);
    $params = ['from' => $from, 'to' => $to];
    if ($item !== 'all') {
        $params['item'] = $item;
    }
    $sql->execute($params);

    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $opening_qty = floatval($row['last_qty'] ?? 0);
        $in_qty = floatval($row['in_qty'] ?? 0);
        $out_qty = floatval($row['out_qty'] ?? 0);
        $closing_qty = floatval($row['end_qty'] ?? 0);
        $item_price = getItemPrice($row['item'], $db);
        $new_price = floatval($row['new_price'] ?? $item_price);
        
        $opening_value = $item_price * $opening_qty;
        $in_value = $new_price * $in_qty;
        $total_qty = $opening_qty + $in_qty;
        $average_price = $total_qty > 0 ? ($opening_value + $in_value) / $total_qty : 0;
        $closing_value = $average_price * $closing_qty;

        $stock_data[] = [
            'date' => $row['date'],
            'item_name' => $row['item_name'],
            'last_qty' => $opening_qty,
            'in_qty' => $in_qty,
            'out_qty' => $out_qty,
            'end_qty' => $closing_qty,
            'average_price' => $average_price,
            'opening_value' => $opening_value,
            'in_value' => $in_value,
            'closing_value' => $closing_value
        ];

        $total_opening += $opening_value;
        $total_in += $in_value;
        $total_out += $out_qty;
        $total_closing += $closing_value;
    }
} catch (PDOException $e) {
    error_log("Error fetching stock data: " . $e->getMessage());
}

// Handle PDF generation
if (isset($_GET['action']) && $_GET['action'] === 'generate_pdf') {
    require_once '../reception/receipt/dompdf/vendor/autoload.php';
    generatePDFReport($db, $from, $to, $item, $itemname, $stock_data, $total_opening, $total_in, $total_out, $total_closing);
    exit;
}

// PDF Generation Function
function generatePDFReport($db, $from, $to, $item, $itemname, $stock_data, $total_opening, $total_in, $total_out, $total_closing) {
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Stock Report System');
    $pdf->SetAuthor('Restaurant Management');
    $pdf->SetTitle('Stock Report - ' . date('M d, Y', strtotime($from)) . ' to ' . date('M d, Y', strtotime($to)));
    $pdf->SetSubject('Stock Report');
    
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
    $pdf->Cell(0, 10, 'KABC HOTEL', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'TEL: <?= $company_phone ?>', 0, 1, 'C');
    $pdf->Cell(0, 6, 'TIN/VAT: <?= $company_tin ?>', 0, 1, 'C');
    $pdf->Ln(8);
    
    // Report Header
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 8, 'STOCK REPORT', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 6, 'Period: ' . date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Item: ' . $itemname, 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('M d, Y H:i:s'), 0, 1, 'C');
    $pdf->Ln(8);
    
    // Summary Section
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'SUMMARY', 0, 1, 'L');
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(3);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Total Opening Value:', 0, 0, 'L');
    $pdf->Cell(50, 6, 'RWF ' . number_format($total_opening, 0), 0, 1, 'L');
    $pdf->Cell(50, 6, 'Total Incoming Value:', 0, 0, 'L');
    $pdf->Cell(50, 6, 'RWF ' . number_format($total_in, 0), 0, 1, 'L');
    $pdf->Cell(50, 6, 'Total Quantity Out:', 0, 0, 'L');
    $pdf->Cell(50, 6, number_format($total_out, 0), 0, 1, 'L');
    $pdf->Cell(50, 6, 'Total Closing Value:', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(50, 6, 'RWF ' . number_format($total_closing, 0), 0, 1, 'L');
    $pdf->Ln(8);
    
    // Detailed Report Table
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'DETAILED STOCK REPORT', 0, 1, 'L');
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(3);
    
    // Table header
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(25, 8, 'Date', 1, 0, 'C', 1);
    $pdf->Cell(35, 8, 'Item Name', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Opening', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'New Stock', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Qty Out', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Closing', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Unit Price', 1, 0, 'C', 1);
    $pdf->Cell(25, 8, 'Total Price', 1, 1, 'C', 1);
    
    // Table data
    $pdf->SetFont('helvetica', '', 9);
    foreach ($stock_data as $row) {
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(25, 8, 'Date', 1, 0, 'C', 1);
            $pdf->Cell(35, 8, 'Item Name', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Opening', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'New Stock', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Qty Out', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Closing', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Unit Price', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Total Price', 1, 1, 'C', 1);
            $pdf->SetFont('helvetica', '', 9);
        }
        
        $pdf->Cell(25, 6, $row['date'], 1, 0, 'C');
        $pdf->Cell(35, 6, substr($row['item_name'], 0, 20), 1, 0, 'L');
        $pdf->Cell(25, 6, number_format($row['last_qty'], 0) . ' (RWF ' . number_format($row['opening_value'], 0) . ')', 1, 0, 'C');
        $pdf->Cell(25, 6, number_format($row['in_qty'], 0) . ' (RWF ' . number_format($row['in_value'], 0) . ')', 1, 0, 'C');
        $pdf->Cell(25, 6, number_format($row['out_qty'], 0), 1, 0, 'C');
        $pdf->Cell(25, 6, number_format($row['end_qty'], 0), 1, 0, 'C');
        $pdf->Cell(25, 6, 'RWF ' . number_format($row['average_price'], 0), 1, 0, 'C');
        $pdf->Cell(25, 6, 'RWF ' . number_format($row['closing_value'], 0), 1, 1, 'C');
    }
    
    // Total row
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(85, 8, 'TOTAL:', 1, 0, 'R');
    $pdf->Cell(25, 8, 'RWF ' . number_format($total_opening, 0), 1, 0, 'C');
    $pdf->Cell(25, 8, 'RWF ' . number_format($total_in, 0), 1, 0, 'C');
    $pdf->Cell(25, 8, number_format($total_out, 0), 1, 0, 'C');
    $pdf->Cell(25, 8, '', 1, 0, 'C');
    $pdf->Cell(25, 8, '', 1, 0, 'C');
    $pdf->Cell(25, 8, 'RWF ' . number_format($total_closing, 0), 1, 1, 'C');
    
    // Output PDF
    $filename = 'Stock_Report_' . date('Y-m-d', strtotime($from)) . '_to_' . date('Y-m-d', strtotime($to)) . '.pdf';
    $pdf->Output($filename, 'D');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report</title>
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
            <div class="card card-modern p-4">
                <div class="card-header bg-transparent border-0">
                    <h3 class="text-center mb-0">Stock Report</h3>
                    <p class="text-center text-muted mt-2">
                        <?php echo date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)); ?> | Item: <?php echo htmlspecialchars($itemname); ?>
                    </p>
                </div>

                <div class="card-body">
                    <!-- Form -->
                    <form action="" method="POST" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Product <span class="text-danger">*</span></label>
                                <select class="form-select" id="item" name="item" required>
                                    <option value="all" <?php echo $item === 'all' ? 'selected' : ''; ?>>All Items</option>
                                    <?php
                                    $sql = $db->prepare("SELECT item_id, item_name FROM tbl_items WHERE item_status = '1' ORDER BY item_name");
                                    $sql->execute();
                                    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = $item === $row['item_id'] ? 'selected' : '';
                                        echo "<option value='{$row['item_id']}' $selected>" . htmlspecialchars($row['item_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date From <span class="text-danger">*</span></label>
                                <input type="date" id="date_from" name="date_from" class="form-control" value="<?php echo htmlspecialchars($from); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date To <span class="text-danger">*</span></label>
                                <input type="date" id="date_to" name="date_to" class="form-control" value="<?php echo htmlspecialchars($to); ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">&nbsp;</label>
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
                            <button  onclick="printReport()">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="stockTable">
                            <thead>
                                <tr>
                                    <th style="color: white;">Date</th>
                                    <th style="color: white;">Item Name</th>
                                    <th style="color: white;">Opening</th>
                                    <th style="color: white;">New Stock</th>
                                    <th style="color: white;">Qty Out</th>
                                    <th style="color: white;">Closing</th>
                                    <th style="color: white;">Unit Price</th>
                                    <th style="color: white;">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stock_data as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                        <td>
                                            <?php echo $row['last_qty'] ? number_format($row['last_qty'], 0) : '-'; ?>
                                            <br>Total: RWF <?php echo number_format($row['opening_value'], 0); ?>
                                        </td>
                                        <td>
                                            <?php echo $row['in_qty'] ? number_format($row['in_qty'], 0) : '-'; ?>
                                            <br>Total: RWF <?php echo number_format($row['in_value'], 0); ?>
                                        </td>
                                        <td><?php echo $row['out_qty'] ? number_format($row['out_qty'], 0) : '-'; ?></td>
                                        <td><?php echo number_format($row['end_qty'], 0); ?></td>
                                        <td>RWF <?php echo number_format($row['average_price'], 0); ?></td>
                                        <td>RWF <?php echo number_format($row['closing_value'], 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <th colspan="2">TOTAL:</th>
                                    <th>RWF <?php echo number_format($total_opening, 0); ?></th>
                                    <th>RWF <?php echo number_format($total_in, 0); ?></th>
                                    <th><?php echo number_format($total_out, 0); ?></th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>RWF <?php echo number_format($total_closing, 0); ?></th>
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

<script>
$(document).ready(function() {
    // Form submission handler with loading indicator
    $('form').on('submit', function(e) {
        const from = $('#date_from').val();
        const to = $('#date_to').val();
        const item = $('#item').val();
        if (from && to && from <= to && item) {
            $('#loader').show();
        } else {
            e.preventDefault();
            alert('Please select a valid date range and item');
        }
    });
});

// PDF Generation Function
function generatePDF() {
    const from = $('#date_from').val();
    const to = $('#date_to').val();
    const item = $('#item').val();
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
    button.disabled = true;
    
    window.location.href = `?action=generate_pdf&from=${from}&to=${to}&item=${item}`;
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 3000);
}

// Print Report Function
function printReport() {
    const printWindow = window.open('', '_blank');
    const from = '<?php echo date('M d, Y', strtotime($from)); ?>';
    const to = '<?php echo date('M d, Y', strtotime($to)); ?>';
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Stock Report - Print</title>
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
                    .header, .summary, table {
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><?php echo htmlspecialchars($company_name); ?></h1>
                <p>TEL: <?= $company_phone ?></p>
                <p>TIN/VAT: <?= $company_tin ?></p>
                <h2>STOCK REPORT</h2>
                <p>Period: ${from} - ${to}</p>
                <p>Item: <?php echo htmlspecialchars($itemname); ?></p>
                <p>Generated on: ${new Date().toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' })}</p>
                <p>Printed by: <?php echo htmlspecialchars($_SESSION['f_name'] ?? 'Unknown User'); ?></p>
            </div>
            
            <div class="summary">
                <h3>Summary</h3>
                <div class="summary-grid">
                    <div>Total Opening Value: <strong>RWF <?php echo number_format($total_opening, 0); ?></strong></div>
                    <div>Total Incoming Value: <strong>RWF <?php echo number_format($total_in, 0); ?></strong></div>
                    <div>Total Quantity Out: <strong><?php echo number_format($total_out, 0); ?></strong></div>
                    <div>Total Closing Value: <strong>RWF <?php echo number_format($total_closing, 0); ?></strong></div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item Name</th>
                        <th>Opening</th>
                        <th>New Stock</th>
                        <th>Qty Out</th>
                        <th>Closing</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stock_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td class="text-center">
                                <?php echo $row['last_qty'] ? number_format($row['last_qty'], 0) : '-'; ?>
                                <br>Total: RWF <?php echo number_format($row['opening_value'], 0); ?>
                            </td>
                            <td class="text-center">
                                <?php echo $row['in_qty'] ? number_format($row['in_qty'], 0) : '-'; ?>
                                <br>Total: RWF <?php echo number_format($row['in_value'], 0); ?>
                            </td>
                            <td class="text-center"><?php echo $row['out_qty'] ? number_format($row['out_qty'], 0) : '-'; ?></td>
                            <td class="text-center"><?php echo number_format($row['end_qty'], 0); ?></td>
                            <td class="text-right">RWF <?php echo number_format($row['average_price'], 0); ?></td>
                            <td class="text-right">RWF <?php echo number_format($row['closing_value'], 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th colspan="2">TOTAL:</th>
                        <th class="text-right">RWF <?php echo number_format($total_opening, 0); ?></th>
                        <th class="text-right">RWF <?php echo number_format($total_in, 0); ?></th>
                        <th class="text-center"><?php echo number_format($total_out, 0); ?></th>
                        <th>-</th>
                        <th>-</th>
                        <th class="text-right">RWF <?php echo number_format($total_closing, 0); ?></th>
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

</body>
</html>
