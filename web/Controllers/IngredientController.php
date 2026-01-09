<?php


class IngredientController extends Controller {
    private $ingredientService;

    function __construct() {
        // Khởi tạo Service thông qua Controller base
        $this->ingredientService = $this->service('IngredientService');
    }

    /**
     * Hiển thị danh sách nguyên liệu (Method mặc định)
     */
    function GetData() {
        // Đồng bộ trạng thái: tự động deactivate nguyên liệu hết hạn
        $this->ingredientService->syncExpiryStatuses();
        
        $stats = $this->ingredientService->getStats();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Ingredients_v',
            'section' => 'ingredients',
            'ingredients' => $this->ingredientService->getAllIngredients(),
            'stats' => $stats
        ]);
    }

    /**
     * Tìm kiếm nguyên liệu (GET)
     */
    function timkiem() {
        // Đồng bộ trạng thái: tự động deactivate nguyên liệu hết hạn
        $this->ingredientService->syncExpiryStatuses();
        
        $keyword = $_GET['search'] ?? '';
        $kq = $this->ingredientService->searchIngredients($keyword);
        $stats = $this->ingredientService->getStats();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Ingredients_v',
            'section' => 'ingredients',
            'ingredients' => $kq,
            'stats' => $stats,
            'keyword' => $keyword
        ]);
    }

    /**
     * Thêm nguyên liệu mới (POST)
     */
    function ins() {
        if (isset($_POST['btnThem'])) {
            try {
                $data = [
                    'name' => $_POST['txtName'],
                    'unit' => $_POST['txtUnit'],
                    'expiry_date' => !empty($_POST['txtExpiryDate']) ? $_POST['txtExpiryDate'] : null
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

            $stats = $this->ingredientService->getStats();
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Ingredients_v',
                'section' => 'ingredients',
                'ingredients' => $this->ingredientService->getAllIngredients(),
                'stats' => $stats
            ]);
        }
    }

    /**
     * Cập nhật nguyên liệu (POST)
     */
    function upd() {
        if (isset($_POST['btnCapnhat'])) {
            try {
                $id = (int)$_POST['txtId'];

                $data = [
                    'name' => $_POST['txtName'],
                    'unit' => $_POST['txtUnit'],
                    'expiry_date' => !empty($_POST['txtExpiryDate']) ? $_POST['txtExpiryDate'] : null
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
                'ingredients' => $this->ingredientService->getAllIngredients(),
                'stats' => $stats
            ]);
        }
    }

    /**
     * Xóa nguyên liệu (POST)
     */
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

            $stats = $this->ingredientService->getStats();
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Ingredients_v',
                'section' => 'ingredients',
                'ingredients' => $this->ingredientService->getAllIngredients(),
                'stats' => $stats
            ]);
        }
    }
}
?>
