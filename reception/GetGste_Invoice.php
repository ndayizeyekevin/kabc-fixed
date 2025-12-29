	<?php

    // 	ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    include '../inc/conn.php';
include_once '../inc/functions.php';



if (isset($_POST['addClientToOrder'])) {


    $clientinroom = $_POST['clientinroom'];
    $code = $_REQUEST['c'];

    $sql = "UPDATE `tbl_cmd` SET `room_client` = '$clientinroom ' WHERE  `OrderCode`='$code'";
    $didq = $db->prepare($sql);
    $didq->execute();

    // update tbl_cmd_qty and tbl_cmd status to 12 as well as tbl_tables to available 1
    $tbl_id = $_GET['resrv'];
    $db->prepare("UPDATE tbl_cmd_qty SET cmd_status = 12 WHERE cmd_code = :order_code")
       ->execute([':order_code' => $code]);
    $db->prepare("UPDATE tbl_tables SET status = 1 WHERE table_id = :tbl_id")
       ->execute([':tbl_id' => $tbl_id]);
       $db->prepare("UPDATE tbl_cmd SET status_id = 12 WHERE OrderCode = :order_code")
         ->execute([':order_code' => $code]);
    echo "<script>alert('Client added to order');</script>";
    // Refresh to avoid resubmission
    echo "<script>window.location.href='?resto=gstInvce&resrv=" . $_GET['resrv'] . "&c=" . $code . "';</script>";
}
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM payment_tracks WHERE id = $delete_id");
    //  echo "<script>alert('Payment deleted');</script>";
}



$editData = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM payment_tracks WHERE id = $edit_id");
    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
    }
}


