<?php
include_once '../inc/conn.php';
function getTotalPaidByMethod($code, $method) {
	global $conn;



	$sql = "SELECT * FROM payment_tracks where  order_code = '$code' AND method = '$method' ";
	$result = $conn->query($sql);
	$sale = 0;
	if ($result->num_rows > 0) {
		// output data of each row
		while ($row = $result->fetch_assoc()) {

			$sale = $sale + $row['amount'];
		}
	}


	return $sale;
}







function lastday() {
    global $conn;

    // Initialize $lastDate to a safe default value (like NULL or an epoch start date)
    // This ensures $lastDate is *always* defined.
    $lastDate = NULL; // Setting a default value prevents the warning

    $sql = "SELECT closed_at FROM days WHERE closed_at  IS NOT NULL ORDER BY id DESC LIMIT 1 ";
    // Select *only* the column you need for better performance.
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Since LIMIT 1 is used, we only need to fetch once
        $row = $result->fetch_assoc();
        $lastDate = date('Y-m-d', strtotime($row['closed_at']));
    }

    return $lastDate;
}



if (isset($_POST['open'])) {



	global $conn;


	$sql = "SELECT * FROM tbl_cmd_qty ORDER BY cmd_qty_id DESC LIMIT 1 ";
	$result = $conn->query($sql);
	$sale = 0;
	if ($result->num_rows > 0) {
		// output data of each row
		while ($row = $result->fetch_assoc()) {

			$lastid = $row['cmd_qty_id'];
		}
	}

	if (isset($_POST['approve'])) {


		$date = $_POST['opendate'];
		// 		$time = time();
		$from = lastday();
		// 		die(var_dump($from));
		$date = date('Y-m-d H:i:s', strtotime($date));
		$uid= $_SESSION['user_id'];
		$timestamp = date('Y-m-d H:i:s');

		$sql = "INSERT INTO `days` (`created_at`, `reviewed_by`, `reviewed_at`, `updated_at`, `opened_at`, `closed_at`, created_by) VALUES ('$date', NULL, NULL, NULL, '$timestamp', NULL, '$uid');";

		if ($conn->query($sql) === TRUE) {
			echo "<script>Day Successfully opened</script>";
			echo "<script>window.location.href='index?resto=detailedsmumary';</script>";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	} else {
		echo "<script>alert('Please comfirm data before closing the day')</script>";
		echo "<script>window.location.href='index?resto=detailedsmumary';</script>";
	}
}

// if (isset($_POST['close'])) {



// 	global $conn;
// 	if (isset($_POST['approve'])) {


// 		$q = "SELECT COUNT(tbl_cmd.id) as un_paid
// 		FROM tbl_cmd
// 		LEFT JOIN payment_tracks
// 		ON tbl_cmd.OrderCode = payment_tracks.order_code
// 		WHERE payment_tracks.order_code IS NULL;";

// 		$d = $conn->query($q); // execute query
// 		$row = $d->fetch_assoc(); // fetch result

// 		if ($row['un_paid'] > 0) {
// 			echo "<script>alert('There are {$row['un_paid']} orders that are not paid, make sure to checkout all commands');
// 					window.location.href = window.location.href;
// 			</script>";
// 			exit;
// 		}

// 		$checkOpenSql = "SELECT created_at 
//                          FROM days 
//                          WHERE closed_at IS NULL 
//                          ORDER BY created_at DESC 
//                          LIMIT 1";
        
//         $openResult = $conn->query($checkOpenSql);
        
//         if ($openResult->num_rows === 0) {
//             // Block closure if no open day is found
//             echo "<script>alert('Error: There is no active day found to close. Please open a new day first.')</script>";
//             echo "<script>window.location.href='index?resto=detailedsmumary';</script>";
//             exit;
//         }
		
// 			$date = $_POST['closedate'];

// 		$date = date('Y-m-d H:i:s', strtotime($date));
// 		$sql = "UPDATE days
// 		JOIN (
// 			SELECT id
// 			FROM days
// 			ORDER BY id DESC
// 			LIMIT 1
// 		) AS last_day ON days.id = last_day.id
// 		SET days.closed_at = '$date';";

// 		if ($conn->query($sql) === TRUE) {
// 			echo "<script>Day Successfull closed</script>";
// 		} else {
// 			echo "Error: " . $sql . "<br>" . $conn->error;
// 		}
// 	} else {
// 		echo "<script>alert('Please comfirm data before closing the day')</script>";
// 	}
// }
