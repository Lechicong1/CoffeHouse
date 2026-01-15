<?php

require_once __DIR__ . '/../../Config/ExcelHelper.php';

class ReportController extends Controller {
    private $reportService;

    function __construct() {
        $this->reportService = $this->service('ReportService');
    }

    function GetData() {
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');

        // Lấy báo cáo tổng hợp
        $report = $this->reportService->getSummaryReport($fromDate, $toDate);

        $viewData = [
            'page' => 'Report_v',
            'section' => 'report',
            'report' => $report,
            'from_date' => $fromDate,
            'to_date' => $toDate
        ];

        if (isset($_GET['show_revenue'])) {
            $viewData['revenue_details'] = $this->reportService->getRevenueDetails($fromDate, $toDate);
        }

        if (isset($_GET['show_employees'])) {
            $viewData['employees'] = $this->reportService->getEmployeeDetails($fromDate, $toDate);
        }

        if (isset($_GET['show_inventory'])) {
            $viewData['inventory_imports'] = $this->reportService->getInventoryImportDetails($fromDate, $toDate);
        }

        $this->view('AdminDashBoard/MasterLayout', $viewData);
    }

    //bao cao tong hop
    function xuatexcel() {
        if (isset($_POST['btnXuatexcel'])) {
            $fromDate = $_POST['from_date'] ?? date('Y-m-01');
            $toDate = $_POST['to_date'] ?? date('Y-m-d');
            $report = $this->reportService->getSummaryReport($fromDate, $toDate);

            $data = [
                ['category' => 'TỔNG THU', 'value' => $report['total_revenue'] ?? 0, 'description' => 'Doanh thu từ bán hàng'],
                ['category' => 'Lương Nhân Viên', 'value' => $report['total_salary'] ?? 0, 'description' => 'Chi phí lương nhân viên'],
                ['category' => 'Nhập Nguyên Liệu', 'value' => $report['total_inventory'] ?? 0, 'description' => 'Chi phí nhập nguyên liệu'],
                ['category' => 'TỔNG CHI', 'value' => $report['total_expense'] ?? 0, 'description' => 'Lương + Nhập NVL'],
                ['category' => 'LỢI NHUẬN', 'value' => $report['profit'] ?? 0, 'description' => 'Thu - Chi']
            ];

            $headers = ['category' => 'Hạng Mục', 'value' => 'Giá Trị (VNĐ)', 'description' => 'Mô Tả'];
            $fileName = 'BaoCaoTongHop_' . date('d-m-Y', strtotime($fromDate)) . '_den_' . date('d-m-Y', strtotime($toDate));
            ExcelHelper::exportToExcel($data, $headers, $fileName);
        }
    }

    function xuatexcelRevenue() {
        if (isset($_POST['btnXuatexcelRevenue'])) {
            $fromDate = $_POST['from_date'] ?? date('Y-m-01');
            $toDate = $_POST['to_date'] ?? date('Y-m-d');
            $revenueDetails = $this->reportService->getRevenueDetails($fromDate, $toDate);

            // Chuyển đổi array sang format Excel (dữ liệu từ DB là array, không phải object)
            $data = array_map(function($item) {
                return [
                    'productName' => $item['productName'] ?? '-',
                    'categoryName' => $item['categoryName'] ?? '-',
                    'totalQuantitySold' => $item['totalQuantitySold'] ?? 0,
                    'totalRevenue' => number_format($item['totalRevenue'] ?? 0, 0, ',', '.')
                ];
            }, $revenueDetails);

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

    //xuat luong nhan vien
    function xuatexcelEmployee() {
        if (isset($_POST['btnXuatexcelEmployee'])) {
            $fromDate = $_POST['from_date'] ?? date('Y-m-01');
            $toDate = $_POST['to_date'] ?? date('Y-m-d');
            $employeeDetails = $this->reportService->getEmployeeDetails($fromDate, $toDate);

            // Chuyển đổi array sang format Excel (dữ liệu từ DB là array, không phải object)
            $data = array_map(function($employee) {
                return [
                    'id' => $employee['id'] ?? '-',
                    'fullname' => $employee['fullname'] ?? '-',
                    'roleName' => $employee['roleName'] ?? '-',
                    'luong' => number_format($employee['luong'] ?? 0, 0, ',', '.'),
                    'create_at' => $employee['create_at'] ?? '-'
                ];
            }, $employeeDetails);

            $headers = [
                'id' => 'ID',
                'fullname' => 'Tên Nhân Viên',
                'roleName' => 'Vai Trò',
                'luong' => 'Lương (VNĐ)',
                'create_at' => 'Ngày Tạo'
            ];

            $fileName = 'ChiTietNhanVienVaLuong_' . date('d-m-Y', strtotime($fromDate)) . '_den_' . date('d-m-Y', strtotime($toDate));
            ExcelHelper::exportToExcel($data, $headers, $fileName);
        }
    }

    // xuat nhap nguyen lieu
    function xuatexcelInventory() {
        if (isset($_POST['btnXuatexcelInventory'])) {
            $fromDate = $_POST['from_date'] ?? date('Y-m-01');
            $toDate = $_POST['to_date'] ?? date('Y-m-d');
            $inventoryDetails = $this->reportService->getInventoryImportDetails($fromDate, $toDate);

            // Chuyển đổi array sang format Excel (dữ liệu từ DB là array, không phải object)
            $data = array_map(function($item) {
                return [
                    'ingredient_name' => $item['ingredient_name'] ?? '-',
                    'import_quantity' => $item['import_quantity'] ?? 0,
                    'unit' => $item['unit'] ?? '-',
                    'total_cost' => number_format($item['total_cost'] ?? 0, 0, ',', '.'),
                    'import_date' => $item['import_date'] ?? '-',
                    'note' => $item['note'] ?? '-'
                ];
            }, $inventoryDetails);

            $headers = [
                'ingredient_name' => 'Tên Nguyên Liệu',
                'import_quantity' => 'Số Lượng',
                'unit' => 'Đơn Vị',
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
