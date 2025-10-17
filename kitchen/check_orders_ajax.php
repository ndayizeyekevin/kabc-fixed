<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

try {
    // Try config.php first (production), then DBcontroller.php (local)
    if (file_exists('../inc/config.php')) {
        include '../inc/config.php';
    } elseif (file_exists('../inc/DBcontroller.php')) {
        include '../inc/DBcontroller.php';
    } else {
        throw new Exception('Database configuration file not found');
    }

    // Check if $db connection exists
    if (!isset($db)) {
        throw new Exception('Database connection not established');
    }

    // Use the existing $db connection

    // Query to get new order items from tbl_cmd_qty with notify = 0
    // Join with menu and category to exclude drinks (cat_id != 2)
    // Only get items with cmd_status = 13 (kitchen orders)
    $sql = "SELECT
                tbl_cmd_qty.cmd_qty_id,
                tbl_cmd_qty.cmd_table_id,
                tbl_cmd_qty.cmd_item,
                tbl_cmd_qty.cmd_qty,
                tbl_cmd_qty.cmd_code,
                tbl_cmd_qty.created_at,
                menu.menu_name,
                tbl_tables.table_no
            FROM tbl_cmd_qty
            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
            INNER JOIN category ON menu.cat_id = category.cat_id
            INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
            WHERE tbl_cmd_qty.notify = 0
            AND tbl_cmd_qty.cmd_status = 13
            AND category.cat_id != 2
            ORDER BY tbl_cmd_qty.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update notify to 1 for fetched order items to mark them as notified
    if (!empty($orders)) {
        $orderItemIds = array_column($orders, 'cmd_qty_id');
        $placeholders = implode(',', array_fill(0, count($orderItemIds), '?'));

        $updateSql = "UPDATE tbl_cmd_qty SET notify = 1 WHERE cmd_qty_id IN ($placeholders)";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute($orderItemIds);
    }

    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
