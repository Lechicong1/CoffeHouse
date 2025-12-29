<?php
require_once __DIR__ . '/../Entity/VoucherEntity.php';
use web\Entity\VoucherEntity;

class VoucherService extends Service {

    /**
     * Lấy tất cả voucher
     * @return array
     */
    public function getAllVouchers() {
        $repository = $this->repository('VoucherRepository');
        return $repository->findAll();
    }

    /**
     * Lấy voucher theo ID
     * @param int $id
     * @return VoucherEntity|null
     */
    public function getVoucherById($id) {
        $repository = $this->repository('VoucherRepository');
        return $repository->findById($id);
    }

    /**
     * Tìm kiếm voucher
     * @param string $keyword
     * @return array
     */
    public function searchVouchers($keyword) {
        $repository = $this->repository('VoucherRepository');
        return $repository->search($keyword);
    }

    /**
     * Tạo voucher mới
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function createVoucher($data) {
        // Validate dữ liệu
        $validation = $this->validateVoucherData($data);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        $repository = $this->repository('VoucherRepository');

        // Kiểm tra tên voucher đã tồn tại
        $existingVoucher = $repository->findByName($data['name']);
        if ($existingVoucher) {
            return [
                'success' => false,
                'message' => 'Tên voucher đã tồn tại!'
            ];
        }

        // Tạo entity
        $voucher = new VoucherEntity([
            'name' => $data['name'],
            'point_cost' => (int)$data['point_cost'],
            'discount_type' => $data['discount_type'],
            'discount_value' => (float)$data['discount_value'],
            'max_discount_value' => !empty($data['max_discount_value']) ? (float)$data['max_discount_value'] : null,
            'min_bill_total' => !empty($data['min_bill_total']) ? (float)$data['min_bill_total'] : 0,
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
            'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
            'quantity' => !empty($data['quantity']) ? (int)$data['quantity'] : null,
            'used_count' => 0,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);

        // Lưu vào database
        $result = $repository->create($voucher);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Thêm voucher thành công!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm voucher!'
            ];
        }
    }

    /**
     * Cập nhật voucher
     * @param int $id
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateVoucher($id, $data) {
        $repository = $this->repository('VoucherRepository');

        // Kiểm tra voucher tồn tại
        $voucher = $repository->findById($id);
        if (!$voucher) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy voucher!'
            ];
        }

        // Validate dữ liệu
        $validation = $this->validateVoucherData($data, $id);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        // Kiểm tra tên voucher đã tồn tại (trừ voucher hiện tại)
        $existingVoucher = $repository->findByName($data['name'], $id);
        if ($existingVoucher) {
            return [
                'success' => false,
                'message' => 'Tên voucher đã tồn tại!'
            ];
        }

        // Cập nhật thông tin
        $voucher->name = $data['name'];
        $voucher->point_cost = (int)$data['point_cost'];
        $voucher->discount_type = $data['discount_type'];
        $voucher->discount_value = (float)$data['discount_value'];
        $voucher->max_discount_value = !empty($data['max_discount_value']) ? (float)$data['max_discount_value'] : null;
        $voucher->min_bill_total = !empty($data['min_bill_total']) ? (float)$data['min_bill_total'] : 0;
        $voucher->start_date = !empty($data['start_date']) ? $data['start_date'] : null;
        $voucher->end_date = !empty($data['end_date']) ? $data['end_date'] : null;
        $voucher->quantity = !empty($data['quantity']) ? (int)$data['quantity'] : null;
        $voucher->used_count = isset($data['used_count']) ? (int)$data['used_count'] : $voucher->used_count;
        $voucher->is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        // Lưu vào database
        $result = $repository->update($voucher);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật voucher thành công!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật voucher!'
            ];
        }
    }

    /**
     * Xem trước áp dụng voucher
     * @param int $customerId
     * @param int $voucherId
     * @param float $totalAmount
     * @return array
     */
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
        return ['success'=>false, 'message'=>'Voucher not found'];
    }

    // ===== VALIDATE =====
    if ((int)$voucher->is_active !== 1) {
        return ['success'=>false, 'message'=>'Voucher not active'];
    }

    $today = date('Y-m-d');
    if ($voucher->start_date && strtotime($today) < strtotime($voucher->start_date)) {
        return ['success'=>false, 'message'=>'Voucher not started yet'];
    }
    if ($voucher->end_date && strtotime($today) > strtotime($voucher->end_date)) {
        return ['success'=>false, 'message'=>'Voucher expired'];
    }

    if (!is_null($voucher->quantity) && $voucher->used_count >= $voucher->quantity) {
        return ['success'=>false, 'message'=>'Voucher out of stock'];
    }

    if ($totalAmount < $voucher->min_bill_total) {
        return ['success'=>false, 'message'=>'Bill total below minimum'];
    }

    if ((int)$voucher->point_cost > 0 && $customerPoints < (int)$voucher->point_cost) {
        return ['success'=>false, 'message'=>'Not enough points'];
    }
    
    // ===== CALCULATE =====
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

    /**
     * Xem trước áp dụng voucher (wrapper)
     */
    public function previewVoucher($customerId, $voucherId, $totalAmount) {
        return $this->previewApplyVoucher($customerId, $voucherId, $totalAmount);
    }

    /**
     * Xóa voucher
     * @param int $id
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteVoucher($id) {
        $repository = $this->repository('VoucherRepository');

        // Kiểm tra voucher tồn tại
        $voucher = $repository->findById($id);
        if (!$voucher) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy voucher!'
            ];
        }

        // Xóa voucher
        $result = $repository->delete($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Xóa voucher thành công!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa voucher!'
            ];
        }
    }



    /**
     * Lấy voucher đang còn hiệu lực
     * @return array
     */
    public function getActiveVouchers() {
        $repository = $this->repository('VoucherRepository');
        return $repository->findActiveVouchers();
    }

    /**
     * Lấy voucher đủ điều kiện cho customer và bill total
     * @param int|null $customerId
     * @param float $billTotal
     * @return array VoucherEntity[]
     */
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

    /**
     * Tính discount amount theo voucher và bill total
    * @param VoucherEntity $v
    * @param float $billTotal
    * @return int Số tiền giảm (đơn vị nhỏ nhất, VND)
     */
    public function calculateDiscount($v, $billTotal) {
        // Kiểm tra bill total có đạt yêu cầu tối thiểu không
        if ($billTotal < $v->min_bill_total) {
            return 0;
        }

        $discount = 0;
        if ($v->discount_type === 'FIXED') {
            $discount = (float)$v->discount_value;
        } else { // PERCENT
            $discount = $billTotal * ((float)$v->discount_value / 100.0);
        }

        if (!empty($v->max_discount_value)) {
            $discount = min($discount, (float)$v->max_discount_value);
        }

        $discount = min($discount, $billTotal);
        return (int)round($discount);
    }

    /**
     * Validate dữ liệu voucher
     * @param array $data
     * @param int|null $id ID voucher (dùng khi update)
     * @return array ['valid' => bool, 'message' => string]
     */
    private function validateVoucherData($data, $id = null) {
        // Kiểm tra tên voucher
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            return [
                'valid' => false,
                'message' => 'Tên voucher phải có ít nhất 3 ký tự!'
            ];
        }

        // Kiểm tra point cost
        if (!isset($data['point_cost']) || $data['point_cost'] < 0) {
            return [
                'valid' => false,
                'message' => 'Điểm đổi phải là số không âm!'
            ];
        }

        // Kiểm tra discount type
        if (empty($data['discount_type']) || !in_array($data['discount_type'], ['FIXED', 'PERCENT'])) {
            return [
                'valid' => false,
                'message' => 'Loại giảm giá không hợp lệ!'
            ];
        }

        // Kiểm tra discount value
        if (!isset($data['discount_value']) || $data['discount_value'] <= 0) {
            return [
                'valid' => false,
                'message' => 'Giá trị giảm phải lớn hơn 0!'
            ];
        }

        // Nếu là phần trăm, kiểm tra không vượt quá 100
        if ($data['discount_type'] === 'PERCENT' && $data['discount_value'] > 100) {
            return [
                'valid' => false,
                'message' => 'Phần trăm giảm không được vượt quá 100%!'
            ];
        }

        // Kiểm tra ngày bắt đầu và kết thúc
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                return [
                    'valid' => false,
                    'message' => 'Ngày kết thúc phải sau ngày bắt đầu!'
                ];
            }
        }

        // Kiểm tra số lượng
        if (!empty($data['quantity']) && $data['quantity'] < 0) {
            return [
                'valid' => false,
                'message' => 'Số lượng không được âm!'
            ];
        }

        return ['valid' => true, 'message' => ''];
    }
}
?>
