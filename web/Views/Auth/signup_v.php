<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng ký tài khoản Coffee House - Quán cà phê phong cách Minimalist">
    <title>Đăng Ký - Coffee House</title>

    <!-- Link CSS -->
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/signup.css">
</head>
<body>
    <!-- Container chính của form signup -->
    <div class="signup-container">

        <!-- Header - Logo & Tiêu đề -->
        <div class="signup-header">
            <div class="logo">☕</div>
            <h1 class="signup-title">Coffee House</h1>
            <p class="signup-subtitle">Tạo tài khoản mới</p>
        </div>

        <!-- Thông báo (ẩn mặc định) -->
        <div id="alertBox" class="alert"></div>

        <!-- Form đăng ký -->
        <form id="signupForm" class="signup-form" action="/COFFEE_PHP/Auth/register" method="POST">

            <!-- Nhóm Full Name -->
            <div class="form-group">
                <label for="fullname" class="form-label">Họ và Tên</label>
                <input
                    type="text"
                    id="fullname"
                    name="fullname"
                    class="form-input"
                    placeholder="Nhập họ và tên"
                    autocomplete="name"
                    required
                >
            </div>

            <!-- Nhóm Phone -->
            <div class="form-group">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-input"
                    placeholder="Nhập số điện thoại"
                    autocomplete="tel"
                    pattern="[0-9]{10,11}"
                    required
                >
            </div>

            <!-- Nhóm Address -->
            <div class="form-group">
                <label for="address" class="form-label">Địa chỉ</label>
                <input
                    type="text"
                    id="address"
                    name="address"
                    class="form-input"
                    placeholder="Nhập địa chỉ (tùy chọn)"
                    autocomplete="street-address"
                >
            </div>

            <!-- Nhóm Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email (tùy chọn)</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="example@email.com"
                    autocomplete="email"
                >
            </div>

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
                    placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                    autocomplete="new-password"
                    required
                >
            </div>

            <!-- Nhóm Confirm Password -->
            <div class="form-group">
                <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                <input
                    type="password"
                    id="confirmPassword"
                    name="confirmPassword"
                    class="form-input"
                    placeholder="Nhập lại mật khẩu"
                    autocomplete="new-password"
                    required
                >
            </div>

            <!-- Nút đăng ký - Pill Shape -->
            <button type="submit" class="btn-signup">
                Đăng Ký
            </button>

        </form>

        <!-- Footer - Links -->
        <div class="signup-footer">
            <span class="footer-text">Đã có tài khoản?</span>
            <a href="/COFFEE_PHP/Auth/showLogin" class="footer-link">
                Đăng nhập ngay
            </a>
        </div>

    </div>

    <!-- Link JavaScript -->
    <script src="/COFFEE_PHP/Public/Js/signup.js"></script>
</body>
</html>
