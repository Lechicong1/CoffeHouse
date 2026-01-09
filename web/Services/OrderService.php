<?php
include_once './web/Repositories/OrderRepository.php';
include_once './web/Repositories/OrderItemRepository.php';
include_once './web/Repositories/CartRepository.php';
include_once './web/Repositories/ProductSizeRepository.php';
include_once './web/Entity/OrderEntity.php';
include_once './web/Entity/OrderItemEntity.php';
include_once './web/Services/VoucherService.php';
include_once './web/Repositories/CustomerRepository.php';

use web\Entity\OrderEntity;
use web\Entity\OrderItemEntity;

class OrderService {
    private $orderRepo;
    private $orderItemRepo;
    private $cartRepo;
    private $productSizeRepo;
    private $voucherService;

    public function __construct() {
        $this->orderRepo = new OrderRepository();
        $this->orderItemRepo = new OrderItemRepository();
        $this->cartRepo = new CartRepository();
        $this->productSizeRepo = new ProductSizeRepository();
        $this->voucherService = new VoucherService();
    }

    /**
     * Lấy OrderRepository instance (dùng cho các trường hợp đặc biệt)
     */
    public function getOrderRepo() {
        return $this->orderRepo;
    }

    /**
     * Tạo đơn hàng từ checkout
     */
    public function createOrderFromCheckout($customerId, $data) {
        try {
            // 1. Lấy giỏ hàng
            $cartItems = $this->cartRepo->findCartByCustomerId($customerId);

            if (empty($cartItems)) {
                throw new Exception('Giỏ hàng trống');
            }

            // 2. Tạo Order Entity
            $order = new OrderEntity();
            $order->order_code = $this->generateOrderCode();
            $order->customer_id = $customerId;
            $order->order_type = $data['order_type'] ?? 'ONLINE_DELIVERY';
            $order->status = ($data['payment_method'] === 'CASH') ? 'PENDING' : 'AWAITING_PAYMENT';
            $order->payment_status = ($data['payment_method'] === 'CASH') ? 'PENDING' : 'AWAITING_PAYMENT';
            $order->payment_method = $data['payment_method'];

            // Compute sub_total from cart to be used for voucher calculation
            $sub_total = 0.0;
            foreach ($cartItems as $ci) {
                $sub_total += (float)$ci->price * (int)$ci->quantity;
            }
            $order->total_amount = $sub_total; // store pre-discount total

            $order->shipping_address = $data['shipping_address'];
            $order->receiver_name = $data['customer_name'];
            $order->receiver_phone = $data['customer_phone'];
            $order->shipping_fee = 0;
            $order->note = $data['note'] ?? '';
            $orderId = $this->orderRepo->create($order);
            foreach ($cartItems as $cartItem) {
                $item = new OrderItemEntity();
                $item->order_id = $orderId;
                $item->product_size_id = $cartItem->product_size_id;
                $item->quantity = $cartItem->quantity;
                $item->price_at_purchase = $cartItem->price;
                $item->note = '';

                $this->orderItemRepo->create($item);
            }

            $discountAmount = 0.0;
            if (!empty($data['voucher']) && !empty($order->customer_id)) {
                $voucherId = isset($data['voucher']['voucher_id']) ? (int)$data['voucher']['voucher_id'] : null;
                if ($voucherId) {
                    $redeemResult = $this->voucherService->redeemVoucher(
                        $order->customer_id,
                        $voucherId,
                        $order->total_amount
                    );

                    if (!$redeemResult['success']) {
                        return $redeemResult;
                    }
                    $discountAmount = $redeemResult['discount_amount'];
                }
            }
            $this->cartRepo->clearCart($customerId);

            $final_total = $order->total_amount - (float)$discountAmount;
            if ($final_total < 0) $final_total = 0.0;

            return [
                'success' => true,
                'order_id' => $orderId,
                'order_code' => $order->order_code,
                'message' => 'Đặt hàng thành công',
                'sub_total' => $order->total_amount,
                'discount_amount' => (float)$discountAmount,
                'final_total' => $final_total
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createOrder($data)
    {
        // 1. Validate items - KHÔNG return null nếu rỗng
        if (empty($data['items']) || !is_array($data['items'])) {
            return ['success' => false, 'message' => 'Không có sản phẩm trong đơn hàng'];
        }

        // 2. Validate và tính sub_total (CHỈ validate, KHÔNG tạo order ở đây)
        $sub_total = 0.0;
        $validatedItems = [];

        foreach ($data['items'] as $it) {
            $sizeId = isset($it['size_id']) ? $it['size_id'] : null;
            $qty = isset($it['qty']) ? (int)$it['qty'] : 0;

            if (!$sizeId || $qty <= 0) {
                continue;
            }

            $productSize = $this->productSizeRepo->findById($sizeId);
            if (!$productSize) {
                return ['success' => false, 'message' => "Sản phẩm không tồn tại: size_id = $sizeId"];
            }

            $realPrice = (float)$productSize->price;
            $sub_total += $realPrice * $qty;

            $validatedItems[] = [
                'size_id' => $productSize->id,
                'qty' => $qty,
                'price' => $realPrice,
                'notes' => $it['notes'] ?? ''
            ];
        }

        // 3. Check items hợp lệ
        if (empty($validatedItems)) {
            return ['success' => false, 'message' => 'Không có sản phẩm hợp lệ trong đơn hàng'];
        }

        // 4. Tạo Order Entity (SAU KHI validate xong)
        $order = new OrderEntity();
        $order->order_code = $this->generateUniqueOrderCode();
        $order->staff_id = $data['staff_id'] ?? null;
        $order->customer_id = $data['customer_id'] ?? null;
        $order->order_type = $data['order_type'] ?? 'AT_COUNTER';
        $order->status = 'PENDING';
        $order->payment_status = 'PAID';
        $order->payment_method = $data['payment_method'] ?? 'CASH';
        $order->total_amount = $sub_total;
        $order->note = $data['note'] ?? '';
        $order->table_number = $data['table_number'] ?? null;

        // Debug log
        error_log("DEBUG OrderService - table_number: " . ($order->table_number ?? 'NULL'));

        // Thông tin shipping nếu có (Mang về)
        if ($order->order_type === 'ONLINE_DELIVERY' || $order->order_type === 'TAKE_AWAY') {
             // Xử lý logic mang về nếu cần
        }

        // 5. Transaction: tạo order + items + voucher
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

            // 6. Lưu Order Items
            $this->orderItemRepo->con = $con;
            foreach ($validatedItems as $itemData) {
                $item = new OrderItemEntity();
                $item->order_id = $orderId;
                $item->product_size_id = $itemData['size_id'];
                $item->quantity = $itemData['qty'];
                $item->price_at_purchase = $itemData['price'];
                $item->note = $itemData['notes'] ?? '';
                $this->orderItemRepo->create($item);
            }

            // 7. Xử lý voucher (nếu có) - gọi VoucherService.redeemVoucher()
            $discountAmount = 0.0;
            if (!empty($data['voucher']) && !empty($order->customer_id)) {
                $voucherId = isset($data['voucher']['voucher_id']) ? (int)$data['voucher']['voucher_id'] : null;
                if ($voucherId) {
                    $redeemResult = $this->voucherService->redeemVoucher(
                        $order->customer_id,
                        $voucherId,
                        $order->total_amount,
                        $con
                    );
                    if (!$redeemResult['success']) {
                        mysqli_rollback($con);
                        return $redeemResult;
                    }
                    $discountAmount = $redeemResult['discount_amount'];
                }
            }

            mysqli_commit($con);

            $final_total = $order->total_amount - (float)$discountAmount;
            if ($final_total < 0) $final_total = 0.0;

            // Cộng điểm cho khách hàng (POS: cộng ngay khi thanh toán)
            $pointsAwarded = $this->awardLoyaltyPoints($order->customer_id, $order->total_amount);

            return [
                'success' => true,
                'order_id' => $orderId,
                'order_code' => $order->order_code,
                'message' => 'Tạo đơn hàng thành công',
                'sub_total' => $order->total_amount,
                'discount_amount' => (float)$discountAmount,
                'final_total' => $final_total,
                'points_awarded' => $pointsAwarded
            ];

        } catch (Exception $e) {
            mysqli_rollback($con);
            return ['success' => false, 'message' => 'Lỗi khi tạo đơn hàng: ' . $e->getMessage()];
        }
    }

    /**
     * Tạo mã đơn hàng tự động (cũ - cho checkout)
     */
    private function generateOrderCode() {
        return 'ORD' . date('YmdHis') . rand(100, 999);
    }

    /**
     * Tạo mã đơn hàng unique theo format ORD + 4 số
     * Kiểm tra trùng và random lại nếu trùng
     */
    private function generateUniqueOrderCode() {
        $maxAttempts = 10;
        $attempts = 0;
        
        do {
            // Tạo mã ORD + 4 số ngẫu nhiên
            $code = 'ORD' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            
            // Kiểm tra trùng trong database
            $exists = $this->orderRepo->findByOrderCode($code);
            
            if (!$exists) {
                return $code;
            }
            
            $attempts++;
        } while ($attempts < $maxAttempts);
        
        // Fallback: dùng timestamp nếu không tìm được mã unique
        return 'ORD' . substr(time(), -4);
    }

    /**
     * Lấy thông tin đơn hàng theo ID
     */
    public function getOrderById($orderId) {
        return $this->orderRepo->findById($orderId);
    }

    /**
     * Lấy danh sách đơn hàng với filter
     * @param array $filters ['status' => 'PROCESSING', 'search' => 'ORD123']
     * @return array
     */
    public function getOrders($filters = []) {
        return $this->orderRepo->findAllWithFilters($filters);
    }

    /**
     * Lấy chi tiết items của đơn hàng
     * @param int $orderId
     * @return array
     */
    public function getOrderItems($orderId) {
        return $this->orderItemRepo->findByOrderId($orderId);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * @param int $orderId
     * @param string $newStatus
     * @return array
     */
    public function updateOrderStatus($orderId, $newStatus) {
        try {
            $order = $this->orderRepo->findById($orderId);
            
            if (!$order) {
                return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
            }

            // Validate status
            $validStatuses = ['PENDING', 'PREPARING', 'READY', 'SHIPPING', 'COMPLETED', 'CANCELLED'];
            if (!in_array($newStatus, $validStatuses)) {
                return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
            }

            // Nếu hủy đơn đã thanh toán -> Đánh dấu hoàn tiền
            if ($newStatus === 'CANCELLED' && $order->payment_status === 'PAID') {
                $order->payment_status = 'REFUNDED';
            }

            $order->status = $newStatus;
            
            if ($this->orderRepo->update($order)) {
                // Cộng điểm khi đơn hàng COMPLETED (Web: shipper hoàn thành)
                if ($newStatus === 'COMPLETED' && $order->customer_id) {
                    $this->awardLoyaltyPoints($order->customer_id, $order->total_amount);
                }
                return ['success' => true, 'message' => 'Cập nhật trạng thái thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cập nhật ghi chú đơn hàng
     * @param int $orderId
     * @param string $note
     * @return array
     */
    public function updateOrderNote($orderId, $note) {
        try {
            $order = $this->orderRepo->findById($orderId);
            
            if (!$order) {
                return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
            }
            
            // Chỉ cho phép sửa note khi đơn hàng đang ở trạng thái PENDING
            if ($order->status !== 'PENDING') {
                return ['success' => false, 'message' => 'Chỉ có thể sửa ghi chú khi đơn hàng đang chờ xác nhận'];
            }

            $order->note = trim($note);
            
            if ($this->orderRepo->update($order)) {
                return ['success' => true, 'message' => 'Cập nhật ghi chú thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cập nhật chi tiết đơn hàng: loại đơn, số bàn, ghi chú (chỉ khi PENDING)
     */
    public function updateOrderDetails($orderId, $orderType, $tableNumber, $note) {
        try {
            $order = $this->orderRepo->findById($orderId);
            if (!$order) {
                return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
            }

            if ($order->status !== 'PENDING') {
                return ['success' => false, 'message' => 'Chỉ có thể sửa đơn khi đang chờ xác nhận'];
            }

            // Nếu chọn mang về, xóa số bàn
            if ($orderType === 'TAKEAWAY') {
                $order->table_number = null;
            } else {
                $order->table_number = $tableNumber ?: null;
            }

            $order->order_type = $orderType;
            $order->note = trim($note);

            if ($this->orderRepo->update($order)) {
                return ['success' => true, 'message' => 'Cập nhật đơn hàng thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi cập nhật đơn hàng'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cập nhật ghi chú cho từng item trong đơn hàng
     * @param int $itemId
     * @param string $note
     * @return array
     */
    public function updateOrderItemNote($itemId, $note) {
        try {
            // Kiểm tra item có tồn tại không
            $item = $this->orderItemRepo->findItemById($itemId);
            if (!$item) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong đơn hàng'
                ];
            }

            // Cập nhật note
            $result = $this->orderItemRepo->updateItemNote($itemId, $note);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật ghi chú thành công'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Lỗi khi cập nhật ghi chú'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Tính và cộng điểm cho khách hàng
     * 1.000đ = 1 điểm
     * @param int|null $customerId
     * @param float $totalAmount - Tổng tiền đơn hàng (trước giảm)
     * @return int - Số điểm được cộng
     */
    private function awardLoyaltyPoints($customerId, $totalAmount) {
        if (!$customerId || $totalAmount <= 0) {
            return 0;
        }

        $points = (int)floor($totalAmount / 1000);
        if ($points <= 0) {
            return 0;
        }

        $customerRepo = new CustomerRepository();
        $customer = $customerRepo->findById($customerId);
        if (!$customer) {
            return 0;
        }

        $newPoints = (int)$customer->points + $points;
        if ($customerRepo->updatePoints($customerId, $newPoints)) {
            return $points;
        }

        return 0;
    }
}
?>
