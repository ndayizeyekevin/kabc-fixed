<?php 
include "../inc/conn.php";

$results_per_page = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $results_per_page;

$from = $_SESSION['from'] ?? date('Y-m-d');
$to = $_SESSION['to'] ?? date('Y-m-d');
$item = $_SESSION['item'] ?? 'all';

// Get all items with their categories in one query with proper pagination
$sql = "SELECT c.cat_id, c.cat_name, i.item_id, i.item_name 
        FROM category c 
        JOIN tbl_items i ON i.cat_id = c.cat_id 
        JOIN tbl_progress p ON p.item = i.item_id 
        WHERE DATE(p.date) BETWEEN ? AND ?
        GROUP BY c.cat_id, i.item_id 
        ORDER BY c.cat_name ASC, i.item_name ASC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $from, $to, $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

$grand_total = 0;
$current_category = '';
$category_total = 0;

while ($row = $result->fetch_assoc()) {
    $cat_id = $row['cat_id'];
    $cat_name = $row['cat_name'];
    $item_id = $row['item_id'];
    $item_name = $row['item_name'];

    // If new category, display category header and close previous category
    if ($current_category != $cat_name) {
        // Close previous category total if not first category
        if ($current_category != '') {
            echo "<tr>
                    <td colspan='6'><strong>Total for $current_category</strong></td>
                    <td><strong>" . number_format($category_total, 2) . "</strong></td>
                  </tr>";
        }
        
        // Start new category
        echo "<tr><td colspan='7'><strong>Category: $cat_name</strong></td></tr>";
        $current_category = $cat_name;
        $category_total = 0;
    }

    $in = getLastQty($item_id);
    $new = newStock($item_id);
    $out = out_qty($item_id);
    $end = end_qty($item_id);
    $unity_price = getItemPrice($item_id);
    $tot = $unity_price * $end;
    $category_total += $tot;
    $grand_total += $tot;

    echo "<tr>
            <td><a href='?resto=cumurative&item_id=$item_id'>$item_name</a></td>
            <td>" . number_format($in) . "</td>
            <td>" . number_format($new, 2) . "</td>
            <td>" . number_format($out, 2) . "</td>
            <td>" . number_format($end, 2) . "</td>
            <td>$unity_price</td>
            <td>" . number_format($tot, 2) . "</td>
          </tr>";
}

// Close the last category total
if ($current_category != '') {
    echo "<tr>
            <td colspan='6'><strong>Total for $current_category</strong></td>
            <td><strong>" . number_format($category_total, 2) . "</strong></td>
          </tr>";
}

// Show grand total
echo '<tr id="grand-total-row">
        <td colspan="6"><strong>Grand Total</strong></td>
        <td><strong>' . number_format($grand_total, 2) . '</strong></td>
      </tr>';

function getItemPrice($id){
    include '../inc/conn.php';	
    
    $item = "SELECT price FROM tbl_items WHERE item_id = ?";
    $stmt = $conn->prepare($item);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tbl_progress = $conn->prepare("SELECT new_price FROM tbl_progress WHERE item = ? ORDER BY prog_id DESC LIMIT 1");
    $tbl_progress->bind_param("i", $id);
    $tbl_progress->execute();
    $progress_result = $tbl_progress->get_result();
    
    if ($result->num_rows > 0 && $progress_result->num_rows > 0) {
        $item_row = $result->fetch_assoc();
        $progress_row = $progress_result->fetch_assoc();
        return $progress_row['new_price'] > 0 ? $progress_row['new_price'] : $item_row['price'];
    }
    return 0;
}

function getLastQty($id){
    include '../inc/conn.php';
    $sql = "SELECT last_qty FROM tbl_progress WHERE item = ? ORDER BY prog_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['last_qty'];
    }
    return 0;
}

function newStock($id){
    include '../inc/conn.php';
    $sql = "SELECT SUM(in_qty) as in_qty FROM tbl_progress WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['in_qty'];
    }
    return 0;
}

function out_qty($id){
    include '../inc/conn.php';
    $sql = "SELECT SUM(out_qty) as out_qty FROM tbl_progress WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['out_qty'];
    }
    return 0;
}

function end_qty($id){
    include '../inc/conn.php';
    $sql = "SELECT end_qty FROM tbl_progress WHERE item = ? ORDER BY prog_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return round((float)$row['end_qty'], 2);
    }
    return 0;
}
?>