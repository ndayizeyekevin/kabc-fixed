$(document).ready(function () {
    const globalApi = '../api/venue_rates/handle_venue_rate_type.php';

    const fetchRoomClasses = () => {
        $.ajax({
            url: globalApi,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                console.log(response);
                const roomClassSelect = $('#room_class_id');
                roomClassSelect.empty();
                if (response.status == 200) {
                    let options = '<option>Choose Room Class</option>'; // Initialize options string
                    response.room_classes.forEach(roomClass => {
                        options += `<option value="${roomClass.id}">${roomClass.class_name}</option>`; // Accumulate options
                    });
                    roomClassSelect.append(options); // Append options after the loop
                }
            }
        });
    };

    const fetchVenueRateTypes = () => {
        $.ajax({
            url: globalApi,
            method: 'GET',
            success: function (data) {
                // console.log(data.data)
                const venueRateTypeList = $('#venue-rate-types-list');
                venueRateTypeList.empty();
const obj = data;
                if (obj.status == 200) {
                    if (obj.data.length > 0) {
                        obj.data.forEach(rateType => {
                            const row = `
                        <tr>
                            <td>${rateType.id}</td>
                            <td>${rateType.type_name}</td>
                            <td>${rateType.created_at}</td>
                            <td>
                                <button class="btn edit-btn text-info" title="Edit" data-id="${rateType.id}" data-rate-type-name="${rateType.type_name}"> <i class="bx bx-edit"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${rateType.id}"><i class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    `;
                            venueRateTypeList.append(row);
                        });
                    } else {
                        venueRateTypeList.html(`<p>No data Found</p>`);
                    }

                } else {
                    console.log(data.message);
                    venueRateTypeList.html(`<p>${data.message}</p>`);
                }
            }
        });
    };

    // Fetch venue rate types
    fetchVenueRateTypes();

    // Create and update class bed type
    $('#venue-rate-type-form').on('submit', function (event) {
        event.preventDefault();
        const rateTypeId = $('#venue_rate_type_id').val();
        const rateTypeName = $('#venue_rate_type_name').val();
        const method = rateTypeId ? 'PUT' : 'POST';
        const data = rateTypeId ? {
            id: rateTypeId,
            type_name: rateTypeName,
        } : {
            type_name: rateTypeName,
        };

        $.ajax({
            url: globalApi,
            method: method,
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                console.log(response)
                const data = response
                console.log(data);
                if (data.status == 201 || data.status == 200) {
                    showRateAlert('Well done!', data.message, data.msg_type);
                    fetchVenueRateTypes();
                    $('#venue-rate-type-form')[0].reset();
                    $('#venue_rate_type_id').val('');
                    $('#class-bed-type-form button').text('Add Class Rate Type');
                } else {
                    showRateAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showRateAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#venue-rate-types-list').on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const typeName = $(this).data('rate-type-name');
        $('#venue_rate_type_id').val(id);
        $('#venue_rate_type_name').val(typeName);
        $('#class-bed-type-form button').text('Update Rate Type');
    });

    // Delete class bed type
    $('#venue-rate-types-list').on('click', '.delete-btn', function () {
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
                        showRateAlert('Well done!', data.message, data.msg_type);
                        fetchVenueRateTypes();
                    } else {
                        showRateAlert('Error', data.message, data.msg_type);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    console.log(jqXHR)

                    showRateAlert('Error', 'An error occurred while processing your request.', 'error');
                }
            });
        }
    });

    fetchVenueRateTypes();
});

// Get alert div
const getRateAlert = document.getElementById('getRateAlert');

// Display message
function showRateAlert(title, message, msgType) {
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
    getRateAlert.innerHTML = alert; // Use innerHTML to render the alert
}