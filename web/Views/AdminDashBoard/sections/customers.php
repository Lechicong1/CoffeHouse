<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/sections/customers.php
 * Customers Management Section - Quản lý khách hàng
 */

// Mock data
$customers = [
    [
        'id' => 'KH001',
        'name' => 'Nguyễn Văn A',
        'phone' => '0901234567',
        'email' => 'nguyenvana@email.com',
        'points' => 150,
        'total_orders' => 12,
        'total_spent' => '1,200,000₫',
        'join_date' => '15/10/2024'
    ],
    [
        'id' => 'KH002',
        'name' => 'Trần Thị B',
        'phone' => '0907654321',
        'email' => 'tranthib@email.com',
        'points' => 85,
        'total_orders' => 8,
        'total_spent' => '850,000₫',
        'join_date' => '20/11/2024'
    ],
];
?>
<section id="customers" class="content-section">
    <div class="section-header">
        <h2>Quản lý Khách hàng</h2>
        <div class="header-actions">
            <input type="text" class="search-input" placeholder="🔍 Tìm khách hàng...">
            <button class="btn-primary" onclick="openModal('customer', 'create')">
                + Thêm khách hàng
            </button>
        </div>
    </div>

    <!-- Customer Statistics -->
    <div class="mini-stats">
        <div class="mini-stat">
            <span class="mini-stat-label">Tổng khách hàng</span>
            <span class="mini-stat-value">1,234</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Khách VIP</span>
            <span class="mini-stat-value success">87</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Khách mới (tháng)</span>
            <span class="mini-stat-value">45</span>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="table-responsive">
        <table class="data-table" id="customersTable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all" aria-label="Chọn tất cả">
                    </th>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Điểm tích lũy</th>
                    <th>Tổng đơn</th>
                    <th>Tổng chi tiêu</th>
                    <th>Ngày tham gia</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr data-customer-id="<?= $customer['id'] ?>">
                        <td>
                            <input type="checkbox" class="select-item">
                        </td>
                        <td><strong><?= htmlspecialchars($customer['id']) ?></strong></td>
                        <td><?= htmlspecialchars($customer['name']) ?></td>
                        <td><?= htmlspecialchars($customer['phone']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td>
                            <span class="points-badge"><?= $customer['points'] ?> điểm</span>
                        </td>
                        <td><?= $customer['total_orders'] ?></td>
                        <td><strong><?= $customer['total_spent'] ?></strong></td>
                        <td><?= htmlspecialchars($customer['join_date']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon edit" 
                                        onclick="viewCustomer('<?= $customer['id'] ?>')" 
                                        title="Xem chi tiết">
                                    👁️
                                </button>
                                <button class="btn-icon edit" 
                                        onclick="editCustomer('<?= $customer['id'] ?>')" 
                                        title="Chỉnh sửa">
                                    ✏️
                                </button>
                                <button class="btn-icon delete" 
                                        onclick="deleteCustomer('<?= $customer['id'] ?>')" 
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
        <span class="page-info">Trang 1 / 25</span>
        <button class="btn-secondary">Sau →</button>
    </div>
</section>
