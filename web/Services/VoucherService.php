<?php
require_once __DIR__ . '/../Entity/VoucherEntity.php';
use web\Entity\VoucherEntity;

class VoucherService extends Service {

    public function syncExpiryStatuses() {
        return $this->repository('VoucherRepository')->deactivateExpiredVouchers();
    }

    public function getAllVouchers() {
        return $this->repository('VoucherRepository')->findAll();
    }

    public function getVoucherById($id) {
        return $this->repository('VoucherRepository')->findById($id);
    }

    public function searchVouchers($keyword) {
        return $this->repository('VoucherRepository')->search($keyword);
    }

    public function createVoucher($data) {
        $validation = $this->validateVoucherData($data);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        $repository = $this->repository('VoucherRepository');

        if ($repository->findByName($data['name'])) {
            return ['success' => false, 'message' => 'Tên voucher đã tồn tại!'];
        }

        $voucher = new VoucherEntity([
            'name' => $data['name'],
            'point_cost' => (int)$data['point_cost'],
            'discount_type' => $data['discount_type'],
            'discount_value' => (float)$data['discount_value'],
            'max_discount_value' => ($data['discount_type'] === 'PERCENT' && !empty($data['max_discount_value'])) ? (float)$data['max_discount_value'] : null,
            'min_bill_total' => !empty($data['min_bill_total']) ? (float)$data['min_bill_total'] : 0,
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
            'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
            'quantity' => !empty($data['quantity']) ? (int)$data['quantity'] : null,
            'used_count' => 0,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);

        $result = $repository->create($voucher);
        return $result 
            ? ['success' => true, 'message' => 'Thêm voucher thành công!']
            : ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm voucher!'];
    }

    public function updateVoucher($id, $data) {
        $repository = $this->repository('VoucherRepository');
        $voucher = $repository->findById($id);
        
        if (!$voucher) {
            return ['success' => false, 'message' => 'Không tìm thấy voucher!'];
        }

        $validation = $this->validateVoucherData($data, $id);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        if ($repository->findByName($data['name'], $id)) {
            return ['success' => false, 'message' => 'Tên voucher đã tồn tại!'];
        }

        $voucher->name = $data['name'];
        $voucher->point_cost = (int)$data['point_cost'];
        $voucher->discount_type = $data['discount_type'];
        $voucher->discount_value = (float)$data['discount_value'];
        $voucher->max_discount_value = ($data['discount_type'] === 'PERCENT' && !empty($data['max_discount_value'])) ? (float)$data['max_discount_value'] : null;
        $voucher->min_bill_total = !empty($data['min_bill_total']) ? (float)$data['min_bill_total'] : 0;
        $voucher->start_date = !empty($data['start_date']) ? $data['start_date'] : null;
        $voucher->end_date = !empty($data['end_date']) ? $data['end_date'] : null;
        $voucher->quantity = !empty($data['quantity']) ? (int)$data['quantity'] : null;
        $voucher->is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        $result = $repository->update($voucher);
        return $result 
            ? ['success' => true, 'message' => 'Cập nhật voucher thành công!']
            : ['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật voucher!'];
    }

    public function previewApplyVoucher($customerId, $voucherId, $totalAmount) {
        $customerRepo = $this->repository('CustomerRepository');
        $voucherRepo  = $this->repository('VoucherRepository');
        $customer = null;
        $customerPoints = 0;

        if (!empty($customerId)) {
            $customer = $customerRepo->findById($customerId);
            if ($customer) $customerPoints = (int)$customer->points;
        }

        $voucher = $voucherRepo->findById($voucherId);
        if (!$voucher) {
            return ['success' => false, 'message' => 'Không tìm thấy voucher'];
        }

        if ((int)$voucher->is_active !== 1) {
            return ['success' => false, 'message' => 'Voucher không hoạt động'];
        }

        $today = date('Y-m-d');
        if ($voucher->start_date && strtotime($today) < strtotime($voucher->start_date)) {
            return ['success' => false, 'message' => 'Voucher chưa bắt đầu'];
        }
        if ($voucher->end_date && strtotime($today) > strtotime($voucher->end_date)) {
            return ['success' => false, 'message' => 'Voucher đã hết hạn'];
        }

        if (!is_null($voucher->quantity) && $voucher->used_count >= $voucher->quantity) {
            return ['success' => false, 'message' => 'Voucher đã hết lượt sử dụng'];
        }

        if ($totalAmount < $voucher->min_bill_total) {
            $minBill = number_format($voucher->min_bill_total, 0, ',', '.');
            return ['success' => false, 'message' => "Đơn hàng tối thiểu {$minBill}đ"];
        }

        if ((int)$voucher->point_cost > 0 && $customerPoints < (int)$voucher->point_cost) {
            return ['success' => false, 'message' => 'Không đủ điểm để đổi voucher'];
        }
        
        $discount = $this->calculateDiscount($voucher, $totalAmount);
        $totalAfter = max(0, $totalAmount - $discount);

        return [
            'success' => true,
            'discount_amount' => (float)$discount,
            'total_after' => (float)$totalAfter,
            'voucher' => [
                'id' => $voucher->id,
                'name' => $voucher->name,
                'point_cost' => $voucher->point_cost
            ]
        ];
    }

    public function previewVoucher($customerId, $voucherId, $totalAmount) {
        return $this->previewApplyVoucher($customerId, $voucherId, $totalAmount);
    }

    public function redeemVoucher($customerId, $voucherId, $billTotal, $con = null) {
        $voucherRepo = $this->repository('VoucherRepository');
        $customerRepo = $this->repository('CustomerRepository');
        
        if ($con) {
            $voucherRepo->con = $con;
            $customerRepo->con = $con;
        }

        $voucher = $voucherRepo->findById($voucherId);
        if (!$voucher) {
            return ['success' => false, 'code' => 'VOUCHER_NOT_FOUND', 'message' => 'Không tìm thấy voucher', 'discount_amount' => 0];
        }

        if ((int)$voucher->is_active !== 1) {
            return ['success' => false, 'code' => 'VOUCHER_INACTIVE', 'message' => 'Voucher không còn hoạt động', 'discount_amount' => 0];
        }

        $today = date('Y-m-d');
        if ($voucher->start_date && strtotime($today) < strtotime($voucher->start_date)) {
            return ['success' => false, 'code' => 'VOUCHER_NOT_STARTED', 'message' => 'Voucher chưa bắt đầu', 'discount_amount' => 0];
        }

        if ($voucher->end_date && strtotime($today) > strtotime($voucher->end_date)) {
            return ['success' => false, 'code' => 'VOUCHER_EXPIRED', 'message' => 'Voucher đã hết hạn', 'discount_amount' => 0];
        }

        if (!is_null($voucher->quantity) && $voucher->used_count >= $voucher->quantity) {
            return ['success' => false, 'code' => 'VOUCHER_OUT_OF_STOCK', 'message' => 'Voucher đã hết lượt sử dụng', 'discount_amount' => 0];
        }

        if ($billTotal < $voucher->min_bill_total) {
            $minBill = number_format($voucher->min_bill_total, 0, ',', '.');
            return ['success' => false, 'code' => 'MIN_BILL_NOT_MET', 'message' => "Đơn hàng tối thiểu {$minBill}đ", 'discount_amount' => 0];
        }

        $pointsUsed = (int)$voucher->point_cost;
        $customerPoints = 0;
        $customer = null;
        
        if ($customerId) {
            $customer = $customerRepo->findById($customerId);
            if ($customer) {
                $customerPoints = (int)$customer->points;
            }
        }

        if ($pointsUsed > 0 && $customerPoints < $pointsUsed) {
            return ['success' => false, 'code' => 'NOT_ENOUGH_POINTS', 'message' => 'Không đủ điểm để đổi voucher', 'discount_amount' => 0];
        }

        $discountAmount = $this->calculateDiscount($voucher, $billTotal);

        if (!$voucherRepo->incrementUsedCount($voucherId, $con)) {
            return ['success' => false, 'code' => 'UPDATE_VOUCHER_FAILED', 'message' => 'Không thể cập nhật voucher', 'discount_amount' => 0];
        }

        if ($pointsUsed > 0 && $customer) {
            $newPoints = max(0, $customerPoints - $pointsUsed);
            if (!$customerRepo->updatePoints($customerId, $newPoints)) {
                return ['success' => false, 'code' => 'UPDATE_POINTS_FAILED', 'message' => 'Không thể cập nhật điểm khách hàng', 'discount_amount' => 0];
            }
        }

        return [
            'success' => true,
            'code' => 'OK',
            'message' => 'Đổi voucher thành công',
            'discount_amount' => (float)$discountAmount,
            'voucher_name' => $voucher->name,
            'points_used' => $pointsUsed
        ];
    }

    public function deleteVoucher($id) {
        $repository = $this->repository('VoucherRepository');
        $voucher = $repository->findById($id);
        
        if (!$voucher) {
            return ['success' => false, 'message' => 'Không tìm thấy voucher!'];
        }

        $result = $repository->delete($id);
        return $result 
            ? ['success' => true, 'message' => 'Xóa voucher thành công!']
            : ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa voucher!'];
    }

    public function getEligibleVouchers($customerId = null, $billTotal = 0) {
        $voucherRepo = $this->repository('VoucherRepository');
        $vouchers = $voucherRepo->findActiveVouchers();

        $customerPoints = 0;
        if ($customerId) {
            $custRepo = $this->repository('CustomerRepository');
            $cust = $custRepo->findById($customerId);
            if ($cust) $customerPoints = (int)$cust->points;
        }

        $eligible = [];
        foreach ($vouchers as $v) {
            if ($v->point_cost > $customerPoints) continue;
            if ($billTotal < $v->min_bill_total) continue;
            $eligible[] = $v;
        }

        return $eligible;
    }

    public function calculateDiscount($v, $billTotal) {
        if ($billTotal < $v->min_bill_total) return 0;

        $discount = $v->discount_type === 'FIXED' 
            ? (float)$v->discount_value 
            : $billTotal * ((float)$v->discount_value / 100.0);

        if ($v->discount_type === 'PERCENT' && !empty($v->max_discount_value)) {
            $discount = min($discount, (float)$v->max_discount_value);
        }
        return (int)round(min($discount, $billTotal));
    }

    private function validateVoucherData($data, $id = null) {
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            return ['valid' => false, 'message' => 'Tên voucher phải có ít nhất 3 ký tự!'];
        }
        if (!isset($data['point_cost']) || $data['point_cost'] < 0) {
            return ['valid' => false, 'message' => 'Điểm đổi phải là số không âm!'];
        }
        if (empty($data['discount_type']) || !in_array($data['discount_type'], ['FIXED', 'PERCENT'])) {
            return ['valid' => false, 'message' => 'Loại giảm giá không hợp lệ!'];
        }
        if (!isset($data['discount_value']) || $data['discount_value'] <= 0) {
            return ['valid' => false, 'message' => 'Giá trị giảm phải lớn hơn 0!'];
        }
        if ($data['discount_type'] === 'PERCENT' && $data['discount_value'] > 100) {
            return ['valid' => false, 'message' => 'Phần trăm giảm không được vượt quá 100%!'];
        }
        if (!empty($data['start_date']) && !empty($data['end_date']) && strtotime($data['end_date']) < strtotime($data['start_date'])) {
            return ['valid' => false, 'message' => 'Ngày kết thúc phải sau ngày bắt đầu!'];
        }
        if (!empty($data['quantity']) && $data['quantity'] < 0) {
            return ['valid' => false, 'message' => 'Số lượng không được âm!'];
        }
        return ['valid' => true, 'message' => ''];
    }
}

