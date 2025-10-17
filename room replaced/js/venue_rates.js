$(document).ready(function () {
    const globalApi = '../api/venue_rates/handle_venue_rates.php';

    const fetchVenues = () => {
        $.ajax({
            url: `../../api/venues/read_venues.php`,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data).venues;
                // console.log(response)
                const venueSelect = $('#venue_id');
                venueSelect.empty();
                venueSelect.append('<option value="">Select Venue</option>');
                response.forEach(venue => {
                    venueSelect.append(`<option value="${venue.id}">${venue.venue_name}</option>`);
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    };

    const fetchRateTypes = () => {
        $.ajax({
            url: '../api/venue_rates/handle_venue_rate_type.php', // Your endpoint to fetch rate types
            method: 'GET',
            success: function (data) {
            const obj = data;
                const response = obj.data;
                const rateTypeSelect = $('#rate_type_id');
                rateTypeSelect.empty();
                rateTypeSelect.append('<option value="">Select Rate Type</option>');
                response.forEach(rateType => {
                    rateTypeSelect.append(`<option value="${rateType.id}">${rateType.type_name}</option>`);
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    };

    const fetchVenueRates = () => {
        $.ajax({
            url: globalApi,
            method: 'GET',
            success: function (data) {
                const venueRatesTable = $('#venueRatesTable').DataTable();
                venueRatesTable.clear();
                const obj = data;
                if (obj.status == 200) {
                    if (obj.data.length > 0) {
                        obj.data.forEach(rate => {
                            venueRatesTable.row.add([
                                rate.id,
                                rate.venue_name,
                                rate.rate_type_name,
                                parseInt(rate.amount).toLocaleString() + 'RWF',
                                rate.start_date,
                                rate.end_date,
                                rate.status,
                                `<button class="btn edit-btn text-info" title="Edit" data-id="${rate.id}" data-rate='${JSON.stringify(rate)}'> <i class="bx bx-edit"></i></button>
                                        <button class="btn delete-btn text-danger" title="Delete" data-id="${rate.id}"><i class="bx bx-trash"></i></button>`
                            ]).draw(false);
                        });
                    } else {
                        venueRatesTable.draw();
                    }
                } else {
                    console.log(obj.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while fetching the data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    };

    fetchVenues(); // Fetch venues and populate select
    fetchRateTypes(); // Fetch rate types and populate select
    fetchVenueRates(); // Fetch venue rates

    // Create and update venue rate
    $('#venue-rate-form').on('submit', function (event) {
        event.preventDefault();
        const rateId = $('#venue_rate_id').val();
        const method = rateId ? 'PUT' : 'POST';
        const data = rateId ? {
            id: rateId,
            venue_id: $('#venue_id').val(),
            rate_type: $('#rate_type_id').val(),
            amount: $('#amount').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            status: $('#status').val(),
        } : {
            venue_id: $('#venue_id').val(),
            rate_type: $('#rate_type_id').val(),
            amount: $('#amount').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            status: $('#status').val(),
        };

        $.ajax({
            url: globalApi,
            method: method,
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                const data = response;
                if (data.status == 201 || data.status == 200) {
                    showAlert('Well done!', data.message, data.msg_type);
                    fetchVenueRates();
                    $('#venue-rate-form')[0].reset();
                    $('#venue_rate_id').val('');
                    $('#venue-rate-form button').text('Add Venue Rate');
                    $('#addVenueRateModal').modal('hide');
                } else {
                    showAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#venueRatesTable').on('click', '.edit-btn', function () {
        const rate = $(this).data('rate');
        // console.log(rate.amount)
        // const rate = JSON.parse($(this).data('rate'));
        $('#venue_rate_id').val(rate.id);
        $('#venue_id').val(rate.venue_id);
        $('#rate_type_id').val(rate.rate_type);
        $('#amount').val(rate.amount);
        $('#start_date').val(rate.start_date);
        $('#end_date').val(rate.end_date);
        $('#status').val(rate.status);
        $('#venue-rate-form .create-rate').text('Update Venue Rate');
        $('#addVenueRateModal').modal('show');
    });

    // Delete venue rate
    $('#venueRatesTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        const confirmation = confirm('Are you sure you want to delete it?');
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
                        showAlert('Well done!', data.message, data.msg_type);
                        fetchVenueRates();
                    } else {
                        showAlert('Error', data.message, data.msg_type);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    showAlert('Error', 'An error occurred while processing your request.', 'error');
                }
            });
        }
    });

    fetchVenueRates();
});

// Get alert div
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
    getAlert.innerHTML = alert; // Use innerHTML to render the alert
}