if (isset($_POST['addClientsTocredit'])) {
    // 1. Prepare and sanitize input variables
    $order_code = $_REQUEST['c'];
    $amount = filter_input(INPUT_POST, 'creaditamount', FILTER_VALIDATE_FLOAT);
    $method = '02';
    $time = time(); // Unix timestamp
    $tbl_id = filter_input(INPUT_GET, 'resrv', FILTER_VALIDATE_INT);
    $client_id = filter_input(INPUT_POST, 'clientname', FILTER_VALIDATE_INT);
    $created_by = $_SESSION['user_id'] ?? 0;
    $currentDateTime = date('Y-m-d H:i:s');
    $zero_paid = 0.00; 

    // 2. Handle New Client Insertion (if necessary)
    if (empty($client_id)) {
        $new_fname = filter_input(INPUT_POST, 'new_fname', FILTER_SANITIZE_STRING);
        $new_phone = filter_input(INPUT_POST, 'new_phone', FILTER_SANITIZE_STRING);

        if (!empty($new_fname) && !empty($new_phone)) {
            $fname = $new_fname;
            $lname = filter_input(INPUT_POST, 'new_lname', FILTER_SANITIZE_STRING);
            $phone = $new_phone;
            $tin = filter_input(INPUT_POST, 'new_tin', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'new_email', FILTER_SANITIZE_EMAIL);
            
            try {
                // Insert new credit user using NAMED PARAMETERS
                $stmt = $db->prepare("INSERT INTO creadit_id (f_name, l_name, phone, tinnumber, created_by, created_at, email) 
                                     VALUES (:fname, :lname, :phone, :tin, :created_by, :time, :email)");
                
                $stmt->execute([
                    ':fname' => $fname, 
                    ':lname' => $lname, 
                    ':phone' => $phone, 
                    ':tin' => $tin, 
                    ':created_by' => $created_by, 
                    ':time' => $time, 
                    ':email' => $email
                ]);
                
                $client_id = $db->lastInsertId();

            } catch (PDOException $e) {
                error_log("Client insertion error: " . $e->getMessage());
                $client_id = null;
            }
        }
    }

    // 3. Process Credit Transaction
    if (!empty($client_id) && $amount !== false && $amount > 0) {
        
        try {
            // START TRANSACTION
            $db->beginTransaction();
            
            // A. Update order (tbl_cmd) with credit user
            $db->prepare("UPDATE tbl_cmd SET creadit_user = :client_id WHERE OrderCode = :order_code")
               ->execute([':client_id' => $client_id, ':order_code' => $order_code]);

            // B. Record payment as credit in payment_tracks
            $stmt = $db->prepare("INSERT INTO payment_tracks (amount, method, order_code, service, created_at, remark)
                                 VALUES (:amount, :method, :order_code, 'resto', :time, 'creadit')");
            $stmt->execute([
                ':amount' => $amount, 
                ':method' => $method, 
                ':order_code' => $order_code, 
                ':time' => $time
            ]);

            // C. Insert into client_billings
            $stmt = $db->prepare("INSERT INTO client_billings (client_id, cmd_code, total_debt, amount_paid, balance_due, created_at) 
                                 VALUES (:client_id, :order_code, :total_debt, :amount_paid, :balance_due, :currentDateTime)");
            $stmt->execute([
                ':client_id' => $client_id, 
                ':order_code' => $order_code, 
                ':total_debt' => $amount,      
                ':amount_paid' => $zero_paid,   
                ':balance_due' => $amount,      
                ':currentDateTime' => $currentDateTime
            ]);

            // D. Update tbl_cmd and tbl_cmd_qty status to 12
            $db->prepare("UPDATE tbl_cmd SET status_id = 12 WHERE OrderCode = :order_code")
               ->execute([':order_code' => $order_code]);

            $db->prepare("UPDATE tbl_cmd_qty SET cmd_status = 12 WHERE cmd_code = :order_code")
               ->execute([':order_code' => $order_code]);

            // E. Update table status to available (1)
            if ($tbl_id !== false && $tbl_id !== null) { 
                $db->prepare("UPDATE tbl_tables SET status = 1 WHERE table_id = :tbl_id")
                   ->execute([':tbl_id' => $tbl_id]);
            }

            // COMMIT TRANSACTION
            $db->commit();
            
            // Success and Redirect
            echo "<script>alert('Credit successfully added');</script>";
            $redirect_url = "?resto=gstInvce&resrv=" . urlencode($_GET['resrv']) . "&c=" . urlencode($order_code);
            echo "<script>window.location.href='./index?resto=OurGste';</script>";

        } catch (PDOException $e) {
            // ROLLBACK on failure
            $db->rollBack();
            error_log("Credit transaction error: " . $e->getMessage());
            echo "<script>alert('Error saving credit: Please check logs or contact support.');</script>";
        }
    } else {
        echo "<script>alert('No client selected/added or amount is invalid.');</script>";
    }
}


if (isset($_POST['addClientsTocreditss'])) {



    $c =  $_POST['clientname'];

    $code = $_REQUEST['c'];

    $sql2 = "UPDATE `tbl_cmd` SET `creadit_user` = '$c' WHERE `OrderCode` = '$code'";
    $didq2 = $db->prepare($sql2);
    $didq2->execute();



    $amount =  mysqli_real_escape_string($conn, $_POST['creaditamount']);
    $amount = htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
    $amount = stripslashes($amount);


    $method = '02';







    $order_code = $_REQUEST['c'];


    $time = time();
    $sql = "INSERT INTO `payment_tracks` (`id`, `amount`, `method`, `order_code`, `service`, `created_at`,remark) VALUES (NULL, '$amount', '$method', '$order_code', 'resto','$time','creadit');";

    if ($conn->query($sql) === true) {
        echo "<script>alert('Credit successfull Added')</script>";
        // Refresh to avoid resubmission
        echo "<script>window.location.href='?resto=gstInvce&resrv=" . $_GET['resrv'] . "&c=" . $order_code . "';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}





if (isset($_POST['addpayment'])) {
    $amounts = $_POST['amount'];
    $methods = $_POST['method'];
    $remarks = $_POST['remark'];
    $order_code = $_GET['c'];
    $time = time();
    $totalPaid = array_sum(array_map('floatval', $amounts));



    for ($i = 0; $i < count($amounts); $i++) {
        $amount = mysqli_real_escape_string($conn, $amounts[$i]);
        $method = mysqli_real_escape_string($conn, $methods[$i]);
        $remark = mysqli_real_escape_string($conn, $remarks[$i]);

        $conn->query("INSERT INTO payment_tracks (amount, method, order_code, service, created_at, remark)
                      VALUES ('$amount', '$method', '$order_code', 'resto', '$time', '$remark')");
    }
    echo "<script>alert('Payment added');</script>";
    // echo "<script>alert('Payments added'); location.href='?c=$order_code';</script>";
    // Refresh to avoid resubmission
    echo "<script>window.location.href='?resto=gstInvce&resrv=" . $_GET['resrv'] . "&c=" . $order_code . "';</script>";
}

if (isset($_POST['updatepayment'])) {
    $edit_id = intval($_POST['edit_id']);
    $amounts =  $_POST['amount'];
    $methods =  $_POST['method'];
    $remarks =  $_POST['remark'];


    for ($i = 0; $i < count($amounts); $i++) {
        $amount = mysqli_real_escape_string($conn, $amounts[$i]);
        $method = mysqli_real_escape_string($conn, $methods[$i]);
        $remark = mysqli_real_escape_string($conn, $remarks[$i]);

        $sql = "UPDATE payment_tracks SET amount='$amount', method='$method', remark='$remark' WHERE id=$edit_id";
        if ($conn->query($sql)) {
            echo "<script>alert('Payment updated');</script>";
            // Refresh to avoid resubmission
            echo "<script>window.location.href='?resto=gstInvce&resrv=" . $_GET['resrv'] . "&c=" . $order_code . "';</script>";
        } else {
            echo "<script>alert('Error updating');</script>";
        }

    }


}






$client =   getClientOrder();


function getRoomClientDetails($id)
{

    include '../inc/conn.php';


    $sql = "SELECT * FROM `tbl_acc_booking` WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            return $client  =   getGuestNames($row['guest_id']) . "  <br> <br> ROOM " . getRoomName(getBookedRoom($row['id']));
        }
    } else {
        return "";
    }
}
function getClientOrder()
{
    include '../inc/conn.php';

    $sql = "SELECT * FROM `tbl_cmd` WHERE OrderCode='" . $_REQUEST['c'] . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            return $client  = getRoomClientDetails($row["room_client"]);
        }
    } else {
        return "";
    }
}



if (isset($_GET['resrv'])) {
    $code = $_GET['c'];
    $stmt = $conn->prepare("
    SELECT * FROM tbl_cmd
    INNER JOIN tbl_tables ON tbl_cmd.reservat_id = tbl_tables.table_id
    INNER JOIN tbl_users ON tbl_users.user_id = tbl_cmd.Serv_id
    WHERE tbl_cmd.OrderCode = ?
");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $get = $result->fetch_assoc();
    /* die(var_dump($get)); */

    if ($get) {
        $serv = $get['f_name'];
        $discount = $get['discount'];
    } else {
        die("No matching order found." . $stmt->error);
    }
}
$pay_no = time();

if (isset($_POST['savesales'])) {
    $todaysdate = date("Y-m-d H:i:s");
    $discount_amount = $_POST['discount_amount'];
    $price_amount = $_POST['price_amount'];
    $tot_amount = $_POST['tot_amount'];
    $waiter = $_POST['waiter'];
    $table = $_POST['table'];
    $invoice_no = $_POST['invoice_no'];
    $order_code = $_GET['c'];

    $names = $_POST['clientName'];



    $lastSale = getLastId() + 3;
    $ref = 0;
    $tin = empty($_POST['clientTin']) ? "null" : $_POST['clientTin'];
    $phone = empty($_POST['clientPhone']) ? "null" : $_POST['clientPhone'];
    $purchase_code = empty($_POST['purchasecode']) ? "null" : '"' . $_POST['purchasecode'] . '"';
    $client_name  = $_POST['clientName'];
    $salestype = "N";
    $rectype = "S";
    $totalitem = $_POST['totalitem'];
    $cfmDt = date("YmdHis");
    $salesDt = date("Ymd");

    $taxblAmtA = $_POST['taxblAmtA'];
    $taxblAmtB = $_POST['taxblAmtB'];
    $taxblAmtC = $_POST['taxblAmtC'];
    $taxblAmtD = $_POST['taxblAmtD'];
    $taxBamount = $_POST['taxBamount'];
    $taxB = $_POST['totTaxB'];
    $taxA = $_POST['totTaxA'];
    $pmtTyCd = $_POST['pmtTyCd'];
    $productList = $_POST['list'];
    $prdct =  '[' . substr($productList, 0, -1) . ']';
    if (is_numeric($tin)) {
        $tin = "$tin";
    } else {
        $tin = "null";
    }
    $receipt = '{"custTin": ' . $tin . ',"custMblNo":"' . $phone . '","rptNo":2,"trdeNm":"","adrs":"KN 32 St","topMsg":"CENTRE SAINT PAUL KIGALI LTD\nKN 32 St, Kigali, Rwanda\nTin: ' . $branch_tin . '\nPhone: ' . $branch_phone . '","btmMsg":"CIS Version 1 Powered by RRA VSDC EBM2.1 \n -------------------------------- \n Welcome","prchrAcptcYn":"N"}';
    $json = formatingJson($ref, $pmtTyCd, $taxblAmtA, $taxblAmtB, $lastSale, $tin, $purchase_code, $client_name, $salestype, $rectype, $totalitem, $taxBamount, $tot_amount, $receipt, $prdct, $taxblAmtC, $taxblAmtD, $cfmDt, $salesDt);


    // echo $json;

    // echo $url . 'trnsSales/saveSales';
    // die($json);
$json = is_string($json) ? $json : json_encode($json);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url . '/trnsSales/saveSales',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ],
]);

$response = curl_exec($curl);

if ($response === false) {
    error_log('cURL error: ' . curl_error($curl));
}
$info = curl_getinfo($curl);
curl_close($curl);

// Inspect $info['http_code'] and $response for server message
    //   echo $response;

// die(var_dump($response));

    $data = json_decode($response);

    $responseData = json_decode($response, true);

    if (isset($responseData['resultCd'])) {
        $code = $data->resultCd;
        if ($code == '000') {


            $res = json_encode($data->data);
            $res = json_decode($res);

            $rcptNo =  $res->rcptNo;
            $totRcptNo =  $res->totRcptNo;
            $vsdcRcptPbctDate =  $res->vsdcRcptPbctDate;
            $sdcId =  $res->sdcId;
            $mrcNo =  $res->mrcNo;
            $int =  $res->intrlData;
            $rcptSign = $res->rcptSign;
            $amount = $tot_amount;

  $receipt = '{"custTin": '.$tin.',
    "custMblNo":"'.$phone.'","rptNo":2,"trdeNm":"","adrs":"KN 4 Ave",
    "topMsg":"CENTRE SAINT PAUL LTD\nKG 13 Avenue 22, Kigali,   Rwanda\nTin: '.$branch_tin.'\nPhone: '.$branch_phone.'",
    "btmMsg":"CIS Version 1 Powered by RRA VSDC EBM2.1 \n -------------------------------- \n Welcome",
    "prchrAcptcYn":"N", "intrlData": "' . $int . '",
    "rcptSign": "' . $rcptSign . '",
    "rcptPbctDt": "' . $vsdcRcptPbctDate . '","curRcptNo": "' . $rcptNo . '",
    "totRcptNo": ' . $totRcptNo . '}';

			$json = formatingJson($ref, $pmtTyCd, $taxblAmtA, $taxblAmtB, $lastSale, $tin, $purchase_code, $client_name, $salestype, $rectype, $totalitem, $taxBamount, $tot_amount, $receipt, $prdct, $taxblAmtC, $taxblAmtD, $cfmDt, $salesDt);
    try {
      $inf = $db->prepare("INSERT INTO `json_receipts` (`saleId`, `json`, `r_id`) VALUES (NULL, :json, :rid)");

      // Example values

      $inf->execute([
        ':json' => $json,
        ":rid" => $lastSale
      ]);
    } catch (Exception $e) {

      die(var_dump($e));
    }

            // 			// data to save as result from rra with response

            if ($salestype == 'N') {

                $jsonMaster = array();
                $jsonIO = array();

                foreach (json_decode($prdct) as $object) {
                    $itemClass = getItemClass($object->itemCd);
                    if ($itemClass != 3) {
                        $qty = getStockValue($object->itemCd) - $object->qty;

                        $jsonMaster[] = '{"tin":"' . $branch_tin . '",
				"bhfId":"00",
				"itemCd": "' . $object->itemCd . '",
				"rsdQty":"' . $qty . '",
				"regrId":"01",
				"regrNm":"Admin",
				"modrNm":"Admin",
				"modrId":"01"
			  }';

                        $type_id = getItemId($object->itemCd);
                        $sql_upd = $db->prepare("UPDATE stock SET `quantities` = $qty WHERE `type`='" . $type_id . "'");
                        $sql_upd->execute();
                    }
                }

                // sendStockMaster($jsonMaster);

                $prdct = json_decode($prdct, true);
                foreach ($prdct as &$item) {
                    $item['totDcAmt'] = $item['dcAmt'];
                }

                $prdct = json_encode($prdct);


                $invoice = countIo();
                $jsonIO[] = '{"tin":"' . $branch_tin . '",
                "bhfId":"00",
                "sarNo":"' . $invoice . '",
                "orgSarNo":"' . $invoice . '",
                "regTyCd":"M",
                "custTin":null,
                "custNm":null,
                "custBhfId":null,
                "sarTyCd":"11",
                "ocrnDt":"' . date("Ymd") . '",
                "totItemCnt":"' . $totalitem . '",
                "totTaxblAmt":"' . $tot_amount . '",
                "totTaxAmt":"' . number_format($taxBamount, 2, '.', '') . '",
                "totAmt":"' . $tot_amount . '",
                "remark":"",
                "regrId":"01",
                "regrNm":"Admin",
                "modrNm":"Admin",
                "modrId":"01",
                "itemList":' . $prdct . '
                }';
                // echo "Type: " . gettype($prdct);
                //    print_r($jsonIO);


                // sendStockIO($jsonIO);
            }

            // --- Start PDO Transaction Block ---
            try {
                $db->beginTransaction();

                // 1. INSERT INTO tbl_vsdc_sales
                $receipt_info = addslashes('{"custTin":' . $tin . ',"custMblNo":null,"rptNo":1,"trdeNm":"saint paul","adrs":"KN 32 St","topMsg":"CENTRE SAINT PAUL KIGALI LTD\nKN 32 St, Kigali, Rwanda\nTin: ' . $branch_tin . '\nPhone: ' . $branch_phone . '","btmMsg":"CIS Version 1 Powered by RRA VSDC EBM2.1 \n -------------------------------- \n Welcome","prchrAcptcYn":"N"}');
                $sql_inf = $db->prepare("INSERT INTO tbl_vsdc_sales SET
                    tin='$branch_tin',
                    bhfId='00',
                    invcNo='$lastSale',
                    orgInvcNo=0,
                    custTin='$tin',
                    custPhone='$phone',
                    prcOrdCd='$purchase_code',
                    custNm='$client_name',
                    salesTyCd='$salestype',
                    rcptTyCd='$rectype',
                    pmtTyCd='$pmtTyCd',
                    salesSttsCd='02',
                    cfmDt='$cfmDt',
                    salesDt='$salesDt',
                    stockRlsDt='" . date("YmdHis") . "',
                    totItemCnt='$totalitem',
                    taxblAmtA='$taxblAmtA',
                    taxblAmtB='$taxblAmtB',
                    taxblAmtC='$taxblAmtC',
                    taxblAmtD='$taxblAmtD',
                    taxRtA=0,
                    taxRtB=0,
                    taxRtC=0,
                    taxRtD=0,
                    taxAmtA=0,
                    taxAmtB='".number_format($taxBamount, 2, '.', '')."',
                    taxAmtC=0,
                    taxAmtD=0,
                    totTaxblAmt='$tot_amount',
                    totTaxAmt='".number_format($taxBamount, 2, '.', '')."',
                    totAmt='$tot_amount',
                    prchrAcptcYn='N',
                    regrId='01',
                    regrNm='admin',
                    modrId='01',
                    modrNm='admin',
                    receipt= '$receipt_info',
                    itemList='$prdct',
                    rcptNo='$rcptNo'
                    ");
                $sql_inf->execute();


                $tin = $tin ?? '9999999999';

                // 2. INSERT INTO tbl_receipts
                $receipt_info = addslashes('{"custTin":"$tin","custMblNo":"0789300811","rptNo":1,"trdeNm":"saint paul","adrs":"KN 4 Ave","topMsg":"CENTRE SAINT PAUL KIGALI LTD\nKN 32 St, Kigali, Rwanda\nTin: ' . $branch_tin . '\nPhone: ' . $branch_phone . '","btmMsg":"CIS Version 1 Powered by RRA VSDC EBM2.1 \n -------------------------------- \n Welcome","prchrAcptcYn":"N"}');
                $sql_inf = $db->prepare("INSERT INTO tbl_receipts SET
                    tin='$branch_tin',
                    bhfId='00',
                    invcNo='$lastSale',
                    orgInvcNo=0,
                    prcOrdCd='$purchase_code',
                    custNm='$client_name',
                    salesTyCd='$salestype',
                    rcptTyCd='$rectype',
                    pmtTyCd='$pmtTyCd',
                    salesSttsCd='02',
                    cfmDt='$cfmDt',
                    salesDt='$salesDt',
                    stockRlsDt='" . date("YmdHis") . "',

                    totItemCnt='$totalitem',
                    taxblAmtA='$taxblAmtA',
                    taxblAmtB='$taxblAmtB',
                    taxblAmtC='$taxblAmtC',
                    taxblAmtD='$taxblAmtD',
                    taxRtA=0,
                    taxRtB=0,
                    taxRtC=0,
                    taxRtD=0,
                    taxAmtA=0,
                    taxAmtB='$taxBamount',
                    taxAmtC=0,
                    taxAmtD=0,
                    totTaxblAmt=0,
                    totTaxAmt='$taxBamount',
                    totAmt='$tot_amount',
                    prchrAcptcYn='N',
                    regrId='01',
                    regrNm='admin',
                    modrId='01',
                    modrNm='admin',
                    receipt= '$receipt_info',
                    itemList='$prdct',
                    rcptNo='$rcptNo'

                    ");
                $sql_inf->execute();

                // 3. INSERT INTO tbl_payment
                $insert = $db->prepare("INSERT INTO `tbl_payment`(`table_id`, `amount`, `discount`, `tot_amount`, `user`, `due_date`)
                VALUES ('$table','$price_amount','$discount_amount','$tot_amount','$waiter','$todaysdate')");
                $insert->execute();

                // 4. UPDATE tbl_cmd
                $sql = "UPDATE `tbl_cmd` SET `status_id` = '12' WHERE `OrderCode` = :order_code";
                $didq = $db->prepare($sql);
                $didq->execute([':order_code' => $order_code]);

                // 5. UPDATE tbl_cmd_qty
                $sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_status` = '12' WHERE `cmd_code` = :order_code";
                $didq2 = $db->prepare($sql2);
                $didq2->execute([':order_code' => $order_code]);

                // 6. UPDATE tbl_tables
                $updateTable = $db->prepare("UPDATE tbl_tables SET status = 1 WHERE table_id = :table_id");
                $updateTable->execute([':table_id' => $table]);

                // Commit the transaction only if all queries above succeeded
                $db->commit();
            } catch (Exception $e) {
                // Rollback the transaction if any query failed
                if ($db->inTransaction()) {
                    $db->rollback();
                }
                // You should handle the error here (e.g., logging or showing a user alert)
                error_log("Transaction failed: " . $e->getMessage());
                // Example alert:
                // echo "<script>alert('A critical database error occurred. Changes were rolled back.');</script>";
            }
            // --- End PDO Transaction Block ---

            if ($pmtTyCd == '01') {
                $pmtTyCd = 'CASH';
            }
            if ($pmtTyCd == '02') {
                $pmtTyCd = 'CREDIT';
            }
            if ($pmtTyCd == '00') {
                $pmtTyCd = 'CASH/CREDIT ';
            }
            if ($pmtTyCd == '04') {
                $pmtTyCd = 'BANK CHECK';
            }
            if ($pmtTyCd == '05') {
                $pmtTyCd = 'DEBIT&CREDIT CARD';
            }
            if ($pmtTyCd == '06') {
                $pmtTyCd = 'MOBILE MONEY';
            }
            if ($pmtTyCd == '07') {
                $pmtTyCd = 'OTHER PAYMENTS';
            }


            $receitType = $salestype . $rectype;
            $lin = 'receiptT=' . $receitType . '&taxbleAmount=' . $taxblAmtB . '&taxA=' . $taxA . '&taxB=' . $taxB . '&ref=' . $ref . '&no=' . $rcptNo . '&total='
                . $totRcptNo . '&vs=' . $vsdcRcptPbctDate . '&sdc=' . $sdcId . '&mrc=' . $mrcNo . '&amount=' . $amount . '&product=' . $prdct . '&int=' . $int . '&sign=' . $rcptSign . '&tin=' . $tin . '&tax=' . $taxBamount;
            $lin .= '&salestype=' . $salestype . '&rectype=' . $rectype . '&receiptNo=' . $lastSale;
            $lin .= '&totalC=' . $taxblAmtC . '&totalD=' . $taxblAmtD;
            $lin .= '&names=' . $client_name . '&clMob=' . $phone;
            $lin .= '&dateData=' . $cfmDt;
            $lin .= '&pmtTyCd=' . $pmtTyCd;


		$stmts = $db->prepare("INSERT INTO `receipts` (`receipt_url`, `agro`)
		VALUES ('$lin', '$client_name')");
	   $stmts->execute();

	   $lastInsertId = $db->lastInsertId();




            $sqlseason = $db->prepare("SELECT * FROM system_configuration");
            $sqlseason->execute();
            $rows = $sqlseason->fetch();

            $printer = $rows['printer'];
            if ($printer == 'paper_roll') {
                if ($receitType == 'NS') {
                    echo "<script>window.open('receipt/normal.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'NR') {
                    echo "<script>window.open('receipt/normal-refund.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'CS') {
                    echo "<script>window.open('receipt/copy.php?$lin', '_blank')</script>";
                }
                if ($receitType == 'CR') {
                    echo "<script>window.open('receipt/copy-refund.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'TS') {
                    echo "<script>window.open('receipt/training.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'TR') {
                    echo "<script>window.open('receipt/training-refund.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'PS') {
                    echo "<script>window.open('receipt/proforma.php?$lin', '_blank')</script>";
                }
            } else {
                if ($receitType == 'NS') {
                    echo "<script>window.open('receipt/a4/normal.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'NR') {
                    echo "<script>window.open('receipt/a4/normal-refund.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'CS') {
                    echo "<script>window.open('receipt/a4/copy.php?$lin', '_blank')</script>";
                }
                if ($receitType == 'CR') {
                    echo "<script>window.open('receipt/a4/copy-refund.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'TS') {
                    echo "<script>window.open('receipt/a4/training.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'TR') {
                    echo "<script>window.open('receipt/a4/training-refund.php?$lin', '_blank')</script>";
                }

                if ($receitType == 'PS') {
                    echo "<script>window.open('receipt/a4/proforma.php?$lin', '_blank')</script>";
                }
            }

            // 		file_put_contents('receipts.txt', $lin."\n", FILE_APPEND);

            // $insert = $db->prepare("INSERT INTO `tbl_payment`(`table_id`, `amount`, `discount`, `tot_amount`, `user`, `due_date`)
		    // VALUES ('$table','$price_amount','$discount_amount','$tot_amount','$waiter','$todaysdate')");
            // $insert->execute();

            // $sql = "UPDATE `tbl_cmd` SET `status_id` = '12' WHERE `OrderCode` = '" . $_REQUEST['c'] . "'";
            // $didq = $db->prepare($sql);
            // $didq->execute();

            // $sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_status` = '12' WHERE `cmd_code` = '" . $_REQUEST['c'] . "'";
            // $didq2 = $db->prepare($sql2);
            // $didq2->execute();

            // // Update table status to available 1 using execute parameterized query
            // $updateTable = $db->prepare("UPDATE tbl_tables SET status = 1 WHERE table_id = :table_id");
            // $updateTable->execute([':table_id' => $table]);


            $msge = $data->resultMsg;
            // echo '<meta http-equiv="refresh"' . 'content="1;URL=?resto=OurGste">';
            // redirect using window.location.href
            echo "<script>window.location.href='?resto=OurGste';</script>";
            
        } else {
            $msge = $data->resultMsg;
            // echo '<meta http-equiv="refresh"'.'content="1;URL=?resto=OurGste">';
        }
    } else {
        echo "<script>alert('Lost connection to vsdc')</script>";
    }
}



if (isset($_POST['add'])) {
    $disc = $_POST['discount'];
    $item = $_POST['item'];
    $code = $_POST['codes'];

    // if($disc < $total){
    $sql = "UPDATE `tbl_cmd_qty` SET `discount` = '$disc' WHERE `cmd_item` = '" . $item . "' AND `cmd_code`='$code'";
    $didq = $db->prepare($sql);
    $didq->execute();
    $msg = "Discount Added successfully" . $disc;
    echo '<meta http-equiv="refresh"' . 'content="1;URL=?resto=gstInvce&resrv=' . $_GET['resrv'] . '&c=' . $code . '">';
    // }
    //     else{
    // 	$msge="Discount is higher than total price";
    //     echo '<meta http-equiv="refresh"'.'content="1;URL=?resto=gstInvce&resrv='.$table_id.'&c='.$code.'">';
    // }
}


if (isset($_POST['addclient'])) {
    $tin = $_POST['tin'];
    $code = $_POST['code'];
    $tables = $_POST['tables'];

    $sql = "UPDATE `tbl_cmd` SET `client` = '$tin' WHERE `reservat_id` = '" . $tables . "' AND `OrderCode`='$code'";
    $didq = $db->prepare($sql);
    $didq->execute();
    $msg = "Tin No Added successfully";
    echo '<meta http-equiv="refresh"' . 'content="1;URL=?resto=gstInvce&resrv=' . $tables . '&c=' . $code . '">';
}



if (isset($_GET['rmv'])) {
    $code = $_GET['c'];
    $id = $_GET['rmv'];
    $tables = $_GET['resrv'];

    $sqldel = $db->prepare("DELETE FROM tbl_sales_payment WHERE payid = '$id'");
    $sqldel->execute();

    $msge = "Payment removed successfully";
    echo '<meta http-equiv="refresh"' . 'content="1;URL=?resto=gstInvce&resrv=' . $tables . '&c=' . $code . '">';
}








$sqltin = $db->prepare("SELECT client ,creadit_user  FROM tbl_cmd WHERE OrderCode = '" . $_GET['c'] . "'");
$sqltin->execute();
$row = $sqltin->fetch();
$count = $row['client'];



$credit = $row['creadit_user'];







if (isset($_POST['addclient'])) {
    $tin = $_POST['tin'];
    $code = $_POST['code'];
    $tables = $_POST['tables'];

    $sql = "UPDATE `tbl_cmd` SET `client` = '$tin' WHERE `reservat_id` = '" . $tables . "' AND `OrderCode`='$code'";
    $didq = $db->prepare($sql);
    $didq->execute();
    $msg = "Tin No Added successfully";
    echo '<meta http-equiv="refresh"' . 'content="1;URL=?resto=gstInvce&resrv=' . $tables . '&c=' . $code . '">';
}





if (isset($_POST['addpayments'])) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include '../inc/conn.php';
    $amount =  mysqli_real_escape_string($conn, $_POST['amount']);
    $amount = htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
    $amount = stripslashes($amount);


    $method =  mysqli_real_escape_string($conn, $_POST['method']);
    $method = htmlspecialchars($method, ENT_QUOTES, 'UTF-8');
    $method = stripslashes($method);


    $remark =  mysqli_real_escape_string($conn, $_POST['remark']);
    $remark = htmlspecialchars($remark, ENT_QUOTES, 'UTF-8');
    $remark = stripslashes($remark);




    $order_code = $_REQUEST['c'];


    $time = time();
    $sql = "INSERT INTO `payment_tracks` (`id`, `amount`, `method`, `order_code`, `service`, `created_at`,remark) VALUES (NULL, '$amount', '$method', '$order_code', 'resto','$time','$remark');";

    if ($conn->query($sql) === true) {
        echo "<script>alert('Payment Added')</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$paidAmount = 0;
$sql = $db->prepare("SELECT * FROM payment_tracks  WHERE order_code = '" . $_GET['c'] . "' ");
$sql->execute();
if ($sql->rowCount() > 0) {
    while ($fetch = $sql->fetch()) {

        $paidAmount = $paidAmount + $fetch['amount'];

        $paymentmethod = $fetch['method'];
    }
}




$sql = $db->prepare("SELECT * FROM tbl_cmd  WHERE OrderCode = '" . $_GET['c'] . "' ");
$sql->execute();
if ($sql->rowCount() > 0) {
    while ($fetch = $sql->fetch()) {

        $paymentstatus = $fetch['status_id'];
    }
}











?>
	<?php
function fill_product($db)
{
    $output = '';

    $select = $db->prepare("SELECT * FROM `paymentmethod`");
    $select->execute();
    $result = $select->fetchAll();

    foreach ($result as $row) {
        $output .= '<option value="' . $row['payment_id'] . '">' . $row["payment_name"] . '</option>';
    }

    return $output;
}
?>
	<!-- Data Table area Start-->
	<div class="data-table-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<?php if ($msge) { ?>

						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<strong>Success!</strong> <?php echo htmlentities($msge); ?>
						</div>
					<?php } ?>
					<div class="data-table-list">
						<div class="invoice" id="print">

							<style type="text/css">
								body {
									font-family: Verdana;
								}

								div.invoice {
									border: 1px solid #ccc;
									padding: 10px;
									height: auto;
									width: 90%;
									margin-left: 5%;
								}

								@media screen and (max-width:600px) {
									div.invoice {
										border: 1px solid #ccc;
										padding: 10px;
										height: 740pt;
										width: 90%;
										margin-left: 1%;
									}
								}

								div.company-address {
									float: left;
									width: 200pt;
								}

								div.invoice-details {
									float: right;
									width: 200pt;
								}

								div.customer-address {
									float: right;
									margin-bottom: 50px;
									margin-top: 100px;
									width: 200pt;
								}

								div.clear-fix {
									clear: both;
									float: none;
								}

								table {
									width: 100%;
								}

								th {
									text-align: left;
								}

								td {}

								.text-left {
									text-align: left;
								}

								.text-center {
									text-align: center;
								}

								.text-right-invoice {
									text-align: right;
								}
							</style>
							<div class="clear-fix"></div>
							<center><u>
									<h3>INVOICE</h3>
								</u></center>
							<br>
							<div class="clear-fix"></div>
							<table class="table table-striped" style="font-size:11px;">
								<thead>
									<tr>
										<th>#</th>
										<th>Item</th>
										<th>Unit Price</th>
										<th>Discount</th>
										<th>Total Price</th>
										<th>Date</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
                                $totprice = 0;
                                $sql_rooms = $db->prepare("SELECT *,SUM(cmd_qty) AS qty FROM `tbl_cmd_qty`
                                    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_code = '" . $_GET['c'] . "'
                                    GROUP BY cmd_item
                                    ");
                                $sql_rooms->execute();
                                $i = 0;
                                while ($fetrooms = $sql_rooms->fetch()) {
                                    $i++;
                                    $totprice = $totprice + ($fetrooms['qty'] * $fetrooms['menu_price'] - $fetrooms['discount']);

                                    $dueamount =  $totprice - $paidAmount;
                                    ?>
										<tr class="gradeU">
											<td><?php echo $i; ?></td>
											<td>x<?php echo $fetrooms['qty'] . ' ' . $fetrooms['menu_name']; ?></td>
											<td><?php echo number_format($fetrooms['menu_price'], 2); ?></td>
											<td><?php echo $fetrooms['discount']; ?></td>
											<td><?php echo number_format($fetrooms['qty'] * $fetrooms['menu_price'] - $fetrooms['discount'], 2); ?></td>
											<td>
												<?php echo $fetrooms['created_at']; ?>
											</td>
											<td>
												<a href="" data-toggle="modal" data-target="#updateMenu" data-menu="<?php echo $fetrooms['menu_id'] ?>" class="btn btn-link"><i class="icon-edit"></i> Discount</a>
											</td>

											<td>













												<?php
    ?>
											</td>
										</tr>
									<?php
}
?>
								</tbody>





								<tfoot>
									<tr>
										<th colspan="6"></th>
										
										<th colspan="1">Total: <?php echo number_format($totprice, 2);
                                        $_SESSION['tot_inv_price'] = number_format($totprice, 2); ?></th>
										<th>
											<div class="dropdown">
												<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
													Manage
												</button>
												<ul class="dropdown-menu">

													<style>
														li {
															padding: 5px;
														}
													</style>
													<?php if ($paymentstatus != 12) { ?>


													<?php } ?>



													<?php if ($dueamount > 0) { ?>


														<?php if (!$client) { ?>

															<li> <a href="#" data-toggle="modal" data-target="#roomclient" data-placement="left" title="Add Order to room">Add order to room</a>
															</li>

														<?php } ?>



														<?php if (!$credit) { ?>

															<li> <a href="#" data-toggle="modal" data-target="#creditclient" data-placement="left" title="Add Order to room"> Add Credit</a>
															</li>

														<?php } ?>


														<li> <a href="#" data-toggle="modal" data-target="#myModal"> Add Payment</a> </li>


													<?php } ?>





												</ul>
											</div>
										</th>
									</tr>



								</tfoot>
							</table>

							<h5 class="mt-4">Payments Made</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Reference</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $order_code = $_REQUEST['c'];
$payments = $conn->query("SELECT * FROM payment_tracks WHERE order_code = '$order_code' ORDER BY id DESC");
$i = 1;
while ($row = $payments->fetch_assoc()) {
    ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo number_format($row['amount'], 2); ?></td>
                <td><?php

            if ($row['method'] == '01') {
                echo "Cash";
            }

    if ($row['method'] == '02') {
        echo "Credit";
    }

    if ($row['method'] == '05') {
        echo "POS";
    }

    if ($row['method'] == '06') {
        echo "Mobile Money";
    }

    if ($row['method'] == '03') {
        echo "Cash Credit";
    }
    if ($row['method'] == '04') {
        echo "Cheque";
    }

    ?></td>
                <td><?php echo $row['remark']; ?></td>
                <td><?php echo date('Y-m-d H:i', $row['created_at']); ?></td>
                <td>

<button type="button"
    class="btn btn-warning edit-btn"
    data-toggle="modal"
    data-target="#myModal"
    data-id="<?php echo $row['id']; ?>"
    data-amount="<?php echo $row['amount']; ?>"
    data-method="<?php echo $row['method']; ?>"
    data-remark="<?php echo htmlspecialchars($row['remark']); ?>">
    Edit
</button>


                    <a href="?resto=gstInvce&resrv=<?php echo $_REQUEST['resrv']?>&c=<?php echo $order_code ?>&delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>


							<?php
                $Mtot = 0;
$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                            INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                            WHERE  tbl_cmd_qty.cmd_code = '" . $_GET['c'] . "' ");
$sql->execute();

if ($sql->rowCount() > 0) {
    $no = 1;
    $list = '';
    $no = 0;
    $t = 0;
    $taxblAmtB = 0;
    $totTaxB = 0;
    $tax = 0;
    $taxblAmtA = 0;
    $totTaxA = 0;
    $taxblAmtC = 0;
    $totTaxC = 0;
    $taxblAmtD = 0;
    $totTaxD = 0;
    $totalamountamount = 0;

    while ($fetch = $sql->fetch()) {
        $cmdcode = $fetch['cmd_code'];
        $tableNo = $fetch['cmd_table_id'];
        $Mprice = $fetch['cmd_qty'] * $fetch['menu_price'];
        $Mname = $fetch['menu_name'];
        $waiter = $fetch['Serv_id'];
        $tax = 0;


        $no += 1;

        $sum = $fetch['cmd_qty'] * $fetch['menu_price'];
        $discount = $sum * ($fetch['discount'] / 100);
        $suppl = $fetch['cmd_qty'] * $fetch['menu_price'];
        $sum = $sum - $discount;

        $Mtot = $Mtot + $sum;
        $totprice = $Mtot;


        $t = $sum;

        if ($fetch['taxTyCd'] == 'B') {
            $taxblAmtB += $t;
            $totTaxB += $t;
            $tax += ($sum * 18 / 118) + $tax;

            $totalamountamount += $tax;
        } elseif ($fetch['taxTyCd'] == 'A') {
            $tax += 0;
            $totTaxA += $t;
            $taxblAmtA += $t;
        } elseif ($fetch['taxTyCd'] == 'C') {
            $tax += 0;
            $taxblAmtC += $t;
        } elseif ($fetch['taxTyCd'] == 'D') {
            $tax += 0;
            $taxblAmtD += $t;
        }

        //   echo 'TOTAL'.$totalamountamount;

        // $discount = $sum * ($fetch['discount']/100);

        $totalbtax = str_replace(',', '', number_format($tax, 2));

        $list .= '{"itemSeq":' . $no . ',"itemCd":"' . $fetch['itemCd'] . '","itemClsCd":"' . $fetch['itemClsCd'] . '","itemNm":"' . $fetch['itemNm'] . '","bcd":null,"pkgUnitCd":"' . $fetch['pkgUnitCd'] . '","pkg":1,"qtyUnitCd":"' . $fetch['qtyUnitCd'] . '","qty":' . $fetch['cmd_qty'] . ',"prc":' . $fetch['dftPrc'] . ',"splyAmt":' . $suppl . ',"dcRt":' . $fetch['discount'] . ',"dcAmt":' . $discount . ', "isrccCd":null,"isrccNm":null,"isrcRt":null,"isrcAmt":null,"taxTyCd":"' . $fetch['taxTyCd'] . '","taxblAmt":' . $sum . ',"taxAmt":' . $totalbtax . ',"totAmt":' . $sum . '},';
        // echo $list;


    }
    $totpaid = 0;
    $sqlpaid = $db->prepare("SELECT SUM(payamount) AS totamount FROM tbl_sales_payment WHERE paycode = '$cmdcode' AND paytable = '$tableNo'");
    $sqlpaid->execute();
    $totpaid = $sqlpaid->fetchObject()->totamount;

    $sqltin = $db->prepare("SELECT client ,creadit_user  FROM tbl_cmd WHERE OrderCode = '" . $_GET['c'] . "'");
    $sqltin->execute();
    $row = $sqltin->fetch();
    $count = $row['client'];



    $credit = $row['creadit_user'];
    ?>



								<div class="modal fade" id="updateMenu" role="dialog">
									<div class="modal-dialog modals-default">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
											</div>
											<form action="" method="POST">
												<div class="modal-body">
													<h2>Add Discount</h2>

													<div class="row">
														<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group ic-cmp-int float-lb floating-lb">
																<div class="form-ic-cmp">

																</div>
																<div class="nk-int-st">
																	<input type="number" min=1 name="discount" class="form-control" placeholder="Type Discount Amount" required>
																	<input type="hidden" name="item" class="form-control" required>
																	<input type="hidden" name="codes" class="form-control" value="<?php echo $_GET['c']; ?>">
																</div>
															</div>
														</div>
													</div>
													<hr>
													<div class="modal-footer">
														<button type="submit" class="btn btn-default" name="add">Save</button>
														<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
													</div>

												</div>
										</div>
										</form>
									</div>
								</div>
							<?php
}
?>
							</table>
							<br>


							<?php

$names = "";
$tin = "";
$phone = "";

if ($credit) { ?>
								<div style='background-color:white;padding:20px'> <?php

                                echo "<h5>Credits User detail <br><br>";
    $sql = "SELECT * FROM `creadit_id` WHERE id='$credit'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            echo $names = $row['f_name'] . " " . $row['l_name'];
            $phone = $row['phone'];
            $tin = $row['tinnumber'];
            echo "<br>" . $row['phone'] . "</h5>";
        }


        ?><a href="remove_credit_user.php?order=<?php echo $_REQUEST['c'] ?>" style='color:red'>Remove</a>


										<br>
								</div> <?php


    } else {
    }
}; ?>


						<?php if ($client) {

						    echo "<h6 style='background-color:white;padding:20px'>ROOM Details</h6>";

						    echo "<h6 style='background-color:white;padding:20px'>" . $client . "</h6>";

						    ?><a href="../services/remove.php?order=<?php echo $_REQUEST['c'] ?>" style='color:red'>Remove Client to this order</a><?php

						}; ?>

						<?php if ($dueamount == 0) { ?>


							<form method="POST">
                                <!-- <label for="">Discount Amount</label> -->
								<input type="hidden" readonly name="discount_amount" class="form-control" value="<?php echo $discount; ?>">
                                <!-- <label for="">Price Amount</label> -->
								<input type="hidden" readonly name="price_amount" class="form-control" value="<?php echo $Mtot; ?>">
                                <!-- <label for="">Total Amount</label> -->
								<input type="hidden" readonly name="tot_amount" class="form-control" value="<?php echo $totprice; ?>">
                                <!-- <label for="">Waiter</label> -->
								<input type="hidden" readonly name="waiter" class="form-control" value="<?php echo $waiter; ?>">
                                <!-- <label for="">Table</label> -->
								<input type="hidden" readonly name="table" class="form-control" value="<?php echo $_GET['resrv']; ?>">
                                <!-- <label for="">Code</label> -->
								<input type="hidden" readonly name="code" class="form-control" value="<?php echo $_GET['c']; ?>">
                                <!-- <label for="">Invoice No</label> -->
								<input type="hidden" readonly name="invoice_no" class="form-control" value="<?php echo $pay_no; ?>">
                                <!-- <label for="">Total Items</label> -->
								<input type="hidden" readonly name="totalitem" class="form-control" value="<?php echo $no; ?>">
                                <!-- <label for="">Taxable Amount B</label> -->
								<input type="hidden" readonly name="totalamountamount" class="form-control" value="<?php echo $totalamountamount ?>">

								<div class="row">
									<?php if ($count == '') { ?>
										<!-- <div class="col-md-2">
												Client Tin
											</div>
											<div class="col-md-4">
											<input type="hidden" name="tables" class="form-control" value="<?php echo $table_id; ?>">
											<input type="hidden" name="code" class="form-control" value="<?php echo $_GET['c']; ?>">
											<input type="number" name="tin" class="form-control">
											<button type="submit" class="btn btn-primary pull-right" name="addclient" style="margin:10px;"> Add</button>
											</div> -->
									<?php }
									if ($totprice != $totpaid) {
									    ?>
										<div class="col-md-12">


											<input type="hidden" value='<?php echo $list; ?>' name="list">
											<input type="hidden" value="<?php echo number_format($totprice); ?>" name="amount">

											<input type="hidden" name="taxableAmount" value="<?php echo $totaltablAmt; ?>">
											<input type="hidden" name="totTaxA" value="<?php echo $totTaxA; ?>">
											<input type="hidden" name="taxBamount" value="<?php echo $totalamountamount; ?>">

											<input type="hidden" name="taxblAmtA" value="<?php echo $taxblAmtA; ?>">
											<input type="hidden" name="taxblAmtB" value="<?php echo $taxblAmtB; ?>">
											<input type="hidden" name="totTaxB" value="<?php echo $totTaxB; ?>">


											<input type="hidden" name="taxblAmtC" value="<?php echo $taxblAmtC; ?>">
											<input type="hidden" name="taxblAmtD" value="<?php echo $taxblAmtD; ?>">



											<div class="col-lg-6">
												<label for="inputAddress">Client TIN</label>
												<input type="text" name="clientTin" class="form-control" value="<?php if ($tin) {
												    echo $tin;
												} ?>">
												<label for="inputAddress">Names</label>
												<input type="text" name="clientName" class="form-control" value="<?php if ($names) {
												    echo $names;
												} else {
												    echo "XXXX";
												} ?>" required>
												<label for="inputAddress">Phone</label>
												<input type="text" name="clientPhone" class="form-control" value="<?php if ($phone) {
												    echo $phone;
												} else {
												    echo "070000000";
												} ?>" required>
												<br><br>
											</div>



											<div class="col-lg-6" id="prevsales">
												<label for="inputAddress">Purchase code</label>
												<input type="text" name="purchasecode" id="purchasecode" autocomplete="off" class="form-control">
												<br><br>
											</div>
											<div class="col-lg-6">

												<label>Payment Method </label>



												<select name="pmtTyCd" class="form-control selectpicker" data-live-search="true" required>

													<?php if ($paymentmethod) {

													    $sql = "SELECT *  FROM tbl_info where code='$paymentmethod' and code_crasification = '7'";
													    $result = $conn->query($sql);

													    if ($result->num_rows > 0) {
													        // output data of each row
													        while ($row = $result->fetch_assoc()) {
													            ?>
																<option value="<?php echo $row['code'] ?>"><?php echo $row['code_name'] ?></option>
													<?php
													        }
													    }
													} ?>
													<?php $cuntries = getInfoTable(7);
									    foreach ($cuntries as $key => $value) {
									        ?>
														<option value="<?php echo trim($value[1]) ?>"><?php echo $value[3] ?></option>
													<?php }
									    ?>
												</select>
											</div>
										</div>
									<?php } else { ?>
										<button type="submit" onclick="window.open('pdf_receipt?resrv=<?php echo urlencode(base64_encode($_GET['c'])); ?>', '_blank')" class="btn btn-primary pull-right" name="save" style="margin:10px;"> Invoice & Print</button>
									<?php } ?>
								</div>
								<div id="btn"><button type="submit" class="btn btn-primary pull-right" name="savesales" style="margin:10px;"> Save </button></div>
							<?php } ?>
							<br><br>
							</form>
						</div>
					</div>

					<!-- Modal -->
					<div id="myModal" class="modal fade" role="dialog">
						<div class="modal-dialog">

							<!-- Modal content-->
							<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Payment</h4>
    </div>
    <div class="modal-body">
        <form method="POST" id="paymentForm">
            <input type="hidden" name="edit_id" id="edit_id" value="">
            <input type="hidden" name="totprice" id="totprice" value="<?php echo $_SESSION['tot_inv_price'];?>" disabled>
            <div class="alert alert-danger" id="totalValidation" style="display:none;"></div>
            
            <div id="paymentRows">
                <div class="payment-row row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Amount</label>
                        <input type="number" min="0" step="0.01" class="form-control" name="amount[]" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" name="method[]" required>
                            <?php
                            $cuntries = getInfoTable(7);
