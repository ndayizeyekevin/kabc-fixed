<?php

$preserve_get = 'no';

$selected_date = $_GET['date'] ?? date('Y-m-d');


$date_from = $_POST['date_from'] ?? null;
$date_to   = $_POST['date_to'] ?? null;

if (!empty($date_from) && !empty($date_to)) {
   $sql_rooms = $db->prepare("SELECT * FROM `tbl_vsdc_sales` WHERE salesDt BETWEEN '$date_from' AND '$date_to' ORDER BY salesDt DESC");
} else {
    $sql_rooms = $db->prepare("SELECT * FROM `tbl_vsdc_sales` WHERE salesDt='$selected_date' ORDER BY salesDt DESC");
}

?>

<!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                         <div class="basic-tb-hd">
                          <h2><small><strong><i class="fa fa-users"></i> Manage Receipts</strong></small></h2>
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

                   


              <div class="text-nowrap table-responsive">
                          <div id = "content">  

                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>CUSTOMER</th>
                                        <th>RECEIPT NO</th>
                                        <th>TIN</th>
                                        <th>DATE</th>
                                        <th>TOTAL</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php




$sql_rooms->execute();
$i = 0;
while ($fetrooms = $sql_rooms->fetch()) {
    $i++;
    
    ?>
                                 <tr class="gradeU">
        							<td><?php echo $i; ?></td>
                    				<td><?php echo $fetrooms['custNm']; ?></td>
                                            <td><a href="org?i=<?=$fetrooms['invcNo']?>"><?php echo $fetrooms['invcNo']; ?></a></td>
                                    <td><?php echo $fetrooms['custTin'] !='null' ? $fetrooms['custTin'] : 0; ?></td>
                                    <td><?php echo $fetrooms['salesDt']; ?></td>
                                    <td><?php echo $fetrooms['totTaxblAmt']; ?></td>
        							<td>
<?php
    if($fetrooms['rcptTyCd']!=='R'){
   ?> 
        							    <a href="refund?ref=<?=$fetrooms['invcNo']?>" class="label label-<?php echo $fetrooms['has_refund']==1 ? 'danger' : 'primary' ?>">(<?php echo $fetrooms['has_refund']==1 ? 'Refunded' : 'Refund' ?>)</a>
<?php
    }
?>
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



</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">

</script>

