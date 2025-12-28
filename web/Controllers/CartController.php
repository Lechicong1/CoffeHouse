<?php
/**
 * FILE: CartController.php
 * DESCRIPTION: Controller xử lý các request liên quan đến giỏ hàng
 * CHUẨN MVC: Controller chỉ nhận request, gọi Service, trả response
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
     * Hiển thị trang giỏ hàng (Method mặc định)
     */
    public function GetData() {
        $customerId = $this->checkAuth();

        try {
            // Gọi Service để lấy dữ liệu - KHÔNG có logic ở đây
            $cartData = $this->cartService->getCart($customerId);

            // Chỉ render view với data từ Service
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
     */
    public function ins() {
        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btnThemGioHang'])) {
            header('Location: /COFFEE_PHP/User/menu');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            // Lấy dữ liệu từ request - KHÔNG validate ở đây
            $productSizeId = $_POST['txtProductSizeId'] ?? null;
            $quantity = (int)($_POST['txtQuantity'] ?? 1);
            $buyNow = $_POST['buy_now'] ?? '0';

            // Gọi Service xử lý - Service sẽ validate và xử lý logic
            $result = $this->cartService->addToCart($customerId, $productSizeId, $quantity);

            // Xử lý kết quả từ Service
            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];

                // Redirect theo hành động
                if ($buyNow === '1') {
                    header('Location: /COFFEE_PHP/Checkout/GetData');
                } else {
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/COFFEE_PHP/Cart/GetData'));
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
     */
    public function upd() {
        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btnCapnhat'])) {
            header('Location: /COFFEE_PHP/Cart/GetData');
            exit();
        }

        $customerId = $this->checkAuth();

        try {
            // Lấy dữ liệu từ request
            $cartItemId = $_POST['txtCartItemId'] ?? null;
            $quantity = (int)($_POST['txtQuantity'] ?? 0);

            // Gọi Service xử lý - Service sẽ validate
            $result = $this->cartService->updateQuantity($customerId, $cartItemId, $quantity);

            // Set message từ Service
            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

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
            $result = $this->cartService->removeItem($customerId, $cartItemId);

            // Set message từ Service
            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

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

            // Set message từ Service
            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        // Redirect về giỏ hàng (URL đúng)
        header('Location: /COFFEE_PHP/Cart/GetData');
        exit();
    }
}
?>
