<?php
include_once './web/Repositories/ShipperRepository.php';
include_once './web/Services/OrderService.php';

class ShipperService {
    private $shipperRepo;
    private $orderService;

    public function __construct() {
        $this->shipperRepo = new ShipperRepository();
        $this->orderService = new OrderService();
    }

    /**
     * Lấy danh sách đơn hàng cần giao (READY)
     */
    public function getReadyOrders() {
        return $this->shipperRepo->findReadyForDeliveryOrders();
    }

    /**
     * Chuyển trạng thái sang SHIPPING
     */
    public function startShipping($orderId) {
        return $this->orderService->updateOrderStatus($orderId, 'SHIPPING');
    }

    /**
     * Chuyển trạng thái sang COMPLETED
     * Gọi OrderService để tái sử dụng logic cộng điểm
     */
    public function completeDelivery($orderId) {
        return $this->orderService->updateOrderStatus($orderId, 'COMPLETED');
    }
}
?>
