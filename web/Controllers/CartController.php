<?php
/**
 * FILE: CartController.php
 * DESCRIPTION: Controller xử lý các request liên quan đến giỏ hàng
 * AUTHOR: Coffee House System
 */

include_once './web/Services/CartService.php';

class CartController extends Controller {
    private $cartService;

    public function __construct() {
        $this->cartService = new CartService();
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * Method: POST
     * Body: product_id, size_id, quantity
     */
    public function addToCart() {
        header('Content-Type: application/json');

        // Kiểm tra đăng nhập
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng',
                'redirect' => '/COFFEE_PHP/Auth/login'
            ]);
            return;
        }

        $customerId = $_SESSION['user']['id'];

        // Lấy dữ liệu từ request
        $data = json_decode(file_get_contents('php://input'), true);

        $productSizeId = $data['product_size_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        // Validate
        if (!$productSizeId) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin sản phẩm'
            ]);
            return;
        }

        // Thêm vào giỏ hàng
        $result = $this->cartService->addToCart($customerId, $productSizeId, $quantity);

        // Lấy số lượng items trong giỏ hàng
        $cartCount = $this->cartService->getCartCount($customerId);
        $result['cart_count'] = $cartCount['count'];

        echo json_encode($result);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng từ FORM (không phải JSON API)
     * Method: POST Form Submit
     * Params: product_id, size_id, quantity, buy_now
     */
    public function addToCartForm() {
        // Kiểm tra đăng nhập
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Enable error reporting để debug
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try {
            if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
                // Chưa đăng nhập -> redirect về trang login
                $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu';
                $_SESSION['error_message'] = 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng';
                header('Location: /COFFEE_PHP/Auth/login');
                exit();
            }

            $customerId = $_SESSION['user']['id'];

            // Lấy dữ liệu từ POST
            $productId = $_POST['product_id'] ?? null;
            $productSizeId = $_POST['product_size_id'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);
            $buyNow = $_POST['buy_now'] ?? '0';

            // Debug log
            error_log("AddToCartForm - Customer: $customerId, Product: $productId, Size: $productSizeId, Qty: $quantity");

            // Validate
            if (!$productId || !$productSizeId) {
                $_SESSION['error_message'] = 'Vui lòng chọn size sản phẩm';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
                exit();
            }

            // Thêm vào giỏ hàng
            $result = $this->cartService->addToCart($customerId, $productSizeId, $quantity);

            error_log("AddToCart Result: " . json_encode($result));

            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];

                // Nếu là "Mua ngay" thì chuyển đến trang checkout
                if ($buyNow === '1') {
                    header('Location: /COFFEE_PHP/User/checkout');
                } else {
                    // Nếu là "Thêm vào giỏ" thì quay lại trang chi tiết hoặc giỏ hàng
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/cart'));
                }
            } else {
                $_SESSION['error_message'] = $result['message'];
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
            }
        } catch (Exception $e) {
            error_log("AddToCartForm Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
        }
        exit();
    }

    /**
     * Lấy giỏ hàng của customer
     * Method: GET
     */
    public function getCart() {
        header('Content-Type: application/json');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }

        $customerId = $_SESSION['user']['id'];
        $result = $this->cartService->getCart($customerId);

        echo json_encode($result);
    }

    /**
     * Cập nhật số lượng sản phẩm
     * Method: POST
     * Body: cart_item_id, quantity
     */
    public function updateQuantity() {
        header('Content-Type: application/json');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $cartItemId = $data['cart_item_id'] ?? null;
        $quantity = $data['quantity'] ?? 0;

        if (!$cartItemId) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin'
            ]);
            return;
        }

        $result = $this->cartService->updateQuantity($cartItemId, $quantity);

        // Lấy lại giỏ hàng
        $customerId = $_SESSION['user']['id'];
        $cart = $this->cartService->getCart($customerId);
        $result['cart'] = $cart;

        echo json_encode($result);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     * Method: POST
     * Body: cart_item_id
     */
    public function removeItem() {
        header('Content-Type: application/json');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $cartItemId = $data['cart_item_id'] ?? null;

        if (!$cartItemId) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin'
            ]);
            return;
        }

        $result = $this->cartService->removeItem($cartItemId);

        // Lấy lại giỏ hàng
        $customerId = $_SESSION['user']['id'];
        $cart = $this->cartService->getCart($customerId);
        $result['cart'] = $cart;

        echo json_encode($result);
    }

    /**
     * Lấy số lượng items trong giỏ hàng
     * Method: GET
     */
    public function getCartCount() {
        header('Content-Type: application/json');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            echo json_encode([
                'success' => true,
                'count' => 0
            ]);
            return;
        }

        $customerId = $_SESSION['user']['id'];
        $result = $this->cartService->getCartCount($customerId);

        echo json_encode($result);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     * Method: POST
     */
    public function clearCart() {
        header('Content-Type: application/json');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }

        $customerId = $_SESSION['user']['id'];
        $result = $this->cartService->clearCart($customerId);

        echo json_encode($result);
    }

    /**
     * Cập nhật số lượng từ FORM
     * Method: POST Form Submit
     */
    public function updateCartItemForm() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập';
            header('Location: /COFFEE_PHP/Auth/login');
            exit();
        }

        $cartItemId = $_POST['cart_item_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (!$cartItemId) {
            $_SESSION['error_message'] = 'Thiếu thông tin';
            header('Location: /COFFEE_PHP/User/cart');
            exit();
        }

        $result = $this->cartService->updateQuantity($cartItemId, $quantity);

        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }

        header('Location: /COFFEE_PHP/User/cart');
        exit();
    }

    /**
     * Xóa sản phẩm từ FORM
     * Method: POST Form Submit
     */
    public function removeItemForm() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập';
            header('Location: /COFFEE_PHP/Auth/login');
            exit();
        }

        $cartItemId = $_POST['cart_item_id'] ?? null;

        if (!$cartItemId) {
            $_SESSION['error_message'] = 'Thiếu thông tin';
            header('Location: /COFFEE_PHP/User/cart');
            exit();
        }

        $result = $this->cartService->removeItem($cartItemId);

        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }

        header('Location: /COFFEE_PHP/User/cart');
        exit();
    }
}
?>
