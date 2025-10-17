<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// die(var_dump($_SESSION));

if (!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])) {
	$from = date('Y-m-d');
	$to = date("Y-m-d");
} else {
	$from = $_SESSION['date_from'];
	$to = $_SESSION['date_to'];
}


include '../inc/conn.php';

include "../inc/close_open_day.php";

?>




<div class="container">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-example-wrap mg-t-30">
				<div class="tab-hd">
					<h2><small><strong><i class="fa fa-refresh"></i>Today's Sales <?php $last = lastday(); ?></strong></small></h2>
				</div>

									<?php include "../inc/date_filter.php"; ?>





				<?php



$selected_date = $_GET['date'] ?? null;

if (!$selected_date) {
    $sql = $db->prepare("
        SELECT 
            cmd_code,
            menu_price,
            tax,
            menu_desc,
            tbl_cmd_qty.cmd_item,
            menu.menu_name,
            SUM(tbl_cmd_qty.cmd_qty) AS totqty,
            tbl_cmd_qty.created_at
        FROM tbl_cmd_qty
        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
        WHERE DATE(tbl_cmd_qty.created_at) = (SELECT opened_at
                FROM days
                ORDER BY id DESC
                LIMIT 1)
        AND EXISTS (
            SELECT 1
            FROM (
                SELECT closed_at
                FROM days
                ORDER BY id DESC
                LIMIT 1
            ) AS last_day
            WHERE last_day.closed_at IS NULL
        )
        GROUP BY tbl_cmd_qty.cmd_item
    ");
    	$sql1 = $sql;
    $sql->execute();
    $sql1->execute();
} else {
    // Safely use the user-selected date
    $sql = $db->prepare("
        SELECT 
            cmd_code,
            menu_price,
            tax,
            menu_desc,
            tbl_cmd_qty.cmd_item,
            menu.menu_name,
            SUM(tbl_cmd_qty.cmd_qty) AS totqty,
            tbl_cmd_qty.created_at
        FROM tbl_cmd_qty
        INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
        WHERE DATE(tbl_cmd_qty.created_at) = :selected_date
        GROUP BY tbl_cmd_qty.cmd_item
    ");
    	$sql1 = $sql;
    $sql->execute(['selected_date' => $selected_date]);
    $sql1->execute(['selected_date' => $selected_date]);
}





					$sql1 = $sql;
			
					$reportDate    =  $_GET['date']??date('Y-m-d');
		
				?>

				<br> <br>
				<button onclick=" printInvoice()"> Print </button>
				<button onclick="exportTableToExcel('sales', 'Sales of <?php echo $reportDate ?>')">Export to Excel</button>
				<div id="content">

					<?php include '../holder/printHeader.php' ?>

					<hr>

					<h4>
						<center>Report for <?php echo $reportDate ?></center>
						<h4>
							<hr>

							<br>
							<br>
							<div id="sales" class="table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>

											<th>ITEM NAME</th>
											<th>ITEM DESCRIPTION</th>
											<th>PRICE</th>
											<th>QTY</th>

											<th>TOTAL</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 0;


										$total = 0;

								// 		$sql->execute(array());

										if ($sql->rowCount()) {
											while ($fetch = $sql->fetch()) {
												$i++;
												$OrderCode = $fetch['cmd_code'];

												$amount = (int)$fetch['menu_price'] * (int)$fetch['totqty'];
												$tottax = $amount * (int)$fetch['tax'];
												// $n = 1;
												$total =  $total + $amount;
										?>
												<tr>

													<td><?php echo $fetch['menu_name']; ?></td>
													<td><?php echo $fetch['menu_desc']; ?></td>
													<td><?php echo number_format($fetch['menu_price']); ?></td>
													<td><?php echo $fetch['totqty']; ?></td>
													<td><?php echo number_format($amount); ?></td>


												</tr>
											<?php
											} ?>
											<tr>
												<th colspan="4">Grand Total</td>
												<th> <?php echo number_format($total) ?> RWF</th>

											</tr>

										<?php

										}
										?>






									</tbody>
								</table>



								<?php


								$cash =  0;
								$card = 0;
								$momo = 0;
								$credit = 0;
								$cheque = 0;

								// $sql1->execute(array());

								if ($sql1->rowCount()) {
									while ($fetch = $sql1->fetch()) {



										$cash = $cash + getTotalPaidByMethod($fetch['cmd_code'], '01');
										$card = $card + getTotalPaidByMethod($fetch['cmd_code'], '05');
										$momo = $momo + getTotalPaidByMethod($fetch['cmd_code'], '06');
										$credit = $credit + getTotalPaidByMethod($fetch['cmd_code'], '02');
										$cheque = $cheque + getTotalPaidByMethod($fetch['cmd_code'], '04');
									}
								} else {
									//echo $last;
								}





								?>




								<hr>
								<br> <br> <br> <br>



								<?php include '../holder/printFooter.php' ?>



							</div>
				</div>


				<?php
				$stmt = $db->query("SELECT * FROM days ORDER BY id DESC LIMIT 1");
$lastDay = $stmt->fetch(PDO::FETCH_ASSOC);

if ($lastDay && is_null($lastDay['closed_at'])) {
    	$n = 1;
} else {
    	$n = 0;
}
			
				if ($n == 1):



				// 	$sql = "SELECT * FROM days ORDER BY id DESC LIMIT 1 ";
				// 	$result = $conn->query($sql);
				// 	$sale = 0;
				// 	if ($result->num_rows > 0) {
				// 		while ($row = $result->fetch_assoc()) {

				// 			$lastDate = $row['closed_at'];
				// 			$date = $row['opened_at'];
				// // 			$date->modify('+1 day');
				// // 			$lastDate = $date->format('Y-m-d');
				// 		}
				// 	}

				?>
					<form method="POST">
						<br>
						<input type="checkbox" id="approve" name="approve">I hereby confirm all imformation above are correct<br>
						<br>
						<input type="datetime-local" min="<?php  ?>" class="form-control" name="closedate" required>
						<br>
						<button type="submit" value="Close day" name="close" class="btn btn-info"> Close day </button>
					</form>
				<?php
				else:
				    	?>
				    <form method="POST">
						<br>
						<input type="checkbox" id="approve" name="approve">I hereby confirm all imformation above are correct<br>
						<br>
						<input type="datetime-local" class="form-control" name="opendate" required>
						<br>
						<button type="submit"  name="open" class="btn btn-info"> Open day </button>
					</form>
				<?php
				endif
				?>

			</div>
		</div>
	</div>
</div>


<script>
	function printInvoice() {
		$("#headerprint").show();
		$("#printFooter").show();
		$('#data-table-basic').removeAttr('id');
		var printContents = document.getElementById('content').innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
	}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#headerprint").hide();
		$("#printFooter").hide();
		$("#date_to").change(function() {
			var from = $("#date_from").val();
			var to = $("#date_to").val();
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');
			$.post('load_sales_report.php', {
				from: from,
				to: to
			}, function(data) {
				$("#display_res").html(data);
				$('#loader').slideUp(910, function() {
					$(this).remove();
				});
				location.reload();
			});
		});

	});
</script>
<script>
	function exportTableToExcel(tableID, filename = '') {
		let downloadLink;
		const dataType = 'application/vnd.ms-excel';
		const tableSelect = document.getElementById(tableID);
		const tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

		filename = filename ? filename + '.xls' : 'excel_data.xls';

		// Create download link element
		downloadLink = document.createElement("a");

		document.body.appendChild(downloadLink);

		// File format
		downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

		// File name
		downloadLink.download = filename;

		// Trigger the download
		downloadLink.click();

		// Clean up
		document.body.removeChild(downloadLink);
	}
</script>
