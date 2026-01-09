<?php
/**
 * VIEW CON - Quản lý Công Thức Sản Phẩm
 * Dùng form submit truyền thống, không dùng JSON
 */

// Lấy dữ liệu từ Controller (đã truyền qua $data)
$products = $data['products'] ?? [];
$ingredients = $data['ingredients'] ?? [];
$currentRecipe = $data['currentRecipe'] ?? [];
$selectedProductId = $data['selectedProductId'] ?? null;
$selectedProduct = $data['selectedProduct'] ?? null;
?>

<!-- Import CSS riêng cho trang Recipe -->
<link rel="stylesheet" href="Public/Css/recipe-page.css">

<section id="recipe" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>📋 Quản lý Công Thức Sản Phẩm</h2>
            <p class="subtitle">Thiết lập nguyên liệu và định lượng cho từng sản phẩm</p>
        </div>
        <div class="header-actions">
            <div class="select-group">
                <label for="productSelect" class="select-label">Sản phẩm:</label>
                <select id="productSelect" class="filter-select" onchange="handleProductChange(this.value)">
                    <option value="">-- Chọn Sản Phẩm --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product->id ?>" <?= ($selectedProductId == $product->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($product->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="size-info">
                <span class="size-badge">S: 0.8x</span>
                <span class="size-badge active">M: 1.0x</span>
                <span class="size-badge">L: 1.2x</span>
            </div>
        </div>
    </div>

    <!-- Two Tables Container -->
    <div class="tables-container">
        <!-- Left Table: Ingredient Selection -->
        <div class="table-panel">
            <div class="panel-header">
                <h3>📦 Chọn Nguyên Liệu & Nhập Định Lượng Gốc (Size M)</h3>
            </div>
            
            <!-- Form LƯU CÔNG THỨC - Submit truyền thống -->
            <form id="saveForm" method="POST" action="/COFFEE_PHP/RecipeController/Save">
                <input type="hidden" name="txtProductId" value="<?= $selectedProductId ?>">
                
                <div class="table-wrapper">
                    <table class="data-table" id="ingredientTable">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">☐</th>
                                <th style="width: 60px;">ID</th>
                                <th>Tên Nguyên Liệu</th>
                                <th style="width: 80px;">Đơn Vị</th>
                                <th style="width: 140px;">Định Lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ingredients)): ?>
                                <tr>
                                    <td colspan="5" style="padding: 40px; text-align: center; color: #999;">
                                        📭 Không có nguyên liệu nào!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ingredients as $ingredient): ?>
                                    <?php 
                                        $inRecipe = false;
                                        $existingQuantity = '';
                                        foreach ($currentRecipe as $recipeItem) {
                                            if (isset($recipeItem['ingredient_id']) && $recipeItem['ingredient_id'] == $ingredient->id) {
                                                $inRecipe = true;
                                                $existingQuantity = $recipeItem['quantity'] ?? '';
                                                break;
                                            }
                                        }
                                    ?>
                                    <tr class="ingredient-row <?= $inRecipe ? 'in-recipe' : '' ?>" data-id="<?= $ingredient->id ?>">
                                        <td style="text-align: center;">
                                            <input type="checkbox" 
                                                   class="ingredient-checkbox" 
                                                   name="chkIngredient[]" 
                                                   value="<?= $ingredient->id ?>"
                                                   <?= $inRecipe ? 'checked' : '' ?>>
                                        </td>
                                        <td><?= $ingredient->id ?></td>
                                        <td><strong><?= htmlspecialchars($ingredient->name) ?></strong></td>
                                        <td><?= htmlspecialchars($ingredient->unit) ?></td>
                                        <td>
                                            <input type="number" 
                                                   class="quantity-input" 
                                                   name="txtQuantity[<?= $ingredient->id ?>]" 
                                                   placeholder="0"
                                                   min="0"
                                                   step="0.01"
                                                   value="<?= htmlspecialchars($existingQuantity) ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <p class="hint-text">💡 Tick chọn nguyên liệu → Nhập định lượng → Click "Lưu Công Thức"</p>
                </div>
            </form>
        </div>

        <!-- Right Table: Current Recipe -->
        <div class="table-panel">
            <div class="panel-header">
                <h3>📋 Công Thức Hiện Tại</h3>
            </div>
            
            <!-- Form CẬP NHẬT ĐỊNH LƯỢNG -->
            <form id="updateQuantityForm" method="POST" action="/COFFEE_PHP/RecipeController/UpdateQuantity">
                <input type="hidden" name="txtProductId" value="<?= $selectedProductId ?>">
                
                <div class="table-wrapper">
                    <table class="data-table" id="currentRecipeTable">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">☐</th>
                                <th style="width: 60px;">ID</th>
                                <th>Nguyên Liệu</th>
                                <th style="width: 140px;">Định Lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($currentRecipe)): ?>
                                <tr id="emptyRecipeRow">
                                    <td colspan="4" style="padding: 40px; text-align: center; color: #999;">
                                        <?php if ($selectedProductId): ?>
                                            📭 Chưa có công thức cho sản phẩm này
                                        <?php else: ?>
                                            👆 Vui lòng chọn sản phẩm để xem công thức
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($currentRecipe as $recipeItem): ?>
                                    <tr class="recipe-row" data-id="<?= $recipeItem['ingredient_id'] ?? '' ?>">
                                        <td style="text-align: center;">
                                            <input type="checkbox" 
                                                   class="delete-checkbox" 
                                                   name="chkDelete[]" 
                                                   value="<?= $recipeItem['ingredient_id'] ?? '' ?>">
                                        </td>
                                        <td><?= $recipeItem['ingredient_id'] ?? '' ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($recipeItem['ingredient_name'] ?? '') ?></strong>
                                            <span class="unit-text">(<?= htmlspecialchars($recipeItem['unit'] ?? '') ?>)</span>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="quantity-input update-qty" 
                                                   name="txtUpdateQty[<?= $recipeItem['ingredient_id'] ?>]" 
                                                   value="<?= htmlspecialchars($recipeItem['quantity'] ?? '') ?>"
                                                   min="0"
                                                   step="0.01">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer hint-text">
                    ✏️ Sửa định lượng rồi click "Cập Nhật" | ☐ Tick để xóa
                </div>
            </form>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-bar">
        <div class="action-group-left">
            <button type="button" class="btn-primary" onclick="submitSave()">
                ➕ Thêm Nguyên Liệu
            </button>
            <button type="button" class="btn-secondary" onclick="refreshPage()">
                🔄 Làm Mới
            </button>
        </div>
        <div class="action-group-right">
            <button type="button" class="btn-edit" onclick="submitUpdateQuantity()">
                📝 Cập Nhật Định Lượng
            </button>
            <button type="button" class="btn-delete" onclick="submitDelete()">
                🗑️ Xóa Đã Chọn
            </button>
        </div>
    </div>
</section>

<!-- JavaScript -->
<script src="/COFFEE_PHP/Public/Js/recipe-page.js"></script>
