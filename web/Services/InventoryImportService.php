<?php
include_once './web/Repositories/InventoryImportRepository.php';
include_once './web/Repositories/IngredientRepository.php';
include_once './web/Entity/InventoryImportEntity.php';
use web\Entity\InventoryImportEntity;

class InventoryImportService extends  Service {

    private $inventoryImportRepository;
    private $ingredientRepository;
    public function __construct() {
        $this->inventoryImportRepository = $this->repository('InventoryImportRepository');
        $this->ingredientRepository = $this->repository('IngredientRepository');
    }

    public function getAllImports() {
        return $this->inventoryImportRepository->findAll();
    }

//    public function getImportById($id) {
//        return $this->inventoryImportRepository->findById($id);
//    }

    public function createImport($data) {
        try {
            // Validate dữ liệu
            $this->validateImportData($data);

            $importData = [
                'ingredient_id' => $data['ingredient_id'],
                'import_quantity' => $data['import_quantity'],
                'total_cost' => $data['total_cost'],
                'import_date' => $data['import_date'],
                'note' => $data['note']
            ];

            $import = new InventoryImportEntity($importData);
            $id = $this->inventoryImportRepository->create($import);

            if ($id) {
                // Cập nhật số lượng tồn kho của nguyên liệu
                $ingredient = $this->ingredientRepository->findById($data['ingredient_id']);
                if ($ingredient) {
                    $ingredient->stock_quantity += $data['import_quantity'];
                    $this->ingredientRepository->update($ingredient);
                }

                return ['success' => true, 'message' => 'Thêm phiếu nhập thành công!'];
            }
            return ['success' => false, 'message' => 'Thêm phiếu nhập thất bại!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function updateImport($data) {
        try {
            $this->validateImportData($data);

            // Lấy thông tin phiếu nhập cũ để tính toán lại kho
            $oldImport = $this->inventoryImportRepository->findById($data['id']);
            if (!$oldImport) {
                return ['success' => false, 'message' => 'Phiếu nhập không tồn tại!'];
            }

            $importData = [
                'id' => $data['id'],
                'ingredient_id' => $data['ingredient_id'],
                'import_quantity' => $data['import_quantity'],
                'total_cost' => $data['total_cost'],
                'import_date' => $data['import_date'],
                'note' => $data['note']
            ];

            $import = new InventoryImportEntity($importData);
            $result = $this->inventoryImportRepository->update($import);

            if ($result) {
                // Cập nhật kho
                // Trường hợp 1: Nguyên liệu không đổi
                if ($oldImport->ingredient_id == $data['ingredient_id']) {
                    $ingredient = $this->ingredientRepository->findById($data['ingredient_id']);
                    if ($ingredient) {
                        // Logic: Điều chỉnh lại kho bằng cách trừ lượng cũ và cộng lượng mới
                        // Ví dụ: Nhập 10 (kho 10), sửa thành 12 -> Kho = 10 - 10 + 12 = 12
                        $ingredient->stock_quantity = $ingredient->stock_quantity - $oldImport->import_quantity + $data['import_quantity'];
                        $this->ingredientRepository->update($ingredient);
                    }
                } 
                // Trường hợp 2: Đổi nguyên liệu
                else {
                    // Trừ kho nguyên liệu cũ
                    $oldIngredient = $this->ingredientRepository->findById($oldImport->ingredient_id);
                    if ($oldIngredient) {
                        $oldIngredient->stock_quantity -= $oldImport->import_quantity;
                        $this->ingredientRepository->update($oldIngredient);
                    }

                    // Cộng kho nguyên liệu mới
                    $newIngredient = $this->ingredientRepository->findById($data['ingredient_id']);
                    if ($newIngredient) {
                        $newIngredient->stock_quantity += $data['import_quantity'];
                        $this->ingredientRepository->update($newIngredient);
                    }
                }

                return ['success' => true, 'message' => 'Cập nhật phiếu nhập thành công!'];
            }
            return ['success' => false, 'message' => 'Cập nhật phiếu nhập thất bại!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function deleteImport($id) {
        try {
            $result = $this->inventoryImportRepository->delete($id);
            if ($result) {
                return ['success' => true, 'message' => 'Xóa phiếu nhập thành công!'];
            }
            return ['success' => false, 'message' => 'Xóa phiếu nhập thất bại!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function searchImports($keyword) {
        return $this->inventoryImportRepository->search($keyword);
    }

//    public function getImportsByIngredient($ingredientId) {
//        return $this->inventoryImportRepository->findByIngredientId($ingredientId);
//    }

    /**
     * Validate dữ liệu nhập kho
     */
    private function validateImportData($data) {
        if (empty($data['ingredient_id'])) {
            throw new Exception("Vui lòng chọn nguyên liệu");
        }
        if (empty($data['import_quantity']) || $data['import_quantity'] <= 0) {
            throw new Exception("Số lượng nhập phải lớn hơn 0");
        }
        if (!isset($data['total_cost']) || $data['total_cost'] < 0) {
            throw new Exception("Tổng tiền không hợp lệ");
        }
        if (empty($data['import_date'])) {
            throw new Exception("Vui lòng chọn ngày nhập");
        }
    }

    /**
     * Lấy danh sách phiếu nhập theo khoảng thời gian (cho Report)
     * @param string $fromDate Ngày bắt đầu (Y-m-d)
     * @param string $toDate Ngày kết thúc (Y-m-d)
     * @return array Danh sách phiếu nhập với thông tin nguyên liệu
     */
    public function getImportsByDateRange($fromDate, $toDate) {
        return $this->inventoryImportRepository->findByDateRange($fromDate, $toDate);
    }
}
?>
