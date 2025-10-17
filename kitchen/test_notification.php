<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try to include the database connection
$db_connected = false;
$db_error = null;

// Try config.php first (production)
if (file_exists('../inc/config.php')) {
    try {
        include '../inc/config.php';
        if (isset($db)) {
            $db_connected = true;
        } else {
            $db_error = "config.php loaded but \$db variable not set";
        }
    } catch (Exception $e) {
        $db_error = "Error loading config.php: " . $e->getMessage();
    }
}
// Fallback to DBcontroller.php (local development)
elseif (file_exists('../inc/DBcontroller.php')) {
    try {
        include '../inc/DBcontroller.php';
        if (isset($db)) {
            $db_connected = true;
        } else {
            $db_error = "DBcontroller.php loaded but \$db variable not set";
        }
    } catch (Exception $e) {
        $db_error = "Error loading DBcontroller.php: " . $e->getMessage();
    }
} else {
    $db_error = "Neither config.php nor DBcontroller.php found in ../inc/";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kitchen Notification System - Test Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        h2 {
            color: #667eea;
            margin-top: 30px;
        }
        .test-section {
            background: #f9f9f9;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
            border-radius: 4px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .info {
            color: blue;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #667eea;
            color: white;
        }
        table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        .btn:hover {
            background: #5568d3;
        }
        #ajax-result {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            white-space: pre-wrap;
            font-family: monospace;
            max-height: 400px;
            overflow-y: auto;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-0 {
            background: #ff9800;
            color: white;
        }
        .status-1 {
            background: #4caf50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Kitchen Notification System - Test & Debug Page</h1>
        
        <div class="test-section">
            <h2>📊 Database Status Check</h2>

            <?php
            // First check if database is connected
            if (!$db_connected) {
                echo '<p class="error">✗ Database connection failed!</p>';
                echo '<p class="error">Error: ' . htmlspecialchars($db_error) . '</p>';
                echo '<p>Please check your database configuration in <code>inc/DBcontroller.php</code></p>';
            } else {
                echo '<p class="success">✓ Database connected successfully</p>';
            }

            if ($db_connected) {
            // Test 1: Check if notify column exists
            try {
                $checkColumn = $db->query("SHOW COLUMNS FROM tbl_cmd_qty LIKE 'notify'");
                $columnExists = $checkColumn->rowCount() > 0;
                
                if ($columnExists) {
                    echo '<p class="success">✓ Column "notify" exists in tbl_cmd_qty</p>';
                } else {
                    echo '<p class="error">✗ Column "notify" does NOT exist in tbl_cmd_qty</p>';
                    echo '<p>Run this SQL to add it: <code>ALTER TABLE tbl_cmd_qty ADD COLUMN notify TINYINT(1) NOT NULL DEFAULT 0;</code></p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">Error checking column: ' . $e->getMessage() . '</p>';
            }
            
            // Test 2: Count orders with notify = 0
            try {
                $stmt = $db->query("SELECT COUNT(*) as total FROM tbl_cmd_qty WHERE notify = 0");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<p class="info">📝 Total orders with notify = 0: <strong>' . $result['total'] . '</strong></p>';
            } catch (Exception $e) {
                echo '<p class="error">Error counting notify=0: ' . $e->getMessage() . '</p>';
            }
            
            // Test 3: Count orders with cmd_status = 13
            try {
                $stmt = $db->query("SELECT COUNT(*) as total FROM tbl_cmd_qty WHERE cmd_status = 13");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<p class="info">🍳 Total orders with cmd_status = 13: <strong>' . $result['total'] . '</strong></p>';
            } catch (Exception $e) {
                echo '<p class="error">Error counting status=13: ' . $e->getMessage() . '</p>';
            }
            
            // Test 4: Count orders matching all criteria
            try {
                $stmt = $db->query("SELECT COUNT(*) as total 
                                    FROM tbl_cmd_qty
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    INNER JOIN category ON menu.cat_id = category.cat_id
                                    WHERE tbl_cmd_qty.notify = 0 
                                    AND tbl_cmd_qty.cmd_status = 13
                                    AND category.cat_id != 2");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<p class="success">🎯 Orders matching ALL notification criteria: <strong>' . $result['total'] . '</strong></p>';
            } catch (Exception $e) {
                echo '<p class="error">Error with full query: ' . $e->getMessage() . '</p>';
            }
            } // End if ($db_connected)
            ?>
        </div>

        <div class="test-section">
            <h2>📋 Recent Orders (Last 20 with notify = 0)</h2>

            <?php
            if ($db_connected) {
            try {
                $sql = "SELECT 
                            tbl_cmd_qty.cmd_qty_id,
                            tbl_cmd_qty.cmd_table_id,
                            tbl_cmd_qty.cmd_status,
                            tbl_cmd_qty.notify,
                            tbl_cmd_qty.created_at,
                            menu.menu_name,
                            category.cat_name,
                            category.cat_id,
                            tbl_tables.table_no
                        FROM tbl_cmd_qty
                        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                        INNER JOIN category ON menu.cat_id = category.cat_id
                        INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                        WHERE tbl_cmd_qty.notify = 0
                        ORDER BY tbl_cmd_qty.created_at DESC
                        LIMIT 20";
                
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($orders) > 0) {
                    echo '<table>';
                    echo '<tr>
                            <th>ID</th>
                            <th>Table</th>
                            <th>Menu Item</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Notify</th>
                            <th>Created</th>
                            <th>Will Notify?</th>
                          </tr>';
                    
                    foreach ($orders as $order) {
                        $willNotify = ($order['cmd_status'] == 13 && $order['cat_id'] != 2) ? '✓ YES' : '✗ NO';
                        $willNotifyClass = ($order['cmd_status'] == 13 && $order['cat_id'] != 2) ? 'success' : 'error';
                        
                        echo '<tr>';
                        echo '<td>' . $order['cmd_qty_id'] . '</td>';
                        echo '<td>Table ' . $order['table_no'] . '</td>';
                        echo '<td>' . $order['menu_name'] . '</td>';
                        echo '<td>' . $order['cat_name'] . ' (ID: ' . $order['cat_id'] . ')</td>';
                        echo '<td>' . $order['cmd_status'] . '</td>';
                        echo '<td><span class="status-badge status-' . $order['notify'] . '">' . $order['notify'] . '</span></td>';
                        echo '<td>' . $order['created_at'] . '</td>';
                        echo '<td class="' . $willNotifyClass . '">' . $willNotify . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                } else {
                    echo '<p class="info">No orders found with notify = 0</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">Error fetching orders: ' . $e->getMessage() . '</p>';
            }
            } else {
                echo '<p class="error">Cannot display orders - database not connected</p>';
            }
            ?>
        </div>

        <div class="test-section">
            <h2>🧪 Test AJAX Endpoint</h2>
            <p>Click the button below to test the notification AJAX endpoint:</p>
            
            <button class="btn" onclick="testAjax()">Test check_orders_ajax.php</button>
            <button class="btn" onclick="testAjaxDebug()">Test Debug Version</button>
            <button class="btn" onclick="resetAllNotify()">Reset All notify to 0</button>
            
            <div id="ajax-result"></div>
        </div>
        
        <div class="test-section">
            <h2>🔍 Category Information</h2>

            <?php
            if ($db_connected) {
            try {
                $stmt = $db->query("SELECT cat_id, cat_name FROM category ORDER BY cat_id");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo '<table>';
                echo '<tr><th>Category ID</th><th>Category Name</th><th>Goes To</th></tr>';

                foreach ($categories as $cat) {
                    $goesTo = ($cat['cat_id'] == 2) ? 'Bar (Excluded)' : 'Kitchen';
                    echo '<tr>';
                    echo '<td>' . $cat['cat_id'] . '</td>';
                    echo '<td>' . $cat['cat_name'] . '</td>';
                    echo '<td>' . $goesTo . '</td>';
                    echo '</tr>';
                }

                echo '</table>';
            } catch (Exception $e) {
                echo '<p class="error">Error fetching categories: ' . $e->getMessage() . '</p>';
            }
            } else {
                echo '<p class="error">Cannot display categories - database not connected</p>';
            }
            ?>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function testAjax() {
            $('#ajax-result').html('Loading...');
            
            $.ajax({
                url: 'check_orders_ajax.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#ajax-result').html(JSON.stringify(response, null, 2));
                },
                error: function(xhr, status, error) {
                    $('#ajax-result').html('ERROR: ' + error + '\n\nResponse: ' + xhr.responseText);
                }
            });
        }
        
        function testAjaxDebug() {
            $('#ajax-result').html('Loading debug info...');
            
            $.ajax({
                url: 'check_orders_ajax_debug.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#ajax-result').html(JSON.stringify(response, null, 2));
                },
                error: function(xhr, status, error) {
                    $('#ajax-result').html('ERROR: ' + error + '\n\nResponse: ' + xhr.responseText);
                }
            });
        }
        
        function resetAllNotify() {
            if (confirm('This will reset ALL notify values to 0. Are you sure?')) {
                $('#ajax-result').html('Resetting...');
                
                $.ajax({
                    url: 'reset_notify.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#ajax-result').html(JSON.stringify(response, null, 2));
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        $('#ajax-result').html('ERROR: ' + error + '\n\nResponse: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>
</body>
</html>

