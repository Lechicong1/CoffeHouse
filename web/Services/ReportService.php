<?php
/**
 * ReportService - Xử lý logic nghiệp vụ cho báo cáo thống kê
 */

include_once './web/Repositories/ReportRepo.php';
include_once './web/Services/InventoryImportService.php';

class ReportService extends Service {
    private $reportRepo;
    private $inventoryImportService;

    public function __construct() {
        $this->reportRepo = new ReportRepo();
        $this->inventoryImportService = new InventoryImportService();
    }

    /**
     * Lấy tổng lương nhân viên theo khoảng thời gian
     */
    public function getTotalSalaryExpense($fromDate, $toDate) {
        return $this->reportRepo->getSalaryEmployeeExpense($fromDate, $toDate);
    }

    /**
     * Lấy danh sách nhân viên và lương theo thời gian (thay thế EmployeeService)
     */
    public function getEmployeeDetails($fromDate, $toDate) {
        return $this->reportRepo->getAllSalaryEmployee($fromDate, $toDate);
    }

    /**
     * Lấy tổng chi phí nhập nguyên liệu theo khoảng thời gian
     */
    public function getTotalInventoryExpense($fromDate, $toDate) {
        return $this->reportRepo->getInventoryExpense($fromDate, $toDate);
    }

    /**
     * Lấy tổng doanh thu từ đơn hàng (Tổng Thu thực tế)
     */
    public function getTotalRevenue($fromDate, $toDate) {
        return $this->reportRepo->getTotalExpenseProduct($fromDate, $toDate);
    }

    /**
     * Lấy chi tiết doanh thu theo sản phẩm
     */
    public function getRevenueDetails($fromDate, $toDate) {
        return $this->reportRepo->getDetailRevenue($fromDate, $toDate);
    }

    /**
     * Lấy chi tiết phiếu nhập nguyên liệu theo thời gian (cho modal)
     */
    public function getInventoryImportDetails($fromDate, $toDate) {
        return $this->inventoryImportService->getImportsByDateRange($fromDate, $toDate);
    }

    /**
     * Tính toán báo cáo tổng hợp
     */
    public function getSummaryReport($fromDate, $toDate) {
        // Lấy tổng lương nhân viên theo thời gian
        $totalSalary = $this->getTotalSalaryExpense($fromDate, $toDate);

        // Lấy tổng chi phí nhập nguyên liệu
        $totalInventory = $this->getTotalInventoryExpense($fromDate, $toDate);

        // Tổng chi
        $totalExpense = $totalSalary + $totalInventory;

        // Tổng thu từ đơn hàng
        $totalRevenue = $this->getTotalRevenue($fromDate, $toDate);

        // Lợi nhuận
        $profit = $totalRevenue - $totalExpense;

        return [
            'total_revenue' => $totalRevenue,
            'total_salary' => $totalSalary,
            'total_inventory' => $totalInventory,
            'total_expense' => $totalExpense,
            'profit' => $profit
        ];
    }
}
?>
