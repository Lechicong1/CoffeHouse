<?php
/**
 * Checkout Controller - Xử lý thanh toán đơn hàng
 * Refactored để ngắn gọn và dễ bảo trì
 */

class CheckoutController extends Controller {
    private $cartService;
    private $customerService;
    private $orderService;
    private $productService;

    public function __construct() {
        $this->cartService = $this->service('CartService');
        $this->customerService = $this->service('CustomerService');
        $this->orderService = $this->service('OrderService');
        $this->productService = $this->service('ProductService');
    }

    // Helper: Kiểm tra auth và trả về customerId
    private function checkAuth() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập!';
            header('Location: /COFFEE_PHP/Auth/login');
            exit;
        }

        return $_SESSION['user']['id'];
    }

    // Lấy dữ liệu checkout (Buy Now hoặc từ Giỏ hàng)
    private function getCheckoutData($customerId) {
        // Nếu là Buy Now -> lấy trực tiếp từ POST
        if (isset($_POST['buy_now']) && $_POST['buy_now'] === '1') {
            $productSizeId = $_POST['txtProductSizeId'] ?? null;
            $quantity = (int)($_POST['txtQuantity'] ?? 1);
            $productName = $_POST['txtProductName'] ?? null;
            $price = (float)($_POST['txtPrice'] ?? 0);

            if (!$productSizeId || $quantity < 1 || !$productName || $price <= 0) {
                throw new Exception('Thông tin sản phẩm không hợp lệ!');
            }

            $item = (object)[
                'product_name' => $productName,
                'quantity' => $quantity,
                'price' => $price,
                'product_size_id' => $productSizeId
            ];

            return [
                'items' => [$item],
                'total' => $price * $quantity,
                'isBuyNow' => true
            ];
        }

        // Lấy từ Giỏ hàng
        if (isset($_POST['cart_product_name']) && isset($_POST['txtTotalAmount'])) {
            $productNames = $_POST['cart_product_name'];
            $productSizeIds = $_POST['cart_product_size_id'];
            $quantities = $_POST['cart_quantity'];
            $prices = $_POST['cart_price'];

            if (empty($productNames)) {
                throw new Exception('Giỏ hàng trống!');
            }

            // Tạo items từ array
            $items = [];
            for ($i = 0; $i < count($productNames); $i++) {
                $items[] = (object)[
                    'product_name' => $productNames[$i],
                    'quantity' => (int)$quantities[$i],
                    'price' => (float)$prices[$i],
                    'product_size_id' => (int)$productSizeIds[$i]
                ];
            }

            return [
                'items' => $items,
                'total' => (float)$_POST['txtTotalAmount'],
                'isBuyNow' => false
            ];
        }

        // Fallback: Nếu không có dữ liệu POST
        throw new Exception('Vui lòng chọn sản phẩm từ giỏ hàng hoặc trang sản phẩm!');
    }

    // Hiển thị trang Checkout
    public function GetData() {
        $customerId = $this->checkAuth();

        try {
            $checkoutData = $this->getCheckoutData($customerId);
            $customer = $this->customerService->getCustomerById($customerId);

            $this->view('UserDashBoard/MasterLayout', [
                'title' => 'Thanh Toán - Coffee House',
                'page' => 'CheckoutPage',
                'currentPage' => 'checkout',
                'additionalCSS' => ['Public/Css/checkout-page.css'],
                'cartItems' => $checkoutData['items'],
                'total' => $checkoutData['total'],
                'customer' => $customer,
                'isBuyNow' => $checkoutData['isBuyNow']
            ]);

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /COFFEE_PHP/Cart/GetData');
            exit;
        }
    }

    // Chuẩn bị dữ liệu đơn hàng từ POST
    private function prepareOrderData() {
        return [
            'customer_name' => trim($_POST['txtCustomerName']),
            'customer_phone' => trim($_POST['txtCustomerPhone']),
            'shipping_address' => trim($_POST['txtShippingAddress']),
            'note' => trim($_POST['txtNote'] ?? ''),
            'payment_method' => $_POST['payment_method'],
            'total_amount' => (float)$_POST['txtTotalAmount'],
            'voucher' => [
                'voucher_id' => !empty($_POST['applied_voucher_id']) ? (int)$_POST['applied_voucher_id'] : null
            ],
            'is_buy_now' => isset($_POST['is_buy_now']) && $_POST['is_buy_now'] === '1' // Để biết có xóa giỏ hàng không
        ];
    }

    // Xử lý đặt hàng
    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/Checkout/GetData');
            exit;
        }

        $customerId = $this->checkAuth();

        try {
            // Lấy items trực tiếp từ getCheckoutData (không cần hàm riêng)
            $checkoutData = $this->getCheckoutData($customerId);

            $orderData = $this->prepareOrderData();
            $orderData['items'] = $checkoutData['items']; // Dùng lại items từ getCheckoutData



            // Tạo đơn hàng
            $result = $this->orderService->createOrderFromCheckout($customerId, $orderData);

            if (!$result['success']) {
                throw new Exception($result['message'] ?? 'Không xác định');
            }

            // Thành công
            $_SESSION['success_message'] = 'Đặt hàng thành công! Mã đơn hàng: ' . $result['order_code'];
            header('Location: /COFFEE_PHP/Checkout/orderSuccess?order_id=' . $result['order_id']);
            exit;

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi đặt hàng: ' . $e->getMessage();
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/Checkout/GetData'));
            exit;
        }
    }

    // Trang thanh toán chuyển khoản với QR Code VietQR
    public function payment() {
        $customerId = $this->checkAuth();
        $orderId = $_GET['order_id'] ?? null;

        if (!$orderId) {
            $_SESSION['error_message'] = 'Không tìm thấy đơn hàng!';
            header('Location: /COFFEE_PHP/User/index');
            exit;
        }

        try {
            $order = $this->orderService->getOrderById($orderId);
            $qrUrl = $this->generateVietQRUrl($_GET['order_code'] ?? '', $_GET['amount'] ?? 0);

            $this->view('UserDashBoard/MasterLayout', [
                'title' => 'Thanh Toán Chuyển Khoản - Coffee House',
                'page' => 'PaymentPage',
                'currentPage' => 'payment',
                'additionalCSS' => ['Public/Css/payment-page.css'],
                'order' => $order,
                'qrUrl' => $qrUrl,
                'bankInfo' => [
                    'bankName' => 'MBBank',
                    'accountNo' => '88221020056868',
                    'accountName' => 'COFFEE HOUSE',
                    'amount' => number_format($_GET['amount'] ?? 0, 0, ',', '.'),
                    'description' => 'Thanh toan don hang ' . ($_GET['order_code'] ?? '')
                ]
            ]);

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /COFFEE_PHP/User/index');
            exit;
        }
    }

    // Helper: Tạo URL VietQR
    private function generateVietQRUrl($orderCode, $amount) {
        $bankId = 'MB';
        $accountNo = '88221020056868';
        $accountName = 'COFFEE HOUSE';
        $description = urlencode('Thanh toan don hang ' . $orderCode);
        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$description}&accountName={$accountName}";
    }

    // Trang xác nhận đơn hàng thành công
    public function orderSuccess() {
        $customerId = $this->checkAuth();
        $orderId = $_GET['order_id'] ?? null;

        if (!$orderId) {
            $_SESSION['error_message'] = 'Không tìm thấy đơn hàng!';
            header('Location: /COFFEE_PHP/User/index');
            exit;
        }

        try {
            $order = $this->orderService->getOrderById($orderId);

            $this->view('UserDashBoard/MasterLayout', [
                'title' => 'Đặt Hàng Thành Công - Coffee House',
                'page' => 'OrderSuccessPage',
                'currentPage' => 'orderSuccess',
                'additionalCSS' => ['Public/Css/order-success.css'],
                'order' => $order
            ]);

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /COFFEE_PHP/User/index');
            exit;
        }
    }
}
?>
