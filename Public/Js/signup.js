document.addEventListener("DOMContentLoaded", function () {
  const signupForm = document.getElementById("signupForm");

  signupForm.addEventListener("submit", function (e) {
    const getValue = (id) => (document.getElementById(id)?.value || "").trim();
    
    const fullname = getValue("fullname");
    const phone = getValue("phone");
    const address = getValue("address");
    const username = getValue("username");
    const password = getValue("password");
    const confirmPassword = getValue("confirmPassword");

    const showError = (msg) => {
      e.preventDefault();
      alert(msg);
      return false;
    };

    if (fullname.length < 2) return showError("Họ và tên phải có ít nhất 2 ký tự!");
    if (phone.length < 10 || phone.length > 11) return showError("Số điện thoại phải có 10-11 chữ số!");
    if (username.length < 3) return showError("Tên đăng nhập phải có ít nhất 3 ký tự!");
    if (address.length > 255) return showError("Địa chỉ quá dài (tối đa 255 ký tự)");
    if (password.length < 6) return showError("Mật khẩu phải có ít nhất 6 ký tự!");
    if (password !== confirmPassword) return showError("Mật khẩu xác nhận không khớp!");

    return true;
  });

  document.querySelectorAll(".form-input").forEach((input) => {
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("focused");
    });
    input.addEventListener("blur", function () {
      this.parentElement.classList.remove("focused");
    });
  });
});
