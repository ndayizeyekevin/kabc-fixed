<?php
// Simple connection test file
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";
echo "<hr>";

echo "<h2>Step 1: Check which config file exists</h2>";
echo "Current directory: " . getcwd() . "<br>";

$config_file = null;
if (file_exists('../inc/config.php')) {
    $config_file = '../inc/config.php';
    echo "✓ File exists: $config_file (PRODUCTION)<br>";
} elseif (file_exists('../inc/DBcontroller.php')) {
    $config_file = '../inc/DBcontroller.php';
    echo "✓ File exists: $config_file (LOCAL)<br>";
} else {
    echo "✗ Neither config.php nor DBcontroller.php found in ../inc/<br>";
    echo "Files in ../inc/: <br>";
    if (is_dir('../inc/')) {
        $files = scandir('../inc/');
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "  - $file<br>";
            }
        }
    } else {
        echo "  Directory ../inc/ does not exist!<br>";
    }
    die();
}

echo "<h2>Step 2: Try to include $config_file</h2>";
try {
    include $config_file;
    echo "✓ File included successfully<br>";
} catch (Exception $e) {
    echo "✗ Error including file: " . $e->getMessage() . "<br>";
    die();
}

echo "<h2>Step 3: Check if \$db variable exists</h2>";
if (isset($db)) {
    echo "✓ \$db variable exists<br>";
    echo "Type: " . get_class($db) . "<br>";
} else {
    echo "✗ \$db variable NOT set<br>";
    die();
}

echo "<h2>Step 4: Test simple query</h2>";
try {
    $stmt = $db->query("SELECT 1 as test");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Query successful! Result: " . $result['test'] . "<br>";
} catch (Exception $e) {
    echo "✗ Query failed: " . $e->getMessage() . "<br>";
    die();
}

echo "<h2>Step 5: Check if tbl_cmd_qty table exists</h2>";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'tbl_cmd_qty'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Table tbl_cmd_qty exists<br>";
    } else {
        echo "✗ Table tbl_cmd_qty NOT found<br>";
    }
} catch (Exception $e) {
    echo "✗ Error checking table: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 6: Check if notify column exists</h2>";
try {
    $stmt = $db->query("SHOW COLUMNS FROM tbl_cmd_qty LIKE 'notify'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Column 'notify' exists in tbl_cmd_qty<br>";
        $col = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Column details: <pre>" . print_r($col, true) . "</pre>";
    } else {
        echo "✗ Column 'notify' NOT found in tbl_cmd_qty<br>";
        echo "<p style='color: red;'>You need to add the notify column! Run this SQL:</p>";
        echo "<pre>ALTER TABLE tbl_cmd_qty ADD COLUMN notify TINYINT(1) NOT NULL DEFAULT 0;</pre>";
    }
} catch (Exception $e) {
    echo "✗ Error checking column: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 7: Count records in tbl_cmd_qty</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM tbl_cmd_qty");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Total records in tbl_cmd_qty: <strong>" . $result['total'] . "</strong><br>";
} catch (Exception $e) {
    echo "✗ Error counting records: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 8: Count records with notify = 0</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM tbl_cmd_qty WHERE notify = 0");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Records with notify = 0: <strong>" . $result['total'] . "</strong><br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 9: Test the notification query</h2>";
try {
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
            ORDER BY tbl_cmd_qty.created_at DESC
            LIMIT 5";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✓ Query executed successfully!<br>";
    echo "Found <strong>" . count($orders) . "</strong> orders matching notification criteria<br>";
    
    if (count($orders) > 0) {
        echo "<h3>Sample orders:</h3>";
        echo "<pre>" . print_r($orders, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "✗ Query failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>✅ All tests completed!</h2>";
echo "<p><a href='test_notification.php'>Go to full test page</a></p>";
?>

