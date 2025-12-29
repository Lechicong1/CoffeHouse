// POS customer helper
document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("open-customer-modal");
  if (btn) btn.addEventListener("click", openPosCustomerModal);
});

function openPosCustomerModal() {
  document.getElementById("posCustomerModal").style.display = "block";
  document.getElementById("posCustomerMessage").textContent = "";
}

function closePosCustomerModal() {
  document.getElementById("posCustomerModal").style.display = "none";
}

function posFindCustomer() {
  const phone = document.getElementById("posPhone").value.trim();
  if (!phone) {
    document.getElementById("posCustomerMessage").textContent =
      "Vui lòng nhập số điện thoại";
    return;
  }
  const findBtn = document.getElementById("posFindBtn");
  if (findBtn) {
    findBtn.disabled = true;
    findBtn.textContent = "Đang tìm...";
  }

  const fd = new FormData();
  fd.append("phone", phone);

  fetch("/COFFEE_PHP/Staff/searchCustomer", { method: "POST", body: fd })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        const c = data.customer;
        document.getElementById("posCustomerMessage").style.color = "#080";
        document.getElementById(
          "posCustomerMessage"
        ).textContent = `Đã tìm: ${c.full_name} — ${c.phone} — ${c.points} điểm`;
      } else {
        document.getElementById("posCustomerMessage").style.color = "#c00";
        document.getElementById("posCustomerMessage").textContent =
          "Không tìm thấy khách với số này";
      }
    })
    .catch((err) => {
      console.error(err);
      document.getElementById("posCustomerMessage").textContent = "Lỗi server";
    })
    .finally(() => {
      if (findBtn) {
        findBtn.disabled = false;
        findBtn.textContent = "Tìm";
      }
    });
}

function posCreateOrUseCustomer() {
  const phone = document.getElementById("posPhone").value.trim();
  if (!phone) {
    document.getElementById("posCustomerMessage").textContent =
      "Số điện thoại bắt buộc";
    return;
  }
  const fullname =
    document.getElementById("posFullName").value.trim() || "Khách lẻ";
  const email = document.getElementById("posEmail").value.trim() || "";

  const fd = new FormData();
  fd.append("phone", phone);
  fd.append("fullname", fullname);
  fd.append("email", email);
  // Optionally pointsToAdd can be passed; default 0
  fd.append("pointsToAdd", 0);
  const createBtn = document.getElementById("posCreateBtn");
  if (createBtn) {
    createBtn.disabled = true;
    createBtn.textContent = "Đang xử lý...";
  }

  fetch("/COFFEE_PHP/Staff/posUpsertCustomer", { method: "POST", body: fd })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        const c = data.customer;
        // update POS UI: top input shows name + phone, small box shows only points
        const nameInput = document.getElementById("pos-customer-name");
        const sel = document.getElementById("pos-selected-customer");
        if (nameInput)
          nameInput.value = `${c.full_name || "Khách"} — ${c.phone || ""}`;
        if (sel) sel.textContent = `${c.points || 0} điểm`;
        // store selected customer in currentOrder state
        window.currentOrder = window.currentOrder || {};
        window.currentOrder.customer = c;
        window.currentOrder.customer_id = c.id;
        window.currentOrder.customer_points = c.points;

        document.getElementById("posCustomerMessage").style.color = "#080";
        document.getElementById("posCustomerMessage").textContent = data.created
          ? "Tạo khách thành công"
          : "Sử dụng khách sẵn có";
        // close modal immediately for faster flow
        closePosCustomerModal();
        // focus back to search input for quick next entry
        const phoneInput = document.getElementById("search-input");
        if (phoneInput) phoneInput.focus();
      } else {
        document.getElementById("posCustomerMessage").style.color = "#c00";
        document.getElementById("posCustomerMessage").textContent =
          data.message || "Lỗi";
      }
    })
    .catch((err) => {
      console.error(err);
      document.getElementById("posCustomerMessage").textContent = "Lỗi kết nối";
    })
    .finally(() => {
      if (createBtn) {
        createBtn.disabled = false;
        createBtn.textContent = "Tạo / Dùng";
      }
    });
}
