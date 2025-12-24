<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/sections/revenue.php
 * Revenue Statistics Section - Thống kê doanh thu
 */

// Mock data
$revenueStats = [
    'today' => '5,200,000₫',
    'week' => '35,400,000₫',
    'month' => '150,200,000₫',
    'year' => '1,850,000,000₫'
];

$topProducts = [
    ['name' => 'Cà phê sữa đá', 'sold' => 245, 'revenue' => '6,125,000₫'],
    ['name' => 'Trà đào cam sả', 'sold' => 189, 'revenue' => '6,615,000₫'],
    ['name' => 'Bạc xỉu', 'sold' => 156, 'revenue' => '4,680,000₫'],
];
?>
<section id="revenue" class="content-section">
    <div class="section-header">
        <h2>Thống kê Doanh thu</h2>
        <div class="header-actions">
            <select class="filter-select">
                <option value="today">Hôm nay</option>
                <option value="week">Tuần này</option>
                <option value="month" selected>Tháng này</option>
                <option value="year">Năm nay</option>
                <option value="custom">Tùy chỉnh</option>
            </select>
            <input type="date" class="date-input" value="<?= date('Y-m-d') ?>">
            <button class="btn-primary">📊 Xuất báo cáo</button>
        </div>
    </div>

    <!-- Revenue Overview -->
    <div class="stats-grid revenue-overview">
        <div class="stat-card large">
            <div class="stat-icon revenue">💰</div>
            <div class="stat-details">
                <h3>Doanh thu hôm nay</h3>
                <p class="stat-value"><?= $revenueStats['today'] ?></p>
                <span class="stat-change positive">+12.5% so với hôm qua</span>
            </div>
        </div>
        
        <div class="stat-card large">
            <div class="stat-icon orders">📅</div>
            <div class="stat-details">
                <h3>Doanh thu tuần</h3>
                <p class="stat-value"><?= $revenueStats['week'] ?></p>
                <span class="stat-change positive">+8.3% so với tuần trước</span>
            </div>
        </div>
        
        <div class="stat-card large">
            <div class="stat-icon customers">📈</div>
            <div class="stat-details">
                <h3>Doanh thu tháng</h3>
                <p class="stat-value"><?= $revenueStats['month'] ?></p>
                <span class="stat-change negative">-2.1% so với tháng trước</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-container">
            <h3>Biểu đồ doanh thu 7 ngày qua</h3>
            <div class="chart-placeholder">
                <p>📊 Biểu đồ sẽ được hiển thị ở đây</p>
                <p class="text-muted">Tích hợp Chart.js hoặc Google Charts</p>
            </div>
        </div>
        
        <div class="chart-container">
            <h3>Doanh thu theo danh mục</h3>
            <div class="chart-placeholder">
                <p>🥧 Biểu đồ tròn sẽ được hiển thị ở đây</p>
                <p class="text-muted">Cà phê: 45% | Trà: 30% | Nước ép: 15% | Khác: 10%</p>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="top-products-section">
        <h3>Top sản phẩm bán chạy</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng bán</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $index => $product): ?>
                        <tr>
                            <td><strong><?= $index + 1 ?></strong></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= $product['sold'] ?></td>
                            <td><strong><?= $product['revenue'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h4>Tổng đơn hàng</h4>
            <p class="summary-value">3,540</p>
            <span class="summary-label">đơn trong tháng</span>
        </div>
        <div class="summary-card">
            <h4>Giá trị TB/đơn</h4>
            <p class="summary-value">42,430₫</p>
            <span class="summary-label">trung bình</span>
        </div>
        <div class="summary-card">
            <h4>Khách hàng mới</h4>
            <p class="summary-value">128</p>
            <span class="summary-label">khách trong tháng</span>
        </div>
        <div class="summary-card">
            <h4>Tỷ lệ hoàn thành</h4>
            <p class="summary-value">97.8%</p>
            <span class="summary-label">đơn thành công</span>
        </div>
    </div>
</section>
