
<div class="colr-area">
        <div class="container">
            <!-- side Menu -->
           
            <!-- / side Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- top Navbar -->
                
                <!-- / top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">                  
                        <!-- breadcrumbs -->
                       

                        <div id="getAlert"></div>

                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Register Room Class Bed Types</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="class-bed-type-form">
                                            <input type="hidden" name="class_bed_type_id" id="class_bed_type_id">

                                            <div class="mb-3">
                                                <label class="form-label" for="room_class_id">Room Class</label>
                                                <select id="room_class_id" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="bed_type_id">Bed Type</label>
                                                <select id="bed_type_id" class="form-control">
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="num_beds">Number of Beds</label>
                                                <input type="number" class="form-control" id="num_beds" placeholder="Enter number of beds" required>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Add Class Bed Type</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Class Bed Type List</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Room Class</th>
                                                    <th>Bed Type</th>
                                                    <th>Number of Beds</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="class-bed-type-list">
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

    <div id="showToast" class="toast-container position-relative"></div>

    <!-- Footer -->
   
    <script src="js/room_class_bed_type.js"></script>
</body>

</html>
