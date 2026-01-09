<?php

class InventoryCheckController extends Controller {
    private $inventoryCheckService;

    function __construct() {
        // Khởi tạo Service thông qua Controller base
        $this->inventoryCheckService = $this->service('InventoryCheckService');
    }

    /**
     * Hiển thị trang kiểm kho (Method mặc định)
     * Lấy dữ liệu kiểm kho của ngày hôm nay
     */
    function GetData() {
        // Lấy ngày từ query string hoặc mặc định là hôm nay
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Lấy dữ liệu kiểm kho
        $inventoryData = $this->inventoryCheckService->getInventoryCheckByDate($date);

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'InventoryCheck_v',
            'section' => 'inventory-check',
            'inventoryData' => $inventoryData,
            'currentDate' => $date
        ]);
    }

    /**
     * Lưu thông tin kiểm kho (POST)
     * Được gọi khi người dùng nhấn nút "Lưu"
     */
    function save() {
        if (isset($_POST['btnSave']) || $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Lấy dữ liệu từ form
                $ingredientName = trim($_POST['txtIngredient'] ?? '');
                $actualQuantity = floatval($_POST['txtActualQuantity'] ?? 0);


                // Validate
                if (empty($ingredientName)) {
                    echo "<script>alert('Vui lòng chọn nguyên liệu!'); window.history.back();</script>";
                    exit();
                }

                if ($actualQuantity < 0) {
                    echo "<script>alert('Số lượng thực tế không được âm!'); window.history.back();</script>";
                    exit();
                }

                // LẤY SỐ LƯỢNG LÝ THUYẾT TỪ DATABASE
                require_once './web/Repositories/IngredientRepository.php';
                $ingredientRepo = new IngredientRepository();
                $ingredient = $ingredientRepo->getByName($ingredientName);

                if (!$ingredient) {
                    echo "<script>alert('Không tìm thấy nguyên liệu!'); window.history.back();</script>";
                    exit();
                }

                $theoryQuantity = floatval($ingredient['stock_quantity']);
                $difference = $actualQuantity - $theoryQuantity;

                // TÍNH TOÁN STATUS DựA trên % chênh lệch
                $status = $this->inventoryCheckService->calculateStatus($theoryQuantity, $actualQuantity);

                $data = [
                    'ingredient' => $ingredientName,
                    'theoryQuantity' => $theoryQuantity,
                    'actualQuantity' => $actualQuantity,
                    'difference' => $difference,
                    'status' => $status,
                    'note' => ''
                ];

                $result = $this->inventoryCheckService->saveInventoryCheck($data);

                if ($result) {
                    echo "<script>alert('✅ Lưu kiểm kho thành công!'); window.location.href='index.php?url=InventoryCheck/GetData';</script>";
                } else {
                    echo "<script>alert('❌ Lưu thất bại!'); window.history.back();</script>";
                }
            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
                echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.history.back();</script>";
            }
            exit();
        }
    }

    /**
     * Tính toán chênh lệch (POST)
     * Được gọi khi người dùng nhấn nút "Tính toán"
     */
    function calculate() {
        if (isset($_POST['btnCalculate'])) {
            try {
                // Validate input
                if (!isset($_POST['txtTheoryQuantity']) || !isset($_POST['txtActualQuantity'])) {
                    throw new Exception('Thiếu dữ liệu đầu vào');
                }

                $theoryQuantity = floatval($_POST['txtTheoryQuantity']);
                $actualQuantity = floatval($_POST['txtActualQuantity']);

                // Kiểm tra số lượng không được âm
                if ($actualQuantity < 0) {
                    throw new Exception('Số lượng thực tế không được âm');
                }

                // Tính toán
                $result = $this->inventoryCheckService->calculateCheck($theoryQuantity, $actualQuantity);

                echo "<script>alert('Đã tính toán và cập nhật báo cáo thành công!')</script>";
                
                // Lưu kết quả vào session hoặc truyền qua view
                $_SESSION['calculated_result'] = $result;
                
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . $e->getMessage() . "')</script>";
            }
            
            // Reload lại trang
            $date = $_POST['currentDate'] ?? date('Y-m-d');
            $inventoryData = $this->inventoryCheckService->getInventoryCheckByDate($date);
            
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'InventoryCheck_v',
                'section' => 'inventory-check',
                'inventoryData' => $inventoryData,
                'currentDate' => $date,
                'calculatedResult' => $result ?? null
            ]);
        }
    }

    /**
     * Lấy dữ liệu kiểm kho theo ngày (GET)
     */
    function getByDate() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $inventoryData = $this->inventoryCheckService->getInventoryCheckByDate($date);

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'InventoryCheck_v',
            'section' => 'inventory-check',
            'inventoryData' => $inventoryData,
            'currentDate' => $date
        ]);
    }

    /**
     * Sửa/Cập nhật thông tin kiểm kho (POST)
     * Được gọi khi người dùng nhấn nút "Sửa"
     */
    function update() {
        if (isset($_POST['btnUpdate']) || $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Lấy dữ liệu từ form (GIỐNG NÚT LƯU)
                $ingredientName = trim($_POST['txtIngredient'] ?? '');
                $actualQuantity = floatval($_POST['txtActualQuantity'] ?? 0);


                // Validate
                if (empty($ingredientName)) {
                    echo "<script>alert('Vui lòng chọn nguyên liệu!'); window.history.back();</script>";
                    exit();
                }

                if ($actualQuantity < 0) {
                    echo "<script>alert('Số lượng thực tế không được âm!'); window.history.back();</script>";
                    exit();
                }

                // LẤY SỐ LƯỢNG LÝ THUYẾT TỪ DATABASE (TỰ ĐỘNG TÍNH TOÁN LẠI)
                require_once './web/Repositories/IngredientRepository.php';
                $ingredientRepo = new IngredientRepository();
                $ingredient = $ingredientRepo->getByName($ingredientName);

                if (!$ingredient) {
                    echo "<script>alert('Không tìm thấy nguyên liệu!'); window.history.back();</script>";
                    exit();
                }

                $theoryQuantity = floatval($ingredient['stock_quantity']);
                $difference = $actualQuantity - $theoryQuantity;

                // TÍNH TOÁN STATUS DựA trên % chênh lệch
                $status = $this->inventoryCheckService->calculateStatus($theoryQuantity, $actualQuantity);

                $data = [
                    'ingredient' => $ingredientName,
                    'theoryQuantity' => $theoryQuantity,
                    'actualQuantity' => $actualQuantity,
                    'difference' => $difference,
                    'status' => $status,
                    'note' => ''
                ];

                // saveInventoryCheck đã có logic UPDATE nếu đã tồn tại
                $result = $this->inventoryCheckService->saveInventoryCheck($data);

                if ($result) {
                    echo "<script>alert('✅ Cập nhật kiểm kho thành công!'); window.location.href='index.php?url=InventoryCheck/GetData';</script>";
                } else {
                    echo "<script>alert('❌ Cập nhật thất bại!'); window.history.back();</script>";
                }
            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
                echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.history.back();</script>";
            }
            exit();
        }
    }

    /**
     * Xóa thông tin kiểm kho (POST)
     */
    function delete() {
        if (isset($_POST['btnDelete'])) {
            try {
                $id = $_POST['txtId'] ?? 0;
                $result = $this->inventoryCheckService->deleteInventoryCheck($id);

                if ($result) {
                    echo "<script>alert('Xóa thông tin kiểm kho thành công!')</script>";
                } else {
                    echo "<script>alert('Xóa thông tin kiểm kho thất bại!')</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . $e->getMessage() . "')</script>";
            }
            
            // Reload lại trang
            $date = $_POST['currentDate'] ?? date('Y-m-d');
            $inventoryData = $this->inventoryCheckService->getInventoryCheckByDate($date);
            
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'InventoryCheck_v',
                'section' => 'inventory-check',
                'inventoryData' => $inventoryData,
                'currentDate' => $date
            ]);
        }
    }
}
?>