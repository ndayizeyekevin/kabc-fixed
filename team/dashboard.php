<!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-windows"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Company Leases</h2>
										<p>Total 
										    <?php 
										     $SQL = $conn->prepare("SELECT COUNT(cpny_ID) as total_leases FROM tbl_company WHERE cpny_status = 'ON'");
										     $SQL->execute();
										     if($SQL->rowCount() > 0){
										         $QUERY =  $SQL->fetch();
										         echo $QUERY['total_leases'];
										     }else{
										     echo "0";
										     }
										    ?>
										</p>
									</div>
								</div>
							</div>
							  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button data-toggle="tooltip" data-placement="left" title="Download Leases" class="btn"><i class="notika-icon notika-sent"></i></button>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->