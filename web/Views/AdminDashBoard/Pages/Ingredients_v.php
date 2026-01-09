<?php
/**
 * VIEW CON - Quản lý Nguyên liệu
 * Chỉ hiển thị dữ liệu, không xử lý logic
 */

// Lấy dữ liệu từ Controller (đã truyền qua $data)
$ingredients = $data['ingredients'] ?? [];
$stats = $data['stats'] ?? ['total' => 0];
$keyword = $data['keyword'] ?? '';
// Helper functions removed. Display logic moved to Client (JS).
?>

<!-- Import CSS riêng cho trang Ingredients -->
<link rel="stylesheet" href="Public/Css/ingredients-page.css">

<section id="ingredients" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>📦 Quản lý Nguyên liệu</h2>
            <p class="subtitle">Tổng số: <strong><?= $stats['total'] ?></strong> nguyên liệu</p>
        </div>
        <div class="header-actions">
            <!-- Button Thêm mới -->
            <button class="btn-primary" onclick="openIngredientModal('add')">
                ➕ Thêm nguyên liệu mới
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 24px;">
        <form method="GET" action="" class="search-form">
            <input type="hidden" name="url" value="Ingredient/timkiem">
            <input type="text" name="search" class="search-input" placeholder="🔍 Tìm kiếm theo tên, đơn vị..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn-primary">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Ingredients Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên nguyên liệu</th>
                    <th>Đơn vị</th>
                    <th>Số lượng tồn kho</th>
                    <th>Hạn sử dụng</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ingredients)): ?>
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #999;">
                            📭 Không có nguyên liệu nào!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $i = 1;
                    foreach ($ingredients as $ingredient):
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><strong><?= htmlspecialchars($ingredient->name) ?></strong></td>
                            <td><?= htmlspecialchars($ingredient->unit) ?></td>
                            <td style="font-weight: 600;">
                                <?= number_format($ingredient->stock_quantity, 0, ',', '.') . ' ' . htmlspecialchars($ingredient->unit) ?>
                            </td>
                            <!-- Cột Hạn sử dụng - Xử lý hiển thị bằng JS -->
                            <td class="col-expiry" data-date="<?= $ingredient->expiry_date ?>"></td>
                            
                            <!-- Cột Tồn kho - Xử lý hiển thị bằng JS -->
                            <td class="col-stock" data-qty="<?= $ingredient->stock_quantity ?>"></td>
                            
                            <!-- Cột Trạng thái - Xử lý hiển thị bằng JS -->
                            <td class="col-status" data-active="<?= $ingredient->is_active ?>"></td>
                            <td>
                                <button class="btn-edit" onclick='openIngredientModal("edit", <?= htmlspecialchars(json_encode($ingredient->toArray())) ?>)' title="Sửa">
                                    ✏️ Sửa
                                </button>
                                <form method="POST" action="?url=Ingredient/del" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                    <input type="hidden" name="txtId" value="<?= $ingredient->id ?>">
                                    <button type="submit" name="btnXoa" class="btn-delete">🗑️ Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal Form Thêm/Sửa Nguyên liệu -->
<div id="ingredientModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title-wrapper">
                <div class="modal-icon">📦</div>
                <h3 id="modalTitle">Thêm nguyên liệu mới</h3>
            </div>
            <button class="btn-close-modal" onclick="closeIngredientModal()">×</button>
        </div>

        <form id="ingredientForm" method="POST" action="">
            <input type="hidden" id="ingredientId" name="txtId">
            <input type="hidden" id="formAction" name="action" value="create">

            <!-- Chỉ có 2 trường: Tên + Đơn vị -->
            <div class="form-grid">
                <div class="form-group">
                    <label><span class="label-icon">📝</span> Tên nguyên liệu</label>
                    <input type="text" id="name" name="txtName" required placeholder="VD: Cà phê Robusta">
                </div>

                <div class="form-group">
                    <label><span class="label-icon">📏</span> Đơn vị</label>
                    <input type="text" id="unit" name="txtUnit" required placeholder="VD: kg, lít, gói">
                </div>

                <div class="form-group">
                    <label><span class="label-icon">📅</span> Hạn sử dụng</label>
                    <input type="date" id="expiryDate" name="txtExpiryDate">
                </div>
            </div>

            <!-- Thông báo về tồn kho -->
            <div class="info-box">
                <span class="info-icon">ℹ️</span>
                <div>
                    <strong>Lưu ý:</strong> Số lượng tồn kho sẽ được cập nhật ở chức năng "Nhập kho"
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeIngredientModal()">
                    <span>❌</span> Hủy bỏ
                </button>
                <button type="submit" id="submitBtn" class="btn-submit" name="btnThem">
                    <span>✅</span> Lưu lại
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script src="Public/Js/ingredients-page.js"></script>
