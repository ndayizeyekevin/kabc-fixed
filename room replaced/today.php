<?php 
include '../inc/conn.php';

// Initialize date range variables (default to today)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Convert dates to datetime format for timestamp comparison
$start_datetime = $start_date . ' 00:00:00';
$end_datetime = $end_date . ' 23:59:59';

// Pagination variables
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Get total count for pagination
$count_sql = $db->prepare("SELECT COUNT(*) as total FROM tbl_acc_booking 
                          WHERE (booking_status_id = 2 OR booking_status_id = 1 OR booking_status_id = 6) 
                          AND created_at BETWEEN :start_datetime AND :end_datetime");
$count_sql->bindParam(':start_datetime', $start_datetime);
$count_sql->bindParam(':end_datetime', $end_datetime);
$count_sql->execute();
$total_rows = $count_sql->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_rows / $per_page);

// Main query with pagination
$sql = $db->prepare("SELECT * FROM tbl_acc_booking 
                    WHERE (booking_status_id = 2 OR booking_status_id = 1 OR booking_status_id = 6) 
                    AND created_at BETWEEN :start_datetime AND :end_datetime
                    ORDER BY created_at DESC 
                    LIMIT :offset, :per_page");
$sql->bindParam(':start_datetime', $start_datetime);
$sql->bindParam(':end_datetime', $end_datetime);
$sql->bindParam(':offset', $offset, PDO::PARAM_INT);
$sql->bindParam(':per_page', $per_page, PDO::PARAM_INT);
$sql->execute();
?>

<div class="colr-area">
    <div class="container">
        <h5 class="card-header">BOOKING LIST 
            <a href="print.php?page=booking&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
               class="btn btn-info">Print</a>
        </h5>
        
        <!-- Date Range Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-4">
                        <input type='hidden' name="resto" value='Booking_list'>
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?php echo htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="?" class="btn btn-secondary ms-2">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th colspan="2">Room (Account)</th>
                        <th>Guest Name</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Rate</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Company</th>
                        <th>Residence</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php 
                    $no = $offset + 1;
                    while($row = $sql->fetch()):
                        $status_class = '';
                        $status_text = '';
                        
                        switch($row['booking_status_id']) {
                            case 1:
                                $status_class = 'warning';
                                $status_text = 'Pending';
                                break;
                            case 2:
                                $status_class = 'success';
                                $status_text = 'Confirmed';
                                break;
                            case 6:
                                $status_class = 'primary';
                                $status_text = 'Checked In';
                                break;
                            default:
                                $status_class = 'secondary';
                                $status_text = 'Unknown';
                        }
                        
                        // Format the created_at timestamp for display
                        $created_at = date('Y-m-d H:i:s', strtotime($row['created_at']));
                    ?>
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $no++; ?></strong></td>
                        <td><?php echo getRoomName(getBookedRoom($row['id'])) ?></td>
                        <td><?php echo getRoomClassType(getRoomClass(getBookedRoom($row['id']))) ?></td>
                        <td><strong><?php echo getGuestNames($row['guest_id']) ?></strong></td>
                        <td><?php echo $row['checkin_date'] ?></td>
                        <td><?php echo $row['checkout_date'] ?></td>
                        <td><?php echo number_format($row['room_price']) ?></td>
                        <td><?php echo number_format($row['duration']) ?></td>
                        <td><span class="badge bg-label-<?php echo $status_class ?>"><?php echo $status_text ?></span></td>
                        <td><?php echo $created_at ?></td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <?php endwhile; ?>
                    
                    <?php if($sql->rowCount() == 0): ?>
                    <tr>
                        <td colspan="12" class="text-center">No bookings found for the selected date range.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?resto=Booking_list&page=<?php echo $page-1 ?>&start_date=<?php echo $start_date ?>&end_date=<?php echo $end_date ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?resto=Booking_list&page=<?php echo $i ?>&start_date=<?php echo $start_date ?>&end_date=<?php echo $end_date ?>"><?php echo $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?resto=Booking_list&page=<?php echo $page+1 ?>&start_date=<?php echo $start_date ?>&end_date=<?php echo $end_date ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>