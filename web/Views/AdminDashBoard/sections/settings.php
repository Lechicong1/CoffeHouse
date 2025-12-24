<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/sections/settings.php
 * Settings Section - Cài đặt hệ thống
 */
?>
<section id="settings" class="content-section">
    <div class="section-header">
        <h2>Cài đặt Hệ thống</h2>
    </div>

    <!-- Settings Tabs -->
    <div class="settings-container">
        <div class="settings-tabs">
            <button class="tab-btn active" data-tab="general">
                ⚙️ Cài đặt chung
            </button>
            <button class="tab-btn" data-tab="shop">
                🏪 Thông tin quán
            </button>
            <button class="tab-btn" data-tab="payment">
                💳 Thanh toán
            </button>
            <button class="tab-btn" data-tab="notification">
                🔔 Thông báo
            </button>
            <button class="tab-btn" data-tab="security">
                🔒 Bảo mật
            </button>
        </div>

        <!-- General Settings -->
        <div class="settings-content active" id="general">
            <h3>Cài đặt chung</h3>
            <form class="settings-form">
                <div class="form-group">
                    <label>Ngôn ngữ hệ thống</label>
                    <select class="form-control">
                        <option value="vi" selected>Tiếng Việt</option>
                        <option value="en">English</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Múi giờ</label>
                    <select class="form-control">
                        <option value="Asia/Ho_Chi_Minh" selected>GMT+7 (Hồ Chí Minh)</option>
                        <option value="Asia/Bangkok">GMT+7 (Bangkok)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Định dạng tiền tệ</label>
                    <input type="text" class="form-control" value="VND" readonly>
                </div>

                <button type="submit" class="btn-primary">💾 Lưu thay đổi</button>
            </form>
        </div>

        <!-- Shop Information -->
        <div class="settings-content" id="shop">
            <h3>Thông tin quán</h3>
            <form class="settings-form">
                <div class="form-group">
                    <label>Tên quán</label>
                    <input type="text" class="form-control" value="Coffee House" required>
                </div>
                
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" class="form-control" value="123 Nguyễn Văn Linh, Q.7, TP.HCM" required>
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="tel" class="form-control" value="0901234567" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="info@coffeehouse.vn" required>
                </div>

                <div class="form-group">
                    <label>Giờ mở cửa</label>
                    <input type="text" class="form-control" value="6:00 AM - 11:00 PM">
                </div>

                <button type="submit" class="btn-primary">💾 Lưu thay đổi</button>
            </form>
        </div>

        <!-- Payment Settings -->
        <div class="settings-content" id="payment">
            <h3>Cài đặt thanh toán</h3>
            <div class="payment-methods">
                <div class="payment-option">
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                    <div class="payment-info">
                        <h4>💵 Tiền mặt</h4>
                        <p>Cho phép thanh toán bằng tiền mặt</p>
                    </div>
                </div>

                <div class="payment-option">
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                    <div class="payment-info">
                        <h4>💳 Chuyển khoản</h4>
                        <p>Thanh toán qua chuyển khoản ngân hàng</p>
                    </div>
                </div>

                <div class="payment-option">
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                    <div class="payment-info">
                        <h4>📱 Ví điện tử</h4>
                        <p>MoMo, ZaloPay, VNPay</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="settings-content" id="notification">
            <h3>Cài đặt thông báo</h3>
            <div class="notification-options">
                <div class="notification-item">
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                    <div class="notification-info">
                        <h4>Đơn hàng mới</h4>
                        <p>Nhận thông báo khi có đơn hàng mới</p>
                    </div>
                </div>

                <div class="notification-item">
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                    <div class="notification-info">
                        <h4>Thanh toán thành công</h4>
                        <p>Thông báo khi khách thanh toán thành công</p>
                    </div>
                </div>

                <div class="notification-item">
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                    <div class="notification-info">
                        <h4>Sản phẩm sắp hết</h4>
                        <p>Cảnh báo khi sản phẩm sắp hết hàng</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="settings-content" id="security">
            <h3>Cài đặt bảo mật</h3>
            <form class="settings-form">
                <div class="form-group">
                    <label>Mật khẩu hiện tại</label>
                    <input type="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu mới</label>
                    <input type="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" required>
                </div>

                <button type="submit" class="btn-primary">🔒 Đổi mật khẩu</button>
            </form>

            <div class="security-options">
                <div class="security-item">
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                    <div class="security-info">
                        <h4>Xác thực 2 lớp</h4>
                        <p>Tăng cường bảo mật với xác thực 2 lớp</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
