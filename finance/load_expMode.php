<?php include'../inc/config.php'; ?>

<?php
$EXPMODE = $_REQUEST['exp_Mode'];
$stts ="cash";
?>

<?php
 if($EXPMODE == "1"){
?>
 <div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Debit Account No</strong></small>
            </div>
            <div class="nk-int-st">
                <select id="bank_ID" class="form-control" name="bank_ID" style="border:none;" required >
            <option>select one...</option>                                   
                  <?php
                        $stmt = $db->prepare("SELECT tbl_bank_amount.bankId,tbl_bank.bank_name,tbl_bank.bank_code,
						tbl_bank_amount.bankAmount 
						FROM tbl_bank 
						INNER JOIN tbl_bank_amount ON tbl_bank.bank_id=tbl_bank_amount.bankId 
						ORDER BY tbl_bank_amount.bankAmount DESC ");
						$stmt->execute();

                        try {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <option value="<?php echo $row['bankId']; ?>"><?php echo $row['bank_code']." | ".$row['bank_name']." [ ".number_format($row['bankAmount'])." FRW ]"; ?></option>
                                <?php
                            }
                        }
                        catch (PDOException $ex) {
                            //Something went wrong rollback!
                            echo $ex->getMessage();
                        }
                        ?>   
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
             <div class="nk-int-mk">
                <small><strong>Debit charges to account</strong></small>
            </div>
            <div class="nk-int-st">
                <input type="number" name="amount_to_bPaid" id="amount_to_bPaid" class="form-control" placeholder="e.g: 52000" required>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Purpose/ Details of Payment</strong></small>
            </div>
            <div class="nk-int-st">
                <textarea class="form-control" rows="2" name="comment" id="comment" placeholder="Details of Payment"></textarea> 
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Year</strong></small>
            </div>
            <div class="nk-int-st">
                <select class="form-control" name="end_year" id="end_year" style="border:none;" required>
			  <option value="">Year</option>
				<?php
					
					$year = 2018;
					$date_year = date('Y');
					$c = $date_year;
					//$date = date('Y-m-d');
						while ( $year <=  $c)
					{
					
					if ($date_year==$year)
						{
						?>
						<option value="<?php echo $date_year;?>"Selected><?php echo $year;?></option>
						<?php
						}
						else
						{
						?>
						<option value="<?php echo $year;?>"><?php echo $year;?></option>
						
						<?php
						}
					    $year++;
						
					}
					//print "hi";
					?>
				  </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Month</strong></small>
            </div>
            <div class="nk-int-st">
                <select class="form-control" name="end_month" id="end_month" style="border:none;" required>
						  <option value="">Month</option>
							<?php
								$i = 1;
								$month = 12;
								$date_month = date('m');
								//$date = date('Y-m-d');
									while ($i <= $month)
								{
								$i = str_pad($i, 2, 0, STR_PAD_LEFT);
									if($date_month==$i)
										{
										?>
										<option value="<?php echo $date_month;?>"selected><?php echo date('F', mktime(0, 0, 0, $i, 1))?></option>
										
										<?php
										}
										else
										
										{
										?>
										<option value="<?php echo $i;?>"><?php echo date('F', mktime(0, 0, 0, $i, 1))?></option>
										
										<?php
										
										}
									$i++;				
								}
								//print "hi";
							?>
						  </select>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Day</strong></small>
            </div>
            <div class="nk-int-st">
                <select class="form-control" name="end_day" id="end_day" style="border:none;" required>
				<option value="">Day</option>
				<?php
					$i = 1;
					$date_year = date('Y');
					$date_day = date('d');
					//$date = date('Y-m-d');
						while ($i <= 31)
					{
					$i = str_pad($i, 2, 0, STR_PAD_LEFT);
						if($date_day==$i)
							{
							?>
							<option value="<?php echo $date_day;?>"selected><?php echo $i;?></option>
							
							<?php
							}
							else
							
							{
							?>
							<option value="<?php echo $i;?>"><?php echo $i;?></option>
							
							<?php
							
							}
						$i++;
						
					}
					//print "hi";
				?>
			  </select>
            </div>
        </div>
    </div>
</div>

 <?php 
     
 }
 elseif($EXPMODE == "0"){
?>
  <div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Debit Account No</strong></small>
            </div>
            <div class="nk-int-st">
                <select id="fee_ID" name="fee_ID" class="form-control" required style="border:none;">
            <option>Select one...</option>                                   
                 <?php
                        $stmt2 = $db->prepare("SELECT tbl_cash_amount.fee_category_id,tbl_income_category.name,tbl_income_category.id,
						tbl_cash_amount.Cash_amount 
						FROM tbl_income_category 
						INNER JOIN tbl_cash_amount ON tbl_income_category.id=tbl_cash_amount.fee_category_id 
						ORDER BY tbl_cash_amount.Cash_amount DESC ");
						$stmt2->execute();

                        try {
                            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <option value="<?php echo $row2['id']; ?>"><?php echo $row2['name']." [ ".number_format($row2['Cash_amount'])." FRW ]"; ?></option>
                                <?php
                            }
                        }
                        catch (PDOException $ex) {
                            //Something went wrong rollback!
                            echo $ex->getMessage();
                        }
                        ?>   
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Debit Account No</strong></small>
            </div>
            <div class="nk-int-st">
                <input type="number" name="amount_to_bPaid" id="amount_to_bPaid" class="form-control" placeholder="e.g: 52000" required>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Purpose/ Details of Payment</strong></small>
            </div>
            <div class="nk-int-st">
                <textarea class="form-control" rows="2" name="comment" id="comment" placeholder="Details of Payment"></textarea> 
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Year</strong></small>
            </div>
            <div class="nk-int-st">
                <select  class="form-control" name="end_year" id="end_year" required style="border:none;">
			  <option value="">Year</option>
				<?php
					
					$year = 2018;
					$date_year = date('Y');
					$c = $date_year;
					//$date = date('Y-m-d');
						while ( $year <=  $c)
					{
					
					if ($date_year==$year)
						{
						?>
						<option value="<?php echo $date_year;?>"Selected><?php echo $year;?></option>
						<?php
						}
						else
						{
						?>
						<option value="<?php echo $year;?>"><?php echo $year;?></option>
						
						<?php
						}
					    $year++;
						
					}
					//print "hi";
					?>
				  </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Month</strong></small>
            </div>
            <div class="nk-int-st">
                <select class="form-control" name="end_month" id="end_month" required  style="border:none;">
						  <option value="">Month</option>
							<?php
								$i = 1;
								$month = 12;
								$date_month = date('m');
								//$date = date('Y-m-d');
									while ($i <= $month)
								{
								$i = str_pad($i, 2, 0, STR_PAD_LEFT);
									if($date_month==$i)
										{
										?>
										<option value="<?php echo $date_month;?>"selected><?php echo date('F', mktime(0, 0, 0, $i, 1))?></option>
										
										<?php
										}
										else
										
										{
										?>
										<option value="<?php echo $i;?>"><?php echo date('F', mktime(0, 0, 0, $i, 1))?></option>
										
										<?php
										
										}
									$i++;				
								}
								//print "hi";
							?>
						  </select>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group ic-cmp-int">
            <div class="nk-int-mk">
                <small><strong>Day</strong></small>
            </div>
            <div class="nk-int-st">
                <select class="form-control" name="end_day" id="end_day" required style="border:none;">
				<option value="">Day</option>
				<?php
					$i = 1;
					$date_year = date('Y');
					$date_day = date('d');
					//$date = date('Y-m-d');
						while ($i <= 31)
					{
					$i = str_pad($i, 2, 0, STR_PAD_LEFT);
						if($date_day==$i)
							{
							?>
							<option value="<?php echo $date_day;?>"selected><?php echo $i;?></option>
							
							<?php
							}
							else
							
							{
							?>
							<option value="<?php echo $i;?>"><?php echo $i;?></option>
							
							<?php
							
							}
						$i++;
						
					}
					//print "hi";
				?>
			  </select>
            </div>
        </div>
    </div>
</div>     

<?php
}
else{
    echo "Select Payment mode!";   
         }
?>