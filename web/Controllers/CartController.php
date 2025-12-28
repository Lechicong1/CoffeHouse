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
     * Kiểm tra đăng nhập
     * @return int Customer ID nếu đã đăng nhập
     */
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

    /**
     * Hiển thị trang giỏ hàng (Method mặc định - giống GetData của Employee)
     */
    public function GetData() {
        $customerId = $this->checkAuth();

        try {
            // Lấy dữ liệu giỏ hàng từ Service
            $cartData = $this->cartService->getCart($customerId);

            // Render view giỏ hàng
            $this->view('UserDashBoard/Pages/CartPage', [
                'title' => 'Giỏ Hàng - Coffee House',
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

    /**
     * Thêm sản phẩm vào giỏ hàng (POST)
     * Giống Employee/ins
     */
    public function ins() {
        if (!isset($_POST['btnThemGioHang'])) {
            header('Location: /COFFEE_PHP/User/menu');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            // Lấy dữ liệu từ form
            $productSizeId = $_POST['txtProductSizeId'] ?? null;
            $quantity = (int)($_POST['txtQuantity'] ?? 1);
            $buyNow = $_POST['buy_now'] ?? '0';

            // Validate
            if (!$productSizeId) {
                $_SESSION['error_message'] = 'Vui lòng chọn size sản phẩm';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
                exit();
            }

            if ($quantity < 1) {
                $_SESSION['error_message'] = 'Số lượng phải lớn hơn 0';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
                exit();
            }

            // Thêm vào giỏ hàng
            $result = $this->cartService->addToCart($customerId, $productSizeId, $quantity);

            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];

                // Nếu là "Mua ngay" thì chuyển đến trang checkout
                if ($buyNow === '1') {
                    header('Location: /COFFEE_PHP/Checkout/GetData');
                } else {
                    // Nếu là "Thêm vào giỏ" thì quay lại trang trước
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/cart'));
                }
            } else {
                $_SESSION['error_message'] = $result['message'];
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/User/menu'));
        }
        exit();
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ (POST)
     * Giống Employee/upd
     */
    public function upd() {
        if (!isset($_POST['btnCapnhat'])) {
            header('Location: /COFFEE_PHP/User/cart');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            $cartItemId = $_POST['txtCartItemId'] ?? null;
            $quantity = (int)($_POST['txtQuantity'] ?? 0);

            // Validate
            if (!$cartItemId) {
                $_SESSION['error_message'] = 'Thiếu thông tin sản phẩm';
                header('Location: /COFFEE_PHP/User/cart');
                exit();
            }

            if ($quantity < 1) {
                $_SESSION['error_message'] = 'Số lượng phải lớn hơn 0';
                header('Location: /COFFEE_PHP/User/cart');
                exit();
            }

            // Cập nhật số lượng
            $result = $this->cartService->updateQuantity($cartItemId, $quantity);

            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

            header('Location: /COFFEE_PHP/User/cart');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /COFFEE_PHP/User/cart');
        }
        exit();
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (POST)
     * Giống Employee/del
     */
    public function del() {
        if (!isset($_POST['btnXoa'])) {
            header('Location: /COFFEE_PHP/User/cart');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            $cartItemId = $_POST['txtCartItemId'] ?? null;

            // Validate
            if (!$cartItemId) {
                $_SESSION['error_message'] = 'Thiếu thông tin sản phẩm';
                header('Location: /COFFEE_PHP/User/cart');
                exit();
            }

            // Xóa sản phẩm
            $result = $this->cartService->removeItem($cartItemId);

            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

            header('Location: /COFFEE_PHP/User/cart');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /COFFEE_PHP/User/cart');
        }
        exit();
    }

    /**
     * Xóa toàn bộ giỏ hàng (POST)
     */
    public function clear() {
        if (!isset($_POST['btnXoaTatCa'])) {
            header('Location: /COFFEE_PHP/User/cart');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            $result = $this->cartService->clearCart($customerId);

            if ($result['success']) {
                $_SESSION['success_message'] = 'Đã xóa toàn bộ giỏ hàng';
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

            header('Location: /COFFEE_PHP/User/cart');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /COFFEE_PHP/User/cart');
        }
        exit();
    }
}
?>
