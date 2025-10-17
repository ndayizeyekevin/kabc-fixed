$(document).ready(function () {
    const globalApi = '../api/bookings/handle_venue_reservations.php';
    const fetchVenuesApi = '../api/venues/read_venues.php';
    const fetchCustomersApi = '../api/customers/handle_customers.php';

    const fetchVenues = () => {
        $.ajax({
            url: fetchVenuesApi,
            method: 'GET',
            success: function (data) {
                // console.log(data)
                const response = JSON.parse(data);
                const venueSelect = $('#venue_id');
                venueSelect.empty();
                venueSelect.append('<option value="">Select Venue</option>');

                if (response.status == 200) {
                    response.venues.forEach(venue => {
                        venueSelect.append(`<option value="${venue.id}">${venue.venue_name}</option>`);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    };

    const fetchCustomers = () => {
        $.ajax({
            url: fetchCustomersApi,
            method: 'GET',
            success: function (data) {
                // console.log(data)
                //const response = data;
                const response = data;
               // alert(response);
                const customerSelect = $('#customer_id');
                customerSelect.empty();
                customerSelect.append('<option value="">Select Customer</option>');

                if (response.status == 200) {
                    response.data.forEach(customer => {
                        customerSelect.append(`<option value="${customer.id}">${customer.names}</option>`);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    };    

    fetchVenues();
    fetchCustomers();
    commonFunctions.fetchReservations(); // Initial fetch

    // Create and update reservation
    $('#reservation-form').on('submit', function (event) {
        event.preventDefault();
        const reservationId = $('#reservation_id').val();
        const method = reservationId ? 'PUT' : 'POST';
        const data = reservationId ? {
            id: reservationId,
            venue_id: $('#venue_id').val(),
            customer_id: $('#customer_id').val(),
            reservation_date: $('#reservation_date').val(),
            reservation_end_date: $('#reservation_end_date').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val(),
            amount: $('#amount').val(),
            status: $('#status').val(),
        } : {
            venue_id: $('#venue_id').val(),
            customer_id: $('#customer_id').val(),
            reservation_date: $('#reservation_date').val(),
            reservation_end_date: $('#reservation_end_date').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val(),
            amount: $('#amount').val(),
            status: $('#status').val(),
        };

        $.ajax({
            url: globalApi,
            method: method,
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                alert('Added');
                window.location.reload();
                const data = response;
                // console.log(data);
                if (data.status == 201 || data.status == 200) {
                    showReservationAlert('Well done!', data.message, data.msg_type);
                    commonFunctions.fetchReservations();
                    $('#reservation-form')[0].reset();
                    $('#reservation_id').val('');
                    $('#reservation-form button').text('Add Reservation');
                    $('#addReservationModal').modal('hide');
                } else {
                    $('#addReservationModal').modal('hide');
                    showReservationAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showReservationAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#reservations-list').on('click', '.edit-btn', function () {
        const reservation = $(this).data('reservation');
        $('#reservation_id').val(reservation.id);
        $('#venue_id').val(reservation.venue_id);
        $('#customer_id').val(reservation.customer_id);
        $('#reservation_date').val(reservation.reservation_date);
        $('#reservation_end_date').val(reservation.reservation_end_date);
        $('#start_time').val(reservation.start_time);
        $('#end_time').val(reservation.end_time);
        $('#amount').val(reservation.amount);
        $('#status').val(reservation.status);
        $('#reservation-form .create-reservation').text('Update Reservation');
        $('#addReservationModal').modal('show');
    });

    // Delete reservation
    $('#reservations-list').on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        const confirmation = confirm('Are you sure you want to delete this reservation?');
        if (confirmation) {
            $.ajax({
                url: globalApi,
                method: 'DELETE',
                data: JSON.stringify({
                    id: id
                }),
                contentType: 'application/json',
                success: function (response) {
                    // console.log(response);
                    const data = response;
                    if (data.status == 200) {
                        showReservationAlert('Well done!', data.message, data.msg_type);
                        commonFunctions.fetchReservations();
                    } else {
                        showReservationAlert('Error', data.message, data.msg_type);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    showReservationAlert('Error', 'An error occurred while processing your request.', 'error');
                }
            });
        }
    });

    // Get alert div
    const getReservationAlert = document.getElementById('getReservationAlert');

    // Display message
    function showReservationAlert(title, message, msgType) {
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
        getReservationAlert.innerHTML = alert; // Use innerHTML to render the alert
    }

    commonFunctions.fetchReservations();
});
