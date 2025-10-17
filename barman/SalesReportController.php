<?php
require_once '../inc/conn.php';

class SalesReportController {
    private $db;
    private $from;
    private $to;
    private $productId;

    public function __construct($db) {
        $this->db = $db;
        $this->initializeDates();
    }

    private function initializeDates() {
        if(!isset($_SESSION['date_from']) && !isset($_SESSION['date_to'])){
            $this->from = date('Y-m-d');
            $this->to = date("Y-m-d");
        } else {
            $this->from = $_SESSION['date_from'];
            $this->to = $_SESSION['date_to'];
        }

        if(isset($_SESSION['product_id'])) {
            $this->productId = $_SESSION['product_id'];
        }
    }

    public function handleRequest() {
        if(isset($_POST['check'])) {
            $this->handleDaySelect();
        } 
        elseif(isset($_POST['filter'])) {
            $this->handleFilter();
        }
    }

    private function handleDaySelect() {
        $to = $_POST['selectday'];
        $_SESSION['selected_day'] = $to;

        $stmt = $this->db->prepare("SELECT opened_at FROM days WHERE DATE(opened_at) = ?");
        $stmt->execute([$to]);
        
        if($row = $stmt->fetch()) {
            $this->from = $row['opened_at'];
        }

        $stmt = $this->db->prepare("SELECT closed_at FROM days WHERE DATE(opened_at) = ?");
        $stmt->execute([$to]);
        
        if($row = $stmt->fetch()) {
            $this->to = $row['closed_at'];
        }
    }

    private function handleFilter() {
        $_SESSION['date_from'] = $this->from = $_POST['date_from'];
        $_SESSION['date_to'] = $this->to = $_POST['date_to'];
        $_SESSION['product_id'] = $this->productId = $_POST['product_id'] ?? null;
    }

    public function getSalesData() {
        $query = "SELECT *, SUM(cmd_qty) AS totqty FROM `tbl_cmd_qty`
                 INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                 WHERE DATE(tbl_cmd_qty.created_at) BETWEEN ? AND ? 
                 AND menu.cat_id = '2'";
        
        $params = [$this->from, date('Y-m-d', strtotime($this->to . ' +1 day'))];

        if($this->productId) {
            $query .= " AND menu.menu_id = ?";
            $params[] = $this->productId;
        }

        $query .= " GROUP BY cmd_item";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableDays() {
        $stmt = $this->db->prepare("SELECT opened_at as date FROM days");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableProducts() {
        $stmt = $this->db->prepare("SELECT * FROM menu WHERE cat_id = '2'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFromDate() { return $this->from; }
    public function getToDate() { return $this->to; }
    public function getProductId() { return $this->productId; }
}