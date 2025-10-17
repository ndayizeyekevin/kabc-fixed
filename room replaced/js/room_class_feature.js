$(document).ready(function () {
    const globalApi = '../api/room_class_feature';
    
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
                    response.room_classes.forEach(roomClass => {
                        const option = `<option value="${roomClass.id}">${roomClass.class_name}</option>`;
                        roomClassSelect.append(option);
                    });
                }
            }
        });
    };

    const fetchFeatures = () => {
        $.ajax({
            url: '../api/features/read.php',
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                console.log(response);
                const featureSelect = $('#feature_id');
                featureSelect.empty();
                if (response.status == 200) {
                    response.features.forEach(feature => {
                        const option = `<option value="${feature.id}">${feature.feature_name}</option>`;
                        featureSelect.append(option);
                    });
                }
            }
        });
    };

    const fetchClassFeatures = () => {
        $.ajax({
            url: `${globalApi}/read.php`,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                alert(response);  // Log the full response
                const classFeatureList = $('#class-feature-list');
                classFeatureList.empty();

                if (response.status == 200) {
                    console.log(response.room_class_features); // Log the specific data array
                    response.room_class_features.forEach(classFeature => {
                        const row = `
                        <tr>
                            <td>${classFeature.id}</td>
                            <td>${classFeature.room_class_name}</td>
                            <td>${classFeature.feature_name}</td>
                            <td>
                                <button class="btn edit-btn" title="Edit" data-id="${classFeature.id}" data-room-class-id="${classFeature.room_class_id}" data-feature-id="${classFeature.feature_id}"><i style="font-size:25px;" class="bx bx-edit"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${classFeature.id}"><i style="font-size:25px;" class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    `;
                        classFeatureList.append(row);
                    });
                } else {
                    console.log(response.message);
                    classFeatureList.html(`<p>${response.message}</p>`);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    };
    // Fetch room classes and features
    fetchRoomClasses();
    fetchFeatures();
    
    // Create and update class feature
    $('#class-feature-form').on('submit', function (event) {
        event.preventDefault();
        const classFeatureId = $('#class_feature_id').val();
        const roomClassId = $('#room_class_id').val();
        const featureId = $('#feature_id').val();
        const url = classFeatureId ? `${globalApi}/update.php` : `${globalApi}/create.php`;
        const data = classFeatureId ? {
            class_feature_id: classFeatureId,
            room_class_id: roomClassId,
            feature_id: featureId
        } : {
            room_class_id: roomClassId,
            feature_id: featureId
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
                    fetchClassFeatures();
                    $('#class-feature-form')[0].reset();
                    $('#class_feature_id').val('');
                    $('#class-feature-form button').text('Add Class Feature');
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
    $('#class-feature-list').on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const roomClassId = $(this).data('room-class-id');
        const featureId = $(this).data('feature-id');
        console.log(id, roomClassId, featureId);
        $('#class_feature_id').val(id);
        $('#room_class_id').val(roomClassId);
        $('#feature_id').val(featureId);
        $('#class-feature-form button').text('Update Class Feature');
    });

    // Delete class feature
    $('#class-feature-list').on('click', '.delete-btn', function () {
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

    fetchClassFeatures();
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
