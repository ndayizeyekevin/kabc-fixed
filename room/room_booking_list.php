
<?php
 
ini_set("display_errors", 1);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- styles -->
<style>

    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .table td {
        vertical-align: middle;
        /* Vertically align content within cells */
    }

    .top-section {
        position: relative;
    }

    .room-params {
        position: relative;
        z-index: 1;
    }

    hr {
        width: 100%;
        border-top: 2px solid #ccc;
        margin-top: 30px;
    }

    .custom-tabs-container {
        border-bottom: 1px solid #e5e5e5;
    }

    .custom-tabs .nav-link {
        border: none;
        background: none;
        color: #6c757d;
        /* Gray for inactive tabs */
        font-weight: bold;
        text-transform: uppercase;
        font-size: 14px;
        padding: 10px 20px;
        position: relative;
        transition: color 0.3s ease;
    }

    .custom-tabs .nav-link:hover {
        color: #0d6efd;
        /* Highlight on hover */
    }

    .custom-tabs .nav-link.active {
        color: black;
        /* Active tab color */
        font-weight: bold;
    }

    .custom-tabs .nav-link.active::after {
        content: '';
        display: block;
        width: 100%;
        height: 3px;
        background-color: #0F7CBF;
        /* Blue underline */
        position: absolute;
        bottom: 0;
        left: 0;
    }

    .nav-tabs .nav-item .nav-link:not(.active) {
        /* remove background color */
        background-color: transparent;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 54px;
        height: 28px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        border-radius: 34px;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #F2A341;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    /* clear span */
    .clear-span {
        cursor: pointer;
        /* Makes the cursor change to a pointer */
        color: #dc3545;
        /* Text color */
        padding: 5px 10px;
        /* Padding to give it some clickable area */
        border: 1px solid transparent;
        /* Border for visual feedback */
        border-radius: 5px;
        /* Rounded corners */
        transition: background-color 0.3s ease, border-color 0.3s ease;
        /* Smooth transition for effects */
    }

    .clear-span:hover {
        background-color: rgba(220, 53, 69, 0.1);
        /* Light background color on hover */
        border-color: #dc3545;
        /* Border color on hover */
    }

    /* validations */
    .is-invalid {
        border-color: #dc3545;
        background-color: #f8d7da;
    }

    .is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        color: #dc3545;
        display: block;
    }

    .is-invalid~.form-check-label {
        color: #dc3545;
    }

    /* customize the .current-transaction-tab to fit the media query for the small size screens */
    @media (max-width: 717px) {
        .current-transaction-tab {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 10px;
            font-size: 12px;
            background-color: #e7f3ff;
            /* Adjust background color as needed */
        }

        .current-transaction-tab span {
            display: block;
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="colr-area">
        <div class="container">

       
                
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="addBookingOffcanvas" aria-labelledby="addBookingOffcanvasLabel" style="width: 95%;">
                            <div id="showToast" class="toast-container position-relative"></div>

                            <div class="offcanvas-header">
                                <h5 id="addBookingOffcanvasLabel" class="offcanvas-title text-center">Add Booking</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>

                            <div class="offcanvas-body d-flex flex-column">
                                
                                <div class="custom-tabs-container">
                                    <ul class="nav nav-tabs custom-tabs justify-content-center nav-justified" id="customTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="book-tab" data-bs-toggle="tab" data-bs-target="#book-content" type="button" role="tab" aria-controls="book-content" aria-selected="true">Book a room</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-content" type="button" role="tab" aria-controls="info-content" aria-selected="false">Add info</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="confirm-tab" data-bs-toggle="tab" data-bs-target="#confirm-content" type="button" role="tab" aria-controls="confirm-content" aria-selected="false">Confirm (1)</button>
                                        </li>
                                    </ul>

                                    <div class="tab-content mt-4" id="customTabContent" >
                                        <!-- Book a Room Tab -->
                                        <div class="tab-pane fade show active" id="book-content" role="tabpanel" aria-labelledby="book-tab">
                                            <!-- room search params -->
                                            <div class="room-params" >
                                               
                                                <form id="search-room-form">
                                                    <div class="row">
                                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                                            <div class="mb-3">
                                                                <label class="form-label text-primary" for="booking-type">Booking Type</label>
                                                                <div id="booking-type" class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" value="Single" checked>
                                                                    <label class="btn btn-outline-primary" for="btnradio1">Single</label>

                                                                    <!-- Remove Group Booking -->

                                                                    <!-- <input type="radio" class="btn-check" name="btnradio" id="btnradio2" value="Group">
                                                                    <label class="btn btn-outline-primary" for="btnradio2">Group</label> -->
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                                            <div class="mb-3">
                                                                <label for="guest-type" class="form-label">Guest Type</label>
                                                                <select class="form-select" id="guest-type" aria-label="Default select example">
                                                                    <option value="individual_guest">Individual Guest</option>
                                                                    <!-- <option value="company_guest">Company Guest</option> -->
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                                            <div class="mb-3">
                                                                <label for="dates-of-stay" class="form-label">Dates of Stay</label>
                                                                <input class="form-control" type="text" name="daterange" value="Select dates of stay" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                                            <div class="mb-3">
                                                                <label for="adults" class="form-label">Adults</label>
                                                                <select class="form-select" id="adults" aria-label="Default select example">
                                                                    <option value="0">0</option>
                                                                    <option value="1">1</option>
                                                                    <option value="2">2</option>
                                                                    <option value="3">3</option>
                                                                    <option value="4">4</option>
                                                                    <option value="5">5</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                                            <div class="mb-3">
                                                                <label for="adults" class="form-label">Children</label>
                                                                <select class="form-select" id="children" aria-label="Default select example">
                                                                    <option value="0">0</option>
                                                                    <option value="1">1</option>
                                                                    <option value="2">2</option>
                                                                    <option value="3">3</option>
                                                                    <option value="4">4</option>
                                                                    <option value="5">5</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                                            <div style="margin-top: 1.7rem">
                                                                <button class="btn btn-primary search-room" id="searchRoom" type="button">Search <i class="bx bx-search"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="current-transaction-tab">
                                                    <span class="badge bg-label-info text-dark float-end d-flex align-items-center" style="height: 30px; font-size:14px">
                                                        Booking details:
                                                        <span id="curr_rooms" class="mx-2">0 room(s)</span>
                                                        |
                                                        <span id="curr_adults" class="mx-3">0 Adult(s)</span>,
                                                        <span id="curr_childrens" class="mx-2">0 Child(ren)</span>
                                                        |
                                                        <span id="curr_amount" class="mx-3">0 RWF</span>
                                                        |
                                                        <span id="clear" class="mx-3 text-danger clear-span">CLEAR</span>
                                                        </di>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- room search params end -->
                                            <hr style="margin-top: 2.5rem">

                                            <!-- results -->
                                            <div class="result-section mt-5">
                                                <div class="table-responsive">
                                                    <table class="table table-borderless table-hover" id="room-search-result-tb">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Room Class</th>
                                                                <th scope="col">Dates</th>
                                                                <th scope="col">Capacity</th>
                                                                <th scope="col">Base Price (RWF)</th>
                                                                <th scope="col">Features</th>
                                                                <th scope="col">Bed Type(s)</th>
                                                                <th scope="col">Bed Detail(s)</th>
                                                                <th scope="col">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- results to be populated here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- results -->
                                        </div>

                                        <!-- Add Info Tab -->
                                        <div class="tab-pane fade" id="info-content" role="tabpanel" aria-labelledby="info-tab">
                                            <div class="top-section">
                                                <div class="mb-3">
                                                                        <label for="searchGuestInput" class="form-label">Search Guest</label>
                                                                        <input type="text" id="searchGuestInput" class="form-control" placeholder="Search by guest names or email">
                                                                        <div id="searchGuestResults" class="dropdown-menu show mt-2" style="display: none;margin-top:-300px">
                                                                            <!-- Dynamic guest results will be appended here -->
                                                                        </div>
                                                                    </div>
                                                <hr>
                                            </div>
                                            <div class="add-info-section">
                                                
                                    
                                                
                                                <div class="row">
                                                    
                                                    <div class="col-md-12">
                                                            <div class="room-params">
                                                    <div class="current-transaction-tab">
                                                        <span class="badge bg-label-info text-dark float-end d-flex align-items-center" style="height: 30px; font-size:14px">
                                                            Booking details:
                                                            <span id="curr_rooms" class="mx-2">0 room(s)</span>
                                                            |
                                                            <span id="curr_adults" class="mx-3">0 Adult(s)</span>,
                                                            <span id="curr_childrens" class="mx-2">0 Child(ren)</span>
                                                            |
                                                            <span id="curr_amount" class="mx-3">0 RWF</span>
                                                            |
                                                            <span id="clear" class="mx-3 text-danger clear-span">CLEAR</span>
                                                            </di>
                                                        </span>
                                                    </div>
                                                </div>
                                                       
                                                    </div>
                                                    <div class="col-md-12">
                                                        
                                                          <div class="col-md-12">
                                                                
                                                                    <input type="hidden" name="guest_id" id="guestID">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="firstName" class="form-label">First Name<span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" id="firstName" placeholder="John" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="lastName" class="form-label">Last Name<span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" id="lastName" placeholder="Doe" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                                                                <input type="date" class="form-control" id="dateOfBirth" value="11/11/2000" placeholder="Date Of Birth">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="placeOfBirth" class="form-label">Place of Birth</label>
                                                                                <input type="text" class="form-control" id="placeOfBirth" placeholder="Kigali">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="phone" class="form-label">Phone</label>
                                                                                <input type="tel" class="form-control" id="phone" placeholder="+25078...">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="nationality" class="form-label">Nationality</label>
                                                                                <input type="text" class="form-control" id="nationality" placeholder="Rwandan">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="email" class="form-label">Email</label>
                                                                                <input type="email" class="form-control" id="email" placeholder="john@gmail.com">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="residence" class="form-label">Residence</label>
                                                                                <input type="text" class="form-control" id="residence" placeholder="Rwanda">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="adress" class="form-label">Address</label>
                                                                                <input type="text" class="form-control" id="adress" placeholder="Kimihurura">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="profession" class="form-label">Profession</label>
                                                                                <input type="text" class="form-control" id="profession" placeholder="Sales man">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group mb-3 card">
                                                                            <div class="py-3">
                                                                                <span>Has Passport ?</span>
                                                                                <!-- Toggle Switch -->
                                                                                <label class="switch float-end">
                                                                                    <input type="checkbox" id="toggleSwitch" />
                                                                                    <span class="slider round"></span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <!-- Identification Field -->
                                                                            <div class="mb-3" id="identificationField">
                                                                                <label for="identificationMain" class="form-label">Identification</label>
                                                                                <input
                                                                                    type="text"
                                                                                    class="form-control"
                                                                                    id="identificationMain"
                                                                                    placeholder="1234567891011121" />
                                                                            </div>
                                                                            <!-- Passport Fields -->
                                                                            <div class="row" id="passportField" style="display: none">
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <label for="passportNumber" class="form-label">Passport Number</label>
                                                                                        <input
                                                                                            type="text"
                                                                                            class="form-control"
                                                                                            id="passportNumber"
                                                                                            placeholder="RW-PASS-203456" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <label for="passExpiration" class="form-label">Expiration Date</label>
                                                                                        <input
                                                                                            type="date"
                                                                                            class="form-control"
                                                                                            id="passExpiration"
                                                                                            placeholder="Passport Expiration Date" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="mb-3">
                                                                                <label for="bookingComment" class="form-label">Booking Comment</label>
                                                                                <textarea class="form-control" id="bookingComment" rows="3" placeholder="Add Comment"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="required-fields">
                                                                        <p><span class="text-danger">*</span> Required fields</p>
                                                                    </div>
                                                                </div>
                                                        <div class="card h-100">
                                                            <div class="card-header">
                                                                BOOKING OPTIONS
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="mb-3">
                                                                    <label for="bookedFrom" class="form-label">Source</label>
                                                                    <select name="booked-from" id="bookedFrom" class="form-control">
                                                                        <option value="reception">Reception</option>
                                                                        <option value="call">Call</option>
                                                                        <option value="email">Email</option>
                                                                        <option value="website">Website</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="bookStatus" class="form-label">Status</label>
                                                                    <select name="book-status" id="bookStatus" class="form-control">
                                                                        <!-- reservation status will be populated here -->
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="company" class="form-label">Company</label>
                                                                    <input type="text" class="form-control" id="company" placeholder="XYZ Ltd">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="coming_from" class="form-label">Coming From</label>
                                                                    <input type="text" class="form-control" id="coming_from" placeholder="United States">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="going_to" class="form-label">Going To</label>
                                                                    <input type="text" class="form-control" id="going_to" placeholder="France">
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <small class="text-dark fw-light fs-6 d-block">Room options <span class="text-danger">*</span></small>
                                                                        <div id="roomOptionsContainer">
                                                                            <!-- Room options will be populated here -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-4 card">
                                                                    <div class="p-3">
                                                                        <span>Notify guest ?</span>
                                                                        <!-- Toggle Switch -->
                                                                        <label class="switch float-end">
                                                                            <input type="checkbox" id="notifyGuest" checked />
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
                                                
                                                
                                                
                                                
                                            </div>
                                        </div>

                                        <!-- Confirm Tab -->
                                        <div class="tab-pane fade" id="confirm-content" role="tabpanel" aria-labelledby="confirm-tab">
                                            <div class="top-section">
                                                <div class="room-params">
                                                    <div class="current-transaction-tab">
                                                        <span class="badge bg-label-info text-dark float-end d-flex align-items-center" style="height: 30px; font-size:14px">
                                                            Booking details:
                                                            <span id="curr_rooms" class="mx-2">0 room(s)</span>
                                                            |
                                                            <span id="curr_adults" class="mx-3">0 Adult(s)</span>,
                                                            <span id="curr_childrens" class="mx-2">0 Child(ren)</span>
                                                            |
                                                            <span id="curr_amount" class="mx-3">0 RWF</span>
                                                            |
                                                            <span id="clear" class="mx-3 text-danger clear-span">CLEAR</span>
                                                            </di>
                                                        </span>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- results -->
                                            <div class="result-section mt-5">
                                                <div class="table-responsive" id="confirm-content">
                                                    <table class="table table-borderless table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Room Class</th>
                                                                <th scope="col">Dates</th>
                                                                <th scope="col">Nights</th>
                                                                <th scope="col">Room Price (RWF)</th>
                                                                <th scope="col">Features</th>
                                                                <th scope="col">Bed Type</th>
                                                                <th scope="col">Room Number</th>
                                                                <th scope="col">Room Bloc</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- results -->
                                        </div>
                                    </div>
                                </div>
                                <div class="total-amount" id="totalAmount" style="display: none;">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <span class="text-muted">Total, RWF: </span><span class="text-dark fw-bold" id="total-booking-price">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="offcanvas-footer mt-2 px-2 mb-3 text-center">
                                <button type="button" class="btn btn-secondary w-30" id="previous-btn" style="display: none;">Previous</button>
                                <button type="button" class="btn btn-secondary w-30" data-bs-dismiss="offcanvas" id="cancel-btn">Cancel</button>
                                <button type="button" class="btn btn-primary w-30" id="next-btn">Next</button>
                                <button type="button" class="btn btn-primary w-30" id="confirm-btn" style="display: none;">Confirm</button>
                            </div>
                        </div>

                        <div id="getBookingAlert"></div>

                        <!-- Booking List -->
                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Room Booking List</h5>
                                        <button type="button" id="addBookingModalBtn" class="btn btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#addBookingOffcanvas" aria-controls="addBookingOffcanvas">
                                            <i class="bx bx-plus"></i> Add Booking
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive z-50">                                           
                                            <table id="tableBookings" class="table table-hover" border="2">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Room</th>
                                                        <th>Guest</th>
                                                        <th>Nights</th>
                                                        <th>Check-in Date</th>
                                                        <th>Check-out Date</th>
                                                        <th>Room Price</th>
                                                        <th>Total Amount</th>
                                                        <th>Booking Status</th>
                                                        <th>Payment Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="booking-list">
                                                    <!-- Booking data will be dynamically populated here -->
                                                </tbody>
                                            </table>
                                        </div>
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

    <!-- modals -->
    <!-- change price modal -->
    <div class="modal fade" id="changePriceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookingLabel">Change Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" class="form-control" id="changed-price">
                </div>
                <button class="btn btn-primary" id="save-price-btn">Change</button>
            </div>
        </div>
    </div>

    <div id="showToast" class="toast-container position-relative"></div>

    <!-- Footer -->

    <script src="js/room_booking_list.js"></script>
    <script src="js/room_booking_custom.js"></script>
     <script>src="js/handle_bookings.js"</script> 
    <script>
        // toggle passport / id field
        $(document).ready(function() {
            $("#toggleSwitch").change(function() {
                if ($(this).is(":checked")) {
                    $("#identificationField").hide(); // Hide Identification Field
                    $("#passportField").show(); // Show Passport Fields
                } else {
                    $("#passportField").hide(); // Hide Passport Fields
                    $("#identificationField").show(); // Show Identification Field
                }
            });
        });
    </script>

    
    
    
</body>

</html>