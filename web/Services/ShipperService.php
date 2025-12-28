<?php
include_once './web/Repositories/ShipperRepository.php';

class ShipperService {
    private $shipperRepo;

    public function __construct() {
        $this->shipperRepo = new ShipperRepository();
    }

    /**
     * Lấy danh sách đơn hàng cần giao (READY_FOR_DELIVERY)
     */
    public function getReadyOrders() {
        return $this->shipperRepo->findReadyForDeliveryOrders();
    }

    /**
     * Chuyển trạng thái sang SHIPPING
     */
    public function startShipping($orderId) {
        // Có thể thêm logic validate ở đây nếu cần
        return $this->shipperRepo->startShipping($orderId);
    }

    /**
     * Chuyển trạng thái sang DELIVERED
     */
    public function completeDelivery($orderId) {
        return $this->shipperRepo->completeDelivery($orderId);
    }
}
?>
