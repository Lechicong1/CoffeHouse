<?php
include_once './Config/Controller.php';
require_once __DIR__ . '/../../Config/ExcelHelper.php';

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

    /**
     * Xuất Excel danh sách phiếu nhập kho
     */
    function xuatexcel() {
        if (isset($_POST['btnXuatexcel'])) {
            // Lấy từ khóa tìm kiếm nếu có
            $keyword = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '';

            // Lấy dữ liệu phiếu nhập
            if (!empty($keyword)) {
                $imports = $this->inventoryImportService->searchImports($keyword);
            } else {
                $imports = $this->inventoryImportService->getAllImports();
            }

            // Lấy danh sách nguyên liệu để map tên
            $ingredients = $this->ingredientService->getAllIngredients();
            $ingredientMap = [];
            foreach ($ingredients as $ingredient) {
                $ingredientMap[$ingredient->id] = $ingredient->name;
            }

            // Chuyển đổi object sang array để xuất Excel
            $data = array_map(function($import) use ($ingredientMap) {
                $ingredientName = isset($ingredientMap[$import->ingredient_id])
                    ? $ingredientMap[$import->ingredient_id]
                    : 'N/A';

                return [
                    'id' => $import->id,
                    'ingredient_name' => $ingredientName,
                    'import_quantity' => $import->import_quantity,
                    'total_cost' => number_format($import->total_cost, 0, ',', '.'),
                    'import_date' => date('d/m/Y H:i', strtotime($import->import_date)),
                    'note' => $import->note ?? '-'
                ];
            }, $imports);

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'id' => 'ID',
                'ingredient_name' => 'Tên Nguyên Liệu',
                'import_quantity' => 'Số Lượng',
                'total_cost' => 'Tổng Tiền (VNĐ)',
                'import_date' => 'Ngày Nhập',
                'note' => 'Ghi Chú'
            ];

            // Gọi hàm xuất Excel từ Helper
            ExcelHelper::exportToExcel($data, $headers, 'DanhSachPhieuNhapKho');
        }
    }
}
?>
