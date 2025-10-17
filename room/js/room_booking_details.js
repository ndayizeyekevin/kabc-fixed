$(document).ready(function () {
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('booking_id');

    const bookingApi = '../api/bookings/handle_room_bookings.php';

    // console.log(bookingId)

    if (bookingId) {
        // Fetch booking details
        fetch(bookingApi + `?bookingId=${bookingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    const booking = data.data[0];
                    // console.log(booking);

                    let statusColor = '';
                    switch (booking.booking_status_name) {
                        case 'Pending':
                            statusColor = 'btn-warning';
                            break;
                        case 'Confirmed':
                            statusColor = 'btn-success';
                            break;
                        case 'Cancelled':
                            statusColor = 'btn-danger';
                            break;
                        case 'No-Show':
                            statusColor = 'btn-secondary';
                            break;
                        default:
                            statusColor = 'btn-secondary';
                            break;
                    }
                    // add statusColor as a class to bookingStatus button
                    $('#bookingStatus').addClass(statusColor);


                    $('#bookingStatus').text(booking.booking_status_name);

                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error fetching booking:', error));
    } else {
        console.log('No bookingId provided in the URL.');
    }
});