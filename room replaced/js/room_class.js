$(document).ready(function () {
    const globalApi = '../api/room_classes';
    
    const fetchClasses = () => {
        $.ajax({
            url: `${globalApi}/read.php`,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                console.log(response);
                const classList = $('#class-list');
                classList.empty();

                if (response.status == 200) {
                    response.room_classes.forEach(roomClass => {
                        const basePrice = parseInt(roomClass.base_price).toLocaleString();
                        const row = `
                        <tr>
                            <td>${roomClass.id}</td>
                            <td>${roomClass.class_name}</td>
                            <td>${basePrice} RWF</td>
                            
                            <td>
                                <button class="btn edit-btn" title="Edit" data-id="${roomClass.id}" data-name="${roomClass.class_name}" data-base-price="${roomClass.base_price}"><i style="font-size:25px;" class="bx bx-edit"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${roomClass.id}"><i style="font-size:25px;" class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    `;
                        classList.append(row);
                    });
                } else {
                    console.log(response.message);
                    classList.html(`<p>${response.message}</p>`);
                }
            }
        });
    };

    // Create and update class
    $('#class-form').on('submit', function (event) {
        event.preventDefault();
        const classId = $('#class_id').val(); // Call val() as a function
        const className = $('#class_name').val();
        const basePrice = $('#base_price').val();
        const url = classId ? `${globalApi}/update.php` : `${globalApi}/create.php`;
        const data = classId ? {
            room_class_id: classId,
            class_name: className,
            base_price: basePrice
        } : {
            class_name: className,
            base_price: basePrice
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: JSON.stringify(data), // Serialize the data properly
            contentType: 'application/json',
            success: function (response) {
                const data = JSON.parse(response);
                console.log(data);
                if (data.status == 201 || data.status == 200) {
                    showToast('Well done!', data.message, data.msg_type);
                    fetchClasses();
                    $('#class-form')[0].reset(); // Reset the form 
                    $('#class_id').val(''); // Clear the hidden input field
                    $('#class-form button').text('Add Class'); // Reset button text
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
    $('#class-list').on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const basePrice = $(this).data('base-price');
        console.log(id, name, basePrice);
        $('#class_id').val(id);
        $('#class_name').val(name);
        $('#base_price').val(basePrice);
        $('#class-form button').text('Update Class');
    });

       // Delete class
       $('#class-list').on('click', '.delete-btn', function () {
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
                        class_id: id
                    }),
                    contentType: 'application/json',
                    success: function (response) {
                        console.log(response);
                        const data = JSON.parse(response);
                        if (data.status == 201 || data.status == 200) {
                            showToast('Well done!', data.message, data.msg_type);
                            fetchClasses();
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

    fetchClasses();
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