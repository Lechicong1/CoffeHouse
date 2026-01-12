<?php
include_once './web/Repositories/OrderRepository.php';
include_once './web/Services/OrderService.php';

class ShipperService {
    private $orderRepo;
    private $orderService;

    public function __construct() {
        $this->orderRepo = new OrderRepository();
        $this->orderService = new OrderService();
    }

    public function getAllDeliveryOrders() {
        return $this->orderRepo->findReadyForDeliveryOrders();
    }

    public function startShipping($orderId) {
        return $this->orderService->updateOrderStatus($orderId, 'SHIPPING');
    }

    public function completeDelivery($orderId) {
        return $this->orderService->updateOrderStatus($orderId, 'COMPLETED');
    }
}


