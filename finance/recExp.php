
 <?php
       //INSERT NEW EXPENSE
			if(isset($_POST["exp_btn"]))
			{
			
			$amount_to_bPaid = $_POST['amount_to_bPaid'];
			$end_year = $_POST['end_year'];				
			$end_month = $_POST['end_month'];				
			$end_day = $_POST['end_day'];
			$comment = $_POST['comment'];
			$exp_Mode = $_POST['exp_Mode'];
			
			$bn_name = $_POST['bn_name'];
			$bn_phone = $_POST['bn_phone'];
			$bn_address = $_POST['bn_address'];
			
			$Today_date = date('Y-m-d H:i:s', time());
			$pymntDate = $end_year."-".$end_month."-".$end_day;
			$Today_date_time = date("Y-m-d H:i:s", time());
			
			if($exp_Mode==1){
			$fee_ID = 0;
			$bank_ID = $_POST['bank_ID'];
			
			$save_Expnese_pymnt = $db->prepare("INSERT INTO tbl_funds_transfer(bankId,fee_ID,expenseAmount,expenseDescr,expenseDate,bn_name,bn_phone,bn_address,expMode) 
			VALUES(?,?,?,?,?,?,?,?,?)");
			$save_Expnese_pymnt->execute(array($bank_ID,$fee_ID,$amount_to_bPaid,$comment,$pymntDate,$bn_name,$bn_phone,$bn_address,$exp_Mode));
			
			//SELECT QUERY
			$stms_amount = $db->prepare("SELECT SUM(tbl_bank_amount.bankAmount) AS BANKAMOUNT 
			FROM tbl_bank_amount 
			WHERE tbl_bank_amount.bankId=? 
			GROUP BY tbl_bank_amount.bankId ");

			try {

				$stms_amount->execute(array($bank_ID));
				$row_count_amount = $stms_amount->rowCount();
				if ($row_count_amount > 0)
					{
					$rows_exp = $stms_amount->fetch(PDO::FETCH_ASSOC);
						$BANKAMOUNT = $rows_exp['BANKAMOUNT'];
						
						$NewAmnt=$BANKAMOUNT-$amount_to_bPaid;
						$updateSrvcCstStatus= $db->prepare("UPDATE tbl_bank_amount SET bankAmount=? WHERE bankId=? ");			
						$updateSrvcCstStatus->execute(array($NewAmnt,$bank_ID));
						
					}
				else
					{
						
					}
			}
			catch (PDOException $ex) {
				echo $ex->getMessage();
			}
			}elseif($exp_Mode==0){
			$fee_ID = $_POST['fee_ID'];
			$bank_ID = 0;
			
	    	$save_Expnese_pymnt2 = $db->prepare("INSERT INTO tbl_funds_transfer(bankId,fee_ID,expenseAmount,expenseDescr,expenseDate,bn_name,bn_phone,bn_address,expMode) 
			VALUES(?,?,?,?,?,?,?,?,?)");
			$save_Expnese_pymnt2->execute(array($bank_ID,$fee_ID,$amount_to_bPaid,$comment,$pymntDate,$bn_name,$bn_phone,$bn_address,$exp_Mode));
			
			//SELECT QUERY
			$stms_amount2 = $db->prepare("SELECT SUM(tbl_cash_amount.Cash_amount) AS CASHAMOUNT 
			FROM tbl_cash_amount 
			WHERE tbl_cash_amount.fee_category_id=? 
			GROUP BY tbl_cash_amount.fee_category_id ");

			try {

				$stms_amount2->execute(array($fee_ID));
				$row_count_amount2 = $stms_amount2->rowCount();
				if ($row_count_amount2 > 0)
					{
					$rows_exp2 = $stms_amount2->fetch(PDO::FETCH_ASSOC);
						$CASHAMOUNT = $rows_exp2['CASHAMOUNT'];
						
						$NewAmnt2=$CASHAMOUNT-$amount_to_bPaid;
						$updateSrvcCstStatus2= $db->prepare("UPDATE tbl_cash_amount SET Cash_amount=? WHERE fee_category_id=? ");			
						$updateSrvcCstStatus2->execute(array($NewAmnt2,$fee_ID));
						
					}
				else
					{
						
					}
			}
			catch (PDOException $ex) {
				echo $ex->getMessage();
			}
			}else{
			$fee_ID = 0;
			$bank_ID = 0;
			}
		    $msg = "Recorded Successfully!";
			echo'<meta http-equiv="refresh"'.'content="4;URL=?onkey=expenses">';

			}
			
   ?>
   
 <!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
		    
		    <?php if($msg){?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
              </div>
            <?php } 
             else if($msge){?>
                 
             <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
              </div>
            <?php } ?>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="fa fa-cog"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Manage Expenses</h2>
										<p>Welcome to <?php echo $Rname;?> <span class="bread-ntd">Panel</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->  
<div class="form-element-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="basic-tb-hd">
                            <strong>Record Expenses</strong>
                        </div>
                        <form action="" method="POST">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group ic-cmp-int">
                                    <div class="nk-int-mk">
                                    <small><strong>Beneficiary Name</strong></small>
                                </div>
                                    <div class="nk-int-st">
                                        <input type="text" name="bn_name" class="form-control" placeholder="Beneficiary Name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group ic-cmp-int">
                                    <div class="nk-int-mk">
                                    <small><strong>Phone Number</strong></small>
                                </div>
                                    <div class="nk-int-st">
                                        <input type="number" name="bn_phone" class="form-control" placeholder="eg: 078...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group ic-cmp-int">
                                    <div class="nk-int-mk">
                                    <small><strong>Beneficiary Post</strong></small>
                                </div>
                                    <div class="nk-int-st">
                                        <input type="text" name="bn_address" class="form-control" placeholder="Type Address">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group ic-cmp-int">
                                <div class="nk-int-mk">
                                    <small><strong>Payment Mode</strong></small>
                                </div>
                                    <div class="nk-int-st">
                                         <select id="exp_Mode" name="exp_Mode" class="selectpicker" data-live-search="true" required onchange=AjaxFunction();>
											<option>Select Payment Mode</option>
                                             <option value="1">Bank Slip</option>
                                             <option value="0">Cash</option>
									    </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div name='display_expMode' id='display_expMode'>
                	
                	    </div>
                        <div class="row">
                            <br>
                            <center>
							  <div class="box-footer">
								<button type="reset" class="btn btn-default"><i class="fa fa-fw fa-remove"></i> Reset</button>
				                <button type="submit" class="btn btn-primary" name="exp_btn" id="record"><i class="fa fa-fw fa-save"></i> Record</button>
							  </div>
    						</center>
                        </div>
                       </form>
                       <br><br>
                        <div class="table-responsive">
						   <table id="data-table-basic" class="table table-striped">
								<thead>
									<tr>
									    <th>#</th>
										<th>Beneficiary Name</th>
										<th>Beneficiary Address</th>
										<th>Beneficiary Telephone</th>
										<th>Debit Bank</th>
										<th>Debit Fee</th>
										<th>Amount</th>
										<th>Purpose/Details of Payment</th>
										<th>Recorded Date</th>
										<th>Pay Mode</th>
										<!--<th>Action</th> -->
									</tr>
						    	</thead>
						    	
								<tbody>
								<?php
								    $i=0;
        							$totalExp=0;
        							//SELECT EXPENSES
        							$stms_exp = $db->prepare("SELECT *FROM tbl_funds_transfer ORDER BY expenseId ASC");
        
        							try {
        
        								$stms_exp->execute(array());
        								$row_count_exp = $stms_exp->rowCount();
        								
        									while ($rows_exp = $stms_exp->fetch(PDO::FETCH_ASSOC)) {
        										$expenseId = $rows_exp['expenseId'];
        										$expenseAmount = $rows_exp['expenseAmount'];
        										$expenseDescr = $rows_exp['expenseDescr'];
        										$expenseDate = $rows_exp['expenseDate'];
        										$bankId = $rows_exp['bankId'];
        										$fee_ID = $rows_exp['fee_ID'];
        										$expMode = $rows_exp['expMode'];
        										$totalExp=$totalExp+$expenseAmount;
        								        
        								        // Additonal
        										$bn_name = $rows_exp['bn_name'];  
        										$bn_address = $rows_exp['bn_address']; 
        										$bn_phone = $rows_exp['bn_phone'];
        										$i++;
        										
        							?>
        							<tr>
        							<td><?php echo $i; ?></td>
        							<td><?php echo $bn_name;?></td>
        							<td><?php echo $bn_address;?></td>
        							<td><?php echo $bn_phone;?></td>
        							<td>
        							<?php 
        							if($bankId !=0){
        							    $CRUD = $db->prepare("SELECT *FROM tbl_bank WHERE bank_id = '".$bankId."' ");
        							    $CRUD->execute();
        							    $QCRUD = $CRUD->fetch();
        							    echo $QCRUD['bank_code']." | ".$QCRUD['bank_name'];
        							}else{
        							    echo "No data";
        							 }
        							?></td>
        							<td>
        							<?php 
        							if($fee_ID !=0){
        							    $CRUD2 = $db->prepare("SELECT *FROM tbl_income_category WHERE id = '".$fee_ID."' ");
        							    $CRUD2->execute();
        							    $QCRUD2 = $CRUD2->fetch();
        							    echo $QCRUD2['name'];
        							}else{
        							    echo "No data";
        							 }
        							?>
        							</td>
        							<td><?php echo number_format($expenseAmount)." FRW";?></td>
        							<td><?php echo $expenseDescr;?></td>
        							<td><?php echo $expenseDate;?></td>
        							<td><?php
        							if($expMode == 1){
        							echo "Bank Slip";
        							}elseif($expMode == 0){
        							  echo "Cash";
        							}else{
        							    echo "No Data";
        							}
        							?></td>
        						
        							</tr>		
        							<?php 
        							}
        							?>
								</tbody>
							</table>
							<div class="invoice-summary">
							<div class="row">
								<div class="col-sm-4 col-sm-offset-8">
									<table id="data-table-basic" class="table table-striped">
										<tbody>
											<tr class="h4">
												<td colspan="2">G. Total :</td>
												<td class="text-left"><span class="small small-info"> <?php echo number_format($totalExp)." FRW"; ?></span></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						
						 <?php
    							
        					}
        					catch (PDOException $ex) {
        						echo $ex->getMessage();
        					}
						?>
						</div>
							
						
                    </div>
                </div>
            </div>
        </div>
    </div>    
     <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
  <script type="text/javascript">
    $(document).ready(function () {

        $("#exp_Mode").change(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_expMode.php?exp_Mode=' + $(this).val() , function (data) {
             $("#display_expMode").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>
   