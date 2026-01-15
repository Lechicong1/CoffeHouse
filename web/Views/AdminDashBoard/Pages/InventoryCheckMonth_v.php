<?php
/**
 * filepath: /web/Views/AdminDashBoard/Pages/InventoryCheckMonth_v.php
 * Trang Báo Cáo Thất Thoát Kho Theo Khoảng Thời Gian - Admin Dashboard
 * Minimalist White Design
 */

// Lấy dữ liệu từ Controller
$fromDate = $data['fromDate'] ?? date('Y-m-01');
$toDate = $data['toDate'] ?? date('Y-m-d');
?>

<!-- Include CSS cho trang này (sử dụng lại CSS của inventory-check-page) -->
<link rel="stylesheet" href="Public/Css/inventory-check-page.css">

<div class="page-container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-left">
            <h2 class="section-title">📊 THẤT THOÁT KHO</h2>
            <p class="section-subtitle">Báo cáo thất thoát nguyên liệu theo khoảng thời gian</p>
        </div>
        <div class="header-right">
            <form method="GET" class="filter-form" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <input type="hidden" name="url" value="InventoryCheckMonth">
                <div class="filter-group">
                    <label for="from_date" class="filter-label">Từ ngày:</label>
                    <input type="date" id="from_date" name="from_date" class="date-input filter-input"
                           value="<?= htmlspecialchars($fromDate) ?>" required>
                </div>
                <div class="filter-group">
                    <label for="to_date" class="filter-label">Đến ngày:</label>
                    <input type="date" id="to_date" name="to_date" class="date-input filter-input"
                           value="<?= htmlspecialchars($toDate) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <span class="btn-icon">🔍</span>
                    <span class="btn-text">LỌC</span>
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='?url=InventoryCheckMonth'">
                    <span class="btn-icon">🔄</span>
                    <span class="btn-text">LÀM MỚI</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="content-section">
        <!-- Inventory Loss Report Table -->
        <div class="data-card">
            <div class="table-container">
                <table class="data-table" id="monthlyInventoryTable">
                    <thead>
                        <tr>
                            <th>NGUYÊN LIỆU</th>
                            <th>NGÀY KIỂM KHO</th>
                            <th>TỔNG LÝ THUYẾT</th>
                            <th>TỔNG THỰC TẾ</th>
                            <th>TỔNG CHÊNH LỆCH</th>
                        </tr>
                    </thead>
                    <tbody id="monthlyInventoryTableBody">
                        <?php if (!empty($data['inventoryData'])): ?>
                            <?php foreach ($data['inventoryData'] as $item): ?>
                                <tr>
                                    <td class="ingredient-name"><?= htmlspecialchars($item->getIngredient()) ?></td>
                                    <td class="date-col"><?= date('d/m/Y', strtotime($item->getCheckedAt())) ?></td>
                                    <td class="quantity-col"><?= number_format($item->getTheoryQuantity(), 2) ?></td>
                                    <td class="quantity-col"><?= number_format($item->getActualQuantity(), 2) ?></td>
                                    <td class="difference-col <?= $item->getDifference() < 0 ? 'negative' : ($item->getDifference() > 0 ? 'positive' : 'zero') ?>">
                                        <?= number_format($item->getDifference(), 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-message">
                                    📭 Không có dữ liệu kiểm kho trong khoảng thời gian này
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script hiển thị thông báo lỗi -->
<?php if (!empty($data['errorMessage'])): ?>
<script>
    alert("Lỗi: <?= addslashes($data['errorMessage']) ?>");
</script>
<?php endif; ?>
