<?php
/**
 * filepath: /web/Views/AdminDashBoard/Pages/InventoryCheckMonth_v.php
 * Trang Báo Cáo Thất Thoát Kho Theo Tháng - Admin Dashboard
 * Minimalist White Design
 */
?>

<!-- Include CSS cho trang này (sử dụng lại CSS của inventory-check-page) -->
<link rel="stylesheet" href="Public/Css/inventory-check-page.css">

<div class="page-container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-left">
            <h2 class="section-title">📊 THẤT THOÁT KHO</h2>
            <p class="section-subtitle">Báo cáo thất thoát nguyên liệu theo tháng</p>
        </div>
        <div class="header-right">
            <div class="filter-group">
                <label for="monthFilter" class="filter-label">Chọn tháng:</label>
                <select id="monthFilter" class="filter-select">
                    <option value="">Tất cả</option>
                    <option value="1" <?= ($data['selectedMonth'] ?? null) == 1 ? 'selected' : '' ?>>Tháng 1</option>
                    <option value="2" <?= ($data['selectedMonth'] ?? null) == 2 ? 'selected' : '' ?>>Tháng 2</option>
                    <option value="3" <?= ($data['selectedMonth'] ?? null) == 3 ? 'selected' : '' ?>>Tháng 3</option>
                    <option value="4" <?= ($data['selectedMonth'] ?? null) == 4 ? 'selected' : '' ?>>Tháng 4</option>
                    <option value="5" <?= ($data['selectedMonth'] ?? null) == 5 ? 'selected' : '' ?>>Tháng 5</option>
                    <option value="6" <?= ($data['selectedMonth'] ?? null) == 6 ? 'selected' : '' ?>>Tháng 6</option>
                    <option value="7" <?= ($data['selectedMonth'] ?? null) == 7 ? 'selected' : '' ?>>Tháng 7</option>
                    <option value="8" <?= ($data['selectedMonth'] ?? null) == 8 ? 'selected' : '' ?>>Tháng 8</option>
                    <option value="9" <?= ($data['selectedMonth'] ?? null) == 9 ? 'selected' : '' ?>>Tháng 9</option>
                    <option value="10" <?= ($data['selectedMonth'] ?? null) == 10 ? 'selected' : '' ?>>Tháng 10</option>
                    <option value="11" <?= ($data['selectedMonth'] ?? null) == 11 ? 'selected' : '' ?>>Tháng 11</option>
                    <option value="12" <?= ($data['selectedMonth'] ?? null) == 12 ? 'selected' : '' ?>>Tháng 12</option>
                </select>
            </div>
            <button class="btn btn-primary" id="filterByMonthBtn">
                <span class="btn-icon">🔍</span>
                <span class="btn-text">TRA THEO THÁNG</span>
            </button>
            <button class="btn btn-secondary" id="refreshMonthlyBtn">
                <span class="btn-icon">🔄</span>
                <span class="btn-text">LÀM MỚI</span>
            </button>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="content-section">
        <!-- Monthly Inventory Loss Report Table -->
        <div class="data-card">
            <div class="table-container">
                <table class="data-table" id="monthlyInventoryTable">
                    <thead>
                        <tr>
                            <th>NGUYÊN LIỆU</th>
                            <th>THÁNG</th>
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
                                    <td class="month-col">Tháng <?= htmlspecialchars($item->getMonth()) ?></td>
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
                                    📭 Không có dữ liệu kiểm kho
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript xử lý Filter -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthFilter = document.getElementById('monthFilter');
    const filterBtn = document.getElementById('filterByMonthBtn');
    const refreshBtn = document.getElementById('refreshMonthlyBtn');

    // Xử lý nút "Tra theo tháng"
    filterBtn.addEventListener('click', function() {
        const selectedMonth = monthFilter.value;

        if (selectedMonth) {
            window.location.href = 'http://localhost/COFFEE_PHP/InventoryCheckMonthController?month=' + selectedMonth;
        } else {
            window.location.href = 'http://localhost/COFFEE_PHP/InventoryCheckMonthController';
        }
    });

    // Xử lý nút "Làm mới"
    refreshBtn.addEventListener('click', function() {
        monthFilter.value = '';
        window.location.href = 'http://localhost/COFFEE_PHP/InventoryCheckMonthController';
    });

    // Cho phép filter khi nhấn Enter
    monthFilter.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            filterBtn.click();
        }
    });
});
</script>

<style>
/* Styles riêng cho trang Thất thoát */
.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-right: 15px;
}

.filter-label {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    text-transform: uppercase;
}

.filter-select {
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    background-color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 150px;
}

.filter-select:hover {
    border-color: #B6DA9F;
}

.filter-select:focus {
    outline: none;
    border-color: #B6DA9F;
    box-shadow: 0 0 0 3px rgba(182, 218, 159, 0.2);
}

.month-col {
    text-align: center;
    font-weight: 600;
    color: #555;
}

.quantity-col {
    text-align: right;
    font-weight: 500;
    color: #333;
}

.difference-col {
    text-align: right;
    font-weight: 700;
    font-size: 15px;
}

.difference-col.positive {
    color: #28a745;
}

.difference-col.negative {
    color: #dc3545;
}

.difference-col.zero {
    color: #6c757d;
}

.ingredient-name {
    font-weight: 600;
    color: #333;
}

.empty-message {
    text-align: center;
    padding: 50px 20px;
    color: #999;
    font-size: 16px;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}
</style>
