<?php

if(isset($_POST['save']))
{
	$sql = "INSERT INTO `items` (`tin`, `bhfId`, `itemCd`,
	`itemClsCd`, `itemTyCd`, `itemNm`, `itemStdNm`,
	`orgnNatCd`, `pkgUnitCd`, `qtyUnitCd`, `taxTyCd`, `btchNo`,
	`bcd`, `dftPrc`, `grpPrcL1`, `grpPrcL2`, 
	`grpPrcL3`, `grpPrcL4`, `grpPrcL5`, 
	`addInfo`, `sftyQty`, `isrcAplcbYn`, 
	`useYn`, `regrNm`, `regrId`, `modrNm`, 
	`modrId`)
	VALUES ('".$_POST['tin']."',
	'".$_POST['bhfId']."', 
	'".$_POST['itemCd']."', 
	'".$_POST['itemClsCd']."',
	'".$_POST['itemTyCd']."',
	'".$_POST['itemNm']."',
	'".$_POST['itemStdNm']."',
	'".$_POST['orgnNatCd']."',
	'".$_POST['pkgUnitCd']."',
	'".$_POST['qtyUnitCd']."', 
	'".$_POST['taxTyCd']."',
	'".$_POST['btchNo']."',
		'".$_POST['bcd']."',
	'".$_POST['dftPrc']."',
	'".$_POST['grpPrcL1']."'
	, '".$_POST['grpPrcL2']."',
	'".$_POST['grpPrcL3']."', 
	'".$_POST['grpPrcL4']."', 
    '".$_POST['grpPrcL5']."',
	'".$_POST['addInfo']."',
	'".$_POST['sftyQty']."',
	'".$_POST['isrcAplcbYn']."', 
	'".$_POST['useYn']."', 
	'".$_POST['regrNm']."',
	'".$_POST['regrId']."', 
	'".$_POST['modrNm']."', 
	'".$_POST['modrId']."');";

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
  <label for="tin">TIN:</label>
  <input type="text" id="tin" name="tin" value="999991130" ><br>
  <label for="bhfId">BHF ID:</label>
  <input type="text" id="bhfId" name="bhfId" value="00" ><br>
  <label for="itemCd">Item Code:</label>
  <input type="text" id="itemCd" name="itemCd" value="RW1NTXU0000006" ><br>
  <label for="itemClsCd">Item Class Code:</label>
  <input type="text" id="itemClsCd" name="itemClsCd" value="5059690800"><br>
  <label for="itemTyCd">Item Type Code:</label>
  <input type="text" id="itemTyCd" name="itemTyCd" value="1"><br>
  <label for="itemNm">Item Name:</label>
  <input type="text" id="itemNm" name="itemNm" value="test material item 3"><br>
  <label for="itemStdNm">Item Standard Name:</label>
  <input type="text" id="itemStdNm" name="itemStdNm"><br>
  <label for="orgnNatCd">Origin Nation Code:</label>
  <input type="text" id="orgnNatCd" name="orgnNatCd" value="RW"><br>
  <label for="pkgUnitCd">Package Unit Code:</label>
  <input type="text" id="pkgUnitCd" name="pkgUnitCd" value="NT"><br>
  <label for="qtyUnitCd">Quantity Unit Code:</label>
  <input type="text" id="qtyUnitCd" name="qtyUnitCd" value="U"><br>
  <label for="taxTyCd">Tax Type Code:</label>
  <input type="text" id="taxTyCd" name="taxTyCd" value="B"><br>
  <label for="btchNo">Batch Number:</label>
  <input type="text" id="btchNo" name="btchNo"><br>
  <label for= "bcd">BCD:</label>
  <input type= "text"id= "bcd"name= "bcd"><br>
  <label for= "dftPrc">Default Price:</label>
  
    <input type= "text"id= "dftPrc"name= "dftPrc"><br>
  
    <label for="grpPrcL1">Group Price Level 1:</label>
  <input type="number" id="grpPrcL1" name="grpPrcL1" value="3500"><br>
  <label for="grpPrcL2">Group Price Level 2:</label>
  <input type="number" id="grpPrcL2" name="grpPrcL2" value="3500"><br>
  <label for="grpPrcL3">Group Price Level 3:</label>
  <input type="number" id="grpPrcL3" name="grpPrcL3" value="3500"><br>
  <label for="grpPrcL4">Group Price Level 4:</label>
  <input type="number" id="grpPrcL4" name="grpPrcL4" value="3500"><br>
  <label for="grpPrcL5">Group Price Level 5:</label>
  <input type="number" id="grpPrcL5" name="grpPrcL5"><br>
  <label for="addInfo">Additional Information:</label>
  <input type="text" id="addInfo" name="addInfo"><br>
  <label for="sftyQty">Safety Quantity:</label>
  <input type="number" id="sftyQty" name="sftyQty"><br>
  <label for="isrcAplcbYn">ISRC Applicable Y/N:</label>
  <input type="text" id="isrcAplcbYn" name="isrcAplcbYn" value="N"><br>
  <label for= "useYn">Use Y/N:</label>
  <input type= "text"id= "useYn"name= "useYn"value= "Y"><br>
  <label for= "regrNm">Register Name:</label>
    <input type= "text"id= "regrNm"name= "regrNm"value= "Y"><br>
   <label for="regrId">Register ID:</label>
  <input type="text" id="regrId" name="regrId" value="Admin" ><br>
  <label for="modrNm">Modifier Name:</label>
  <input type="text" id="modrNm" name="modrNm" value="Admin"><br>
  <label for="modrId">Modifier ID:</label>
  <input type="text" id="modrId" name="modrId" value="Admin"><br>
  <input type="submit" name="save" value="save">
  </form>