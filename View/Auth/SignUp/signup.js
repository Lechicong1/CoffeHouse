/* ============================================
   FILE: signup.js
   DESCRIPTION: JavaScript xử lý logic đăng ký
   FRAMEWORK: Vanilla JS (No Dependencies)
   ============================================ */

// Chờ DOM load xong
document.addEventListener('DOMContentLoaded', function() {

    // Lấy các phần tử DOM
    const signupForm = document.getElementById('signupForm');
    const fullnameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const termsCheckbox = document.getElementById('terms');
    const alertBox = document.getElementById('alertBox');

    // Xử lý sự kiện submit form
    signupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        handleSignup();
    });

    // Xử lý sự kiện input (xóa lỗi khi user nhập lại)
    const inputs = [fullnameInput, emailInput, usernameInput, passwordInput, confirmPasswordInput];
    inputs.forEach(input => {
        input.addEventListener('input', clearAlert);
    });

    // Kiểm tra độ mạnh mật khẩu khi nhập
    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });

    /**
     * Hàm xử lý đăng ký
     */
    function handleSignup() {
        const fullname = fullnameInput.value.trim();
        const email = emailInput.value.trim();
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        const termsAccepted = termsCheckbox.checked;

        // Validate dữ liệu
        if (!fullname || !email || !username || !password || !confirmPassword) {
            showAlert('Vui lòng điền đầy đủ thông tin', 'error');
            return;
        }

        // Validate họ tên
        if (fullname.length < 3) {
            showAlert('Họ và tên phải có ít nhất 3 ký tự', 'error');
            fullnameInput.focus();
            return;
        }

        // Validate email
        if (!isValidEmail(email)) {
            showAlert('Email không hợp lệ', 'error');
            emailInput.focus();
            return;
        }

        // Validate username
        if (username.length < 3) {
            showAlert('Tên đăng nhập phải có ít nhất 3 ký tự', 'error');
            usernameInput.focus();
            return;
        }

        if (!isValidUsername(username)) {
            showAlert('Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới', 'error');
            usernameInput.focus();
            return;
        }

        // Validate password
        if (password.length < 6) {
            showAlert('Mật khẩu phải có ít nhất 6 ký tự', 'error');
            passwordInput.focus();
            return;
        }

        // Validate confirm password
        if (password !== confirmPassword) {
            showAlert('Mật khẩu xác nhận không khớp', 'error');
            confirmPasswordInput.focus();
            return;
        }

        // Validate điều khoản
        if (!termsAccepted) {
            showAlert('Vui lòng đồng ý với điều khoản sử dụng', 'warning');
            return;
        }

        // Hiển thị thông báo thành công
        showAlert('Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...', 'success');

        // Giả lập đăng ký (thay bằng API call thực tế)
        setTimeout(() => {
            console.log('Signup Data:', { fullname, email, username, password });
            // Chuyển hướng đến trang đăng nhập
            window.location.href = '../Login/login.html';
        }, 2000);
    }

    /**
     * Kiểm tra email hợp lệ
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Kiểm tra username hợp lệ
     */
    function isValidUsername(username) {
        const usernameRegex = /^[a-zA-Z0-9_]+$/;
        return usernameRegex.test(username);
    }

    /**
     * Kiểm tra độ mạnh mật khẩu
     */
    function checkPasswordStrength(password) {
        if (password.length === 0) {
            return;
        }

        let strength = 0;
        let strengthText = '';
        let strengthClass = '';

        // Kiểm tra độ dài
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;

        // Kiểm tra chứa số
        if (/\d/.test(password)) strength++;

        // Kiểm tra chứa chữ hoa
        if (/[A-Z]/.test(password)) strength++;

        // Kiểm tra chứa ký tự đặc biệt
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        // Đánh giá độ mạnh
        if (strength <= 2) {
            strengthText = 'Mật khẩu yếu';
            strengthClass = 'weak';
        } else if (strength <= 3) {
            strengthText = 'Mật khẩu trung bình';
            strengthClass = 'medium';
        } else {
            strengthText = 'Mật khẩu mạnh';
            strengthClass = 'strong';
        }

        // Hiển thị thông báo (có thể thêm UI indicator)
        console.log(`Password Strength: ${strengthText}`);
    }

    /**
     * Hiển thị thông báo
     */
    function showAlert(message, type) {
        alertBox.textContent = message;
        alertBox.className = `alert alert-${type} show`;

        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            clearAlert();
        }, 5000);
    }

    /**
     * Xóa thông báo
     */
    function clearAlert() {
        alertBox.classList.remove('show');
    }

    // Thêm hiệu ứng focus cho input
    const allInputs = document.querySelectorAll('.form-input');
    allInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Xử lý hiển thị/ẩn mật khẩu (có thể thêm nút toggle)
    // Toggle password visibility (nếu cần thêm icon mắt)
    
});
