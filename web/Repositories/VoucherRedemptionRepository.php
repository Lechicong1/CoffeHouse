<?php
require_once __DIR__ . '/../Entity/VoucherRedemptionEntity.php';
use web\Entity\VoucherRedemptionEntity;

class VoucherRedemptionRepository extends ConnectDatabase {

    /**
     * Chèn một bản ghi redemption (ghi lịch sử đổi voucher)
     */
    public function insertRedemption($customerId, $voucherId, $orderId, $pointsUsed, $discountAmount) {
        $sql = "INSERT INTO voucher_redemptions (customer_id, voucher_id, order_id, points_used, discount_amount, created_at, status) VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = mysqli_prepare($this->con, $sql);
        $status = 'REDEEMED';
        mysqli_stmt_bind_param($stmt, "iiiids", $customerId, $voucherId, $orderId, $pointsUsed, $discountAmount, $status);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy lịch sử đổi voucher của một khách hàng
     * @return VoucherRedemptionEntity[]
     */
    public function getByCustomerId($customerId, $limit = 100, $offset = 0) {
        $sql = "SELECT * FROM voucher_redemptions WHERE customer_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $customerId, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = new VoucherRedemptionEntity($r);
        }
        return $rows;
    }

    /**
     * Tìm redemption theo id
     */
    public function findById($id) {
        $sql = "SELECT * FROM voucher_redemptions WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        return $row ? new VoucherRedemptionEntity($row) : null;
    }

    /**
     * Đếm số lần redemption theo voucher
     */
    public function countByVoucherId($voucherId) {
        $sql = "SELECT COUNT(*) as total FROM voucher_redemptions WHERE voucher_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $voucherId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        return (int)$row['total'];
    }

    /**
     * Tổng tiền giảm (discount) theo voucher
     */
    public function sumDiscountByVoucherId($voucherId) {
        $sql = "SELECT COALESCE(SUM(discount_amount),0) as total FROM voucher_redemptions WHERE voucher_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $voucherId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        return (float)$row['total'];
    }

    /**
     * Tổng điểm đã đổi theo voucher
     */
    public function sumPointsByVoucherId($voucherId) {
        $sql = "SELECT COALESCE(SUM(points_used),0) as total FROM voucher_redemptions WHERE voucher_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $voucherId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        return (int)$row['total'];
    }

    /**
     * Lấy các redemption mới nhất (tổng quan)
     */
    public function getRecentRedemptions($limit = 50, $offset = 0) {
        $sql = "SELECT * FROM voucher_redemptions ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = new VoucherRedemptionEntity($r);
        }
        return $rows;
    }

    /**
     * Giảm used_count của voucher một cách an toàn (không xuống dưới 0)
     */
    public function decrementVoucherUsedCount($voucherId) {
        $sql = "UPDATE vouchers SET used_count = GREATEST(0, used_count - 1) WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $voucherId);
        return mysqli_stmt_execute($stmt);
    }
}

?>
