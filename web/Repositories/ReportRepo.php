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
}

?>
