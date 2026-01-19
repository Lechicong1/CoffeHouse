<?php
include_once './web/Repositories/OrderRepository.php';
include_once './web/Repositories/OrderItemRepository.php';
include_once './web/Repositories/CartRepository.php';
include_once './web/Repositories/ProductSizeRepository.php';
include_once './web/Repositories/RecipeRepository.php';
include_once './web/Repositories/IngredientRepository.php';
include_once './web/Entity/OrderEntity.php';
include_once './web/Entity/OrderItemEntity.php';
include_once './web/Services/VoucherService.php';
include_once './web/Services/CustomerService.php';
include_once './web/Repositories/CustomerRepository.php';
include_once './Enums/size.enum.php';
include_once './Enums/status.enum.php';

use web\Entity\OrderEntity;
use web\Entity\OrderItemEntity;

class OrderService  {
    private $orderRepo;
    private $orderItemRepo;
    private $cartRepo;
    private $productSizeRepo;
    private $voucherService;
    private $customerService;
    private $recipeRepo;
    private $ingredientRepo;

    public function __construct() {
        $this->orderRepo = new OrderRepository();
        $this->orderItemRepo = new OrderItemRepository();
        $this->cartRepo = new CartRepository();
        $this->productSizeRepo = new ProductSizeRepository();
        $this->voucherService = new VoucherService();
        $this->customerService = new CustomerService();
        $this->recipeRepo = new RecipeRepository();
        $this->ingredientRepo = new IngredientRepository();
    }

    /**
     * Lấy OrderRepository instance (dùng cho các trường hợp đặc biệt)
     */
    public function getOrderRepo() {
        return $this->orderRepo;
    }

    /**
     * Validate dữ liệu đơn hàng từ checkout
     */
    public function validateOrderData($data) {
        $requiredFields = [
            'customer_name' => 'Tên người nhận',
            'customer_phone' => 'Số điện thoại',
            'shipping_address' => 'Địa chỉ giao hàng',
            'payment_method' => 'Phương thức thanh toán',
            'total_amount' => 'Tổng tiền'
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($data[$field])) {
                return [
                    'success' => false,
                    'message' => "Vui lòng nhập {$label}"
                ];
            }
        }

        // Validate phone number format
        if (!is_numeric($data['customer_phone'])){
            return [
                'success' => false,
                'message' => 'Số điện thoại kông hợp lệ '
            ];
        }


        // Validate total amount
        if (!is_numeric($data['total_amount']) || $data['total_amount'] <= 0) {
            return [
                'success' => false,
                'message' => 'Tổng tiền không hợp lệ'
            ];
        }

