<?php
 ob_start();
 if(isset($_POST['proceed'])){
$_SESSION['cid'] = $_REQUEST['cid'];
$_SESSION['id'] = $_REQUEST['myId'];
$_SESSION['chk_in_date'] = trim($_POST['chkin']);
$_SESSION['chk_out_date'] = trim($_POST['chkout']);
$_SESSION['bed'] = trim($_POST['bed']);
}
?>

<section id="cart_items">
		<div class="container">
<div class="review-payment">
				<h2>Reservation Info</h2>
			</div>

			<div class="table-responsive cart_info">
            <form action="?page=authenticate" method="POST">
				<table class="table table-condensed">
					<thead>
						<tr class="cart_menu">
							<td class="image">Room</td>
							<td class="Type">Room Type</td>
                            <td class="price">Price Per Day</td>
                            <td class="Type">Bed Type</td>
                            <td>Check In</td>
							<td>Check Out</td>
                            <td></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="cart_product">
								<img src="../room_gallery/<?php echo $_REQUEST['img']; ?>" alt="" 
                                                                     style="width:70px;height:70px;">
							</td>
							<td class="cart_description">
								<?php echo $_REQUEST['type']; ?>
							</td>
                                                        <td class="cart_price">
								<?php echo "US$".$_REQUEST['price']; ?>
							</td>
                            <td class="cart_description">
                                <select name="bed" class="form-control">
                                    <option>Select Bed Type</option>
                                        <?php
                                        $sql = $db->prepare("SELECT * FROM `tbl_bed_type`");
        		                        $sql->execute();
        		                      while($fetch = $sql->fetch()){
                                                        ?>
							<option value="<?php echo $fetch['bed_id'] ?>"><?php echo $fetch['bed_name']; ?></option><?php } ?></select>
							</td>
							
                            <td><input type="date" name="chkin" class="form-control" required></td>
							<td><input type="date" name="chkout" class="form-control" required></td>
							<td><button name="proceed" class="btn btn-primary" target="_blank">Proceed</button></td>
						</tr>

						
					</tbody>
				</table>
</form>
			</div>
</div>
<section>