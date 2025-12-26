<?php
include_once './Config/Controller.php';
include_once './web/Entity/ProductEntity.php';
include_once './web/Entity/ProductSizeEntity.php';
use web\Entity\ProductEntity;
use web\Entity\ProductSizeEntity;

class ProductController extends Controller {
    private $productService;
    private $categoryService;

    public function __construct() {
        $this->productService = $this->service('ProductService');
        $this->categoryService = $this->service('CategoryService');
    }

    public function GetData() {
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
            'keyword' => ''
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
            // Handle file upload
            $imageUrl = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "Public/Assets/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $imageUrl = $targetFile;
                }
            }

            $data = [
                'category_id' => $_POST['category_id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'image_url' => $imageUrl,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $product = new ProductEntity($data);
            $productId = $this->productService->createProduct($product);

            // Thêm size M, L, XL
            if ($productId) {
                $sizes = ['M', 'L', 'XL'];
                foreach ($sizes as $size) {
                    $priceKey = 'price_' . $size;
                    if (isset($_POST[$priceKey]) && $_POST[$priceKey] !== '') {
                        $sizeData = [
                            'product_id' => $productId,
                            'size_name' => $size,
                            'price' => $_POST[$priceKey]
                        ];
                        $productSize = new ProductSizeEntity($sizeData);
                        $this->productService->addProductSize($productSize);
                    }
                }
            }

            header('Location: ?url=ProductController');
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $currentProduct = $this->productService->getProductById($id);
            
            // Handle file upload
            $imageUrl = $currentProduct->image_url;
            
            // Check if delete image flag is set
            if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
                $imageUrl = ''; // Remove image reference
                // Optional: Delete physical file if needed
                // if (file_exists($currentProduct->image_url)) unlink($currentProduct->image_url);
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "Public/Assets/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $imageUrl = $targetFile;
                }
            }

            $data = [
                'id' => $id,
                'category_id' => $_POST['category_id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'image_url' => $imageUrl,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $product = new ProductEntity($data);
            $this->productService->updateProduct($product);

            // Cập nhật size M, L, XL
            $currentSizes = $this->productService->getProductSizes($id);
            $sizeMap = [];
            foreach ($currentSizes as $s) {
                $sizeMap[$s->size_name] = $s;
            }

            $sizes = ['M', 'L', 'XL'];
            foreach ($sizes as $size) {
                $priceKey = 'price_' . $size;
                $newPrice = isset($_POST[$priceKey]) ? $_POST[$priceKey] : '';

                if ($newPrice !== '') {
                    if (isset($sizeMap[$size])) {
                        // Update
                        $s = $sizeMap[$size];
                        $s->price = $newPrice;
                        $this->productService->updateProductSize($s);
                    } else {
                        // Create
                        $sizeData = [
                            'product_id' => $id,
                            'size_name' => $size,
                            'price' => $newPrice
                        ];
                        $productSize = new ProductSizeEntity($sizeData);
                        $this->productService->addProductSize($productSize);
                    }
                } else {
                    // Nếu user xóa giá, ta xóa size đó đi (nếu tồn tại)
                    if (isset($sizeMap[$size])) {
                        $this->productService->deleteProductSize($sizeMap[$size]->id);
                    }
                }
            }

            header('Location: ?url=ProductController');
        }
    }

    public function delete() {
        if (isset($_POST['id'])) {
            $this->productService->deleteProduct($_POST['id']);
            header('Location: ?url=ProductController');
        }
    }
}
?>
