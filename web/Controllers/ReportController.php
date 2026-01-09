<?php
/**
 * ReportController - Quản lý báo cáo thống kê chi tiêu & lợi nhuận
 * Theo mô hình MVC chuẩn
 */

class ReportController extends Controller {
    private $reportService;

    function __construct() {
        $this->reportService = $this->service('ReportService');
    }

    /**
     * Hiển thị trang báo cáo tổng hợp (Method mặc định)
     */
    function GetData() {
        // Lấy thời gian từ request hoặc mặc định
        $fromDate = $_GET['from_date'] ?? date('Y-m-01'); // Đầu tháng
        $toDate = $_GET['to_date'] ?? date('Y-m-d'); // Hôm nay

        // Lấy báo cáo tổng hợp
        $report = $this->reportService->getSummaryReport($fromDate, $toDate);

        // Dữ liệu truyền vào view
        $viewData = [
            'page' => 'Report_v',
            'section' => 'report',
            'report' => $report,
            'from_date' => $fromDate,
            'to_date' => $toDate
        ];

        // Nếu request yêu cầu xem chi tiết nhân viên
        if (isset($_GET['show_employees'])) {
            $viewData['employees'] = $this->reportService->getEmployeeDetails();
        }

        // Nếu request yêu cầu xem chi tiết nhập nguyên liệu
        if (isset($_GET['show_inventory'])) {
            $viewData['inventory_imports'] = $this->reportService->getInventoryImportDetails($fromDate, $toDate);
        }

        $this->view('AdminDashBoard/MasterLayout', $viewData);
    }

    /**
     * Lấy chi tiết nhân viên và lương (API cho modal - giữ lại để tương thích)
     */
    function GetEmployeeDetails() {
        header('Content-Type: application/json');

        try {
            $employees = $this->reportService->getEmployeeDetails();

            // Format dữ liệu cho frontend
            $data = [];
            foreach ($employees as $employee) {
                $data[] = [
                    'id' => $employee->id,
                    'name' => $employee->fullname,
                    'roleName' => $employee->roleName,
                    'salary' => $employee->luong
                ];
            }

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy chi tiết phiếu nhập nguyên liệu (API cho modal - giữ lại để tương thích)
     */
    function GetInventoryDetails() {
        header('Content-Type: application/json');

        try {
            $fromDate = $_GET['from_date'] ?? date('Y-m-01');
            $toDate = $_GET['to_date'] ?? date('Y-m-d');

            $imports = $this->reportService->getInventoryImportDetails($fromDate, $toDate);

            echo json_encode([
                'success' => true,
                'data' => $imports
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }
}
?>
