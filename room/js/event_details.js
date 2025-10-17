$(document).ready(function () {    
    const globalApi = '../api/bookings/handle_event_details.php';
    const reservationApi = '../api/bookings/handle_venue_reservations.php';
    const eventTypesApi = '../api/venues/handle_event_types.php';

    // Adjust this API endpoint as needed 
    const fetchEventTypes = () => {
        $.ajax({
            url: eventTypesApi,
            method: 'GET',
            success: function (data) {
                const response = data;
                const eventTypeSelect = $('#event_type');
                eventTypeSelect.empty();
                eventTypeSelect.append('<option value="">Select Event Type</option>');
                if (response.status == 200) {
                    response.data.forEach(eventType => {
                        eventTypeSelect.append(`<option value="${eventType.id}">${eventType.event_name}</option>`);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    };
    fetchEventTypes(); // Fetch event types on page load

    // Fetch event details by reservation ID
    const fetchEventDetails = (reservationId) => {
        $.ajax({
            url: `${globalApi}?reservation_id=${reservationId}`,
            method: 'GET',
            success: function (data) {
                console.log(data.data);
                const eventDetailsList = $('#event-details-list');
                eventDetailsList.empty();

                if (data.status == 200) {
                    if (data.data.length > 0) {
                        data.data.forEach(detail => {
                            const row = `
                                <tr>
                                    <td>${detail.id}</td>
                                    <td>${detail.event_name}</td>
                                    <td>${detail.guest_count}</td>
                                    <td>${detail.setup_requirements}</td>
                                    <td>${detail.catering_needs}</td>
                                    <td>${detail.special_requests}</td>
                                    <td>${detail.created_at}</td>
                                    <td>${detail.updated_at}</td>
                                    <td>
                                        <button class="btn edit-detail-btn text-info" title="Edit" data-id="${detail.id}" data-detail='${JSON.stringify(detail)}'> <i class="bx bx-edit"></i></button>
                                        <button class="btn delete-detail-btn text-danger" title="Delete" data-id="${detail.id}"><i class="bx bx-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            eventDetailsList.append(row);
                        });
                    } else {
                        eventDetailsList.html('<tr><td colspan="9" class="text-center">No event details found</td></tr>');
                    }
                } else {
                    console.log(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while fetching the event details.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    };

    // Fetch reservation details and open offcanvas
    $('#reservations-list').on('click', '.details-btn', function () {
        const reservationId = $(this).data('id');
        $('#reservation_id_details').val(reservationId);

        $.ajax({
            url: `${reservationApi}?id=${reservationId}`,
            method: 'GET',
            success: function (data) {
                if (data.status == 200 && data.data.length > 0) {
                    const reservation = data.data[0];
                    $('#reservationDetails').text(`Reservation for ${reservation.customer_name} at ${reservation.venue_name} on ${reservation.reservation_date}`);
                } else {
                    $('#reservationDetails').text('Reservation details not found');
                }
                fetchEventDetails(reservationId);
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('reservationDetailsOffcanvas'));
                offcanvas.show();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while fetching the reservation details.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Create and update event detail
    $('#event-detail-form').on('submit', function (event) {
        event.preventDefault();
        const detailId = $('#detail_id').val();
        const method = detailId ? 'PUT' : 'POST';
        const data = detailId ? {
            id: detailId,
            reservation_id: $('#reservation_id_details').val(),
            event_type: $('#event_type').val(),
            guest_count: $('#guest_count').val(),
            setup_requirements: $('#setup_requirements').val(),
            catering_needs: $('#catering_needs').val(),
            special_requests: $('#special_requests').val(),
        } : {
            reservation_id: $('#reservation_id_details').val(),
            event_type: $('#event_type').val(),
            guest_count: $('#guest_count').val(),
            setup_requirements: $('#setup_requirements').val(),
            catering_needs: $('#catering_needs').val(),
            special_requests: $('#special_requests').val(),
        };

        $.ajax({
            url: globalApi,
            method: method,
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                console.log(response);
                const data = response;
                console.log(data);
                if (data.status == 201 || data.status == 200) {
                    showEventAlert('Well done!', data.message, data.msg_type);
                    fetchEventDetails($('#reservation_id_details').val());
                    $('#event-detail-form')[0].reset();
                    $('#detail_id').val('');
                    $('#event-detail-form button').text('Add Event Detail');
                } else {
                    showEventAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                console.log(jqXHR.responseText)
                showEventAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#event-details-list').on('click', '.edit-detail-btn', function () {
        const detail = $(this).data('detail');
        $('#detail_id').val(detail.id);
        $('#event_type').val(detail.event_type);
        $('#guest_count').val(detail.guest_count);
        $('#setup_requirements').val(detail.setup_requirements);
        $('#catering_needs').val(detail.catering_needs);
        $('#special_requests').val(detail.special_requests);
        $('#event-detail-form button').text('Update Event Detail');
    });

    // Delete event detail
    $('#event-details-list').on('click', '.delete-detail-btn', function () {
        const id = $(this).data('id');

        const confirmation = confirm('Are you sure you want to delete this event detail?');
        if (confirmation) {
            $.ajax({
                url: globalApi,
                method: 'DELETE',
                data: JSON.stringify({
                    id: id
                }),
                contentType: 'application/json',
                success: function (response) {
                    console.log(response);
                    const data = response;
                    if (data.status == 200) {
                        showEventAlert('Well done!', data.message, data.msg_type);
                        fetchEventDetails($('#reservation_id_details').val());
                    } else {
                        showEventAlert('Error', data.message, data.msg_type);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    showEventAlert('Error', 'An error occurred while processing your request.', 'error');
                }
            });
        }
    });

    // Update reservation status
    const updateReservationStatus = (reservationId, status) => {
        const data = {
            id: reservationId,
            status: status
        };

        $.ajax({
            url: globalApi,
            method: 'PATCH',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                console.log(response);
                const data = response;
                if (data.status == 200) {
                    showEventAlert('Well done!', data.message, data.msg_type);
                    commonFunctions.fetchReservations();
                } else {
                    showEventAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showEventAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    };

    $('#confirm-reservation-btn').on('click', function () {
        const reservationId = $('#reservation_id_details').val();
        updateReservationStatus(reservationId, 'Confirmed');
    });

    $('#cancel-reservation-btn').on('click', function () {
        const reservationId = $('#reservation_id_details').val();
        updateReservationStatus(reservationId, 'Cancelled');
    });

    $('#fulfill-reservation-btn').on('click', function () {
        const reservationId = $('#reservation_id_details').val();
        updateReservationStatus(reservationId, 'Fulfilled');
    });

    // Get alert div
    const getEventAlert = document.getElementById('getReservationDetailsAlert');

    // Display message
    function showEventAlert(title, message, msgType) {
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
        getEventAlert.innerHTML = alert; // Use innerHTML to render the alert
    }

    // Initial fetch for reservations
    commonFunctions.fetchReservations();
});