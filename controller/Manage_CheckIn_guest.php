<?php

$preserve_get = 'no';

$selected_date = $_GET['date'] ?? date('Y-m-d');
// for cancel Subject
if (isset($_GET['b'])) {
    try {
        $b = 4;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE `tbl_reservation` SET `status` = '".$b."' WHERE `reservation_id` = '".$_GET['b']."'";
        $didq = $db->prepare($sql);
        $didq->execute();
        $msge = "Reservation Cancelled successfully";
        echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=mngeResrv">';
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

} elseif (isset($_GET['d'])) {
    try {
        $reservation_ID = $_GET['d'];
        $todaysdate = date("Y-m-d H:i:s");

        $stmt_room = $db->prepare("SELECT * FROM `tbl_reservation` WHERE reservation_id = '".$reservation_ID."' ");
        $stmt_room->execute();
        $row_room = $stmt_room->fetch();
        $rrr = $row_room['roomID'];
        $ggg = $row_room['guest_id'];

        $stmt_r = $db->prepare("SELECT * FROM `tbl_rooms` WHERE room_id = '".$rrr."' ");
        $stmt_r->execute();
        $row_r = $stmt_r->fetch();
        $price = $row_r['price'];
        $price_fbu = $row_r['price_fbu'];

        $stmt_guest = $db->prepare("SELECT * FROM `guest` WHERE guest_id = '".$ggg."' ");
        $stmt_guest->execute();
        $row_guest = $stmt_guest->fetch();
        $country = $row_guest['country'];

        if ($country != 15) {
            $f_price = $price;
        } else {
            $f_price = $price_fbu;
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

}

?>

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                         <div class="basic-tb-hd">
                          <h2><small><strong><i class="fa fa-users"></i> Manage Our Guest</strong></small></h2>
                         </div>

                         <hr>
                        <br>


          <div class="d-flex gap-4">
    <form method="POST" class="form-inline">
        <label for="date_from">Start date:</label>
        <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>">
        <label for="date_to">End date:</label>
        <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>">
        <button type="submit" name="process" id="process" class="btn btn-info btn-sm">Search</button>
    </form>

                       
</div>

                     <button id="printH"> Print </button>


              <div class="text-nowrap table-responsive">
                          <div id = "content">  
  <?php include '../holder/printHeader.php'?>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Table No</th>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totprice = 0;
$date_from = $_POST['date_from'] ?? null;
$date_to = $_POST['date_to'] ?? null;
if (isset($_POST['date_from']) & isset($_POST['date_to'])) {
    $q = "DATE(tbl_cmd_qty.created_at) >= '$date_from'
          AND DATE(tbl_cmd_qty.created_at) <= '$date_to'";
} else {
    $q = "DATE(tbl_cmd_qty.created_at) = '$selected_date'";
}

$sql_rooms = $db->prepare("SELECT *,tbl_cmd_qty.created_at FROM `tbl_cmd_qty`
                                    INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_status != '12'
                                    AND $q
                                    GROUP BY cmd_code
                                    ORDER BY cmd_table_id ASC
                                    ");
$sql_rooms->execute();
$i = 0;
while ($fetrooms = $sql_rooms->fetch()) {
    $i++;
    $totprice = $totprice + $fetrooms['menu_price'];

    $stmts = $db->prepare("SELECT * FROM tbl_status WHERE id = '".$fetrooms['cmd_status']."'");
    $stmts->execute();
    $fsts = $stmts->fetch();
    ?>
                                 <tr class="gradeU">
         							<td><?php echo $i; ?></td>
                    				<td><?php echo $fetrooms['table_no']; ?> |  <?php  $d = $fetrooms['Serv_id']; ?>
                    			<?php
        $sql_roomss = $db->prepare("SELECT * FROM tbl_users WHERE user_id ='$d'");
    $sql_roomss->execute();
    while ($fetroomss = $sql_roomss->fetch()) {
        echo $fetroomss['f_name'];
    }
    ?>
                    				</td>
         							<td>
                    <?php echo $fetrooms['created_at']; ?>
         							</td>
                                    <td>
                                    	<?php
        $sql_roomsss = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode ='".$fetrooms['cmd_code']."' group by  OrderCode  ");
    $sql_roomsss->execute();
    while ($fetroomsss = $sql_roomsss->fetch()) {
        echo $fetroomsss['id'];
    }
    ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Calculate total amount for this cmd_code
                                        $cmd_code = $fetrooms['cmd_code'];
                                        $total_amount = 0;
                                        $sql_total = $db->prepare("SELECT SUM(menu_price * cmd_qty) as total FROM tbl_cmd_qty INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item WHERE cmd_code = ?");
                                        $sql_total->execute([$cmd_code]);
                                        $row_total = $sql_total->fetch();
                                        $total_amount = $row_total['total'] ?? 0;
                                        echo number_format($total_amount, 2);
                                        ?>
                                    </td>
         							<td>
                                        <?php if ($fetrooms['cmd_status']) { ?>
        							    <a href="?resto=gstDet&res=<?php echo $fetrooms['cmd_table_id'];?>&c=<?php echo $fetrooms['cmd_code'];?>" onclick="if(!confirm('Do you really want to View Details?'))return false;else return true;" class="label label-primary">Details</a>
        							    <!-- <a href="Cancel_Invoice?resrv=<?php echo $fetrooms['cmd_table_id'];?>&c=<?php echo $fetrooms['cmd_code'];?>" onclick="if(!confirm('Do you really want to Cancel This Order?'))return false;else return true;" class="label label-danger">Cancel</a> -->
                                     <?php } ?>

                                     <a hidden href="?resto=transfer&res=<?php echo $fetrooms['Serv_id'];?>&c=<?php echo $fetrooms['cmd_code'];?>"  class="label label-primary">Transfer</a>
         							</td>
         						</tr>
					            <?php

}
?>
    						</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->

    <div id="update<?php echo $fetrooms['reservation_id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header text-center">
            <h2 class="modal-title"><i class="fa fa-pencil"></i> <?php echo $cpny_ID;?> Profile</h2>
          </div>
          <!-- END Modal Header -->

          <!-- Modal Body -->
          <div class="modal-body">

                <form id="settings_form" method="POST" enctype="multipart/form-data" class="form-horizontal form-bordered">

                  <fieldset style="margin-top: 10px;">

                     <div class="form-group">
                      <label class="col-md-4 control-label" for="">Firstname</label>
                      <div class="col-md-8">
                        <input type="text" id="cpname" name="cpname" class="form-control" value="<?php echo $cmp_full;?>" placeholder="Enter Company Name..">
                      </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Company Email</label>
                        <div class="col-md-8">
                            <input type="Email" id="cpemail" name="cpemail" class="form-control" value="<?php echo $cpny_email;?>" placeholder="Enter Email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Phone</label>
                        <div class="col-md-8">
                            <input type="text" id="cphone" name="cphone" class="form-control" value="<?php echo $cpny_phone;?>" placeholder="Company Phone">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="">Address</label>
                        <div class="col-md-8">
                            <input type="text" id="cpny_address" name="cpny_address" class="form-control" value="<?php echo $cpny_address;?>" placeholder="Company Phone">
                        </div>
                    </div>

                  </fieldset>
                    <div class="form-group form-actions" style="margin-top: 10px;">
                        <div class="col-xs-12 text-right">
                            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" name="btn-profile" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>
          <!-- END Modal Body -->
        </div>
      </div>
    </div>

    <script>
    // Prevent going back
    history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
        history.pushState(null, "", window.location.href);
    };
</script>

<?php include '../holder/printFooter.php'?>
</div>
</div>
<script> function printInvoice() { var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#printH").click(function(){
        $("#headerprint").show();
var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; 
 });
$("#headerprint").hide();
			//      $("#date_to").change(function () {
			//          var from = $("#date_from").val();
			//          var to = $("#date_to").val();
			// $(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
			//          $.post('load_sales_report.php',{from:from,to:to} , function (data) {
			//           $("#display_res").html(data);
			//              $('#loader').slideUp(910, function () {
			//                  $(this).remove();
			//              });
			//              location.reload();
			//          });
			//      });

    });
</script>