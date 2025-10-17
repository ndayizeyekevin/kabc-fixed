<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../inc/conn.php';
function getItemPrice($id){
	
include '../inc/conn.php';	

$item = "SELECT * FROM tbl_items where item_id='$id' ";
$result = $conn->query($item);

$tbl_progress = $conn ->query ("SELECT * FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1;");

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    while($row2 = $tbl_progress->fetch_assoc()){
        return  $row2['new_price'] > 0 ? $row2['new_price'] : $row['price']  ;
    }
  }
}

}

// function getOpeningStock($itemId) {
//     include '../inc/conn.php';

//     // Get the first day of the current month
//     $monthStartDate = date('Y-m-01');

//     // Get the latest closing stock BEFORE the start of the current month
//     $stmt = mysqli_query($conn, "
//         SELECT end_qty AS opening_stock 
//         FROM tbl_progress 
//         WHERE item = '$itemId' AND date < '$monthStartDate' 
//         ORDER BY date DESC, prog_id DESC 
//         LIMIT 1
//     ");

//     if ($result = mysqli_fetch_assoc($stmt)) {
//         return floatval($result['opening_stock']);
//     } else {
//         return 0;
//     }
// }



function getOpeningStock($itemId) {
    include '../inc/conn.php';

    $itemId = intval($itemId);
    $monthStart = date('Y-m'); // First day of current month with data

    $stmt = mysqli_query($conn, "
        SELECT last_qty 
        FROM tbl_progress 
        WHERE item = '$itemId' AND date > '$monthStart' 
        ORDER BY date ASC, prog_id ASC 
        LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($stmt)) {
        return floatval($row['last_qty']);
    } else {
        return 0; // No previous record found
    }
}


// Filters
$item = isset($_POST['item']) ? $_POST['item'] : 0;

$where = "WHERE 1";
if (!empty($_POST['from']) && !empty($_POST['to'])) {
    $from = $_POST['from'];
    $to = $_POST['to'];
    $where .= " AND t.date BETWEEN '$from' AND '$to'";
    $monthStart = date('Y-m-01', strtotime($from));
} else {
    $monthStart = date('Y-m-01');
}

if ($item != 0) {
    $where .= " AND t.item = '$item'";
}

// Main SQL
$sql = "SELECT 
            c.cat_name,
            i.cat_id,
            i.item_id,
            t.item,
            i.item_name,
            SUM(t.in_qty) AS new_stock,
            SUM(t.out_qty) AS qty_out,
            i.price
        FROM tbl_progress t
        JOIN tbl_items i ON i.item_id = t.item
        JOIN category c ON c.cat_id = i.cat_id
        $where
        GROUP BY c.cat_name, t.item
        ORDER BY c.cat_name, i.item_name";

$result = $conn->query($sql);

$grand_total = 0;
$current_category = '';
$category_total = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['cat_name'];
        $item_name = $row['item_name'];
        $new_stock = floatval($row['new_stock']);
        $qty_out = floatval($row['qty_out']);
        // $price = floatval($row['price']);
        $price = getItemPrice($row['item_id']);
        $id = $row['item_id'];

        // Opening from nearest available record
        $opening = getOpeningStock($id);

        // Calculate closing = opening + new - out
        $closing = $opening + $new_stock - $qty_out;

        $total_price = (float)$closing * (float)$price;

        // New category group?
        if ($current_category != $category) {
            if ($current_category != '') {
                echo "<tr style='background:#f1f1f1;'>
                        <td colspan='6'><strong>Total for $current_category</strong></td>
                        <td><strong>" . number_format($category_total, 2) . "</strong></td>
                      </tr>";
            }

            echo "<tr style='background:#d1ecf1;'><td colspan='7'><strong>Category: $category</strong></td></tr>";
            $current_category = $category;
            $category_total = 0;
        }

        // Item row
        echo "<tr>
                <td><a href='index.php?resto=cumurative&item_id={$id}'>{$item_name}</a></td>
                <td>" . number_format($opening, 2) . "</td>
                <td>" . number_format($new_stock, 2) . "</td>
                <td>" . number_format($qty_out, 2) . "</td>
                <td>" . number_format($closing, 2) . "</td>
                <td>" . number_format((float)$price, 2) . "</td>
                <td>" . number_format($total_price, 2) . "</td>
              </tr>";

        $category_total += $total_price;
        $grand_total += $total_price;
    }

    // Final category total
    echo "<tr style='background:#f1f1f1;'>
            <td colspan='6'><strong>Total for $current_category</strong></td>
            <td><strong>" . number_format($category_total, 2) . "</strong></td>
          </tr>";

    // Grand total
    echo "<tr style='background:#d4edda;' id='grand-total-row'>
            <td colspan='6'><strong>General Total</strong></td>
            <td><strong>" . number_format($grand_total, 2) . "</strong></td>
          </tr>";
} else {
    echo "<tr><td colspan='7'><center>No stock records found for selected criteria.</center></td></tr>";
}
?>
