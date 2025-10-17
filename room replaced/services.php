
<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-free">
<!-- header -->
<?php 


$room_payments= 0;
$today = date('Y-m-d');
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
		 $room_payments = $room_payments + $row['total'];  
}}

$venue_payments = 0;
$sql = "SELECT * FROM venu_orders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
	  
	  
		 $venue_payments= $venue_payments + $row['total']; 
	 	  
}}





$orders = 0;
$sql = "SELECT * FROM  orders ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
$orders  =  $orders + 1;
		
}}





$venu_orders = 0;
$sql = "SELECT * FROM venu_orders ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  
  
$venu_orders  =  $venu_orders +1 ;
	
	   

	  	
}}






	?>
	






<!-- / header -->

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- side Menu -->
      <?php include_once "../../partials/side_nav.php"; ?>
      <!-- / side Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- top Navbar -->
        <?php include_once "../../partials/top_nav.php"; ?>
        <!-- / top Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y">
            <div id="showAlert"></div>
            <!-- breadcrumbs -->
            <p><?php include '../../partials/breadcrumb.php'; ?></p>

            <div class="row">
              <div class="col-lg-12 col-md-12 order-1">
                <div class="row">
                  <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <!-- <img
                                src="../../assets/img/icons/unicons/chart-success.png"
                                alt="chart success"
                                class="rounded" 
                              /> -->
                            <span class="text-success"><i style="font-size: 30px;" class="bx bxs-bookmarks"></i></span>
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt3"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Room Order</span>
                        <h3 class="card-title mb-2"><?php 
						


echo number_format($orders);
						
						
						?></h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
                      </div>
                    </div>
                  </div>
				  
				  
				      <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <!-- <img src="../../assets/img/icons/unicons/paypal.png" alt="Credit Card" class="rounded" /> -->
                            <span class="text-warning "><i style="font-size: 30px;" class="bx bxs-user"></i></span>
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt4"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span class="d-block mb-1">Total Venues Order</span>
                     <h3 class="card-title text-nowrap mb-2">
				  
				  <?php  echo number_format($venu_orders);?></h3>
                        <small class="text-warning fw-semibold"><i class="bx bx-up-arrow-alt"></i> +3%</small>
                      </div>
                    </div>
                  </div>
				  
                  <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <img
                              src="../../assets/img/icons/unicons/wallet-info.png"
                              alt="Credit Card"
                              class="rounded" />
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt6"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span>Total Room Amount</span>
                        <h3 class="card-title text-nowrap mb-1"><?php 
						
						echo number_format($room_payments);
						
						?> RWF</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
                      </div>
                    </div>
                  </div>
				  
				  
              
                  <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <div class="card">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <div class="avatar flex-shrink-0">
                            <img src="../../assets/img/icons/unicons/cc-primary.png" alt="Credit Card" class="rounded" />
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="cardOpt1"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="cardOpt1">
                              <a class="dropdown-item" href="javascript:void(0);">View More</a>
                              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                          </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Revenue</span>
                        <h3 class="card-title mb-2"><?php 
						
						echo number_format($venue_payments);
						
						?> RWF</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

       <br>     <br>
			    <!-- Basic Bootstrap Table -->
            <div class="card">
              <h5 class="card-header">Room Orders</h5>
              <div class="text-nowrap">
                 <table  class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>QTY</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                   
                                                  
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
											<?php 
											
											$sum = 0;
											$total = 0;
											$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  	$total = 	$total  + $row['total'];
	 
	?>
	
	<tr>
                                                    <td><?php echo $sum = $sum + 1;?></td>
                                                    <td><?php echo getServiceName($row['name']);?></td>
													 <td><?php echo $row['qty'];?></td>
                                                    <td><?php echo  number_format($row['price']);?></td>
													<td><?php echo  number_format($row['total']);?></td>
                                                    
                                                
                                                  
                                                    <th><a href="delete.php?id=<?php echo $row['order_id']?>">Delete</a></th>
                                                </tr>
	
<?php	
	
  }
}
	?>										
											
             	<tr>
                                                    <td>Grand Total:</td>
                                                    <td></td>
													 <td></td>
                                                    <td></td>
                                                    <td></td>
													<td><?php echo number_format($total);?> RWF</td>
                                                    
                                                
                                                  
                                                   
                                                </tr>                                     
                                            </tbody>
                                        </table>
              </div>
            </div>
            <!--/ Basic Bootstrap Table -->
			
			     <br>     <br>
			    <!-- Basic Bootstrap Table -->
            <div class="card">
              <h5 class="card-header">Venue Orders</h5>
              <div class="text-nowrap">
                <table  class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>QTY</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                   
                                                  
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
											
                                            <tbody>
											<?php 
											
											$sum = 0;
											$total = 0;
											$sql = "SELECT * FROM venu_orders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	  	$total = 	$total  + $row['total'];
	 
	?>
	
	<tr>
                                                    <td><?php echo $sum = $sum + 1;?></td>
                                                    <td><?php echo getServiceName($row['names']);?></td>
													 <td><?php echo $row['qty'];?></td>
                                                    <td><?php echo  number_format($row['price']);?></td>
													<td><?php echo  number_format($row['total']);?></td>
                                                    
                                                
                                                  
                                                    <th><a href="delete.php?id=<?php echo $row['order_id']?>">Delete</a></th>
                                                </tr>
	
<?php	
	
  }
}
	?>										
											
             	<tr>
                                                    <td>Grand Total:</td>
                                                    <td></td>
													 <td></td>
                                                    <td></td>
                                                    <td></td>
													<td><?php echo number_format($total);?> RWF</td>
                                                    
                                                
                                                  
                                                   
                                                </tr>                                     
                                            </tbody>
                                        </table>
              </div>
            </div>
            <!--/ Basic Bootstrap Table -->
			
			
			
										
			
			
            <!--/ Basic Bootstrap Table -->
          </div>
          <!-- / Content -->
        </div>
      </div>
    </div>
  </div>

  <div id="showToast" class="toast-container position-relative"></div>

  <!-- Footer -->

</body>

</html>