<?php
/**
 * ReportController - Quản lý báo cáo thống kê chi tiêu & lợi nhuận
 * Theo mô hình MVC chuẩn
 */
require_once __DIR__ . '/../../Config/ExcelHelper.php';

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

        // Nếu request yêu cầu xem chi tiết doanh thu (Tổng Thu)
        if (isset($_GET['show_revenue'])) {
            $viewData['revenue_details'] = $this->reportService->getRevenueDetails($fromDate, $toDate);
        }

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

    /**
     * Xuất Excel báo cáo tổng hợp
     */
    function xuatexcel() {
        if (isset($_POST['btnXuatexcel'])) {
            $fromDate = $_POST['from_date'] ?? date('Y-m-01');
            $toDate = $_POST['to_date'] ?? date('Y-m-d');

            // Lấy báo cáo tổng hợp
            $report = $this->reportService->getSummaryReport($fromDate, $toDate);

            // Chuyển đổi sang array để xuất Excel
            $data = [
                [
                    'category' => 'TỔNG THU',
                    'value' => number_format($report['total_revenue'] ?? 0, 0, ',', '.'),
                    'description' => 'Doanh thu từ bán hàng'
                ],
                [
                    'category' => 'Lương Nhân Viên',
                    'value' => number_format($report['total_salary'] ?? 0, 0, ',', '.'),
                    'description' => 'Chi phí lương toàn bộ nhân viên'
                ],
                [
                    'category' => 'Nhập Nguyên Liệu',
                    'value' => number_format($report['total_inventory'] ?? 0, 0, ',', '.'),
                    'description' => 'Chi phí nhập nguyên liệu trong kỳ'
                ],
                [
                    'category' => 'TỔNG CHI',
                    'value' => number_format($report['total_expense'] ?? 0, 0, ',', '.'),
                    'description' => 'Tổng chi phí (Lương + Nhập NVL)'
                ],
                [
                    'category' => 'LỢI NHUẬN',
                    'value' => number_format($report['profit'] ?? 0, 0, ',', '.'),
                    'description' => 'Thu - Chi = Lợi nhuận/Lỗ'
                ]
            ];

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'category' => 'Hạng Mục',
                'value' => 'Giá Trị (VNĐ)',
                'description' => 'Mô Tả'
            ];

            $fileName = 'BaoCaoTongHop_' . date('d-m-Y', strtotime($fromDate)) . '_den_' . date('d-m-Y', strtotime($toDate));
            ExcelHelper::exportToExcel($data, $headers, $fileName);
        }
    }

    /**
     * Xuất Excel chi tiết doanh thu theo sản phẩm
     */
    function xuatexcelRevenue() {
        if (isset($_POST['btnXuatexcelRevenue'])) {
            $fromDate = $_POST['from_date'] ?? date('Y-m-01');
            $toDate = $_POST['to_date'] ?? date('Y-m-d');

            // Lấy chi tiết doanh thu
            $revenueDetails = $this->reportService->getRevenueDetails($fromDate, $toDate);

            // Chuyển đổi sang array để xuất Excel
            $data = array_map(function($item) {
                return [
                    'productName' => $item['productName'] ?? 'N/A',
                    'categoryName' => $item['categoryName'] ?? 'N/A',
                    'totalQuantitySold' => $item['totalQuantitySold'],
                    'totalRevenue' => number_format($item['totalRevenue'], 0, ',', '.')
                ];
            }, $revenueDetails);

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'productName' => 'Tên Sản Phẩm',
                'categoryName' => 'Danh Mục',
                'totalQuantitySold' => 'Số Lượng Bán',
                'totalRevenue' => 'Tổng Doanh Thu (VNĐ)'
            ];

            $fileName = 'ChiTietDoanhThu_' . date('d-m-Y', strtotime($fromDate)) . '_den_' . date('d-m-Y', strtotime($toDate));
            ExcelHelper::exportToExcel($data, $headers, $fileName);
        }
    }

    /**
     * Xuất Excel chi tiết nhân viên và lương
     */
    function xuatexcelEmployee() {
        if (isset($_POST['btnXuatexcelEmployee'])) {
            // Lấy chi tiết nhân viên
            $employees = $this->reportService->getEmployeeDetails();

            $roleMap = [
                'ORDER' => 'Nhân viên Order',
                'BARTENDER' => 'Nhân viên Pha chế',
                'SHIPPER' => 'Nhân viên Giao hàng'
            ];

            // Chuyển đổi sang array để xuất Excel
            $data = array_map(function($emp) use ($roleMap) {
                return [
                    'id' => $emp->id,
                    'fullname' => $emp->fullname,
                    'roleName' => $roleMap[$emp->roleName] ?? $emp->roleName,
                    'luong' => number_format($emp->luong, 0, ',', '.')
                ];
            }, $employees);

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'id' => 'ID',
                'fullname' => 'Tên Nhân Viên',
                'roleName' => 'Vai Trò',
                'luong' => 'Lương (VNĐ)'
            ];

            ExcelHelper::exportToExcel($data, $headers, 'ChiTietNhanVienVaLuong');
        }
    }

    /**
     * Xuất Excel chi tiết nhập nguyên liệu
     */
    function xuatexcelInventory() {
        if (isset($_POST['btnXuatexcelInventory'])) {
            $fromDate = $_POST['from_date'] ?? date('Y-m-01');
            $toDate = $_POST['to_date'] ?? date('Y-m-d');

            // Lấy chi tiết nhập kho
            $imports = $this->reportService->getInventoryImportDetails($fromDate, $toDate);

            // Chuyển đổi sang array để xuất Excel
            $data = array_map(function($import) {
                return [
                    'ingredient_name' => $import->ingredient_name ?? 'N/A',
                    'import_quantity' => $import->import_quantity,
                    'total_cost' => number_format($import->total_cost, 0, ',', '.'),
                    'import_date' => date('d/m/Y', strtotime($import->import_date)),
                    'note' => $import->note ?? '-'
                ];
            }, $imports);

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'ingredient_name' => 'Tên Nguyên Liệu',
                'import_quantity' => 'Số Lượng Nhập',
                'total_cost' => 'Tổng Tiền (VNĐ)',
                'import_date' => 'Ngày Nhập',
                'note' => 'Ghi Chú'
            ];

            $fileName = 'ChiTietNhapNguyenLieu_' . date('d-m-Y', strtotime($fromDate)) . '_den_' . date('d-m-Y', strtotime($toDate));
            ExcelHelper::exportToExcel($data, $headers, $fileName);
        }
    }
}
?>
