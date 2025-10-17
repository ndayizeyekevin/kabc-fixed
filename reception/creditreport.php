    <!-- jQuery -->


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

<br><br>
<a href="receipt/credit_report.php" class ="btn btn-warning">Export to PDF</a>
<button class ="btn btn-success" onclick="exportToExcel()">Export to Excel</button>
<br><br>
<?php


$conditions = "WHERE c.creadit_user IS NOT NULL";
$params = [];

if (isset($_POST['filter']) && !empty($_POST['fromdate']) && !empty($_POST['todate'])) {
    $from = $_POST['fromdate'];
    $to = $_POST['todate'];
    $conditions .= " AND DATE(c.created_at) BETWEEN :from AND :to";
    $params[':from'] = $from;
    $params[':to'] = $to;

     $_SESSION['from'] = $from;
    $_SESSION['to'] = $to;

}

// Step 1: Get credit orders
$orderSql = $db->prepare("
    SELECT
        c.id AS cmd_id,
        c.OrderCode,
        c.created_at,
        cr.f_name,
        cr.l_name,
        cr.phone,
        cr.tinnumber
    FROM tbl_cmd c
    INNER JOIN creadit_id cr ON c.creadit_user = cr.id
    $conditions
    ORDER BY c.created_at DESC
");
$orderSql->execute($params);

if ($orderSql->rowCount()) {
    ?>

    <a href="receipt"></a>


    <?php
    echo '<table  id="myTable" class="table table-bordered">';
    echo '<thead>
            <tr>
                <th>#</th>
                <th>Credit Client</th>
                <th>Order Code</th>
                <th>Phone / TIN</th>
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

        // Step 2: Get items for this order
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
        echo "<td>" . htmlspecialchars($order['f_name'] . ' ' . $order['l_name']) . "</td>";
        echo "<td>" . htmlspecialchars($orderCode) . "</td>";
        echo "<td>" . htmlspecialchars($order['phone']) . "<br><small>TIN: " . htmlspecialchars($order['tinnumber']) . "</small></td>";
        echo "<td>" . htmlspecialchars($order['created_at']) . "</td>";
        echo "<td>$itemsHtml</td>";
        echo "<td><strong>" . number_format($orderTotal, 2) . "</strong></td>";
        echo "</tr>";
    }

    echo "<tr>
            <td colspan='6' align='right'><strong>Grand Total</strong></td>
            <td><strong>" . number_format($grandTotal, 2) . "</strong></td>
          </tr>";
    echo '</tbody></table>';
} else {
    echo "<p>No credit orders found.</p>";
}
?>
</div></div>
<script>
function exportToExcel() {
    var table = document.getElementById("myTable");
    var wb = XLSX.utils.table_to_book(table, {sheet:"Sheet1"});
    XLSX.writeFile(wb, "Credits report.xlsx");
}
</script>

