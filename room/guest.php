
      <div class="colr-area">
        <div class="container">
            <!-- Side Menu -->
           
            <!-- / Side Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Top Navbar -->
                
                <!-- / Top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Breadcrumbs -->
                       

                        <div id="getCustomerAlert"></div>

                        <!-- Customer List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Guest List</h5>
                                      
                                    </div>
                                    <div class="card-body">
                                        <div class="py-3 table-responsive">
                                            <table  class="table table-hover" border="2">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Date of Birth</th>
                                                        <th>Place of Birth</th>
                                                        <th>Nationality</th>
                                                        <th>Identification</th>
                                                        <th>Residence</th>
                                                        <th>Address</th>
                                                        <th>Passport Number</th>
                                                        <th>Passport Expiration date</th>
                                                        <th>Created At</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
												
<?php 
include '../inc/conn.php';
$no = 0;
$sql = "SELECT * FROM tbl_acc_guest";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
	?>
	<tr>
	<td><?php echo $no = $no + 1?></td>
	<td><?php echo $row['first_name']." ".$row['last_name']?></td>
	<td><?php echo $row['email_address']?></td>
	<td><?php echo $row['phone_number']?></td>
	<td><?php echo $row['date_of_birth']?></td>
	<td><?php echo $row['place_of_birth']?></td>
	<td><?php echo $row['nationality']?></td>
	<td><?php echo $row['identification']?></td>
	<td><?php echo $row['residence']?></td>
	<td><?php echo $row['address']?></td>
	<td><?php echo $row['passport_expiration_date']?></td>
	<td><?php echo $row['passport_expiration_date']?></td>
	<td><?php echo $row['created_at']?></td>
	<td><a href="">Edit</a> <a href="">Delete</a></td>
</tr>	
                                                  
<?php }}?>                    </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for adding a customer -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerLabel">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customer-form">
                        <input type="hidden" name="customer_id" id="customer_id">
                        <div class="mb-3">
                            <label for="names" class="form-label">Name</label>
                            <input type="text" class="form-control" id="names" placeholder="Customer Name" required />
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" placeholder="Address" required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Email" required />
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" placeholder="Phone" required />
                        </div>
                        <div class="mb-3">
                            <label for="identification" class="form-label">Identification</label>
                            <input type="text" class="form-control" id="identification" placeholder="Identification" />
                        </div>
                        <div class="mb-3">
                            <label for="tin" class="form-label">TIN</label>
                            <input type="text" class="form-control" id="tin" placeholder="TIN" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary create-customer">Create Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>

    <script src="js/customers.js"></script> <!-- Your custom JavaScript file -->
