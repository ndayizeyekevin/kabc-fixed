$(document).ready(function () {
    const globalApi = '../api/customers/handle_customers.php';
 
    const fetchCustomers = () => {
        $.ajax({
            url: globalApi,
            method: 'GET',
            success: function (res) {
               //alert(res);
                const customersTable = $('#customersTable').DataTable({
                    retrieve: true,
                });
                customersTable.clear();

const obj = res;

                if (obj.status == 200) {
                    if (obj.data.length > 0) {
                        
                        let i = 1;
                      //  alert(i);
                        obj.data.forEach(customer => {
                            let date_created = customer.created_at;
                            date_created = date_created.substring(0, 10);
                            let actionData = `
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <button class="btn edit-btn text-info" title="Edit" data-id="${customer.id}" data-customer='${JSON.stringify(customer)}'> <i class="bx bx-edit"></i></button>
                                    <button class="btn delete-btn text-danger" title="Delete" data-id="${customer.id}"><i class="bx bx-trash"></i></button>
                                </div>
                            </div>
                            `;

                            customersTable.row.add([
                                i++,
                                customer.names,
                                customer.address,
                                customer.email,
                                customer.phone,
                                customer.identification,
                                customer.tin,
                                date_created,
                                actionData
                            ]).draw(false);
                        });
                    } else {
                        customersTable.draw();
                    }
                } else {
                  //  alert(res);
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
    };

    fetchCustomers(); // Initial fetch

    // Create and update customer
    $('#customer-form').on('submit', function (event) {
        event.preventDefault();
        const customerId = $('#customer_id').val();
        const method = customerId ? 'PUT' : 'POST';
        const data = customerId ? {
            id: customerId,
            names: $('#names').val(),
            Address: $('#address').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            identification: $('#identification').val(),
            tin: $('#tin').val(),
        } : {
            names: $('#names').val(),
            Address: $('#address').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            identification: $('#identification').val(),
            tin: $('#tin').val(),
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
                    showCustomerAlert('Well done!', data.message, data.msg_type);
                    fetchCustomers();
                    $('#customer-form')[0].reset();
                    $('#customer_id').val('');
                    $('#customer-form button').text('Add Customer');
                    $('#addCustomerModal').modal('hide');
                } else {
                    showCustomerAlert('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                console.log(jqXHR.responseText);
                showCustomerAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Populate form with data
    $('#customers-list').on('click', '.edit-btn', function () {
        // const customer = JSON.parse($(this).data('customer'));
        const customer = $(this).data('customer');
        $('#customer_id').val(customer.id);
        $('#names').val(customer.names);
        $('#address').val(customer.address);
        $('#email').val(customer.email);
        $('#phone').val(customer.phone);
        $('#identification').val(customer.identification);
        $('#tin').val(customer.tin);
        $('#customer-form .create-customer').text('Update Customer');
        $('#addCustomerModal').modal('show');
    });

    // Delete customer
    $('#customers-list').on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        const confirmation = confirm('Are you sure you want to delete this customer?');
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
                        showCustomerAlert('Well done!', data.message, data.msg_type);
                        fetchCustomers();
                    } else {
                        showCustomerAlert('Error', data.message, data.msg_type);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    showCustomerAlert('Error', 'An error occurred while processing your request.', 'error');
                }
            });
        }
    });

    // Get alert div
    const getCustomerAlert = document.getElementById('getCustomerAlert');

    // Display message
    function showCustomerAlert(title, message, msgType) {
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
        getCustomerAlert.innerHTML = alert; // Use innerHTML to render the alert
    }

    fetchCustomers();
});
