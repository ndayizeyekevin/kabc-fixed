<?php include '../inc/conn.php';
error_reporting(E_ALL);
ini_set("display_errors", 1);



// PERFORMING DELETE
if(isset($_GET['delete']) && !empty($_GET['delete'])){
    $sql = mysqli_query($conn, "DELETE FROM  `tbl_progress` WHERE tbl_progress.item ='".$_GET['item_id']."' AND tbl_progress.prog_id='".$_GET['delete']."'");
    if($sql){
        echo"<script>alert('Item follow deleted Successful')</script>";
    }else{
        echo"<script>alert('Item follow not deleted')</script>";
    }
}

?>

<div class="container my-5">
    <div class="card shadow rounded-4">
        <div class="card-header bg-info text-white text-center fs-5">
            Item Report
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="bg-white text-dark">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Opening STK</th>
                            <th>In QTY</th>
                            <th>Out QTY</th>
                            <th>Closing STK</th>
                            <!-- <th>Action</th>  New column  -->
                            <!--<th>Cumulative Stock</th> New column -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = mysqli_query($conn, "SELECT * FROM tbl_progress, tbl_items WHERE tbl_items.item_id = '" . $_GET['item_id'] . "' AND tbl_progress.item = '" . $_GET['item_id'] . "' ORDER BY tbl_progress.date DESC, tbl_progress.prog_id");
                        $total_in = 0;
                        $Out_qty = 0;

                        if(mysqli_num_rows($sql) == 0){
                            echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
                            return;
                        }

                        while ($row = mysqli_fetch_assoc($sql)) {
                            $in_qty = (float)$row['in_qty']?? 0;
                            $out_qty = (float)$row['out_qty']?? 0;
                            $end_qty = (float)$row['end_qty']?? 0;
                            // $totaIn = (float)$row[''];

                            // Update cumulative stock
                            $total_in += $in_qty;
                            $Out_qty += $out_qty;
                        ?>
                            <tr>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo $row['last_qty']; ?></td>
                                <td><?php echo $in_qty; ?></td>
                                <td><?php echo number_format($out_qty,2); ?></td>
                                <td><?php echo number_format($end_qty, 2); ?></td>
                                <!-- <td class="text-outline-danger"><a href="?resto=cumurative&item_id=<?php echo $_GET['item_id']; ?>&delete=<?php echo $row['prog_id']; ?>">Delete</a></td> -->
                                <!--<td class="fw-semibold text-primary">-->
                                    <?php // echo number_format($cumulative_stock, 2); ?>
                                <!--</td>-->
                            </tr>
                        <?php } ?>
                        <tr class="table-secondary fw-bold">
                            <td colspan="3" class="text-start">TOTAL</td>
                            <td><?php echo number_format($total_in, 2); ?> </td>
                            <td><?php echo number_format($Out_qty, 2); ?> </td>
                            <td><?php echo number_format($end_qty, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


