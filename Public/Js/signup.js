/* ============================================
   FILE: signup.js
   DESCRIPTION: JavaScript xử lý logic đăng ký
   FRAMEWORK: Vanilla JS (No Dependencies)
   ============================================ */

// Chờ DOM load xong
document.addEventListener("DOMContentLoaded", function () {
  // Lấy các phần tử DOM
  const signupForm = document.getElementById("signupForm");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirmPassword");

  /**
   * Validation client-side trước khi submit
   */
  signupForm.addEventListener("submit", function (e) {
    const fullname = document.getElementById("fullname").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const address = document.getElementById("address")
      ? document.getElementById("address").value.trim()
      : "";
    const username = document.getElementById("username").value.trim();
    const password = passwordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

    // Validate họ tên
    if (fullname.length < 2) {
      e.preventDefault();
      alert("Họ và tên phải có ít nhất 2 ký tự!");
      return false;
    }

    // Validate phone
    if (phone.length < 10 || phone.length > 11) {
      e.preventDefault();
      alert("Số điện thoại phải có 10-11 chữ số!");
      return false;
    }

    // Validate username
    if (username.length < 3) {
      e.preventDefault();
      alert("Tên đăng nhập phải có ít nhất 3 ký tự!");
      return false;
    }

    // Validate address length (optional)
    if (address.length > 255) {
      e.preventDefault();
      alert("Địa chỉ quá dài (tối đa 255 ký tự)");
      return false;
    }

    // Validate password
    if (password.length < 6) {
      e.preventDefault();
      alert("Mật khẩu phải có ít nhất 6 ký tự!");
      return false;
    }

    // Kiểm tra confirm password
    if (password !== confirmPassword) {
      e.preventDefault();
      alert("Mật khẩu xác nhận không khớp!");
      return false;
    }

    // Nếu OK, cho phép submit form (POST đến server)
    return true;
  });

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
