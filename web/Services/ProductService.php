<?php
include_once './web/Repositories/ProductRepository.php';
include_once './web/Repositories/CategoryRepository.php';
include_once './web/Entity/ProductEntity.php';
include_once './web/Entity/ProductSizeEntity.php';
use web\Entity\ProductEntity;
use web\Entity\ProductSizeEntity;

class ProductService {
    private $productRepository;
    private $categoryRepository;

    public function __construct() {
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
    }
    public function getMenuForPOS() {
        $products = $this->productRepository->findAll();
        $menu = [];

        foreach ($products as $product) {
            if (!$product->is_active) continue;

            $sizes = $this->productRepository->getSizesByProductId($product->id);
            $sizeList = [];
            foreach ($sizes as $size) {
                $sizeList[] = [
                    'id' => $size->id,  // product_size_id - QUAN TRỌNG!
                    'size' => $size->size_name,
                    'price' => (int)$size->price
                ];
            }

            // Default price (prefer M)
            $price = 0;
            foreach ($sizes as $size) {
                if ($size->size_name === 'M') {
                    $price = $size->price;
                    break;
                }
            }
            if ($price == 0 && count($sizes) > 0) {
                $price = $sizes[0]->price;
            }

            $menu[] = [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->image_url ? $product->image_url : 'Public/Assets/default-coffee.png',
                'price' => (int)$price,
                'sizes' => $sizeList,
                'category_id' => $product->category_id
            ];
        }
        return $menu;
    }

    public function getAllProducts() {
        return $this->productRepository->findAll();
    }

    public function getProductById($id) {
        return $this->productRepository->findById($id);
    }

