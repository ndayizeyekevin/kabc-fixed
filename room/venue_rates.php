
<div class="colr-area">
        <div class="container">
            <!-- Layout container -->
            <div class="layout-page">
               

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                
                        <div id="getAlert"></div>

                        <!-- handle venue rate types canvas -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="venueRateTypesOffcanvas" aria-labelledby="venueRateTypesLabel" style="width: 40%">
                            <div class="offcanvas-header">
                                <h5 id="venueRateTypesLabel" class="offcanvas-title">Handle Venue Rate Types</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <div id="getRateAlert"></div>
                                <!-- venue rate type form -->
                                <form id="venue-rate-type-form">
                                    <input type="hidden" id="venue_rate_type_id" />

                                    <div class="mb-3">
                                        <label for="venue_rate_type_name" class="form-label">Type Name</label>
                                        <input type="text" class="form-control" id="venue_rate_type_name" placeholder="Rate Type Name" required />
                                    </div>

                                    <div class="offcanvas-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                                <!-- Rates type list -->
                                <div class="mt-3">
                                    <h2>Venue Rates List</h2>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Type Name</th>
                                                <th>Date Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="venue-rate-types-list">
                                            <!-- Venue rates types data will be dynamically populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Venue Rates List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Venue Rates List</h5>
                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                            <button type="button" id="addVenueRateModalBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addVenueRateModal">
                                                <i class="bx bx-plus"></i> Add Rate
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#venueRateTypesOffcanvas" aria-controls="venueRateTypesOffcanvas">
                                                Handle Rate Types
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- <h2>Venue Rates List</h2> -->
                                        <table id="venueRatesTable" class="table table-hover table-bordered table-border-bottom-1 table-border-top-1 table-stripped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Venue</th>
                                                    <th>Rate Type</th>
                                                    <th>Amount</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Venue rates data will be dynamically populated here -->
                                            </tbody>
                                        </table>

                                        <!-- Pagination -->
                                        <nav class="mt-2" aria-label="Page navigation">
                                            <ul class="pagination" id="pagination">
                                                <!-- Pagination links will be dynamically populated here -->
                                            </ul>
                                        </nav>
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

    <!-- Modal for adding a venue rate -->
    <div class="modal fade" id="addVenueRateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVenueRateLabel">Add Venue Rate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="venue-rate-form">
                        <input type="hidden" name="venue_rate_id" id="venue_rate_id">
                        <div class="mb-3">
                            <label for="venue_id" class="form-label">Venue</label>
                            <select class="form-select" id="venue_id" required>
                                <!-- Venue options will be dynamically populated -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Rate Type</label>
                            <select class="form-select" id="rate_type_id" required>
                                <!-- Venue Rate types options will be dynamically populated -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" placeholder="1000" required />
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" required />
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" required />
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary create-rate">Create Rate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>


    <script src="js/venue_rates.js"></script>
    <script src="js/venue_rate_types.js"></script>
</body>

</html>