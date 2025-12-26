<?php
include_once './web/Repositories/OrderRepository.php';
include_once './web/Repositories/OrderItemRepository.php';
include_once './web/Entity/OrderEntity.php';
include_once './web/Entity/OrderItemEntity.php';

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
                $item->product_id = $itemData['id']; // Product ID
                $item->product_size_id = $itemData['size_id']; // Size ID
                $item->quantity = $itemData['qty'];
                $item->price_at_purchase = $itemData['price'];
                $item->note = $itemData['notes'] ?? '';
                
                $this->orderItemRepo->create($item);
            }
            return ['success' => true, 'order_id' => $orderId, 'message' => 'Tạo đơn hàng thành công'];
        }

        return ['success' => false, 'message' => 'Lỗi khi tạo đơn hàng'];
    }
}
?>
