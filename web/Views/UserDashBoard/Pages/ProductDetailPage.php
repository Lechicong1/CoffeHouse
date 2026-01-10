
<?php
$product = $data['product'] ?? null;
$category = $data['category'] ?? null;
?>

<?php if (isset($product)): ?>
<section class="product-detail-section">
    <!-- Breadcrumb -->


    <div class="product-container">
        <!-- Product Images -->
        <div class="product-images">
            <div class="main-image">
                <?php if (!empty($product->image_url)): ?>
                    <img src="<?= htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                <?php else: ?>
                    <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800" alt="<?= htmlspecialchars($product->name) ?>">
                <?php endif; ?>
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

            <!-- Form cho cả 2 action: Thêm giỏ hàng và Mua ngay -->
            <form method="POST" id="product-form">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                <input type="hidden" name="txtProductSizeId" id="selected-product-size-id"
                       value="<?= !empty($product->sizes) ? $product->sizes[0]->id : '' ?>">
                <input type="hidden" name="txtQuantity" id="selected-quantity" value="1">

                <!-- Hiển thị giá sản phẩm -->
                <div class="product-price" id="product-price">
                    <?php
                    if (!empty($product->sizes)) {
                        // Hiển thị giá của size đầu tiên
                        echo number_format($product->sizes[0]->price, 0, ',', '.') . 'đ';
                    } else {
                        echo 'Liên hệ';
                    }
                    ?>
                </div>

                <!-- Chọn size với button style -->
                <?php if (!empty($product->sizes)): ?>
                <div class="product-options">
                    <div class="option-group">
                        <label>Kích thước <span style="color: red;">*</span></label>
                        <div class="size-options">
                            <?php foreach ($product->sizes as $index => $size): ?>
                                <button type="button"
                                        class="size-btn <?= $index === 0 ? 'active' : '' ?>"
                                        data-product-size-id="<?= $size->id ?>"
                                        data-size="<?= htmlspecialchars($size->size_name) ?>"
                                        data-price="<?= $size->price ?>">
                                    Size <?= htmlspecialchars($size->size_name) ?> -
                                    <?= number_format($size->price, 0, ',', '.') ?>đ
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Số lượng với quantity controls -->
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
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn size
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active từ tất cả
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            // Add active cho button được click
            this.classList.add('active');
            // Cập nhật hidden input
            document.getElementById('selected-product-size-id').value = this.dataset.productSizeId;
        });
    });

    // Xử lý quantity
    let qty = 1;
    const qtyDisplay = document.getElementById('quantity');
    const qtyInput = document.getElementById('selected-quantity');

    document.getElementById('decrease-qty').addEventListener('click', function() {
        if (qty > 1) {
            qty--;
            qtyDisplay.textContent = qty;
            qtyInput.value = qty;
        }
    });

    document.getElementById('increase-qty').addEventListener('click', function() {
        qty++;
        qtyDisplay.textContent = qty;
        qtyInput.value = qty;
    });

    // Xử lý tab
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(`tab-${tab}`).classList.add('active');
        });
    });
});
</script>
