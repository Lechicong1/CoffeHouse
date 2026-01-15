<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Coffee House</title>

    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/login.css?v=<?php echo time(); ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">☕</div>
            <h1 class="login-title">Coffee House</h1>
            <p class="login-subtitle">Đăng nhập để tiếp tục</p>
        </div>

        <div class="back-button-container">
            <a href="/COFFEE_PHP/User/index" class="btn-back">← Quay lại trang chủ</a>
        </div>

        <form id="loginForm" class="login-form" action="/COFFEE_PHP/Auth/login" method="POST">
            <input type="hidden" id="userType" name="userType" value="customer">

            <div class="form-group">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" id="username" name="username" class="form-input" placeholder="Nhập tên đăng nhập" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mật khẩu</label>

                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-input" placeholder="Nhập mật khẩu" required>

                    <span class="toggle-password" id="togglePassword">
                            <i class="fa-regular fa-eye"></i>
                        </span>
                </div>
            </div>

            <div class="form-checkbox-group">
                <input type="checkbox" id="remember" name="remember" class="form-checkbox" value="1">
                <label for="remember" class="form-checkbox-label">Ghi nhớ đăng nhập</label>
            </div>

            <!-- Nút đăng nhập - Hai lựa chọn: Khách hàng / Nhân viên -->
            <div class="login-buttons">
                <button type="button" class="btn-login" onclick="loginAs('customer')">Đăng Nhập (Khách hàng)</button>
                <button type="button" class="btn-login btn-secondary" onclick="loginAs('employee')">Đăng Nhập (Nhân viên)</button>
            </div>
        </form>

        <div class="login-footer">
            <a href="#" class="footer-link" onclick="handleRegister(); return false;">Đăng ký tài khoản</a>
        </div>
    </div>

<script src="/COFFEE_PHP/Public/Js/login.js?v=<?php echo time(); ?>"></script>
</body>
</html>