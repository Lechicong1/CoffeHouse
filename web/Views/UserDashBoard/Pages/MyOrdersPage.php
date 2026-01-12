<?php
$orders = $data['orders'] ?? [];
?>

<section class="my-orders-section">
    <div class="container">
        <h1 class="page-title">ĐƠN HÀNG CỦA TÔI</h1>



        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <div class="empty-icon">📦</div>
                <h2>Chưa có đơn hàng nào</h2>
                <p>Bạn chưa đặt đơn hàng nào. Hãy khám phá thực đơn của chúng tôi!</p>
                <a href="?url=UserController/menu" class="btn-primary">Xem Thực Đơn</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <h3>Đơn hàng #<?= htmlspecialchars($order->order_code) ?></h3>
                                <span class="order-date">
                                    📅 <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                                </span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-<?= strtolower($order->status) ?>">
                                    <?php
                                    $statusText = [
                                        'PENDING' => 'Chờ xử lý',
                                        'PREPARING' => 'Đang pha chế',
                                        'READY' => 'Đã hoàn thành',
                                        'SHIPPING' => 'Đang giao',
                                        'COMPLETED' => 'Đã giao',
                                        'CANCELLED' => 'Đã hủy'
                                    ];
                                    echo $statusText[$order->status] ?? $order->status;
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="order-body">
                            <div class="order-details">

                                <?php if ($order->order_type === 'ONLINE_DELIVERY'): ?>
                                    <div class="detail-row">
                                        <span class="label">Người nhận:</span>
                                        <span class="value"><?= htmlspecialchars($order->receiver_name) ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Số điện thoại:</span>
                                        <span class="value"><?= htmlspecialchars($order->receiver_phone) ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Địa chỉ:</span>
                                        <span class="value"><?= htmlspecialchars($order->shipping_address) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="detail-row">
                                    <span class="label">Thanh toán:</span>
                                    <span class="value">
                                        <?php
                                        $paymentText = [
                                            'CASH' => '💵 Tiền mặt',
                                            'BANK_TRANSFER' => '🏦 Chuyển khoản',
                                            'COD' => '📦 COD'
                                        ];
                                        echo $paymentText[$order->payment_method] ?? $order->payment_method;
                                        ?>
                                    </span>
                                </div>


                                <?php if (!empty($order->note)): ?>
                                    <div class="detail-row">
                                        <span class="label">Ghi chú:</span>
                                        <span class="value"><?= htmlspecialchars($order->note) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="order-total">
                                <span class="total-label">Tổng tiền:</span>
                                <span class="total-amount"><?= number_format($order->total_amount, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>

                        <div class="order-actions">
                            <?php if ($order->status === 'PENDING'): ?>
                                <form method="POST" action="?url=OrderController/cancelOrder"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                    <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                    <button type="submit" class="btn-cancel">❌ Hủy đơn</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

