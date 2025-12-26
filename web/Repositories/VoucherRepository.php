<?php
require_once __DIR__ . '/../Entity/VoucherEntity.php';
use web\Entity\VoucherEntity;

class VoucherRepository extends ConnectDatabase {

    /**
     * Lấy tất cả voucher
     * @return array
     */
    public function findAll() {
        $sql = "SELECT * FROM vouchers ORDER BY `id` DESC";
        $result = mysqli_query($this->con, $sql);

        $vouchers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vouchers[] = new VoucherEntity($row);
        }

        return $vouchers;
    }

    /**
     * Lấy voucher theo ID
     * @param int $id
     * @return VoucherEntity|null
     */
    public function findById($id) {
        $sql = "SELECT * FROM vouchers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new VoucherEntity($data) : null;
    }

    /**
     * Lấy voucher theo tên
     * @param string $name
     * @param int|null $excludeId ID voucher cần loại trừ (khi update)
     * @return VoucherEntity|null
     */
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

    /**
     * Tìm kiếm voucher
     * @param string $keyword
     * @return array
     */
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

    /**
     * Tạo voucher mới
     * @param VoucherEntity $voucher
     * @return bool
     */
    public function create($voucher) {
        $sql = "INSERT INTO vouchers (name, point_cost, discount_type, discount_value, 
                max_discount_value, min_bill_total, start_date, end_date, quantity, 
                used_count, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sisdddsssii",
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

    /**
     * Cập nhật voucher
     * @param VoucherEntity $voucher
     * @return bool
     */
    public function update($voucher) {
        $sql = "UPDATE vouchers 
                SET name = ?, point_cost = ?, discount_type = ?, discount_value = ?,
                    max_discount_value = ?, min_bill_total = ?, start_date = ?, 
                    end_date = ?, quantity = ?, used_count = ?, is_active = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sisdddsssiii",
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

    /**
     * Xóa voucher
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM vouchers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Đếm tổng số voucher
     * @return int
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM vouchers";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }

    /**
     * Đếm voucher đang hoạt động
     * @return int
     */
    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM vouchers WHERE is_active = 1";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }

    /**
     * Lấy voucher đang còn hiệu lực
     * @return array
     */
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
}
?>
