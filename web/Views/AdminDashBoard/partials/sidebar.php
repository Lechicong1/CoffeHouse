<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/partials/sidebar.php
 * Sidebar Component - Menu điều hướng cho Admin Dashboard
 */

// Định nghĩa các menu items
$menuItems = [
    [
        'icon' => '📊',
        'text' => 'Tổng quan',
        'url' => '/web/Views/AdminDashBoard/dashboard.php', // Trang tĩnh hoặc trang chủ
        'target' => 'dashboard',
        'description' => 'Xem tổng quan hệ thống'
    ],
    [
        'icon' => '🛍️',
        'text' => 'Đơn hàng',
        'url' => '?url=Order',
        'target' => 'orders',
        'description' => 'Quản lý đơn hàng'
    ],
    [
        'icon' => '☕',
        'text' => 'Sản phẩm',
        'url' => '?url=Product',
        'target' => 'products',
        'description' => 'Quản lý sản phẩm'
    ],
    [
        'icon' => '👥',
        'text' => 'Khách hàng',
        'url' => '?url=Customer',
        'target' => 'customers',
        'description' => 'Quản lý khách hàng'
    ],
    [
        'icon' => '👔',
        'text' => 'Nhân viên',
        'url' => '?url=Employee',
        'target' => 'employees',
        'description' => 'Quản lý nhân viên'
    ],
];
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="brand-icon">☕</span>
        <h2 class="brand-name">Coffee House</h2>
    </div>
    
    <ul class="menu-list">
        <?php foreach ($menuItems as $item): ?>
            <li class="menu-item <?= ($currentSection === $item['target']) ? 'active' : '' ?>" 
                data-target="<?= $item['target'] ?>">
                <a href="<?= $item['url'] ?>"
                   class="menu-link"
                   title="<?= $item['description'] ?>">
                    <span class="icon"><?= $item['icon'] ?></span>
                    <span class="text"><?= htmlspecialchars($item['text']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <!-- Logout Button -->
    <div class="sidebar-footer">
        <a href="/logout.php" class="menu-link logout-link">
            <span class="icon">🚪</span>
            <span class="text">Đăng xuất</span>
        </a>
    </div>
</nav>
