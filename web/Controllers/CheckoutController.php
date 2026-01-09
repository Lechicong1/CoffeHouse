<?php
/**
 * Checkout Controller - Xử lý thanh toán đơn hàng
 * Theo mô hình MVC chuẩn
 */

require_once './web/Services/CartService.php';
require_once './web/Services/CustomerService.php';
require_once './web/Services/OrderService.php';

class CheckoutController extends Controller {
    private $cartService;
    private $customerService;
    private $orderService;

    public function __construct() {
        $this->cartService = new CartService();
        $this->customerService = new CustomerService();
        $this->orderService = new OrderService();
    }

    /**
     * Kiểm tra đăng nhập và lấy customer ID
     * @return int|null
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
     * Hiển thị trang checkout (Method mặc định - giống GetData của Employee)
     */
    public function GetData() {
        $customerId = $this->checkAuth();

        try {
            // Lấy thông tin giỏ hàng
            $cartData = $this->cartService->getCart($customerId);

            if (!$cartData['success'] || empty($cartData['items'])) {
                echo "<script>alert('Giỏ hàng trống!'); window.location.href='/COFFEE_PHP/Cart/index';</script>";
                exit;
            }

            // Lấy thông tin khách hàng và địa chỉ
            $customer = $this->customerService->getCustomerById($customerId);
            $customerAddress = $this->customerService->getCustomerAddress($customerId);

            // Render view
            $this->view('UserDashBoard/Pages/CheckoutPage', [
                'title' => 'Thanh Toán - Coffee House',
                'cartItems' => $cartData['items'],
                'total' => $cartData['total'],
                'customer' => $customer,
                'customerAddress' => $customerAddress
            ]);

        } catch (Exception $e) {
            echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "'); window.location.href='/COFFEE_PHP/Cart/index';</script>";
            exit;
        }
    }

    /**
     * Xử lý đặt hàng (POST) - Giống như Employee/ins, upd, del
     */
    public function placeOrder() {
        // Debug: Kiểm tra POST data
        error_log("=== CHECKOUT DEBUG ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("btnDatHang isset: " . (isset($_POST['btnDatHang']) ? 'YES' : 'NO'));

        // Kiểm tra phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Not POST request, redirecting to GetData");
            header('Location: /COFFEE_PHP/Checkout/GetData');
            exit;
        }

        $customerId = $this->checkAuth();

        try {
            // Validate dữ liệu
            $requiredFields = ['txtCustomerName', 'txtCustomerPhone', 'txtShippingAddress', 'payment_method', 'txtTotalAmount'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Vui lòng điền đầy đủ thông tin: $field");
                }
            }

            // Chuẩn bị dữ liệu đơn hàng
            $orderData = [
                'customer_name' => trim($_POST['txtCustomerName']),
                'customer_phone' => trim($_POST['txtCustomerPhone']),
                'shipping_address' => trim($_POST['txtShippingAddress']),
                'note' => trim($_POST['txtNote'] ?? ''),
                'payment_method' => $_POST['payment_method'],
                'total_amount' => (float)$_POST['txtTotalAmount'],
                'voucher' => [ 'voucher_id' => isset($_POST['applied_voucher_id']) && $_POST['applied_voucher_id'] !== '' ? (int)$_POST['applied_voucher_id'] : null ],
                'order_type' => 'ONLINE_DELIVERY'
            ];

            error_log("Order data prepared: " . print_r($orderData, true));

            // Tạo đơn hàng thông qua Service
            $result = $this->orderService->createOrderFromCheckout($customerId, $orderData);

            error_log("Order result: " . print_r($result, true));

            if ($result['success']) {
                // Xử lý theo phương thức thanh toán
                if ($orderData['payment_method'] === 'CASH') {
                    echo "<script>
                        alert('Đặt hàng thành công!');
                        window.location.href = '/COFFEE_PHP/Checkout/orderSuccess?order_id={$result['order_id']}';
                    </script>";
                } else {
                    echo "<script>
                        window.location.href = '/COFFEE_PHP/Checkout/payment?order_id={$result['order_id']}&order_code=" . urlencode($result['order_code']) . "&amount={$orderData['total_amount']}';
                    </script>";
                }
                exit;
            } else {
                echo "<script>
                    alert('Lỗi: " . addslashes($result['message']) . "');
                    window.history.back();
                </script>";
                exit;
            }

        } catch (Exception $e) {
            error_log("Exception in placeOrder: " . $e->getMessage());
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.history.back();
            </script>";
            exit;
        }
    }

    /**
     * Trang thanh toán chuyển khoản với QR Code VietQR
     */
    public function payment() {
        $customerId = $this->checkAuth();

        $orderId = $_GET['order_id'] ?? null;
        $orderCode = $_GET['order_code'] ?? '';
        $amount = $_GET['amount'] ?? 0;

        if (!$orderId) {
            header('Location: /COFFEE_PHP/User/index');
            exit;
        }

        try {
            // Lấy thông tin đơn hàng
            $order = $this->orderService->getOrderById($orderId);

            // Tạo URL QR VietQR
            $bankId = 'MB'; // MBBank
            $accountNo = '88221020056868';
            $accountName = 'COFFEE HOUSE';
            $description = urlencode('Thanh toan don hang ' . $orderCode);
            $vietQRUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$description}&accountName={$accountName}";

            // Render view
            $this->view('UserDashBoard/Pages/PaymentPage', [
                'title' => 'Thanh Toán Chuyển Khoản - Coffee House',
                'order' => $order,
                'qrUrl' => $vietQRUrl,
                'bankInfo' => [
                    'bankName' => 'MBBank',
                    'accountNo' => $accountNo,
                    'accountName' => $accountName,
                    'amount' => number_format($amount, 0, ',', '.'),
                    'description' => 'Thanh toan don hang ' . $orderCode
                ]
            ]);

        } catch (Exception $e) {
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.location.href = '/COFFEE_PHP/User/index';
            </script>";
            exit;
        }
    }

    /**
     * Trang xác nhận đơn hàng thành công
     */
    public function orderSuccess() {
        $customerId = $this->checkAuth();

        $orderId = $_GET['order_id'] ?? null;

        if (!$orderId) {
            header('Location: /COFFEE_PHP/User/index');
            exit;
        }

        try {
            // Lấy thông tin đơn hàng
            $order = $this->orderService->getOrderById($orderId);

            // Render view
            $this->view('UserDashBoard/Pages/OrderSuccessPage', [
                'title' => 'Đặt Hàng Thành Công - Coffee House',
                'order' => $order
            ]);

        } catch (Exception $e) {
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.location.href = '/COFFEE_PHP/User/index';
            </script>";
            exit;
        }
    }
}
?>
