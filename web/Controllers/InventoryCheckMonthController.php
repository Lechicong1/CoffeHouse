<?php
/**
 * InventoryCheckMonthController - Xử lý báo cáo thất thoát kho theo khoảng thời gian
 */
class InventoryCheckMonthController extends Controller {

    private $inventoryCheckService;

    public function __construct() {
        $this->inventoryCheckService = $this->service('InventoryCheckService');
    }

    public function Index() {
        // Lấy khoảng thời gian từ request (nếu có)
        $fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
        $toDate = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

        // Biến lưu thông báo lỗi
        $errorMessage = null;
        $inventoryData = [];

        try {
            // Lấy dữ liệu theo khoảng thời gian
            $inventoryData = $this->inventoryCheckService->getInventoryCheckByDateRange($fromDate, $toDate);
        } catch (Exception $e) {
            error_log("Error in InventoryCheckMonthController: " . $e->getMessage());
            $errorMessage = $e->getMessage();

            // Reset về giá trị mặc định khi có lỗi
            $fromDate = date('Y-m-01');
            $toDate = date('Y-m-d');

            // Thử lấy dữ liệu với ngày mặc định
            try {
                $inventoryData = $this->inventoryCheckService->getInventoryCheckByDateRange($fromDate, $toDate);
            } catch (Exception $ex) {
                $inventoryData = [];
            }
        }

        // Truyền dữ liệu vào view
        $data = [
            'section' => 'inventory_check_month',
            'page' => 'InventoryCheckMonth_v',
            'inventoryData' => $inventoryData,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'errorMessage' => $errorMessage
        ];

        // Render Master Layout với View con
        $this->view('AdminDashBoard/MasterLayout', $data);
    }
}
