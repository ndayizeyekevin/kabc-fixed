<?php
     $id = $_GET['m'];
     $code = $_GET['c'];
     $get_rsv = $db->prepare("SELECT * FROM tbl_tables 
        WHERE table_id = '".$id."'");
       $get_rsv->execute();
       $getFetch = $get_rsv->fetch();
    
      // for activate Subject
        if(isset($_REQUEST['proccess']))
        {
        try{
            $menu_id = $_REQUEST['menu_id'];
            $reservation_ID = $_REQUEST['reservation_ID'];
            $orderCode = $_POST['orderCode'];
    		$a = 7;

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    			$sql = "UPDATE `tbl_cmd` SET `status_id` = '".$a."' WHERE `OrderCode` = '".$orderCode."' ";
    			$acidq = $db->prepare($sql);
    			$acidq->execute();

    		 for($i=0; $i<count($menu_id); $i++){
    			

                $sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_status` = '".$a."' WHERE `cmd_qty_id` = '".$menu_id[$i]."' AND
    			`cmd_code` = '".$orderCode."' ";
    			$acidq2 = $db->prepare($sql2);
    			$acidq2->execute();
    			
    			$msg="Processing Now!";
                echo '<meta http-equiv="refresh"'.'content="2;URL=?resto=prcsOrder">';
                
    		 }
    	}catch(PDOException $e){
    		echo $e->getMessage();
    	}
      }
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
                    <div class="normal-table-list mg-t-30">
                        <form action="" method="POST">
                        <div class="bsc-tbl-st" id="print">
                            <div class="basic-tb-hd">
                            <h2>Menu-Order <span class="rmNo">Table No. <?php $_SESSION['tableno'] = $getFetch['table_no']; echo $getFetch['table_no'];?></span></h2>
                        </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu Order</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Date-Time</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 <?php
                                    $i = 0;
                                       $bon="";
                            		$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                                    WHERE tbl_cmd_qty.cmd_code = '".$code."' AND tbl_cmd_qty.cmd_status = '13' AND menu.cat_id != '2'");
                            		$sql->execute(array());
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                            		   $cat_id= $fetch['cat_id'];
                            		   $subcat_ID = $fetch['subcat_ID'];
                            		   $menu_id = $fetch['cmd_qty_id'];
                            		   $OrderCode = $fetch['cmd_code'];
                            		   $message = $fetch['message'];
                            		   $_SESSION['no'] = $OrderCode;
                            		   $Serv_id=$fetch['Serv_id'];
                            		   $q = "SELECT concat(f_name, ' ', l_name) as names FROM `tbl_user_log`WHERE tbl_user_log.u_id = $Serv_id";
                            		  
                            		   $res_categ = $db->prepare($q);
                            		 $res_categ->execute();
                            		 $fCateg = $res_categ->fetch();
                            		 $Cname =  $fCateg['names'];
                            		  $_SESSION['servant_name'] = $Cname;
                            		  
                            		   $bon = $bon.$fetch['menu_name'].'<br> Qty: '.$fetch['cmd_qty'].'<br> '.$fetch['message'].'<hr style="border: none; border-top: 1px dashed black; width: 100%;" />';

                                       $GetStsqty = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$OrderCode."'");
                                   $GetStsqty->execute();
                                   $fstsqty = $GetStsqty->fetch();
                            		   
                            		 $res_categ = $db->prepare("SELECT *FROM category WHERE cat_id ='".$cat_id."'");
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
                                         <input type="checkbox" name="menu_id[]" id="<?php echo $menu_id;?>" value="<?php echo $menu_id;?>"/>
                                         <label for="<?php echo $menu_id;?>"></label>    
                                        </td>
                                        <td>x<?php echo $fetch['cmd_qty'].' '.$fetch['menu_name']; ?></td>
                                        <td><?php echo $Cname; ?></td>
                                        <td><?php echo $Subname; ?></td>
                                        <td><?php echo $fstsqty['dTrm']; ?></td>
                                        <td class="alert alert-danger text-info text-capitalize"><?php echo $message; ?></td>
                                    </tr>
                                    <?php 
                            		    } 
                            		    
                            		}
                            		
                            		$_SESSION['bon'] = $bon;
                                    ?>
                                </tbody>
                            </table>
                         </div>
                         <input type="hidden" name="orderCode" value="<?php echo $OrderCode;?>">
                        <input type="hidden" name="reservation_ID" value="<?php echo $id;?>">
                      <br/>
                          <button type="submit" name="proccess" id="proccess" class="btn btn-info btn-sm" style="border-radius: 5px;"><i class="fa fa-step-forward"></i> Proceed</button>
                      </form>
                      <hr>
                        <a href="https://saintpaul.gope.rw/reciept/bon.php?ref=<?php echo $_REQUEST['c']?>" 
   class="btn btn-secondary btn-sm" 
   onclick="if(!confirm('Print bon?')) return false; else return true;">
   <i class="fa fa-cutlery"></i> Kitchen
</a>
                    </div>
                </div>
            </div>
        </div>
    </div>