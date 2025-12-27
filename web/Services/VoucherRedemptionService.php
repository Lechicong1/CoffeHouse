<?php
require_once __DIR__ . '/../Entity/VoucherRedemptionEntity.php';
require_once __DIR__ . '/../Repositories/VoucherRedemptionRepository.php';

class VoucherRedemptionService extends Service {

    /**
     * Ghi một redemption (gói wrapper gọi repository để chèn)
     */
    public function recordRedemption($customerId, $voucherId, $orderId, $pointsUsed, $discountAmount) {
        $repo = $this->repository('VoucherRedemptionRepository');
        $ok = $repo->insertRedemption($customerId, $voucherId, $orderId, $pointsUsed, $discountAmount);
        return $ok ? ['success' => true] : ['success' => false, 'message' => 'Không thể ghi redemption'];
    }

    /**
     * Redeem voucher theo kiểu nguyên tử: khoá các bản ghi, kiểm tra lại, cập nhật used_count voucher,
     * cập nhật điểm khách và chèn bản ghi redemption trong cùng một transaction.
     * @param int $customerId
     * @param int $voucherId
     * @param int $orderId
     * @param int $pointsUsed
     * @param float $discountAmount
     * @param float|null $billTotal (tùy chọn) tổng hoá đơn để tính lại discount
     * @return array
     */
    public function redeemAtomic($customerId, $voucherId, $orderId, $pointsUsed, $discountAmount, $billTotal = null, $externalCon = null, $manageTransaction = true) {
        $voucherRepo = $this->repository('VoucherRepository');
        $repoCon = $voucherRepo->con;
        $con = $externalCon ?? $repoCon;

        // Nếu caller yêu cầu service tự quản transaction, bắt đầu ở đây
        if ($manageTransaction) {
            if (!mysqli_begin_transaction($con)) {
                return ['success' => false, 'message' => 'Không thể bắt đầu transaction'];
            }
        }

        try {
            // Khoá hàng voucher để tránh race-condition
            $sql = "SELECT * FROM vouchers WHERE id = ? FOR UPDATE";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $voucherId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $vrow = mysqli_fetch_assoc($res);
            if (!$vrow) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Voucher không tồn tại'];
            }

            require_once __DIR__ . '/VoucherService.php';
            $voucherEntity = new \web\Entity\VoucherEntity($vrow);

            // Kiểm tra lại điều kiện áp dụng voucher
            if (!$voucherEntity->is_active) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Voucher không còn hiệu lực'];
            }
            $today = date('Y-m-d');
            if (!empty($voucherEntity->start_date) && strtotime($today) < strtotime($voucherEntity->start_date)) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Voucher chưa bắt đầu'];
            }
            if (!empty($voucherEntity->end_date) && strtotime($today) > strtotime($voucherEntity->end_date)) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Voucher đã hết hạn'];
            }
            if (!is_null($voucherEntity->quantity) && $voucherEntity->used_count >= $voucherEntity->quantity) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Voucher đã hết lượt'];
            }

            // Khoá hàng customer để cập nhật điểm an toàn
            $sql = "SELECT * FROM customers WHERE id = ? FOR UPDATE";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customerId);
            mysqli_stmt_execute($stmt);
            $cres = mysqli_stmt_get_result($stmt);
            $crow = mysqli_fetch_assoc($cres);
            if (!$crow) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Customer không tồn tại'];
            }
            $customerPoints = (int)$crow['points'];
            // Kiểm tra pointsUsed phải khớp với point_cost của voucher
            if ((int)$pointsUsed !== (int)$voucherEntity->point_cost) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'PointsUsed không hợp lệ'];
            }

            if ($customerPoints < $pointsUsed) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Không đủ điểm'];
            }

            // Nếu có billTotal thì xác thực lại discount do client gửi
            if (!is_null($billTotal)) {
                $vs = new VoucherService();
                $expected = $vs->calculateDiscount($voucherEntity, $billTotal);
                if ((int)round($expected) !== (int)round($discountAmount)) {
                    mysqli_rollback($con);
                    return ['success' => false, 'message' => 'Discount không khớp với bill thực tế'];
                }
            }

            // Cập nhật used_count của voucher
            $sql = "UPDATE vouchers SET used_count = used_count + 1 WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $voucherId);
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Không thể cập nhật voucher'];
            }

            // Cập nhật điểm tích luỹ của khách
            $newPoints = max(0, $customerPoints - $pointsUsed);
            $sql = "UPDATE customers SET points = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $newPoints, $customerId);
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Không thể cập nhật điểm khách hàng'];
            }

            // Chèn bản ghi lịch sử redeem vào `voucher_redemptions`
            $sql = "INSERT INTO voucher_redemptions (customer_id, voucher_id, order_id, points_used, discount_amount, created_at, status) VALUES (?, ?, ?, ?, ?, NOW(), 'REDEEMED')";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "iiiid", $customerId, $voucherId, $orderId, $pointsUsed, $discountAmount);
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Không thể ghi lịch sử đổi voucher'];
            }

            // Commit transaction nếu service tự quản transaction
            if ($manageTransaction) mysqli_commit($con);
            return ['success' => true, 'message' => 'Redeemed'];

        } catch (Exception $e) {
            // Rollback khi có lỗi
            if ($manageTransaction) mysqli_rollback($con);
            return ['success' => false, 'message' => 'Lỗi khi redeem: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy lịch sử dùng voucher của 1 customer
     */
    public function getHistoryByCustomer($customerId, $limit = 100, $offset = 0) {
        $repo = $this->repository('VoucherRedemptionRepository');
        $items = $repo->getByCustomerId($customerId, $limit, $offset);
        $out = [];
        foreach ($items as $it) {
            if (method_exists($it, 'toArray')) $out[] = $it->toArray(); else $out[] = $it;
        }
        return $out;
    }

    /**
     * Thống kê theo voucher
     */
    public function getStatsByVoucher($voucherId) {
        $repo = $this->repository('VoucherRedemptionRepository');
        $count = $repo->countByVoucherId($voucherId);
        $totalDiscount = $repo->sumDiscountByVoucherId($voucherId);
        $totalPoints = $repo->sumPointsByVoucherId($voucherId);
        return [
            'voucher_id' => $voucherId,
            'uses' => $count,
            'total_discount' => $totalDiscount,
            'total_points' => $totalPoints
        ];
    }

    /**
     * Lấy danh sách voucher top theo số lần sử dụng
     */
    public function getTopVouchers($limit = 10) {
        $sql = "SELECT voucher_id, COUNT(*) as uses, COALESCE(SUM(discount_amount),0) as total_discount
                FROM voucher_redemptions
                GROUP BY voucher_id
                ORDER BY uses DESC
                LIMIT " . intval($limit);

        $repo = $this->repository('VoucherRedemptionRepository');
        // Sử dụng kết nối DB từ repository
        $con = $repo->con;
        $res = mysqli_query($con, $sql);
        $rows = [];
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
        return $rows;
    }
}

?>
