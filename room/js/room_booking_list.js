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

    // Function to fetch and store all guests
    const fetchGuests = () => {
        $.ajax({
            url: guestsApi,
            method: 'GET',
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status == 200) {
                    allGuests = data.guests;
                } else {
                    // console.log(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while fetching the guests.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    };

    fetchGuests(); // Fetch guests on page load

    // Function to filter and display guest results
    const filterGuests = (searchTerm) => {
        const filteredGuests = allGuests.filter(guest => {
            const guestName = `${guest.first_name +' '+guest.last_name} (${guest.email_address})`.toLowerCase();
            return guestName.includes(searchTerm.toLowerCase());
        });

        const resultsDiv = $('#searchGuestResults');
        resultsDiv.empty();

        if (filteredGuests.length > 0) {
            filteredGuests.forEach(guest => {
                const guestElement = $(`<a href="#" class="dropdown-item" data-guest='${JSON.stringify(guest)}'>${guest.first_name + ' ' +guest.last_name} (${guest.email_address})</a>`);
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
                // console.log(data)
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
                // console.log(data)
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
                // console.log(response)
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
            url: roomOptionsApi, // Make sure this URL is correct
            method: 'GET',
            success: function (data) {
                // console.log(data);
                const response = JSON.parse(data);
                const roomOptionsContainer = $('#roomOptionsContainer');
                roomOptionsContainer.empty();

                if (response.status == 200) {
                    response.data.forEach(option => {
                        const optionElement = `
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="option_${option.id}" value="${option.id}" />
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
                             let room_price = parseInt(bookings.bookings).toLocaleString();
                             let consumed = getDiff(booking.checkin_date, booking.booking_status_id, booking.checkout_date);
                             
                            let actionData = `
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <button class="btn edit-btn text-info" title="Details" data-id="${booking.id}" data-booking='${JSON.stringify(booking)}'> <i class="bx bx-link-external"></i> Details</button>
                                </div>
                            </div>
                            `;
                            // handle booking statuses [Pending, Confirmed, Cancelled, No-Show]  by asigning them colors
                            let statusColor = '';
                            switch (booking.booking_status_name) {
                                case 'Pending':
                                    statusColor = 'badge bg-label-warning';
                                    break;
                                case 'Confirmed':
                                    statusColor = 'badge bg-label-success';
                                    break;
                                case 'Cancelled':
                                    statusColor = 'badge bg-label-danger';
                                    break;
                                case 'No-Show':
                                    statusColor = 'badge bg-label-secondary';
                                    break;
                                default:
                                    statusColor = 'badge bg-label-secondary';
                                    break;
                            }

                            // handle payment statuses [Pending, Paid, Cancelled]  by asigning them colors
                            const payment_status = booking.payment_status_name == null ? 'Pending' : booking.payment_status_name;
                            let paymentColor = '';
                            switch (payment_status) {
                                case 'Pending':
                                    paymentColor = 'badge bg-label-warning';
                                    break;
                                case 'Paid':
                                    paymentColor = 'badge bg-label-success';
                                    break;
                                case 'Cancelled':
                                    paymentColor = 'badge bg-label-danger';
                                    break;
                                default:
                                    paymentColor = 'badge bg-label-secondary';
                                    break;
                            }                            

                            tableBookings.row.add([
                                i++,
                                booking.room_number,
                                booking.first_name + ' ' + booking.last_name,
                                booking.duration,
                                booking.checkin_date,
                                booking.checkout_date,
                                 parseInt(booking.room_price).toLocaleString() + ' RWF',
                                '+' + parseInt(booking.room_price)  * parseInt(consumed).toLocaleString()+ ' RWF', // the balance amount will be calculated with real data once payment impremented
                                `<span class="${statusColor}">${booking.booking_status_name}</span>`,
                                `<span class="${paymentColor}">${payment_status}</span>`,
                                `<button type="button" class="btn rounded-pill me-2 btn-outline-secondary btn-sm details-btn" data-id="${booking.booking_id}" data-booking='${JSON.stringify(booking)}'>Details</button>`
                            ]).draw(true);
                        });
                    } else {
                        tableBookings.draw(false);
                    }
                } else {
                    // console.log(res.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while fetching the data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    };

    $(document).on('click','.details-btn', function(){
        const bookingId = $(this).data('id');
        const bookingData = $(this).data('booking');
        
        // Create a URL with query parameters
        const url = window.location.href.split('/').slice(0, -1).filter(a => a !== 'rm').join('/') +
        '?resto=room_booking_details&&booking_id=' + bookingId;

        // Navigate to the details page
        window.location.href = `${url}`;
    });

    // compute duration days
    const calculateDuration = () => {
        const checkinDate = new Date($('#checkin_date').val());
        const checkoutDate = new Date($('#checkout_date').val());
        const timeDiff = Math.abs(checkoutDate - checkinDate);
        const duration = Math.ceil(timeDiff / (1000 * 3600 * 24));
        $('#duration').val(duration > 0 ? duration : 0);
    };
    $('#checkin_date, #checkout_date').on('change', calculateDuration);

    fetchGuests();
    fetchPaymentStatuses();
    fetchPaymentModes();
    fetchBookingStatuses();
    fetchRoomOptions();
    fetchBookings(); // Initial fetch

    // Create and update booking
    $('#booking-form').on('submit', function (event) {
        event.preventDefault();
        const bookingId = $('#booking_id').val();
        const method = bookingId ? 'PUT' : 'POST';
        const data = bookingId ? {
            id: bookingId,
            guest_id: $('#guest_id').val(),
            payment_status_id: $('#payment_status_id').val(),
            checkin_date: $('#checkin_date').val(),
            checkout_date: $('#checkout_date').val(),
            duration: $('#duration').val(),
            num_adults: $('#num_adults').val(),
            num_children: $('#num_children').val(),
            booking_amount: $('#booking_amount').val(),
            coming_from: $('#coming_from').val(),
            going_to: $('#going_to').val(),
            mode_of_payment_id: $('#mode_of_payment_id').val(),
            booked_from: $('#booked_from').val(),
            company: $('#company').val(),
            other_note: $('#other_note').val()
        } : {
            guest_id: $('#guest_id').val(),
            payment_status_id: $('#payment_status_id').val(),
            checkin_date: $('#checkin_date').val(),
            checkout_date: $('#checkout_date').val(),
            duration: $('#duration').val(),
            num_adults: $('#num_adults').val(),
            num_children: $('#num_children').val(),
            booking_amount: $('#booking_amount').val(),
            coming_from: $('#coming_from').val(),
            going_to: $('#going_to').val(),
            mode_of_payment_id: $('#mode_of_payment_id').val(),
            booked_from: $('#booked_from').val(),
            company: $('#company').val(),
            other_note: $('#other_note').val()
        };

        $.ajax({
            url: globalApi,
            method: method,
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                // console.log(response);
                const data = response;
                
                if (data.status == 201 || data.status == 200) {
                    showBookingAlert('Well done!', data.message, data.msg_type);
                    // fetchBookings();
                    $('#booking-form')[0].reset();
                    $('#booking_id').val('');
                    $('#booking-form button').text('Add Booking');
                    $('#addBookingModal').modal('hide');
                } else {
                    showBookingAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showBookingAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#bookings-list').on('click', '.edit-btn', function () {
        const booking = $(this).data('booking');
        $('#booking_id').val(booking.id);
        $('#guest_id').val(booking.guest_id);
        $('#payment_status_id').val(booking.payment_status_id);
        $('#checkin_date').val(booking.checkin_date);
        $('#checkout_date').val(booking.checkout_date);
        $('#duration').val(booking.duration);
        $('#num_adults').val(booking.num_adults);
        $('#num_children').val(booking.num_children);
        $('#booking_amount').val(booking.booking_amount);
        $('#coming_from').val(booking.coming_from);
        $('#going_to').val(booking.going_to);
        $('#mode_of_payment_id').val(booking.mode_of_payment_id);
        $('#booked_from').val(booking.booked_from);
        $('#company').val(booking.company);
        $('#other_note').val(booking.other_note);
        $('#booking-form button').text('Update Booking');
        $('#addBookingModal').modal('show');
    });

    // Delete booking
    $('#bookings-list').on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        const confirmation = confirm('Are you sure you want to delete this booking?');
        if (confirmation) {
            $.ajax({
                url: globalApi,
                method: 'DELETE',
                data: JSON.stringify({
                    id: id
                }),
                contentType: 'application/json',
                success: function (response) {
                    
                    const data = response;
                    if (data.status == 200) {
                        showBookingAlert('Well done!', data.message, data.msg_type);
                        fetchBookings();
                    } else {
                        showBookingAlert('Error', data.message, data.msg_type);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    showBookingAlert('Error', 'An error occurred while processing your request.', 'error');
                }
            });
        }
    });

    // ++++++++++++++++++++++++++++++++++++++++++++++++++
    // booking process    
    $('#searchRoom').on('click', function () {
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        const checkinDate = dateRange[0];
        const checkoutDate = dateRange[1];
        const numAdults = $('#adults').val();
        const numChildren = $('#children').val();

        // validate numAdults to not be 0
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
                // console.log(response)
                
                const res = response;
                
                if (res.status == 200) {
                    const totalGuests = currentTransaction.adults + currentTransaction.children;
                    const rooms = res.data;
                    availableRoomsByClass = res.data;
                    const resultsTable = $('#room-search-result-tb tbody');
                    resultsTable.empty();

                    // check if rooms empty
                    if (rooms.length == 0) {
                        resultsTable.append('<tr style=""><td colspan="8"><span class="alert alert-info text-black" role="alert">No rooms found.</span></td></tr>');
                    } else {
                        rooms.forEach(room => {
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
                                    <button type="button" class="btn btn-sm btn-primary add-room" data-room-id="${room.id}" data-room-class-id="${room.room_class_id}" data-capacity="${room.capacity}" data-room-price="${room.base_price}">Add</button>
                                </td>
                            </tr>
                        `;
                            resultsTable.append(row);
                        });
                    }
                } else {
                    // console.log(res.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown, jqXHR.responseText);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while fetching the data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    let currentTransaction = {
        rooms: 0,
        adults: 0,
        children: 0,
        amount: 0,
        roomID: null,
        roomClass: null,
        checkinDate: null,
        checkoutDate: null,
    };

    // Function to update current transaction tab
    const updateCurrentTransactionTab = () => {
        $('.current-transaction-tab #curr_rooms').text(`${currentTransaction.rooms} room(s)`);
        $('.current-transaction-tab #curr_adults').text(`${currentTransaction.adults} Adult(s)`);
        $('.current-transaction-tab #curr_childrens').text(`${currentTransaction.children} Child(ren)`);
        $('.current-transaction-tab #curr_amount').text(`${currentTransaction.amount} RWF`);
    };

    // Function to show toast
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

    // Handle Add Room button click
    $('table').on('click', '.add-room', function () {
        const roomId = $(this).data('room-id');
        const roomClass = $(this).data('room-class-id');
        const capacity = $(this).data('capacity');

        // if total adults and children is greater than capacity show toast
        if (currentTransaction.adults + currentTransaction.children > capacity) {
            showToast('Error', `Total adults and children cannot be greater than ${capacity}`, 'danger');
            return;
        }

        currentTransaction.roomID = roomId;
        currentTransaction.roomClass = roomClass;

        // disable all add-room buttons
        $('table').find('.add-room').prop('disabled', true);

        const quantity = 1;
        // const roomPrice = $(`.add-room[data-room-id="${roomId}"]`).closest('tr').find('td:nth-child(4)').text().split(' ')[0].replace(/,/g, '');
        const roomPrice = $(this).data('room-price');

        currentTransaction.rooms += parseInt(quantity);
        currentTransaction.amount += (parseInt(roomPrice) * parseInt(quantity));

        // Assuming num_adults and num_children are available per room; update as required
        currentTransaction.adults += parseInt($('#adults').val());
        currentTransaction.children += parseInt($('#children').val());

        // initialize checkin and checkout dates
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        currentTransaction.checkinDate = dateRange[0];
        currentTransaction.checkoutDate = dateRange[1];

        updateCurrentTransactionTab();
        showToast('Success', 'Room added to the booking.', 'success');
    });

    // handle clear button click
    $('.clear-span').each(function () {
        $(this).on('click', function () {
            currentTransaction = {
                rooms: 0,
                adults: 0,
                children: 0,
                amount: 0,
                roomID: null,
                roomClass: null,
                checkinDate: null,
                checkoutDate: null
            };

            // go back to book-content tab
            $('#book-content').tab('show');

            // set all add-room button back to enabled
            $('table').find('.add-room').prop('disabled', false);

            updateCurrentTransactionTab();
            showToast('Success!', 'Booking details cleared', 'success');
        })
    });

    const getNumberofNights = () => {
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        const checkinDate = dateRange[0];
        const checkoutDate = dateRange[1];

        // calculate duration
        const checkinDateParts = checkinDate.split('-');
        const checkoutDateParts = checkoutDate.split('-');
        const checkinDateObj = new Date(checkinDateParts[0], checkinDateParts[1] - 1, checkinDateParts[2]);
        const checkoutDateObj = new Date(checkoutDateParts[0], checkoutDateParts[1] - 1, checkoutDateParts[2]);
        const duration = checkoutDateObj - checkinDateObj;
        const numberOfNights = Math.ceil(duration / (1000 * 60 * 60 * 24));
        return numberOfNights;

    }

    // Function to populate booking summary table
    const populateBookingSummaryTable = () => {
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        const checkinDate = dateRange[0];
        const checkoutDate = dateRange[1];

        const summaryTableBody = $('#confirm-content table tbody');
        summaryTableBody.empty(); // Clear existing rows

        availableRoomsByClass.forEach((room, index) => {
            if (room.id == currentTransaction.roomID) {
                const totalGuests = currentTransaction.adults + currentTransaction.children;

                fetchAvailableRoomNumbers(room.room_class_id, currentTransaction.checkinDate, currentTransaction.checkoutDate, totalGuests, room.id);

                const nights = getNumberofNights() == 0 ? 1 : getNumberofNights();
                const vatAmount = (room.base_price * nights * 0.1);
                let basePrice = parseInt(room.base_price).toLocaleString();
                // total booking price
                const totalBookingPrice = (room.base_price * nights);
                totalBookingAmount = totalBookingPrice;
                finalRoomPrice = room.base_price;
                finalVatAmount = vatAmount;
                const totalBookingPriceFormatted = totalBookingPrice.toLocaleString();
                $('#total-booking-price').text(totalBookingPriceFormatted);

                const roomRow = `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-house-door-fill me-2"></i>
                            ${room.room_class}
                        </div>
                        
                    </td>
                    <td>${checkinDate} -<br>${checkoutDate}</td>
                    <td>${getNumberofNights()}</td>
                    <td>
                        <span id="confirm-room-price">${basePrice}</span> RWF <br>
                     
                    </td>
                    <td>${room.features}</td>
                    <td>${room.bed_types}</td>
                    <td>
                        <select id="room-number-select-${room.id}" class="form-select form-select-sm room-number-select" data-room-class="${room.room_class}" data-room-id="${room.id}">
                            <!-- Room numbers will be dynamically populated here -->
                            
                        </select>
                    </td>
                    <td>${room.floor_number}</td>
                    <td>
                        <button type="button" id="change=price" class="btn change-price-btn">
                            <span class="text-primary fs-6 fw-semibold"><i class='bx bx-edit-alt' style='color:#ffffff' ></i> Change Price</span>
                        </button>
                    </td>
                </tr>
            `;
                summaryTableBody.append(roomRow);
            }
        });
    };

    // toggle chnage proce modal when change-price-btn is clicked
    $(document).on('click', '.change-price-btn', function () {
        $('#changePriceModal').modal('show');

        const currentPrice = $('#confirm-room-price').text();
        const currentPriceFormatted = parseInt(currentPrice.replace(/,/g, ''));
        $('#changed-price').val(parseInt(currentPriceFormatted));
    });

    // recalculate vat amount
    $(document).on('click', '#save-price-btn', function () {
        const changedPrice = $('#changed-price').val();
        const nights = getNumberofNights() == 0 ? 1 : getNumberofNights();
        const vatAmount = (changedPrice * nights * 0.1);

        $('#confirm-room-price').text(changedPrice);
        $('#confirm-vat-amount').text(vatAmount);

        const totalAmount = changedPrice * nights + vatAmount;
        totalBookingAmount = totalAmount;
        finalRoomPrice = changedPrice;
        finalVatAmount = vatAmount;
        const totalBookingPriceFormatted = totalAmount.toLocaleString();
        $('#total-booking-price').text(totalBookingPriceFormatted);


        $('#changePriceModal').modal('hide');
        showToast('Success!', 'Price Change Successfully!', 'success');
    });


    // Function to fetch available room numbers based on room class
    // const fetchAvailableRoomNumbers = (roomClass, checkinDate, checkoutDate, totalGuests, selectElement) => {
    //     $.ajax({
    //         url: '../../api/rooms/get_available_rooms_per_booking.php',
    //         method: 'GET',
    //         data: {
    //             room_class: roomClass,
    //             checkin_date: checkinDate,
    //             checkout_date: checkoutDate,
    //             total_guests: totalGuests,
    //         },
    //         success: function (response) {
                
    //             // const responses = JSON.parse(response);
    //             const responses = response;
    //             if (responses.status == 200) {
    //                 const rooms = responses.data;
    //                 const selectElement = $('#room-number-select');
    //                 selectElement.empty();
    //                 rooms.forEach(room => {
    //                     const option = `<option data-index="${room.id}" value="${room.room_number}">${room.room_number}</option>`;
    //                     selectElement.append(option);
    //                 });
    //             } else {
    //                 // console.log(responses.message);
    //             }
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             console.error('AJAX error:', textStatus, errorThrown, jqXHR.responseText);
    //             Swal.fire({
    //                 title: 'Error',
    //                 text: 'An error occurred while fetching the room numbers.',
    //                 icon: 'error',
    //                 confirmButtonText: 'OK'
    //             });
    //         }
    //     });
    // };
    const fetchAvailableRoomNumbers = (roomClass, checkinDate, checkoutDate, totalGuests, roomId) => {
    $.ajax({
        // url: '../../api/rooms/get_available_rooms_per_booking.php',
        url: '../api/rooms/get_available_rooms_per_booking.php',
        method: 'GET',
        data: {
            room_class: roomClass,
            checkin_date: checkinDate,
            checkout_date: checkoutDate,
            total_guests: totalGuests,
        },
        success: function (response) {
            const responses = response;
            if (responses.status == 200) {
                const rooms = responses.data;
                const selectElement = $(`#room-number-select-${roomId}`);
                selectElement.empty();
                rooms.forEach(room => {
                    const option = `<option data-id="${room.id}" value="${room.room_number}">${room.room_number}</option>`;
                    selectElement.append(option);
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown, jqXHR.responseText);
            Swal.fire({
                title: 'Error',
                text: 'An error occurred while fetching the room numbers.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
};

// UPDATE CURRENT ROOM SELECTION
$(document).on('change', '.room-number-select', function () {
    // Get the selected room number (value of <option>) 
    const selectedRoomNumber = $(this).val();

    // Get the room ID stored in the selected option's data-id attribute
    const selectedRoomId = $(this).find(':selected').data('id');

    // Get additional attributes
    const roomClass = $(this).data('room-class');
    const roomDataId = $(this).data('room-id');

    // Update currentTransaction.roomID with the selected room ID
    currentTransaction.roomID = selectedRoomId;

    // (Optional) Also keep track of selected room details by room ID
    if (!currentTransaction.selectedRooms) {
        currentTransaction.selectedRooms = {};
    }

    currentTransaction.selectedRooms[roomDataId] = {
        roomNumber: selectedRoomNumber,
        roomId: selectedRoomId,
        roomClass: roomClass,
    };
});



    // Remove room from booking summary
    $(document).on('click', '.remove-room-btn', function () {
        const index = $(this).data('index');
        currentTransaction.rooms.splice(index, 1); // Remove room from transaction
        populateBookingSummaryTable(); // Re-populate table
        updateCurrentTransactionTab(); // Update the current transaction summary
    });

    // Navigate to Confirm tab and populate summary
    $('button[data-bs-target="#confirm-content"]').on('click', function () {
        populateBookingSummaryTable();
    });

    // Handle booking confirmation
    $('#confirm-btn').on('click', function () {
        // get total booking price
        const selectedOption = $('input[name="inlineRadioOptions"]:checked').val();
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
            room_option: selectedOption,
            room_price: parseInt(finalRoomPrice),
            vat_amount: finalVatAmount,
            total_booking_amount: totalBookingAmount,
        };

        // Validate required fields
        const requiredFields = ['#firstName', '#lastName', '#phone', '#nationality', '#residence', '#adress'];
        let isValid = true;
        requiredFields.forEach(field => {
            if ($(field).val().trim() === '') {
                isValid = false;
                $(field).addClass('is-invalid');
            } else {
                $(field).removeClass('is-invalid');
            }
        });

        // Room option validation        
        if (!selectedOption) {
            isValid = false;
            $('#roomOptionsContainer').addClass('is-invalid');
        } else {
            $('#roomOptionsContainer').removeClass('is-invalid');
        }

        if (!isValid) {
            showToast('Error', 'Please fill in all required fields', 'error');
            return;
        }

        $.ajax({
            url: globalApi,
            method: 'POST',
            data: JSON.stringify(bookingData),
            contentType: 'application/json',
            success: function (response) {
                
                 const res= response;
                if (res.status == 201) {
                    
                    $('#addBookingOffcanvas').offcanvas('hide');
                    window.location.reload();
                    Swal.fire({
                        title: 'Success',
                        text: 'Room booked successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Optionally, you can reset the form and transaction details
                        // $('#search-room-form')[0].reset();
                        // $('#searchGuestInput').val('');
                        // currentTransaction = {
                        //     rooms: 0,
                        //     adults: 0,
                        //     children: 0,
                        //     amount: 0,
                        //     roomID: null,
                        //     roomClass: null,
                        //     checkinDate: null,
                        //     checkoutDate: null,
                        // };
                        // updateCurrentTransactionTab();
                        alert('Booked Successfully');
                        window.location.reload();
                        
                    });
                } else {
                    $('#addBookingOffcanvas').offcanvas('hide');
                     window.location.reload();
                     alert(res.message);
                    Swal.fire({
                        title: 'Error',
                        text: res.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.value) {
                            $('#addBookingOffcanvas').offcanvas('show');
                        }
                    });;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown, jqXHR.responseText);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while confirming the booking.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Get alert div
    const getBookingAlert = document.getElementById('getBookingAlert');

    // Display message
    function showBookingAlert(title, message, msgType) {
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
        getBookingAlert.innerHTML = alert; // Use innerHTML to render the alert
    }

    fetchBookings(); // Initial fetch
    
    
// function getDiff(checkedin, st, dayClosedAt){
    
    
// var today = new Date();
// var dd = String(today.getDate()).padStart(2, '0');
// var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
// var yyyy = today.getFullYear();

// var now = dayClosedAt;
    
// const date1 = new Date(checkedin);
// const date2 = new Date(now);
// const diffTime = Math.abs(date2 - date1);
// const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); 

// if(st==6){
// return diffDays;
//     }else{
//         return 0;
//     }
    
// }


function getDiff(checkedin, st, dayClosedAt) {

    const date1 = new Date(checkedin);
    const date2 = new Date(dayClosedAt);

    if (isNaN(date1.getTime()) || isNaN(date2.getTime())) {
        console.log("Invalid date(s)");
        return 0;
    }

    const diffTime = Math.abs(date2 - date1);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));


    return st == 6 ? diffDays : 0;
}




});