    public function createProduct($data, $files) {
        try {
            // Validate dữ liệu
            $this->validateProductData($data);

            // Handle file upload
            $imageUrl = '';
            if (isset($files['image']) && $files['image']['error'] == 0) {
                $targetDir = "Public/Assets/";

                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($targetDir)) {
                    if (!mkdir($targetDir, 0777, true)) {
                        throw new Exception("Không thể tạo thư mục upload. Kiểm tra quyền truy cập!");
                    }
                }

                // Kiểm tra quyền ghi
                if (!is_writable($targetDir)) {
                    throw new Exception("Thư mục '$targetDir' không có quyền ghi. Chạy: sudo chmod -R 777 Public/Assets/");
                }

                $fileName = time() . '_' . basename($files["image"]["name"]);
                $targetFile = $targetDir . $fileName;

                // Upload file
                if (!move_uploaded_file($files["image"]["tmp_name"], $targetFile)) {
                    $uploadError = error_get_last();
                    throw new Exception("Upload file thất bại! Lỗi: " . ($uploadError ? $uploadError['message'] : 'Không xác định'));
                }

                // Set quyền cho file vừa upload (quan trọng trên Linux)
                chmod($targetFile, 0666);
                $imageUrl = $targetFile;
            } elseif (isset($files['image']) && $files['image']['error'] != 4) {
                // Error code khác 4 (UPLOAD_ERR_NO_FILE) nghĩa là có lỗi
                $errorMessages = [
                    1 => 'File quá lớn (vượt quá upload_max_filesize)',
                    2 => 'File quá lớn (vượt quá MAX_FILE_SIZE)',
                    3 => 'File chỉ được upload một phần',
                    6 => 'Không tìm thấy thư mục tạm',
                    7 => 'Không thể ghi file vào đĩa',
                    8 => 'PHP extension dừng upload'
                ];
                $errorCode = $files['image']['error'];
                $errorMsg = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : "Lỗi không xác định ($errorCode)";
                throw new Exception("Lỗi upload: " . $errorMsg);
            }

            $productData = [
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'image_url' => $imageUrl,
                'is_active' => isset($data['is_active']) ? 1 : 0
            ];

            $product = new ProductEntity($productData);
            $productId = $this->productRepository->create($product);

            if ($productId) {
                // Thêm size M, L, XL
                $sizes = ['M', 'L', 'XL'];
                foreach ($sizes as $size) {
                    $priceKey = 'price_' . $size;
                    if (isset($data[$priceKey]) && $data[$priceKey] !== '') {
                        $sizeData = [
                            'product_id' => $productId,
                            'size_name' => $size,
                            'price' => $data[$priceKey]
                        ];
                        $productSize = new ProductSizeEntity($sizeData);
                        $this->productRepository->addSize($productSize);
                    }
                }
                return ['success' => true, 'message' => 'Thêm sản phẩm thành công!'];
            }

            return ['success' => false, 'message' => 'Thêm sản phẩm thất bại!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function updateProduct($data, $files) {
        try {
            $id = $data['id'];
            $currentProduct = $this->productRepository->findById($id);
            
            if (!$currentProduct) {
                throw new Exception('Sản phẩm không tồn tại!');
            }

            // Validate dữ liệu
            $this->validateProductData($data, true, $id);

            // Handle file upload
            $imageUrl = $currentProduct->image_url;
            
            // Check if delete image flag is set
            if (isset($data['delete_image']) && $data['delete_image'] == '1') {
                $imageUrl = ''; 
            }

            if (isset($files['image']) && $files['image']['error'] == 0) {
                $targetDir = "Public/Assets/";

                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($targetDir)) {
                    if (!mkdir($targetDir, 0777, true)) {
                        throw new Exception("Không thể tạo thư mục upload. Kiểm tra quyền truy cập!");
                    }
                }

                // Kiểm tra quyền ghi
                if (!is_writable($targetDir)) {
                    throw new Exception("Thư mục '$targetDir' không có quyền ghi. Chạy: sudo chmod -R 777 Public/Assets/");
                }

                $fileName = time() . '_' . basename($files["image"]["name"]);
                $targetFile = $targetDir . $fileName;

                // Upload file
                if (!move_uploaded_file($files["image"]["tmp_name"], $targetFile)) {
                    $uploadError = error_get_last();
                    throw new Exception("Upload file thất bại! Lỗi: " . ($uploadError ? $uploadError['message'] : 'Không xác định'));
                }

                // Set quyền cho file vừa upload (quan trọng trên Linux)
                chmod($targetFile, 0666);
                $imageUrl = $targetFile;
            } elseif (isset($files['image']) && $files['image']['error'] != 4) {
                // Error code khác 4 (UPLOAD_ERR_NO_FILE) nghĩa là có lỗi
                $errorMessages = [
                    1 => 'File quá lớn (vượt quá upload_max_filesize)',
                    2 => 'File quá lớn (vượt quá MAX_FILE_SIZE)',
                    3 => 'File chỉ được upload một phần',
                    6 => 'Không tìm thấy thư mục tạm',
                    7 => 'Không thể ghi file vào đĩa',
                    8 => 'PHP extension dừng upload'
                ];
                $errorCode = $files['image']['error'];
                $errorMsg = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : "Lỗi không xác định ($errorCode)";
                throw new Exception("Lỗi upload: " . $errorMsg);
            }

            $productData = [
                'id' => $id,
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'image_url' => $imageUrl,
                'is_active' => isset($data['is_active']) ? 1 : 0
            ];

            $product = new ProductEntity($productData);
            $updateResult = $this->productRepository->update($product);

            if ($updateResult) {
                // Cập nhật size M, L, XL
                $currentSizes = $this->productRepository->getSizesByProductId($id);
                $sizeMap = [];
                foreach ($currentSizes as $s) {
                    $sizeMap[$s->size_name] = $s;
                }

                $sizes = ['M', 'L', 'XL'];
                foreach ($sizes as $size) {
                    $priceKey = 'price_' . $size;
                    $newPrice = isset($data[$priceKey]) ? $data[$priceKey] : '';

                    if ($newPrice !== '') {
                        if (isset($sizeMap[$size])) {
                            // Update
                            $s = $sizeMap[$size];
                            $s->price = $newPrice;
                            $this->productRepository->updateSize($s);
                        } else {
                            // Create
                            $sizeData = [
                                'product_id' => $id,
                                'size_name' => $size,
                                'price' => $newPrice
                            ];
                            $productSize = new ProductSizeEntity($sizeData);
                            $this->productRepository->addSize($productSize);
                        }
                    } else {
                        // Nếu user xóa giá, ta xóa size đó đi (nếu tồn tại)
                        if (isset($sizeMap[$size])) {
                            $this->productRepository->deleteSize($sizeMap[$size]->id);
                        }
                    }
                }
                return ['success' => true, 'message' => 'Cập nhật sản phẩm thành công!'];
            }

            return ['success' => false, 'message' => 'Cập nhật sản phẩm thất bại!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function deleteProduct($id) {
        // Xóa sizes trước
        $this->productRepository->deleteSizesByProductId($id);
        // Xóa sản phẩm
        if ($this->productRepository->delete($id)) {
            return ['success' => true, 'message' => 'Xóa sản phẩm thành công!'];
        }
        return ['success' => false, 'message' => 'Xóa sản phẩm thất bại!'];
    }

    public function getProductsByCategory($categoryId) {
        return $this->productRepository->findByCategoryId($categoryId);
    }

    public function searchProducts($keyword) {
        return $this->productRepository->search($keyword);
    }

    public function getProductSizes($productId) {
        return $this->productRepository->getSizesByProductId($productId);
    }

    /**
     * Validate dữ liệu sản phẩm
     */
    private function validateProductData($data, $isUpdate = false, $excludeId = null) {
        // Validate name
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            throw new Exception("Tên sản phẩm phải có ít nhất 3 ký tự");
        }

        // Check duplicate name
        $existingProduct = $this->productRepository->findByName($data['name'], $excludeId);
        if ($existingProduct) {
            throw new Exception("Tên sản phẩm đã tồn tại");
        }

        // Validate category_id
        if (empty($data['category_id'])) {
            throw new Exception("Vui lòng chọn danh mục");
        }
    }
}
?>

