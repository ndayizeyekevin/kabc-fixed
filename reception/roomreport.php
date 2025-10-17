<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <div class="data-table-area">
        <div class="container">
           <form method="POST">
  <label>From:</label>
  <input type="date" name="fromdate" value="<?php echo $_POST['fromdate'] ?? ''; ?>">
  <label>To:</label>
  <input type="date" name="todate" value="<?php echo $_POST['todate'] ?? ''; ?>">
  <button type="submit" name="filter">Filter</button>
</form>
<br>
<a href="receipt/room_report.php" class ="btn btn-warning">Export to PDF</a>
<button class ="btn btn-success" onclick="exportToExcel()">Export to Excel</button>
<br><br>
<?php
$conditions = "";
$params = [];

if (isset($_POST['filter']) && !empty($_POST['fromdate']) && !empty($_POST['todate'])) {
    $from = $_POST['fromdate'];
    $to = $_POST['todate'];
    $conditions = "WHERE DATE(c.created_at) BETWEEN :from AND :to";
    $params = [':from' => $from, ':to' => $to];

    $_SESSION['from'] = $from;
    $_SESSION['to'] = $to;
}

// Step 1: Get orders (one per guest per order)
$orderSql = $db->prepare("
    SELECT
        c.id AS cmd_id,
        c.OrderCode,
        c.created_at,
        g.first_name,
        g.last_name
    FROM tbl_cmd c
    INNER JOIN tbl_acc_guest g ON c.room_client = g.id
    $conditions
    ORDER BY c.created_at DESC
");
$orderSql->execute($params);

if ($orderSql->rowCount()) {
    echo '<table id="myTable" class="table table-bordered">';
    echo '<thead>
            <tr>
                <th>#</th>
                <th>Guest Name</th>
                <th>Order Code</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
            </tr>
          </thead>';
    echo '<tbody>';

    $no = 1;
    $grandTotal = 0;

    while ($order = $orderSql->fetch()) {
        $orderCode = $order['OrderCode'];

        // Step 2: Get items in the order
        $itemSql = $db->prepare("
            SELECT
                m.menu_name,
                m.menu_price,
                q.cmd_qty,
                (m.menu_price * q.cmd_qty) AS line_total
            FROM tbl_cmd_qty q
            INNER JOIN menu m ON q.cmd_item = m.menu_id
            WHERE q.cmd_code = :code
        ");
        $itemSql->execute([':code' => $orderCode]);

        $itemsHtml = "<table  class='table table-sm table-bordered'><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th></tr>";
        $orderTotal = 0;

        while ($item = $itemSql->fetch()) {
            $itemsHtml .= "<tr>
                <td>" . htmlspecialchars($item['menu_name']) . "</td>
                <td>" . number_format($item['menu_price'], 2) . "</td>
                <td>" . (int)$item['cmd_qty'] . "</td>
                <td>" . number_format($item['line_total'], 2) . "</td>
            </tr>";
            $orderTotal += $item['line_total'];
        }
        $itemsHtml .= "</table>";

        $grandTotal += $orderTotal;

        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($orderCode) . "</td>";
        echo "<td>" . htmlspecialchars($order['created_at']) . "</td>";
        echo "<td>$itemsHtml</td>";
        echo "<td><strong>" . number_format($orderTotal, 2) . "</strong></td>";
        echo "</tr>";
    }

    echo "<tr>
            <td colspan='5' align='right'><strong>Grand Total</strong></td>
            <td><strong>" . number_format($grandTotal, 2) . "</strong></td>
          </tr>";
    echo '</tbody></table>';
} else {
    echo "<p>No data found.</p>";
}
?>

</div></div>
<script>
function exportToExcel() {
    var table = document.getElementById("myTable");
    var wb = XLSX.utils.table_to_book(table, {sheet:"Sheet1"});
    XLSX.writeFile(wb, "Guest Report.xlsx");
}
</script>
