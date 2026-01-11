<?php
/**
 * InventoryCheckMonthController - Xử lý báo cáo thất thoát kho theo tháng
 */
class InventoryCheckMonthController extends Controller {

    private $inventoryCheckService;

    public function __construct() {
        $this->inventoryCheckService = $this->service('InventoryCheckService');
    }

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
}
