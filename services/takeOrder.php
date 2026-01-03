 <?php
 if (!empty($_POST['menu_id'])) {
    try {
        // VALIDATION: Ensure menu_id is valid array with items
        if (empty($_POST['menu_id']) || !is_array($_POST['menu_id'])) {
            throw new Exception("No menu items selected. Please select at least one item.");
        }

        // VALIDATION: Check reservation ID
        if (empty($_POST['reservID']) || !is_numeric($_POST['reservID'])) {
            throw new Exception("Invalid table selection. Please select a valid table.");
        }

        // VALIDATION: Validate each menu item exists and is numeric
        foreach ($_POST['menu_id'] as $item_id) {
            if (!is_numeric($item_id) || $item_id <= 0) {
                throw new Exception("Invalid menu item selected. Please refresh and try again.");
            }
        }

        // VALIDATION: Verify menu items exist in database
        $menu_placeholders = str_repeat('?,', count($_POST['menu_id']) - 1) . '?';
        $menu_check = $db->prepare("SELECT COUNT(*) as count FROM menu WHERE menu_id IN ($menu_placeholders)");
        $menu_check->execute($_POST['menu_id']);
        $menu_count = $menu_check->fetch()['count'];
        
        if ($menu_count != count($_POST['menu_id'])) {
            throw new Exception("One or more selected menu items are invalid. Please refresh and try again.");
        }

        // Function to generate random code
        function productcode() {
            $chars = "003232303232023232023456789";
            srand((double)microtime() * 1000000);
            $i = 0;
            $pass = '';
            while ($i <= 7) {
                $num = rand() % strlen($chars);
                $tmp = substr($chars, $num, 1);
                $pass = $pass . $tmp;
                $i++;
            }
            return $pass;
        }

        $reservID = $_POST['reservID'];
        $menu_id = $_POST['menu_id'];
        $today = date("Y-m-d H:i:s");
        $sts = 3;
        $cpny_ID = 0; // always 0 when order is for a table, not a company

        // FIXED LOGIC: Check for existing ACTIVE orders for this table TODAY
        $sql = $db->prepare("SELECT * FROM tbl_cmd 
                             WHERE reservat_id = :reservID 
                             AND status_id != '12' 
                             AND DATE(dTrm) = CURDATE()
                             ORDER BY id DESC LIMIT 1");
        $sql->execute([':reservID' => $reservID]);
        $count = $sql->rowCount();

        // START TRANSACTION - Ensures both tables are updated together or not at all
        $db->beginTransaction();
        
        // Prepare statement to fetch full menu row (used for snapshotting at insert time)
        $menu_price_stmt = $db->prepare("SELECT * FROM menu WHERE menu_id = :menu_id");

        if ($count > 0) {
            // EXISTING ORDER: Add items to existing order
            $get = $sql->fetch();
            $ordcode = $get['OrderCode'];

            // Insert each menu item as separate records
            foreach ($menu_id as $item_id) {
                // fetch menu price for unit_price field
                $menu_price_stmt->execute([':menu_id' => $item_id]);
                $fetch = $menu_price_stmt->fetch(PDO::FETCH_ASSOC);
                if (!$fetch) { throw new Exception("Menu item not found: " . $item_id); }
                // echo $fetch['menu_price'];
                // Insert into tbl_cmd (main order record)
                $order = $db->prepare("INSERT INTO 
                    `tbl_cmd`(`reservat_id`,`menu_id`,`Serv_id`, `unit_price`, `status_id`,`company_id`, `dTrm`,`OrderCode`)
                    VALUES(:reservID, :menu_id, :Serv_id, :unit_price, :status_id, :company_id, :dTrm, :ordcode)
                ");
                $order->execute([
                    ':reservID' => $reservID,
                    ':menu_id' => $item_id,
                    ':Serv_id' => $Oug_UserID,
                    ':unit_price' => $fetch['menu_price'],
                    ':status_id' => $sts,
                    ':company_id' => $cpny_ID,
                    ':dTrm' => $today,
                    ':ordcode' => $ordcode
                ]);

                // Insert into tbl_cmd_qty (quantity details) with snapshot fields
                $qty_insert = $db->prepare("INSERT INTO `tbl_cmd_qty`(`Serv_id`, `unit_price`, `cmd_table_id`,`cmd_item`, `cmd_qty`, `cmd_code`, `cmd_status`, `notify`, `item_name`, `item_code`, `cat_id`, `subcat_id`, `discount`)
                                           VALUES (:Serv_id, :unit_price, :cmd_table_id, :cmd_item, :cmd_qty, :cmd_code, :cmd_status, :notify, :item_name, :item_code, :cat_id, :subcat_id, :discount)");

                // Build snapshot data from fetched menu row
                $menuRow = $fetch; // from earlier SELECT * FROM menu
                $qty_insert->execute([
                    ':Serv_id' => $Oug_UserID,
                    ':unit_price' => isset($menuRow['menu_price']) ? $menuRow['menu_price'] : null,
                    ':cmd_table_id' => $reservID,
                    ':cmd_item' => $item_id,
                    ':cmd_qty' => 1,
                    ':cmd_code' => $ordcode,
                    ':cmd_status' => $sts,
                    ':notify' => 0,
                    ':item_name' => $menuRow['menu_name'] ?? null,
                    ':item_code' => $menuRow['item_code'] ?? null,
                    ':cat_id' => $menuRow['cat_id'] ?? null,
                    ':subcat_id' => $menuRow['subcat_id'] ?? ($menuRow['subcat_ID'] ?? null),
                    ':discount' => 0
                ]);
            }

            // COMMIT TRANSACTION
            $db->commit();
            $msg = "Successfully added items to existing order!";
            echo '<meta http-equiv="refresh" content="2;URL=index?resto=prcsOrder_prcssng&m=' . $reservID . '&s=' . $ordcode . '&st=' . $sts . '">';
            
        } else {
            // NEW ORDER: Create fresh order with new order code
            $Order_code = time() . productcode();

            // Insert each menu item as separate records
            foreach ($menu_id as $item_id) {                    // fetch menu price for unit_price field
                    $menu_price_stmt->execute([':menu_id' => $item_id]);
                    $fetch = $menu_price_stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$fetch) { throw new Exception("Menu item not found: " . $item_id); }                // Insert into tbl_cmd (main order record)
                $order = $db->prepare("INSERT INTO `tbl_cmd`(`reservat_id`,`menu_id`,`Serv_id`, `status_id`,`company_id`, `dTrm`,`OrderCode`)
                                       VALUES(:reservID,:menu_id,:Serv_id, :status_id,:company_id,:dTrm,:Order_code)");
                $order->execute([
                    ':reservID' => $reservID,
                    ':menu_id' => $item_id,
                    ':Serv_id' => $Oug_UserID,
                    ':status_id' => $sts,
                    ':company_id' => $cpny_ID,
                    ':dTrm' => $today,
                    ':Order_code' => $Order_code
                ]);

                // Insert into tbl_cmd_qty (quantity details) with snapshot fields
                $qty_insert = $db->prepare("INSERT INTO `tbl_cmd_qty`(`Serv_id`, `unit_price`, `cmd_table_id`,`cmd_item`, `cmd_qty`, `cmd_code`, `cmd_status`, `notify`, `item_name`, `item_code`, `cat_id`, `subcat_id`, `discount`)
                                           VALUES (:Serv_id, :unit_price, :cmd_table_id,:cmd_item,:cmd_qty,:cmd_code,:cmd_status,:notify,:item_name,:item_code,:cat_id,:subcat_id,:discount)");

                $menuRow = $fetch; // from earlier SELECT * FROM menu
                $qty_insert->execute([
                    ':Serv_id' => $Oug_UserID,
                    ':unit_price' => isset($menuRow['menu_price']) ? $menuRow['menu_price'] : null,
                    ':cmd_table_id' => $reservID,
                    ':cmd_item' => $item_id,
                    ':cmd_qty' => 1,
                    ':cmd_code' => $Order_code,
                    ':cmd_status' => $sts,
                    ':notify' => 0,
                    ':item_name' => $menuRow['menu_name'] ?? null,
                    ':item_code' => $menuRow['item_code'] ?? null,
                    ':cat_id' => $menuRow['cat_id'] ?? null,
                    ':subcat_id' => $menuRow['subcat_id'] ?? ($menuRow['subcat_ID'] ?? null),
                    ':discount' => 0
                ]);
            }

            // COMMIT TRANSACTION
            $db->commit();
            $msg = "Successfully created new order!";
            echo '<meta http-equiv="refresh" content="2;URL=index?resto=prcsOrder_prcssng&m=' . $reservID . '&s=' . $Order_code . '&st=' . $sts . '">';
        }
    } catch (Exception $ex) {
        // ROLLBACK TRANSACTION on any error
        if ($db->inTransaction()) {
            $db->rollback();
        }
        
        // Set error message for display
        $msge = $ex->getMessage();
        $msg = $ex->getMessage();
        
        // Log error for debugging (optional)
        error_log("Order Creation Error: " . $ex->getMessage() . " - User: " . ($Oug_UserID ?? 'Unknown') . " - Table: " . ($_POST['reservID'] ?? 'Unknown'));
    } catch (PDOException $ex) {
        // ROLLBACK TRANSACTION on database error
        if ($db->inTransaction()) {
            $db->rollback();
        }
        
        // Set user-friendly error message
        $msge = "Database error occurred. Please try again or contact support.";
        $msg = "Database error occurred. Please try again or contact support.";
        
        // Log detailed error for debugging
        error_log("Database Error in Order Creation: " . $ex->getMessage() . " - User: " . ($Oug_UserID ?? 'Unknown') . " - Table: " . ($_POST['reservID'] ?? 'Unknown'));
    }
}
 ?>
 
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
                 
                 <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Sorry!</strong> Access Denied!<?php echo htmlentities($snp); ?>
                  </div>
                <?php } ?>
            <div class="form-example-wrap mg-t-30">
            <form action="" method="POST">
                 <div class="row">
                   <label class="col-md-2 control-label" for=""><strong>Select Table </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <select name="rmID" id="rmID" class="form-control chosen" data-live-search="true" onchange=AjaxFunction(); required placeholder="Choose One">
                         <option></option>
                            <?php 
                            $stmt = $db->prepare("SELECT * FROM tbl_tables 
                            WHERE status = '1' AND table_id NOT IN(SELECT reservat_id FROM tbl_cmd WHERE status_id != '12' AND DATE(tbl_cmd.dtrm) = CURDATE())
                            ORDER BY table_id ASC");
                            $stmt->execute();
                            while($fetch = $stmt->fetch()){
                            ?>
    						<option value="<?php echo $fetch['table_id']; ?>"><?php echo $fetch['table_no']; ?></option>
    						<?php 
    						 }
    						?>
    				     </select>
                    </div>
                
                      <div name='display_res' id='display_res'>
                          
                      </div>
                   </div> <!-- /Row -->
                   
                   <br>
                   <div name="menuResult" id="menuResult">
     
                  </div>
                 <br>
                     
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#rmID").change(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_room?rmID=' + $(this).val() , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

        // PREVENT FORM SUBMISSION WITHOUT MENU SELECTION
        $('form').on('submit', function(e) {
            // Check if any menu items are selected
            var selectedItems = $('input[name="menu_id[]"]:checked');
            
            if (selectedItems.length === 0) {
                e.preventDefault(); // Stop form submission
                alert('Please select at least one menu item before placing the order.');
                return false;
            }
            
            // Check if table is selected
            var selectedTable = $('#rmID').val();
            if (!selectedTable || selectedTable === '') {
                e.preventDefault(); // Stop form submission
                alert('Please select a table before placing the order.');
                return false;
            }
            
            // Show confirmation
            var itemCount = selectedItems.length;
            var tableName = $('#rmID option:selected').text();
            
            if (!confirm('Confirm order: ' + itemCount + ' item(s) for ' + tableName + '?')) {
                e.preventDefault();
                return false;
            }
            
            return true; // Allow form submission
        });

    });
</script>
