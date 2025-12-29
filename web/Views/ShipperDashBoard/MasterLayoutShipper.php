<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Shipper - Coffee House">
    <title><?php echo $title ?? 'Shipper Dashboard'; ?></title>
    <base href="http://localhost/COFFEE_PHP/">

    <!-- Link CSS -->
    <link rel="stylesheet" href="Public/Css/shipper-style.css">
</head>
<body>
    <!-- Container chính -->
    <div class="dashboard-container">

        <!-- Header - Logo & Tiêu đề -->
        <div class="dashboard-header">
            <div class="header-left">
                <div class="logo">☕</div>
                <div class="header-info">
                    <h1 class="dashboard-title">Coffee House</h1>
                    <p class="dashboard-subtitle">Dashboard Shipper</p>
                </div>
            </div>
            <div class="header-right">
                <div class="shipper-info">
                    <span class="shipper-name"><?php echo $_SESSION['user']['name'] ?? 'Shipper'; ?></span>
                    <span class="shipper-id">ID: <?php echo $_SESSION['user']['id'] ?? '---'; ?></span>
                </div>
                <button class="btn-logout" onclick="handleLogout()">
                    Đăng xuất
                </button>
            </div>
        </div>

        <!-- Thông báo (ẩn mặc định) -->
        <div id="alertBox" class="alert"></div>

        <!-- Main Content -->
        <main class="dashboard-content">
            <?php
                // Render Page Content
                if (isset($data['page']) && file_exists('./web/Views/ShipperDashBoard/Pages/' . $data['page'] . '.php')) {
                    require_once './web/Views/ShipperDashBoard/Pages/' . $data['page'] . '.php';
                } else {
                    echo '<div style="text-align: center; padding: 50px; color: var(--text-light);">';
                    echo '<h2>Trang không tồn tại</h2>';
                    echo '</div>';
                }
            ?>
        </main>

    </div>

    <!-- Link JS -->
    <script src="Public/Js/shipper-logic.js"></script>
</body>
</html>
