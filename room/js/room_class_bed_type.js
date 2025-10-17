$(document).ready(function () {
    const globalApi = '../api/room_class_bed_type';

    const fetchRoomClasses = () => {
        $.ajax({
            url: '../api/room_classes/read.php',
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

    const fetchBedTypes = () => {
        $.ajax({
            url: '../api/bed_type/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                console.log(response);
                const bedTypeSelect = $('#bed_type_id');
                bedTypeSelect.empty();
                if (response.status == 200) {
                    let options = '<option>Choose Bed Type</option>'; // Initialize options string
                    response.bed_types.forEach(bedType => {
                        options += `<option value="${bedType.id}">${bedType.bed_type_name}</option>`; // Accumulate options
                    });
                    bedTypeSelect.append(options); // Append options after the loop
                }
            }
        });
    };    

    const fetchClassBedTypes = () => {
        $.ajax({
            url: `${globalApi}/read.php`,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                console.log(response);
                const classBedTypeList = $('#class-bed-type-list');
                classBedTypeList.empty();

                if (response.status == 200) {
                    response.room_class_bed_types.forEach(classBedType => {
                        const row = `
                        <tr>
                            <td>${classBedType.id}</td>
                            <td>${classBedType.room_class_name}</td>
                            <td>${classBedType.bed_type_name}</td>
                            <td>${classBedType.num_beds}</td>
                            <td>
                                <button class="btn edit-btn" title="Edit" data-id="${classBedType.id}" data-room-class-id="${classBedType.room_class_id}" data-bed-type-id="${classBedType.bed_type_id}" data-num-beds="${classBedType.num_beds}"><i style="font-size:25px;" class="bx bx-edit"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${classBedType.id}"><i style="font-size:25px;" class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    `;
                        classBedTypeList.append(row);
                    });
                } else {
                    console.log(response.message);
                    classBedTypeList.html(`<p>${response.message}</p>`);
                }
            }
        });
    };

    // Fetch room classes and bed types
    fetchRoomClasses();
    fetchBedTypes();

    // Create and update class bed type
    $('#class-bed-type-form').on('submit', function (event) {
        event.preventDefault();
        const classBedTypeId = $('#class_bed_type_id').val();
        const roomClassId = $('#room_class_id').val();
        const bedTypeId = $('#bed_type_id').val();
        const numBeds = $('#num_beds').val();
        const url = classBedTypeId ? `${globalApi}/update.php` : `${globalApi}/create.php`;
        const data = classBedTypeId ? {
            class_bed_type_id: classBedTypeId,
            room_class_id: roomClassId,
            bed_type_id: bedTypeId,
            num_beds: numBeds
        } : {
            room_class_id: roomClassId,
            bed_type_id: bedTypeId,
            num_beds: numBeds
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                const data = JSON.parse(response);
                console.log(data);
                if (data.status == 201 || data.status == 200) {
                    showToast('Well done!', data.message, data.msg_type);
                    fetchClassBedTypes();
                    $('#class-bed-type-form')[0].reset();
                    $('#class_bed_type_id').val('');
                    $('#class-bed-type-form button').text('Add Class Bed Type');
                } else {
                    showToast('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#class-bed-type-list').on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const roomClassId = $(this).data('room-class-id');
        const bedTypeId = $(this).data('bed-type-id');
        const numBeds = $(this).data('num-beds');
        console.log(id, roomClassId, bedTypeId, numBeds);
        $('#class_bed_type_id').val(id);
        $('#room_class_id').val(roomClassId);
        $('#bed_type_id').val(bedTypeId);
        $('#num_beds').val(numBeds);
        $('#class-bed-type-form button').text('Update Class Bed Type');
    });

    // Delete class bed type
    $('#class-bed-type-list').on('click', '.delete-btn', function () {
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
                        class_bed_type_id: id
                    }),
                    contentType: 'application/json',
                    success: function (response) {
                        console.log(response);
                        const data = JSON.parse(response);
                        if (data.status == 201 || data.status == 200) {
                            showToast('Well done!', data.message, data.msg_type);
                            fetchClassBedTypes();
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

    fetchClassBedTypes();
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