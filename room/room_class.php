
<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template-free">
<!-- header -->

<!-- / header -->

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
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
                                        <h5 class="mb-0">Register Room Classes</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="class-form">
                                            <input type="hidden" name="class_id" id="class_id">
                                            <div class="mb-3">
                                                <label class="form-label" for="class_name">Class Name</label>
                                                <div class="input-group input-group-merge">
                                                    <span id="class_name_label" class="input-group-text"><i class="bx bx-rename"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="class_name"
                                                        placeholder="Deluxe"
                                                        aria-label="Deluxe"
                                                        aria-describedby="Deluxe" />
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="base_price">Base Price</label>
                                                <div class="input-group input-group-merge">
                                                    <span id="base_price_label" class="input-group-text"><i class="bx bx-dollar-circle"></i></span>
                                                    <input
                                                        type="number"
                                                        class="form-control"
                                                        id="base_price"
                                                        placeholder="100"
                                                        aria-label="100"
                                                        aria-describedby="100" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Class</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Class List</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Class Name</th>
                                                    <th>Base Price</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="class-list">
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
   
    <script src="js/room_class.js"></script>
</body>

</html>
