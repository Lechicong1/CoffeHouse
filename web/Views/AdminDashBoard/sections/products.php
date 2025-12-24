<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/sections/products.php
 * Products Management Section - Quản lý sản phẩm
 */

// Mock data - Sau này từ database
$products = [
    [
        'id' => 'SP001',
        'image' => '☕',
        'name' => 'Cà phê sữa đá',
        'category' => 'Cà phê',
        'price' => '25,000₫',
        'stock' => 'Còn hàng',
        'status' => 'active'
    ],
    [
        'id' => 'SP002',
        'image' => '🍵',
        'name' => 'Trà đào cam sả',
        'category' => 'Trà',
        'price' => '35,000₫',
        'stock' => 'Còn hàng',
        'status' => 'active'
    ],
    [
        'id' => 'SP003',
        'image' => '🥤',
        'name' => 'Bạc xỉu',
        'category' => 'Cà phê',
        'price' => '30,000₫',
        'stock' => 'Hết hàng',
        'status' => 'out_of_stock'
    ],
];
?>
<section id="products" class="content-section">
    <div class="section-header">
        <h2>Quản lý Sản phẩm</h2>
        <div class="header-actions">
            <input type="text" class="search-input" placeholder="🔍 Tìm sản phẩm...">
            <select class="filter-select">
                <option value="all">Tất cả danh mục</option>
                <option value="coffee">Cà phê</option>
                <option value="tea">Trà</option>
                <option value="juice">Nước ép</option>
                <option value="food">Đồ ăn</option>
            </select>
            <button class="btn-primary" onclick="openModal('product', 'create')">
                + Thêm món mới
            </button>
        </div>
    </div>

    <!-- Product Statistics -->
    <div class="mini-stats">
        <div class="mini-stat">
            <span class="mini-stat-label">Tổng sản phẩm</span>
            <span class="mini-stat-value">48</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Đang bán</span>
            <span class="mini-stat-value success">42</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Hết hàng</span>
            <span class="mini-stat-value warning">6</span>
        </div>
    </div>

    <!-- Products Table -->
    <div class="table-responsive">
        <table class="data-table" id="productsTable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all" aria-label="Chọn tất cả">
                    </th>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên món</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr data-product-id="<?= $product['id'] ?>">
                        <td>
                            <input type="checkbox" class="select-item">
                        </td>
                        <td><strong><?= htmlspecialchars($product['id']) ?></strong></td>
                        <td>
                            <div class="product-image">
                                <?= $product['image'] ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><strong><?= $product['price'] ?></strong></td>
                        <td>
                            <span class="stock-badge <?= $product['status'] ?>">
                                <?= htmlspecialchars($product['stock']) ?>
                            </span>
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" 
                                       <?= $product['status'] === 'active' ? 'checked' : '' ?>
                                       onchange="toggleProductStatus('<?= $product['id'] ?>')">
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon edit" 
                                        onclick="editProduct('<?= $product['id'] ?>')" 
                                        title="Chỉnh sửa">
                                    ✏️
                                </button>
                                <button class="btn-icon delete" 
                                        onclick="deleteProduct('<?= $product['id'] ?>')" 
                                        title="Xóa">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <button class="btn-secondary" disabled>← Trước</button>
        <span class="page-info">Trang 1 / 3</span>
        <button class="btn-secondary">Sau →</button>
    </div>
</section>
