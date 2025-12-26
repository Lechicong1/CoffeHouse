<?php
/**
 * filepath: /web/Views/AdminDashBoard/Pages/Categories_v.php
 * VIEW CON - Quản lý Danh mục
 * Chỉ hiển thị dữ liệu, không xử lý logic
 */

// Lấy dữ liệu từ Controller (đã truyền qua $data)
$categories = $data['categories'] ?? [];
$stats = $data['stats'] ?? ['total' => 0];
$keyword = $data['keyword'] ?? '';
$successMessage = $data['successMessage'] ?? ($_GET['success'] ?? null);
$errorMessage = $data['errorMessage'] ?? null;
?>

<!-- Import CSS riêng cho trang Categories -->
<link rel="stylesheet" href="Public/Css/categories-page.css">

<section id="categories" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>🏷️ Quản lý Danh mục</h2>
            <p class="subtitle">Tổng số: <strong><?= $stats['total'] ?></strong> danh mục</p>
        </div>
        <div class="header-actions">
            <!-- Button Thêm mới -->
            <button class="btn-primary" onclick="openCategoryModal('add')">
                ➕ Thêm danh mục mới
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 24px;">
        <form method="GET" action="" class="search-form">
            <input type="hidden" name="url" value="Category">
            <input type="text" name="search" class="search-input" placeholder="🔍 Tìm kiếm theo tên danh mục hoặc mô tả..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn-primary">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" style="padding: 40px; text-align: center; color: #999;">
                            📭 Không có danh mục nào!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $i = 1;
                    foreach ($categories as $category):
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <div class="category-name">
                                    <span class="category-icon">🏷️</span>
                                    <strong><?= htmlspecialchars($category->name) ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="category-description">
                                    <?= htmlspecialchars($category->description ?: '-') ?>
                                </div>
                            </td>
                            <td>
                                <button class="btn-edit" onclick='openCategoryModal("edit", <?= htmlspecialchars(json_encode($category->toArray())) ?>)' title="Sửa">
                                    ✏️ Sửa
                                </button>
                                <form method="POST" action="?url=Category/del" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?')">
                                    <input type="hidden" name="txtId" value="<?= $category->id ?>">
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

<!-- Modal Form Thêm/Sửa Danh mục -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title-wrapper">
                <div class="modal-icon">🏷️</div>
                <h3 id="modalTitle">Thêm danh mục mới</h3>
            </div>
            <button class="btn-close-modal" onclick="closeCategoryModal()">×</button>
        </div>

        <form id="categoryForm" method="POST" action="">
            <input type="hidden" id="categoryId" name="txtId">

            <!-- Tên danh mục -->
            <div class="form-group">
                <label><span class="label-icon">🏷️</span> Tên danh mục *</label>
                <input type="text" id="categoryName" name="txtName" required placeholder="Nhập tên danh mục (VD: Cà phê, Trà sữa...)">
            </div>

            <!-- Mô tả -->
            <div class="form-group">
                <label><span class="label-icon">📝</span> Mô tả</label>
                <textarea id="categoryDescription" name="txtDescription" rows="4" placeholder="Nhập mô tả chi tiết về danh mục (không bắt buộc)"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCategoryModal()">
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
<script src="Public/Js/categories-page.js"></script>
<script>
    // Truyền messages từ PHP sang JavaScript
    window.CATEGORY_MESSAGES = {
        success: <?= $successMessage ? "'" . addslashes($successMessage) . "'" : 'null' ?>,
        error: <?= $errorMessage ? "'" . addslashes($errorMessage) . "'" : 'null' ?>
    };
</script>
