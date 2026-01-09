<?php
include_once './web/Entity/EmployeeEntity.php';
use web\Entity\EmployeeEntity;
class ReportRepo extends ConnectDatabase {
    public function getSalaryEmployeeExpense() {
        $sql = "SELECT SUM(luong) as total_salary FROM employee WHERE roleName IN ('ORDER', 'BARTENDER', 'SHIPPER')";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total_salary'] ?? 0;
    }

    public function getInventoryExpense($fromDate, $toDate) {
        $sql = "SELECT SUM(total_cost) as total_cost FROM inventory_imports WHERE import_date BETWEEN ? AND ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $fromDate, $toDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['total_cost'] ?? 0;
    }
    public function getTotalExpenseProduct($fromDate, $toDate) {
        $sql = "SELECT SUM(quantity*price_at_purchase) as total_revenue FROM order_items ot
        JOIN orders o ON o.id = ot.order_id 
        WHERE o.created_at BETWEEN ? AND ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $fromDate, $toDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['total_revenue'] ?? 0;
    }
    public function getDetailRevenue($fromDate, $toDate) {
        $sql = "select p.name as productName ,c.name as categoryName,
         sum(oi.quantity) as totalQuantitySold,sum(oi.quantity * ps.price) as totalRevenue
         from orders o
         join order_items oi on o.id = oi.order_id
         join product_sizes ps on ps.id = oi.product_size_id 
         join products p on p.id= ps.product_id
         join categories c on c.id = p.category_id
         where o.created_at BETWEEN ? AND ?
         group by p.id ";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $fromDate, $toDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}

?>
