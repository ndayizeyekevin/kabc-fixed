$(document).ready(function () {
    const globalApi = '../api/venues/handle_event_types.php';

    const fetchEventTypes = () => {
        $.ajax({
            url: globalApi,
            method: 'GET',
            success: function (data) {
                // console.log(data.data);
                const eventTypesTable = $('#eventTypesTable').DataTable();
                eventTypesTable.clear();
                const obj = data;
                if (obj.status == 200) {
                    if (obj.data.length > 0) {
                        obj.data.forEach(eventType => {
                            eventTypesTable.row.add([
                                eventType.id,
                                eventType.event_name,
                                eventType.event_code,
                                eventType.created_at,
                                `<button class="btn edit-btn text-info" title="Edit" data-id="${eventType.id}" data-event='${JSON.stringify(eventType)}'> <i class="bx bx-edit"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${eventType.id}"><i class="bx bx-trash"></i></button>`
                            ]).draw(false);
                        });
                    } else {
                        eventTypesTable.draw();
                    }
                } else {
                    // console.log(obj.message);
                   //alert(data.message);
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

    fetchEventTypes(); // Initial fetch

    // Create and update event type
    $('#event-type-form').on('submit', function (event) {
        event.preventDefault();
        const eventId = $('#event_id').val();
        const method = eventId ? 'PUT' : 'POST';
        const data = eventId ? {
            id: eventId,
            event_name: $('#event_name').val(),
            event_code: $('#event_code').val(),
        } : {
            event_name: $('#event_name').val(),
            event_code: $('#event_code').val(),
        };

        $.ajax({
            url: globalApi,
            method: method,
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                // console.log(response);
                const data = response;
                // console.log(data);
                if (data.status == 201 || data.status == 200) {
                    showEventAlert('Well done!', data.message, data.msg_type);
                    fetchEventTypes();
                    $('#event-type-form')[0].reset();
                    $('#event_id').val('');
                    $('#event-type-form button').text('Add Event Type');
                    $('#addEventTypeModal').modal('hide');
                } else {
                    showEventAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showEventAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#event-types-list').on('click', '.edit-btn', function () {
        const eventType = $(this).data('event');
        // const eventType = JSON.parse($(this).data('event'));
        $('#event_id').val(eventType.id);
        $('#event_name').val(eventType.event_name);
        $('#event_code').val(eventType.event_code);
        $('#event-type-form .create-event').text('Update Event Type');
        $('#addEventTypeModal').modal('show');
    });

    // Delete event type
    $('#event-types-list').on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        const confirmation = confirm('Are you sure you want to delete this event type?');
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
                        showEventAlert('Well done!', data.message, data.msg_type);
                        fetchEventTypes();
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

    // Get alert div
    const getEventAlert = document.getElementById('getEventAlert');

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

    fetchEventTypes();
});
