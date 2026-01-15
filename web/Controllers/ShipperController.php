<?php
include_once './Config/Controller.php';
include_once './web/Services/ShipperService.php';

class ShipperController extends Controller {
    private $shipperService;

    public function __construct() {
        $this->shipperService = new ShipperService();
    }

    public function GetData() {
        $this->index();
    }

    public function index() {
        $this->view('ShipperDashBoard/MasterLayoutShipper', [
            'page' => 'Shipper-delivery_v',
            'orders' => $this->shipperService->getAllDeliveryOrders(),
            'title' => 'Shipper Dashboard - Coffee House'
        ]);
    }

    public function startShipping() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $orderId = $_POST['order_id'] ?? null;
        if (!$orderId) return;

        if ($this->shipperService->startShipping($orderId)) {
            header('Location: index.php?url=Shipper/index');
            exit;
        }
        echo "Lỗi: Không thể cập nhật trạng thái đơn hàng.";
    }

    public function completeDelivery() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $orderId = $_POST['order_id'] ?? null;
        if (!$orderId) return;

        if ($this->shipperService->completeDelivery($orderId)) {
            header('Location: index.php?url=Shipper/index');
            exit;
        }
        echo "Lỗi: Không thể cập nhật trạng thái đơn hàng.";
    }
}

