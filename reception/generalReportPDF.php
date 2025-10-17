<?php
ob_start();
  require_once ("../inc/config.php");
  $date_from = $_REQUEST['date_from'];
?>
<center>
    <h4><u><b>General Report</b></u></h4>
</center>
<table width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Table No</th>
            <th>Menu Order</th>
            <th>Category</th>
            <th>Sub-Category</th>
            <th>U.Price</th>
            <th>Total</th>
            <th>Date-Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        $amount = 0;
        $sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
        INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
        WHERE DATE(created) = '".$date_from."'");
        $sql->execute(array());
        if($sql->rowCount()){
        while($fetch = $sql->fetch()){
            $i++;
        
        $amount += $fetch['cmd_qty']*$fetch['menu_price'];

        $OrderCode = $fetch['cmd_code'];
            
            
        $menu_id = $fetch['menu_id'];
        $cat_id= $fetch['cat_id'];
        $subcat_ID = $fetch['subcat_ID'];
        
        $GetStsqty = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$OrderCode."'");
        $GetStsqty->execute();
        $fstsqty = $GetStsqty->fetch();


        $res_categ = $db->prepare("SELECT * FROM category WHERE cat_id ='".$cat_id."'");
            $res_categ->execute();
            $fCateg = $res_categ->fetch();
            $Cname =  $fCateg['cat_name'];
            
            $res_sub_categ = $db->prepare("SELECT *FROM subcategory WHERE subcat_id ='".$subcat_ID."'");
            $res_sub_categ->execute();
            $fsub_Categ = $res_sub_categ->fetch();
            $Subname =  $fsub_Categ['subcat_name'];
        ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $fetch['cmd_table_id']; ?></td>
            <td>x<?php echo $fetch['cmd_qty'].' '.$fetch['menu_name']; ?></td>
            <td><?php echo $Cname;?></td>
            <td><?php echo $Subname;?></td>
            <td><?php echo number_format($fetch['menu_price']);?></td>
            <td><?php echo number_format($fetch['cmd_qty']*$fetch['menu_price']);?></td>
            <td><?php echo $fstsqty['dTrm'];?></td>
        </tr>
        <?php 
            } 
            
        }
        ?>
        <tfoot>
            <tr>
                <th colspan="6"></th>
                <th colspan="2">Total: <?php echo number_format($amount); ?></th>
            </tr>
        </tfoot>
    </tbody>
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