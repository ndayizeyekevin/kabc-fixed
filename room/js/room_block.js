$(document).ready(function () {
    const globalApi = '../api/floors';
    
    const fetchBlocks = () => {
        $.ajax({
            url: `${globalApi}/read.php`,
            method: 'GET',
            success: function (data) {
                const response = JSON.parse(data);
                console.log(response);
                const blockList = $('#block-list');
                blockList.empty();

                if (response.status == 200) {
                    response.room_blocks.forEach(block => {
                        const row = `
                        <tr>
                            <td>${block.id}</td>
                            <td>${block.floor_number}</td>
                            <td>
                                <button class="btn edit-btn" title="Edit" data-id="${block.id}" data-name="${block.floor_number}"><i style="font-size:25px;" class="bx bx-edit"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${block.id}"><i style="font-size:25px;" class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    `;
                        blockList.append(row);
                    });
                } else {
                    console.log(response.message);
                    blockList.html(`<p>${response.message}</p>`);
                }
            }
        });
    };

    // Create and update block
    $('#block-form').on('submit', function (event) {
        event.preventDefault();
        const blockId = $('#block_id').val(); // Call val() as a function
        const blockName = $('#block_name').val();
        const url = blockId ? `${globalApi}/update.php` : `${globalApi}/create.php`;
        const data = blockId ? {
            block_id: blockId,
            block_name: blockName
        } : {
            block_name: blockName
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: JSON.stringify(data), // Serialize the data properly
            contentType: 'application/json',
            success: function (response) {
                const data = JSON.parse(response);
                console.log(data)
                if (data.status == 201 || data.status == 200) {
                    showToast('Well done!', data.message, data.msg_type);
                    fetchBlocks();
                    $('#block-form')[0].reset(); // Reset the form 
                    $('#block_id').val(''); // Clear the hidden input field
                    $('#block-form button').text('Add Block'); // Reset button text
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
    $('#block-list').on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        console.log(id, name);
        $('#block_id').val(id);
        $('#block_name').val(name);
        $('#block-form button').text('Update Block');
    });

    // Delete block
    $('#block-list').on('click', '.delete-btn', function () {
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
                        block_id: id
                    }),
                    contentType: 'application/json',
                    success: function (response) {
                        console.log(response);
                        const data = JSON.parse(response);
                        if (data.status == 201 || data.status == 200) {
                            showToast('Well done!', data.message, data.msg_type);
                            fetchBlocks();
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

    fetchBlocks();
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