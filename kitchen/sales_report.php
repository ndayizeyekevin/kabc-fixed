<?php
if (!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])) {
    $from = date('Y-m-d');
    $to = date("Y-m-d");
} else {
    $from = $_SESSION['date_from'];
    $to = $_SESSION['date_to'];
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                    <h2><small><strong><i class="fa fa-refresh"></i> Kitchen Report</strong></small></h2>
                </div>
                <hr>
                <form action="" method="POST">
                    <div class="row">
                        <label class="col-md-2 control-label" for=""><strong>Date From </strong><span class="text-danger">*</span></label>
                        <div class="col-md-3">
                            <input type="date" id="date_from" name="date_from" class="form-control">
                        </div>
                        <label class="col-md-2 control-label" for=""><strong>Date To </strong><span class="text-danger">*</span></label>
                        <div class="col-md-3">
                            <input type="date" id="date_to" name="date_to" class="form-control">
                        </div>
                    </div>
                </form>
                <br>
                <br>
                <!-- HTML Part -->
<form method="POST" action="generate_pdf.php" target="_blank">
</form>
<div class="row">
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-success" onclick="printData()">
            <i class="fa fa-print"></i> Print
        </button>
    </div>
</div>
</form>
                <br>
                <br>
                <div class="table-responsive">
                    <div class="print-section">
                    <table id="data-table-basic" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ITEM NO</th>
                                <th>ITEM NAME</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>PRICE</th>
                                <th>QTY</th>
                                <!--<th>AMOUNT</th>-->
                                <!--<th>TAX RATE</th>-->
                                <!--<th>TAX</th>-->
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            if ($_GET['resto'] == 'report') {
                                $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE DATE(tbl_cmd_qty.created_at) BETWEEN '" . $from . "' AND '" . $to . "' AND cmd_status = '12' AND menu.cat_id != '2'
                                    GROUP BY cmd_item");
                                $sql->execute(array());
                            } elseif ($_GET['resto'] == 'reportb') {
                                $sql = $db->prepare("SELECT *,SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE DATE(tbl_cmd_qty.created_at) BETWEEN '" . $from . "' AND '" . $to . "' AND cmd_status = '12' AND menu.cat_id = '2'
                                    GROUP BY cmd_item");
                                $sql->execute(array());
                            }
                            if ($sql->rowCount()) {
                                while ($fetch = $sql->fetch()) {
                                    $i++;
                                    $OrderCode = $fetch['cmd_code'];

                                    $amount = $fetch['menu_price'] * $fetch['totqty'];
                                    // $tottax = $amount * (int)$fetch['tax'];
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $fetch['menu_name']; ?></td>
                                        <td><?php echo $fetch['menu_desc']; ?></td>
                                        <td><?php echo number_format($fetch['menu_price']); ?></td>
                                        <td><?php echo $fetch['totqty']; ?></td>
                                        <td><?php echo number_format($amount); ?></td>
                                        <!--<td>-->
                                            <?php 
                                            // echo $fetch['tax'];
                                            ?>
                                            <!--</td>-->
                                        <!--<td>-->
                                        <?php
                                        // echo number_format($tottax); 
                                        ?>
                                        <!--</td>-->
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 
    
    
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#date_to").change(function() {
                var from = $("#date_from").val();
                var to = $("#date_to").val();
                $(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');
                $.post('load_sales_report.php', {
                    from: from,
                    to: to
                }, function(data) {
                    $("#display_res").html(data);
                    $('#loader').slideUp(910, function() {
                        $(this).remove();
                    });
                    location.reload();
                });
            });
        });

        // Print function from baqueting_report.php
        function printData() {
            var divToPrint = document.querySelector('.print-section');
            var printedBy = '';
            <?php
            $printedBy = '';
            try {
                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                    $stmtPB = $db->prepare("SELECT u.f_name, u.l_name FROM tbl_users u INNER JOIN tbl_user_log l ON u.user_id = l.user_id WHERE l.user_id = ? LIMIT 1");
                    $stmtPB->execute([$_SESSION['user_id']]);
                    $rowPB = $stmtPB->fetch(PDO::FETCH_ASSOC);
                    if ($rowPB) {
                        $printedBy = trim(($rowPB['f_name'] ?? '') . ' ' . ($rowPB['l_name'] ?? ''));
                    }
                }
            } catch (Exception $e) {}
            if ($printedBy === '') {
                if (isset($_SESSION['user_fullname'])) { $printedBy = $_SESSION['user_fullname']; }
                elseif (isset($_SESSION['fullname'])) { $printedBy = $_SESSION['fullname']; }
                elseif (isset($_SESSION['name'])) { $printedBy = $_SESSION['name']; }
                elseif (isset($_SESSION['username'])) { $printedBy = $_SESSION['username']; }
                elseif (isset($_SESSION['f_name']) || isset($_SESSION['l_name'])) { $printedBy = trim(($_SESSION['f_name'] ?? '') . ' ' . ($_SESSION['l_name'] ?? '')); }
            }
            ?>
            var html = '<html><head><title>Kitchen Report</title>';
            html += '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">';
            html += '<style>@page { size: auto; margin: 5mm; } table, td, th { border: 1px solid black !important; font-size: 20px !important; } .btn, .search-box, form { display: none !important; } </style>';
            html += '</head><body>';
            var startDate = document.getElementById('date_from').value || '<?php echo isset($_SESSION['date_from']) ? $_SESSION['date_from'] : date('Y-m-d'); ?>';
            var endDate = document.getElementById('date_to').value || '<?php echo isset($_SESSION['date_to']) ? $_SESSION['date_to'] : date('Y-m-d'); ?>';
            html += '<center><img src="https://saintpaul.gope.rw/img/logo.png" style="max-width:180px; margin-bottom:10px;"></center>';
            html += '<div style="text-align:center;font-size:12px;margin-bottom:10px;">Printed on: ' + new Date().toLocaleString() + '</div>';
            html += '<div style="text-align:center;font-size:14px;margin-bottom:10px;">Date Range: ' + startDate + ' to ' + endDate + '</div>';
            html += divToPrint.innerHTML;
            html += '<div style="margin-top:40px; display:flex; justify-content:space-between; gap:20px;">';
            html += '<div style="width:32%; text-align:left;"><strong>Printed by:</strong><br><?php echo htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><div style="margin-top:50px; border-top:1px solid #000; height:0;"></div><small>Name & Signature</small></div>';
            html += '<div style="width:32%; text-align:right;"><strong>Approved by:</strong><br><br><div style="margin-top:50px; border-top:1px solid #000; height:0;"></div><small>Name & Signature</small></div>';
            html += '</div>';
            html += '</body></html>';
            var newWin = window.open('', '_blank');
            newWin.document.write(html);
            newWin.document.close();
            newWin.focus();
            setTimeout(function() { newWin.print(); newWin.close(); }, 200);
        }
    </script>
