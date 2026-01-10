<?php
/**
 * VIEW - Báo cáo thống kê chi tiêu & lợi nhuận (Pure PHP Server)
 * Hoàn toàn không phụ thuộc JavaScript - Server-side rendering
 */

// Lấy dữ liệu từ Controller
$report = $data['report'] ?? [];
$fromDate = $data['from_date'] ?? date('Y-m-01');
$toDate = $data['to_date'] ?? date('Y-m-d');
$showRevenueDetails = isset($_GET['show_revenue']);
$showEmployeeDetails = isset($_GET['show_employees']);
$showInventoryDetails = isset($_GET['show_inventory']);
?>

<link rel="stylesheet" href="Public/Css/report-page.css">

<section id="report" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>💰 Báo cáo Thống kê</h2>
            <p class="subtitle">Báo cáo tài chính của cửa hàng</p>
        </div>
    </div>

    <!--
        DATE FILTER: GET request → ReportController/index
        Router sẽ gọi: ReportController->index() với params from_date, to_date
    -->
    <div class="date-filter-card">
        <form method="GET" class="date-filter-form">
            <input type="hidden" name="url" value="ReportController">
            <div class="filter-group">
                <label for="from_date">Từ ngày:</label>
                <input type="date" id="from_date" name="from_date" class="date-input"
                       value="<?= htmlspecialchars($fromDate) ?>" required>
            </div>
            <div class="filter-group">
                <label for="to_date">Đến ngày:</label>
                <input type="date" id="to_date" name="to_date" class="date-input"
                       value="<?= htmlspecialchars($toDate) ?>" required>
            </div>
            <button type="submit" class="btn-primary">🔍 Lọc</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='?url=ReportController'">
                🔄 Làm mới
            </button>
        </form>

        <!--
            XUẤT EXCEL: POST → ReportController/xuatexcel
            Router sẽ gọi: ReportController->xuatexcel()
        -->
        <form method="POST" action="?url=ReportController/xuatexcel" style="display: inline-block;">
            <input type="hidden" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
            <input type="hidden" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
            <button type="submit" name="btnXuatexcel" class="btn-primary">
                📊 Xuất Excel Tổng Hợp
            </button>
        </form>
    </div>

    <!-- Statistics Cards - Clickable để xem chi tiết -->
    <div class="stats-cards-container">
        <!-- Card 1: Tổng Thu -->
        <div class="stat-card card-revenue">
            <div class="card-icon">📥</div>
            <div class="card-content">
                <h3>Tổng Thu</h3>
                <p class="card-value"><?= number_format($report['total_revenue'] ?? 0, 0, ',', '.') ?> đ</p>
                <!-- GET → ReportController với param show_revenue=1 -->
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&show_revenue=1"
                   class="card-hint">👆 Xem chi tiết</a>
            </div>
        </div>

        <!-- Card 2: Lương NV -->
        <div class="stat-card card-salary">
            <div class="card-icon">💼</div>
            <div class="card-content">
                <h3>Lương NV</h3>
                <p class="card-value"><?= number_format($report['total_salary'] ?? 0, 0, ',', '.') ?> đ</p>
                <!-- GET → ReportController với param show_employees=1 -->
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&show_employees=1"
                   class="card-hint">👆 Xem chi tiết</a>
            </div>
        </div>

        <!-- Card 3: Nhập NVL -->
        <div class="stat-card card-inventory">
            <div class="card-icon">📦</div>
            <div class="card-content">
                <h3>Nhập NVL</h3>
                <p class="card-value"><?= number_format($report['total_inventory'] ?? 0, 0, ',', '.') ?> đ</p>
                <!-- GET → ReportController với param show_inventory=1 -->
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&show_inventory=1"
                   class="card-hint">👆 Xem chi tiết</a>
            </div>
        </div>

        <!-- Card 4: Tổng Chi -->
        <div class="stat-card card-expense">
            <div class="card-icon">📤</div>
            <div class="card-content">
                <h3>Tổng Chi</h3>
                <p class="card-value"><?= number_format($report['total_expense'] ?? 0, 0, ',', '.') ?> đ</p>
            </div>
        </div>

        <!-- Card 5: Lợi Nhuận -->
        <div class="stat-card <?= ($report['profit'] ?? 0) >= 0 ? 'card-profit' : 'card-loss' ?>">
            <div class="card-icon"><?= ($report['profit'] ?? 0) >= 0 ? '💚' : '💔' ?></div>
            <div class="card-content">
                <h3>Lợi Nhuận</h3>
                <p class="card-value"><?= number_format($report['profit'] ?? 0, 0, ',', '.') ?> đ</p>
            </div>
        </div>
    </div>

    <!-- Chi Tiết Doanh Thu Theo Sản Phẩm - Hiển thị bằng PHP -->
    <?php if ($showRevenueDetails): ?>
    <div class="detail-section">
        <div class="section-title">
            <h3>💰 Chi Tiết Doanh Thu Theo Sản Phẩm</h3>
            <div style="display: flex; gap: 10px;">
                <!-- Xuất Excel Chi Tiết Doanh Thu -->
                <form method="POST" action="?url=ReportController/xuatexcelRevenue" style="display: inline-block;">
                    <input type="hidden" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
                    <input type="hidden" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
                    <button type="submit" name="btnXuatexcelRevenue" class="btn-primary">
                        📊 Xuất Excel
                    </button>
                </form>
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>"
                   class="btn-close">✖ Đóng</a>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Số lượng bán</th>
                        <th>Tổng doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $revenueDetails = $data['revenue_details'] ?? [];
                    if (empty($revenueDetails)):
                    ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #9ca3af;">
                                📭 Không có dữ liệu doanh thu trong khoảng thời gian này
                            </td>
                        </tr>
                    <?php else:
                        $stt = 1;
                        foreach ($revenueDetails as $item):
                    ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($item['productName'] ?? 'N/A') ?></td>
                            <td>
                                <span style="background: #dbeafe; color: #1e40af; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?= htmlspecialchars($item['categoryName'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #f59e0b;">
                                <?= number_format($item['totalQuantitySold'], 0, ',', '.') ?>
                            </td>
                            <td style="font-weight: 700; color: #10b981;">
                                <?= number_format($item['totalRevenue'], 0, ',', '.') ?> đ
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="modal-total">
                <strong>Tổng doanh thu:</strong>
                <span><?= number_format($report['total_revenue'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi Tiết Nhân Viên & Lương - Hiển thị bằng PHP -->
    <?php if ($showEmployeeDetails): ?>
    <div class="detail-section">
        <div class="section-title">
            <h3>📋 Danh Sách Nhân Viên & Lương</h3>
            <div style="display: flex; gap: 10px;">
                <!-- Xuất Excel Chi Tiết Nhân Viên -->
                <form method="POST" action="?url=ReportController/xuatexcelEmployee" style="display: inline-block;">
                    <input type="hidden" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
                    <input type="hidden" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
                    <button type="submit" name="btnXuatexcelEmployee" class="btn-primary">
                        📊 Xuất Excel
                    </button>
                </form>
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>"
                   class="btn-close">✖ Đóng</a>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên nhân viên</th>
                        <th>Vai trò</th>
                        <th>Lương</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $employees = $data['employees'] ?? [];
                    if (empty($employees)):
                    ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #9ca3af;">
                                📭 Không có nhân viên trong khoảng thời gian này
                            </td>
                        </tr>
                    <?php else:
                        foreach ($employees as $emp):
                    ?>
                        <tr>
                            <td><strong>#<?= $emp['id'] ?></strong></td>
                            <td><?= htmlspecialchars($emp['fullname']) ?></td>
                            <td>
                                <span style="background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?= htmlspecialchars($emp['roleName']) ?>
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #10b981;">
                                <?= number_format($emp['luong'] ?? 0, 0, ',', '.') ?> đ
                            </td>
                            <td><?= date('d/m/Y', strtotime($emp['create_at'])) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="modal-total">
                <strong>Tổng lương (trong kỳ):</strong>
                <span><?= number_format($report['total_salary'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi Tiết Nhập Nguyên Liệu - Hiển thị bằng PHP -->
    <?php if ($showInventoryDetails): ?>
    <div class="detail-section">
        <div class="section-title">
            <h3>📦 Chi Tiết Nhập Nguyên Liệu</h3>
            <div style="display: flex; gap: 10px;">
                <!-- Xuất Excel Chi Tiết Nhập Kho -->
                <form method="POST" action="?url=ReportController/xuatexcelInventory" style="display: inline-block;">
                    <input type="hidden" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
                    <input type="hidden" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
                    <button type="submit" name="btnXuatexcelInventory" class="btn-primary">
                        📊 Xuất Excel
                    </button>
                </form>
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>"
                   class="btn-close">✖ Đóng</a>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên nguyên liệu</th>
                        <th>Số lượng</th>
                        <th>Đơn vị</th>
                        <th>Tổng tiền</th>
                        <th>Ngày nhập</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $imports = $data['inventory_imports'] ?? [];
                    if (empty($imports)):
                    ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #9ca3af;">
                                📭 Không có phiếu nhập nào trong khoảng thời gian này
                            </td>
                        </tr>
                    <?php else:
                        foreach ($imports as $item):
                    ?>
                        <tr>
                            <td><strong>#<?= $item['id'] ?></strong></td>
                            <td><?= htmlspecialchars($item['ingredient_name'] ?? 'N/A') ?></td>
                            <td style="font-weight: 600; color: #3b82f6;">
                                <?= number_format($item['import_quantity'], 2, ',', '.') ?>
                            </td>
                            <td><?= htmlspecialchars($item['unit'] ?? '') ?></td>
                            <td style="font-weight: 700; color: #f59e0b;">
                                <?= number_format($item['total_cost'], 0, ',', '.') ?> đ
                            </td>
                            <td><?= date('d/m/Y', strtotime($item['import_date'])) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="modal-total">
                <strong>Tổng chi phí nhập:</strong>
                <span><?= number_format($report['total_inventory'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- JS chỉ để validation form ngày tháng -->
<script src="Public/Js/report-page.js"></script>
