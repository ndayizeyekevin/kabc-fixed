<?php
     $id = $_GET['m'];
     $code = $_GET['c'];
     $get_rsv = $db->prepare("SELECT * FROM tbl_tables 
        WHERE table_id = '".$id."'");
       $get_rsv->execute();
       $getFetch = $get_rsv->fetch();
    
      
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
                    <div class="normal-table-list mg-t-30">
                        <form action="" method="POST">
                        <div class="bsc-tbl-st" id="print">
                            <div class="basic-tb-hd">
                            <h2>Menu-Order <span class="rmNo">Table No. <?php echo $getFetch['table_no'];?></span></h2>
                        </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu Order</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Date-Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 <?php
                                    $i = 0;
                            		$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id=tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_code='".$code."' AND menu.cat_id='2'");
                            		$sql->execute(array());
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
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
                                        <td>
                                         <?php echo $i++; ?>   
                                        </td>
                                        <td>x<?php echo $fetch['cmd_qty'].' '.$fetch['menu_name']; ?></td>
                                        <td><?php echo $Cname;?></td>
                                        <td><?php echo $Subname;?></td>
                                        <td><?php echo $fstsqty['dTrm'];?></td>
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
        </div>
    </div>