foreach ($cuntries as $key => $value) {
    echo '<option value="' . trim($value['code']) . '">' . $value['code_name'] . '</option>';
}
?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reference</label>
                        <input type="text" class="form-control" name="remark[]" placeholder="Reference / POS Auth">
                    </div>
                </div>
            </div>

            <div class="mb-3" id="addAnotherWrapper">
                <button type="button" class="btn btn-secondary" onclick="addRow()">+ Add Another Method</button>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="submitBtn" name="addpayment">
                    Confirm
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>
						</div>
					</div>


					<!-- Credit Client Modal -->
<div class="modal fade" id="creditclient" tabindex="-1" role="dialog" aria-labelledby="creditclientLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h4 class="modal-title" id="creditclientLabel">Add Credit Client</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            &times;
          </button>
        </div>
        <div class="modal-body">

          <!-- Select Existing Client -->
          <div class="form-group">
            <label for="clientname">Select Existing Client</label>
            <select class="form-control" name="clientname" id="clientname">
              <option value="">-- Choose Existing --</option>
              <?php
                $res = $conn->query("SELECT id, f_name, l_name, phone FROM creadit_id ORDER BY f_name");
                while ($row = $res->fetch_assoc()) {
                    $name = htmlspecialchars($row['f_name'] . ' ' . $row['l_name'] . ' (' . $row['phone'] . ')');
                    echo "<option value='{$row['id']}'>{$name}</option>";
                }
                ?>
            </select>
          </div>

          <hr>
          <p class="text-center">OR Add New Client</p>

          <!-- New Client Inputs -->
          <div class="row">
            <div class="col-md-6">
              <label>First Name</label>
              <input type="text" name="new_fname" class="form-control">
            </div>
            <div class="col-md-6">
              <label>Last Name</label>
              <input type="text" name="new_lname" class="form-control">
            </div>
          </div>

          <div class="form-group mt-2">
            <label>Phone</label>
            <input type="text" name="new_phone" class="form-control">
          </div>

          <div class="form-group">
            <label>TIN Number</label>
            <input type="text" name="new_tin" class="form-control">
          </div>

          <div class="form-group">
            <label>Email (optional)</label>
            <input type="email" name="new_email" class="form-control">
          </div>

          <!-- Credit Amount -->
          <div class="form-group mt-3">
            <label>Credit Amount</label>
            <input type="number" min="<?php echo $totprice; ?>" max="<?php echo $totprice; ?>" name="creaditamount" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" name="addClientsTocredit" class="btn btn-primary">Confirm Credit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>

      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="roomclient" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 class="modal-title">Extra info</h3>
            </div>
            <div class="modal-body">
                <form method="POST">
            <div class="row">
            <div class="form-group">
                <div class="col-md-12">
					 <select name="clientinroom" class="form-control">
					<?php
