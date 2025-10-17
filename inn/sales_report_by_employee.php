<?php
// Per Servant Sales Report with Date Range and Print/PDF Functionality
// Requires: composer require tecnickcom/tcpdf

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration file for database connection
require_once ("../inc/config.php");

// Set default date range
$from = isset($_SESSION['date_from']) ? $_SESSION['date_from'] : date('Y-m-d');
$to = isset($_SESSION['date_to']) ? $_SESSION['date_to'] : date('Y-m-d');

// Handle form submissions for date range
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['date_from'], $_POST['date_to'])) {
        $_SESSION['date_from'] = $from = $_POST['date_from'];
        $_SESSION['date_to'] = $to = $_POST['date_to'];
    }
}

// Handle PDF generation
if (isset($_GET['action']) && $_GET['action'] === 'generate_pdf') {
    require_once '../reception/receipt/dompdf/vendor/autoload.php';
    generatePDFReport($db, $from, $to);
    exit;
}

// Function to get employee name
function getEmployeeName($db, $id) {
    try {
        $sql = $db->prepare("SELECT f_name, l_name FROM tbl_users WHERE user_id = :id");
        $sql->execute(['id' => $id]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['f_name'] . " " . $row['l_name'] : 'Unknown';
    } catch (PDOException $e) {
        error_log("Error in getEmployeeName: " . $e->getMessage());
        return 'Unknown';
    }
}

// Function to count orders
function countOrders($db, $id, $from, $to) {
    try {
        $sql = $db->prepare("SELECT COUNT(*) as order_count 
                            FROM tbl_cmd_qty 
                            WHERE cmd_status = '12' 
                            AND Serv_id = :id 
                            AND DATE(created_at) BETWEEN :from AND :to");
        $sql->execute(['id' => $id, 'from' => $from, 'to' => $to]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        return $row['order_count'];
    } catch (PDOException $e) {
        error_log("Error in countOrders: " . $e->getMessage());
        return 0;
    }
}

// Function to get total sales
function getTotal($db, $id, $from, $to) {
    try {
        $sql = $db->prepare("SELECT SUM(cq.cmd_qty * m.menu_price) as total 
                            FROM tbl_cmd_qty cq 
                            INNER JOIN menu m ON m.menu_id = cq.cmd_item 
                            WHERE cq.cmd_status = '12' 
                            AND cq.Serv_id = :id 
                            AND DATE(cq.created_at) BETWEEN :from AND :to");
        $sql->execute(['id' => $id, 'from' => $from, 'to' => $to]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        return floatval($row['total'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error in getTotal: " . $e->getMessage());
        return 0;
    }
}

// Fetch data for table
$servants = [];
$totalOrders = 0;
$totalSales = 0;
try {
    $sql = $db->prepare("SELECT DISTINCT Serv_id 
                        FROM tbl_cmd_qty 
                        WHERE cmd_status = '12' 
                        AND DATE(created_at) BETWEEN :from AND :to");
    $sql->execute(['from' => $from, 'to' => $to]);
    
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $serv_id = $row['Serv_id'];
        $orders = countOrders($db, $serv_id, $from, $to);
        $total = getTotal($db, $serv_id, $from, $to);
        
        $servants[] = [
            'serv_id' => $serv_id,
            'name' => getEmployeeName($db, $serv_id),
            'orders' => $orders,
            'total' => $total
        ];
        
        $totalOrders += $orders;
        $totalSales += $total;
    }
} catch (PDOException $e) {
    error_log("Error fetching servants: " . $e->getMessage());
}

// PDF Generation Function
function generatePDFReport($db, $from, $to) {
    $servants = [];
    $totalOrders = 0;
    $totalSales = 0;
    try {
        $sql = $db->prepare("SELECT DISTINCT Serv_id 
                            FROM tbl_cmd_qty 
                            WHERE cmd_status = '12' 
                            AND DATE(created_at) BETWEEN :from AND :to");
        $sql->execute(['from' => $from, 'to' => $to]);
        
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $serv_id = $row['Serv_id'];
            $orders = countOrders($db, $serv_id, $from, $to);
            $total = getTotal($db, $serv_id, $from, $to);
            
            $servants[] = [
                'serv_id' => $serv_id,
                'name' => getEmployeeName($db, $serv_id),
                'orders' => $orders,
                'total' => $total
            ];
            
            $totalOrders += $orders;
            $totalSales += $total;
        }
    } catch (PDOException $e) {
        error_log("Error fetching servants for PDF: " . $e->getMessage());
    }
    
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Sales Report System');
    $pdf->SetAuthor('Restaurant Management');
    $pdf->SetTitle('Per Servant Sales Report - ' . date('M d, Y', strtotime($from)) . ' to ' . date('M d, Y', strtotime($to)));
    $pdf->SetSubject('Per Servant Sales Report');
    
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
    $pdf->Cell(0, 8, 'PER SERVANT SALES REPORT', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 6, 'Period: ' . date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('M d, Y H:i:s'), 0, 1, 'C');
    $pdf->Ln(8);
    
    // Summary Section
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'SUMMARY', 0, 1, 'L');
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(3);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Total Orders:', 0, 0, 'L');
    $pdf->Cell(50, 6, number_format($totalOrders, 0), 0, 1, 'L');
    $pdf->Cell(50, 6, 'Total Sales:', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(50, 6, 'RWF ' . number_format($totalSales, 0), 0, 1, 'L');
    $pdf->Ln(8);
    
    // Top Performers
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'TOP PERFORMING SERVANTS', 0, 1, 'L');
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(3);
    
    $topServants = array_slice($servants, 0, 3);
    foreach ($topServants as $index => $servant) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, ($index + 1) . '. ' . $servant['name'] . ': ' . number_format($servant['orders'], 0) . ' orders, RWF ' . number_format($servant['total'], 0), 0, 1, 'L');
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
    $pdf->Cell(20, 8, 'No', 1, 0, 'C', 1);
    $pdf->Cell(80, 8, 'Servant Name', 1, 0, 'C', 1);
    $pdf->Cell(40, 8, 'Total Orders', 1, 0, 'C', 1);
    $pdf->Cell(40, 8, 'Total Sales', 1, 1, 'C', 1);
    
    // Table data
    $pdf->SetFont('helvetica', '', 9);
    $i = 0;
    foreach ($servants as $servant) {
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(20, 8, 'No', 1, 0, 'C', 1);
            $pdf->Cell(80, 8, 'Servant Name', 1, 0, 'C', 1);
            $pdf->Cell(40, 8, 'Total Orders', 1, 0, 'C', 1);
            $pdf->Cell(40, 8, 'Total Sales', 1, 1, 'C', 1);
            $pdf->SetFont('helvetica', '', 9);
        }
        
        $pdf->Cell(20, 6, ++$i, 1, 0, 'C');
        $pdf->Cell(80, 6, $servant['name'], 1, 0, 'L');
        $pdf->Cell(40, 6, number_format($servant['orders'], 0), 1, 0, 'C');
        $pdf->Cell(40, 6, 'RWF ' . number_format($servant['total'], 0), 1, 1, 'R');
    }
    
    // Total row
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(100, 8, 'GRAND TOTAL:', 1, 0, 'R');
    $pdf->Cell(40, 8, number_format($totalOrders, 0), 1, 0, 'C');
    $pdf->Cell(40, 8, 'RWF ' . number_format($totalSales, 0), 1, 1, 'R');
    
    // Output PDF
    $filename = 'Per_Servant_Sales_Report_' . date('Y-m-d', strtotime($from)) . '_to_' . date('Y-m-d', strtotime($to)) . '.pdf';
    $pdf->Output($filename, 'D');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Per Servant Sales Report</title>
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
        .top-performer {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card card-modern p-4">
                <div class="card-header bg-transparent border-0">
                    <h3 class="text-center mb-0"><i class="fa fa-users"></i> Per Servant Sales Report</h3>
                    <p class="text-center text-muted mt-2">
                        <?php echo date('M d, Y', strtotime($from)) . ' - ' . date('M d, Y', strtotime($to)); ?>
                    </p>
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
                           
                            <button onclick="printReport()">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                        </div>
                    </div>

                    <!-- Top Performers -->
                    <?php if (!empty($servants)): ?>
                    <div class="top-performer">
                        <h5 class="mb-3"><i class="fas fa-trophy me-2"></i>Top Performing Servants</h5>
                        <div class="row">
                            <?php 
                            $topServants = array_slice($servants, 0, 3);
                            foreach ($topServants as $index => $servant): 
                            ?>
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
                                    <div>
                                        <strong><?php echo htmlspecialchars($servant['name']); ?></strong><br>
                                        <small>Orders: <?php echo number_format($servant['orders'], 0); ?> | Sales: RWF <?php echo number_format($servant['total'], 0); ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="salesTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="color: white;">No</th>
                                    <th style="color: white;">Servant Name</th>
                                    <th style="color: white;">Total Orders</th>
                                    <th style="color: white;">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 0;
                                foreach ($servants as $servant): 
                                ?>
                                    <tr>
                                        <td><?php echo ++$i; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($servant['name']); ?></td>
                                        <td><span class="badge bg-info"><?php echo number_format($servant['orders'], 0); ?></span></td>
                                        <td class="fw-bold text-success">RWF <?php echo number_format($servant['total'], 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <th colspan="2">GRAND TOTAL:</th>
                                    <th><?php echo number_format($totalOrders, 0); ?></th>
                                    <th class="text-success">RWF <?php echo number_format($totalSales, 0); ?></th>
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
});

// PDF Generation Function
function generatePDF() {
    const from = $('#date_from').val();
    const to = $('#date_to').val();
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
    button.disabled = true;
    
    window.location.href = `?action=generate_pdf&from=${from}&to=${to}`;
    
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
            <title>Per Servant Sales Report - Print</title>
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
                <h2>PER SERVANT SALES REPORT</h2>
                <p>Period: ${from} - ${to}</p>
                <p>Generated on: ${new Date().toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' })}</p>
                <p>Printed by: <?php echo htmlspecialchars($_SESSION['f_name'] ?? 'Unknown User'); ?></p>
            </div>
            
            <div class="summary">
                <h3>Summary</h3>
                <div class="summary-grid">
                    <div>Total Orders: <strong><?php echo number_format($totalOrders, 0); ?></strong></div>
                    <div>Total Sales: <strong>RWF <?php echo number_format($totalSales, 0); ?></strong></div>
                </div>
            </div>
            
            <div class="top-performer">
                <h3>Top Performing Servants</h3>
                <div class="top-performer-grid">
                    <?php foreach (array_slice($servants, 0, 3) as $index => $servant): ?>
                        <div>
                            <strong>#<?php echo $index + 1; ?> <?php echo htmlspecialchars($servant['name']); ?></strong><br>
                            Orders: <?php echo number_format($servant['orders'], 0); ?><br>
                            Sales: RWF <?php echo number_format($servant['total'], 0); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Servant Name</th>
                        <th>Total Orders</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 0;
                    foreach ($servants as $servant): 
                    ?>
                        <tr>
                            <td class="text-center"><?php echo ++$i; ?></td>
                            <td><?php echo htmlspecialchars($servant['name']); ?></td>
                            <td class="text-center"><?php echo number_format($servant['orders'], 0); ?></td>
                            <td class="text-right">RWF <?php echo number_format($servant['total'], 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th colspan="2">GRAND TOTAL:</th>
                        <th class="text-center"><?php echo number_format($totalOrders, 0); ?></th>
                        <th class="text-right">RWF <?php echo number_format($totalSales, 0); ?></th>
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
