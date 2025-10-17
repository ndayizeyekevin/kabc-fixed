<?php
require_once ("../inc/config.php");
require_once ("../holder/template_scripts.php");
require_once ("../holder/template_styles.php");
$date_from = $_REQUEST['date_from'];
?>
  
  <style>
      .rmNo {
      text-align: center;
      text-transform: uppercase;
      color: #ba4a48;
    }
  </style>
 <div class="normal-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list">
                        <form action="" method="POST">
                        <div class="bsc-tbl-st" id="print">
                        <a href="generalReportPDF.php?&date_from=<?php echo $date_from;?>" class="btn btn-success pull-right" title="click to download" target="_blank" style="margin:5px;"><i class="fa fa-pdf-o"></i> Export PDF</a>
                        <table id="data-table-basic" class="table table-striped" style="font-size:11px;">
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
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>