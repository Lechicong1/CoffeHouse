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
        $this->view('UserDashBoard/MasterLayout', [
            'title' => 'Trang Chủ - Coffee House',
            'page' => 'HomePage',
            'currentPage' => 'index',
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Trang menu - Hiển thị tất cả danh mục
     */
    function menu() {
        $categories = $this->categoryService->getAllCategories();

        $this->view('UserDashBoard/MasterLayout', [
            'title' => 'Thực Đơn - Coffee House',
            'page' => 'MenuPage',
            'currentPage' => 'menu',
            'additionalCSS' => [
                'Public/Css/user-menu-style.css'
            ],
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

            $this->view('UserDashBoard/MasterLayout', [
                'title' => $category->name . ' - Coffee House',
                'page' => 'CategoryProductsPage',
                'currentPage' => 'categoryProducts',
                'additionalCSS' => [
                    'Public/Css/user-category-products.css'
                ],
                'category' => $category,
                'products' => $products
            ]);
        } else {
            // Redirect về trang menu nếu không có category
            header('Location: /COFFEE_PHP/User/menu');
            exit;
        }
    }

    /**
     * Trang chi tiết sản phẩm
     */
    function productDetail() {
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$productId) {
            header('Location: /COFFEE_PHP/User/menu');
            exit;
        }

        $product = $this->productService->getProductById($productId);

        if (!$product || $product->is_active != 1) {
            header('Location: /COFFEE_PHP/User/menu');
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

        $this->view('UserDashBoard/MasterLayout', [
            'title' => $product->name . ' - Coffee House',
            'page' => 'ProductDetailPage',
            'currentPage' => 'productDetail',
            'additionalCSS' => [
                'Public/Css/user-product-detail.css'
            ],
            'additionalJS' => [
                'Public/Js/user-product-detail.js'
            ],
            'product' => $product,
            'category' => $category,
            'relatedProducts' => $relatedProducts
        ]);
    }

    /**
     * Trang about
     */
    function about() {
        $this->view('UserDashBoard/MasterLayout', [
            'title' => 'Về Chúng Tôi - Coffee House',
            'page' => 'AboutPage',
            'currentPage' => 'about',
            'additionalCSS' => [
                'Public/Css/user-about.css'
            ]
        ]);
    }
}
?>
