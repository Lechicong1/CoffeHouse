<?php
/**
 * VIEW CON - Quản lý Đơn hàng (Admin)
 * Hiển thị danh sách đơn hàng với thông tin cơ bản
 */

// Lấy dữ liệu từ Controller
$orders = $data['orders'] ?? [];
$keyword = $data['keyword'] ?? '';
$totalOrders = $data['totalOrders'] ?? count($orders);

/**
 * Hàm chuyển đổi status sang tiếng Việt
 */
function getStatusLabel($status) {
    $labels = [
        'PENDING' => 'Chờ xử lý',
        'AWAITING_PAYMENT' => 'Chờ thanh toán',
        'PREPARING' => 'Đang pha chế',
        'READY' => 'Sẵn sàng',
        'SHIPPING' => 'Đang giao',
        'COMPLETED' => 'Hoàn thành',
        'CANCELLED' => 'Đã hủy'
    ];
    return $labels[$status] ?? $status;
}

/**
 * Hàm lấy class CSS cho status badge
 */
function getStatusClass($status) {
    $classes = [
        'PENDING' => 'status-pending',
        'AWAITING_PAYMENT' => 'status-awaiting',
        'PREPARING' => 'status-preparing',
        'READY' => 'status-ready',
        'SHIPPING' => 'status-shipping',
        'COMPLETED' => 'status-completed',
        'CANCELLED' => 'status-cancelled'
    ];
    return $classes[$status] ?? 'status-default';
}

/**
 * Hàm chuyển đổi payment_status sang tiếng Việt
 */
function getPaymentStatusLabel($paymentStatus) {
    $labels = [
        'PENDING' => 'Chưa thanh toán',
        'AWAITING_PAYMENT' => 'Chờ thanh toán',
        'PAID' => 'Đã thanh toán',
        'REFUNDED' => 'Đã hoàn tiền',
        'UNPAID' => 'Chưa thanh toán'
    ];
    return $labels[$paymentStatus] ?? $paymentStatus;
}

/**
 * Hàm lấy class CSS cho payment status badge
 */
function getPaymentStatusClass($paymentStatus) {
    $classes = [
        'PENDING' => 'payment-pending',
        'AWAITING_PAYMENT' => 'payment-awaiting',
        'PAID' => 'payment-paid',
        'REFUNDED' => 'payment-refunded',
        'UNPAID' => 'payment-unpaid'
    ];
    return $classes[$paymentStatus] ?? 'payment-default';
}
?>

<!-- Import CSS riêng cho trang Đơn hàng -->
<link rel="stylesheet" href="Public/Css/order-admin-page.css">

<section id="orders" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>📦 Quản lý Đơn hàng</h2>
            <p class="subtitle">Tổng số: <strong><?= $totalOrders ?></strong> đơn hàng</p>
        </div>
        <div class="header-actions">
            <!-- Nút Làm mới -->
            <a href="OrderAdminController" class="btn-primary btn-refresh">
                🔄 Làm mới
            </a>

            <!-- Button Xuất Excel -->
            <form method="POST" action="OrderAdminController/xuatexcel" style="margin: 0;">
                <input type="hidden" name="txtSearch" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" name="btnXuatexcel" class="btn-primary btn-excel">
                    📊 Xuất Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 24px;">
        <form method="POST" action="OrderAdminController/timkiem" class="search-form">
            <input type="text" name="txtSearch" class="search-input"
                   placeholder="🔍 Tìm kiếm theo mã đơn, tên người nhận, số điện thoại..."
                   value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" name="btnTimkiem" class="btn-primary">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã đơn hàng</th>
                    <th>Trạng thái</th>
                    <th>Thanh toán</th>
                    <th>Tổng tiền</th>
                    <th>Tên người nhận</th>
                    <th>SĐT người nhận</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                            📭 Không có đơn hàng nào!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $i = 1;
                    foreach ($orders as $order):
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><strong><?= htmlspecialchars($order['order_code']) ?></strong></td>
                            <td>
                                <span class="status-badge <?= getStatusClass($order['status']) ?>">
                                    <?= getStatusLabel($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="payment-badge <?= getPaymentStatusClass($order['payment_status']) ?>">
                                    <?= getPaymentStatusLabel($order['payment_status']) ?>
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #27ae60;">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?>đ
                            </td>
                            <td><?= htmlspecialchars($order['receiver_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($order['receiver_phone'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
