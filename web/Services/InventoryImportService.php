<?php
include_once './web/Repositories/InventoryImportRepository.php';
include_once './web/Entity/InventoryImportEntity.php';
use web\Entity\InventoryImportEntity;

class InventoryImportService {
    private $inventoryImportRepository;

    public function __construct() {
        $this->inventoryImportRepository = new InventoryImportRepository();
    }

    public function getAllImports() {
        return $this->inventoryImportRepository->findAll();
    }

    public function getImportById($id) {
        return $this->inventoryImportRepository->findById($id);
    }

    public function createImport($data) {
        try {
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
                return ['success' => true, 'message' => 'Thêm phiếu nhập thành công!'];
            }
            return ['success' => false, 'message' => 'Thêm phiếu nhập thất bại!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function updateImport($data) {
        try {
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

    public function getImportsByIngredient($ingredientId) {
        return $this->inventoryImportRepository->findByIngredientId($ingredientId);
    }
}
?>
