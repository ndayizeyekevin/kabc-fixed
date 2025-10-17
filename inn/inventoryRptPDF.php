<?php 
ob_start();
require_once ("../inc/config.php");

   $from = $_SESSION['from'];
   $to = $_SESSION['to'];
   $item = $_SESSION['item'];

?>
<center>
    <h4><u><b>Stock Report</b></u></h4>
</center>
<table width="100%" height="100%" border="1" cellpadding="5" cellspacing="0" style="font-size:8pt;color:#000000">
	<tr>
		<td colspan="2">
<table width="100%" cellspacing="0" border="1">
     <thead>
        <tr>
            <th> Date</th>
            <th> Type </th>
            <th> Opening Quantity </th>
            <th> Received Quantity </th>
            <th> Used Quantity </th>
            <th> Closing Stock </th>
        </tr>
    </thead>
    <tbody>

        <?php
        if($item != 'all'){
        $result2 = $db->prepare("SELECT * FROM tbl_progress INNER JOIN tbl_items ON tbl_items.item_id=tbl_progress.item WHERE date BETWEEN '$from' AND '$to' AND item='".$item."' ");
        $result2->execute();
        }else{
        $result2 = $db->prepare("SELECT * FROM tbl_progress INNER JOIN tbl_items ON tbl_items.item_id=tbl_progress.item WHERE date BETWEEN '$from' AND '$to' ");
        $result2->execute();    
        }
        for($i=1;$rows = $result2->fetch(); $i++){
            ?>
            <tr class="record">
                <td><?php echo $rows['date']; ?></td>
                <td><?php echo $rows['item_name']; ?></td>
                <td>
                    <?php 
                    if($rows['last_qty'] == ''){
                        echo "-";
                    }
                    else{
                        echo $rows['last_qty']; 
                    }
                    ?>
                </td>
                <td>
                    <?php 
                    if($rows['in_qty'] == ''){
                        echo "-";
                    }
                    else{
                        echo $rows['in_qty']; 
                    }
                    ?>
                </td>
                
                <td>
                    <?php 
                    if($rows['out_qty'] == ''){
                        echo "-";
                    }
                    else{
                        echo $rows['out_qty']; 
                    }
                    ?>
                </td>
                <td>
                    <?php 
                    echo $rows['end_qty'];
                    ?>
                    
                </td>
            </tr>
            <?php
        }
        ?>

    </tbody>
</table>
</td>
</tr>
</table>
<?php
include("../mpdf60/mpdf.php");
$mpdf=new mPDF('P','A4'); 
$mpdf = new mPDF();
$stylesheet = file_get_contents('../mpdf60/pdf.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->SetDisplayMode('fullpage');

$stylesheet = file_get_contents('../mpdf60/mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet,1);
$report_content=ob_get_contents();
ob_clean();

$mpdf->WriteHTML($report_content,2);
$mpdf->Output('general_report.pdf','I');
exit;
?>