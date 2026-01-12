<?php
// Extract data from Controller
$fromDate = $data['fromDate'] ?? date('Y-m-01');
$toDate = $data['toDate'] ?? date('Y-m-d');
$categoryId = $data['categoryId'] ?? 'all';
$sortBy = $data['sortBy'] ?? 'desc';
$categories = $data['categories'] ?? [];
$summary = $data['summary'] ?? ['total_volume'=>0, 'total_revenue'=>0, 'best_seller_qty'=>'N/A', 'best_seller_rev'=>'N/A'];
$details = $data['details'] ?? [];
$error = $data['error'] ?? null;
?>
<link rel="stylesheet" href="Public/Css/product-report.css">
<style>
    .alert-error {
        background-color: #fde8e8;
        border: 1px solid #f8b4b4;
        color: #c81e1e;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
    }
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }

    /* Override header text color in report tables: remove custom/brand color and use default dark text */
    .report-table thead th {
        color: #222 !important;
        /* ensure any font-weight or text-transform remains but color is neutral */
    }
    .btn-filter, .btn-export {
        background: #B6DA9F !important; /* Màu xanh cốm (giống màu voucher trong POS) */
        color: white !important;        /* Chữ trắng */
        border: none !important;        /* Bỏ viền xấu */
        padding: 10px 24px !important;  /* Làm nút to ra */
        border-radius: 4px !important;  /* Bo góc nhẹ */
        cursor: pointer !important;     /* Con chuột biến thành bàn tay */
        transition: all 0.3s ease !important; /* Hiệu ứng mượt khi di chuột */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important; /* Đổ bóng nhẹ */
    }
    /* Khi di chuột vào: Nút đậm màu hơn, nổi lên một chút */
    .btn-filter:hover, .btn-export:hover {
        background: #98C480 !important;
        transform: translateY(-1px) !important;
    }

    /* Khi bấm xuống: Nút lún xuống lại vị trí cũ */
    .btn-filter:active, .btn-export:active {
        transform: translateY(0) !important;
    }
    .section-title-wrapper {
        display: flex;
        justify-content: space-between; /* Đẩy tiêu đề sang trái, nút sang phải */
        align-items: center;            /* Căn giữa theo chiều dọc */
        margin-bottom: 20px;
    }
