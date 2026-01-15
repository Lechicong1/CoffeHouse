<?php

include_once './web/Services/CartService.php';

class CartController extends Controller {
    private $cartService;

    public function __construct() {
        $this->cartService = new CartService();
    }


    private function checkAuth() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập để thực hiện thao tác này';
            header('Location: /COFFEE_PHP/Auth/login');
            exit();
        }

        return $_SESSION['user']['id'];
    }

    public function GetData() {
        $customerId = $this->checkAuth();

        try {
            $cartData = $this->cartService->getCart($customerId);

            $this->view('UserDashBoard/MasterLayout', [
                'title' => 'Giỏ Hàng - Coffee House',
                'page' => 'CartPage',
                'currentPage' => 'cart',
                'additionalCSS' => [
                    'Public/Css/cart-page.css'
                ],
                'cartItems' => $cartData['items'] ?? [],
                'total' => $cartData['total'] ?? 0,
                'count' => $cartData['count'] ?? 0
            ]);

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /COFFEE_PHP/User/menu');
            exit();
        }
    }


    public function ins() {
        $customerId = $this->checkAuth();

        try {
            $productSizeId = $_POST['txtProductSizeId'] ?? null;
            $quantity = (int)($_POST['txtQuantity'] ?? 1);

            $result = $this->cartService->addToCart($customerId, $productSizeId, $quantity);

            $_SESSION[$result['success'] ? 'success_message' : 'error_message'] = $result['message'];

            $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/Cart/GetData';
            header('Location: ' . $redirectUrl);
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
        }
        exit();
    }

    public function upd() {


        $customerId = $this->checkAuth();

        try {
            $cartItemId = $_POST['txtCartItemId'] ?? null;
            $quantity = (int)($_POST['txtQuantity'] ?? 0);

            $result = $this->cartService->updateQuantity( $cartItemId, $quantity);

            $_SESSION[$result['success'] ? 'success_message' : 'error_message'] = $result['message'];

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        // Luôn redirect về giỏ hàng (URL đúng)
        header('Location: /COFFEE_PHP/Cart/GetData');
        exit();
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (POST)
     */
    public function del() {
        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btnXoa'])) {
            header('Location: /COFFEE_PHP/Cart/GetData');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            // Lấy dữ liệu từ request
            $cartItemId = $_POST['txtCartItemId'] ?? null;

            // Gọi Service xử lý
            $result = $this->cartService->removeItem( $cartItemId);

            // Set message
            $_SESSION[$result['success'] ? 'success_message' : 'error_message'] = $result['message'];

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        // Redirect về giỏ hàng (URL đúng)
        header('Location: /COFFEE_PHP/Cart/GetData');
        exit();
    }

    /**
     * Xóa toàn bộ giỏ hàng (POST)
     */
    public function clear() {
        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btnXoaTatCa'])) {
            header('Location: /COFFEE_PHP/Cart/GetData');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            // Gọi Service xử lý
            $result = $this->cartService->clearCart($customerId);

            // Set message
            $_SESSION[$result['success'] ? 'success_message' : 'error_message'] = $result['message'];

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        // Redirect về giỏ hàng (URL đúng)
        header('Location: /COFFEE_PHP/Cart/GetData');
        exit();
    }
}
?>
