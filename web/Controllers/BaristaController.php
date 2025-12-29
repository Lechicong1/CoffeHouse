<?php
include_once './Config/Controller.php';
include_once './web/Services/BaristaService.php';

class BaristaController extends Controller {
    private $baristaService;

    public function __construct() {
        $this->baristaService = new BaristaService();
    }

    /**
     * Default Action
     */
    public function GetData() {
        $this->index();
    }

    /**
     * Hiển thị Dashboard Barista
     */
    public function index() {
        // Kiểm tra quyền truy cập nếu cần
        
        $orders = $this->baristaService->getBaristaOrders();
        
        $this->view('BaristaDashBoard/MasterLayoutBarista', [
            'page' => 'Barista-dashboard_v',
            'orders' => $orders,
            'title' => 'Barista Dashboard - Coffee House'
        ]);
    }

    /**
     * Nhận đơn (PENDING -> PROCESSING)
     */
    public function acceptOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            if ($orderId) {
                $this->baristaService->acceptOrder($orderId);
            }
        }
        // Redirect back
        header('Location: index.php?url=Barista/index');
    }

    /**
     * Hoàn thành đơn (PREPARING -> READY)
     */
    public function completeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            if ($orderId) {
                $this->baristaService->completeOrder($orderId);
            }
        }
        // Redirect back
        header('Location: index.php?url=Barista/index');
    }
}
?>
