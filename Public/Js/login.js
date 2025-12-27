/* ============================================
   FILE: login.js
   DESCRIPTION: JavaScript xử lý logic đăng nhập
   FRAMEWORK: Vanilla JS (No Dependencies)
   ============================================ */

// Chờ DOM load xong
document.addEventListener("DOMContentLoaded", function () {
  // Lấy các phần tử DOM
  const loginForm = document.getElementById("loginForm");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");
  const rememberCheckbox = document.getElementById("remember");

  // Load thông tin đã lưu từ cookie (nếu có)
  loadSavedCredentials();

  /**
   * Validation client-side trước khi submit
   */
  loginForm.addEventListener("submit", function (e) {
    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    // Validate cơ bản
    if (!username || !password) {
      e.preventDefault();
      alert("Vui lòng nhập đầy đủ thông tin đăng nhập!");
      return false;
    }

    if (username.length < 3) {
      e.preventDefault();
      alert("Tên đăng nhập phải có ít nhất 3 ký tự!");
      return false;
    }

    // Nếu OK, cho phép submit form (POST đến server)
    return true;
  });

  /**
   * Load thông tin đã lưu từ Cookie
   */
  function loadSavedCredentials() {
    const savedUsername = getCookie("remember_username");
    if (savedUsername) {
      usernameInput.value = savedUsername;
      rememberCheckbox.checked = true;
    }
  }

  /**
   * Helper: Lấy cookie theo tên
   */
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
    return null;
  }

  // Thêm hiệu ứng focus cho input
  const inputs = document.querySelectorAll(".form-input");
  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("focused");
    });

    input.addEventListener("blur", function () {
      this.parentElement.classList.remove("focused");
    });
  });
});

/**
 * Hàm xử lý quên mật khẩu
 */
function handleForgotPassword() {
  alert("Chức năng quên mật khẩu đang được phát triển!");
}

/**
 * Hàm chuyển đến trang đăng ký
 */
function handleRegister() {
  window.location.href = "/COFFEE_PHP/Auth/showSignup";
}

/**
 * Submit form with selected user type (customer | employee)
 * @param {string} type
 */
function loginAs(type) {
  const form = document.getElementById("loginForm");
  const userTypeInput = document.getElementById("userType");
  if (userTypeInput) userTypeInput.value = type;
  // trigger form submit
  form.submit();
}
