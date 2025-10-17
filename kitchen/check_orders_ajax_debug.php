<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$debug = [];

try {
    // Try config.php first (production), then DBcontroller.php (local)
    if (file_exists('../inc/config.php')) {
        include '../inc/config.php';
        $debug['config_file'] = 'config.php';
    } elseif (file_exists('../inc/DBcontroller.php')) {
        include '../inc/DBcontroller.php';
        $debug['config_file'] = 'DBcontroller.php';
    } else {
        throw new Exception('Database configuration file not found');
    }

    if (!isset($db)) {
        throw new Exception('Database connection not established');
    }

    // Use the existing $db connection
    $debug['connection'] = 'Connected successfully';
    
    // Query to get new order items from tbl_cmd_qty with notify = 0
    // Join with menu and category to exclude drinks (cat_id != 2)
    // Only get items with cmd_status = 13 (kitchen orders)
    $sql = "SELECT 
                tbl_cmd_qty.cmd_qty_id,
                tbl_cmd_qty.cmd_table_id,
                tbl_cmd_qty.cmd_item,
                tbl_cmd_qty.cmd_qty,
                tbl_cmd_qty.cmd_code,
                tbl_cmd_qty.cmd_status,
                tbl_cmd_qty.notify,
                tbl_cmd_qty.created_at,
                menu.menu_name,
                menu.cat_id as menu_cat_id,
                category.cat_id as category_cat_id,
                category.cat_name,
                tbl_tables.table_no
            FROM tbl_cmd_qty
            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
            INNER JOIN category ON menu.cat_id = category.cat_id
            INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
            WHERE tbl_cmd_qty.notify = 0 
            AND tbl_cmd_qty.cmd_status = 13
            AND category.cat_id != 2
            ORDER BY tbl_cmd_qty.created_at DESC";
    
    $debug['query'] = $sql;
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $debug['found_orders'] = count($orders);
    $debug['orders_data'] = $orders;
    
    // Also check total orders with notify = 0 (without filters)
    $sqlAll = "SELECT COUNT(*) as total FROM tbl_cmd_qty WHERE notify = 0";
    $stmtAll = $db->prepare($sqlAll);
    $stmtAll->execute();
    $totalNotify0 = $stmtAll->fetch(PDO::FETCH_ASSOC);
    $debug['total_notify_0'] = $totalNotify0['total'];
    
    // Check orders with cmd_status = 13
    $sqlStatus = "SELECT COUNT(*) as total FROM tbl_cmd_qty WHERE notify = 0 AND cmd_status = 13";
    $stmtStatus = $db->prepare($sqlStatus);
    $stmtStatus->execute();
    $totalStatus13 = $stmtStatus->fetch(PDO::FETCH_ASSOC);
    $debug['total_status_13'] = $totalStatus13['total'];
    
    // Check category filter
    $sqlCat = "SELECT COUNT(*) as total 
               FROM tbl_cmd_qty
               INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
               INNER JOIN category ON menu.cat_id = category.cat_id
               WHERE tbl_cmd_qty.notify = 0 
               AND tbl_cmd_qty.cmd_status = 13
               AND category.cat_id != 2";
    $stmtCat = $db->prepare($sqlCat);
    $stmtCat->execute();
    $totalCat = $stmtCat->fetch(PDO::FETCH_ASSOC);
    $debug['total_after_category_filter'] = $totalCat['total'];
    
    // Update notify to 1 for fetched orders to mark them as notified
    if (!empty($orders)) {
        $orderItemIds = array_column($orders, 'cmd_qty_id');
        $placeholders = implode(',', array_fill(0, count($orderItemIds), '?'));
        
        $updateSql = "UPDATE tbl_cmd_qty SET notify = 1 WHERE cmd_qty_id IN ($placeholders)";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute($orderItemIds);
        
        $debug['updated_ids'] = $orderItemIds;
        $debug['update_status'] = 'Updated ' . count($orderItemIds) . ' records';
    } else {
        $debug['update_status'] = 'No records to update';
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders),
        'debug' => $debug
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'debug' => $debug
    ], JSON_PRETTY_PRINT);
}
?>

