<?php
/**
 * Checkout Controller - Xử lý thanh toán đơn hàng
 * Theo mô hình MVC chuẩn
 */

require_once './web/Services/CartService.php';
require_once './web/Services/CustomerService.php';
require_once './web/Services/OrderService.php';
require_once './web/Services/ProductService.php';

class CheckoutController extends Controller {
    private $cartService;
    private $customerService;
    private $orderService;
    private $productService;

    public function __construct() {
        $this->cartService = new CartService();
        $this->customerService = new CustomerService();
        $this->orderService = new OrderService();
        $this->productService = new ProductService();
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
            // Kiểm tra xem có phải "Buy Now" không
            if (isset($_POST['buy_now']) && $_POST['buy_now'] === '1') {
                // Mua ngay - chỉ checkout sản phẩm được chọn
                $productSizeId = $_POST['txtProductSizeId'] ?? null;
                $quantity = (int)($_POST['txtQuantity'] ?? 1);

                if (!$productSizeId || $quantity < 1) {
                    $_SESSION['error_message'] = 'Thông tin sản phẩm không hợp lệ!';
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
                    exit;
                }

                // Lấy thông tin sản phẩm để checkout
                $buyNowData = $this->productService->getBuyNowData($productSizeId, $quantity);

                if (!$buyNowData['success']) {
                    $_SESSION['error_message'] = $buyNowData['message'];
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
                    exit;
                }

                $cartItems = $buyNowData['items'];
                $total = $buyNowData['total'];
                $isBuyNow = true;
            } else {
                // Checkout từ giỏ hàng - lấy toàn bộ giỏ hàng
                $cartData = $this->cartService->getCart($customerId);

                if (!$cartData['success'] || empty($cartData['items'])) {
                    $_SESSION['error_message'] = 'Giỏ hàng trống!';
                    header('Location: /COFFEE_PHP/Cart/GetData');
                    exit;
                }

                $cartItems = $cartData['items'];
                $total = $cartData['total'];
                $isBuyNow = false;
            }

            // Lấy thông tin khách hàng và địa chỉ
            $customer = $this->customerService->getCustomerById($customerId);
            $customerAddress = $this->customerService->getCustomerAddress($customerId);

            // Render view - Sử dụng MasterLayout
            $this->view('UserDashBoard/MasterLayout', [
                'title' => 'Thanh Toán - Coffee House',
                'page' => 'CheckoutPage',
                'currentPage' => 'checkout',
                'additionalCSS' => [
                    'Public/Css/checkout-page.css'
                ],
                'cartItems' => $cartItems,
                'total' => $total,
                'customer' => $customer,
                'customerAddress' => $customerAddress,
                'isBuyNow' => $isBuyNow
            ]);

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi: ' . $e->getMessage();
            header('Location: /COFFEE_PHP/Cart/GetData');
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

            // Render view - Sử dụng MasterLayout
            $this->view('UserDashBoard/MasterLayout', [
                'title' => 'Thanh Toán Chuyển Khoản - Coffee House',
                'page' => 'PaymentPage',
                'currentPage' => 'payment',
                'additionalCSS' => [
                    'Public/Css/payment-page.css'
                ],
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
            $_SESSION['error_message'] = 'Lỗi: ' . $e->getMessage();
            header('Location: /COFFEE_PHP/User/index');
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

            // Render view - Sử dụng MasterLayout
            $this->view('UserDashBoard/MasterLayout', [
                'title' => 'Đặt Hàng Thành Công - Coffee House',
                'page' => 'OrderSuccessPage',
                'currentPage' => 'orderSuccess',
                'additionalCSS' => [
                    'Public/Css/order-success.css'
                ],
                'order' => $order
            ]);

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi: ' . $e->getMessage();
            header('Location: /COFFEE_PHP/User/index');
            exit;
        }
    }
}
?>
