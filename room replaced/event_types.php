
<div class="colr-area">
        <div class="container">

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Top Navbar -->
               

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Breadcrumbs -->
                       
                        <div id="getEventAlert"></div>

                        <!-- Event Type List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Event Type List</h5>
                                        <button type="button" id="addEventTypeModalBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addEventTypeModal">
                                            <i class="bx bx-plus"></i> Add Event Type
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <table id="eventTypesTable" class="table table-hover table-bordered table-border-bottom-1 table-border-top-1 table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Event Name</th>
                                                    <th>Event Code</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="event-types-list">
                                                <!-- Event type data will be dynamically populated here -->
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
    <!-- Modal for adding an event type -->
    <div class="modal fade" id="addEventTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventTypeLabel">Add Event Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="event-type-form">
                        <input type="hidden" name="event_id" id="event_id">
                        <div class="mb-3">
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="event_name" placeholder="Event Name" required />
                        </div>
                        <div class="mb-3">
                            <label for="event_code" class="form-label">Event Code</label>
                            <input type="text" class="form-control" id="event_code" placeholder="Event Code" required />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary create-event">Create Event Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>

    <!-- Footer -->
    <?php include_once "../../partials/footer.php"; ?>
    <script src="js/event_types.js"></script>
</body>

</html>