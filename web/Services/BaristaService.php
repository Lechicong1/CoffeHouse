<?php
include_once './web/Repositories/BaristaRepository.php';
include_once './web/Repositories/OrderItemRepository.php';
include_once './Enums/status.enum.php';

class BaristaService {
    private $baristaRepo;
    private $orderItemRepo;

    public function __construct() {
        $this->baristaRepo = new BaristaRepository();
        $this->orderItemRepo = new OrderItemRepository();
    }

    /**
     * Lấy danh sách đơn hàng cho Barista
     */
    public function getBaristaOrders() {
        $orders = $this->baristaRepo->findBaristaOrders();
        foreach ($orders as $order) {
            $order->items = $this->orderItemRepo->findByOrderId($order->id);
        }
        return $orders;
    }

    /**
     * Barista nhận đơn -> Chuyển sang PREPARING
     */
    public function acceptOrder($orderId) {
        return $this->baristaRepo->updateStatus($orderId, OrderStatus::PREPARING);
    }

    /**
     * Barista hoàn thành đơn -> Chuyển sang READY
     */
    public function completeOrder($orderId) {
        // Tùy thuộc vào logic, nếu là đơn online thì READY, tại chỗ thì COMPLETED
        // Ở đây tạm thời set là READY để Shipper có thể thấy
        return $this->baristaRepo->updateStatus($orderId, OrderStatus::READY);
    }
}
?>