$sql = $db->prepare("
    SELECT

        b.id AS booking_id,
        g.first_name,
        g.last_name,
		g.id as userbooking,
        b.checkin_date,
        b.checkout_date,
        b.payment_status_id,
        r.room_number
    FROM tbl_acc_booking b
    JOIN tbl_acc_guest g ON b.guest_id = g.id
    JOIN tbl_acc_booking_room br ON b.id = br.booking_id
    JOIN tbl_acc_room r ON br.room_id = r.id
    WHERE b.booking_status_id = 6 AND b.checkout_date >= CURRENT_DATE
");
$sql->execute();

if ($sql->rowCount()) {
    while ($row = $sql->fetch()) { ?>

		<option value="<?php echo $row['booking_id']; ?> "><?php   echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?> -  <?php echo $row['room_number']; ?> </option>


		<?php }
    } ?>


				 </select>


            </div>
            </div>
             <button type="submit" name="addClientToOrder" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save </button>
            </div>

            </form>
            </div>
        </div>
    </div>
    </div>


				</div>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


	<script>
		<?php

            function getax($id)
            {
                // include 'link.php';
                global $conn;
                $sql = "SELECT * FROM menu where itemCd='$id'";
                $result = $conn->query($sql);

                if ($result->rowCount() > 0) {

                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

                        return $row['taxTyCd'];
                    }
                }
            }


