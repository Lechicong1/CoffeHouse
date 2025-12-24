<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/sections/orders.php
 * Orders Management Section - Quản lý đơn hàng
 */

// Mock data - Sau này sẽ lấy từ database
$orders = [
    [
        'id' => '#1023',
        'customer' => 'Nguyễn Văn A',
        'phone' => '0901234567',
        'time' => '10:30 AM',
        'items' => 'Cà phê sữa đá x2',
        'total' => '50,000₫',
        'status' => 'completed',
        'payment' => 'Tiền mặt'
    ],
    [
        'id' => '#1024',
        'customer' => 'Trần Thị B',
        'phone' => '0907654321',
        'time' => '11:15 AM',
        'items' => 'Latte nóng',
        'total' => '45,000₫',
        'status' => 'pending',
        'payment' => 'Chuyển khoản'
    ],
    [
        'id' => '#1025',
        'customer' => 'Lê Văn C',
        'phone' => '0912345678',
        'time' => '11:45 AM',
        'items' => 'Cappuccino x2, Bánh mì',
        'total' => '95,000₫',
        'status' => 'processing',
        'payment' => 'Ví điện tử'
    ],
];

$statusLabels = [
    'completed' => 'Hoàn thành',
    'pending' => 'Đang chờ',
    'processing' => 'Đang pha',
    'cancelled' => 'Đã hủy'
];
?>
<section id="orders" class="content-section">
    <div class="section-header">
        <h2>Quản lý Đơn hàng</h2>
        <div class="header-actions">
            <input type="text" class="search-input" placeholder="🔍 Tìm kiếm đơn hàng...">
            <select class="filter-select">
                <option value="all">Tất cả trạng thái</option>
                <option value="pending">Đang chờ</option>
                <option value="processing">Đang pha</option>
                <option value="completed">Hoàn thành</option>
                <option value="cancelled">Đã hủy</option>
            </select>
            <button class="btn-primary" onclick="openModal('order', 'create')">
                + Tạo đơn mới
            </button>
        </div>
    </div>

    <!-- Orders Statistics -->
    <div class="mini-stats">
        <div class="mini-stat">
            <span class="mini-stat-label">Tổng đơn hôm nay</span>
            <span class="mini-stat-value">124</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Đang xử lý</span>
            <span class="mini-stat-value warning">18</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Hoàn thành</span>
            <span class="mini-stat-value success">106</span>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="table-responsive">
        <table class="data-table" id="ordersTable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all" aria-label="Chọn tất cả">
                    </th>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>SĐT</th>
                    <th>Thời gian</th>
                    <th>Món</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr data-order-id="<?= $order['id'] ?>">
                        <td>
                            <input type="checkbox" class="select-item">
                        </td>
                        <td><strong><?= htmlspecialchars($order['id']) ?></strong></td>
                        <td><?= htmlspecialchars($order['customer']) ?></td>
                        <td><?= htmlspecialchars($order['phone']) ?></td>
                        <td><?= htmlspecialchars($order['time']) ?></td>
                        <td><?= htmlspecialchars($order['items']) ?></td>
                        <td><strong><?= $order['total'] ?></strong></td>
                        <td><?= htmlspecialchars($order['payment']) ?></td>
                        <td>
                            <span class="status <?= $order['status'] ?>">
                                <?= $statusLabels[$order['status']] ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon edit" 
                                        onclick="viewOrder('<?= $order['id'] ?>')" 
                                        title="Xem chi tiết">
                                    👁️
                                </button>
                                <button class="btn-icon edit" 
                                        onclick="editOrder('<?= $order['id'] ?>')" 
                                        title="Chỉnh sửa">
                                    ✏️
                                </button>
                                <button class="btn-icon delete" 
                                        onclick="deleteOrder('<?= $order['id'] ?>')" 
                                        title="Xóa">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <button class="btn-secondary" disabled>← Trước</button>
        <span class="page-info">Trang 1 / 5</span>
        <button class="btn-secondary">Sau →</button>
    </div>
</section>
