<?php
$imports = $data['imports'] ?? [];
$ingredients = $data['ingredients'] ?? [];
$keyword = $data['keyword'] ?? '';
?>

<!-- Import CSS riêng cho trang Inventory Imports -->
<link rel="stylesheet" href="Public/Css/inventory-imports-page.css">

<section id="inventory-imports" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>📥 Quản lý Nhập kho</h2>
            <p class="subtitle">Tổng số: <strong><?= count($imports) ?></strong> phiếu nhập</p>
        </div>
        <div class="header-actions">
            <!-- Button Xuất Excel -->
            <form method="POST" action="InventoryImportController/xuatexcel" style="display: inline-block; margin-right: 10px;">
                <input type="hidden" name="txtSearch" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" name="btnXuatexcel" class="btn-primary" style="background: #27ae60;">
                    📊 Xuất Excel
                </button>
            </form>

            <!-- Button Thêm mới -->
            <button class="btn-primary" onclick="openImportModal('add')">
                ➕ Tạo phiếu nhập
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 24px;">
        <form method="POST" action="?url=InventoryImportController/timkiem" class="search-form">
            <input type="text" name="txtSearch" class="search-input" placeholder="🔍 Tìm kiếm theo tên nguyên liệu hoặc ghi chú..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn-primary">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Imports Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Nguyên liệu</th>
                    <th>Số lượng</th>
                    <th>Tổng tiền</th>
                    <th>Ngày nhập</th>
                    <th>Ghi chú</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($imports)): ?>
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                            📭 Không có phiếu nhập nào!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $i = 1;
                    foreach ($imports as $import):
                        $ingredientName = 'N/A';
                        $unit = '';
                        foreach ($ingredients as $ing) {
                            if ($ing->id == $import->ingredient_id) {
                                $ingredientName = $ing->name;
                                $unit = $ing->unit;
                                break;
                            }
                        }
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($ingredientName) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($import->import_quantity) ?> <?= htmlspecialchars($unit) ?>
                            </td>
                            <td>
                                <span style="color: #2563eb; font-weight: 600;">
                                    <?= number_format($import->total_cost, 0, ',', '.') ?> đ
                                </span>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($import->import_date)) ?>
                            </td>
                            <td>
                                <span style="color: #777; font-size: 0.95em;">
                                    <?= htmlspecialchars(mb_strimwidth($import->note, 0, 50, "...")) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-edit" onclick='openImportModal("edit", <?= json_encode($import) ?>)' title="Sửa">
                                    ✏️ Sửa
                                </button>
                                <form method="POST" action="?url=InventoryImportController/delete" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phiếu nhập này không?')">
                                    <input type="hidden" name="id" value="<?= $import->id ?>">
                                    <button type="submit" class="btn-delete">🗑️ Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal Form Thêm/Sửa Phiếu Nhập -->
<div id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title-wrapper">
                <div class="modal-icon">📥</div>
                <h3 id="modalTitle">Tạo phiếu nhập mới</h3>
            </div>
            <span class="close" onclick="closeImportModal()">&times;</span>
        </div>
        <form id="importForm" method="POST" action="?url=InventoryImportController/store">
            <div class="modal-body">
                <input type="hidden" id="importId" name="id">
                
                <div class="form-group">
                    <label for="ingredientId">Nguyên liệu <span class="required">*</span></label>
                    <select id="ingredientId" name="ingredient_id" required class="custom-select">
                        <option value="">-- Chọn nguyên liệu --</option>
                        <?php foreach ($ingredients as $ing): ?>
                            <option value="<?= $ing->id ?>"><?= htmlspecialchars($ing->name) ?> (<?= htmlspecialchars($ing->unit) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="importQuantity">Số lượng nhập <span class="required">*</span></label>
                    <input type="number" id="importQuantity" name="import_quantity" required placeholder="Nhập số lượng" step="0.01">
                </div>

                <div class="form-group">
                    <label for="totalCost">Tổng tiền (VNĐ) <span class="required">*</span></label>
                    <input type="number" id="totalCost" name="total_cost" required placeholder="Nhập tổng tiền">
                </div>

                <div class="form-group">
                    <label for="importDate">Ngày nhập <span class="required">*</span></label>
                    <input type="date" id="importDate" name="import_date" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="note">Ghi chú</label>
                    <textarea id="note" name="note" rows="2" placeholder="Nhập ghi chú (nếu có)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeImportModal()">Hủy bỏ</button>
                <button type="submit" class="btn-primary" id="btnSave">Lưu lại</button>
            </div>
        </form>
    </div>
</div>

<script src="Public/Js/inventory-imports-page.js"></script>
