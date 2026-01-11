<?php
require_once __DIR__ . '/../Repositories/ProductReportRepository.php';

class ProductReportService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new ProductReportRepository();
    }

    public function getCategories()
    {
        return $this->repo->getAllCategories();
    }

    public function getProductReportData($fromDate, $toDate, $categoryId = null, $sortBy = 'desc')
    {
        $products = $this->repo->getProductReportData($fromDate, $toDate, $categoryId);

        // 2. Tính toán tổng để hiển thị lên 4 Card View
        $totalVolume = 0;
        $totalRevenue = 0;
        $bestSellerByQty = null;
        $bestSellerByRev = null;

        if (!empty($products)) {
            // Mặc định thằng đầu tiên (do đã sort revenue DESC) là thằng doanh thu cao nhất
            // Cần tính lại BestSeller Reveneu vì danh sách có thể bị filter sau này, 
            // nhưng logic BestSellerRev luôn là thằng active cao nhất trong group đang chọn
            $bestSellerByRev = $products[0];

            $maxQty = -1;
            foreach ($products as $p) {
                $totalVolume += $p['total_quantity'];
                $totalRevenue += $p['total_revenue'];

                // Tìm best seller theo số lượng
                if ($p['total_quantity'] > $maxQty) {
                    $maxQty = $p['total_quantity'];
                    $bestSellerByQty = $p;
                }
            }
        }

        // Tính % Tỷ trọng cho từng món
        foreach ($products as &$p) {
            $p['percent'] = ($totalRevenue > 0)
                ? round(($p['total_revenue'] / $totalRevenue) * 100, 2)
                : 0;
            
            // Tính giá bán hiện tại (TB)
            $p['avg_price'] = ($p['total_quantity'] > 0)
                ? $p['total_revenue'] / $p['total_quantity']
                : 0;
        }

        // 4. Xử lý Lọc/Sắp xếp danh sách chi tiết (Table)
        // Lưu ý: $summary ở trên vẫn giữ nguyên thống kê TOÀN BỘ (theo ngày/danh mục)
        // còn bảng chi tiết dưới đây sẽ bị cắt theo yêu cầu
        if ($sortBy === 'asc') {
            usort($products, function($a, $b) { return $a['total_revenue'] - $b['total_revenue']; });
        } elseif ($sortBy === 'top_5_high') {
            // SQL đã default DESC, chỉ cần slice
            $products = array_slice($products, 0, 5);
        } elseif ($sortBy === 'top_5_low') {
            // Sort ASC rồi lấy 5
            usort($products, function($a, $b) { return $a['total_revenue'] - $b['total_revenue']; });
            $products = array_slice($products, 0, 5);
        }

        return [
            'summary' => [
                'total_volume' => $totalVolume,
                'total_revenue' => $totalRevenue,
                'best_seller_qty' => $bestSellerByQty ? $bestSellerByQty['product_name'] : 'N/A',
                'best_seller_rev' => $bestSellerByRev ? $bestSellerByRev['product_name'] : 'N/A'
            ],
            'details' => $products
        ];
    }
}
