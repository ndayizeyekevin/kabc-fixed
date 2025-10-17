  <table id="data-table-basic" class="table table-striped">
                                 <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                           <th>Category</th>
                                        
                                        <th>Quantity</th>
                                           <th>Unit</th>
                                     
                                         <th>Action</th> 
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
                                	
                                	
                                		    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
                                		    
                                		    
                                		  $sql = "SELECT qty,price,item_name,unit_name,limit_data,item_id,cat_id FROM `tbl_items` 
                                		INNER JOIN `tbl_item_stock` ON tbl_items.item_id=tbl_item_stock.item
                                		INNER JOIN tbl_unit ON tbl_items.item_unit=tbl_unit.unit_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($fetch = $result->fetch_assoc()) {  
                                		    
                                		    
                                		    
                                		    $i++;
                                		    
                                		    $total = $fetch['price'] * $fetch['qty'];
                                		    $amount =  $amount  + $total;
                                		    
                                		    
                                		    
                                		    
                                		    
                                		    
                                     	?>
                                     	<tr>
                                     	    <td><?php echo $i; ?></td>
                                            <td><?php echo $fetch['item_name']; ?></td>
                                            
                                             <td><?php echo getCategoryName($fetch['cat_id']); ?></td>
                                            
                                            <td><?php echo $fetch['qty']; ?> <?php if($fetch['qty']<=$fetch['limit_data']){
                                            echo "<span style='color:Red'>You have reached limit</span>";}?> </td>
                                            <td><?php echo $fetch['unit_name']; ?></td>
                                            
                                               
                                            
                                             <td>
                                                 
                                                 
                                            
                                             
                                               <a class="btn-sm" href="?resto=stock_take&myId=<?php echo $fetch['item_id'] ?>">Stock Take</a>
                                                 
                                                 

                                            </td>
                                     	</tr>
                                            <?php
                                        }}
                                    ?>
                         
                            </tbody>
                        </table>
                                   
                                    
                                         <tr>
                                <td colspan='5'>Total</td>
                          
                                       <td><?php echo number_format($amount)?></td>
                            </tr>