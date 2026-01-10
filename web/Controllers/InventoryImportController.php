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

    /**
     * Hiển thị trang chính với danh sách phiếu nhập
     */
    public function GetData($message = null) {
        // Hiển thị thông báo nếu có
        $this->showMessage($message);

        $imports = $this->inventoryImportService->getAllImports();
        $ingredients = $this->ingredientService->getAllIngredients();

        $this->renderView($imports, $ingredients, '');
    }

    /**
     * Tìm kiếm phiếu nhập
     */
    public function timkiem() {
        $keyword = trim($_POST['txtSearch'] ?? '');

        if ($keyword) {
            $imports = $this->inventoryImportService->searchImports($keyword);
        } else {
            $imports = $this->inventoryImportService->getAllImports();
        }

        $ingredients = $this->ingredientService->getAllIngredients();
        $this->renderView($imports, $ingredients, $keyword);
    }

    /**
     * Tạo phiếu nhập mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->inventoryImportService->createImport($_POST);
            // Sau khi tạo thành công, redirect về trang chính
            header('Location: ?url=InventoryImportController/GetData');
            exit();
        }
    }

    /**
     * Cập nhật phiếu nhập
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->inventoryImportService->updateImport($_POST);
            // Sau khi cập nhật thành công, redirect về trang chính
            header('Location: ?url=InventoryImportController/GetData');
            exit();
        }
    }

    /**
     * Xóa phiếu nhập
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            if ($id > 0) {
                $result = $this->inventoryImportService->deleteImport($id);
                // Sau khi xóa, redirect về trang chính
                header('Location: ?url=InventoryImportController/GetData');
                exit();
            }
        }
    }

    /**
     * Xuất Excel danh sách phiếu nhập kho
     */
    public function xuatexcel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnXuatexcel'])) {
            $keyword = trim($_POST['txtSearch'] ?? '');

            // Lấy dữ liệu phiếu nhập (đã có ingredient_name và unit từ JOIN)
            if (!empty($keyword)) {
                $imports = $this->inventoryImportService->searchImports($keyword);
            } else {
                $imports = $this->inventoryImportService->getAllImports();
            }

            // Chuyển đổi dữ liệu để xuất Excel
            $data = $this->prepareExcelData($imports);

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

    // ========== PRIVATE HELPER METHODS ==========

    /**
     * Hiển thị thông báo
     */
    private function showMessage($message) {
        if ($message) {
            $messageText = addslashes($message['message']);
            if ($message['success']) {
                echo "<script>alert('$messageText')</script>";
            } else {
                echo "<script>alert('Lỗi: $messageText')</script>";
            }
        }
    }

    /**
     * Render view với dữ liệu
     */
    private function renderView($imports, $ingredients, $keyword) {
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'InventoryImports_v',
            'imports' => $imports,
            'ingredients' => $ingredients,
            'section' => 'inventory_imports',
            'keyword' => $keyword
        ]);
    }

    /**
     * Chuẩn bị dữ liệu để xuất Excel
     */
    private function prepareExcelData($imports) {
        return array_map(function($import) {
            return [
                'id' => $import->id,
                'ingredient_name' => $import->ingredient_name ?? 'N/A',
                'import_quantity' => $import->import_quantity . ' ' . ($import->unit ?? ''),
                'total_cost' => number_format($import->total_cost, 0, ',', '.'),
                'import_date' => date('d/m/Y H:i', strtotime($import->import_date)),
                'note' => $import->note ?? '-'
            ];
        }, $imports);
    }
}
?>
