<?php
include_once './Config/Controller.php';

class ProductController extends Controller {
    private $productService;
    private $categoryService;

    public function __construct() {
        $this->productService = $this->service('ProductService');
        $this->categoryService = $this->service('CategoryService');
    }

    public function GetData($message = null) {
        if ($message) {
            if ($message['success']) {
                echo "<script>alert('" . addslashes($message['message']) . "')</script>";
            } else {
                echo "<script>alert('Lỗi: " . addslashes($message['message']) . "')</script>";
            }
        }

        $products = $this->productService->getAllProducts();
        
        // Lấy size cho từng sản phẩm để hiển thị (nếu cần) hoặc để edit
        foreach ($products as $product) {
            $product->sizes = $this->productService->getProductSizes($product->id);
        }

        $categories = $this->categoryService->getAllCategories();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Products_v',
            'products' => $products,
            'categories' => $categories,
            'section' => 'products',
            'keyword' => '',
            'message' => $message
        ]);
    }

    public function timkiem() {
        $keyword = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '';
        
        if ($keyword) {
            $products = $this->productService->searchProducts($keyword);
        } else {
            $products = $this->productService->getAllProducts();
        }

        // Lấy size cho từng sản phẩm
        foreach ($products as $product) {
            $product->sizes = $this->productService->getProductSizes($product->id);
        }

        $categories = $this->categoryService->getAllCategories();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Products_v',
            'products' => $products,
            'categories' => $categories,
            'section' => 'products',
            'keyword' => $keyword
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->productService->createProduct($_POST, $_FILES);
            $this->GetData($result);
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->productService->updateProduct($_POST, $_FILES);
            $this->GetData($result);
        }
    }

    public function delete() {
        if (isset($_POST['id'])) {
            $result = $this->productService->deleteProduct($_POST['id']);
            $this->GetData($result);
        }
    }
}
?>
