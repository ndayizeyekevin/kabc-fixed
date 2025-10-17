$(document).ready(function () {
    const globalApi = '../api/bookings/handle_room_bookings.php';
    const guestsApi = '../api/guests/read.php';
    const paymentStatusesApi = '../api/payments/handle_payment_statuses.php';
    const paymentModesApi = '../api/payments/handle_payment_modes.php';
    const handleRooms = '../api/rooms/get_available_rooms_per_booking.php';
    const bookingStatusApi = '../api/bookings/handle_booking_statuses.php';
    const roomOptionsApi = '../api/rooms/handle_room_options.php';

    let allGuests = [];
    let availableRoomsByClass = [];
    let totalBookingAmount = 0;
    let finalRoomPrice = 0;
    let finalVatAmount = 0;

    // Updated currentTransaction structure
    let currentTransaction = {
        rooms: 0,
        adults: 0,
        children: 0,
        amount: 0,
        roomID: null,          // Will store ACTUAL room ID (e.g., 305)
        roomClassID: null,     // Store room class ID separately
        roomPrice: 0,          // Base price per night
        checkinDate: null,
        checkoutDate: null,
        selectedRoomNumber: null
    };

    // Function to fetch and store all guests
    const fetchGuests = () => {
        $.ajax({
            url: guestsApi,
            method: 'GET',
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status == 200) {
                    allGuests = data.guests;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showToast('Error', 'An error occurred while fetching guests.', 'danger');
            }
        });
    };

    fetchGuests();

    // Function to filter and display guest results
    const filterGuests = (searchTerm) => {
        const filteredGuests = allGuests.filter(guest => {
            const guestName = `${guest.first_name} ${guest.last_name} (${guest.email_address})`.toLowerCase();
            return guestName.includes(searchTerm.toLowerCase());
        });

        const resultsDiv = $('#searchGuestResults');
        resultsDiv.empty();

        if (filteredGuests.length > 0) {
            filteredGuests.forEach(guest => {
                const guestElement = $(`<a href="#" class="dropdown-item" data-guest='${JSON.stringify(guest)}'>${guest.first_name} ${guest.last_name} (${guest.email_address})</a>`);
                resultsDiv.append(guestElement);
            });
            resultsDiv.show();
        } else {
            resultsDiv.hide();
        }
    };

    // Handle input on the search box
    $('#searchGuestInput').on('input', function () {
        const searchTerm = $(this).val();
        if (searchTerm.length > 0) {
            filterGuests(searchTerm);
        } else {
            $('#searchGuestResults').hide();
        }
    });

    // Handle guest selection from the results
    $('#searchGuestResults').on('click', '.dropdown-item', function (e) {
        e.preventDefault();
        const selectedGuest = JSON.parse($(this).attr('data-guest'));

        $('#guestID').val(selectedGuest.id);
        $('#firstName').val(selectedGuest.first_name);
        $('#lastName').val(selectedGuest.last_name);
        $('#dateOfBirth').val(selectedGuest.date_of_birth);
        $('#placeOfBirth').val(selectedGuest.place_of_birth);
        $('#phone').val(selectedGuest.phone_number);
        $('#nationality').val(selectedGuest.nationality);
        $('#email').val(selectedGuest.email_address);
        $('#residence').val(selectedGuest.residence);
        $('#adress').val(selectedGuest.address);
        $('#profession').val(selectedGuest.profession);

        if (selectedGuest.passport_number) {
            $('#toggleSwitch').prop('checked', true);
            $('#passportNumber').val(selectedGuest.passport_number);
            $('#passExpiration').val(selectedGuest.passport_expiration_date);
            $('#identificationMain').val('');
            $('#passportField').show();
            $('#identificationField').hide();
        } else {
            $('#toggleSwitch').prop('checked', false);
            $('#passportNumber').val('');
            $('#passExpiration').val('');
            $('#identificationMain').val(selectedGuest.identification);
            $('#passportField').hide();
            $('#identificationField').show();
        }

        $('#bookingComment').val(selectedGuest.booking_comment);
        $('#searchGuestResults').hide();
        $('#searchGuestInput').val('');
    });

    const fetchPaymentStatuses = () => {
        $.ajax({
            url: paymentStatusesApi,
            method: 'GET',
            success: function (data) {
                const response = data;
                const paymentStatusSelect = $('#payment_status_id');
                paymentStatusSelect.empty();
                paymentStatusSelect.append('<option value="">Select Payment Status</option>');

                if (response.status == 200) {
                    response.data.forEach(status => {
                        paymentStatusSelect.append(`<option value="${status.id}">${status.payment_status_name}</option>`);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    };

    const fetchPaymentModes = () => {
        $.ajax({
            url: paymentModesApi,
            method: 'GET',
            success: function (data) {
                const response = data;
                const paymentModeSelect = $('#mode_of_payment_id');
                paymentModeSelect.empty();
                paymentModeSelect.append('<option value="">Select Payment Mode</option>');

                if (response.status == 200) {
                    response.data.forEach(mode => {
                        paymentModeSelect.append(`<option value="${mode.id}">${mode.payment_mode_name}</option>`);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    };

    const fetchBookingStatuses = () => {
        $.ajax({
            url: bookingStatusApi,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const bookingStatusSelect = $('#bookStatus');
                bookingStatusSelect.empty();

                if (response.status == 200) {
                    response.data.forEach(stat => {
                        bookingStatusSelect.append(`<option value="${stat.id}">${stat.status_name}</option>`);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown, jqXHR.responseText);
            }
        });
    };

    const fetchRoomOptions = () => {
        $.ajax({
            url: roomOptionsApi,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                const roomOptionsContainer = $('#roomOptionsContainer');
                roomOptionsContainer.empty();

                if (response.status == 200) {
                    response.data.forEach(option => {
                        const optionElement = `
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="option_${option.id}" value="${option.id}" required />
                                <label class="form-check-label" for="option_${option.id}">${option.option_code}</label>
                            </div>
                        `;
                        roomOptionsContainer.append(optionElement);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown, jqXHR.responseText);
            }
        });
    };
    
    fetchRoomOptions();

    const fetchBookings = () => {
        $.ajax({
            url: globalApi,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                const res = response;
                if (res.status == 200) {
                    const bookings = res.data;
                    const tableBookings = $('#tableBookings').DataTable({
                        retrieve: true,
                    });
                    tableBookings.clear();
                    if (bookings.length > 0) {
                        let i = 1;
                        bookings.forEach(booking => {
                            let booking_amount = booking.booking_amount;
                            let changed_booking_amount = parseInt(booking_amount).toLocaleString();
                            let consumed = getDiff(booking.checkin_date, booking.booking_status_id, booking.checkout_date);
                            
                            let actionData = `
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <button class="btn edit-btn text-info" title="Details" data-id="${booking.id}" data-booking='${JSON.stringify(booking)}'> <i class="bx bx-link-external"></i> Details</button>
                                </div>
                            </div>
                            `;
                            
                            // Booking status colors
                            let statusColor = '';
                            switch (booking.booking_status_name) {
                                case 'Pending': statusColor = 'badge bg-label-warning'; break;
                                case 'Confirmed': statusColor = 'badge bg-label-success'; break;
                                case 'Cancelled': statusColor = 'badge bg-label-danger'; break;
                                case 'No-Show': statusColor = 'badge bg-label-secondary'; break;
                                default: statusColor = 'badge bg-label-secondary'; break;
                            }

                            // Payment status colors
                            const payment_status = booking.payment_status_name || 'Pending';
                            let paymentColor = '';
                            switch (payment_status) {
                                case 'Pending': paymentColor = 'badge bg-label-warning'; break;
                                case 'Paid': paymentColor = 'badge bg-label-success'; break;
                                case 'Cancelled': paymentColor = 'badge bg-label-danger'; break;
                                default: paymentColor = 'badge bg-label-secondary'; break;
                            }                            

                            tableBookings.row.add([
                                i++,
                                booking.room_number,
                                booking.first_name + ' ' + booking.last_name,
                                booking.duration,
                                booking.checkin_date,
                                booking.checkout_date,
                                parseInt(booking.room_price).toLocaleString() + ' RWF',
                                '+' + (parseInt(booking.room_price) * parseInt(consumed)).toLocaleString() + ' RWF',
                                `<span class="${statusColor}">${booking.booking_status_name}</span>`,
                                `<span class="${paymentColor}">${payment_status}</span>`,
                                `<button type="button" class="btn rounded-pill me-2 btn-outline-secondary btn-sm details-btn" data-id="${booking.booking_id}" data-booking='${JSON.stringify(booking)}'>Details</button>`
                            ]).draw(true);
                        });
                    } else {
                        tableBookings.draw(false);
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast('Error', 'An error occurred while fetching bookings.', 'danger');
            }
        });
    };

    $(document).on('click','.details-btn', function(){
        const bookingId = $(this).data('id');
        const url = window.location.href.split('/').slice(0, -1).join('/') +
                    '?resto=room_booking_details&&booking_id=' + bookingId;
        window.location.href = `${url}`;
    });

    // Compute duration days
    const calculateDuration = () => {
        const checkinDate = new Date($('#checkin_date').val());
        const checkoutDate = new Date($('#checkout_date').val());
        const timeDiff = Math.abs(checkoutDate - checkinDate);
        const duration = Math.ceil(timeDiff / (1000 * 3600 * 24));
        $('#duration').val(duration > 0 ? duration : 0);
    };
    $('#checkin_date, #checkout_date').on('change', calculateDuration);

    // Initialize all data fetches
    fetchGuests();
    fetchPaymentStatuses();
    fetchPaymentModes();
    fetchBookingStatuses();
    fetchBookings();

    // Function to update current transaction tab
    const updateCurrentTransactionTab = () => {
        $('.current-transaction-tab #curr_rooms').text(`${currentTransaction.rooms} room(s)`);
        $('.current-transaction-tab #curr_adults').text(`${currentTransaction.adults} Adult(s)`);
        $('.current-transaction-tab #curr_childrens').text(`${currentTransaction.children} Child(ren)`);
        $('.current-transaction-tab #curr_amount').text(`${currentTransaction.amount.toLocaleString()} RWF`);
    };

    // Display toast notification
    function showToast(title, message, type) {
        const toastContainer = $('#showToast');
        toastContainer.html('');
        
        const toast = `
        <div class="bs-toast toast toast-placement-ex m-2 fade bg-${type} top-0 end-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i style="font-size:35px" class="bx ${type === 'success' ? 'bx-check-double' : 'bx-error'} me-2"></i>
                <div class="me-auto fw-semibold">${title}</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>
        `;

        toastContainer.append(toast);

        const toastElement = toastContainer.find('.toast');
        const toastInstance = new bootstrap.Toast(toastElement, {
            delay: 3000
        });
        toastInstance.show();
    }

    // Handle room search
    $('#searchRoom').on('click', function () {
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        const checkinDate = dateRange[0];
        const checkoutDate = dateRange[1];
        const numAdults = $('#adults').val();
        const numChildren = $('#children').val();

        if (numAdults == 0) {
            showToast('Validation Error', 'Please select at least one adult.', 'danger');
            return;
        }

        $.ajax({
            url: handleRooms,
            method: 'POST',
            data: JSON.stringify({
                checkin_date: checkinDate,
                checkout_date: checkoutDate,
                num_adults: numAdults,
                num_children: numChildren
            }),
            contentType: "application/json",
            success: function (response) {
                const res = response;
                
                if (res.status == 200) {
                    availableRoomsByClass = res.data;
                    const resultsTable = $('#room-search-result-tb tbody');
                    resultsTable.empty();

                    if (res.data.length == 0) {
                        resultsTable.append('<tr><td colspan="8"><span class="alert alert-info text-black">No rooms found.</span></td></tr>');
                    } else {
                        res.data.forEach(room => {
                            let basePrice = parseInt(room.base_price).toLocaleString();
                            const row = `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-house-door-fill me-2"></i>
                                        ${room.room_class}
                                    </div>
                                    <div hidden class="text-muted">${room.num_available_rooms} room(s)</div>
                                </td>
                                <td>${checkinDate} - ${checkoutDate}</td>
                                <td>${room.capacity}</td>
                                <td>${basePrice} RWF</td>
                                <td>${room.features}</td>
                                <td>${room.bed_types}</td>
                                <td>${room.bed_details}</td>                                
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary add-room" 
                                        data-room-class-id="${room.room_class_id}" 
                                        data-capacity="${room.capacity}" 
                                        data-room-price="${room.base_price}">
                                        Add
                                    </button>
                                </td>
                            </tr>
                        `;
                            resultsTable.append(row);
                        });
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showToast('Error', 'Failed to search for rooms.', 'danger');
            }
        });
    });

    // Handle Add Room button click
    $('table').on('click', '.add-room', function () {
        const roomClassID = $(this).data('room-class-id');
        const capacity = $(this).data('capacity');
        const roomPrice = $(this).data('room-price');

        // Validate capacity
        if (parseInt($('#adults').val()) > capacity) {
            showToast('Error', `Total guests exceed room capacity (${capacity})`, 'danger');
            console.log(`Total guests exceed room capacity (${capacity})`)
            return;
        }

        currentTransaction.roomClassID = roomClassID;
        currentTransaction.roomPrice = roomPrice;
        currentTransaction.roomID = null;

        // Disable all add-room buttons
        $('table').find('.add-room').prop('disabled', true);

        // Update counts
        currentTransaction.rooms = 1;
        currentTransaction.adults = parseInt($('#adults').val());
        currentTransaction.children = parseInt($('#children').val());

        // Get dates
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        currentTransaction.checkinDate = dateRange[0];
        currentTransaction.checkoutDate = dateRange[1];

        // Calculate amount
        const nights = getNumberofNights();
        currentTransaction.amount = (roomPrice * nights);

        updateCurrentTransactionTab();
        showToast('Success', 'Room added to booking', 'success');
    });

    // Handle clear button click
    $('.clear-span').on('click', function () {
        currentTransaction = {
            rooms: 0,
            adults: 0,
            children: 0,
            amount: 0,
            roomID: null,
            roomClassID: null,
            roomPrice: 0,
            checkinDate: null,
            checkoutDate: null,
            selectedRoomNumber: null
        };

        // Go back to book-content tab
        $('#book-content').tab('show');

        // Enable add-room buttons
        $('table').find('.add-room').prop('disabled', false);

        updateCurrentTransactionTab();
        showToast('Cleared', 'Booking details reset', 'success');
    });

    // Calculate number of nights
    const getNumberofNights = () => {
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        if (dateRange.length < 2) return 0;
        
        const checkinDate = new Date(dateRange[0]);
        const checkoutDate = new Date(dateRange[1]);
        
        if (isNaN(checkinDate) || isNaN(checkoutDate)) return 0;
        
        const diffTime = Math.abs(checkoutDate - checkinDate);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
    }

    // Function to populate booking summary table
    const populateBookingSummaryTable = () => {
        const summaryTableBody = $('#confirm-content table tbody');
        summaryTableBody.empty();

        const room = availableRoomsByClass.find(r => r.room_class_id == currentTransaction.roomClassID);
        if (!room) return;

        const nights = getNumberofNights();
        const vatAmount = (currentTransaction.roomPrice * nights * 0.1);
        // Vat amout removed
        totalBookingAmount = (currentTransaction.roomPrice * nights);
        finalRoomPrice = currentTransaction.roomPrice;
        finalVatAmount = vatAmount;

        $('#total-booking-price').text(totalBookingAmount.toLocaleString());

        const roomRow = `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-house-door-fill me-2"></i>
                        ${room.room_class}
                    </div>
                </td>
                <td>${currentTransaction.checkinDate} -<br>${currentTransaction.checkoutDate}</td>
                <td>${nights}</td>
                <td>
                    <span id="confirm-room-price">${currentTransaction.roomPrice.toLocaleString()}</span> RWF
                </td>
                <td>${room.features}</td>
                <td>${room.bed_types}</td>
                <td>
                    <select id="room-number-select" class="form-select form-select-sm">
                        <!-- Room numbers will populate here -->
                    </select>
                </td>
                <td>${room.floor_number}</td>
                <td>
                    <button type="button" class="btn change-price-btn">
                        <span class="text-primary fs-6 fw-semibold">
                            <i class='bx bx-edit-alt' style='color:#ffffff'></i> Change Price
                        </span>
                    </button>
                </td>
            </tr>
        `;
        summaryTableBody.append(roomRow);

        // Fetch available room numbers
        fetchAvailableRoomNumbers(
            room.room_class_id, 
            currentTransaction.checkinDate, 
            currentTransaction.checkoutDate, 
            currentTransaction.adults + currentTransaction.children
        );
    };

    // Fetch available room numbers
    const fetchAvailableRoomNumbers = (roomClassID, checkinDate, checkoutDate, totalGuests) => {
        $.ajax({
            url: '../api/rooms/get_available_rooms_per_booking.php',
            method: 'GET',
            data: {
                room_class: roomClassID,
                checkin_date: checkinDate,
                checkout_date: checkoutDate,
                total_guests: totalGuests,
            },
            success: function (response) {
                const responses = response;
                const selectElement = $('#room-number-select');
                selectElement.empty();

                if (responses.status == 200 && responses.data.length > 0) {
                    const rooms = responses.data;
                    
                    // Set first room as default
                    const firstRoom = rooms[0];
                    selectElement.append(`<option value="${firstRoom.room_number}" data-id="${firstRoom.id}" selected>
                        ${firstRoom.room_number}
                    </option>`);
                    
                    // Update transaction with actual room ID
                    currentTransaction.roomID = firstRoom.id;
                    currentTransaction.selectedRoomNumber = firstRoom.room_number;

                    // Add other rooms
                    rooms.slice(1).forEach(room => {
                        selectElement.append(`<option value="${room.room_number}" data-id="${room.id}">
                            ${room.room_number}
                        </option>`);
                    });
                } else {
                    selectElement.append('<option>No rooms available</option>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showToast('Error', 'Failed to fetch room numbers', 'danger');
            }
        });
    };

    // Handle room selection change
    $(document).on('change', '#room-number-select', function() {
        const selectedRoomID = $(this).find(':selected').data('id');
        const selectedRoomNumber = $(this).val();
        currentTransaction.roomID = selectedRoomID;
        currentTransaction.selectedRoomNumber = selectedRoomNumber;
    });

    // Toggle change price modal
    $(document).on('click', '.change-price-btn', function () {
        $('#changePriceModal').modal('show');
        const currentPrice = $('#confirm-room-price').text();
        $('#changed-price').val(parseInt(currentPrice.replace(/,/g, '')));
    });

    // Save changed price
    $(document).on('click', '#save-price-btn', function () {
        const changedPrice = $('#changed-price').val();
        const nights = getNumberofNights();
        const vatAmount = (changedPrice * nights * 0.1);

        $('#confirm-room-price').text(changedPrice);
        finalRoomPrice = parseInt(changedPrice);
        finalVatAmount = vatAmount;
        
        // With vat
        // const totalAmount = (changedPrice * nights) + vatAmount;
        // without Vat
        const totalAmount = changedPrice * nights;
        
        totalBookingAmount = totalAmount;
        $('#total-booking-price').text(totalAmount.toLocaleString());
        $('.current-transaction-tab #curr_amount').text(`${totalAmount.toLocaleString()} RWF`);
        $('#changePriceModal').modal('hide');
        showToast('Success', 'Price updated successfully', 'success');
    });

    // Navigate to Confirm tab
    $('button[data-bs-target="#confirm-content"]').on('click', function () {
        if (currentTransaction.rooms === 0) {
            showToast('Error', 'Please add a room first', 'danger');
            return;
        }
        populateBookingSummaryTable();
    });

    // Handle booking confirmation
    $('#confirm-btn').on('click', function () {
        // Validate required fields
        const requiredFields = [
            '#firstName', '#lastName', '#phone', 
            '#nationality', '#residence', '#adress'
        ];
        
        let isValid = true;
        requiredFields.forEach(field => {
            if ($(field).val().trim() === '') {
                isValid = false;
                $(field).addClass('is-invalid');
            } else {
                $(field).removeClass('is-invalid');
            }
        });

        // Validate room selection
        if (!currentTransaction.roomID) {
            showToast('Error', 'Please select a room number', 'danger');
            isValid = false;
        }

        // Validate room option
        const selectedOption = $('input[name="inlineRadioOptions"]:checked').val();
        if (!selectedOption) {
            $('#roomOptionsContainer').addClass('is-invalid');
            isValid = false;
        } else {
            $('#roomOptionsContainer').removeClass('is-invalid');
        }

        if (!isValid) {
            showToast('Error', 'Please fill in all required fields', 'danger');
            return;
        }

        // Prepare booking data
        const bookingData = {
            booking_type: $('input[name="btnradio"]:checked').val(),
            guest_type: $('#guest-type').val(),
            notify_guest: $('#notifyGuest').is(':checked'),
            guest_info: {
                guest_id: $('#guestID').val(),
                first_name: $('#firstName').val(),
                last_name: $('#lastName').val(),
                date_of_birth: $('#dateOfBirth').val(),
                place_of_birth: $('#placeOfBirth').val(),
                phone: $('#phone').val(),
                nationality: $('#nationality').val(),
                email: $('#email').val(),
                residence: $('#residence').val(),
                address: $('#adress').val(),
                profession: $('#profession').val(),
                passport_number: $('#toggleSwitch').is(':checked') ? $('#passportNumber').val() : '',
                passport_expiration_date: $('#toggleSwitch').is(':checked') ? $('#passExpiration').val() : '',
                identification: $('#toggleSwitch').is(':checked') ? '' : $('#identificationMain').val(),
                booking_comment: $('#bookingComment').val()
            },
            checkin_date: currentTransaction.checkinDate,
            checkout_date: currentTransaction.checkoutDate,
            num_adults: currentTransaction.adults,
            num_children: currentTransaction.children,
            coming_from: $('#coming_from').val(),
            going_to: $('#going_to').val(),
            booked_from: $('#bookedFrom').val(),
            company: $('#company').val(),
            booking_status: $('#bookStatus').val(),
            duration: getNumberofNights(),
            room_id: currentTransaction.roomID,
            room_number: currentTransaction.selectedRoomNumber,
            room_option: selectedOption,
            room_price: finalRoomPrice,
            vat_amount: finalVatAmount,
            total_booking_amount: totalBookingAmount,
        };

        $.ajax({
            url: globalApi,
            method: 'POST',
            data: JSON.stringify(bookingData),
            contentType: 'application/json',
            success: function (response) {
                const res = response;
                if (res.status == 201) {
                    $('#addBookingOffcanvas').offcanvas('hide');
                    Swal.fire({
                        title: 'Success',
                        text: 'Room booked successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res.message || 'Booking failed',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while confirming the booking.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Helper function for date difference
    function getDiff(checkedin, st, dayClosedAt) {
        const date1 = new Date(checkedin);
        const date2 = new Date(dayClosedAt);

        if (isNaN(date1.getTime()) || isNaN(date2.getTime())) {
            return 0;
        }

        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        return st == 6 ? diffDays : 0;
    }

    // Initialize DataTable
    $('#tableBookings').DataTable();
});