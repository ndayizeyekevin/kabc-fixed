<?php
if(isset($_POST['update'])){
  $orderqty = $_POST['orderqty'];
  $orderqtyid = $_POST['orderqtyid'];
  $orderCode = $_POST['orderCode'];

  for($i=0; $i<count($orderqtyid); $i++){
    $sqlupd = $db->prepare("UPDATE tbl_cmd_qty SET cmd_qty = '".$orderqty[$i]."',cmd_status='5' WHERE cmd_qty_id = '".$orderqtyid[$i]."'");
    $sqlupd->execute();
  }
  
  $sqlupd = $db->prepare("UPDATE tbl_cmd SET status_id = '5' WHERE `OrderCode` = '".$orderCode."'");
  $sqlupd->execute();
  $msg = "Order quantity updated!";
  echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
}

  if(isset($_POST['confirm']))
    {
    try{
        $menu_id = $_POST['menu_id'];
        $reservation_ID = $_POST['reservation_ID'];
        $orderCode = $_POST['orderCode'];
        $table = $_GET['m'];
        
        $a = 8;

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE `tbl_cmd` SET `status_id` = '".$a."' WHERE `OrderCode` = '".$orderCode."'";
			$acidq = $db->prepare($sql);
			$acidq->execute();


         for($i=0; $i<count($menu_id); $i++){
			
			
			$sql2 = "UPDATE `tbl_cmd_qty` SET `cmd_status` = '".$a."' WHERE `cmd_qty_id` = '".$menu_id[$i]."' AND
			`cmd_code` = '".$orderCode."'";
			$acidqq = $db->prepare($sql2);
			$acidqq->execute();
			$msg = "Successfully Delivered!";
            echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
		  }

	}catch(PDOException $e){
	 echo $e->getMessage();
	 }
   }

 $id = $_GET['m'];
 $code = $_GET['s'];

 $stmts = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$code."'");
 $stmts->execute();
 $getsts = $stmts->fetch();
 $status = $getsts['status_id'];

 $get_rsv = $db->prepare("SELECT * FROM tbl_tables
    WHERE table_id = '".$id."'");
   $get_rsv->execute();
   $getFetch = $get_rsv->fetch();
   
   $GetSts = $db->prepare("SELECT *FROM tbl_status WHERE id = '".$status."'");
   $GetSts->execute();
   $fsts = $GetSts->fetch();
   
   $sttus = $fsts['status_name'];
   
   if($status == 13){
    $snp = "waiting Menu order to be completed";   
   }elseif($status == 5){
    $msg = "Order completed";
   }elseif($status == 7){
    $msg = "Order is in process";
   }elseif($status == 8){
    $msg = "Order delivered";
   }
   
   if(isset($_GET['id'])){
       $item = $_GET['id'];
       $m=$_GET['m'];
       $s=$_GET['s'];
       $st=$_GET['st'];
       
      $sql = $db->prepare("DELETE FROM tbl_cmd WHERE id = '".$item."'");
      $sql->execute();
      $sql2 = $db->prepare("DELETE FROM tbl_cmd_qty WHERE cmd_qty_id = '".$item."'");
      $sql2->execute();
       
      $msge = "Item Removed Successfully!";
      echo'<meta http-equiv="refresh"'.'content="1;URL=index?resto=prcsOrder_prcssng&m='.$m.'&s='.$s.'&st='.$st.'">';
   }


   if(isset($_POST['add']))
   {
    $Order_code=$_POST['orderCode'];
     $reservID = $_POST['reservation_ID'];
     $menu_id = $_POST['items'];
     $quantity = $_POST['quantity'];
     $today = date("Y-m-d H:i:s");
     $sts = 5;
     
      
          
         $sql = $db->prepare("SELECT * FROM tbl_cmd WHERE reservat_id = '".$reservID."' AND OrderCode = '$Order_code' AND status_id != '12'");
         $sql->execute();
         $count = $sql->rowCount();
         if($count > 0){
             $get = $sql->fetch();
             $ordcode = $get['OrderCode'];
             
             for($i=0; $i<count($menu_id); $i++){
                 $order = $conn->prepare("INSERT INTO `tbl_cmd`(`reservat_id`, `menu_id`, `Serv_id`, `status_id`,`company_id`, `dTrm`,`OrderCode`) 
         VALUES('$reservID','$menu_id[$i]','$Oug_UserID','$sts','$cpny_ID','$today','$ordcode')");
         $order->execute();
         
         $sql = $db->prepare("INSERT INTO `tbl_cmd_qty`(`Serv_id`, `cmd_table_id`,`cmd_item`, `cmd_qty`, `cmd_code`, `cmd_status`) 
         VALUES ('$Oug_UserID','$reservID','$menu_id[$i]','$quantity[$i]','$ordcode','5')");
         $sql->execute();
         
         $msg = "Successfully Ordered!";
         echo'<meta http-equiv="refresh"'.'content="2;URL=index?resto=lorder">';
             }
            }
   }
 ?>
 <?php 
function fill_product($db){
  $output= '';

  $select = $db->prepare("SELECT * FROM `menu`
  INNER JOIN category ON category.cat_id = menu.cat_id
  INNER JOIN subcategory ON subcategory.subcat_id = menu.subcat_ID
  ORDER BY menu.menu_name ASC");
  $select->execute();
  $result = $select->fetchAll();

  foreach($result as $row){
    $output.='<option value="'.$row['menu_id'].'">'.$row["menu_name"].'</option>';
  }

  return $output;
}
?>
  <style>
      .rmNo {
      text-align: center;
      text-transform: uppercase;
      color: #4CAF50;
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
                     <strong>Oooh Snap!</strong> <?php echo htmlentities($msge); ?>
                  </div>
                 <?php
                  }else if($snp){
                ?>
                 
                 <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Sorry!</strong> Access Denied!<?php echo htmlentities($snp); ?>
                  </div>
                <?php } ?>
                
                    <div class="normal-table-list mg-t-30">
                        <form action="" method="POST">
                        <div class="bsc-tbl-st" id="print">
                            <div class="basic-tb-hd">
                            <h2><small> <i class="fa fa-refresh"></i> <?php echo $sttus; ;?><span class="rmNo"> Table No. <?php echo $getFetch['table_no'];?></span></small> </h2>
                            <button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Items" class="btn pull-right"><i class="fa fa-plus-circle"></i> Add Items</button>
                        </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu Order</th>
                                        <th>Quantity</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Date-Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 <?php
                                    $i = 0;
                                    $tot = array();
                            		$sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                                    WHERE tbl_cmd_qty.cmd_code='".$code."'");
                            		$sql->execute(array());
                            		if($sql->rowCount()){
                            		while($fetch = $sql->fetch()){
                            		   $i++;
                                    $tot[] = $fetch['cmd_status'];
                                   
                                   $OrderCode = $fetch['cmd_code'];
                                   $status = $fetch['cmd_status'];
                            		  
                            		   
                                   
                                   $GetStsqty = $db->prepare("SELECT * FROM tbl_cmd WHERE OrderCode = '".$OrderCode."'");
                                   $GetStsqty->execute();
                                   $fstsqty = $GetStsqty->fetch();

                                   $GetStsmenu = $db->prepare("SELECT * FROM menu WHERE menu_id = '".$fetch['cmd_item']."'");
                                   $GetStsmenu->execute();
                                   $fstsmenu = $GetStsmenu->fetch();
                                   
                                   $rsv_ID = $fstsqty['reservat_id'];
                            		   $cat_id= $fstsmenu['cat_id'];
                            		   $subcat_ID = $fstsmenu['subcat_ID'];
                            		   $menu_id = $fetch['cmd_qty_id'];
                            		   $OrderCode = $fstsqty['OrderCode'];
                                   $serv = $fstsqty['Serv_id'];
                            		   
                            		   $GetSts = $db->prepare("SELECT *FROM tbl_status WHERE id = '".$status."'");
                                    $GetSts->execute();
                                    $fsts = $GetSts->fetch();


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
                                        <?php
                                        if($status == '5'){
                                            ?>
                                        <td>
                                          <input type="checkbox" name="menu_id[]" id="<?php echo $menu_id;?>" value="<?php echo $menu_id;?>"/>
                                          <label for="<?php echo $menu_id;?>"></label>    
                                        </td>
                                        
                                        <?php }else{?>
                                        <td><?php echo $i;?></td>
                                        <?php }?>
                                        <td><?php echo $fstsmenu['menu_name']; ?></td>
                                        <td>
                                        <?php
                                        if($status == '3'){ ?> 
                                        <input type="number" name="orderqty[]" value="<?php echo $fetch['cmd_qty']; ?>" class="form-control">  
                                        <input type="hidden" name="orderqtyid[]" value="<?php echo $fetch['cmd_qty_id']; ?>" class="form-control">  
                                        <?php 
                                        }else{
                                          echo $fetch['cmd_qty'];
                                        }
                                        ?>  
                                      </td>
                                        <td><?php echo $Cname;?></td>
                                        <td><?php echo $Subname;?></td>
                                        <td><?php echo $fetch['created'];?></td>
                                        <td>
                                        <?php
                                        if($status == '3'){
                                            $bullet ='<i class="fa fa-circle" aria-hidden="true" style="color: #dd5252;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                          ?>
                                          <a href="?resto=prcsOrder_prcssng&m=<?php echo $_GET['m'] ?>&s=<?php echo $_GET['s']; ?>&id=<?php echo $fetch['cmd_qty_id']; ?>&st=<?php echo $_GET['st']; ?>" class="btn btn-link btn-sm" style="color:#e46a76;" onclick="if(!confirm('Do you really want to remove this item on Order?'))return false;else return true;"><i class="fa fa-close"></i> Remove</a> 
                                          <?php
                                        } 
                                        elseif($status == '5'){
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                        }
                                        elseif($status == '7'){
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #44912d;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                        }
                                        elseif($status == '8'){
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                        }
                                        elseif($status == '13'){
                                          $bullet = '<i class="fa fa-circle" aria-hidden="true" style="color: #2b9fbc;"></i>';
                                          $text = $fsts['status_name'];
                                          echo $bullet." ".$text;
                                        }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php 
                            		    }
                            		}
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="orderCode" value="<?php echo $OrderCode;?>">
                        <input type="hidden" name="reservation_ID" value="<?php echo $id;?>">
                        <br/>
                        <?php 
                        // if($_GET['st'] != '8' && $_GET['st'] != '7' && $_GET['st'] != '3' && $_GET['st'] != '13')
                        if(in_array('5',$tot))
                        { 
                          ?>
                        <button type="submit" name="confirm" id="confirm" class="btn btn-info btn-sm" style="border-radius: 5px;" onclick="if(!confirm('Do you really want to Deliver?'))return false;else return true;"><i class="fa fa-thumbs-up"></i> Deliver </button>
                        <?php } ?>
                        <?php if($_GET['st']== '3'){ ?>
                        <button type="submit" name="update" id="update" class="btn btn-primary btn-sm" style="border-radius: 5px;" onclick="if(!confirm('Do you really want to update order quantity?'))return false;else return true;"><i class="fa fa-edit"></i> Confirm Order Quantity</button>
                        <?php } ?>
                        <a href="?resto=lorder" class="btn btn-secondary btn-sm" onclick="if(!confirm('Do you really want to go back?'))return false;else return true;"><i class="fa fa-step-backward"></i> Back</a>
                        
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Add Items</h4>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
            <div class="row">
            <div class="form-group">
                <div class="col-md-12">
                    <input type="hidden" name="orderCode" value="<?php echo $OrderCode;?>">
                    <input type="hidden" name="reservation_ID" value="<?php echo $id;?>">
                    <input type="hidden" name="serv_ID" value="<?php echo $serv;?>">
                 <table class="table table-border" id="myOrder">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>
                          <button type="button" name="addOrder" class="btn btn-success btn-sm btn_addOrder" required><span>
                            <i class="fa fa-plus"></i>
                          </span></button>
                        </th>
                    </tr>
        
                </thead>
                <tbody>
        
                </tbody>
              </table>
            </div>
            </div>
            <div class="form-group">
    		    <div class="form-actions col-md-12">
    		        <br />
    		        <center>								
    			        <button type="submit" name="add" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
    			        <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
    		        </center>
    		    </div>
            </div>
            </div>
            
            </form>
            </div>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> 
    <script>
 $(document).ready(function(){
      $(document).on('click','.btn_addOrder', function(){
        var html='';
        html+='<tr>';
        html+='<td><select class="form-control selectpicker productid" data-live-search="true" name="items[]" required><option value="">--Select Item--</option><?php
        echo fill_product($db)?></select></td>';
        html+='<td><input autocomplete="off" type="number" class="form-control quantity" name="quantity[]" required placeholder="0"></td>';
        html+='<td><button type="button" name="remove" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-remove"></i></button></td>'

        $('#myOrder').append(html);

        $('.productid').on('change', function(e){
          var productid = this.value;
          var tr=$(this).parent().parent();
          $.ajax({
            url:"getproduct.php",
            method:"get",
            data:{id:productid},
            success:function(data){
              $("#btn").html('<button type="submit" class="btn btn-success" name="add">Request</button>');
              tr.find(".quantity").val();
              tr.find("#unit").text(data["unit_name"]);
            }
          })
        })
         $('.selectpicker').selectpicker();
      })

      $(document).on('click','.btn-remove', function(){
        $(this).closest('tr').remove();
        calculate(0,0);
        $("#paid").val(0);
      })
    });
  </script>