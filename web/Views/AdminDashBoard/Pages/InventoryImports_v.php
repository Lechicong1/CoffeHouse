<?php
$imports = $data['imports'] ?? [];
$ingredients = $data['ingredients'] ?? [];
$keyword = $data['keyword'] ?? '';
$showModal = isset($_GET['action']) && in_array($_GET['action'], ['add', 'edit']);
$editImport = null;

// Lấy dữ liệu cho modal edit
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    foreach ($imports as $import) {
        if ($import->id == $_GET['id']) {
            $editImport = $import;
            break;
        }
    }
}
?>

<link rel="stylesheet" href="Public/Css/inventory-imports-page.css">

<section id="inventory-imports" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>📥 Quản lý Nhập kho</h2>
            <p class="subtitle">Tổng số: <strong><?= count($imports) ?></strong> phiếu nhập</p>
        </div>
        <div class="header-actions">
            <!--
                XUẤT EXCEL: POST → InventoryImportController/xuatexcel
                Router sẽ gọi: InventoryImportController->xuatexcel()
            -->
            <form method="POST" action="?url=InventoryImportController/xuatexcel" style="display: inline;">
                <input type="hidden" name="txtSearch" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" name="btnXuatexcel" class="btn-primary">📊 Xuất Excel</button>
            </form>

            <!--
                THÊM MỚI: GET với param action=add
                Sẽ reload trang và hiện modal form thêm mới
            -->
            <a href="?url=InventoryImportController/GetData&action=add" class="btn-primary">➕ Tạo phiếu nhập</a>
        </div>
    </div>

    <!--
        TÌM KIẾM: POST → InventoryImportController/timkiem
        Router sẽ gọi: InventoryImportController->timkiem()
    -->
    <form method="POST" action="?url=InventoryImportController/timkiem" class="search-form">
        <input type="text" name="txtSearch" class="search-input"
               placeholder="🔍 Tìm kiếm theo tên nguyên liệu hoặc ghi chú..."
               value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit" class="btn-primary">Tìm kiếm</button>
        <?php if ($keyword): ?>
            <a href="?url=InventoryImportController/GetData" class="btn-secondary">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <!-- Table -->
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
                            <td><strong><?= htmlspecialchars($ingredientName) ?></strong></td>
                            <td><?= htmlspecialchars($import->import_quantity) ?> <?= htmlspecialchars($unit) ?></td>
                            <td style="color: #2563eb; font-weight: 600;">
                                <?= number_format($import->total_cost, 0, ',', '.') ?> đ
                            </td>
                            <td><?= date('d/m/Y', strtotime($import->import_date)) ?></td>
                            <td style="color: #777; font-size: 13px;">
                                <?= htmlspecialchars(mb_strimwidth($import->note, 0, 50, "...")) ?>
                            </td>
                            <td>
                                <!--
                                    SỬA: GET với param action=edit&id=X
                                    Sẽ reload trang và hiện modal form sửa với dữ liệu của phiếu nhập id=X
                                -->
                                <a href="?url=InventoryImportController/GetData&action=edit&id=<?= $import->id ?>"
                                   class="btn-edit">✏️ Sửa</a>

                                <!--
                                    XÓA: POST → InventoryImportController/delete
                                    Router sẽ gọi: InventoryImportController->delete()
                                -->
                                <form method="POST" action="?url=InventoryImportController/delete"
                                      style="display: inline;"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa phiếu nhập này không?')">
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

<!--
    MODAL FORM - Hiển thị bằng PHP thuần túy (KHÔNG DÙNG JS)
    - Chỉ hiện khi URL có ?action=add hoặc ?action=edit
    - Form submit TRỰC TIẾP đến Controller thông qua action attribute
-->
<?php if ($showModal): ?>
<div class="modal active" id="importModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title-wrapper">
                <h3><?= $_GET['action'] === 'add' ? 'Tạo phiếu nhập mới' : 'Cập nhật phiếu nhập' ?></h3>
            </div>
            <a href="?url=InventoryImportController/GetData" class="close">&times;</a>
        </div>

        <!--
            FORM SUBMIT: POST → InventoryImportController/store hoặc update
            - Nếu action=add: POST đến InventoryImportController/store
            - Nếu action=edit: POST đến InventoryImportController/update

            Router sẽ tự động:
            1. Cắt URL: InventoryImportController/store
            2. Gọi: new InventoryImportController()
            3. Gọi method: store() với dữ liệu POST
            4. Controller xử lý xong sẽ redirect về GetData (đóng modal)
        -->
        <form method="POST" action="?url=InventoryImportController/<?= $_GET['action'] === 'add' ? 'store' : 'update' ?>">
            <div class="modal-body">
                <?php if (isset($_GET['action']) && $_GET['action'] === 'edit'): ?>
                    <input type="hidden" name="id" value="<?= $editImport ? $editImport->id : '' ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="ingredientId">Nguyên liệu <span class="required">*</span></label>
                    <select name="ingredient_id" required>
                        <option value="">-- Chọn nguyên liệu --</option>
                        <?php foreach ($ingredients as $ing): ?>
                            <option value="<?= $ing->id ?>"
                                    <?= ($editImport && $editImport->ingredient_id == $ing->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ing->name) ?> (<?= htmlspecialchars($ing->unit) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="importQuantity">Số lượng nhập <span class="required">*</span></label>
                    <input type="number" name="import_quantity" required step="0.01"
                           placeholder="Nhập số lượng"
                           value="<?= $editImport ? htmlspecialchars($editImport->import_quantity) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="totalCost">Tổng tiền (VNĐ) <span class="required">*</span></label>
                    <input type="number" name="total_cost" required
                           placeholder="Nhập tổng tiền"
                           value="<?= $editImport ? htmlspecialchars($editImport->total_cost) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="importDate">Ngày nhập <span class="required">*</span></label>
                    <input type="date" name="import_date" required
                           value="<?= $editImport ? date('Y-m-d', strtotime($editImport->import_date)) : date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="note">Ghi chú</label>
                    <textarea name="note" rows="2"
                              placeholder="Nhập ghi chú (nếu có)"><?= $editImport ? htmlspecialchars($editImport->note) : '' ?></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <a href="?url=InventoryImportController/GetData" class="btn-secondary">Hủy bỏ</a>
                <!--
                    Khi click button này:
                    1. Form submit với method POST
                    2. Gửi đến action đã định nghĩa ở thẻ <form>
                    3. Router nhận request và gọi Controller->Action
                    4. KHÔNG CÓ JS NÀO CHẶN - Hoạt động như form tìm kiếm
                -->
                <button type="submit" class="btn-primary">💾 Lưu lại</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- JS chỉ để validation TRƯỚC KHI submit, KHÔNG ngăn submit -->
<script src="Public/Js/inventory-imports-page.js"></script>
