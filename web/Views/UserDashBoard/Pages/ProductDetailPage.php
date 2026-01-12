<?php
$product = $data['product'] ?? null;
$category = $data['category'] ?? null;
?>

<?php if (isset($product)): ?>
<section class="product-detail-section">
    <div class="product-container">
        <!-- Product Images -->
        <div class="product-images">
            <div class="main-image">
                <img src="<?= !empty($product->image_url) ? htmlspecialchars($product->image_url) : 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800' ?>"
                     alt="<?= htmlspecialchars($product->name) ?>">
            </div>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <span class="product-category">
                <?= isset($category) ? strtoupper(htmlspecialchars($category->name)) : 'SẢN PHẨM' ?>
            </span>
            <h1><?= htmlspecialchars($product->name) ?></h1>

            <div class="product-rating">
                <div class="stars">★★★★★</div>
                <span class="rating-count">(4.9 - 128 đánh giá)</span>
            </div>

            <div class="product-description">
                <p><?= nl2br(htmlspecialchars($product->description)) ?></p>
            </div>

            <form method="POST" id="product-form">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                <input type="hidden" name="txtProductSizeId" id="selected-product-size-id"
                       value="<?= !empty($product->sizes) ? $product->sizes[0]->id : '' ?>">
                <input type="hidden" name="txtQuantity" id="selected-quantity" value="1">
                <!-- Thêm các field cho Buy Now - không cần query lại database -->
                <input type="hidden" name="txtProductName" id="selected-product-name" value="<?= htmlspecialchars($product->name) ?>">
                <input type="hidden" name="txtPrice" id="selected-price" value="<?= !empty($product->sizes) ? $product->sizes[0]->price : 0 ?>">

                <!-- Giá sản phẩm -->
                <div class="product-price" id="product-price">
                    <?= !empty($product->sizes) ? number_format($product->sizes[0]->price, 0, ',', '.') . 'đ' : 'Liên hệ' ?>
                </div>

                <!-- Chọn size -->
                <?php if (!empty($product->sizes)): ?>
                <div class="product-options">
                    <div class="option-group">
                        <label>Kích thước <span style="color: red;">*</span></label>
                        <div class="size-options">
                            <?php foreach ($product->sizes as $index => $size): ?>
                                <button type="button"
                                        class="size-btn <?= $index === 0 ? 'active' : '' ?>"
                                        data-product-size-id="<?= $size->id ?>"
                                        data-price="<?= $size->price ?>">
                                    Size <?= htmlspecialchars($size->size_name) ?> -
                                    <?= number_format($size->price, 0, ',', '.') ?>đ
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Số lượng -->
                <div class="quantity-selector">
                    <label>Số lượng</label>
                    <div class="quantity-controls">
                        <button type="button" class="quantity-btn" id="decrease-qty">-</button>
                        <span class="quantity-value" id="quantity">1</span>
                        <button type="button" class="quantity-btn" id="increase-qty">+</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="product-actions">
                    <button type="submit" name="btnThemGioHang" formaction="?url=CartController/ins" class="btn-add-cart">
                        🛒 Thêm vào giỏ hàng
                    </button>
                    <button type="submit" name="btnMuaNgay" formaction="?url=CheckoutController/GetData" class="btn-buy-now">
                        ⚡ Đặt hàng ngay
                    </button>
                    <input type="hidden" name="buy_now" value="1">
                </div>
            </form>

            <!-- Product Meta -->
            <div class="product-meta">
                <div class="meta-item">
                    <div class="meta-icon">📦</div>
                    <div class="meta-text">
                        <strong>Giao hàng</strong>
                        <span>15-30 phút</span>
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-icon">✓</div>
                    <div class="meta-text">
                        <strong>Chất lượng</strong>
                        <span>100% nguyên chất</span>
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-icon">🔥</div>
                    <div class="meta-text">
                        <strong>Nhiệt độ</strong>
                        <span>Nóng / Đá</span>
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-icon">💚</div>
                    <div class="meta-text">
                        <strong>Đánh giá</strong>
                        <span>4.9/5 ★</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<section style="padding: 100px 20px; text-align: center;">
    <h2>Sản phẩm không tồn tại</h2>
    <p><a href="?url=UserController/menu">← Quay lại thực đơn</a></p>
</section>
<?php endif; ?>

<script>
// Chọn size
document.querySelectorAll('.size-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('selected-product-size-id').value = this.dataset.productSizeId;
        document.getElementById('selected-price').value = this.dataset.price; // Cập nhật giá
        document.getElementById('product-price').textContent = new Intl.NumberFormat('vi-VN').format(this.dataset.price) + 'đ';
    });
});

// Xử lý số lượng
let qty = 1;
document.getElementById('decrease-qty').addEventListener('click', function() {
    if (qty > 1) {
        qty--;
        document.getElementById('quantity').textContent = qty;
        document.getElementById('selected-quantity').value = qty;
    }
});
document.getElementById('increase-qty').addEventListener('click', function() {
    qty++;
    document.getElementById('quantity').textContent = qty;
    document.getElementById('selected-quantity').value = qty;
});
</script>
