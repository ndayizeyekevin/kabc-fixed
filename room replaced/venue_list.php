
<div class="colr-area">
        <div class="container">
       
            <div class="layout-page">
        
                <!-- / top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                 

                        <div id="getAlert"></div>

                        <!-- edit venue offcanvas -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="editVenueOffcanvas" aria-labelledby="editVenueLabel">
                            <div class="offcanvas-header">
                                <h5 id="editVenueLabel" class="offcanvas-title">Edit Venue</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <!-- Edit Venue Form -->
                                <form id="edit-venue-form">
                                    <input type="hidden" id="edit_venue_id" />

                                    <div class="mb-3">
                                        <label for="edit_venue_name" class="form-label">Venue Name</label>
                                        <input type="text" class="form-control" id="edit_venue_name" placeholder="Venue Name" required />
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_venue_type" class="form-label">Venue Type</label>
                                        <select class="form-select" id="edit_venue_type" required>
                                            <!-- Venue types will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_capacity" class="form-label">Capacity</label>
                                        <input type="number" class="form-control" id="edit_capacity" placeholder="200" required />
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="edit_location" placeholder="1234 Street, City" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_status" class="form-label">Status</label>
                                        <select class="form-select" id="edit_status" required>
                                            <!-- Status options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_amenities" class="form-label">Amenities</label>
                                        <div id="edit_amenities_container" class="mb-2"></div>
                                        <select class="form-select" id="edit_amenity" aria-label="Select Amenity">
                                            <!-- Amenities options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="offcanvas-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Venues List</h5>
                                        <button type="button" id="addVenueModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVenueModal">
                                            <i class="bx bx-plus"></i> Add Venue
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Venues List -->
                                        <h2>Venues List</h2>
                                        <table id="venues-list" class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Capacity</th>
                                                    <th>Amenities</th>
                                                    <th>Location</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Venues data will be populated here -->
                                            </tbody>
                                        </table>

                                        <!-- Pagination -->
                                        <nav class="mt-2" aria-label="Page navigation">
                                            <ul class="pagination" id="pagination">
                                                <!-- Pagination links will be populated here -->
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

    <!-- Modal -->
    <!-- add venue -->
    <div class="modal fade" id="addVenueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Venue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="create-venue-form">
                        <div class="mb-3">
                            <label class="form-label" for="venue_name">Venue Name</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_name_label" class="input-group-text"><i class="bx bx-rename"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="venue_name"
                                    placeholder="Grand Hall"
                                    aria-label="Grand Hall"
                                    aria-describedby="venue_name_label"
                                    required />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="venue_type">Venue Type</label>
                            <div class="input-group input-group-merge">
                                <span id="venue_type_label" class="input-group-text"><i class="bx bx-building"></i></span>
                                <select
                                    class="form-control"
                                    id="venue_type"
                                    aria-label="Venue Type"
                                    aria-describedby="venue_type_label"
                                    required>
                                    <!-- Options will be populated dynamically -->
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="capacity">Capacity</label>
                            <div class="input-group input-group-merge">
                                <span id="capacity_label" class="input-group-text"><i class="bx bx-group"></i></span>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="capacity"
                                    placeholder="200"
                                    aria-label="200"
                                    aria-describedby="capacity_label"
                                    required />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="amenities">Amenities</label>
                            <div id="amenities_container" style="margin-top: 3px;margin-bottom:3px"> <!-- Selected amenities will be displayed here as badges --> </div>
                            <div class="input-group input-group-merge">
                                <span id="amenities_label" class="input-group-text"><i class="bx bx-list-ul"></i></span>
                                <select
                                    class="form-control"
                                    id="amenity"
                                    aria-label="Amenity"
                                    aria-describedby="amenities_label">
                                    <!-- Options will be populated dynamically -->
                                </select>
                            </div>
                            <div id="amenities_error" class="invalid-feedback mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="location">Location</label>
                            <div class="input-group input-group-merge">
                                <span id="location_label" class="input-group-text"><i class="bx bx-map"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="location"
                                    placeholder="1234 Street, City"
                                    aria-label="Location"
                                    aria-describedby="location_label" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <div class="input-group input-group-merge">
                                <span id="status_label" class="input-group-text"><i class="bx bx-info-circle"></i></span>
                                <select
                                    class="form-control"
                                    id="status"
                                    aria-label="Status"
                                    aria-describedby="status_label"
                                    required>
                                    <!-- Options will be populated dynamically -->
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create Venue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal end -->

        <div id="showToast" class="toast-container position-relative"></div>

        <!-- Footer -->
        <?php include_once "../../partials/footer.php"; ?>
        <script src="js/venues.js"></script>
</body>

</html>