<?php
$products = $data['products'] ?? [];
$categories = $data['categories'] ?? [];
$keyword = $data['keyword'] ?? '';
?>

<!-- Import CSS riêng cho trang Products -->
<link rel="stylesheet" href="Public/Css/products-page.css">

<section id="products" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>🛒 Quản lý Sản phẩm</h2>
            <p class="subtitle">Tổng số: <strong><?= count($products) ?></strong> sản phẩm</p>
        </div>
        <div class="header-actions">
            <!-- Button Thêm mới -->
            <button class="btn-primary" onclick="openProductModal('add')">
                ➕ Thêm sản phẩm mới
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 24px;">
        <form method="POST" action="?url=ProductController/timkiem" class="search-form">
            <input type="text" name="txtSearch" class="search-input" placeholder="🔍 Tìm kiếm sản phẩm theo tên..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn-primary">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #999;">
                            📭 Không có sản phẩm nào!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $i = 1;
                    foreach ($products as $product):
                        $categoryName = 'N/A';
                        foreach ($categories as $cat) {
                            if ($cat->id == $product->category_id) {
                                $categoryName = $cat->name;
                                break;
                            }
                        }
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <div class="product-name">
                                    <strong><?= htmlspecialchars($product->name) ?></strong>
                                </div>
                            </td>
                            <td>
                                <span style="color: #555; font-weight: 500;"><?= htmlspecialchars($categoryName) ?></span>
                            </td>
                            <td>
                                <span style="color: #777; font-size: 0.95em;">
                                    <?= htmlspecialchars(mb_strimwidth($product->description, 0, 50, "...")) ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?= $product->is_active ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $product->is_active ? 'Hoạt động' : 'Ngừng bán' ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-info" onclick='openProductDetailModal(<?= json_encode($product) ?>)' title="Xem chi tiết">
                                    👁️ Xem
                                </button>
                                <button class="btn-edit" onclick='openProductModal("edit", <?= json_encode($product) ?>)' title="Sửa">
                                    ✏️ Sửa
                                </button>
                                <form method="POST" action="?url=ProductController/delete" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                    <input type="hidden" name="id" value="<?= $product->id ?>">
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

<!-- Modal Xem Chi Tiết Sản Phẩm -->
<div id="productDetailModal" class="modal">
    <div class="modal-content" style="width: 400px;">
        <div class="modal-header">
            <div class="modal-title-wrapper">
                <div class="modal-icon">👁️</div>
                <h3>Chi tiết sản phẩm</h3>
            </div>
            <span class="close" onclick="closeProductDetailModal()">&times;</span>
        </div>
        <div class="modal-body" style="text-align: center;">
            <img id="detailImage" src="" alt="Product Image" style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px; margin-bottom: 16px; border: 1px solid #eee;">
            <h3 id="detailName" style="margin-bottom: 8px; color: #333;"></h3>
            <p id="detailDescription" style="color: #666; font-size: 0.9em; margin-bottom: 20px;"></p>
            
            <div style="background: #f9fafb; padding: 16px; border-radius: 8px; text-align: left;">
                <div class="form-group">
                    <label for="detailSizeSelect" style="font-size: 0.9em;">Chọn Size:</label>
                    <select id="detailSizeSelect" onchange="updateDetailPrice()" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ddd;">
                        <!-- Options will be populated by JS -->
                    </select>
                </div>
                <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 500; color: #555;">Giá bán:</span>
                    <span id="detailPriceDisplay" style="font-size: 1.2em; font-weight: 700; color: #2563eb;">0 đ</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeProductDetailModal()">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal Form Thêm/Sửa Sản phẩm -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title-wrapper">
                <div class="modal-icon">🛒</div>
                <h3 id="modalTitle">Thêm sản phẩm mới</h3>
            </div>
            <span class="close" onclick="closeProductModal()">&times;</span>
        </div>
        <form id="productForm" method="POST" enctype="multipart/form-data" action="?url=ProductController/store">
            <div class="modal-body">
                <input type="hidden" id="productId" name="id">
                <input type="hidden" id="deleteImageFlag" name="delete_image" value="0">
                
                <div class="form-group">
                    <label for="productName">Tên sản phẩm <span class="required">*</span></label>
                    <input type="text" id="productName" name="name" required placeholder="Nhập tên sản phẩm">
                </div>

                <div class="form-group">
                    <label for="productCategory">Danh mục <span class="required">*</span></label>
                    <select id="productCategory" name="category_id" required class="custom-select">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #2d3748;">Giá bán theo Size (VNĐ)</label>
                    <div style="display: flex; gap: 16px;">
                        <div style="flex: 1;">
                            <label for="priceM" style="font-size: 0.9em; color: #4a5568;">Size M</label>
                            <input type="number" id="priceM" name="price_M" placeholder="Nhập giá" class="form-control">
                        </div>
                        <div style="flex: 1;">
                            <label for="priceL" style="font-size: 0.9em; color: #4a5568;">Size L</label>
                            <input type="number" id="priceL" name="price_L" placeholder="Nhập giá" class="form-control">
                        </div>
                        <div style="flex: 1;">
                            <label for="priceXL" style="font-size: 0.9em; color: #4a5568;">Size XL</label>
                            <input type="number" id="priceXL" name="price_XL" placeholder="Nhập giá" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="productDescription">Mô tả</label>
                    <textarea id="productDescription" name="description" rows="2" placeholder="Nhập mô tả sản phẩm"></textarea>
                </div>

                <div class="form-group">
                    <label for="productImage">Hình ảnh</label>
                    <div class="custom-file-upload">
                        <label for="productImage" class="btn-upload">
                            📁 Chọn ảnh...
                        </label>
                        <input type="file" id="productImage" name="image" accept="image/*" onchange="previewImage(this)">
                        <span id="fileName" style="margin-left: 10px; color: #666; font-size: 0.9em;">Chưa chọn tệp</span>
                    </div>
                    <div id="currentImage" style="margin-top: 10px; display: none;">
                        <img src="" alt="Current Image" style="max-width: 100px; max-height: 100px; border-radius: 8px;">
                        <button type="button" class="btn-remove-image" onclick="removeImage()" title="Gỡ ảnh">✕</button>
                    </div>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="productActive" name="is_active" checked>
                    <label for="productActive">Đang hoạt động</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeProductModal()">Hủy bỏ</button>
                <button type="submit" class="btn-primary" id="btnSave">Lưu lại</button>
            </div>
        </form>
    </div>
</div>

<script src="Public/Js/products-page.js"></script>
