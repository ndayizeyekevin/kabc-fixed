<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Try config.php first (production), then DBcontroller.php (local)
    if (file_exists('../inc/config.php')) {
        include '../inc/config.php';
    } elseif (file_exists('../inc/DBcontroller.php')) {
        include '../inc/DBcontroller.php';
    } else {
        throw new Exception('Database configuration file not found');
    }

    if (!isset($db)) {
        throw new Exception('Database connection not established');
    }

    // Reset all notify values to 0 for testing
    $sql = "UPDATE tbl_cmd_qty SET notify = 0 WHERE cmd_status = 13";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $affected = $stmt->rowCount();
    
    echo json_encode([
        'success' => true,
        'message' => 'Reset ' . $affected . ' records to notify = 0',
        'affected_rows' => $affected
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>

