<?php
/**
 * IngredientService - Xử lý logic nghiệp vụ cho Ingredient
 */
class IngredientService extends Service {

    private $ingredientRepo;

    public function __construct() {
        // Khởi tạo Repository thông qua Service base
        $this->ingredientRepo = $this->repository('IngredientRepository');
    }

    /**
     * Lấy tất cả nguyên liệu
     */
    public function getAllIngredients() {
        return $this->ingredientRepo->findAll();
    }

    /**
     * Đồng bộ trạng thái hết hạn
     * Chuyển tất cả nguyên liệu hết hạn sang is_active = 0
     */
    public function syncExpiryStatuses() {
        $this->ingredientRepo->deactivateExpiredIngredients();
    }

    /**
     * Lấy danh sách nguyên liệu đang hoạt động
     * Đã lọc bỏ nguyên liệu hết hạn hoặc bị vô hiệu hóa
     */
    public function getActiveIngredients() {
        // Trước khi lấy, chạy đồng bộ để disable các cái vừa hết hạn
        $this->syncExpiryStatuses();
        return $this->ingredientRepo->findActive();
    }

    /**
     * Lấy nguyên liệu theo ID
     */
    public function getIngredientById($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID không hợp lệ");
        }

        return $this->ingredientRepo->findById($id);
    }

    /**
     * Tìm kiếm nguyên liệu
     */
    public function searchIngredients($keyword) {
        if (empty(trim($keyword))) {
            return $this->getAllIngredients();
        }

        return $this->ingredientRepo->search($keyword);
    }

    /**
     * Tạo nguyên liệu mới
     */
    public function createIngredient($data) {
        // Validate dữ liệu
        $this->validateIngredientData($data);

        // Tạo entity
        $ingredient = new IngredientEntity();
        $ingredient->name = trim($data['name']);
        $ingredient->unit = trim($data['unit']);
        $ingredient->stock_quantity = 0; // Mặc định tồn kho = 0
        $ingredient->expiry_date = !empty($data['expiry_date']) ? $data['expiry_date'] : null;
        
        // Logic đồng bộ: Nếu expiry_date đã qua (hết hạn) thì is_active = 0
        if (!empty($ingredient->expiry_date)) {
            $expiryDate = new DateTime($ingredient->expiry_date);
            $today = new DateTime('today');
            $ingredient->is_active = ($expiryDate >= $today) ? 1 : 0;
        } else {
            $ingredient->is_active = 1;
        }

        // Lưu vào database
        $result = $this->ingredientRepo->create($ingredient);

        if ($result) {
            return ['success' => true, 'message' => 'Tạo nguyên liệu thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi tạo nguyên liệu'];
        }
    }

    /**
     * Cập nhật nguyên liệu
     */
    public function updateIngredient($id, $data) {
        // Kiểm tra nguyên liệu có tồn tại không
        $ingredient = $this->ingredientRepo->findById($id);
        if (!$ingredient) {
            throw new Exception("Nguyên liệu không tồn tại");
        }

        // Validate dữ liệu (loại trừ chính ingredient này khi check trùng tên)
        $this->validateIngredientData($data, true, $id);

        // Cập nhật thông tin (KHÔNG cập nhật stock_quantity)
        $ingredient->name = trim($data['name']);
        $ingredient->unit = trim($data['unit']);
        $ingredient->expiry_date = !empty($data['expiry_date']) ? $data['expiry_date'] : null;
        // $ingredient->stock_quantity giữ nguyên giá trị cũ
        
        // Logic đồng bộ: Nếu expiry_date đã qua (hết hạn) thì is_active = 0
        if (!empty($ingredient->expiry_date)) {
            $expiryDate = new DateTime($ingredient->expiry_date);
            $today = new DateTime('today');
            $ingredient->is_active = ($expiryDate >= $today) ? 1 : 0;
        } else {
            $ingredient->is_active = 1;
        }

        // Lưu vào database
        if ($this->ingredientRepo->update($ingredient)) {
            return ['success' => true, 'message' => 'Cập nhật thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];
        }
    }

    /**
     * Xóa nguyên liệu
     */
    public function deleteIngredient($id) {
        // Kiểm tra nguyên liệu có tồn tại không
        $ingredient = $this->ingredientRepo->findById($id);
        if (!$ingredient) {
            throw new Exception("Nguyên liệu không tồn tại");
        }

        // Xóa
        if ($this->ingredientRepo->delete($id)) {
            return ['success' => true, 'message' => 'Xóa nguyên liệu thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi xóa nguyên liệu'];
        }
    }

    /**
     * Kiểm tra tên nguyên liệu đã tồn tại chưa
     * @param string $name Tên cần kiểm tra
     * @param int|null $excludeId ID nguyên liệu cần loại trừ (khi update)
     * @return bool
     */
    public function checkNameExists($name, $excludeId = null) {
        if (empty($name)) {
            return false;
        }
        $ingredient = $this->ingredientRepo->findByName($name, $excludeId);
        return $ingredient !== null;
    }

    /**
     * Lấy thống kê
     */
    public function getStats() {
        return [
            'total' => $this->ingredientRepo->count()
        ];
    }

    /**
     * Validate dữ liệu nguyên liệu
     */
    private function validateIngredientData($data, $isUpdate = false, $excludeId = null) {
        // Validate name
        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            throw new Exception("Tên nguyên liệu phải có ít nhất 2 ký tự");
        }

        // Kiểm tra tên đã tồn tại chưa
        if ($this->checkNameExists($data['name'], $excludeId)) {
            throw new Exception("Tên nguyên liệu đã tồn tại");
        }

        // Validate unit
        if (empty($data['unit']) || strlen(trim($data['unit'])) < 1) {
            throw new Exception("Đơn vị không được để trống");
        }

        // BỎ validate stock_quantity vì không cho nhập từ form
    }
}
?>
