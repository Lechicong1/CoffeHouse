<?php
include_once './Config/Controller.php';

class OrderController extends Controller {
    private $orderService;

    function __construct() {
        $this->orderService = $this->service('OrderService');
    }

    private function checkAuth() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            header('Location: /COFFEE_PHP/Auth/login');
            exit;
        }

        return $_SESSION['user']['id'];
    }

    function GetData() {
        $customerId = $this->checkAuth();

        // Lấy danh sách đơn hàng của khách hàng (gọi trực tiếp repo từ controller)
        $orders = $this->orderService->findByCustomerId($customerId);

        $this->view('UserDashBoard/MasterLayout', [
            'title' => 'Đơn Hàng Của Tôi - Coffee House',
            'page' => 'MyOrdersPage',
            'currentPage' => 'myOrders',
            'additionalCSS' => ['Public/Css/my-orders.css'],
            'orders' => $orders
        ]);
    }

    function cancelOrder() {
        $customerId = $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=OrderController/GetData');
            exit;
        }

        $orderId = $_POST['order_id'] ?? null;

        if (!$orderId) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng';
            header('Location: ?url=OrderController/GetData');
            exit;
        }

        // Gọi service để xử lý logic hủy đơn
        $result = $this->orderService->cancelOrder($orderId, $customerId);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: ?url=OrderController/GetData');
        exit;
    }
}
?>
