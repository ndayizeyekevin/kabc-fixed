<?php

class StoreController{

   public static function getUnitePrice(PDO $conn,int $item):float{
        try {
            
            $stmt = $conn->prepare("SELECT  new_price  AS price
            FROM tbl_progress 
            WHERE item = :id 
            ORDER BY prog_id DESC 
            LIMIT 1");
            $stmt->execute([':id' => $item]);
            $price=0;
            if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch();
            $price = (float)$result['price'];
            }
            return $price;
        } catch (Throwable $th) {
            throw $th;
        }
   }

    public static function getStoreBalance($db, $start_date = null, $end_date = null, $item_id = null): array{
        try {
            // Set default dates if not provided
            if ($start_date === null || $end_date === null) {
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d');
            }
            $pre_day = date('Y-m-d', strtotime($start_date . ' -1 day'));
            // Prepare parameters for query
            // $params = [$pre_day,$start_date, $end_date,$end_date, $start_date];
            $params = [$pre_day,$start_date, $end_date,$start_date, $end_date,$end_date, $start_date];
            $item_condition = '';
            
            if ($item_id !== null) {
                $item_condition = ' AND p.item = ?';
                $params[] = $item_id;
            }
    
            $sql = "SELECT 
                        p.item,
                        i.item_name,
                        u.unit_name,
                        i.item_id,
                        i.price AS standard_price,
                        p.new_price,
                        -- Opening balance (coalesce to 0 if null)
                        COALESCE(
                            (SELECT pp.end_qty 
                             FROM tbl_progress pp 
                             WHERE pp.item = p.item AND pp.date <= ? 
                             ORDER BY pp.date DESC, pp.prog_id DESC LIMIT 1),
                            0
                        ) AS opening_balance,
                        
                        -- Total incoming (coalesce to 0 if null)
                         COALESCE((
                            SELECT SUM(pp.in_qty)
                            FROM tbl_progress pp
                            WHERE pp.item = p.item AND pp.date BETWEEN ? AND ?
                        ), 0) AS total_in,
                        
                        -- Total outgoing (coalesce to 0 if null)
                        -- COALESCE(SUM(p.out_qty), 0) AS total_out,
                        COALESCE((SELECT SUM(pp.out_qty) FROM tbl_progress pp WHERE pp.item = i.item_id AND pp.date BETWEEN ? AND ?), 0 ) AS total_out,
                        
                        -- Closing balance calculation
                        COALESCE(
                            (SELECT pp.end_qty 
                             FROM tbl_progress pp 
                             WHERE pp.item = p.item AND pp.date <= ? 
                             ORDER BY pp.date DESC, pp.prog_id DESC LIMIT 1),
                            0
                        ) AS closing_balance,
                        
                        -- Inventory value calculation
                        COALESCE(
                            (SELECT pp.end_qty * COALESCE(pp.new_price, i.price)
                             FROM tbl_progress pp 
                             WHERE pp.item = p.item AND pp.date <= ? 
                             ORDER BY pp.date DESC, pp.prog_id DESC LIMIT 1),
                            0
                        ) AS inventory_value
                        
                    FROM tbl_progress p
                    RIGHT JOIN tbl_items i ON p.item = i.item_id
                    JOIN tbl_unit u ON i.item_unit = u.unit_id
                    GROUP BY p.item, i.item_name, u.unit_name, i.price
                    ORDER BY i.item_name";
    
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Calculate summary totals with proper null handling
            $totals = [
                'total_opening' => 0,
                'total_in' => 0,
                'total_out' => 0,
                'total_closing' => 0,
                'total_value' => 0
            ];
    
            foreach ($items as $item) {
                $totals['total_opening'] += (float)$item['opening_balance'];
                $totals['total_in'] += (float)$item['total_in'];
                $totals['total_out'] += (float)$item['total_out'];
                $totals['total_closing'] = (float)$item['closing_balance'];
                $totals['total_value'] += (float)$item['inventory_value'];
            }
    
            return [
                'success' => true,
                'data' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'items' => $items,
                    'totals' => $totals,
                    'item_filter' => $item_id
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }

    public static function checkItemStock($db, $item_id) {
        $stmt = $db->prepare("SELECT qty FROM tbl_item_stock WHERE item = :item");
        $stmt->execute([':item' => $item_id]);
    
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    
        return null; // Item not found in stock
    }    

    public static function updateItemStock(PDO $db, int $item, float $qty) {
        $stmt = $db->prepare("UPDATE tbl_item_stock SET qty = :qty WHERE item = :item");
        $stmt->execute([
            ':qty' => $qty,
            ':item' => $item,
        ]);
    }
    public static function updateItemPrice(PDO $db, int $item, float $price) {
        $stmt = $db->prepare("UPDATE tbl_items SET price=:price WHERE item_id = :item");
        $stmt->execute([
            ':item' => $item,
            ':price'=>$price
        ]);
    }

    public static function insertItemStock($db, $item, $qty, $date) {
        $stmt = $db->prepare("INSERT INTO tbl_item_stock (item, qty, date_tkn) VALUES (:item, :qty, :date)");
        $stmt->execute([
            ':item' => $item,
            ':qty' => $qty,
            ':date' => $date
        ]);

    }
    
    public static function log_stock($db, $item, $qty, $price) {
        try {
            // Get the last stock entry for the item
            $sql_chk = $db->prepare("SELECT end_qty FROM tbl_progress WHERE item = :item ORDER BY prog_id DESC LIMIT 1");
            $sql_chk->execute([':item' => $item]);
    
            $lastqty = 0;
            if ($row = $sql_chk->fetch(PDO::FETCH_ASSOC)) {
                $lastqty = (float)$row['end_qty'];
            }
    
            $endQty = $lastqty + $qty;
            $date = date('Y-m-d H:i:s'); // Use current timestamp
    
            // Insert new stock log
            $stmt = $db->prepare("INSERT INTO tbl_progress (date, in_qty, last_qty, item, end_qty, new_price) 
                                  VALUES (:date, :in_qty, :last_qty, :item, :end_qty, :price)");
            $stmt->execute([
                ':date'     => $date,
                ':in_qty'   => $qty,
                ':last_qty' => $lastqty,
                ':item'     => $item,
                ':end_qty'  => $endQty,
                ':price'    => $price
            ]);
            // Update item stock
             self::updateItemPrice($db, $item, $price);
           return true;
        } catch (PDOException $e) {
            // Optional: log error or rethrow
            echo "Error logging stock: " . $e->getMessage();
        }
    }
    public static function log_stock_out($db, $item, $qty, $remark = "issued") {
        try {
            // Get the last stock entry for the item
            $sql_chk = $db->prepare("SELECT end_qty FROM tbl_progress WHERE item = :item ORDER BY prog_id DESC LIMIT 1");
            $sql_chk->execute([':item' => $item]);
    
            $lastqty = 0;
            if ($row = $sql_chk->fetch(PDO::FETCH_ASSOC)) {
                $lastqty = (float)$row['end_qty'];
            }
    
            $endQty = $lastqty - $qty;
            $date   = date('Y-m-d H:i:s');
            $price  = self::getUnitePrice($db, $item); // Assuming this returns unit price
    
            // Insert stock out log
            $stmt = $db->prepare("
                INSERT INTO tbl_progress (date, out_qty, last_qty, item, end_qty, new_price, remark) 
                VALUES (:date, :out_qty, :last_qty, :item, :end_qty, :price, :remark)
            ");
            $stmt->execute([
                ':date'     => $date,
                ':out_qty'  => $qty,
                ':last_qty' => $lastqty,
                ':item'     => $item,
                ':end_qty'  => $endQty,
                ':price'    => $price,
                ':remark'   => $remark
            ]);
    
        } catch (PDOException $e) {
            echo "Error logging stock: " . $e->getMessage();
        }
    }
    
    
    public static function getNewStockItems($db) {
        $sql = "
            SELECT * FROM tbl_items i
            WHERE NOT EXISTS (
                SELECT 1 FROM tbl_item_stock s
                WHERE s.item = i.item_id
            )
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function recordDamagedItem(PDO $db, array $post, string $remark) {
        $item    = $post['item'];
        $qty     = $post['qty'];
        $comment = $post['comment'];
        $date    = $post['date']; // Expected format: Y-m-d or Y-m-d H:i:s
    
        try {
            // Check current stock
            $checkStmt = $db->prepare("SELECT qty FROM tbl_item_stock WHERE item = :item");
            $checkStmt->execute([':item' => $item]);
    
            if ($checkStmt->rowCount() === 0) {
                throw new Exception("Item not found in stock.");
            }
    
            $stock = $checkStmt->fetch(PDO::FETCH_ASSOC);
            $currentQty = (float) $stock['qty'];
    
            if ($qty > $currentQty) {
                throw new Exception("Insufficient stock to mark as damaged.");
            }
    
            // Update item stock
            $newQty = $currentQty - $qty;
            $updateStmt = $db->prepare("UPDATE tbl_item_stock SET qty = :qty WHERE item = :item");
            $updateStmt->execute([
                ':qty'  => $newQty,
                ':item' => $item
            ]);
    
            // Insert into damaged table
            $insertStmt = $db->prepare("
                INSERT INTO damaged (item_id, qty, message, created_at)
                VALUES (:item_id, :qty, :message, :created_at)
            ");
            $insertStmt->execute([
                ':item_id'    => $item,
                ':qty'        => $qty,
                ':message'    => $comment,
                ':created_at' => $date
            ]);
    
            // Log stock out movement
            self::log_stock_out($db, $item, $qty, $remark);
    
            return ['success' => true, 'message' => 'Damaged item recorded successfully.'];
    
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    }