</style>
<div class="product-report-container">
    <div class="report-header">
        <h2>Báo Cáo Hiệu Suất Sản Phẩm</h2>

        <!-- Form lọc dữ liệu -->
        <form method="GET" action="" class="filter-form">

            <div class="filter-group">
                <label>Từ ngày:</label>
                <input type="date" name="from_date" value="<?php echo date('Y-m-d', strtotime($fromDate)); ?>" required>
            </div>

            <div class="filter-group">
                <label>Đến ngày:</label>
                <input type="date" name="to_date" value="<?php echo date('Y-m-d', strtotime($toDate)); ?>" required>
            </div>

            <div class="filter-group">
                <label>Danh mục:</label>
                <select name="category_id">
                    <option value="all">Tất cả danh mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($categoryId == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Sắp xếp / Lọc:</label>
                <select name="sort_by">
                    <option value="desc" <?php echo ($sortBy == 'desc') ? 'selected' : ''; ?>>Doanh thu cao ➝ thấp</option>
                    <option value="asc" <?php echo ($sortBy == 'asc') ? 'selected' : ''; ?>>Doanh thu thấp ➝ cao</option>
                    <option value="top_5_high" <?php echo ($sortBy == 'top_5_high') ? 'selected' : ''; ?>>⭐ Top 5 Doanh Thu Cao Nhất</option>
                    <option value="top_5_low" <?php echo ($sortBy == 'top_5_low') ? 'selected' : ''; ?>>⚠️ Top 5 Doanh Thu Thấp Nhất</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">Lọc Dữ Liệu</button>
        </form>
    </div>

    <?php if ($error): ?>
        <div class="alert-error">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <!-- 1. KHU VỰC TỔNG QUAN (4 KPI CARDS) -->
    <div class="kpi-grid">
        <!-- Tổng Số Ly -->
        <div class="kpi-card">
            <div class="kpi-icon volume-icon">☕</div>
            <div class="kpi-content">
                <h3>Tổng Số Ly</h3>
                <p class="kpi-value"><?php echo number_format($summary['total_volume']); ?></p>
                <span class="kpi-desc">Ly bán ra</span>
            </div>
        </div>

        <!-- Tổng Doanh Thu -->
        <div class="kpi-card">
            <div class="kpi-icon revenue-icon">💰</div>
            <div class="kpi-content">
                <h3>Tổng Doanh Thu</h3>
                <p class="kpi-value"><?php echo number_format($summary['total_revenue']); ?> đ</p>
                <span class="kpi-desc">Doanh thu thuần</span>
            </div>
        </div>

        <!-- Best Seller (Volume) -->
        <div class="kpi-card">
            <div class="kpi-icon star-icon">🏆</div>
            <div class="kpi-content">
                <h3>Bán Chạy Nhất</h3>
                <p class="kpi-value small-text" title="<?php echo htmlspecialchars($summary['best_seller_qty']); ?>">
                    <?php echo htmlspecialchars(mb_strimwidth($summary['best_seller_qty'], 0, 20, "...")); ?>
                </p>
                <span class="kpi-desc">Theo số lượng</span>
            </div>
        </div>

        <!-- Highest Revenue -->
        <div class="kpi-card">
            <div class="kpi-icon diamond-icon">💎</div>
            <div class="kpi-content">
                <h3>Doanh Thu Cao Nhất</h3>
                <p class="kpi-value small-text" title="<?php echo htmlspecialchars($summary['best_seller_rev']); ?>">
                    <?php echo htmlspecialchars(mb_strimwidth($summary['best_seller_rev'], 0, 20, "...")); ?>
                </p>
                <span class="kpi-desc">Mang về nhiều tiền nhất</span>
            </div>
        </div>
    </div>

    <!-- 2. KHU VỰC CHI TIẾT (ADVANCED TABLE) -->
    <div class="table-section">
        <div class="section-title-wrapper">
            <div class="section-title">Chi Tiết Hiệu Quả Từng Món</div>

            <!-- Form xuất Excel -->
            <form method="POST" action="/COFFEE_PHP/ProductReportController/xuatexcel" class="export-form-inline">
                <input type="hidden" name="from_date" value="<?php echo date('Y-m-d', strtotime($fromDate)); ?>">
                <input type="hidden" name="to_date" value="<?php echo date('Y-m-d', strtotime($toDate)); ?>">
                <input type="hidden" name="category_id" value="<?php echo $categoryId; ?>">
                <input type="hidden" name="sort_by" value="<?php echo $sortBy; ?>">
                <button type="submit" name="btnXuatexcel" class="btn-export">Xuất Excel</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">STT</th>
                        <th width="30%" class="text-left">Sản Phẩm</th>
                        <th width="15%" class="text-center">Danh Mục</th>
                        <th width="10%" class="text-right">Số Lượng</th>
                        <th width="15%" class="text-right">Doanh Thu</th>
                        <th width="15%" class="text-left">Tỷ Trọng (%)</th>
                        <th width="10%" class="text-right">Giá Trung bình</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($details)): ?>
                        <tr><td colspan="7" align="center">Không có dữ liệu trong khoảng thời gian này.</td></tr>
                    <?php else: ?>
                        <?php $i = 1; foreach ($details as $row): ?>
                        <tr>
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td>
                                <div class="product-info-cell">
                                    <span><?php echo htmlspecialchars($row['product_name']); ?></span>
                                </div>
                            </td>
                            <td class="text-center"><span class="badge-cat"><?php echo htmlspecialchars($row['category_name']); ?></span></td>
                            <td class="text-right"><?php echo number_format($row['total_quantity']); ?></td>
                            <td class="text-right bold"><?php echo number_format($row['total_revenue']); ?> đ</td>
                            <td class="text-left">
                                <div class="progress-wrapper">
                                    <span class="progress-text"><?php echo $row['percent']; ?>%</span>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: <?php echo $row['percent']; ?>%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right"><?php echo number_format($row['avg_price']); ?> đ</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
