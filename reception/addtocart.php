<?php


    $db_username = getenv('DB_USERNAME');;
	$db_password = getenv('DB_PASSWORD');
	$conn = new PDO( 'mysql:host=localhost;'.getenv('DB_NAME').'', $db_username, $db_password );
	if(!$conn){
		die("Fatal Error: Connection Failed!");
	}
	
	$db = new PDO('mysql:host=localhost;'.getenv('DB_NAME').';charset=utf8', $db_username,getenv('DB_PASSWORD'));

$id = $_POST['user'];
$item = $_POST['item'];
$qty = $_POST['qty'];
$transport = $_POST['transport'];
$discount = $_POST['discount'];
$aptc_margin = $_POST['aptc_margin'];





$sql = "SELECT * FROM menu where itemCd ='$item'";
$types = $conn->query($sql);

$type_id = $types->fetch(PDO::FETCH_ASSOC)['menu_id'];

// $br = $_SESSION['branch'];
$sql1 = $db->prepare("SELECT * FROM stock INNER JOIN menu ON menu.menu_id=stock.type WHERE menu.itemCd = '$item' LIMIT 1");
$sql1->execute();
$row = $sql1->fetch();

$count = $sql1->rowCount();
if($count > 0){ 

$quantities = $row['quantities'];

if($quantities > $qty){

$sql = "SELECT * FROM cart_sales where item_id ='$item' and session_id ='$id' and status= 0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {

    $sql = "UPDATE cart_sales SET qty = '$qty', transport='$transport', discount='$discount', aptc_margin='$aptc_margin' WHERE   item_id ='$item' and session_id ='$id' and status= 0";
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

  }
}else{
    $sql = "INSERT INTO `cart_sales` (`cart_id`, `session_id`, `item_id`, `status`, `qty`, `transport`, `discount`, `aptc_margin`, `type_id`) VALUES (NULL, '$id', '$item', '0',$qty, $transport, $discount, $aptc_margin, $type_id);";
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

}
echo 'ok';
}else{
  echo '000';
}

}else{
  echo '000';
}




