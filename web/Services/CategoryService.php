<?php
/**
 * CategoryService - Xử lý logic nghiệp vụ cho Category
 */

use web\Entity\CategoryEntity;

class CategoryService extends Service {

    private $categoryRepo;

    public function __construct() {
        // Khởi tạo Repository thông qua Service base
        $this->categoryRepo = $this->repository('CategoryRepository');
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAllCategories() {
        return $this->categoryRepo->findAll();
    }

    /**
     * Lấy danh mục theo ID
     */
    public function getCategoryById($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID không hợp lệ");
        }

        return $this->categoryRepo->findById($id);
    }

    /**
     * Tìm kiếm danh mục
     */
    public function searchCategories($keyword) {
        if (empty(trim($keyword))) {
            return $this->getAllCategories();
        }

        return $this->categoryRepo->search($keyword);
    }

    /**
     * Tạo danh mục mới
     */
    public function createCategory($data) {
        // Validate dữ liệu
        $this->validateCategoryData($data);

        // Tạo entity
        $category = new CategoryEntity();
        $category->name = trim($data['name']);
        $category->description = trim($data['description'] ?? '');

        // Lưu vào database
        $result = $this->categoryRepo->create($category);
        
        if ($result) {
            return ['success' => true, 'message' => 'Tạo danh mục thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi tạo danh mục'];
        }
    }

    /**
     * Cập nhật danh mục
     */
    public function updateCategory($id, $data) {
        // Kiểm tra danh mục có tồn tại không
        $category = $this->categoryRepo->findById($id);
        if (!$category) {
            throw new Exception("Danh mục không tồn tại");
        }

        // Validate dữ liệu
        $this->validateCategoryData($data, true, $id);

        // Cập nhật thông tin
        $category->name = trim($data['name']);
        $category->description = trim($data['description'] ?? '');

        // Lưu vào database
        if ($this->categoryRepo->update($category)) {
            return ['success' => true, 'message' => 'Cập nhật thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];
        }
    }

    /**
     * Xóa danh mục
     */
    public function deleteCategory($id) {
        // Kiểm tra danh mục có tồn tại không
        $category = $this->categoryRepo->findById($id);
        if (!$category) {
            throw new Exception("Danh mục không tồn tại");
        }

        // TODO: Kiểm tra xem có sản phẩm nào đang dùng danh mục này không
        // Nếu có thì không cho xóa

        // Xóa danh mục
        if ($this->categoryRepo->delete($id)) {
            return ['success' => true, 'message' => 'Xóa danh mục thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi xóa danh mục'];
        }
    }

    /**
     * Kiểm tra tên danh mục đã tồn tại chưa
     */
    public function checkNameExists($name, $excludeId = null) {
        $category = $this->categoryRepo->findByName($name, $excludeId);
        return $category !== null;
    }

    /**
     * Lấy thống kê
     */
    public function getStats() {
        return [
            'total' => $this->categoryRepo->count()
        ];
    }

    /**
     * Validate dữ liệu danh mục
     */
    private function validateCategoryData($data, $isUpdate = false, $excludeId = null) {
        // 1. Kiểm tra tên danh mục
        if (empty(trim($data['name']))) {
            throw new Exception("Tên danh mục không được để trống");
        }

        if (strlen(trim($data['name'])) < 2) {
            throw new Exception("Tên danh mục phải có ít nhất 2 ký tự");
        }

        if (strlen(trim($data['name'])) > 255) {
            throw new Exception("Tên danh mục không được quá 255 ký tự");
        }

        // 2. Kiểm tra tên danh mục đã tồn tại chưa
        if ($this->checkNameExists($data['name'], $excludeId)) {
            throw new Exception("Tên danh mục đã tồn tại");
        }

        // 3. Kiểm tra mô tả (optional)
        if (!empty($data['description']) && strlen($data['description']) > 1000) {
            throw new Exception("Mô tả không được quá 1000 ký tự");
        }

        return true;
    }
}
?>