        return ['success' => true];
    }

    private function validateIngredientStock($orderItems) {
        foreach ($orderItems as $item) {
            $productSizeId = is_array($item) ? $item['product_size_id'] : $item->product_size_id;
            $quantity = is_array($item) ? $item['quantity'] : $item->quantity;

            $productSize = $this->productSizeRepo->findById($productSizeId);

            if ($productSize) {
                $multiplier = SizeEnum::getMultiplier($productSize->size_name);
                $recipes = $this->recipeRepo->getByProductId($productSize->product_id);

                foreach ($recipes as $recipe) {
                    $ingredient = $this->ingredientRepo->findById($recipe->ingredient_id);
                    if ($ingredient) {
                        $quantityNeeded = $recipe->base_amount * $multiplier * $quantity;

                        if ($ingredient->stock_quantity < $quantityNeeded) {
                            return ['success' => false, 'message' => 'Không đủ nguyên liệu để đặt hàng'];
                        }
                    }
                }
            }
        }

        return ['success' => true];
    }


    public function createOrderFromCheckout($customerId, $data) {
        try {
            // 1. Lấy items trực tiếp từ $data (đã được Controller xử lý sẵn)
            if (empty($data['items'])) {
                return ['success' => false, 'message' => 'Không có sản phẩm trong đơn hàng'];
            }

            $cartItems = $data['items'];
            // Validate qua OrderService
            $validation = $this->validateOrderData($data);
            if (!$validation['success']) {
                throw new Exception($validation['message']);
            }
            // 2. Validate số lượng nguyên liệu trước khi đặt hàng
            $validateResult = $this->validateIngredientStock($cartItems);
            if (!$validateResult['success']) {
                return $validateResult;
            }
            // 3. Tạo Order Entity - Luôn tạo với trạng thái PENDING
            $order = new OrderEntity();
            $order->order_code = $this->generateOrderCode();
            $order->customer_id = $customerId;
            $order->order_type = 'ONLINE_DELIVERY';
            $order->status = OrderStatus::PENDING;
            $order->payment_status = 'PAID';
            $order->payment_method = $data['payment_method'];
            $order->total_amount = $data['total_amount'];
            $order->shipping_address = $data['shipping_address'];
            $order->receiver_name = $data['customer_name'];
            $order->receiver_phone = $data['customer_phone'];
            $order->shipping_fee = 0;
            $order->note = $data['note'] ?? '';

            // 4. Tạo đơn hàng
            $orderId = $this->orderRepo->create($order);

            // 5. Tạo order items
            foreach ($cartItems as $cartItem) {
                $item = new OrderItemEntity();
                $item->order_id = $orderId;
                $item->product_size_id = $cartItem->product_size_id;
                $item->quantity = $cartItem->quantity;
                $item->price_at_purchase = $cartItem->price;
                $item->note = '';

                $this->orderItemRepo->create($item);
            }

            // 6. Xử lý voucher (nếu có) - chỉ redeem (cập nhật used_count, trừ điểm)
            // Lưu ý: Frontend đã gửi total_amount ĐÃ TRỪ voucher, không cần tính lại
            if (!empty($data['voucher']) && !empty($order->customer_id)) {
                $voucherId = isset($data['voucher']['voucher_id']) ? (int)$data['voucher']['voucher_id'] : null;
                if ($voucherId) {
                    // Tính subtotal gốc từ items để check min_bill_total đúng
                    $originalSubtotal = 0;
                    foreach ($cartItems as $item) {
                        $originalSubtotal += (float)$item->price * (int)$item->quantity;
                    }
                    
                    // Gọi redeemVoucher với subtotal GỐC để validate min_bill_total đúng
                    $redeemResult = $this->voucherService->redeemVoucher(
                        $order->customer_id,
                        $voucherId,
                        $originalSubtotal
                    );

                    if (!$redeemResult['success']) {
                        return $redeemResult;
                    }
                }
            }
            // 7. Xóa giỏ hàng (chỉ khi checkout từ Cart, không phải Buy Now)
            if (empty($data['is_buy_now'])) {
                $this->cartRepo->clearCart($customerId);
            }

            return [
                'success' => true,
                'order_id' => $orderId,
                'order_code' => $order->order_code,
                'message' => 'Đặt hàng thành công'
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

        // Chuẩn hóa dữ liệu & Tính tổng tiền
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
                'product_size_id' => $productSize->id, // cho checkkho
                'quantity' => $qty,
                'price' => $realPrice,
                'notes' => $it['notes'] ?? ''
            ];
        }

        // Check items hợp lệ & Kiểm tra tồn kho nguyên liệu trước
        if (empty($validatedItems)) {
            return ['success' => false, 'message' => 'Không có sản phẩm hợp lệ trong đơn hàng'];
        }

        // --- Kiểm tra tồn kho nguyên liệu ---
        $stockCheck = $this->validateIngredientStock($validatedItems);
        if (!$stockCheck['success']) {
            return $stockCheck;
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

        // tạo order + items + voucher
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

            // Nếu có discount, update lại total_amount trong DB
            if ($discountAmount > 0) {
                $order->id = $orderId;
                $order->total_amount = $final_total;
                $this->orderRepo->update($order);
            }

            // Cộng điểm cho khách hàng (POS: cộng ngay khi thanh toán)
            $pointsAwarded = $this->customerService->awardLoyaltyPoints($order->customer_id, $order->total_amount);

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


    private function generateOrderCode() {
        return 'ORD' . date('YmdHis') . rand(100, 999);
    }

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

    public function getOrderById($orderId) {
        return $this->orderRepo->findById($orderId);
    }

    /**
     * Lấy tất cả đơn hàng cho Admin (đơn giản)
     * @return array
     */
    public function getAllOrdersForAdmin() {
        return $this->orderRepo->getAllOrdersForAdmin();
    }

    /**
     * Tìm kiếm đơn hàng theo keyword cho Admin
     * @param string $keyword
     * @return array
     */
    public function searchOrdersForAdmin($keyword) {
        return $this->orderRepo->searchOrdersForAdmin($keyword);
    }

    /**
     * Lấy danh sách đơn hàng với filter
     *  ['status' => 'PROCESSING', 'search' => 'ORD123']
     */
    public function getOrders($filters = []) {
        // Normalize filters and apply default order_type for staff POS listing
        $normalized = [];
        if (!empty($filters['status'])) {
            $normalized['status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $normalized['search'] = trim($filters['search']);
        }

        // Nếu controller không cung cấp order_type, mặc định chỉ lấy AT_COUNTER và TAKEAWAY
        if (isset($filters['order_type']) && !empty($filters['order_type'])) {
            $normalized['order_type'] = $filters['order_type'];
        } else {
            $normalized['order_type'] = ['AT_COUNTER', 'TAKEAWAY'];
        }

        return $this->orderRepo->findAllWithFilters($normalized);
    }


    public function getOrderItems($orderId) {
        return $this->orderItemRepo->findByOrderId($orderId);
    }

    public function createOrderFromPOS($postData) {
        try {
            // Validate required fields minimally here; deep validation in createOrder()
            $data = [];
            $data['staff_id'] = $postData['staff_id'] ?? null;
            $data['customer_id'] = !empty($postData['customer_id']) ? (int)$postData['customer_id'] : null;
            $data['order_type'] = $postData['order_type'] ?? 'AT_COUNTER';
            $data['payment_method'] = $postData['payment_method'] ?? 'CASH';
            $data['total_amount'] = isset($postData['total_amount']) ? (float)$postData['total_amount'] : 0.0;
            $data['note'] = trim($postData['note'] ?? '');
            $data['table_number'] = !empty($postData['table_number']) ? trim($postData['table_number']) : null;

            // Parse cart items which may be JSON string
            $items = $postData['cart_items'] ?? [];
            if (is_string($items)) {
                $decoded = json_decode($items, true);
                if ($decoded && is_array($decoded)) {
                    $items = $decoded;
                } else {
                    $items = [];
                }
            }
            $data['items'] = $items;

            // Voucher
            if (!empty($postData['voucher_id'])) {
                $data['voucher'] = ['voucher_id' => (int)$postData['voucher_id']];
            }

            return $this->createOrder($data);
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Lấy dữ liệu order + items dùng cho hóa đơn (service helper)
     * @param int $orderId
     * @return array ['success'=>bool,'order'=>array,'items'=>array]
     */
    public function getOrderInvoiceData($orderId) {
        try {
            if (!$orderId) return ['success' => false, 'message' => 'Thiếu order_id'];

            // Lấy order (có customer info) bằng cách dùng repository findAllWithFilters và tìm id
            $all = $this->orderRepo->findAllWithFilters([]);
            $orderData = null;
            foreach ($all as $o) {
                if ((int)$o['id'] === (int)$orderId) {
                    $orderData = $o;
                    break;
                }
            }

            if (!$orderData) return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];

            $items = $this->getOrderItems($orderId);
            return ['success' => true, 'order' => $orderData, 'items' => $items];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
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

            // Nếu hủy đơn đã thanh toán -> Đánh dấu hoàn tiền
            if ($newStatus === OrderStatus::CANCELLED && $order->payment_status === 'PAID') {
                $order->payment_status = 'REFUNDED';
            }

            // Khi giao hàng xong (COMPLETED) -> Đánh dấu đã thanh toán (COD)
            if ($newStatus === OrderStatus::COMPLETED && $order->payment_status !== 'PAID') {
                $order->payment_status = 'PAID';
            }

            $order->status = $newStatus;
            
            if ($this->orderRepo->update($order)) {
                // Cộng điểm khi đơn hàng COMPLETED (Web: shipper hoàn thành)
                if ($newStatus === OrderStatus::COMPLETED && $order->customer_id) {
                    $this->customerService->awardLoyaltyPoints($order->customer_id, $order->total_amount);
                }
                return ['success' => true, 'message' => 'Cập nhật trạng thái thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateOrderNote($orderId, $note) {
        try {
            $order = $this->orderRepo->findById($orderId);
            
            if (!$order) {
                return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
            }
            
            // Chỉ cho phép sửa note khi đơn hàng đang ở trạng thái PENDING
            if ($order->status !== OrderStatus::PENDING) {
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

            if ($order->status !== OrderStatus::PENDING) {
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

   // cong le
    public function cancelOrder($orderId, $customerId) {
        try {
            $order = $this->orderRepo->findById($orderId);
            if (!$order) {
                return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
            }
            if ($order->customer_id != $customerId) {
                return ['success' => false, 'message' => 'Bạn không có quyền hủy đơn hàng này'];
            }
            // 3. Kiểm tra trạng thái đơn hàng (chỉ cho phép hủy PENDING)
            if ($order->status !== OrderStatus::PENDING) {
                return ['success' => false, 'message' => 'Không thể hủy đơn hàng đã được xử lý'];
            }

            // 4. Cập nhật trạng thái
            $order->status = OrderStatus::CANCELLED;

            // Nếu đã thanh toán -> đánh dấu hoàn tiền
            if ($order->payment_status === 'PAID') {
                $order->payment_status = 'REFUNDED';
            } else {
                $order->payment_status = 'CANCELLED';
            }

            // 5. Cập nhật vào database
            if ($this->orderRepo->update($order)) {
                return [
                    'success' => true,
                    'message' => 'Hủy đơn hàng thành công'
                ];
            }


        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }


    /**
     * Lấy danh sách đơn hàng theo customerId (chuẩn service)
     */
    public function findByCustomerId($customerId) {
        return $this->orderRepo->findByCustomerId($customerId);
    }

}
?>
