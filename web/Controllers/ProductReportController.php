<?php
require_once __DIR__ . '/../Services/ProductReportService.php';

class ProductReportController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new ProductReportService();
    }

    public function index()
    {
        // Nhận filter từ request GET, mặc định là tháng hiện tại
        $reqFromDate = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
        $reqToDate = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
        
        // Tạo full datetime cho query
        $fromDate = $reqFromDate . ' 00:00:00';
        $toDate = $reqToDate . ' 23:59:59';
        
        $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : 'all';
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'desc';

        $error = null;
        // Default empty data
        $reportData = [
            'summary' => ['total_volume'=>0, 'total_revenue'=>0, 'best_seller_qty'=>'N/A', 'best_seller_rev'=>'N/A'],
            'details' => []
        ];

        // VALIDATION: Backend Check
        // 1. Kiểm tra ngày bắt đầu > ngày kết thúc (Ngược)
        if (strtotime($fromDate) > strtotime($toDate)) {
            $error = "Lỗi: Ngày bắt đầu không thể lớn hơn ngày kết thúc!";
        }
        // 2. Kiểm tra ngày tương lai (Future dates)
        elseif (strtotime($reqFromDate) > time()) {
             $error = "Lỗi: Ngày báo cáo không được ở trong tương lai!";
        }
        else {
             // Dữ liệu hợp lệ mới truy vấn DB
             $reportData = $this->service->getProductReportData($fromDate, $toDate, $categoryId, $sortBy);
        }

        $categories = $this->service->getCategories();

        // Biến view
        $viewData = [
            'page' => 'ProductReport_v', // View file name in Pages folder
            'section' => 'product_report', // Active menu item
            'summary' => $reportData['summary'],
            'details' => $reportData['details'],
            'categories' => $categories,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'categoryId' => $categoryId,
            'sortBy' => $sortBy,
            'error' => $error
        ];
        
        // Trả về view thông qua MasterLayout
        $this->view('AdminDashBoard/MasterLayout', $viewData);
    }
}

