
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
                                        <h5 class="mb-0">Register Room Features</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="feature-form">
                                            <input type="hidden" name="feature_id" id="feature_id">
                                            <div class="mb-3">
                                                <label class="form-label" for="feature_name">Feature Name</label>
                                                <div class="input-group input-group-merge">
                                                    <span id="feature_name_label" class="input-group-text"><i class="bx bx-rename"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="feature_name"
                                                        placeholder="Air Conditioning"
                                                        aria-label="Air Conditioning"
                                                        aria-describedby="Air Conditioning" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Feature</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Feature List</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Feature Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="feature-list">
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
   
    <script src="js/room_feature.js"></script>
</body>

</html>
