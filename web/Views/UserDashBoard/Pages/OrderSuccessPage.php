<!-- ===================================
     FILE: OrderSuccessPage.php
     MÔ TẢ: Trang đặt hàng thành công
     Nội dung chính - Được include vào MasterLayout
     =================================== -->
<main class="success-container">
    <div class="success-wrapper">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="success-title">ĐẶT HÀNG THÀNH CÔNG!</h1>
        <p class="success-message">Cảm ơn bạn đã đặt hàng tại Coffee House</p>
        <?php if (isset($data['order'])): ?>
            <div class="order-info">
                <h2>Thông Tin Đơn Hàng</h2>
                <div class="info-row">
                    <span class="label">Mã đơn hàng:</span>
                    <span class="value order-code"><?php echo htmlspecialchars($data['order']->order_code); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Tổng tiền:</span>
                    <span class="value amount"><?php echo number_format($data['order']->total_amount, 0, ',', '.'); ?>đ</span>
                </div>
                <div class="info-row">
                    <span class="label">Phương thức thanh toán:</span>
                    <span class="value">
                        <?php echo $data['order']->payment_method === 'CASH' ? 'Tiền mặt' : 'Chuyển khoản'; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Trạng thái:</span>
                    <span class="value status">
                        <?php
                            $statusText = [
                                'PENDING' => 'Đang chờ xử lý',
                                'AWAITING_PAYMENT' => 'Chờ thanh toán',
                                'CONFIRMED' => 'Đã xác nhận',
                                'PREPARING' => 'Đang chuẩn bị',
                                'SHIPPING' => 'Đang giao',
                                'COMPLETED' => 'Hoàn thành',
                                'CANCELLED' => 'Đã hủy'
                            ];
                            echo $statusText[$data['order']->status] ?? 'Đang chờ xử lý';
                        ?>
                    </span>
                </div>
                <?php if ($data['order']->payment_method === 'BANK_TRANSFER' && $data['order']->payment_status === 'AWAITING_PAYMENT'): ?>
                    <div class="payment-notice">
                        <i class="fas fa-info-circle"></i>
                        <p>Vui lòng hoàn tất thanh toán để đơn hàng được xử lý nhanh chóng.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="action-buttons">
            <a href="/COFFEE_PHP/User/index" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Về Trang Chủ
            </a>
            <a href="/COFFEE_PHP/User/orders" class="btn btn-secondary">
                <i class="fas fa-receipt"></i>
                Xem Đơn Hàng
            </a>
        </div>
        <div class="thank-you-note">
            <p>Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng.</p>
            <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ: <strong>1900 xxxx</strong></p>
        </div>
    </div>
</main>
