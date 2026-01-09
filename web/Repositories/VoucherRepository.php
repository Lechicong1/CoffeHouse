<?php
require_once __DIR__ . '/../Entity/VoucherEntity.php';
use web\Entity\VoucherEntity;

class VoucherRepository extends ConnectDatabase {

    public function findAll() {
        $sql = "SELECT * FROM vouchers ORDER BY `id` DESC";
        $result = mysqli_query($this->con, $sql);

        $vouchers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vouchers[] = new VoucherEntity($row);
        }

        return $vouchers;
    }

    public function findById($id) {
        $sql = "SELECT * FROM vouchers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new VoucherEntity($data) : null;
    }

    public function findByName($name, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM vouchers WHERE name = ? AND id != ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "si", $name, $excludeId);
        } else {
            $sql = "SELECT * FROM vouchers WHERE name = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $name);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new VoucherEntity($data) : null;
    }

    public function search($keyword) {
        $sql = "SELECT * FROM vouchers 
                WHERE name LIKE ? 
                OR discount_type LIKE ?
                ORDER BY name";

        $stmt = mysqli_prepare($this->con, $sql);
        $searchTerm = "%$keyword%";
        mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $vouchers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vouchers[] = new VoucherEntity($row);
        }

        return $vouchers;
    }

    public function create($voucher) {
        $sql = "INSERT INTO vouchers (name, point_cost, discount_type, discount_value, 
                max_discount_value, min_bill_total, start_date, end_date, quantity, 
                used_count, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sisdddssiii",
            $voucher->name,
            $voucher->point_cost,
            $voucher->discount_type,
            $voucher->discount_value,
            $voucher->max_discount_value,
            $voucher->min_bill_total,
            $voucher->start_date,
            $voucher->end_date,
            $voucher->quantity,
            $voucher->used_count,
            $voucher->is_active
        );

        return mysqli_stmt_execute($stmt);
    }

    public function update($voucher) {
        $sql = "UPDATE vouchers 
                SET name = ?, point_cost = ?, discount_type = ?, discount_value = ?,
                    max_discount_value = ?, min_bill_total = ?, start_date = ?, 
                    end_date = ?, quantity = ?, used_count = ?, is_active = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sisdddssiiii",
            $voucher->name,
            $voucher->point_cost,
            $voucher->discount_type,
            $voucher->discount_value,
            $voucher->max_discount_value,
            $voucher->min_bill_total,
            $voucher->start_date,
            $voucher->end_date,
            $voucher->quantity,
            $voucher->used_count,
            $voucher->is_active,
            $voucher->id
        );

        return mysqli_stmt_execute($stmt);
    }

    public function delete($id) {
        $sql = "DELETE FROM vouchers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    public function findActiveVouchers() {
        $sql = "SELECT * FROM vouchers 
                WHERE is_active = 1 
                AND (end_date IS NULL OR end_date >= CURDATE())
                AND (quantity IS NULL OR quantity > used_count)
                ORDER BY point_cost ASC";
        
        $result = mysqli_query($this->con, $sql);

        $vouchers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vouchers[] = new VoucherEntity($row);
        }

        return $vouchers;
    }

    /**
     * Tự động set is_active = 0 cho các voucher đã hết hạn
     * @return int Số voucher đã deactivate
     */
    public function deactivateExpiredVouchers() {
        $sql = "UPDATE vouchers 
                SET is_active = 0 
                WHERE is_active = 1 
                AND end_date IS NOT NULL 
                AND end_date < CURDATE()";
        
        mysqli_query($this->con, $sql);
        return mysqli_affected_rows($this->con);
    }
}
?>
