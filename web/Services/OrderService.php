<?php
include_once './web/Repositories/OrderRepository.php';
include_once './web/Repositories/OrderItemRepository.php';
include_once './web/Entity/OrderEntity.php';
include_once './web/Entity/OrderItemEntity.php';
include_once './web/Services/VoucherService.php';
include_once './web/Repositories/VoucherRepository.php';
include_once './web/Repositories/CustomerRepository.php';
// Note: VoucherRedemption components removed — redemption handled directly via repositories

use web\Entity\OrderEntity;
use web\Entity\OrderItemEntity;

class OrderService {
    private $orderRepo;
    private $orderItemRepo;

    public function __construct() {
        $this->orderRepo = new OrderRepository();
        $this->orderItemRepo = new OrderItemRepository();
    }

    public function createOrder($data) {
        // 1. Tạo Order Entity
        $order = new OrderEntity();
        $order->order_code = $data['order_code'];
        $order->staff_id = $data['staff_id'] ?? null; // Lấy từ session nếu có
        $order->customer_id = $data['customer_id'] ?? null; // Mặc định null hoặc khách lẻ
        $order->order_type = $data['order_type'] ?? 'AT_COUNTER';
        $order->status = 'COMPLETED'; // POS thanh toán xong là completed luôn
        $order->payment_status = 'PAID';
        $order->payment_method = $data['payment_method'] ?? 'CASH';

        // Tính toán sub_total (tổng trước giảm) từ items để tránh nhầm lẫn
        $sub_total = 0.0;
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $it) {
                $price = isset($it['price']) ? (float)$it['price'] : 0.0;
                $qty = isset($it['qty']) ? (int)$it['qty'] : 0;
                $sub_total += $price * $qty;
            }
        } else {
            $sub_total = isset($data['total_amount']) ? (float)$data['total_amount'] : 0.0;
        }

        // Quy ước: orders.total_amount lưu `sub_total` (trước khi trừ voucher/discount)
        $order->total_amount = $sub_total;
        $order->note = $data['note'] ?? '';
        
        // Thông tin shipping nếu có (Mang về)
        if ($order->order_type === 'ONLINE_DELIVERY' || $order->order_type === 'TAKE_AWAY') {
             // Xử lý logic mang về nếu cần
        }

        // 2. Lưu Order trong transaction (bao gồm items và redeem)
        $con = $this->orderRepo->con;
        if (!mysqli_begin_transaction($con)) {
            return ['success' => false, 'message' => 'Không thể bắt đầu transaction DB'];
        }

        try {
            $orderId = $this->orderRepo->create($order);
            if (!$orderId) {
                mysqli_rollback($con);
                return ['success' => false, 'message' => 'Lỗi khi tạo đơn hàng'];
            }

            // 3. Lưu Order Items (reuse cùng connection)
            $this->orderItemRepo->con = $con;
            foreach ($data['items'] as $itemData) {
                $item = new OrderItemEntity();
                $item->order_id = $orderId;
                $item->product_id = $itemData['id']; // ID sản phẩm
                $item->product_size_id = $itemData['size_id']; // ID kích thước/size
                $item->quantity = $itemData['qty'];
                $item->price_at_purchase = $itemData['price'];
                $item->note = $itemData['notes'] ?? '';

                $this->orderItemRepo->create($item);
            }

            // Nếu có thông tin voucher được gửi kèm, backend sẽ TÍNH và redeem trong cùng transaction
            $discountAmount = 0.0;
            if (!empty($data['voucher']) && !empty($order->customer_id)) {
                $custId = $order->customer_id;
                $voucherId = isset($data['voucher']['voucher_id']) ? (int)$data['voucher']['voucher_id'] : null;

                if ($voucherId) {
                    $vService = new VoucherService();
                    $v = $vService->getVoucherById($voucherId);
                    if (!$v) {
                        mysqli_rollback($con);
                        return ['success' => false, 'message' => 'Voucher không tồn tại'];
                    }

                    // Server-side calculate discount and pointsUsed (FE does not send discount)
                    $discountAmount = $vService->calculateDiscount($v, $order->total_amount);
                    $pointsUsed = (int)$v->point_cost;

                    // Apply redemption using repositories (user removed VoucherRedemptionService)
                    $voucherRepo = new VoucherRepository();
                    $voucherRepo->con = $con;
                    $customerRepo = new CustomerRepository();
                    $customerRepo->con = $con;

                    // reload customer under same connection
                    $cust = $customerRepo->findById($custId);
                    if (!$cust) {
                        mysqli_rollback($con);
                        return ['success' => false, 'message' => 'Customer not found'];
                    }

                    if ($cust->points < $pointsUsed) {
                        mysqli_rollback($con);
                        return ['success' => false, 'message' => 'Không đủ điểm để đổi voucher'];
                    }

                    // increment used_count
                    $v->used_count = (int)$v->used_count + 1;
                    if (!$voucherRepo->update($v)) {
                        mysqli_rollback($con);
                        return ['success' => false, 'message' => 'Không thể cập nhật voucher.used_count'];
                    }

                    // deduct customer points
                    $newPoints = max(0, (int)$cust->points - $pointsUsed);
                    if (!$customerRepo->updatePoints($custId, $newPoints)) {
                        mysqli_rollback($con);
                        return ['success' => false, 'message' => 'Không thể cập nhật điểm khách hàng'];
                    }

                    // VoucherRedemption logging removed per user request
                }
            }

            mysqli_commit($con);

            // Tính final total (không lưu vào DB ở bước này)
            $final_total = $order->total_amount - (float)$discountAmount;
            if ($final_total < 0) $final_total = 0.0;

            return [
                'success' => true,
                'order_id' => $orderId,
                'message' => 'Tạo đơn hàng thành công',
                'sub_total' => $order->total_amount,
                'discount_amount' => (float)$discountAmount,
                'final_total' => $final_total
            ];
        } catch (Exception $e) {
            mysqli_rollback($con);
            return ['success' => false, 'message' => 'Lỗi khi tạo đơn hàng: ' . $e->getMessage()];
        }
    }
}
?>
