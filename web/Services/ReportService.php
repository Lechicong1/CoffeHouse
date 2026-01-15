<?php


include_once './web/Repositories/ReportRepo.php';
include_once './web/Services/InventoryImportService.php';

class ReportService extends Service {
    private $reportRepo;
    private $inventoryImportService;

    public function __construct() {
        $this->reportRepo = new ReportRepo();
        $this->inventoryImportService = new InventoryImportService();
    }

    public function getTotalSalaryExpense($fromDate, $toDate) {
        return $this->reportRepo->getSalaryEmployeeExpense($fromDate, $toDate);
    }

    public function getEmployeeDetails($fromDate, $toDate) {
        return $this->reportRepo->getAllSalaryEmployee($fromDate, $toDate);
    }

    public function getTotalInventoryExpense($fromDate, $toDate) {
        return $this->reportRepo->getInventoryExpense($fromDate, $toDate);
    }

    public function getTotalRevenue($fromDate, $toDate) {
        return $this->reportRepo->getTotalExpenseProduct($fromDate, $toDate);
    }

    public function getRevenueDetails($fromDate, $toDate) {
        return $this->reportRepo->getDetailRevenue($fromDate, $toDate);
    }

    public function getInventoryImportDetails($fromDate, $toDate) {
        return $this->inventoryImportService->getImportsByDateRange($fromDate, $toDate);
    }

     // bao cao tong hop
    public function getSummaryReport($fromDate, $toDate) {
        // tong luong nhan vien
        $totalSalary = $this->getTotalSalaryExpense($fromDate, $toDate);

        // tong chi phi nguyen lieu
        $totalInventory = $this->getTotalInventoryExpense($fromDate, $toDate);

        // tong chi
        $totalExpense = $totalSalary + $totalInventory;

        // tong thu
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
