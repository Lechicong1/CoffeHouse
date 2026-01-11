<?php
include_once './Config/Controller.php';

/**
 * Order Controller - Quản lý đơn hàng của khách hàng
 * Theo mô hình MVC chuẩn
 */
class OrderController extends Controller {
    private $orderService;

    function __construct() {
        $this->orderService = $this->service('OrderService');
    }

    /**
     * Kiểm tra đăng nhập
     */
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

    /**
     * Hiển thị danh sách đơn hàng của khách hàng
     */
    function GetData() {
        $customerId = $this->checkAuth();

        // Lấy danh sách đơn hàng của khách hàng
        $orders = $this->orderService->getOrderRepo()->findByCustomerId($customerId);

        $this->view('UserDashBoard/MasterLayout', [
            'title' => 'Đơn Hàng Của Tôi - Coffee House',
            'page' => 'MyOrdersPage',
            'currentPage' => 'myOrders',
            'additionalCSS' => ['Public/Css/my-orders.css'],
            'orders' => $orders
        ]);
    }

    /**
     * Hủy đơn hàng
     */
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
