CREATE TABLE `tbl_stocktake` (
  `stocktake_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `system_qty` decimal(10,3) NOT NULL DEFAULT 0.000,
  `counted_qty` decimal(10,3) NOT NULL DEFAULT 0.000,
  `variance` decimal(10,3) NOT NULL DEFAULT 0.000,
  `stocktake_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `stocktake_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`stocktake_id`),
  KEY `item_id` (`item_id`),
  KEY `stocktake_by` (`stocktake_by`),
  CONSTRAINT `tbl_stocktake_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `tbl_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_stocktake_ibfk_2` FOREIGN KEY (`stocktake_by`) REFERENCES `tbl_users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;