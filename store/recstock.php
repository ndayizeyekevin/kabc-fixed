<?php
include '../inc/conn.php';
    // add new member;


    if(ISSET($_POST['add'])){
        $item = $_POST['item'];
        $unit = $_POST['unit'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $supplier = !empty($_POST['supplier']) ? $_POST['supplier'] : NULL;
        $status = '1';
        $today = date("Y-m-d");

        // Insert into tbl_items
        $sql = "INSERT INTO `tbl_items`(`item_name`, `item_unit`, `item_status`, `price`, `cat_id`, `supplier`) 
            VALUES ('$item', '$unit', '$status', '$price', '$category', '$supplier')";
        $conn->query($sql);
        $lastID = $conn->insert_id;

        // Insert into tbl_item_stock
        $sql2 = "INSERT INTO `tbl_item_stock`( `item`, `qty`, `date_tkn`) VALUES ('$lastID','0','$today')";
        $conn->query($sql2);

        // Insert into tbl_progress to track price
        $sql3 = "INSERT INTO `tbl_progress`(`date`, `in_qty`, `last_qty`, `out_qty`, `item`, `end_qty`, `new_price`) VALUES ('$today', '0', '0', '0', '$lastID', '0', '$price')";
        $conn->query($sql3);

        $msg = "Successfully Added!";
        echo '<meta http-equiv="refresh" content="1;URL=index?resto=stock">';
    }
        
        
    function getItemPrice($id) {
        include '../inc/conn.php';

        // Get the item from tbl_items
        $itemQuery = "SELECT * FROM tbl_items WHERE item_id = '$id'";
        $itemResult = $conn->query($itemQuery);

        if ($itemResult && $itemResult->num_rows > 0) {
            $item = $itemResult->fetch_assoc();
            $defaultPrice = $item['price'];

            // Check tbl_progress for the latest new_price
            $progressQuery = "SELECT new_price FROM tbl_progress WHERE item = '$id' ORDER BY prog_id DESC LIMIT 1";
            $progressResult = $conn->query($progressQuery);

            if ($progressResult && $progressResult->num_rows > 0) {
                $progress = $progressResult->fetch_assoc();
                if ($progress['new_price'] > 0) {
                    return $progress['new_price'];
                }
            }

            // Fallback to default item price
            return $defaultPrice;
        }

        // If item not found, return null or any fallback value you want
        return null;
    }



    function getCategoryName($id){
        
    include '../inc/conn.php';	

    $sql = "SELECT * FROM category where cat_id='$id' ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        return $row['cat_name'];
    }
    }

    }





    function update_price($amount,$id){
    include '../inc/conn.php';   
        
        
    // $sql = "UPDATE tbl_items SET price='$amount' WHERE item_id=$id";

    // if ($conn->query($sql) === TRUE) {
    
    //  return 0;
    // } else {
    //   echo "Error updating record: " . $conn->error;
    // }
        
    }
        
        
    error_reporting(0);
        
    function getAverage($id){
        

    return  getItemPrice($id);

        
    }
        
        
    if (isset($_POST['record'])) {
        $item  = $_POST['item_id'];
        $qty   = $_POST['qty'];
        $price = $_POST['price']; 
        $date  = date("Y-m-d");

        // Check if item already exists in stock
        $sql = "SELECT * FROM tbl_item_stock WHERE item = '$item'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQty = $row['qty'] + $qty;
            $sql2 = "UPDATE tbl_item_stock SET qty = '$newQty' WHERE item = '$item'";
            $conn->query($sql2);
        } else {
            $sql2 = "INSERT INTO tbl_item_stock (item, qty, date_tkn) VALUES ('$item', '$qty', '$date')";
            $conn->query($sql2);
        }
        // Log the stock change (simple insert)
        // $sql3 = "INSERT INTO tbl_progress (date, in_qty, last_qty, out_qty, item, end_qty, new_price) VALUES ('$date', '$qty', '0', '0', '$item', '$qty', '$price')";
        // $conn->query($sql3);
        // Check if the item exists in tbl_progress and if the quantity is zero, update the price and the quantity with the new values instead of inserting a new row

        $checkProgress = "SELECT * FROM tbl_progress WHERE item = '$item' ORDER BY prog_id DESC LIMIT 1";
        $progressResult = $conn->query($checkProgress);
        if ($progressResult && $progressResult->num_rows > 0) {
            $progressRow = $progressResult->fetch_assoc();
            if ($progressRow['end_qty'] == 0) {
                // Update existing row
                $sql4 = "UPDATE tbl_progress SET in_qty = in_qty + '$qty', last_qty = last_qty + $qty, new_price = '$price', end_qty = end_qty + '$qty', remark = 'Opening Stock' WHERE prog_id = '".$progressRow['prog_id']."'";
                $conn->query($sql4);
            } else {
                // Insert new row
                $sql3 = "INSERT INTO tbl_progress (date, in_qty, last_qty, out_qty, item, end_qty, new_price, remark) VALUES ('$date', '$qty', '0', '0', '$item', '$qty', '$price', 'Opening Stock')";
                $conn->query($sql3);
            }
        } else {
            // No previous progress record, insert new
            $sql3 = "INSERT INTO tbl_progress (date, in_qty, last_qty, out_qty, item, end_qty, new_price, remark) VALUES ('$date', '$qty', '0', '0', '$item', '$qty', '$price', 'Opening Stock')";
            $conn->query($sql3);
        }


        $msg = "Successfully Recorded!";
        echo '<meta http-equiv="refresh" content="1;URL=index?resto=stock">';
    }
                
    ?>

    <!-- delete -->
    <?php
            if(ISSET($_GET['myId'])){
            $id = $_GET['myId'];

            $conn->query("DELETE from `tbl_items` WHERE `item_id`='$id' ");
            $conn->query("DELETE from `tbl_item_stock` WHERE `item`='$id' ");

            $msge = "Deleted Successfully!";
            echo'<meta http-equiv="refresh"'.'content="2;URL=?resto=stock">';
        }
    ?>

    <style>
        .form-ic-cmp{
            margin-bottom:10px;
        }
    </style>
    <!-- Breadcomb area Start-->
        <div class="breadcomb-area">
            <div class="container">
                
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
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="breadcomb-list">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="breadcomb-wp">
                                        <div class="breadcomb-icon">
                                            <i class="fa fa-cog"></i>
                                        </div>
                                        <div class="breadcomb-ctn">
                                            <h2>Manage Stock</h2>
                                            
                                            <center><h4> Stock as on <?php echo date('Y-m-d');?> </h4> </center>
                                        </div>
                                    </div>
                                </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
                                    <div class="breadcomb-report">
                                        <button type="button" data-toggle="modal" data-target="#myModalone1" data-placement="left" title="Record Stock" class="btn"><i class="fa fa-plus-circle"></i> Record Initial Stock</button>
                                        <button type="button" data-toggle="modal" data-target="#myModalone" data-placement="left" title="Add Item" class="btn"><i class="fa fa-plus-circle"></i> Add Item</button>
                                        <button class='btn btn-info' id="print">Print</button>
                                        <button class='btn btn-success' id="exportExcel">Export Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Data Table area Start-->
        <div class="data-table-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="data-table-list">
                            <div class="table-responsive">
                                <div id="DataList">Loading stock please wait....</div>
                                <table id="data-table-basic" class="table table-striped" hidden>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item Name</th>
                                            <th>Category</th>
                                            
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                        <?php
                                        $i = 0;
                                        $amount = 0;
                                        $sql = $conn->prepare("SELECT * FROM `tbl_items` 
                                            INNER JOIN `tbl_item_stock` ON tbl_items.item_id=tbl_item_stock.item
                                            INNER JOIN tbl_unit ON tbl_items.item_unit=tbl_unit.unit_id LIMIT 5");
                                        $sql->execute();
                                        while($fetch = $sql->fetch()){
                                            $i++;
                                            $row_price = (float)getAverage($fetch['item_id']);
                                            $row_qty = (float)$fetch['qty'];
                                            $row_total = $row_price * $row_qty;
                                            $amount += $row_total;
                                    ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $fetch['item_name']; ?></td>
                                            <td><?php echo getCategoryName($fetch['cat_id']); ?></td>
                                            <td><?php echo $fetch['qty']; ?> <?php if($fetch['qty']<=$fetch['limit_data']){ echo "<span style='color:Red'>You have reached limit</span>";}?> </td>
                                            <td><?php echo $fetch['unit_name']; ?></td>
                                            <td><?php echo number_format($row_price); ?></td>
                                            <td><?php echo number_format($row_total); ?></td>
                                            <td>
                                                <a class="btn-sm" href="?resto=update_item&myId=<?php echo $fetch['item_id'] ?>">Update</a>
                                                <a class="btn-sm" href="?resto=stock&myId=<?php echo $fetch['item_id'] ?>" onclick="if(!confirm('Do you really want to Delete This Item?'))return false;else return true;">Delete</a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    <tr>
                                        <td colspan='6'>Total</td>
                                        <td><?php echo number_format($amount); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
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
                <h4 class="modal-title">Add New Item</h4>
                </div>
                <div class="modal-body">
                    <form action="" enctype="multipart/form-data" method="POST">
            <div class="row">
                <div class="form-group">
                    <label class="col-md-2 control-label" for=""><strong>Item Name</strong><span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        <input type="text" name="item" class="form-control" required>
                    </div>
                        <label class="col-md-2 control-label" for=""><strong>Unit</strong></label>
                    <div class="col-md-4">
                        <br>
                            <select name="unit" class="form-control chosen" data-placeholder="Choose Unit...">
                        <?php
                        $stmt = $db->query('SELECT * FROM tbl_unit');
                        try {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                if($res_cntr==$row['unit_id'])
                                {
                                ?>
                                <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['unit_name']; ?></option>

                                <?php
                            }
                            else
                            {
                                ?>
                                <option value="<?php echo $row['unit_id']; ?>"><?php echo $row['unit_name']; ?></option>
                                <?php
                            }
                            }
                        }
                        catch (PDOException $ex) {
                            //Something went wrong rollback!
                            echo $ex->getMessage();
                        }
                    ?>   
                        </select>
                    </div>
                    
                    <br><br>
                    
                    <label class="col-md-2 control-label" for=""><strong>Category</strong></label>
                    <div class="col-md-4">
                            <select name="category" class="form-control chosen" data-placeholder="Select category...">
                        <?php
                        $stmt = $db->query('SELECT * FROM category');
                        try {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            
                                <option value="<?php echo $row['cat_id']; ?>"><?php echo $row['cat_name']; ?></option>
                                <?php
                            
                            }
                        }
                        catch (PDOException $ex) {
                            //Something went wrong rollback!
                            echo $ex->getMessage();
                        }
                    ?>   
                        </select>
                        
                        <br><br>
                        
                                                    <select name="supplier" class="form-control">
                                    <option value=''>Select Supplier</option><?php
                $result = $db->prepare("SELECT * FROM suppliers");
                                            $result->execute();
                                            while($fetch = $result->fetch()){
    ?><option value='<?php echo $fetch['id']?>'><?php echo $fetch['name']?></option>
    <?php }

        ?>			
            
    </select>	
                        
                    </div>
                    
                    
                    
                
                    
                </div>
                </div>
                <label class="col-md-2 control-label" for=""><strong>Item Price</strong><span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        <input type="text" name="price"  placeholder="Price" class="form-control" required>
                    </div>
                <br>
                <br>
                <div class="row">
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
        
        <div class="modal fade" id="myModalone1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
        
        <div class="modal-header">
            <h4 class="modal-title">Record Item Quantity</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>

        <div class="modal-body">
            <form action="" method="POST" enctype="multipart/form-data">

            <div class="form-group row">
                <label class="col-md-4 col-form-label"><strong>Item Name <span class="text-danger">*</span></strong></label>
                <div class="col-md-8">
                <select name="item_id" class="form-control chosen" data-placeholder="Choose Item..." required>
                    <?php foreach (StoreController::getNewStockItems($db) as $row): ?>
                    <option value="<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-4 col-form-label"><strong>Quantity <span class="text-danger">*</span></strong></label>
                <div class="col-md-8">
                <input type="number" name="qty" min="0" step="0.001" class="form-control" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-4 col-form-label"><strong>Price (RWF) <span class="text-danger">*</span></strong></label>
                <div class="col-md-8">
                <input type="number" name="price" class="form-control" step="0.01" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12 text-center">
                <button type="submit" name="record" class="btn btn-info btn-sm" style="border-radius: 4px;">
                    <i class="fa fa-save"></i> Save
                </button>
                <button type="reset" class="btn btn-default btn-sm">
                    <i class="fa fa-remove"></i> Reset
                </button>
                </div>
            </div>

            </form>
        </div>

        </div>
    </div>
    </div>

        </div>
        
        
        <?php
        // Resolve Printed by full name from logged-in user ID with safe fallbacks
        $printedBy = '';
        try {
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                $stmtPB = $db->prepare("SELECT u.f_name, u.l_name 
                                        FROM tbl_users u 
                                        INNER JOIN tbl_user_log l ON u.user_id = l.user_id 
                                        WHERE l.user_id = ? LIMIT 1");
                $stmtPB->execute([$_SESSION['user_id']]);
                $rowPB = $stmtPB->fetch(PDO::FETCH_ASSOC);
                if ($rowPB) {
                    $printedBy = trim(($rowPB['f_name'] ?? '') . ' ' . ($rowPB['l_name'] ?? ''));
                }
            }
        } catch (Exception $e) {
            // ignore and fallback below
        }
        if ($printedBy === '') {
            if (isset($_SESSION['user_fullname'])) { $printedBy = $_SESSION['user_fullname']; }
            elseif (isset($_SESSION['fullname'])) { $printedBy = $_SESSION['fullname']; }
            elseif (isset($_SESSION['name'])) { $printedBy = $_SESSION['name']; }
            elseif (isset($_SESSION['username'])) { $printedBy = $_SESSION['username']; }
            elseif (isset($_SESSION['f_name']) || isset($_SESSION['l_name'])) { $printedBy = trim(($_SESSION['f_name'] ?? '') . ' ' . ($_SESSION['l_name'] ?? '')); }
        }
        ?>
        <!-- SheetJS for real XLSX export -->
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
        <script>
        getData();

        function getData(){
            var data = new FormData();
            data.append("type", "daily_occupancy_rate");

            var xhr = new XMLHttpRequest();
            xhr.withCredentials = true;

            xhr.addEventListener("readystatechange", function() {
            if(this.readyState === 4) {
                document.getElementById('DataList').innerHTML=this.responseText;
                // alert(this.responseText);
            }
            });

            xhr.open("POST", "stock_items.php");
            xhr.send(data);
        }

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("print").addEventListener("click", function () {
                const table = document.getElementById("data-table-basic");

                if (!table) {
                    alert("Table not found!");
                    return;
                }

                const printContents = `
                    <html>
                    <head>
                        <center>
                        <img src='../img/logo.png' alt='Company Logo' style='max-width:150px;'><br>
                        <div><?php $company_name; ?><br>
                        <?php $company_address; ?><br>
                        TIN/VAT Number: <?php $company_tin; ?><br>
                        <br>
                        Phone: <?php $company_phone; ?> <br>
                        </p></div>
                        </center>
                        <br>
                        <title>Print Inventory Table</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 40px; }
                            table { width: 100%; border-collapse: collapse; }
                            th, td { border: 1px solid black; padding: 8px; text-align: left; }
                            h2 { text-align: center; margin-bottom: 20px; }
                            a, #printx { display: none; }
                            .signature-section { margin-top: 40px; }
                            .signature-row { display: flex; justify-content: space-between; gap: 20px; }
                            .signature-box { width: 32%; text-align: center; }
                            .signature-box .sig-line { margin-top: 50px; border-top: 1px solid #000; height: 0; }
                        </style>
                    </head>
                    <body>
                        <h2>Inventory Table</h2>
                        ${table.outerHTML}
                        <div class="signature-section">
                            <div class="signature-row">
                                <div class="signature-box" style="text-align:left;">
                                    <strong>Printed by:</strong><br>
                                    <?= htmlspecialchars($printedBy ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box">
                                    <strong>Received by:</strong><br>
                                    <br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                                <div class="signature-box" style="text-align:right;">
                                    <strong>Approved by:</strong><br>
                                    <br>
                                    <div class="sig-line"></div>
                                    <small>Name & Signature</small>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write(printContents);
                printWindow.document.close();

                printWindow.onload = function () {
                    printWindow.focus();
                    printWindow.print();
                    // printWindow.close();
                };
            });

            // Export to Excel (true .xlsx using SheetJS)
            document.getElementById("exportExcel").addEventListener("click", function(){
                const table = document.getElementById("data-table-basic");
                if (!table) {
                    alert("Table not found!");
                    return;
                }
                // Clone table so we can adjust columns without affecting UI
                const exportTable = table.cloneNode(true);
                // Remove the last Action column (header and body) if present
                try {
                    const theadRow = exportTable.querySelector('thead tr');
                    if (theadRow && theadRow.lastElementChild) theadRow.removeChild(theadRow.lastElementChild);
                    exportTable.querySelectorAll('tbody tr').forEach(tr => { if (tr.lastElementChild) tr.removeChild(tr.lastElementChild); });
                } catch(e) { /* ignore */ }

                // Create workbook from table
                const wb = XLSX.utils.table_to_book(exportTable, { sheet: "Stock" });
                const today = new Date();
                const y = today.getFullYear();
                const m = String(today.getMonth()+1).padStart(2,'0');
                const d = String(today.getDate()).padStart(2,'0');
                XLSX.writeFile(wb, `stock_${y}${m}${d}.xlsx`);
            });
        });
        </script>
