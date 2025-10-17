
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template-free">
<!-- Header -->

<!-- / Header -->

<body>
<div class="colr-area">
        <div class="container">
            <!-- Side Menu -->
           
            <!-- / Side Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Top Navbar -->
                
                <!-- / Top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Breadcrumbs -->
                       

                        <div  hidden id="getReservationAlert"></div>

                        <!-- Offcanvas for reservation details -->
                        <div class="offcanvas offcanvas-end" hidden tabindex="-1" id="reservationDetailsOffcanvas" aria-labelledby="reservationDetailsLabel" style="width: 70%">
                            <div class="offcanvas-header">
                                <h5 id="reservationDetailsLabel" class="offcanvas-title">Reservation Details</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <div id="getReservationDetailsAlert"></div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 id="reservationDetails"></h6>
                                        <div class="d-flex justify-content-end my-3">
                                            <button type="button" id="confirm-reservation-btn" class="btn btn-success btn-sm mx-1">Confirm</button>
                                            <button type="button" id="fulfill-reservation-btn" class="btn btn-primary btn-sm mx-1">Fulfill</button>
                                            <button type="button" id="cancel-reservation-btn" class="btn btn-danger btn-sm mx-1">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <form id="event-detail-form" class="mt-3">
                                            <input type="hidden" name="detail_id" id="detail_id">
                                            <input type="hidden" name="reservation_id_details" id="reservation_id_details">
                                            <div class="mb-3 row">
                                                <div class="col-md-6 col-sm-12">
                                                    <label for="event_type" class="form-label">Event Type</label>
                                                    <select class="form-select" id="event_type" required>
                                                        <!-- Event type options will be dynamically populated -->
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <label for="guest_count" class="form-label">Guest Count</label>
                                                    <input type="number" class="form-control" id="guest_count" placeholder="Guest Count" required />
                                                </div>

                                            </div>
                                            <div class="mb-3 row">
                                                <div class="col-md-4 col-sm-12">
                                                    <label for="setup_requirements" class="form-label">Setup Requirements</label>
                                                    <textarea class="form-control" id="setup_requirements" placeholder="Setup Requirements" required></textarea>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <label for="catering_needs" class="form-label">Catering Needs</label>
                                                    <textarea class="form-control" id="catering_needs" placeholder="Catering Needs" required></textarea>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <label for="special_requests" class="form-label">Special Requests</label>
                                                    <textarea class="form-control" id="special_requests" placeholder="Special Requests" required></textarea>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Details</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Event Details List -->
                                <div class="mt-3">
                                    <h2>Event Details</h2>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Event Type</th>
                                                <th>Guest Count</th>
                                                <th>Setup Requirements</th>
                                                <th>Catering Needs</th>
                                                <th>Special Requests</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="event-details-list">
                                        <center><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="40" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"></animate></circle><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="100" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"></animate></circle><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="160" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"></animate></circle></svg></center>
                                            <!-- Event details data will be dynamically populated here -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="offcanvas-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                                </div>
                            </div>
                        </div>


                        <!-- Reservation List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Reservation List</h5>
                                        <button type="button" id="addReservationModalBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                                            <i class="bx bx-plus"></i> Add Reservation
                                        </button>
                                    </div>
                                    <div class="container py-4">
                                        <form id="fillterForm" method="get" class="d-flex flex-wrap gap-3 align-items-end">
                                            
                                            <!-- From Date -->
                                            <div class="d-flex flex-column">
                                            <label for="from" class="form-label">📅 From</label>
                                            <input 
                                                type="date" 
                                                class="form-control" 
                                                id="from" 
                                                name="date_to"
                                            >
                                            </div>

                                            <!-- To Date -->
                                            <div class="d-flex flex-column">
                                            <label for="to" class="form-label">📅 To</label>
                                            <input 
                                                type="date" 
                                                class="form-control" 
                                                id="to" 
                                                name="date_to"
                                            >
                                            </div>

                                            <!-- Submit Button -->
                                            <div>
                                            <button type="submit" class="btn btn-primary" id="filiterBtn">filter</button>
                                            </div>

                                        </form>
                                        </div>

                                    <div class="card-body">
                                        <table id="reservationsTable" class="table table-hover table-bordered table-border-bottom-1 table-border-top-1 table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Venue</th>
                                                    <th>Customer</th>
                                                    <th>Reservation Date</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reservations-list">
                                                <!-- Reservation data will be dynamically populated here -->
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
    <!-- Modal for adding a reservation -->
    <div  class="modal  fade" id="addReservationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReservationLabel">Add Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reservation-form">
                        <input type="hidden" name="reservation_id" id="reservation_id">
                        <div class="mb-3">
                            <label for="venue_id" class="form-label">Venue</label>
                            <select class="form-select" id="venue_id" required>
                                <!-- Venue options will be dynamically populated -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select class="form-select" id="customer_id" required>
                                <!-- Customer options will be dynamically populated -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reservation_date" class="form-label">Reservation start_date</label>
                            <input type="date" class="form-control" id="reservation_date" required />
                        </div>
                           <div class="mb-3">
                            <label for="reservation_date" class="form-label">Reservation end date</label>
                            <input type="date" class="form-control" id="reservation_end_date" required />
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time" required />
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="end_time" required />
                        </div>
                        <div class="mb-3" hidden>
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Confirmed" selected>Confirmed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary create-reservation">Create Reservation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>

    <!-- Footer -->
    <script src="js/common.js"></script>
    <script src="js/venue_booking_list.js"></script>
    <script src="js/event_details.js"></script>
    <script>
const form = document.querySelector("#fillterForm");
const dataDiv = document.querySelector("#reservations-list");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    dataDiv.innerHTML = `<center><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="40" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"></animate></circle><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="100" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"></animate></circle><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="160" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"></animate></circle></svg></center>`;

    const from = form.querySelector("#from").value;
    const to = form.querySelector("#to").value;

    try {
        const response = await fetch(`../api/bookings/handle_venue_reservations.php?from=${from}&to=${to}`);
        const result = await response.json();
        const data = result.data;

        if (!data || data.length === 0) {
            dataDiv.innerHTML = "<span>No data found on specified date range</span>";
            return;
        }

        const rows = data.map(element => {
            const { id, venue_name, customer_name,reservation_date, start_time, end_time, status } = element;
            return `
    <tr>
        <td>${id}</td>
        <td>${venue_name}</td>
        <td>${customer_name}</td>
        <td>${reservation_date}</td>
        <td>${start_time}</td>
        <td>${end_time}</td>
        <td>${status}</td>
        <td style="display:flex;">
            <a href="?resto=venue_checkout&&booking_id=${id}" class="btn details-btn text-primary" title="Details">
                <i class="bx bx-detail"></i>
            </a>
            <button class="btn edit-btn text-info" title="Edit" data-id="${id}" data-reservation='${JSON.stringify(element)}'>
                <i class="bx bx-edit"></i>
            </button>
            <button class="btn delete-btn text-danger" title="Delete" data-id="${id}">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    </tr>
`;
        }).join("");

        dataDiv.innerHTML = `${rows}`;

    } catch (error) {
        console.error("Error fetching reservations:", error);
        dataDiv.innerHTML = "<span style='color: red;'>Failed to load data. Try again later.</span>";
    }
});
    </script>

