  <?php
  include_once "./controllers/storeController.php";
  include_once __DIR__."/../inc/config.php";
  
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  
  ?>
  
      
      
  
    <table id="data-table-basic" class="table table-striped">
                                 <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                           <th>Category</th>
                                        
                                        <th>Quantity</th>
                                           <th>Unit</th>
                                         <th>Price</th>
                                         <th>Total</th>
                                         <th id='printx'>Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                            
                                    <?php
                                    include '../inc/conn.php';

                                        function getCategoryName($id){
                                        	
                                        include '../inc/conn.php';	
                                        
                                        $sql = "SELECT cat_name FROM category where cat_id='$id' ";
                                        $result = $conn->query($sql);
                                        
                                        if ($result->num_rows > 0) {
                                          // output data of each row
                                          while($row = $result->fetch_assoc()) {
                                            return $row['cat_name'];
                                          }
                                        }
                                        }

                                    
                                    
                                    $i = 0;
                                    $amount =0;
                                		$sql = "SELECT qty,price,item_name,unit_name,limit_data,item_id,cat_id FROM `tbl_items` 
                                		INNER JOIN `tbl_item_stock` ON tbl_items.item_id = tbl_item_stock.item
                                		INNER JOIN tbl_unit ON tbl_items.item_unit = tbl_unit.unit_id ORDER BY item_name ASC";
                                        $result = $conn->query($sql);
                                        
                                        if ($result->num_rows > 0) {
                                          // output data of each row
                                          while($fetch = $result->fetch_assoc()) {  
                                                                        		    
                                		    
                                		    
                                		    $i++;
                                		    
                                		    $total = (float)StoreController::getUnitePrice($db,$fetch['item_id']) * (float)$fetch['qty'];
                                		    $amount =  $amount  + $total;
                                		    
  
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><?php echo $fetch['item_name']; ?></td>
                                            
                                             <td><?php echo getCategoryName($fetch['cat_id']); ?></td>
                                            
                                            <td><?php echo $fetch['qty']; ?> <?php if($fetch['qty']<=$fetch['limit_data']){
                                            echo "<span style='color:Red'>You have reached limit</span>";}?> </td>
                                            <td><?php echo $fetch['unit_name']; ?></td>
                                             <td><?php  echo number_format((float)StoreController::getUnitePrice($db,$fetch['item_id']),3); 
                                             
                                    
                                             ?></td>
                                               <td><?php echo number_format(((float)StoreController::getUnitePrice($db,$fetch['item_id']) * (float)$fetch['qty']),3); ?></td>
                                            
                                             <td id="printx">
                                                 
                                                 
                                             <a class="btn-sm" href="?resto=update_item&myId=<?php echo $fetch['item_id'] ?>">Update</a>
                                             
                                               <!-- <a class="btn-sm" href="?resto=stock_take&myId=<?php //echo $fetch['item_id'] ?>">Stock Take</a>
                                                <a class="btn-sm" href="?resto=stock&myId=<?php //echo  $fetch['item_id'] ?>" onclick="if(!confirm('Do you really want to Delete This Item?'))return false;else return true;">Delete</a>
                                            </td> -->
                                     	</tr>
                                            <?php
                                        }}
                                    ?>
                                    
                                    <tr>
                                <td colspan='6'><strong>TOTAL</strong></td>
                          
                                       <td><strong><?php echo number_format($amount)?></strong></td>
                            </tr>
                         
                            </tbody>
                        </table>

