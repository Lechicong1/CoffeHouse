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
        $this->view('UserDashBoard/MasterLayout', [
            'title' => 'Trang Chủ - Coffee House',
            'page' => 'HomePage',
            'currentPage' => 'index',
            'products' => $this->productService->getActiveProducts(),
            'categories' => $this->categoryService->getAllCategories()
        ]);
    }

    /**
     * Trang menu - Hiển thị tất cả danh mục
     */
    function menu() {
        $this->view('UserDashBoard/MasterLayout', [
            'title' => 'Thực Đơn - Coffee House',
            'page' => 'MenuPage',
            'currentPage' => 'menu',
            'additionalCSS' => ['Public/Css/user-menu-style.css'],
            'categories' => $this->categoryService->getAllCategories()
        ]);
    }

    /**
     * Trang sản phẩm theo danh mục
     */
    function categoryProducts() {
        $categoryId = $_GET['id'] ?? null;
        if (!$categoryId) {
            header('Location: /COFFEE_PHP/User/menu');
            exit;
        }

        $category = $this->categoryService->getCategoryById($categoryId);
        $products = $this->productService->getActiveProductsByCategory($categoryId);

        $this->view('UserDashBoard/MasterLayout', [
            'title' => $category->name . ' - Coffee House',
            'page' => 'CategoryProductsPage',
            'currentPage' => 'categoryProducts',
            'additionalCSS' => ['Public/Css/user-category-products.css'],
            'additionalJS' => ['Public/Js/user-category-products.js'],
            'category' => $category,
            'products' => $products
        ]);
    }

    /**
     * Trang chi tiết sản phẩm
     */
    function productDetail() {
        $productId = $_GET['id'] ?? null;
        if (!$productId) {
            header('Location: /COFFEE_PHP/User/menu');
            exit;
        }

        $product = $this->productService->getProductById($productId);
        $product->sizes = $this->productService->getProductSizes($product->id);
        $category = $this->categoryService->getCategoryById($product->category_id);
        $relatedProducts = $this->productService->getRelatedProducts($product->category_id, $productId, 4);

        $this->view('UserDashBoard/MasterLayout', [
            'title' => $product->name . ' - Coffee House',
            'page' => 'ProductDetailPage',
            'currentPage' => 'productDetail',
            'additionalCSS' => ['Public/Css/user-product-detail.css'],
            'additionalJS' => ['Public/Js/user-product-detail.js'],
            'product' => $product,
            'category' => $category,
            'relatedProducts' => $relatedProducts
        ]);
    }

}
?>
