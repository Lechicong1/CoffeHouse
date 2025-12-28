<?php
include_once './web/Repositories/OrderRepository.php';
include_once './web/Repositories/OrderItemRepository.php';
include_once './web/Repositories/CartRepository.php';
include_once './web/Entity/OrderEntity.php';
include_once './web/Entity/OrderItemEntity.php';

use web\Entity\OrderEntity;
use web\Entity\OrderItemEntity;

class OrderService {
    private $orderRepo;
    private $orderItemRepo;
    private $cartRepo;

    public function __construct() {
        $this->orderRepo = new OrderRepository();
        $this->orderItemRepo = new OrderItemRepository();
        $this->cartRepo = new CartRepository();
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
            $order->total_amount = $data['total_amount'];
            $order->shipping_address = $data['shipping_address'];
            $order->receiver_name = $data['customer_name'];
            $order->receiver_phone = $data['customer_phone'];
            $order->shipping_fee = 0;
            $order->note = $data['note'] ?? '';

            // 3. Lưu Order
            $orderId = $this->orderRepo->create($order);

            if ($orderId) {
                // 4. Lưu Order Items từ cart
                foreach ($cartItems as $cartItem) {
                    $item = new OrderItemEntity();
                    $item->order_id = $orderId;
                    $item->product_size_id = $cartItem->product_size_id;
                    $item->quantity = $cartItem->quantity;
                    $item->price_at_purchase = $cartItem->price;
                    $item->note = '';

                    $this->orderItemRepo->create($item);
                }

                // 5. Xóa giỏ hàng sau khi đặt hàng thành công
                $this->cartRepo->clearCart($customerId);

                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'order_code' => $order->order_code,
                    'message' => 'Đặt hàng thành công'
                ];
            }

            throw new Exception('Lỗi khi tạo đơn hàng');

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
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
        $order->total_amount = $data['total_amount'];
        $order->note = $data['note'] ?? '';
        
        // Thông tin shipping nếu có (Mang về)
        if ($order->order_type === 'ONLINE_DELIVERY' || $order->order_type === 'TAKE_AWAY') {
             // Xử lý logic mang về nếu cần
        }

        // 2. Lưu Order
        $orderId = $this->orderRepo->create($order);
        
        if ($orderId) {
            // 3. Lưu Order Items
            foreach ($data['items'] as $itemData) {
                $item = new OrderItemEntity();
                $item->order_id = $orderId;
                $item->product_size_id = $itemData['size_id']; // Chỉ cần Size ID
                $item->quantity = $itemData['qty'];
                $item->price_at_purchase = $itemData['price'];
                $item->note = $itemData['notes'] ?? '';
                
                $this->orderItemRepo->create($item);
            }
            return ['success' => true, 'order_id' => $orderId, 'message' => 'Tạo đơn hàng thành công'];
        }

        return ['success' => false, 'message' => 'Lỗi khi tạo đơn hàng'];
    }

    /**
     * Tạo mã đơn hàng tự động
     */
    private function generateOrderCode() {
        return 'ORD' . date('YmdHis') . rand(100, 999);
    }

    /**
     * Lấy thông tin đơn hàng theo ID
     */
    public function getOrderById($orderId) {
        return $this->orderRepo->findById($orderId);
    }
}
?>
