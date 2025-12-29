<?php
include_once './Config/Controller.php';

class InventoryImportController extends Controller {
    private $inventoryImportService;
    private $ingredientService;

    public function __construct() {
        $this->inventoryImportService = $this->service('InventoryImportService');
        $this->ingredientService = $this->service('IngredientService');
    }

    public function GetData($message = null) {
        if ($message) {
            if ($message['success']) {
                echo "<script>alert('" . addslashes($message['message']) . "')</script>";
            } else {
                echo "<script>alert('Lỗi: " . addslashes($message['message']) . "')</script>";
            }
        }

        $imports = $this->inventoryImportService->getAllImports();
        $ingredients = $this->ingredientService->getAllIngredients();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'InventoryImports_v',
            'imports' => $imports,
            'ingredients' => $ingredients,
            'section' => 'inventory_imports',
            'keyword' => '',
            'message' => $message
        ]);
    }

    public function timkiem() {
        $keyword = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '';
        
        if ($keyword) {
            $imports = $this->inventoryImportService->searchImports($keyword);
        } else {
            $imports = $this->inventoryImportService->getAllImports();
        }

        $ingredients = $this->ingredientService->getAllIngredients();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'InventoryImports_v',
            'imports' => $imports,
            'ingredients' => $ingredients,
            'section' => 'inventory_imports',
            'keyword' => $keyword
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->inventoryImportService->createImport($_POST);
            $this->GetData($result);
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->inventoryImportService->updateImport($_POST);
            $this->GetData($result);
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $result = $this->inventoryImportService->deleteImport($id);
            $this->GetData($result);
        }
    }
}
?>
