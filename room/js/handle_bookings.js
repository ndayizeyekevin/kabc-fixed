// booking process 
$(document).ready(function () {
    const handleRooms = '../api/rooms/get_available_rooms_per_booking.php';

    $('#searchRoom').on('click', function () {
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        const checkinDate = dateRange[0];
        const checkoutDate = dateRange[1];
        const numAdults = $('#adults').val();
        const numChildren = $('#children').val();

        $.ajax({
            url: handleRooms,
            method: 'POST',
            data: {
                checkin_date: checkinDate,
                checkout_date: checkoutDate,
                num_adults: numAdults,
                num_children: numChildren
            },
            success: function (response) {
                console.log(response)
                if (response.status == 200) {
                    const rooms = response.data;
                    const resultsTable = $('#room-search-result-tb tbody');
                    resultsTable.empty();

                    rooms.forEach(room => {
                        let basePrice = parseInt(room.base_price).toLocaleString();
                        const row = `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-house-door-fill me-2"></i>
                                        ${room.room_class}
                                    </div>
                                    <div class="text-muted">${room.num_available_rooms} room(s)</div>
                                </td>
                                <td>${checkinDate} - ${checkoutDate}</td>
                                <td>${room.max_capacity}</td>
                                <td>${basePrice} RWF</td>
                                <td>${room.features}</td>
                                <td>${room.bed_types}</td>
                                <td>${room.bed_details}</td>                                
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary add-room" data-room-id="${room.id}">Add</button>
                                </td>
                            </tr>
                        `;
                        resultsTable.append(row);
                    });
                } else {
                    console.log(response.message);
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
    });

    let currentTransaction = {
        rooms: 0,
        adults: 0,
        children: 0,
        amount: 0
    };

    // Function to update current transaction tab
    const updateCurrentTransactionTab = () => {
        $('#curr_rooms').text(`${currentTransaction.rooms} room(s)`);
        $('#curr_adults').text(`${currentTransaction.adults} Adult(s)`);
        $('#curr_childrens').text(`${currentTransaction.children} Child(ren)`);
        $('#curr_amount').text(`${currentTransaction.amount} RWF`);
    };

    // Function to show toast
    const showToast = (message) => {
        const toast = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        $('#toast-container').append(toast);
        $('.toast').toast('show');
    };

    // Handle Add Room button click
    $('table').on('click', '.add-room', function () {
        const roomId = $(this).data('room-id');
        const quantity = $(`.room-quantity[data-room-id="${roomId}"]`).val();
        const roomPrice = $(`.add-room[data-room-id="${roomId}"]`).closest('tr').find('td:nth-child(4)').text().split(' ')[0].replace(/,/g, '');

        currentTransaction.rooms += parseInt(quantity);
        currentTransaction.amount += (parseInt(roomPrice) * parseInt(quantity));

        // Assuming num_adults and num_children are available per room; update as required
        currentTransaction.adults += parseInt($('#adults').val());
        currentTransaction.children += parseInt($('#children').val());

        updateCurrentTransactionTab();
        showToast('Room added to the booking.');
    });

});