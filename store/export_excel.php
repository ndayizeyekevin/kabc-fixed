<?php
include '../inc/conn.php';

$item = isset($_POST['item']) ? $_POST['item'] : 0;
$from = isset($_POST['from']) ? $_POST['from'] : date('Y-m-d');
$to = isset($_POST['to']) ? $_POST['to'] : date('Y-m-d');

$where = "WHERE t.date BETWEEN '$from' AND '$to'";
if ($item != 0) {
    $where .= " AND t.item = '$item'";
}

$sql = "SELECT 
            c.cat_name,
            i.cat_id,
            t.item,
            i.item_name,
            SUM(t.last_qty) AS opening,
            SUM(t.in_qty) AS new_stock,
            SUM(t.out_qty) AS qty_out,
            SUM(t.end_qty) AS closing,
            i.price
        FROM tbl_progress t
        JOIN tbl_items i ON i.item_id = t.item
        JOIN category c ON c.cat_id = i.cat_id
        $where
        GROUP BY c.cat_name, t.item
        ORDER BY c.cat_name, i.item_name";

$result = $conn->query($sql);

// Headers for Excel export
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=stock_report_" . date("Ymd_His") . ".xlsx");

echo "<table border='1'>";
echo "<tr>
        <th>Category</th>
        <th>Item Name</th>
        <th>Opening Qty</th>
        <th>New Stock</th>
        <th>Qty Out</th>
        <th>Closing Qty</th>
        <th>Price</th>
        <th>Total</th>
      </tr>";

$grand_total = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total = $row['closing'] * $row['price'];
        $grand_total += $total;

        echo "<tr>
                <td>{$row['cat_name']}</td>
                <td>{$row['item_name']}</td>
                <td>{$row['opening']}</td>
                <td>{$row['new_stock']}</td>
                <td>{$row['qty_out']}</td>
                <td>{$row['closing']}</td>
                <td>{$row['price']}</td>
                <td>{$total}</td>
              </tr>";
    }

    echo "<tr>
            <td colspan='7'><strong>Grand Total</strong></td>
            <td><strong>{$grand_total}</strong></td>
          </tr>";
} else {
    echo "<tr><td colspan='8'>No stock data found.</td></tr>";
}

echo "</table>";
?>
