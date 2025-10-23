<?php

include '../inc/conn.php'; // Assuming this provides a valid $conn MySQLi object



// 1. Prepare the SQL statement
// The '?' is a placeholder for the data we will bind later.
$stmt = $conn->prepare("DELETE FROM request_store_item WHERE id = ?");

// Check if the prepare statement was successful
if ($stmt === false) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}

// 2. Bind the user input (the 'id' from $_REQUEST)
// 's' specifies the variable type: 's' for string, 'i' for integer, etc.
// If 'id' is guaranteed to be an integer, use 'i'. Since it's often a string (even if numeric), 's' is safer if you're unsure.
$bind_success = $stmt->bind_param("s", $_REQUEST['id']);

if ($bind_success === false) {
    echo "Error binding parameters: " . $stmt->error;
    $stmt->close();
    exit;
}

// 3. Execute the statement
if ($stmt->execute()) {
    // Check if any rows were actually affected/deleted
    if ($stmt->affected_rows > 0) {
        // Use a secure way to access the ID for the redirect URL
        $redirect_id = urlencode($_REQUEST['req']);
        
        // Success: Redirect the user
        echo "<script>
        window.location.href='./?resto=manageRequest&id=$redirect_id';
        </script>";
    } else {
        // Success but no record found to delete
        echo "Record with ID '{$_REQUEST['id']}' not found.";
    }
} else {
    // Error executing the statement
    echo "Error deleting record: " . $stmt->error;
}

// 4. Close the statement
$stmt->close();

?>