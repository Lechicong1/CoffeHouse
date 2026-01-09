<?php
/**
 * VIEW - Báo cáo thống kê chi tiêu & lợi nhuận (Pure PHP - No JavaScript)
 * Hiển thị các card thống kê và chi tiết bằng PHP thuần
 */

// Lấy dữ liệu từ Controller
$report = $data['report'] ?? [];
$fromDate = $data['from_date'] ?? date('Y-m-01');
$toDate = $data['to_date'] ?? date('Y-m-d');
$showEmployeeDetails = isset($_GET['show_employees']);
$showInventoryDetails = isset($_GET['show_inventory']);
?>

<!-- Import CSS riêng cho trang Report -->
<link rel="stylesheet" href="Public/Css/report-page.css">

<section id="report" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>💰 THỐNG KÊ CHI TIÊU & LỢI NHUẬN</h2>
            <p class="subtitle">Báo cáo tài chính của cửa hàng</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="date-filter-card">
        <form method="GET" class="date-filter-form">
            <input type="hidden" name="url" value="ReportController">
            <div class="filter-group">
                <label for="from_date">Từ ngày:</label>
                <input type="date" id="from_date" name="from_date" class="date-input" value="<?= $fromDate ?>" required>
            </div>
            <div class="filter-group">
                <label for="to_date">Đến ngày:</label>
                <input type="date" id="to_date" name="to_date" class="date-input" value="<?= $toDate ?>" required>
            </div>
            <button type="submit" class="btn-primary btn-filter">
                🔍 Lọc
            </button>
            <button type="button" class="btn-secondary btn-refresh" onclick="window.location.href='ReportController'">
                🔄 Làm mới
            </button>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards-container">
        <!-- Card 1: Tổng Thu -->
        <div class="stat-card card-revenue">
            <div class="card-icon">📥</div>
            <div class="card-content">
                <h3>Tổng Thu</h3>
                <p class="card-value"><?= number_format($report['total_revenue'] ?? 0, 0, ',', ',') ?> VNĐ</p>
            </div>
        </div>

        <!-- Card 2: Lương NV (Clickable) -->
        <div class="stat-card card-salary">
            <div class="card-icon">💼</div>
            <div class="card-content">
                <h3>Lương NV</h3>
                <p class="card-value"><?= number_format($report['total_salary'] ?? 0, 0, ',', ',') ?> VNĐ</p>
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&show_employees=1" class="card-hint">
                    👆 Nhấn để xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card 3: Nhập NVL (Clickable) -->
        <div class="stat-card card-inventory">
            <div class="card-icon">📦</div>
            <div class="card-content">
                <h3>Nhập NVL</h3>
                <p class="card-value"><?= number_format($report['total_inventory'] ?? 0, 0, ',', ',') ?> VNĐ</p>
                <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&show_inventory=1" class="card-hint">
                    👆 Nhấn để xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card 4: Tổng Chi -->
        <div class="stat-card card-expense">
            <div class="card-icon">📤</div>
            <div class="card-content">
                <h3>Tổng Chi</h3>
                <p class="card-value"><?= number_format($report['total_expense'] ?? 0, 0, ',', ',') ?> VNĐ</p>
            </div>
        </div>

        <!-- Card 5: Lợi Nhuận -->
        <div class="stat-card <?= ($report['profit'] ?? 0) >= 0 ? 'card-profit' : 'card-loss' ?>">
            <div class="card-icon"><?= ($report['profit'] ?? 0) >= 0 ? '💚' : '💔' ?></div>
            <div class="card-content">
                <h3>Lợi Nhuận</h3>
                <p class="card-value"><?= number_format($report['profit'] ?? 0, 0, ',', ',') ?> VNĐ</p>
            </div>
        </div>
    </div>

    <!-- Chi Tiết Nhân Viên & Lương -->
    <?php if ($showEmployeeDetails): ?>
    <div class="detail-section">
        <div class="section-title">
            <h3>📋 Danh Sách Nhân Viên & Lương</h3>
            <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>" class="btn-close">✖ Đóng</a>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên nhân viên</th>
                        <th>Vai trò</th>
                        <th>Lương</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $employees = $data['employees'] ?? [];
                    if (empty($employees)):
                    ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #95a5a6;">
                                📭 Không có dữ liệu nhân viên
                            </td>
                        </tr>
                    <?php else:
                        $roleMap = [
                            'ORDER' => 'Nhân viên Order',
                            'BARTENDER' => 'Nhân viên Pha chế',
                            'SHIPPER' => 'Nhân viên Giao hàng'
                        ];
                        foreach ($employees as $emp):
                            $roleDisplay = $roleMap[$emp->roleName] ?? $emp->roleName;
                    ?>
                        <tr>
                            <td><strong>#<?= $emp->id ?></strong></td>
                            <td><?= htmlspecialchars($emp->fullname) ?></td>
                            <td>
                                <span style="background: #B6DA9F; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?= $roleDisplay ?>
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #27ae60;">
                                <?= number_format($emp->luong ?? 0, 0, ',', ',') ?> VNĐ
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="modal-total">
                <strong>Tổng lương:</strong>
                <span><?= number_format($report['total_salary'] ?? 0, 0, ',', ',') ?> VNĐ</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi Tiết Nhập Nguyên Liệu -->
    <?php if ($showInventoryDetails): ?>
    <div class="detail-section">
        <div class="section-title">
            <h3>📦 Chi Tiết Nhập Nguyên Liệu</h3>
            <a href="?url=ReportController&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>" class="btn-close">✖ Đóng</a>
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
                            <td colspan="6" style="text-align: center; padding: 20px; color: #95a5a6;">
                                📭 Không có phiếu nhập nào trong khoảng thời gian này
                            </td>
                        </tr>
                    <?php else:
                        foreach ($imports as $item):
                    ?>
                        <tr>
                            <td><strong>#<?= $item['id'] ?></strong></td>
                            <td><?= htmlspecialchars($item['ingredient_name'] ?? 'N/A') ?></td>
                            <td style="font-weight: 600; color: #3498db;">
                                <?= number_format($item['import_quantity'], 2, ',', ',') ?>
                            </td>
                            <td><?= htmlspecialchars($item['unit'] ?? '') ?></td>
                            <td style="font-weight: 700; color: #e67e22;">
                                <?= number_format($item['total_cost'], 0, ',', ',') ?> VNĐ
                            </td>
                            <td><?= date('d/m/Y', strtotime($item['import_date'])) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="modal-total">
                <strong>Tổng chi phí nhập:</strong>
                <span><?= number_format($report['total_inventory'] ?? 0, 0, ',', ',') ?> VNĐ</span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>
