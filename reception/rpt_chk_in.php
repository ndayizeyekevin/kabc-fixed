<?php
header('Content-type: text/html; charset=utf-8');
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=chk_in_report.xls");
?>

<?php
  require_once ("../inc/config.php");
  $date_from = $_REQUEST['date_from'];
  $date_to = $_REQUEST['date_to'];
?>
 <table id="data-table-basic" class="table table-striped">
    <thead>
        <tr>
    	   <th colspan="6" style="background-color:#00a65a;color:white;font-weight:bold;padding:6px;">
    	    <h2>CheckIn Report</h2>
             Date From: <?php echo $date_from; ?><br>
             Date To: <?php echo $date_to; ?>
             </th>
    	    </tr>
        <tr>
            <th>Names</th>
            <th>Nationality</th>
            <th>Passport Number</th>
            <th>Check_in</th>
            <th>Check_out</th>
            <th>Room Number</th>
        </tr>
    </thead>
    	<tbody>
            <?php

                $sql_rooms = $db->prepare("SELECT * FROM tbl_reservation
                INNER JOIN tbl_rooms ON tbl_reservation.roomID=tbl_rooms.room_id
                INNER JOIN guest ON tbl_reservation.guest_id=guest.guest_id
                INNER JOIN tbl_status ON tbl_reservation.status=tbl_status.id
                WHERE status = 11 AND arrival BETWEEN '".$_REQUEST['date_from']."' AND '".$_REQUEST['date_to']."' 
                GROUP BY tbl_reservation.reservation_id");
                $sql_rooms->execute();
                $i = 0;
                while($fetrooms = $sql_rooms->fetch()) {
                    $country = $fetrooms['country'];
            ?>
         <tr class="gradeU">
			<td>
				<?php echo $fetrooms['firstname'] ." ". $fetrooms['lastname']; ?>
			</td>
			<td>
			     <?php
			     $c = $db->prepare("SELECT * FROM tbl_country WHERE cntr_id = '".$country."' ");
			     $c->execute();
			     $b = $c->fetch();
			     echo $b['cntr_name']; ?>
			</td>
			<td>
			     <?php echo $fetrooms['nid_passport']; ?>
			</td>
			<td>
			     <?php echo $fetrooms['arrival']; ?>
			</td>
			<td>
			     <?php echo $fetrooms['departure']; ?>
			</td>
			<td>
			     <?php echo $fetrooms['room_no']; ?>
			</td>
		</tr>
        <?php   
                }  
		?>
</tbody>
  </table>