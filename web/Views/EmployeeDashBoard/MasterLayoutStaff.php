<?php
/**
 * Master Layout - Staff Dashboard (Sage Green & Cream Theme)
 * Dành cho nhân viên bán hàng / Barista
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập (Uncomment khi có Auth)
// if (!isset($_SESSION['user_logged_in'])) {
//     header('Location: /COFFEE_PHP/login');
//     exit;
// }

// Lấy thông tin nhân viên từ session
$staffName = $_SESSION['staff_name'] ?? 'Nhân Viên';
$staffRole = $_SESSION['staff_role'] ?? 'Staff';

// Xác định section hiện tại
$currentSection = $data['section'] ?? 'pos';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee House Staff - <?= ucfirst($currentSection) ?></title>
    <base href="http://localhost/COFFEE_PHP/">
    
    <!-- CSS Layout mới cho Staff -->
    <link rel="stylesheet" href="Public/Css/staff-layout.css">
    
    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Outer Container (Background Sage Green) -->
    <div class="staff-container">
        
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar" id="sidebar">
            <!-- Brand -->
            <div class="sidebar-header">
                <span class="brand-icon"><i class="fa-solid fa-mug-hot"></i></span>
                <span class="brand-name">COFFEE HOUSE</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <ul class="menu-list">
                    <!-- POS / Bán Hàng -->
                    <li class="menu-item <?= $currentSection === 'pos' ? 'active' : '' ?>">
                        <a href="http://localhost/COFFEE_PHP/StaffController/pos" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-cash-register"></i></span>
                            <span class="menu-text">BÁN HÀNG</span>
                        </a>
                    </li>

                    <!-- Đơn Hàng -->
                    <li class="menu-item <?= $currentSection === 'orders' ? 'active' : '' ?>">
                        <a href="http://localhost/COFFEE_PHP/StaffController/orders" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-receipt"></i></span>
                            <span class="menu-text">ĐƠN HÀNG</span>
                        </a>
                    </li>

                    <!-- (Removed: Khách Hàng & Cá Nhân per request) -->
                </ul>
            </nav>

            <!-- Logout -->
            <div class="sidebar-footer">
                <a href="/COFFEE_PHP/Auth/logout" class="menu-link logout-link">
                    <span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span class="menu-text">ĐĂNG XUẤT</span>
                </a>
            </div>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content" id="mainContent">
            <!-- ========== HEADER ========== -->
            <header class="header">
                <div class="header-left" style="display: flex; align-items: center;">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title" style="margin-left: 15px;"><?= strtoupper($currentSection) ?></h1>
                </div>

                <div class="header-right" style="display: flex; align-items: center; gap: 20px;">

                    <!-- User Profile -->
                    <div class="user-profile">
                        <div class="user-avatar">
                            <span><?= substr($staffName, 0, 1) ?></span>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($staffName) ?></span>
                            <span class="user-role"><?= htmlspecialchars($staffRole) ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ========== DYNAMIC CONTENT ========== -->
            <div class="content-wrapper">
                <?php 
                    // Include view con dựa vào tham số 'page'
                    if (isset($data['page'])) {
                        // Đường dẫn tới các trang con của Staff
                        $pageFile = __DIR__ . '/Pages/' . $data['page'] . '.php';
                        
                        // Fallback: Nếu không tìm thấy trong Pages/, thử tìm ở thư mục gốc EmployeeDashBoard
                        if (!file_exists($pageFile)) {
                            $pageFile = __DIR__ . '/' . $data['page'] . '.php';
                        }

                        if (file_exists($pageFile)) {
                            include_once $pageFile;
                        } else {
                            echo '<div style="text-align: center; padding: 50px; color: var(--text-light);">';
                            echo '<h2>⚠️ Không tìm thấy trang</h2>';
                            echo '<p>File: ' . htmlspecialchars($data['page']) . '.php không tồn tại.</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div style="text-align: center; padding: 50px;">';
                        echo '<h2>Chào mừng trở lại, ' . htmlspecialchars($staffName) . '!</h2>';
                        echo '<p>Chọn một chức năng từ menu để bắt đầu.</p>';
                        echo '</div>';
                    }
                ?>
            </div>
        </main>

        <!-- ========== MODAL (Generic) ========== -->
        <div id="modalOverlay" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 id="modalTitle" class="modal-title">TIÊU ĐỀ</h2>
                    <button class="close-modal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Content injected via JS -->
                </div>
                <div class="modal-footer" style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button class="btn btn-secondary close-modal">Đóng</button>
                    <button class="btn btn-primary" id="modalSubmitBtn">Lưu</button>
                </div>
            </div>
        </div>

    </div>

    <!-- Simple Script for Layout Interaction -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            if(toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                });
            }

            // Modal Logic (Basic)
            const modalOverlay = document.getElementById('modalOverlay');
            const closeButtons = document.querySelectorAll('.close-modal');

            closeButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    modalOverlay.classList.remove('active');
                });
            });

            // Close modal when clicking outside
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) {
                    modalOverlay.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
