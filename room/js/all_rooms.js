$(document).ready(function () {
    const globalApi = '../api/rooms';

    // Function to export data to CSV
    $('#export-csv').on('click', function () {
        const filters = {
            room_number: $('#search-room-number').val(),
            room_class_id: $('#search-room-class').val(),
            block_id: $('#search-block').val(),
            status_id: $('#search-status').val(),
            capacity: $('#search-capacity').val(),
            price_min: $('#search-price-min').val(),
            price_max: $('#search-price-max').val()
        };

        $.ajax({
            url: `${globalApi}/export_csv.php`,
            method: 'GET',
            data: filters,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                const url = window.URL.createObjectURL(data);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'rooms.csv';
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Function to export data to Excel
    $('#export-excel').on('click', function () {
        const filters = {
            room_number: $('#search-room-number').val(),
            room_class_id: $('#search-room-class').val(),
            block_id: $('#search-block').val(),
            status_id: $('#search-status').val(),
            capacity: $('#search-capacity').val(),
            price_min: $('#search-price-min').val(),
            price_max: $('#search-price-max').val()
        };

        $.ajax({
            url: `${globalApi}/export_excel.php`,
            method: 'GET',
            data: filters,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                // console.log(data)
                const url = window.URL.createObjectURL(data);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'rooms.xlsx';
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Function to export data to PDF
    $('#export-pdf').on('click', function () {
        const filters = {
            room_number: $('#search-room-number').val(),
            room_class_id: $('#search-room-class').val(),
            block_id: $('#search-block').val(),
            status_id: $('#search-status').val(),
            capacity: $('#search-capacity').val(),
            price_min: $('#search-price-min').val(),
            price_max: $('#search-price-max').val()
        };

        $.ajax({
            url: `${globalApi}/export_pdf.php`,
            method: 'GET',
            data: filters,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                // console.log(data)
                const url = window.URL.createObjectURL(data);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'rooms.pdf';
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });
    

    // Fetch room classes, blocks, and statuses for room forms
    function fetchRoomClasses() {
        $.ajax({
            url: '../api/room_classes/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const roomClassSelect = $('#room_class_id');
                roomClassSelect.empty();
                roomClassSelect.append(`<option value="">Select Room Class</option>`);
                if (response.status == 200) {
                    response.room_classes.forEach(roomClass => {
                        const option = `<option value="${roomClass.id}">${roomClass.class_name}</option>`;
                        roomClassSelect.append(option);
                    });
                }
            }
        });
    }

    function fetchBlocks() {
        $.ajax({
            url: '../api/floors/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const blockSelect = $('#block_id');
                blockSelect.empty();
                blockSelect.append(`<option value="">Select Block</option>`);
                if (response.status == 200) {
                    response.room_blocks.forEach(block => {
                        const option = `<option value="${block.id}">${block.floor_number}</option>`;
                        blockSelect.append(option);
                    });
                }
            }
        });
    }

    function fetchStatuses() {
        $.ajax({
            url: '../api/room_status/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const statusSelect = $('#status_id');
                statusSelect.empty();
                statusSelect.append(`<option value="">Select Status</option>`);
                if (response.status == 200) {
                    response.room_statuses.forEach(status => {
                        const option = `<option value="${status.id}">${status.status_name}</option>`;
                        statusSelect.append(option);
                    });
                }
            }
        });
    }

    // Fetch room classes, blocks, and statuses for search filters
    function fetchRoomClassesForFilters() {
        $.ajax({
            url: '../api/room_classes/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const roomClassSelect = $('#search-room-class');
                roomClassSelect.empty();
                roomClassSelect.append(`<option value="">Select Room Class</option>`);
                if (response.status == 200) {
                    response.room_classes.forEach(roomClass => {
                        const option = `<option value="${roomClass.id}">${roomClass.class_name}</option>`;
                        roomClassSelect.append(option);
                    });
                }
            }
        });
    }

    function fetchBlocksForFilters() {
        $.ajax({
            url: '../api/floors/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const blockSelect = $('#search-block');
                blockSelect.empty();
                blockSelect.append(`<option value="">Select Block</option>`);
                if (response.status == 200) {
                    response.room_blocks.forEach(block => {
                        const option = `<option value="${block.id}">${block.floor_number}</option>`;
                        blockSelect.append(option);
                    });
                }
            }
        });
    }

    function fetchStatusesForFilters() {
        $.ajax({
            url: '../api/room_status/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const statusSelect = $('#search-status');
                statusSelect.empty();
                statusSelect.append(`<option value="">Select Status</option>`);
                if (response.status == 200) {
                    response.room_statuses.forEach(status => {
                        const option = `<option value="${status.id}">${status.status_name}</option>`;
                        statusSelect.append(option);
                    });
                }
            }
        });
    }

    // Fetch and display rooms with filters
    function fetchRooms(filters = {}) {
        $.ajax({
            url: `${globalApi}/read.php`,
            method: 'GET',
            data: filters,
            success: function (data) {
                const response = JSON.parse(data);
                //alert(data);
                const roomList = $('#room-list');
                roomList.empty();
let counter = 1;
                if (response.status == 200) {
                    response.rooms.forEach(room => {
                        const basePrice = parseInt(room.base_price).toLocaleString();
                        //let counter = counter +1;
                        const row = `
                        <tr>
                            <td>${counter++}</td>
                            <td>${room.room_number}</td>
                            <td>${room.room_class_name}</td>
                            <td>${room.block_name}</td>
                            <td>${room.status_name}</td>
                            <td>${room.capacity}</td>
                            <td>${basePrice} RWF</td>
                            <td>
                                <button class="btn edit-btn" title="Edit" data-id="${room.id}" data-room-number="${room.room_number}" data-room-class-id="${room.room_class_id}" data-block-id="${room.floor_id}" data-status-id="${room.status_id}" data-capacity="${room.capacity}" data-base-price="${room.base_price}"><i style="font-size:25px;" class="bx bx-edit"></i></button>
                                <button class="btn view-btn" title="View Details" data-id="${room.id}" data-room-number="${room.room_number}" data-room-class-name="${room.room_class_name}" data-block-name="${room.block_name}" data-status-name="${room.status_name}" data-capacity="${room.capacity}" data-base-price="${room.base_price}" data-features="${room.features}" data-bed-types="${room.bed_types}" data-num-beds="${room.num_beds}" data-bs-toggle="offcanvas" data-bs-target="#roomDetailsOffcanvas" aria-controls="roomDetailsOffcanvas"><i style="font-size:25px;" class="bx bx-show-alt"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${room.id}"><i style="font-size:25px;" class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    // <button class="btn view-availability-btn" title="View Availability" data-id="${room.id}" data-bs-toggle="offcanvas" data-bs-target="#roomAvailabilityOffcanvas" aria-controls="roomAvailabilityOffcanvas"><i style="font-size:25px;" class="bx bx-calendar"></i></button>
                    // <button class="btn view-maintenance-btn" title="View Maintenance" data-id="${room.id}" data-bs-toggle="offcanvas" data-bs-target="#roomMaintenanceOffcanvas" aria-controls="roomMaintenanceOffcanvas"><i style="font-size:25px;" class="bx bx-wrench"></i></button>
                        roomList.append(row);
                    });
                } else {
                    console.log(response.message);
                    roomList.html(`<p>${response.message}</p>`);
                }
            }
        });
    }

    // Handle search and filter form submission
    $('#search-filter-form').on('submit', function (event) {
        event.preventDefault();
        const filters = {
            room_number: $('#search-room-number').val(),
            room_class_id: $('#search-room-class').val(),
            block_id: $('#search-block').val(),
            status_id: $('#search-status').val(),
            capacity: $('#search-capacity').val(),
            price_min: $('#search-price-min').val(),
            price_max: $('#search-price-max').val()
        };
        fetchRooms(filters);
    });

    // Clear filters 
    $('#clear-filters').on('click', function () {
        $('#search-filter-form')[0].reset(); // Reset the form 
        fetchRooms(); // Fetch all rooms without filters 
    });

    // Fetch room classes, blocks, and statuses for room forms
    fetchRoomClasses();
    fetchBlocks();
    fetchStatuses();

    // Fetch room classes, blocks, and statuses for search filters
    fetchRoomClassesForFilters();
    fetchBlocksForFilters();
    fetchStatusesForFilters();

    // Fetch and display rooms
    fetchRooms();

    // Create and update room
    $('#room-form').on('submit', function (event) {
        event.preventDefault();
        const roomId = $('#room_id').val(); // Call val() as a function
        const roomNumber = $('#room_number').val();
        const roomClassId = $('#room_class_id').val();
        const blockId = $('#block_id').val();
        const statusId = $('#status_id').val();
        const capacity = $('#capacity').val();
        const basePrice = $('#base_price').val();
        const url = roomId ? `${globalApi}/update.php` : `${globalApi}/create.php`;
        const data = roomId ? {
            room_id: roomId,
            room_number: roomNumber,
            room_class_id: roomClassId,
            block_id: blockId,
            status_id: statusId,
            capacity: capacity,
            // base_price: basePrice
        } : {
            room_number: roomNumber,
            room_class_id: roomClassId,
            block_id: blockId,
            status_id: statusId,
            capacity: capacity,
            // base_price: basePrice
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: JSON.stringify(data), // Serialize the data properly
            contentType: 'application/json',
            success: function (response) {
                console.log(response)
                const data = JSON.parse(response);
                // console.log(data);
                if (data.status == 201 || data.status == 200) {
                    showToast('Well done!', data.message, data.msg_type);
                    fetchRooms();
                    $('#room-form')[0].reset(); // Reset the form
                    $('#room_id').val('');
                    $('#room-form button').text('Add Room'); // Reset button text
                } else {
                    showToast('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown, jqXHR.responseType);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#room-list').on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const roomNumber = $(this).data('room-number');
        const roomClassId = $(this).data('room-class-id');
        const blockId = $(this).data('block-id');
        const statusId = $(this).data('status-id');
        const capacity = $(this).data('capacity');
        // const basePrice = $(this).data('base-price');
        // console.log(id, roomNumber, roomClassId, blockId, statusId, capacity, basePrice);
        $('#room_id').val(id);
        $('#room_number').val(roomNumber);
        $('#room_class_id').val(roomClassId);
        $('#block_id').val(blockId);
        $('#status_id').val(statusId);
        $('#capacity').val(capacity);
        // $('#base_price').val(basePrice);
        $('#room-form button').text('Update Room');
        $('html, body').animate({ 
            scrollTop: $('#register-room-row').offset().top 
        }, 10);  
    });

    // View room details
    $('#room-list').on('click', '.view-btn', function () {
        const roomNumber = $(this).data('room-number');
        const roomClassName = $(this).data('room-class-name');
        const blockName = $(this).data('block-name');
        const statusName = $(this).data('status-name');
        const capacity = $(this).data('capacity');
        const basePrice = $(this).data('base-price');
        const features = $(this).data('features');
        const bedTypes = $(this).data('bed-types');
        const numBeds = $(this).data('num-beds');

        $('#detail-room-number').text(roomNumber);
        $('#detail-room-class').text(roomClassName);
        $('#detail-block').text(blockName);
        $('#detail-status').text(statusName);
        $('#detail-capacity').text(capacity);
        $('#detail-base-price').text(`${parseInt(basePrice).toLocaleString()} RWF`);
        $('#detail-features').text(features || 'N/A');
        $('#detail-bed-types').text(bedTypes || 'N/A');
        $('#detail-num-beds').text(numBeds);
    });

    // Delete room
    $('#room-list').on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F2A341',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: `${globalApi}/delete.php`,
                    method: 'POST',
                    data: JSON.stringify({
                        room_id: id
                    }),
                    contentType: 'application/json',
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.status == 201 || data.status == 200) {
                            showToast('Well done!', data.message, data.msg_type);
                            fetchRooms();
                        } else {
                            showToast('Error', data.message, data.msg_type);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('AJAX error:', textStatus, errorThrown);
                        showAlert('Error', 'An error occurred while processing your request.', 'error');
                    }
                });
            }
        });
    });

    // FullCalendar Initialization
    const roomAvailabilityCalendar = new FullCalendar.Calendar(document.getElementById('room-availability-calendar'), {
        initialView: 'dayGridMonth',
        events: function (fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: `${globalApi}/availability.php`,
                method: 'GET',
                success: function (data) {
                    const response = JSON.parse(data);
                    if (response.status === 200) {
                        successCallback(response.availability);
                    } else {
                        failureCallback(response.message);
                    }
                }
            });
        }
    });

    roomAvailabilityCalendar.render();

    // View room availability
    $('#room-list').on('click', '.view-availability-btn', function () {
        const roomId = $(this).data('id');

        // Fetch and render availability for the selected room
        roomAvailabilityCalendar.refetchEvents();
    });

    // Show Room Availability Offcanvas
    $('#room-list').on('click', '.view-availability-btn', function () {
        const roomId = $(this).data('id');
        $('#roomAvailabilityOffCanvas').offcanvas('show');
    });

    // Fetch and display maintenance data
    const fetchMaintenanceData = (roomId) => {
        $.ajax({
            url: `${globalApi}/maintenance.php`,
            method: 'GET',
            data: {
                room_id: roomId
            },
            success: function (data) {
                const response = JSON.parse(data);
                const maintenanceList = $('#maintenance-list');
                maintenanceList.empty();
                if (response.status == 200) {
                    response.maintenance.forEach(item => {
                        const listItem = `
                        <li>
                            <strong>${item.date}:</strong> ${item.issue}
                        </li>
                        `;
                        maintenanceList.append(listItem);
                    });
                } else {
                    maintenanceList.html(`<li>${response.message}</li>`);
                }
            }
        });
    };

    // Show Room Maintenance Offcanvas
    $('#room-list').on('click', '.view-maintenance-btn', function () {
        const roomId = $(this).data('id');
        $('#maintenance-room-id').val(roomId);
        fetchMaintenanceData(roomId);
        $('#roomMaintenanceOffcanvas').offcanvas('show');
    });

    // Add maintenance record
    $('#maintenance-form').on('submit', function (event) {
        event.preventDefault();
        const roomId = $('#maintenance-room-id').val();
        const date = $('#maintenance-date').val();
        const issue = $('#maintenance-issue').val();
        const data = {
            room_id: roomId,
            date: date,
            issue: issue
        };
        $.ajax({
            url: `${globalApi}/add_maintenance.php`,
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status == 201 || data.status == 200) {
                    showToast('Success', data.message, 'success');
                    fetchMaintenanceData(roomId);
                    $('#maintenance-form')[0].reset();
                } else {
                    showToast('Error', data.message, 'danger');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Initial fetch rooms
    fetchRooms();
});

// Show alert div
const getAlert = document.getElementById('getAlert');

// Display message
function showAlert(title, message, msgType) {
    const type = msgType === 'success' ? 'success' : 'danger';
    const icon = msgType === 'success' ? 'check' : 'error';
    const alert = `
        <div class="alert alert-${type} alert-dismissible" role="alert">
            <h4 class="alert-heading d-flex align-items-center">
                <span class="alert-icon rounded-circle">
                    <i class="bx bx-${icon}"></i>
                </span>
                ${title}
            </h4>
            <hr>
            <p class="mb-0">${message}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    getAlert.innerHTML += alert; // Use innerHTML to render the alert
}

// Display toast notification
function showToast(title, message, msgType) {
    $('#showToast').html('');
    const type = msgType === 'success' ? 'success' : 'danger';
    const icon = msgType === 'success' ? 'check-double' : 'error';
    const toast = `
    <div class="bs-toast toast toast-placement-ex m-2 fade bg-${type} top-0 end-0" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
        <div class="toast-header">
            <i style="font-size:35px" class="bx bx-${icon} me-2"></i>
            <div class="me-auto fw-semibold">${title}</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">${message}</div>
    </div>
    `;

    $('#showToast').append(toast);

    // Ensure the toast element is in the DOM before initializing Bootstrap Toast
    var toastElement = document.querySelector('#showToast .toast');
    var toastInstance = new bootstrap.Toast(toastElement, {
        delay: 3000
    });
    toastInstance.show();
}