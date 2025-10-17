
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
                                        <h5 class="mb-0">Register Room Blocks</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="block-form">
                                            <input type="hidden" name="block_id" id="block_id">
                                            <div class="mb-3">
                                                <label class="form-label" for="block_name">Block Name</label>
                                                <div class="input-group input-group-merge">
                                                    <span id="block_name_label" class="input-group-text"><i class="bx bx-rename"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="block_name"
                                                        placeholder="East Wing"
                                                        aria-label="East Wing"
                                                        aria-describedby="East Wing" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Block</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Block List</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Block Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="block-list">
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
   
    <script src="js/room_block.js"></script>
</body>

</html>
