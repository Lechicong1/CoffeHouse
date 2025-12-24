<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/sections/dashboard.php
 * Dashboard Section - Tổng quan hệ thống
 */

// Lấy dữ liệu thống kê (sau này sẽ lấy từ database qua Controller)
$stats = [
    'todayRevenue' => '5,200,000₫',
    'todayOrders' => 124,
    'newCustomers' => 18,
];

// Đơn hàng gần đây (mock data - sau này từ database)
$recentOrders = [
    [
        'id' => '#1023',
        'customer' => 'Nguyễn Văn A',
        'items' => 'Cà phê sữa đá x2',
        'total' => '50,000₫',
        'status' => 'completed',
        'time' => '10:30 AM'
    ],
    [
        'id' => '#1024',
        'customer' => 'Trần Thị B',
        'items' => 'Latte nóng',
        'total' => '45,000₫',
        'status' => 'pending',
        'time' => '11:15 AM'
    ],
    [
        'id' => '#1025',
        'customer' => 'Lê Văn C',
        'items' => 'Cappuccino x2, Bánh mì',
        'total' => '95,000₫',
        'status' => 'processing',
        'time' => '11:45 AM'
    ],
];

// Mapping trạng thái
$statusLabels = [
    'completed' => 'Hoàn thành',
    'pending' => 'Đang chờ',
    'processing' => 'Đang pha',
    'cancelled' => 'Đã hủy'
];
?>
<section id="dashboard" class="content-section">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon revenue">💰</div>
            <div class="stat-details">
                <h3>Doanh thu ngày</h3>
                <p class="stat-value"><?= $stats['todayRevenue'] ?></p>
                <span class="stat-change positive">+12.5%</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orders">🧾</div>
            <div class="stat-details">
                <h3>Số đơn hàng</h3>
                <p class="stat-value"><?= $stats['todayOrders'] ?></p>
                <span class="stat-change positive">+8.2%</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon customers">👤</div>
            <div class="stat-details">
                <h3>Khách mới</h3>
                <p class="stat-value"><?= $stats['newCustomers'] ?></p>
                <span class="stat-change negative">-2.4%</span>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="orders-section">
        <div class="section-header">
            <h2>Đơn hàng gần đây</h2>
            <a href="?section=orders" class="btn-link">Xem tất cả →</a>
        </div>
        
        <div class="table-responsive">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên khách</th>
                        <th>Món</th>
                        <th>Thời gian</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($order['id']) ?></strong></td>
                            <td><?= htmlspecialchars($order['customer']) ?></td>
                            <td><?= htmlspecialchars($order['items']) ?></td>
                            <td><?= htmlspecialchars($order['time']) ?></td>
                            <td><strong><?= $order['total'] ?></strong></td>
                            <td>
                                <span class="status <?= $order['status'] ?>">
                                    <?= $statusLabels[$order['status']] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Thao tác nhanh</h2>
        <div class="actions-grid">
            <a href="?section=orders" class="action-card">
                <span class="action-icon">🛍️</span>
                <span class="action-text">Tạo đơn mới</span>
            </a>
            <a href="?section=products" class="action-card">
                <span class="action-icon">☕</span>
                <span class="action-text">Thêm sản phẩm</span>
            </a>
            <a href="?section=employees" class="action-card">
                <span class="action-icon">👔</span>
                <span class="action-text">Quản lý nhân viên</span>
            </a>
            <a href="?section=revenue" class="action-card">
                <span class="action-icon">📈</span>
                <span class="action-text">Xem báo cáo</span>
            </a>
        </div>
    </div>
</section>
