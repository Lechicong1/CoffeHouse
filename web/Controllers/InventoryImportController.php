<?php
include_once './Config/Controller.php';
include_once './web/Entity/InventoryImportEntity.php';
use web\Entity\InventoryImportEntity;

class InventoryImportController extends Controller {
    private $inventoryImportService;
    private $ingredientService;

    public function __construct() {
        $this->inventoryImportService = $this->service('InventoryImportService');
        $this->ingredientService = $this->service('IngredientService');
    }

    public function GetData() {
        $imports = $this->inventoryImportService->getAllImports();
        $ingredients = $this->ingredientService->getAllIngredients();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'InventoryImports_v',
            'imports' => $imports,
            'ingredients' => $ingredients,
            'section' => 'inventory_imports',
            'keyword' => ''
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
            $data = [
                'ingredient_id' => $_POST['ingredient_id'],
                'import_quantity' => $_POST['import_quantity'],
                'total_cost' => $_POST['total_cost'],
                'import_date' => $_POST['import_date'],
                'note' => $_POST['note']
            ];

            $import = new InventoryImportEntity($data);
            $this->inventoryImportService->createImport($import);

            header('Location: ?url=InventoryImportController');
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $_POST['id'],
                'ingredient_id' => $_POST['ingredient_id'],
                'import_quantity' => $_POST['import_quantity'],
                'total_cost' => $_POST['total_cost'],
                'import_date' => $_POST['import_date'],
                'note' => $_POST['note']
            ];

            $import = new InventoryImportEntity($data);
            $this->inventoryImportService->updateImport($import);

            header('Location: ?url=InventoryImportController');
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $this->inventoryImportService->deleteImport($id);
            header('Location: ?url=InventoryImportController');
        }
    }
}
?>
