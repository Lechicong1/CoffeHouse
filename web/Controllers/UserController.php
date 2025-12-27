<?php
include_once './Config/Controller.php';
/**
 * User Controller - Quản lý giao diện người dùng
 * Theo mô hình MVC chuẩn
 */

class UserController extends Controller {
    private $productService;
    private $categoryService;

    function __construct() {
        // Khởi tạo Service thông qua Controller base
        $this->productService = $this->service('ProductService');
        $this->categoryService = $this->service('CategoryService');
    }

    /**
     * Method mặc định - Hiển thị trang chủ (Giống GetData của các controller khác)
     */
    function GetData() {
        $this->index();
    }

    /**
     * Trang chủ - Hiển thị sản phẩm nổi bật
     */
    function index() {
        // Lấy tất cả sản phẩm
        $allProducts = $this->productService->getAllProducts();
        $categories = $this->categoryService->getAllCategories();

        // Lọc chỉ lấy sản phẩm active
        $products = [];
        if ($allProducts) {
            foreach ($allProducts as $product) {
                if ($product->is_active == 1) {
                    // Lấy size cho sản phẩm
                    $product->sizes = $this->productService->getProductSizes($product->id);
                    $products[] = $product;
                }
            }
        }

        // Render view
        $this->view('UserDashBoard/index', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Trang menu - Hiển thị tất cả danh mục
     */
    function menu() {
        $categories = $this->categoryService->getAllCategories();

        $this->view('UserDashBoard/menu', [
            'categories' => $categories
        ]);
    }

    /**
     * Trang sản phẩm theo danh mục
     */
    function categoryProducts() {
        $categoryId = isset($_GET['id']) ? $_GET['id'] : null;

        if ($categoryId) {
            $category = $this->categoryService->getCategoryById($categoryId);
            $allProducts = $this->productService->getProductsByCategory($categoryId);

            // Lọc sản phẩm active và lấy sizes
            $products = [];
            if ($allProducts) {
                foreach ($allProducts as $product) {
                    if ($product->is_active == 1) {
                        $product->sizes = $this->productService->getProductSizes($product->id);
                        $products[] = $product;
                    }
                }
            }

            $this->view('UserDashBoard/category-products', [
                'category' => $category,
                'products' => $products
            ]);
        } else {
            // Redirect về trang menu nếu không có category
            echo "<script>window.location.href='User/menu';</script>";
            exit;
        }
    }

    /**
     * Trang chi tiết sản phẩm
     */
    function productDetail() {
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$productId) {
            echo "<script>window.location.href='/COFFEE_PHP/User/menu';</script>";
            exit;
        }

        $product = $this->productService->getProductById($productId);

        if (!$product || $product->is_active != 1) {
            echo "<script>window.location.href='/COFFEE_PHP/User/menu';</script>";
            exit;
        }

        // Load sizes và category
        $product->sizes = $this->productService->getProductSizes($product->id);
        $category = $this->categoryService->getCategoryById($product->category_id);

        // Lấy sản phẩm liên quan (cùng category)
        $relatedProductsAll = $this->productService->getProductsByCategory($product->category_id);

        // Loại bỏ sản phẩm hiện tại và chỉ lấy active
        $relatedProducts = [];
        if ($relatedProductsAll) {
            foreach ($relatedProductsAll as $p) {
                if ($p->id != $productId && $p->is_active == 1) {
                    $p->sizes = $this->productService->getProductSizes($p->id);
                    $relatedProducts[] = $p;

                    // Giới hạn 4 sản phẩm
                    if (count($relatedProducts) >= 4) break;
                }
            }
        }

        $this->view('UserDashBoard/product-detail', [
            'product' => $product,
            'category' => $category,
            'relatedProducts' => $relatedProducts
        ]);
    }

    /**
     * Trang about
     */
    function about() {
        $this->view('UserDashBoard/about', []);
    }

    /**
     * Trang giỏ hàng
     */
    function cart() {
        // Kiểm tra đăng nhập
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập để xem giỏ hàng';
            header('Location: /COFFEE_PHP/Auth/login');
            exit();
        }

        // Lấy giỏ hàng từ CartService
        include_once './web/Services/CartService.php';
        $cartService = new CartService();
        $customerId = $_SESSION['user']['id'];
        $cartData = $cartService->getCart($customerId);

        $this->view('UserDashBoard/cart', [
            'cartItems' => $cartData['items'] ?? [],
            'total' => $cartData['total'] ?? 0,
            'count' => $cartData['count'] ?? 0
        ]);
    }

    /**
     * Trang checkout
     */
    function checkout() {
        // Kiểm tra đăng nhập
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập để thanh toán';
            header('Location: /COFFEE_PHP/Auth/login');
            exit();
        }

        // Lấy giỏ hàng
        include_once './web/Services/CartService.php';
        $cartService = new CartService();
        $customerId = $_SESSION['user']['id'];
        $cartData = $cartService->getCart($customerId);

        // Kiểm tra giỏ hàng có trống không
        if (empty($cartData['items'])) {
            $_SESSION['error_message'] = 'Giỏ hàng của bạn đang trống';
            header('Location: /COFFEE_PHP/User/menu');
            exit();
        }

        $this->view('UserDashBoard/checkout', [
            'cartItems' => $cartData['items'],
            'total' => $cartData['total'],
            'count' => $cartData['count']
        ]);
    }
}
?>
