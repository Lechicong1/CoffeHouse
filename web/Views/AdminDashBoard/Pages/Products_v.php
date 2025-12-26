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

<script>
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');
    const modalTitle = document.getElementById('modalTitle');
    const btnSave = document.getElementById('btnSave');

    function openProductModal(mode, product = null) {
        // Sử dụng class 'active' để kích hoạt modal (display: flex) thay vì style.display = 'block'
        modal.classList.add('active');
        
        // Reset delete flag
        document.getElementById('deleteImageFlag').value = '0';
        document.getElementById('fileName').textContent = 'Chưa chọn tệp';

        if (mode === 'add') {
            modalTitle.textContent = 'Thêm sản phẩm mới';
            form.action = '?url=ProductController/store';
            form.reset();
            document.getElementById('productId').value = '';
            document.getElementById('currentImage').style.display = 'none';
            document.getElementById('productActive').checked = true;
            
            // Reset prices
            document.getElementById('priceM').value = '';
            document.getElementById('priceL').value = '';
            document.getElementById('priceXL').value = '';
        } else {
            modalTitle.textContent = 'Cập nhật sản phẩm';
            form.action = '?url=ProductController/update';
            
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productCategory').value = product.category_id;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productActive').checked = product.is_active == 1;

            // Reset prices first
            document.getElementById('priceM').value = '';
            document.getElementById('priceL').value = '';
            document.getElementById('priceXL').value = '';

            // Populate Prices based on Sizes
            if (product.sizes && product.sizes.length > 0) {
                product.sizes.forEach(size => {
                    if (size.size_name === 'M') document.getElementById('priceM').value = size.price;
                    if (size.size_name === 'L') document.getElementById('priceL').value = size.price;
                    if (size.size_name === 'XL') document.getElementById('priceXL').value = size.price;
                });
            }

            if (product.image_url) {
                const imgContainer = document.getElementById('currentImage');
                imgContainer.style.display = 'inline-block'; // Changed to inline-block for relative positioning
                imgContainer.querySelector('img').src = product.image_url;
            } else {
                document.getElementById('currentImage').style.display = 'none';
            }
        }
    }

    function closeProductModal() {
        modal.classList.remove('active');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            closeProductModal();
        }
        if (event.target == document.getElementById('productDetailModal')) {
            closeProductDetailModal();
        }
    }

    function removeImage() {
        document.getElementById('currentImage').style.display = 'none';
        document.getElementById('productImage').value = ''; // Clear file input
        document.getElementById('fileName').textContent = 'Chưa chọn tệp';
        document.getElementById('deleteImageFlag').value = '1'; // Set flag to delete image
    }

    // --- Logic cho Modal Chi Tiết Sản Phẩm ---
    let currentProductSizes = [];

    function openProductDetailModal(product) {
        const detailModal = document.getElementById('productDetailModal');
        detailModal.classList.add('active'); // Use class active for centering
        
        // Populate Data
        document.getElementById('detailName').textContent = product.name;
        document.getElementById('detailDescription').textContent = product.description || 'Chưa có mô tả';
        
        const img = document.getElementById('detailImage');
        if (product.image_url) {
            img.src = product.image_url;
            img.style.display = 'inline-block';
        } else {
            img.style.display = 'none';
        }

        // Populate Sizes
        const sizeSelect = document.getElementById('detailSizeSelect');
        sizeSelect.innerHTML = ''; // Clear old options
        currentProductSizes = product.sizes || [];

        if (currentProductSizes.length > 0) {
            currentProductSizes.forEach((size, index) => {
                const option = document.createElement('option');
                option.value = index; // Use index to easily get price later
                option.textContent = size.size_name;
                sizeSelect.appendChild(option);
            });
            // Trigger update for first item
            updateDetailPrice();
        } else {
            const option = document.createElement('option');
            option.textContent = 'Chưa có size';
            sizeSelect.appendChild(option);
            document.getElementById('detailPriceDisplay').textContent = '---';
        }
    }

    function closeProductDetailModal() {
        document.getElementById('productDetailModal').classList.remove('active');
    }

    function updateDetailPrice() {
        const sizeSelect = document.getElementById('detailSizeSelect');
        const selectedIndex = sizeSelect.value;
        
        if (currentProductSizes[selectedIndex]) {
            const price = currentProductSizes[selectedIndex].price;
            // Format currency VND
            const formattedPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
            document.getElementById('detailPriceDisplay').textContent = formattedPrice;
        }
    }

    function previewImage(input) {
        const fileNameSpan = document.getElementById('fileName');
        const currentImageDiv = document.getElementById('currentImage');
        const img = currentImageDiv.querySelector('img');

        if (input.files && input.files[0]) {
            fileNameSpan.textContent = input.files[0].name;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                currentImageDiv.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            fileNameSpan.textContent = 'Chưa chọn tệp';
        }
    }
</script>
