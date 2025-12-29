<?php
include_once './Config/Controller.php';
include_once './web/Services/ShipperService.php';

class ShipperController extends Controller {
    private $shipperService;

    public function __construct() {
        $this->shipperService = new ShipperService();
    }

    /**
     * Default Action: Chuyển hướng đến trang chính (index)
     * URL: http://localhost/COFFEE_PHP/ShipperController
     */
    public function GetData() {
        $this->index();
    }

    /**
     * Hiển thị danh sách đơn hàng cần giao
     * URL: index.php?controller=shipper&action=index
     */
    public function index() {
        // Kiểm tra đăng nhập (nếu cần)
        // if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'shipper') { ... }

        $orders = $this->shipperService->getReadyOrders();
        
        // Render view MasterLayoutShipper
        $this->view('ShipperDashBoard/MasterLayoutShipper', [
            'page' => 'Shipper-delivery_v',
            'orders' => $orders,
            'title' => 'Shipper Dashboard - Coffee House'
        ]);
    }

    /**
     * Xử lý nhận đơn (READY -> SHIPPING)
     * URL: index.php?url=Shipper/startShipping
     * Method: POST
     */
    public function startShipping() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            
            if ($orderId) {
                $result = $this->shipperService->startShipping($orderId);
                
                if ($result) {
                    // Redirect lại trang danh sách
                    header('Location: index.php?url=Shipper/index');
                    exit;
                } else {
                    echo "Lỗi: Không thể cập nhật trạng thái đơn hàng.";
                }
            }
        }
    }

    /**
     * Xử lý hoàn thành đơn (SHIPPING -> COMPLETED)
     * URL: index.php?url=Shipper/completeDelivery
     * Method: POST
     */
    public function completeDelivery() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            
            if ($orderId) {
                $result = $this->shipperService->completeDelivery($orderId);
                
                if ($result) {
                    // Redirect lại trang danh sách
                    header('Location: index.php?url=Shipper/index');
                    exit;
                } else {
                    echo "Lỗi: Không thể cập nhật trạng thái đơn hàng.";
                }
            }
        }
    }
}
?>
