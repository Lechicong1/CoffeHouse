<?php
include_once './web/Repositories/ProductRepository.php';

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

    public function createProduct($product) {
        return $this->productRepository->create($product);
    }

    public function updateProduct($product) {
        return $this->productRepository->update($product);
    }

    public function deleteProduct($id) {
        return $this->productRepository->delete($id);
    }

    public function getProductsByCategory($categoryId) {
        return $this->productRepository->findByCategoryId($categoryId);
    }

    public function searchProducts($keyword) {
        return $this->productRepository->search($keyword);
    }

    public function addProductSize($productSize) {
        return $this->productRepository->addSize($productSize);
    }

    public function getProductSizes($productId) {
        return $this->productRepository->getSizesByProductId($productId);
    }

    public function updateProductSize($productSize) {
        return $this->productRepository->updateSize($productSize);
    }

    public function deleteProductSize($sizeId) {
        return $this->productRepository->deleteSize($sizeId);
    }

    public function deleteProductSizes($productId) {
        return $this->productRepository->deleteSizesByProductId($productId);
    }
}
?>
