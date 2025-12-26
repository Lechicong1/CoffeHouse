<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập vào Coffee House - Quán cà phê phong cách Minimalist">
    <title>Đăng Nhập - Coffee House</title>

    <!-- Link CSS -->
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/login.css">
</head>
<body>
    <!-- Container chính của form login -->
    <div class="login-container">
        <!-- Header - Logo & Tiêu đề -->
        <div class="login-header">
            <div class="logo">☕</div>
            <h1 class="login-title">Coffee House</h1>
            <p class="login-subtitle">Đăng nhập để tiếp tục</p>
        </div>

        <!-- Back button -->
        <div class="back-button-container">
            <a href="/COFFEE_PHP/User/index" class="btn-back">← Quay lại trang chủ</a>
        </div>

        <!-- Thông báo (ẩn mặc định) -->
        <div id="alertBox" class="alert"></div>

        <!-- Form đăng nhập -->
        <form id="loginForm" class="login-form" action="/COFFEE_PHP/Auth/login" method="POST">
            <input type="hidden" id="userType" name="userType" value="customer">
            <!-- Nhóm Username -->
            <div class="form-group">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="Nhập tên đăng nhập"
                    autocomplete="username"
                    required
                >
            </div>

            <!-- Nhóm Password -->
            <div class="form-group">
                <label for="password" class="form-label">Mật khẩu</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Nhập mật khẩu"
                    autocomplete="current-password"
                    required
                >
            </div>

            <!-- Checkbox - Ghi nhớ đăng nhập -->
            <div class="form-checkbox-group">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="form-checkbox"
                    value="1"
                >
                <label for="remember" class="form-checkbox-label">
                    Ghi nhớ đăng nhập
                </label>
            </div>

            <!-- Nút đăng nhập - Hai lựa chọn: Khách hàng / Nhân viên -->
            <div class="login-buttons">
                <button type="button" class="btn-login" onclick="loginAs('customer')">Đăng Nhập (Khách hàng)</button>
                <button type="button" class="btn-login btn-secondary" onclick="loginAs('employee')">Đăng Nhập (Nhân viên)</button>
            </div>
        </form>

        <!-- Footer - Links -->
        <div class="login-footer">
            <a href="#" class="footer-link" onclick="handleForgotPassword(); return false;">
                Quên mật khẩu?
            </a>
            <span class="footer-separator">|</span>
            <a href="#" class="footer-link" onclick="handleRegister(); return false;">
                Đăng ký tài khoản
            </a>
        </div>
    </div>

    <!-- Link JavaScript -->
    <script src="/COFFEE_PHP/Public/Js/login.js"></script>
</body>
</html>
