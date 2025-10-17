

<div class="colr-area">
        <div class="container">
           
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
                                        <h5 class="mb-0">Register Bed Types</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="bed-type-form">
                                            <input type="hidden" name="bed_type_id" id="bed_type_id">
                                            <div class="mb-3">
                                                <label class="form-label" for="type_name">Bed Type Name</label>
                                                <div class="input-group input-group-merge">
                                                    <span id="type_name_label" class="input-group-text"><i class="bx bx-rename"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="type_name"
                                                        placeholder="King Size"
                                                        aria-label="King Size"
                                                        aria-describedby="King Size" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Bed Type</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Bed Type List</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Bed Type Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bed-type-list">
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
   
    <script src="js/bed_type.js"></script>
</body>

</html>
