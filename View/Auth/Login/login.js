/* ============================================
   FILE: login.js
   DESCRIPTION: JavaScript xử lý logic đăng nhập
   FRAMEWORK: Vanilla JS (No Dependencies)
   ============================================ */

// Chờ DOM load xong
document.addEventListener('DOMContentLoaded', function() {

    // Lấy các phần tử DOM
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');
    const alertBox = document.getElementById('alertBox');

    // Load thông tin đã lưu (nếu có)
    loadSavedCredentials();

    // Xử lý sự kiện submit form
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        handleLogin();
    });

    // Xử lý sự kiện input (xóa lỗi khi user nhập lại)
    usernameInput.addEventListener('input', clearAlert);
    passwordInput.addEventListener('input', clearAlert);

    /**
     * Hàm xử lý đăng nhập
     */
    function handleLogin() {
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();
        const remember = rememberCheckbox.checked;

        // Validate dữ liệu
        if (!username || !password) {
            showAlert('Vui lòng nhập đầy đủ thông tin đăng nhập', 'error');
            return;
        }

        if (username.length < 3) {
            showAlert('Tên đăng nhập phải có ít nhất 3 ký tự', 'error');
            return;
        }

        
        // ==================== HARD-CODED CREDENTIALS ====================
        // Danh sách tài khoản hard-coded
        const accounts = {
            admin: {
                username: 'admin',
                password: 'admin',
                role: 'admin',
                dashboard: '../../AdminDashBoard/admin.html'
            },
            employee: {
                username: 'employee',
                password: 'employee',
                role: 'employee',
                dashboard: '../../EmployeeDashBoard/staff.html'
            },
            shipper: {
                username: 'shipper',
                password: 'shipper',
                role: 'shipper',
                dashboard: '../../ShipperDashBoard/shipperdemo.html'
            },
            user: {
                username: 'user',
                password: 'user123',
                role: 'user',
                dashboard: '../../UserDashBoard/index.html'
            }
        };

        // Kiểm tra tài khoản
        let foundAccount = null;
        for (let key in accounts) {
            if (accounts[key].username === username && accounts[key].password === password) {
                foundAccount = accounts[key];
                break;
            }
        }

        // Nếu tài khoản không đúng
        if (!foundAccount) {
            showAlert('Tên đăng nhập hoặc mật khẩu không chính xác!', 'error');
            passwordInput.value = ''; // Xóa mật khẩu
            passwordInput.focus();
            return;
        }

        // Lưu thông tin nếu chọn "Ghi nhớ"
        if (remember) {
            saveCredentials(username);
        } else {
            clearSavedCredentials();
        }

        // Lưu thông tin user vào localStorage
        localStorage.setItem('currentUser', JSON.stringify({
            username: foundAccount.username,
            role: foundAccount.role
        }));

        // Hiển thị thông báo thành công
        showAlert(`Đăng nhập thành công với vai trò ${foundAccount.role.toUpperCase()}! Đang chuyển hướng...`, 'success');

        // Chuyển hướng đến dashboard tương ứng
        setTimeout(() => {
            console.log('Login Data:', { 
                username: foundAccount.username, 
                role: foundAccount.role,
                dashboard: foundAccount.dashboard
            });
            window.location.href = foundAccount.dashboard;
        }, 1500);
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

    /**
     * Lưu thông tin đăng nhập vào LocalStorage
     */
    function saveCredentials(username) {
        localStorage.setItem('savedUsername', username);
        localStorage.setItem('rememberMe', 'true');
    }

    /**
     * Load thông tin đã lưu từ LocalStorage
     */
    function loadSavedCredentials() {
        const savedUsername = localStorage.getItem('savedUsername');
        const rememberMe = localStorage.getItem('rememberMe');

        if (rememberMe === 'true' && savedUsername) {
            usernameInput.value = savedUsername;
            rememberCheckbox.checked = true;
        }
    }

    /**
     * Xóa thông tin đã lưu
     */
    function clearSavedCredentials() {
        localStorage.removeItem('savedUsername');
        localStorage.removeItem('rememberMe');
    }

    // Thêm hiệu ứng focus cho input
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

});

/**
 * Hàm xử lý quên mật khẩu
 */
function handleForgotPassword() {
    alert('Chức năng quên mật khẩu đang được phát triển!');
    // Thêm logic xử lý quên mật khẩu ở đây
}

/**
 * Hàm chuyển đến trang đăng ký
 */
function handleRegister() {
    window.location.href = '../Register/register.html';
}

