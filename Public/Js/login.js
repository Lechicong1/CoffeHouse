/* ============================================
   FILE: login.js
   DESCRIPTION: JavaScript xử lý logic đăng nhập
   ============================================ */

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const rememberCheckbox = document.getElementById("remember");
    const togglePasswordBtn = document.getElementById("togglePassword");

    // Load thông tin đã lưu
    loadSavedCredentials();

    // --- 1. Xử lý Ẩn/Hiện Mật Khẩu ---
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener("click", function () {
            // Kiểm tra type hiện tại
            const type =
                passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);

            // Đổi icon (Mắt mở / Mắt đóng)
            const icon = this.querySelector("i");
            if (type === "text") {
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });
    }

    // --- 2. Validation khi Submit ---
    // Lưu ý: Hàm này được gọi khi form submit (nhấn Enter).
    // Nút bấm onclick="loginAs" ở dưới cũng sẽ trigger submit form này.
    loginForm.addEventListener("submit", function (e) {
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        // Reset thông báo lỗi hệ thống (nếu có)
        // (Ở đây dùng alert nên không cần reset UI text)

        // Validate rỗng
        if (!username || !password) {
            e.preventDefault();
            alert("Vui lòng nhập đầy đủ thông tin đăng nhập!");
            return false;
        }

        // Validate Username >= 3 ký tự
        if (username.length < 3) {
            e.preventDefault();
            alert("Tên đăng nhập phải có ít nhất 3 ký tự!");
            usernameInput.focus();
            return false;
        }

        // Validate Password >= 6 ký tự
        if (password.length < 6) {
            e.preventDefault();
            alert("Mật khẩu phải có ít nhất 6 ký tự!");

            // XÓA MẬT KHẨU ĐANG NHẬP
            passwordInput.value = "";
            passwordInput.focus();

            return false;
        }

        // Nếu mọi thứ OK -> Form sẽ được gửi đi
        return true;
    });

    // --- Các hàm Helper cũ giữ nguyên ---
    function loadSavedCredentials() {
        const savedUsername = getCookie("remember_username");
        if (savedUsername) {
            usernameInput.value = savedUsername;
            rememberCheckbox.checked = true;
        }
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(";").shift();
        return null;
    }

    // Effect Focus
    const inputs = document.querySelectorAll(".form-input");
    inputs.forEach((input) => {
        input.addEventListener("focus", function () {
            // Tìm parent form-group hoặc password-wrapper
            const parent = this.closest(".form-group") || this.parentElement;
            if(parent) parent.classList.add("focused");
        });

        input.addEventListener("blur", function () {
            const parent = this.closest(".form-group") || this.parentElement;
            if(parent) parent.classList.remove("focused");
        });
    });
});

// --- Các hàm Global gọi từ HTML ---

function handleRegister() {
    window.location.href = "/COFFEE_PHP/Auth/showSignup";
}

function loginAs(type) {
    const form = document.getElementById("loginForm");
    const userTypeInput = document.getElementById("userType");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");

    if (userTypeInput) userTypeInput.value = type;

    // Gọi validation thủ công trước khi submit
    // Vì form.submit() bằng JS sẽ BỎ QUA event listener 'submit' ở trên
    // Nên ta phải copy logic validate vào đây hoặc trigger click button submit ẩn.

    // Cách tốt nhất: Gọi logic validate tại đây
    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    if (!username || !password) {
        alert("Vui lòng nhập đầy đủ thông tin đăng nhập!");
        return;
    }

    if (username.length < 3) {
        alert("Tên đăng nhập phải có ít nhất 3 ký tự!");
        usernameInput.focus();
        return;
    }

    if (password.length < 6) {
        alert("Mật khẩu phải có ít nhất 6 ký tự!");
        passwordInput.value = ""; // Xóa mật khẩu sai
        passwordInput.focus();
        return;
    }

    // Nếu OK mới submit
    form.submit();
}