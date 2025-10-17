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
                            <input type="text" class="form-control" id="address" placeholder="Address" />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Email" />
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

<div class="colr-area">
        <div class="container">
                        <div id="getCustomerAlert"></div>

                        <!-- Customer List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Customer List</h5>
                                        <button type="button" id="addCustomerModalBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                            <i class="bx bx-plus"></i> Add Customer
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="py-3 table-responsive">
                                            <table id="customersTable" class="table table-hover" border="2">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Address</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Identification</th>
                                                        <th>TIN</th>
                                                        <th>Created At</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="customers-list">
                                                    <!-- Customer data will be dynamically populated here -->
                                                </tbody>
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
 

    <div id="showToast" class="toast-container position-relative"></div>

    <script src="js/customers.js"></script> <!-- Your custom JavaScript file -->

