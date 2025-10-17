<?php


if(isset($_POST['save']))
{
	
	
	$sql = "INSERT INTO `customer` (`tin`, `bhfId`, `custNo`,
	`custTin`, 
	`custNm`, `adrs`, `telNo`, `email`, `faxNo`, `useYn`,
	`remark`, `regrNm`, `regrId`, `modrNm`, `modrId`)
	VALUES (
	'".$_POST['tin']."', 
	'".$_POST['bhfId']."', 
	'".$_POST['custNo']."', 
	'".$_POST['custTin']."', 
	'".$_POST['custNm']."', 
	'".$_POST['adrs']."', 
    '".$_POST['telNo']."', 
	'".$_POST['email']."', 
	'".$_POST['faxNo']."', 
	'".$_POST['useYn']."', 
	'".$_POST['remark']."', 
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
	<div class="breadcomb-area">
		<div class="container">




<form method="POST">
  <fieldset>
    <legend>Customer Details</legend>
    <label for="tin">TIN:</label>
    <input type="text" id="tin" name="tin" value="999000099" ><br>
    <label for="bhfId">BHF ID:</label>
    <input type="text" id="bhfId" name="bhfId" value="00" ><br>
    <label for="custNo">Customer No:</label>
    <input type="text" id="custNo" name="custNo" value="999991113" ><br>
    <label for="custTin">Customer TIN:</label>
    <input type="text" id="custTin" name="custTin" value="107397100" ><br>
    <label for="custNm">Customer Name:</label>
    <input type="text" id="custNm" name="custNm" value="ERIRWANDA"><br>
    <label for="adrs">Address:</label>
    <input type="text" id="adrs" name="adrs"><br>
    <label for="telNo">Telephone No:</label>
    <input type="tel" id="telNo" name="telNo"><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br>
    <label for="faxNo">Fax No:</label>
    <input type="text" id="faxNo" name="faxNo"><br>
    <label for="useYn">Use Y/N:</label>
    <input type="text" id="useYn" name="useYn" value="Y"><br>
    <label for="remark">Remark:</label>
    <input type="text" id="remark" name="remark"><br>
	
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
  <input type="submit" value="Submit" name="save">
</form>