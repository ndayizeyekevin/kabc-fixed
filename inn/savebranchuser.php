<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password,'aptc');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";



if(isset($_POST['save']))
{
	
	
	$sql = "INSERT INTO `branchusers` (`tin`, 
	`bhfId`,
	`userId`,
	`userNm`, 
	`pwd`,
	`adrs`,
	`cntc`, 
	`authCd`,
	`remark`, 
	`useYn`, 
	`regrNm`, 
	`regrId`,
	`modrNm`, 
	`modrId`) VALUES 
	(
	'".$_POST['tin']."',
	'".$_POST['bhfId']."',
	'".$_POST['userId']."',
	'".$_POST['userNm']."',
'".$_POST['pwd']."',
	'".$_POST['adrs']."',
'".$_POST['cntc']."',
	'".$_POST['authCd']."',
	'".$_POST['remark']."',
	'".$_POST['useYn']."',
	'".$_POST['regrNm']."',
'".$_POST['regrId']."',
	'".$_POST['modrNm']."',
	'".$_POST['modrId']."'
	);";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

	
	
}

?>





<form action="#" method="POST">
  <fieldset>
    <legend>User Details</legend>
    <label for="tin">TIN:</label>
    <input type="text" id="tin" name="tin" value="999000099" ><br>
    <label for="bhfId">BHF ID:</label>
    <input type="text" id="bhfId" name="bhfId" value="00" ><br>
    <label for="userId">User ID:</label>
    <input type="text" id="userId" name="userId" value="userId3" ><br>
    <label for="userNm">User Name:</label>
    <input type="text" id="userNm" name="userNm" value="UserName3"><br>
    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd" value="12341234"><br>
    <!-- more labels and inputs for the rest of the user details -->
 
   <label for="adrs">Address:</label>
    <input type="text" id="adrs" name="adrs"><br>
    <label for="cntc">Contact:</label>
    <input type="text" id="cntc" name="cntc"><br>
    <label for="authCd">Authorization Code:</label>
    <input type="text" id="authCd" name="authCd"><br>
    <label for="remark">Remark:</label>
    <input type="text" id="remark" name="remark"><br>
    <label for="useYn">Use Y/N:</label>
    <input type="text" id="useYn" name="useYn" value="Y"><br>
    <label for="regrNm">Register Name:</label>
    <input type="text" id="regrNm" name="regrNm" value="Admin" ><br>
    <label for="regrId">Register ID:</label>
    <input type="text" id="regrId" name="regrId" value="Admin" ><br>
    <label for="modrNm">Modifier Name:</label>
    <input type="text" id="modrNm" name="modrNm" value="Admin"><br>
    <label for="modrId">Modifier ID:</label>
    <input type="text" id="modrId" name="modrId" value="Admin"><br>
 </fieldset>

  <!-- add a submit button -->
  <input type="submit" value="Submit" name ="save">
</form>