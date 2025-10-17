const commonFunctions = (() => {
    const globalApi = '../api/bookings/handle_venue_reservations.php';

    const fetchReservations = () => {
        $.ajax({
            url: globalApi,
            method: 'GET',
            success: function (data) {
               // console.log(data.data);
                const reservationsTable = $('#reservationsTable').DataTable();
                reservationsTable.clear();
                const obj = data;
                if (obj.status == 200) {
                    if (obj.data.length > 0) {
                        obj.data.forEach(reservation => {
                            reservationsTable.row.add([
                                reservation.id,
                                reservation.venue_name,
                                reservation.customer_name,
                                reservation.reservation_date,
                                reservation.start_time,
                                reservation.end_time,
                                reservation.status,
                                `<a href="?resto=venue_checkout&&booking_id=${reservation.id}" class="btn details-btn text-primary" title="Details" ><i class="bx bx-detail"></i></a>
                                
                                <button class="btn edit-btn text-info" title="Edit" data-id="${reservation.id}" data-reservation='${JSON.stringify(reservation)}'> <i class="bx bx-edit"></i></button>
                                <button class="btn delete-btn text-danger" title="Delete" data-id="${reservation.id}"><i class="bx bx-trash"></i></button>`
                            ]).draw(false);
                        });
                    } else {
                        reservationsTable.draw();
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

    return {
        fetchReservations
    };
})();
