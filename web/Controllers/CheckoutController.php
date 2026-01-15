<?php

class CheckoutController extends Controller {
    private $customerService;
    private $orderService;

    public function __construct() {
        $this->customerService = $this->service('CustomerService');
        $this->orderService = $this->service('OrderService');
    }

    private function checkAuth() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập!';
            header('Location: /COFFEE_PHP/Auth/login');
            exit;
        }

        return $_SESSION['user']['id'];
    }

    // lay du lieu fill vao man checkout
    private function getCheckoutData() {
        // mua ngay -> lay tu productDetail
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

        throw new Exception('Vui lòng chọn sản phẩm từ giỏ hàng hoặc trang sản phẩm!');
    }

    // Hiển thị trang Checkout
    public function GetData($message = null) {
        $customerId = $this->checkAuth();

        // Hiển thị thông báo lỗi/thành công nếu có
        $this->showMessage($message);

        try {
            // Chuẩn bị dữ liệu view mặc định
            $viewData = [
                'title' => 'Thanh Toán - Coffee House',
                'page' => 'CheckoutPage',
                'currentPage' => 'checkout',
                'additionalCSS' => ['Public/Css/checkout-page.css']
            ];

            // Kiểm tra có dữ liệu lỗi từ session không (khi validate lỗi quay lại)
            if (isset($_SESSION['checkout_data']) && isset($_SESSION['checkout_items'])) {
                $checkoutData = $_SESSION['checkout_items'];
                $orderData = $_SESSION['checkout_data'];

                // Xóa session sau khi lấy
                unset($_SESSION['checkout_data'], $_SESSION['checkout_items']);

                $customer = $this->customerService->getCustomerById($customerId);

                // Merge dữ liệu đã nhập với thông tin customer
                $customer->name = $orderData['customer_name'];
                $customer->phone = $orderData['customer_phone'];
                $customer->address = $orderData['shipping_address'];

                // Thêm dữ liệu đã nhập để giữ lại
                $viewData['savedNote'] = $orderData['note'] ?? '';
                $viewData['savedPaymentMethod'] = $orderData['payment_method'] ?? '';
            } else {
                // Lấy dữ liệu checkout bình thường từ POST
                $checkoutData = $this->getCheckoutData();
                $customer = $this->customerService->getCustomerById($customerId);
            }

            // Gộp dữ liệu chung
            $viewData['cartItems'] = $checkoutData['items'];
            $viewData['total'] = $checkoutData['total'];
            $viewData['customer'] = $customer;
            $viewData['isBuyNow'] = $checkoutData['isBuyNow'];

            $this->view('UserDashBoard/MasterLayout', $viewData);

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /COFFEE_PHP/Cart/GetData');
            exit;
        }
    }

    // ========== PRIVATE HELPER METHODS ==========

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

    public function placeOrder() {
        $customerId = $this->checkAuth();

        // Lấy items trực tiếp từ getCheckoutData (không cần hàm riêng)
        $checkoutData = $this->getCheckoutData();
        // lay name,phone,...
        $orderData = $this->prepareOrderData();
        $orderData['items'] = $checkoutData['items']; // Dùng lại items từ getCheckoutData

        $result = $this->orderService->createOrderFromCheckout($customerId, $orderData);

        if (!$result['success']) {
            $_SESSION['checkout_data'] = $orderData;
            $_SESSION['checkout_items'] = $checkoutData;
            // Gọi lại GetData với message lỗi
            return $this->GetData($result);
        }

        // Thành công - redirect đến trang success
        header('Location: /COFFEE_PHP/Checkout/orderSuccess?order_id=' . $result['order_id']);
        exit;
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
