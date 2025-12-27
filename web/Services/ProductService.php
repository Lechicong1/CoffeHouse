<?php
include_once './web/Repositories/ProductRepository.php';
include_once './web/Entity/ProductEntity.php';
include_once './web/Entity/ProductSizeEntity.php';
use web\Entity\ProductEntity;
use web\Entity\ProductSizeEntity;

class ProductService {
    private $productRepository;

    public function __construct() {
        $this->productRepository = new ProductRepository();
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
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = time() . '_' . basename($files["image"]["name"]);
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($files["image"]["tmp_name"], $targetFile)) {
                    $imageUrl = $targetFile;
                }
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
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = time() . '_' . basename($files["image"]["name"]);
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($files["image"]["tmp_name"], $targetFile)) {
                    $imageUrl = $targetFile;
                }
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
