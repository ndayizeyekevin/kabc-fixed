<?php
// finance/sales_export_xlsx.php
// Server-side export endpoint using URL GET parameters.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Kigali');

include '../inc/conn.php';

// 1. Capture dates from request (GET or POST)
$selected_from = $_REQUEST['select_from'] ?? null;
$selected_to   = $_REQUEST['select_to'] ?? $selected_from ?? null;

// If no date provided, fallback to currently open business day (mirror sales.php behavior)
if (empty($selected_from)) {
    $sql_last_day = $db->prepare("SELECT opened_at, closed_at FROM days WHERE closed_at IS NULL ORDER BY opened_at DESC LIMIT 1");
    $sql_last_day->execute();
    $last_day = $sql_last_day->fetch(PDO::FETCH_ASSOC);

    if ($last_day) {
        // Use the opened_at date for select_from and todays date for select_to
        $selected_from = date('Y-m-d', strtotime($last_day['opened_at']));
        $selected_to = date('Y-m-d');
    } else {
        die("Error: No date provided and no open business day.");
    }
}

// Ensure select_to is populated
if (empty($selected_to)) { $selected_to = $selected_from; }

// 2. Resolve business-day range (matches logic in sales.php)
$from = $to = null;
$resolveStmt = $db->prepare("SELECT opened_at, closed_at FROM days 
                             WHERE DATE(opened_at) = :selected_date 
                             OR (:selected_date_start BETWEEN opened_at AND COALESCE(closed_at, NOW())) 
                             ORDER BY opened_at DESC LIMIT 1");

// Resolve Start Time based on 'From' date
$resolveStmt->execute(['selected_date' => $selected_from, 'selected_date_start' => $selected_from . ' 00:00:00']);
if ($resolveStmt->rowCount() > 0) {
    $f = $resolveStmt->fetch(PDO::FETCH_ASSOC);
    $from = $f['opened_at'];
} else {
    $from = $selected_from . ' 00:00:00';
}

// Resolve End Time based on 'To' date
$resolveStmt->execute(['selected_date' => $selected_to, 'selected_date_start' => $selected_to . ' 00:00:00']);
if ($resolveStmt->rowCount() > 0) {
    $t = $resolveStmt->fetch(PDO::FETCH_ASSOC);
    $to = $t['closed_at'] ?? date('Y-m-d H:i:s');
} else {
    $to = $selected_to . ' 23:59:59';
}

// Ensure range order is correct
if (strtotime($from) > strtotime($to)) {
    $tmp = $from; $from = $to; $to = $tmp;
}

// 3. Build category list (Qualifying logic: room charged or fully paid)
$categories = [];
$catSql = $db->prepare(
    "SELECT DISTINCT q.cat_id, c.cat_name
     FROM tbl_cmd_qty q
     INNER JOIN category c ON c.cat_id = q.cat_id
     INNER JOIN tbl_cmd t ON t.OrderCode = q.cmd_code
     WHERE q.created_at BETWEEN :from_time AND :to_time
       AND (
         t.room_client IS NOT NULL
         OR (
           (SELECT SUM(amount) FROM payment_tracks WHERE order_code = t.OrderCode) >=
           (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty)
            FROM tbl_cmd_qty q2
            WHERE q2.cmd_code = t.OrderCode)
         )
       )"
);
$catSql->execute(['from_time' => '2025-12-30 10:53:06', 'to_time' => '2025-12-31 08:59:52']);
while ($r = $catSql->fetch(PDO::FETCH_ASSOC)) {
    $categories[$r['cat_id']] = $r['cat_name'];
}

if (empty($categories)) {
    die("No sales found for the selected period.");
}

// 4. Prepare data rows
$rows = [];
$rows[] = ['Category', 'Item Name', 'Unit Price', 'Qty', 'Total'];

foreach ($categories as $catId => $catName) {
    $itemStmt = $db->prepare(
        "SELECT q.item_name, q.unit_price, SUM(q.cmd_qty) AS qty, 
                SUM(CAST(REPLACE(q.unit_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS total
         FROM tbl_cmd_qty q
         INNER JOIN tbl_cmd t ON t.OrderCode = q.cmd_code
         WHERE q.created_at BETWEEN :from_time AND :to_time
           AND q.cat_id = :cat_id
           AND (
             t.room_client IS NOT NULL
             OR (
               (SELECT SUM(amount) FROM payment_tracks WHERE order_code = t.OrderCode) >=
               (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty) 
                FROM tbl_cmd_qty q2 WHERE q2.cmd_code = t.OrderCode)
             )
           )
         GROUP BY q.cmd_item, q.item_name, q.unit_price"
    );
    $itemStmt->execute(['from_time' => '2025-12-30 10:53:06', 'to_time' => '2025-12-31 08:59:52', 'cat_id' => $catId]);

    while ($it = $itemStmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = [$catName, $it['item_name'], $it['unit_price'], $it['qty'], $it['total']];
    }

    // Sub-total logic
    $sumStmt = $db->prepare(
        "SELECT SUM(CAST(REPLACE(q.unit_price, ',', '') AS DECIMAL(18,2)) * q.cmd_qty) AS cat_total, 
                SUM(q.cmd_qty) as cat_qty
         FROM tbl_cmd_qty q
         INNER JOIN tbl_cmd t ON t.OrderCode = q.cmd_code
         WHERE q.created_at BETWEEN :from_time AND :to_time
           AND q.cat_id = :cat_id
           AND (
             t.room_client IS NOT NULL
             OR (
               (SELECT SUM(amount) FROM payment_tracks WHERE order_code = t.OrderCode) >=
               (SELECT SUM(CAST(REPLACE(q2.unit_price, ',', '') AS DECIMAL(18,2)) * q2.cmd_qty) 
                FROM tbl_cmd_qty q2 WHERE q2.cmd_code = t.OrderCode)
             )
           )"
    );
    $sumStmt->execute(['from_time' => '2025-12-30 10:53:06', 'to_time' => '2025-12-31 08:59:52', 'cat_id' => $catId]);
    $s = $sumStmt->fetch(PDO::FETCH_ASSOC);
    $rows[] = [$catName . ' - Sub Total', '', '', $s['cat_qty'] ?? 0, $s['cat_total'] ?? 0];
}

// Debug mode: show resolved range, number of exported rows and sum total for verification
if (isset($_GET['debug']) || isset($_POST['debug'])) {
    $export_rows_count = 0;
    $export_sum_total = 0.0;
    foreach ($rows as $i => $r) {
        if ($i === 0) continue; // skip header
        $val = $r[4] ?? null;
        if (is_numeric($val)) {
            $export_rows_count++;
            $export_sum_total += (float)$val;
        }
    }
    header('Content-Type: application/json');
    echo json_encode([
        'from' => '2025-12-30 10:53:06',
        'to' => '2025-12-31 08:59:52',
        'categories' => array_values($categories),
        'rows' => $export_rows_count,
        'sum_total' => $export_sum_total,
        'sample_row' => $rows[1] ?? null
    ], JSON_PRETTY_PRINT);
    exit;
}

// 5. Output Logic
$autoload = __DIR__ . '/../vendor/autoload.php';
$filename = "Sales_Report_" . $selected_from . "_to_" . $selected_to;

if (file_exists($autoload)) {
    require_once $autoload;
    if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($rows, NULL, 'A1');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'.xlsx"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

// Fallback to HTML Excel
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'.xls"');
echo "<table border=1>";
foreach ($rows as $r) {
    echo "<tr><td>" . implode("</td><td>", $r) . "</td></tr>";
}
echo "</table>";