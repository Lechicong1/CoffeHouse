<?php
include_once './web/Repositories/InventoryImportRepository.php';

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

    public function createImport($import) {
        return $this->inventoryImportRepository->create($import);
    }

    public function updateImport($import) {
        return $this->inventoryImportRepository->update($import);
    }

    public function deleteImport($id) {
        return $this->inventoryImportRepository->delete($id);
    }

    public function searchImports($keyword) {
        return $this->inventoryImportRepository->search($keyword);
    }

    public function getImportsByIngredient($ingredientId) {
        return $this->inventoryImportRepository->findByIngredientId($ingredientId);
    }
}
?>
