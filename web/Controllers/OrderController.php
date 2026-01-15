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

    /**
     * Hiển thị thông báo lỗi hoặc thành công
     */
    private function showMessage($message) {
        if ($message) {
            $messageText = addslashes($message['message']);
            if ($message['success']) {
                echo "<script>alert('$messageText')</script>";
            } else {
                echo "<script>alert('Lỗi: $messageText')</script>";
            }
        }
    }

    function GetData($message = null) {
        $customerId = $this->checkAuth();

        // Hiển thị thông báo nếu có
        $this->showMessage($message);

        // Lấy danh sách đơn hàng của khách hàng
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
            return $this->GetData([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ]);
        }
        // Gọi service để xử lý logic hủy đơn
        $result = $this->orderService->cancelOrder($orderId, $customerId);

        // Hiển thị kết quả
        return $this->GetData($result);
    }
}
?>
