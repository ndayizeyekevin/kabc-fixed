<?php
   $db_username = getenv('DB_USERNAME');
	$db_password = getenv('DB_PASSWORD');
  $db_name = getenv('DB_NAME');
	$conn = new PDO("mysql:host=localhost;dbname=$db_name;charset=utf8mb4", $db_username, $db_password);
	if(!$conn){
		die("Fatal Error: Connection Failed!");
	}
$item = $_POST['item'];

$sql = "SELECT * FROM `tbl_vsdc_sales` WHERE `invcNo`='$item'";
$result = $conn->query($sql);

if ($result === false) {
    $errorInfo = $conn->errorInfo();
    die("SQL query failed: " . $errorInfo[2]);
}


$table = '<tr>
<td>Item</td>
<td>Tax Rate</td>
<td>Price</td>


<td>Quantity</td>
<td>T.Price</td>

</tr>';

while($row = $result->fetch()) {
// Fix money decimals
$moneyFields = ['totTaxAmt', 'taxAmtB'];
foreach ($moneyFields as $field) {
    if (isset($row[$field])) {
        $row[$field] = number_format((float)$row[$field], 2, '.', '');
    }
}

// Fix date formats
$row['cfmDt'] = date('YmdHis', strtotime($row['cfmDt']));
$row['salesDt'] = date('Ymd', strtotime($row['salesDt']));

// Clean custTin
if ($row['custTin'] === "null" || is_null($row['custTin'])) {
    $row['custTin'] = "";
}

// Clean prcOrdCd
if ($row['prcOrdCd'] === "null" || is_null($row['prcOrdCd'])) {
    $row['prcOrdCd'] = "000000"; // or appropriate fallback
}

// Validate rcptTyCd
$row['rcptTyCd'] = 'R'; // or valid value from your system

$row['orgInvcNo'] = $row['invcNo']; // or valid value from your system
$row['rfdRsnCd'] = '03';
$row['taxRtB'] = '18';
$row['bhfId'] = '00';

try {
    $stmt = $conn->prepare("SELECT invcNo FROM tbl_vsdc_sales ORDER BY invcNo DESC LIMIT 1");
    $stmt->execute();

    $roww = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($roww && isset($roww['invcNo'])) {
        $lastInvcNo = (int)$roww['invcNo'] + 1;
    } else {
        $lastInvcNo = 1; // Default if no invoice found
    }


} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
$row['invcNo'] = $lastInvcNo;
// Fix taxAmt in itemList
$items = json_decode($row['itemList'], true);
foreach ($items as &$item) {
    if (isset($item['taxAmt'])) {
        $item['taxAmt'] = number_format((float)$item['taxAmt'], 2, '.', '');
    }
}
unset($item);
$row['itemList'] = $items;
    $receipt = json_decode($row['receipt'], true);
  $row['receipt']=$receipt;
  $sales = $row;
                        foreach ($items as $value) {
                           
                        
    $table .= '<tr>
    <td>'.$value['itemNm'].'</td>
    <td>'.$value['taxTyCd'].'</td>
    <td>'.$value['prc'].'</td>
    <td>'.$value['qty'].'</td>
    <td>'.$value['totAmt'].'</td>
    </tr>';
}
$table .= '<tr>
  <td colspan="5"></td>
  <td>VAT</td>
  <td>'.$row['totTaxAmt'].'</td>

</tr>
<tr>
  <td colspan="5"></td>
  <td>Total</td>
  <td>'.$row['totAmt'].'</td>
</tr>';
}

$table .='</table>';


$sql = "SELECT * FROM `tbl_vsdc_sales` WHERE `invcNo`='$item'";
$result = $conn->query($sql);
$data = $result->fetch();

// // Display data without a loop
$names = $data['custNm'];
$tin = $data['custTin'];
$phone = $data['custPhone'];

// echo $table;
header('Content-Type: application/json');
$response = [
  'table' => $table,
  'names' => $names,
  "tin" => $tin,
  'phone' => $phone,
  'sale'=> $sales
];

echo json_encode($response);
