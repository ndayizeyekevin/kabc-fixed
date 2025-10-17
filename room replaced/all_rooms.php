	<div class="breadcomb-area">
		<div class="container">
                            <div class="col-lg-12">
                                <div class="card mb-12">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Register Room</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="room-form">
                                            <input type="hidden" name="room_id" id="room_id">

                                            <div class="mb-3">
                                                <label class="form-label" for="room_number">Room Number</label>
                                                <input type="text" class="form-control" id="room_number" placeholder="Enter room number" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="room_class_id">Room Class</label>
                                                <select id="room_class_id" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="block_id">Room Block</label>
                                                <select id="block_id" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="status_id">Room Status</label>
                                                <select id="status_id" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="capacity">Capacity</label>
                                                <input type="number" class="form-control" id="capacity" placeholder="Enter room capacity" required>
                                            </div>

                                            <!-- <div class="mb-3">
                                                <label class="form-label" for="base_price">Base Price</label>
                                                <input type="number" class="form-control" id="base_price" placeholder="Enter base price" required>
                                            </div> -->

                                            <button type="submit" class="btn btn-primary">Add Room</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <!-- Advanced Search and Filters -->
                                <div class="card mb-12">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Search and Filter Rooms</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="search-filter-form" class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label" for="search-room-number">Room Number</label>
                                                <input type="text" class="form-control" id="search-room-number" placeholder="Enter room number">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="search-room-class">Room Class</label>
                                                <select id="search-room-class" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="search-block">Block</label>
                                                <select id="search-block" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="search-status">Status</label>
                                                <select id="search-status" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="search-capacity">Capacity</label>
                                                <input type="number" class="form-control" id="search-capacity" placeholder="Enter capacity">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="search-price-min">Min Price</label>
                                                <input type="number" class="form-control" id="search-price-min" placeholder="Enter minimum price">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="search-price-max">Max Price</label>
                                                <input type="number" class="form-control" id="search-price-max" placeholder="Enter maximum price">
                                            </div>
                                            <div class="col-md-3 align-self-end">
                                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i>Search</button>
                                                <button type="button" class="btn btn-secondary" id="clear-filters"><i class="bx bx-trash-alt"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- search filter end -->
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Room List</h5>
                                        <!-- Export buttons -->
                                        <div class="mb-4"> 
                                            <!-- Export to CSV Button -->
                                            <!--<button id="export-csv" class="btn btn-success">Export to CSV</button> -->
                                            <!-- Export to Excel Button -->
                                            <!--<button id="export-excel" class="btn btn-primary">Export to Excel</button>-->
                                            <!-- Export to PDF Button -->
                                            <button id="export-pdf" class="btn btn-danger">Export to PDF</button>
                                        </div>                                       

                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Room Number</th>
                                                    <th>Room Class</th>
                                                    <th>Block</th>
                                                    <th>Status</th>
                                                    <th>Capacity</th>
                                                    <th>Base Price</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="room-list">
                                                <!-- Data will be populated here by JavaScript -->
                                            </tbody>
                                        </table>
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

    <!-- Room Details Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="roomDetailsOffcanvas" aria-labelledby="roomDetailsOffcanvasLabel" style="width: 20%;">
        <div class="offcanvas-header">
            <h5 id="roomDetailsOffcanvasLabel" class="offcanvas-title">Room Details</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p><strong>Room Number:</strong> <span id="detail-room-number"></span></p>
            <p><strong>Room Class:</strong> <span id="detail-room-class"></span></p>
            <p><strong>Block:</strong> <span id="detail-block"></span></p>
            <p><strong>Status:</strong> <span id="detail-status"></span></p>
            <p><strong>Capacity:</strong> <span id="detail-capacity"></span></p>
            <p><strong>Base Price:</strong> <span id="detail-base-price"></span></p>
            <p><strong>Features:</strong> <span id="detail-features"></span></p>
            <p><strong>Bed Types:</strong> <span id="detail-bed-types"></span></p>
            <p><strong>Number of Beds:</strong> <span id="detail-num-beds"></span></p>

        </div>
    </div>

    <!-- Room Availability Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="roomAvailabilityOffcanvas" aria-labelledby="roomAvailabilityOffcanvasLabel" style="width: 50%;">
        <div class="offcanvas-header">
            <h5 id="roomAvailabilityOffcanvasLabel" class="offcanvas-title">Room Availability</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Calendar or Availability Chart will be rendered here -->
            <div id="room-availability-calendar"></div>
        </div>
    </div>

    <!-- Room Maintenance Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="roomMaintenanceOffcanvas" aria-labelledby="roomMaintenanceOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 id="roomMaintenanceOffcanvasLabel" class="offcanvas-title">Room Maintenance</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="maintenance-form">
                <input type="hidden" name="room_id" id="maintenance-room-id">
                <div class="mb-3">
                    <label class="form-label" for="maintenance-date">Maintenance Date</label>
                    <input type="date" class="form-control" id="maintenance-date" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="maintenance-issue">Issue Description</label>
                    <textarea class="form-control" id="maintenance-issue" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Maintenance</button>
            </form>
            <hr>
            <h5>Maintenance History</h5>
            <ul id="maintenance-list">
                <!-- Maintenance history will be populated here by JavaScript -->
            </ul>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>

 <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
    <script src="js/all_rooms.js"></script>
</body>

</html>