?>
	</script>

	<script>
		// JavaScript to handle modal events
		$('#updateMenu').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget); // Button that triggered the modal


			var menu = button.data('menu');
			// var itemSeq = button.data('itemseq');
			// var dclDe = button.data('dclde');
			// var hsCd = button.data('hscd');
			// console.log(taskCd);

			$('input[name="item"]').val(menu);
			// $('input[name="itemSeq"]').val(itemSeq);
			// $('input[name="dclDe"]').val(dclDe);
			// $('input[name="hsCd"]').val(hsCd);

		});
	</script>

	<script>
		// Get elements
		const searchInput = document.getElementById("searchInput");
		const select = document.getElementById("dropdown");

		// Add event listener to filter options as user types
		searchInput.addEventListener("input", function() {
			const filter = searchInput.value.toLowerCase();
			const options = select.getElementsByTagName("option");

			// Loop through all options and hide those that do not match the search
			for (let i = 0; i < options.length; i++) {
				const option = options[i];
				if (option.text.toLowerCase().indexOf(filter) > -1) {
					option.style.display = ""; // Show option
				} else {
					option.style.display = "none"; // Hide option
				}
			}
		});
	</script>
<!-- <script>
	function addRow() {
		const row = document.querySelector('.payment-row');
		const clone = row.cloneNode(true);
		clone.querySelectorAll('input').forEach(input => input.value = '');
		document.getElementById('paymentRows').appendChild(clone);
	}

	document.getElementById("paymentForm").addEventListener("submit", function (e) {
		const amountInputs = document.querySelectorAll("input[name='amounts[]']");
		let total = 0;
		amountInputs.forEach(input => total += parseFloat(input.value || 0));
		const dueAmount = <?php //echo $dueamount?>;

	});
