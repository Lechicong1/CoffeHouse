<!-- ===================================
     FILE: CheckoutPage.php
     MÔ TẢ: Trang thanh toán
     Nội dung chính - Được include vào MasterLayout
     =================================== -->
<main class="checkout-container">
    <div class="checkout-wrapper">
        <h1 class="checkout-title">THANH TOÁN</h1>
        <div class="checkout-content">
            <!-- Thông tin giao hàng -->
            <div class="checkout-section">
                <div class="section-header">
                    <i class="fas fa-shipping-fast"></i>
                    <h2>Thông Tin Giao Hàng</h2>
                </div>
                <form id="checkoutForm" method="POST" action="/COFFEE_PHP/Checkout/placeOrder" class="checkout-form">
                    <input type="hidden" name="txtTotalAmount" value="<?php echo $data['total']; ?>">
                    <div class="form-group">
                        <label for="customerVoucher">Mã Voucher (nếu có)</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" id="customerVoucher" name="txtVoucherCode" placeholder="Nhập mã voucher hoặc chọn" style="flex:1;padding:8px;">
                            <button type="button" id="openVoucherListBtn" class="btn">Chọn voucher</button>
                        </div>
                        <input type="hidden" id="appliedVoucherId" name="applied_voucher_id" value="">
                        <div id="checkoutVoucherMsg" style="margin-top:8px;color:#0a6; font-size:0.95rem"></div>
                    </div>
                    <div class="form-group">
                        <label for="customerName">Họ và tên <span class="required">*</span></label>
                        <input type="text" id="customerName" name="txtCustomerName" value="<?php echo htmlspecialchars($data['customer']->full_name ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="customerPhone">Số điện thoại <span class="required">*</span></label>
                        <input type="tel" id="customerPhone" name="txtCustomerPhone" value="<?php echo htmlspecialchars($data['customer']->phone ?? ''); ?>" pattern="[0-9]{10}" required>
                    </div>
                    <div class="form-group">
                        <label for="shippingAddress">Địa chỉ giao hàng <span class="required">*</span></label>
                        <textarea id="shippingAddress" name="txtShippingAddress" rows="3" required><?php echo htmlspecialchars($data['customerAddress'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="note">Ghi chú</label>
                        <textarea id="note" name="txtNote" rows="3" placeholder="Ghi chú thêm cho đơn hàng..."></textarea>
                    </div>
                    <div class="section-header">
                        <i class="fas fa-credit-card"></i>
                        <h2>Phương Thức Thanh Toán</h2>
                    </div>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="CASH" checked>
                            <div class="payment-card">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Tiền Mặt</span>
                                <p>Thanh toán khi nhận hàng</p>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="BANK_TRANSFER">
                            <div class="payment-card">
                                <i class="fas fa-university"></i>
                                <span>Chuyển Khoản</span>
                                <p>Quét mã QR để thanh toán</p>
                            </div>
                        </label>
                    </div>
                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" name="btnDatHang" class="btn-place-order">
                            <i class="fas fa-check-circle"></i>
                            Đặt Hàng
                        </button>
                        <a href="/COFFEE_PHP/Cart/index" class="btn-back-cart">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại giỏ hàng
                        </a>
                    </div>
                    <div id="qrSection" class="qr-section" style="display: none;">
                        <div class="qr-container">
                            <h3>Quét Mã QR Để Thanh Toán</h3>
                            <div class="qr-code">
                                <img id="qrImage" src="" alt="QR Code VietQR">
                            </div>
                            <div class="bank-info">
                                <h4>Thông Tin Chuyển Khoản</h4>
                                <div class="bank-detail">
                                    <i class="fas fa-university"></i>
                                    <div><strong>Ngân hàng:</strong> <span>MBBank</span></div>
                                </div>
                                <div class="bank-detail">
                                    <i class="fas fa-credit-card"></i>
                                    <div><strong>Số tài khoản:</strong> <span>88221020056868</span></div>
                                </div>
                                <div class="bank-detail highlight">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <div><strong>Số tiền:</strong> <span><?php echo number_format($data['total'], 0, ',', '.'); ?>đ</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="order-summary">
                <h3>Tóm Tắt Đơn Hàng</h3>
                <div class="summary-items">
                    <?php if (isset($data['cartItems'])): ?>
                        <?php foreach ($data['cartItems'] as $item): ?>
                            <div class="summary-item">
                                <span><?php echo htmlspecialchars($item->product_name); ?> x<?php echo $item->quantity; ?></span>
                                <span><?php echo number_format($item->price * $item->quantity, 0, ',', '.'); ?>đ</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="summary-total">
                    <span>Tổng cộng:</span>
                    <span><?php echo number_format($data['total'], 0, ',', '.'); ?>đ</span>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
const TOTAL_AMOUNT = <?php echo $data['total']; ?>;
const ORDER_CODE = 'ORD' + Date.now();
document.getElementById('checkoutForm')?.addEventListener('submit', function() {
    document.querySelector('.btn-place-order').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    document.querySelector('.btn-place-order').disabled = true;
});
document.querySelectorAll('input[name="payment_method"]').forEach(option => {
    option.addEventListener('change', function() {
        const qrSection = document.getElementById('qrSection');
        if (this.value === 'BANK_TRANSFER') {
            qrSection.style.display = 'block';
            const vietQRUrl = `https://img.vietqr.io/image/MB-88221020056868-compact2.png?amount=${TOTAL_AMOUNT}&addInfo=${encodeURIComponent('Thanh toan ' + ORDER_CODE)}&accountName=COFFEE%20HOUSE`;
            document.getElementById('qrImage').src = vietQRUrl;
        } else {
            qrSection.style.display = 'none';
        }
    });
});
</script>
<script src="/COFFEE_PHP/Public/Js/voucher-utils.js"></script>
<script src="/COFFEE_PHP/Public/Js/voucher-web.js"></script>
