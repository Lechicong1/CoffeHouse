<?php
/**
 * InventoryCheckMonthController - Xử lý báo cáo thất thoát kho theo tháng
 */
class InventoryCheckMonthController extends Controller {

    private $inventoryCheckService;

    public function __construct() {
        $this->inventoryCheckService = $this->service('InventoryCheckService');
    }

    /**
     * Hiển thị trang báo cáo thất thoát theo tháng
     */
    public function Index() {
        try {
            // Lấy tháng từ request (nếu có)
            $selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : null;

            // Lấy dữ liệu
            if ($selectedMonth) {
                $inventoryData = $this->inventoryCheckService->getInventoryCheckBySpecificMonth($selectedMonth);
            } else {
                $inventoryData = $this->inventoryCheckService->getInventoryCheckByMonth();
            }

            // Truyền dữ liệu vào view
            $data = [
                'section' => 'inventory_check_month',
                'page' => 'InventoryCheckMonth_v',
                'inventoryData' => $inventoryData,
                'selectedMonth' => $selectedMonth
            ];

            // Render Master Layout với View con
            $this->view('AdminDashBoard/MasterLayout', $data);
        } catch (Exception $e) {
            error_log("Error in InventoryCheckMonthController: " . $e->getMessage());
            echo "Lỗi: " . $e->getMessage();
        }
    }

    /**
     * API lấy dữ liệu theo tháng (cho AJAX)
     */
    public function GetDataByMonth() {
        header('Content-Type: application/json');

        try {
            $month = isset($_GET['month']) ? (int)$_GET['month'] : null;

            if ($month) {
                $data = $this->inventoryCheckService->getInventoryCheckBySpecificMonth($month);
            } else {
                $data = $this->inventoryCheckService->getInventoryCheckByMonth();
            }

            // Chuyển entities thành array
            $result = array_map(function($entity) {
                return $entity->toArray();
            }, $data);

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error in GetDataByMonth: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
