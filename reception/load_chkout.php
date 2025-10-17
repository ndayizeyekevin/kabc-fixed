<?php
require_once ("../inc/config.php");
require_once ("../holder/template_scripts.php");
require_once ("../holder/template_styles.php");

$adlt = $row['Adults'];
$chld = $row['Children'];

$date_from = $_REQUEST['date_from'];
$date_to = $_REQUEST['date_to'];
?>

<table id="data-table-basic" class="table table-striped">
    <a href="rpt_chk_out.php?&date_from=<?php echo $date_from;?>&date_to=<?php echo $date_to;?>" name="export_excel1" class="btn btn-success pull-right" title="click to download" style="margin:5px;"><i class="fa fa-file-text"></i> Export Excel</a>
    <thead>
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
                WHERE status = 12 AND arrival BETWEEN '".$date_from."' AND '".$date_to."' 
                GROUP BY tbl_reservation.reservation_id");
                $sql_rooms->execute();
                $rowcount = $sql_rooms->rowCount();
                if($rowcount > 0){
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
                }
                else{
                    $msge = "No Checkedout Record Found!";
                }
		?>
</tbody>
  </table>
  <?php if($msg){?>
                  <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
                  </div>
                <?php } 
                 else if($msge){?>
                     
                 <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
                  </div>
                <?php } ?>