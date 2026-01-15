<?php
require_once __DIR__ . '/../../Config/ExcelHelper.php';


class IngredientController extends Controller {
    private $ingredientService;

    function __construct() {
        // Khởi tạo Service thông qua Controller base
        $this->ingredientService = $this->service('IngredientService');
    }

    function GetData() {
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Ingredients_v',
            'section' => 'ingredients',
            'ingredients' => $this->ingredientService->getAllIngredients()
        ]);
    }

    function timkiem() {
        $keyword = $_GET['search'] ?? '';
        $kq = $this->ingredientService->searchIngredients($keyword);

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Ingredients_v',
            'section' => 'ingredients',
            'ingredients' => $kq,
            'keyword' => $keyword
        ]);
    }

    function ins() {
        if (isset($_POST['btnThem'])) {
            try {
                $data = [
                    'name' => $_POST['txtName'],
                    'unit' => $_POST['txtUnit']
                ];

                $result = $this->ingredientService->createIngredient($data);

                if ($result['success']) {
                    echo "<script>alert('Thêm nguyên liệu thành công!')</script>";
                } else {
                    echo "<script>alert('Thêm thất bại: " . $result['message'] . "')</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . $e->getMessage() . "')</script>";
            }

            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Ingredients_v',
                'section' => 'ingredients',
                'ingredients' => $this->ingredientService->getAllIngredients()
            ]);
        }
    }

    function upd() {
        if (isset($_POST['btnCapnhat'])) {
            try {
                $id = (int)$_POST['txtId'];

                $data = [
                    'name' => $_POST['txtName'],
                    'unit' => $_POST['txtUnit']
                ];

                $result = $this->ingredientService->updateIngredient($id, $data);

                if ($result['success']) {
                    echo "<script>alert('Cập nhật thành công!')</script>";
                } else {
                    echo "<script>alert('Cập nhật thất bại: " . $result['message'] . "')</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . $e->getMessage() . "')</script>";
            }

            $stats = $this->ingredientService->getStats();
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Ingredients_v',
                'section' => 'ingredients',
                'ingredients' => $this->ingredientService->getAllIngredients()
            ]);
        }
    }

    function del() {
        if (isset($_POST['btnXoa'])) {
            try {
                $id = (int)$_POST['txtId'];
                $result = $this->ingredientService->deleteIngredient($id);

                if ($result['success']) {
                    echo "<script>alert('Xóa nguyên liệu thành công!')</script>";
                } else {
                    echo "<script>alert('Xóa thất bại: " . $result['message'] . "')</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . $e->getMessage() . "')</script>";
            }

            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Ingredients_v',
                'section' => 'ingredients',
                'ingredients' => $this->ingredientService->getAllIngredients()
            ]);
        }
    }

    function xuatexcel() {
        if (isset($_POST['btnXuatexcel'])) {
            // Lấy từ khóa tìm kiếm nếu có
            $keyword = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '';

            // Lấy dữ liệu nguyên liệu
            if (!empty($keyword)) {
                $ingredients = $this->ingredientService->searchIngredients($keyword);
            } else {
                $ingredients = $this->ingredientService->getAllIngredients();
            }

            // Chuyển đổi object sang array để xuất Excel
            $data = array_map(function($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'unit' => $ingredient->unit,
                    'stock_quantity' => $ingredient->stock_quantity ?? 0
                ];
            }, $ingredients);

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'id' => 'ID',
                'name' => 'Tên Nguyên Liệu',
                'unit' => 'Đơn Vị',
                'stock_quantity' => 'Số Lượng Tồn Kho'
            ];

            // Gọi hàm xuất Excel từ Helper
            ExcelHelper::exportToExcel($data, $headers, 'DanhSachNguyenLieu');
        }
    }
}
?>
