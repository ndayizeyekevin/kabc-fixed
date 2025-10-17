$(document).ready(function () {
    const apiBaseUrl = '../api/venues';
    let currentPage = 1;
    const itemsPerPage = 10;

    // Fetch lookup values for venue types
    function fetchVenueTypes(targetSelect, callback) {
        $.ajax({
            url: `${apiBaseUrl}/read_venue_types.php`,
            method: 'GET',
            success: function (data) {
                const venueTypes = JSON.parse(data);
                const venueTypeSelect = $(targetSelect);
                venueTypeSelect.empty();
                venueTypeSelect.append(`<option value="">Select Type</option>`);
                venueTypes.forEach(type => {
                    venueTypeSelect.append(`<option value="${type.type_id}">${type.type_name}</option>`);
                });
                if (callback) callback(); // Call the callback after options are populated
            }
        });
    }

    // Fetch lookup values for statuses
    function fetchStatuses(targetSelect, callback) {
        $.ajax({
            url: `${apiBaseUrl}/read_statuses.php`,
            method: 'GET',
            success: function (data) {
                const statuses = JSON.parse(data);
                const statusSelect = $(targetSelect);
                statusSelect.empty();
                statusSelect.append(`<option value="">Select Status</option>`);
                statuses.forEach(status => {
                    statusSelect.append(`<option value="${status.status_id}">${status.status_name}</option>`);
                });
                if (callback) callback(); // Call the callback after options are populated
            }
        });
    }

    // Fetch lookup values for amenities
    function fetchAmenities(selectId) {
        $.ajax({
            url: `${apiBaseUrl}/read_amenities.php`,
            method: 'GET',
            success: function (data) {
                const amenities = JSON.parse(data);
                const amenitySelect = $(selectId);
                amenitySelect.empty();
                amenitySelect.append(`<option value="">Select Amenity</option>`);
                amenities.forEach(amenity => {
                    amenitySelect.append(`<option value="${amenity.amenity_id}">${amenity.amenity_name}</option>`);
                });
            }
        });
    }

    // Initialize selected amenities array
    let selectedAmenities = [];

    // Handle adding an amenity
    function handleAmenitySelection(selectId, containerId) {
        $(selectId).change(function () {
            const selectedAmenity = $(`${selectId} option:selected`);
            const amenityId = selectedAmenity.val();
            const amenityName = selectedAmenity.text();

            if (amenityId && !selectedAmenities.includes(parseInt(amenityId))) {
                selectedAmenities.push(parseInt(amenityId));
                $(containerId).append(`
                <span class="badge bg-label-warning amenity-badge" data-id="${amenityId}">
                    ${amenityName} <span class="remove-amenity text-dark" style="cursor: pointer;"> <i class="bx bx-x"></i> </span>
                </span>
            `);
            }

            $(selectId).val('');
            $(selectId).removeClass('is-invalid');
            $('#amenities_error').text('');
        });
    }

    // Handle removing an amenity
    function handleAmenityRemoval(containerId) {
        $(document).on('click', `${containerId} .remove-amenity`, function () {
            const amenityBadge = $(this).closest('.amenity-badge');
            const amenityId = amenityBadge.data('id');

            selectedAmenities = selectedAmenities.filter(id => id !== amenityId);
            amenityBadge.remove();
        });
    }

    // Fetch and display venues with pagination
    function fetchVenues(page = 1) {
        $.ajax({
            url: `${apiBaseUrl}/read_venues.php`,
            method: 'GET',
            success: function (data) {
                // console.log(data)
                const response = JSON.parse(data);
                const venuesList = $('#venues-list tbody');
                venuesList.empty();
                if (response.status == 200) {
                    const venues = response.venues
                    // console.log(venues)
                    const startIndex = (page - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    const paginatedVenues = venues.slice(startIndex, endIndex);

                    paginatedVenues.forEach(venue => {
                        const row = `
                        <tr>
                            <td>${venue.id}</td>
                            <td>${venue.venue_name}</td>
                            <td>${venue.venue_type}</td>
                            <td>${venue.capacity}</td>
                            <td>${venue.amenities}</td>
                            <td>${venue.location}</td>
                            <td>${venue.status}</td>
                            <td>
                                <button class="btn btn-default edit-venue-btn" data-bs-toggle="offcanvas" data-bs-target="#editVenueOffcanvas" aria-controls="editVenueOffcanvas" data-id="${venue.id}"><span class="text-info"><i class="bx  bx-pencil"></i></span></button>
                                <button class="btn btn-default delete-venue-btn" data-id="${venue.id}"><span class="text-danger"><i class="bx bx-trash"></i></span></button>
                            </td>
                        </tr>
                    `;
                        venuesList.append(row);
                    });

                    // Update pagination
                    updatePagination(venues.length, page);
                } else {
                    // console.log(response.message);
                    venuesList.html(`<p>${response.message}</p>`);
                }
            }
        });
    }

    // Update pagination links
    function updatePagination(totalItems, currentPage) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const pagination = $('#pagination');
        pagination.empty();

        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            pagination.append(`<li class="page-item ${activeClass}"><a class="page-link" href="#">${i}</a></li>`);
        }
    }

    // Handle pagination click
    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        currentPage = parseInt($(this).text());
        fetchVenues(currentPage);
    });

    // Open create venue modal
    $('#addVenueModalBtn').on('click', function () {
        fetchVenueTypes('#venue_type');
        fetchStatuses('#status');
        fetchAmenities('#amenity');

        handleAmenitySelection('#amenity', '#amenities_container');
        handleAmenityRemoval('#amenities_container');
    });

    // Close create venue modal
    $('.close').on('click', function () {
        $('#create-venue-modal').hide();
    });

    // Create venue
    $('#create-venue-form').on('submit', function (e) {
        // validate amenities
        if (selectedAmenities.length === 0) {
            e.preventDefault();
            $('#amenity').addClass('is-invalid');
            $('#amenities_error').text('Please select at least one amenity.');
            return false;
        }
        e.preventDefault();
        const venueData = {
            venue_name: $('#venue_name').val(),
            venue_type: $('#venue_type').val(),
            capacity: $('#capacity').val(),
            amenities: selectedAmenities,
            location: $('#location').val(),
            status: $('#status').val()
        };

        $.ajax({
            url: `${apiBaseUrl}/create_venue.php`,
            method: 'POST',
            data: JSON.stringify(venueData),
            contentType: 'application/json',
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status == 201) {
                    $('#create-venue-form')[0].reset();
                    $('#addVenueModal').modal('hide');
                    selectedAmenities = [];
                    showAlert('Well done!', data.message, data.msg_type);
                    fetchVenues(currentPage);
                } else {
                    showToast('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // populate venue offcanvas
    $(document).on('click', '.edit-venue-btn', function () {
        const venueId = $(this).data('id');
        // Fetch venue data via AJAX or already have the data available
        $.ajax({
            url: `${apiBaseUrl}/get_venue.php`,
            method: 'GET',
            data: {
                venue_id: venueId
            },
            success: function (response) {
                const venue = JSON.parse(response).data;
                // console.log(venue)

                // Fetch venue types and statuses before setting values
                fetchVenueTypes('#edit_venue_type', function () {
                    // After fetching venue types, set the selected value for the venue type
                    $('#edit_venue_type').val(venue.venue_type_id).change();
                });

                fetchStatuses('#edit_status', function () {
                    // After fetching statuses, set the selected value for the status
                    $('#edit_status').val(venue.status_id).change();
                });

                // Populate the offcanvas fields with the venue data
                $('#edit_venue_id').val(venue.venue_id);
                $('#edit_venue_name').val(venue.venue_name);
                $('#edit_capacity').val(venue.capacity);
                $('#edit_location').val(venue.location);

                // Populate amenities container with existing amenities
                $('#edit_amenities_container').html('');
                venue.amenities.forEach(function (amenity) {
                    $('#edit_amenities_container').append(`
                    <span class="badge bg-label-info amenity-badge" data-id="${amenity.amenity_id}">
                        ${amenity.amenity_name}
                        <span class="remove-amenity text-dark" style="cursor: pointer;">
                            <i class="bx bx-x"></i>
                        </span>
                    </span>
                `);
                    selectedAmenities.push(amenity.amenity_id); // Store the selected amenities
                });

                fetchAmenities('#edit_amenity');

                // Re-initialize amenities handling for the edit form
                handleAmenitySelection('#edit_amenity', '#edit_amenities_container');
                handleAmenityRemoval('#edit_amenities_container');
            },
            error: function () {
                alert('Failed to fetch venue data');
            }
        });
    });

    // update venue
    $('#edit-venue-form').on('submit', function (e) {
        e.preventDefault();

        // Get the selected amenities from the displayed badges, not from the dropdown
        let selectedAmenityIds = [];
        $('#edit_amenities_container .amenity-badge').each(function () {
            selectedAmenityIds.push($(this).data('id')); // Push amenity ID from badge
        });

        if (selectedAmenityIds.length === 0) {
            // If no amenities are selected, show error
            $('#edit_amenity').addClass('is-invalid');
            $('#edit_amenities_error').text('Please select at least one amenity.');
            return false;
        }

        // Prepare the venue data
        const venueData = {
            venue_id: $('#edit_venue_id').val(),
            venue_name: $('#edit_venue_name').val(),
            venue_type: $('#edit_venue_type').val(),
            capacity: $('#edit_capacity').val(),
            location: $('#edit_location').val(),
            status: $('#edit_status').val(),
            amenities: selectedAmenityIds // Use only selected amenities' IDs
        };

        $.ajax({
            url: `${apiBaseUrl}/update_venue.php`, // Backend endpoint for editing venue
            method: 'POST',
            data: JSON.stringify(venueData),
            contentType: 'application/json',
            success: function (response) {
                const data = JSON.parse(response);
                // console.log(data)
                if (data.status == 200) {
                    $('#edit-venue-form')[0].reset();
                    $('#editVenueOffcanvas').offcanvas('hide');

                    selectedAmenities = []; // Clear selected amenities
                    showAlert('Success', data.message, data.msg_type);
                    fetchVenues(currentPage); // Refresh the venue list
                } else {
                    showToast('Error', data.message, data.msg_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while processing your request.', 'error');
            }
        });
    });


    // Delete venue
    $(document).on('click', '.delete-venue-btn', function () {
        const venueId = $(this).data('id');        
        const confirmation = confirm("Are you sure you want to delete this venue?");

        if (confirmation) {
            // If confirmed, send the delete request via AJAX
            $.ajax({
                url: `${apiBaseUrl}/delete_venue.php`,
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify({
                    venue_id: venueId
                }),
                success: function (response) {
                    // console.log(response)
                    const data = JSON.parse(response);
                    if (data.status === 200) {
                        // On success, remove the venue from the DOM
                        // $(`#venue_${venueId}`).remove(); // Remove the venue row from the list
                        fetchVenues(currentPage); // Fetch the updated list of venues
                        showAlert('Success', data.message, 'success');
                    } else {
                        showAlert('Error', data.message, 'error');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    showAlert('Error', 'An error occurred while deleting the venue.', 'error');
                }
            });
        }
    });

    // Fetch venues on page load
    fetchVenues();
});

// Function to handle venue deletion
function deleteVenue(venueId) {
    // Ask for confirmation before deleting
    const confirmation = confirm("Are you sure you want to delete this venue?");

    if (confirmation) {
        // If confirmed, send the delete request via AJAX
        $.ajax({
            url: `${apiBaseUrl}/delete_venue.php`,
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({
                venue_id: venueId
            }),
            success: function (response) {
                console.log(response)
                const data = JSON.parse(response);
                if (data.status === 200) {
                    // On success, remove the venue from the DOM
                    // $(`#venue_${venueId}`).remove(); // Remove the venue row from the list
                    fetchVenues(currentPage); // Fetch the updated list of venues
                    showAlert('Success', data.message, 'success');
                } else {
                    showAlert('Error', data.message, 'error');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                showAlert('Error', 'An error occurred while deleting the venue.', 'error');
            }
        });
    }
}

// Show alert div
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

// Display toast notification
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