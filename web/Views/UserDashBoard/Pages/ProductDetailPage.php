<!-- ===================================
     FILE: ProductDetailPage.php
     Trang chi tiết sản phẩm - PHP Server thuần túy
     Form submit trực tiếp đến CartController
     =================================== -->

<?php
$product = $data['product'] ?? null;
$category = $data['category'] ?? null;
$relatedProducts = $data['relatedProducts'] ?? [];
?>

<?php if (isset($product)): ?>
<section class="product-detail-section">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="?url=UserController/index">Trang chủ</a>
        <span>›</span>
        <a href="?url=UserController/menu">Thực đơn</a>
        <span>›</span>
        <a href="?url=UserController/categoryProducts&id=<?= $product->category_id ?>">
            <?= isset($category) ? htmlspecialchars($category->name) : 'Danh mục' ?>
        </a>
        <span>›</span>
        <strong><?= htmlspecialchars($product->name) ?></strong>
    </div>

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

            <div class="product-description">
                <p><?= nl2br(htmlspecialchars($product->description)) ?></p>
            </div>

            <!--
                FORM SUBMIT: POST → CartController/ins (hoặc action tương ứng)
                Router sẽ gọi: CartController->ins() với dữ liệu POST

                Luồng xử lý:
                1. User chọn size → JS cập nhật hidden input
                2. User chọn số lượng → JS cập nhật hidden input
                3. User click "Thêm giỏ hàng" → Form submit POST
                4. Controller nhận POST data và xử lý
                5. Redirect về trang giỏ hàng hoặc trang hiện tại
            -->
            <form method="POST" action="?url=CartController/ins" id="add-to-cart-form">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                <input type="hidden" name="txtProductSizeId" id="selected-product-size-id"
                       value="<?= !empty($product->sizes) ? $product->sizes[0]->id : '' ?>">
                <input type="hidden" name="txtQuantity" id="selected-quantity" value="1">
                <input type="hidden" name="buy_now" id="buy-now-flag" value="0">

                <div class="product-options">
                    <?php if (!empty($product->sizes)): ?>
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
                    <?php endif; ?>
                </div>

                <!-- Quantity -->
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
                    <!--
                        THÊM GIỎ HÀNG: POST với buy_now=0
                        Controller->ins() sẽ thêm vào giỏ và redirect về trang hiện tại
                    -->
                    <button type="submit" name="btnThemGioHang" class="btn-add-cart">
                        🛒 Thêm vào giỏ hàng
                    </button>

                    <!--
                        MUA NGAY: JS set buy_now=1 rồi submit form
                        Controller->ins() sẽ thêm vào giỏ và redirect đến checkout
                    -->
                    <button type="button" class="btn-buy-now" id="buy-now">
                        ⚡ Đặt hàng ngay
                    </button>
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

    <!-- Product Tabs (Pure CSS/JS - Không cần server) -->
    <div class="product-tabs">
        <div class="tab-buttons">
            <button class="tab-btn active" data-tab="description">Mô tả</button>
            <button class="tab-btn" data-tab="reviews">Đánh giá</button>
        </div>

        <div class="tab-content active" id="tab-description">
            <h3>Mô tả chi tiết</h3>
            <p style="line-height: 1.8; color: #666;">
                <?= nl2br(htmlspecialchars($product->description)) ?>
            </p>
        </div>

        <div class="tab-content" id="tab-reviews">
            <h3>Đánh giá từ khách hàng</h3>
            <p style="color: #666; padding: 20px 0;">
                Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên đánh giá!
            </p>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (isset($relatedProducts) && !empty($relatedProducts)): ?>
    <div class="related-products">
        <h2>SẢN PHẨM LIÊN QUAN</h2>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $relProduct):
                $minPrice = null;
                if (!empty($relProduct->sizes)) {
                    $prices = array_column($relProduct->sizes, 'price');
                    $minPrice = min($prices);
                }
            ?>
            <div class="product-card">
                <a href="?url=UserController/productDetail&id=<?= $relProduct->id ?>">
                    <div class="product-image">
                        <?php if (!empty($relProduct->image_url)): ?>
                            <img src="<?= htmlspecialchars($relProduct->image_url) ?>"
                                 alt="<?= htmlspecialchars($relProduct->name) ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500"
                                 alt="<?= htmlspecialchars($relProduct->name) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <h3><?= strtoupper(htmlspecialchars($relProduct->name)) ?></h3>
                        <p class="product-price">
                            <?php if ($minPrice): ?>
                                Từ <?= number_format($minPrice, 0, ',', '.') ?>đ
                            <?php else: ?>
                                Liên hệ
                            <?php endif; ?>
                        </p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php else: ?>
<section style="padding: 100px 20px; text-align: center;">
    <h2>Sản phẩm không tồn tại</h2>
    <p><a href="?url=UserController/menu">← Quay lại thực đơn</a></p>
</section>
<?php endif; ?>
