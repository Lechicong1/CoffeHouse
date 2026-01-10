<!-- ===================================
     FILE: PaymentPage.php
     MÔ TẢ: Trang thanh toán chuyển khoản
     Nội dung chính - Được include vào MasterLayout
     =================================== -->
<main class="payment-container">
    <div class="payment-wrapper">
        <div class="payment-header">
            <i class="fas fa-qrcode"></i>
            <h1>THANH TOÁN CHUYỂN KHOẢN</h1>
            <p>Quét mã QR bên dưới để hoàn tất thanh toán</p>
        </div>
        <?php if (isset($data['order'])): ?>
            <div class="order-info-box">
                <div class="info-item">
                    <span class="label">Mã đơn hàng:</span>
                    <span class="value order-code"><?php echo htmlspecialchars($data['order']->order_code); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Tổng tiền:</span>
                    <span class="value amount"><?php echo number_format($data['order']->total_amount, 0, ',', '.'); ?>đ</span>
                </div>
            </div>
        <?php endif; ?>
        <div class="qr-section">
            <div class="qr-container">
                <h3>Quét Mã QR Để Thanh Toán</h3>
                <div class="qr-code">
                    <img src="<?php echo $data['qrUrl']; ?>" alt="QR Code VietQR">
                </div>
                <div class="bank-info">
                    <h4>Thông Tin Chuyển Khoản</h4>
                    <div class="bank-detail">
                        <i class="fas fa-university"></i>
                        <div>
                            <strong>Ngân hàng:</strong>
                            <span><?php echo $data['bankInfo']['bankName']; ?></span>
                        </div>
                    </div>
                    <div class="bank-detail">
                        <i class="fas fa-credit-card"></i>
                        <div>
                            <strong>Số tài khoản:</strong>
                            <span><?php echo $data['bankInfo']['accountNo']; ?></span>
                        </div>
                    </div>
                    <div class="bank-detail highlight">
                        <i class="fas fa-money-bill-wave"></i>
                        <div>
                            <strong>Số tiền:</strong>
                            <span class="amount-value"><?php echo $data['bankInfo']['amount']; ?>đ</span>
                        </div>
                    </div>
                    <div class="bank-detail">
                        <i class="fas fa-comment-dots"></i>
                        <div>
                            <strong>Nội dung:</strong>
                            <span><?php echo $data['bankInfo']['description']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="payment-note">
                    <i class="fas fa-info-circle"></i>
                    <p><strong>Lưu ý quan trọng:</strong> Vui lòng chuyển khoản đúng nội dung để đơn hàng được xử lý nhanh chóng.</p>
                </div>
            </div>
        </div>
        <div class="action-buttons">
            <a href="/COFFEE_PHP/Checkout/orderSuccess?order_id=<?php echo $data['order']->id; ?>" class="btn btn-primary">
                <i class="fas fa-check-double"></i>
                Tôi Đã Chuyển Khoản
            </a>
            <a href="/COFFEE_PHP/User/index" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Về Trang Chủ
            </a>
        </div>
    </div>
</main>
