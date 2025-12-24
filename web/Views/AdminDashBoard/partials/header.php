<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/partials/header.php
 * Header Component - Thanh header với thông tin user và toggle menu
 */

// Lấy thông tin admin từ biến đã được định nghĩa trong admin.php
$displayName = $adminName ?? 'Admin';
$displayRole = $adminRole ?? 'Administrator';

// Tạo initials từ tên (2 chữ cái đầu)
$nameParts = explode(' ', $displayName);
$initials = strtoupper(
    (isset($nameParts[0]) ? substr($nameParts[0], 0, 1) : '') .
    (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : 
     (isset($nameParts[0]) && strlen($nameParts[0]) > 1 ? substr($nameParts[0], 1, 1) : ''))
);

// Lấy tiêu đề section hiện tại
$sectionTitles = [
    'dashboard' => 'Tổng quan',
    'orders' => 'Quản lý Đơn hàng',
    'products' => 'Quản lý Sản phẩm',
    'customers' => 'Quản lý Khách hàng',
    'employees' => 'Quản lý Nhân viên',
    'revenue' => 'Thống kê Doanh thu',
    'settings' => 'Cài đặt Hệ thống'
];

$pageTitle = $sectionTitles[$currentSection] ?? ucfirst($currentSection);
?>
<header class="top-header">
    <div class="header-left">
        <button class="toggle-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
    </div>
    
    <div class="header-right">
        <!-- Notification Icon (optional) -->
        <button class="icon-btn notification-btn" title="Thông báo">
            <span class="icon">🔔</span>
            <span class="badge">3</span>
        </button>
        
        <!-- User Info -->
        <div class="user-info">
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($displayName) ?></span>
                <span class="user-role"><?= htmlspecialchars($displayRole) ?></span>
            </div>
            <div class="user-avatar" title="<?= htmlspecialchars($displayName) ?>">
                <?= $initials ?>
            </div>
        </div>
    </div>
</header>