</script> -->
<script>
$(document).ready(function() {
    // Function to calculate and validate total
    function validatePaymentTotal() {
        let totalEntered = 0;
        $("input[name='amount[]']").each(function() {
            totalEntered += parseFloat($(this).val()) || 0;
        });

        
        const totalRequiredText = $("#totprice").val();
		const totalRequired = parseFloat(totalRequiredText.replace(/,/g,''));
		console.log(totalRequired)
        const isValid = Math.abs(totalEntered - parseFloat(totalRequired)) < 0.01; // Allow small floating point differences
        
        // Toggle submit button based on validation
        $('#submitBtn').prop('disabled', !isValid);
        
        // Show/hide validation message
        if (totalEntered > 0 && !isValid) {
            $('#totalValidation').show().text(`Total must equal ${parseFloat(totalRequired)?.toFixed(2)}. Current: ${parseFloat(totalEntered)?.toFixed(2)}`);
        } else {
            $('#totalValidation').hide();
        }
        
        return isValid;
    }

    // Add row function
    function addRow() {
        const row = $('.payment-row:first').clone();
        row.find('input').val('');
        row.find('select').val('01'); // Default to first payment method
        $('#paymentRows').append(row);
        
        // Add remove button to new row (except first)
        if($('.payment-row').length > 1) {
            row.append('<div class="col-md-12 text-right"><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></div>');
        }
        
        // Rebind events
        row.find('input[name="amount[]"]').on('input', validatePaymentTotal);
    }

    // Remove row function
    $(document).on('click', '.remove-row', function() {
        if($('.payment-row').length > 1) {
            $(this).closest('.payment-row').remove();
            validatePaymentTotal();
        }
    });

    // Initialize
    $('input[name="amount[]"]').on('input', validatePaymentTotal);
    $('#addAnotherWrapper button').on('click', addRow);
    
    // Form submission handler
    $('#paymentForm').on('submit', function(e) {
        if(!validatePaymentTotal()) {
            e.preventDefault();
            alert('Total payment amount must exactly match the order total.');
        }
    });

    // Edit button handler
    $('.edit-btn').click(function() {
        const id = $(this).data('id');
        const amount = $(this).data('amount');
        const method = $(this).data('method');
        const remark = $(this).data('remark');

        $('#edit_id').val(id);
        $('#amount').val(amount);
        $('#method').val(method);
        $('#remark').val(remark);
        $('#addAnotherWrapper').hide();
        $('#submitBtn').text('Update').attr('name', 'updatepayment');
    });

    // Reset modal on close
    $('#myModal').on('hidden.bs.modal', function() {
        $('#paymentForm')[0].reset();
        $('#edit_id').val('');
        $('#submitBtn').text('Confirm').attr('name', 'addpayment').prop('disabled', false);
        $('#addAnotherWrapper').show();
        $('#totalValidation').hide();
        
        // Keep only first row, remove others
        $('.payment-row:not(:first)').remove();
    });
});
</script>


<script>
$(document).ready(function () {
    $('.edit-btn').click(function () {
        const id = $(this).data('id');
        const amount = $(this).data('amount');
        const method = $(this).data('method');
        const remark = $(this).data('remark');

        // Set values in modal
        $('#edit_id').val(id);
        $('#amount').val(amount);
        $('#method').val(method);
        $('#remark').val(remark);

        // Hide Add Another row button (if editing)
        $('#addAnotherWrapper').hide();

        // Change submit button
        $('#submitBtn').text('Update').attr('name', 'updatepayment');

        // Open modal
        $('#myModal').modal('show');
    });

    // Reset modal on close
    $('#myModal').on('hidden.bs.modal', function () {
        $('#paymentForm')[0].reset();
        $('#edit_id').val('');
        $('#submitBtn').text('Confirm').attr('name', 'addpayment');
        $('#addAnotherWrapper').show();
    });
});
</script>
