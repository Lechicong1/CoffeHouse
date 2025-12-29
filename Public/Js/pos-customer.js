/*
 * Module POS - Quản lý Khách Hàng (pos-customer.js)
 * - File này xử lý modal tìm / tạo khách cho giao diện POS
 * - Kiểm tra request trả về kiểu JSON hoặc HTML (server trả view)
 * - Nếu server redirect về trang POS có biến JavaScript `CUSTOMER_SEARCH_RESULT`,
 *   hàm sẽ trích dữ liệu đó và cập nhật giao diện.
 *
 * Ghi chú: tất cả chú thích trong file đã được viết bằng tiếng Việt để dễ hiểu.
 */
(function () {
  // Module POS - quản lý khách hàng (theo mẫu voucher-web.js)
  // parseCustomerFromHtml: cố gắng trích `CUSTOMER_SEARCH_RESULT` từ HTML trả về
  // Nếu tìm thấy sẽ parse JSON và trả về object customer, không tìm thấy trả về null
  function parseCustomerFromHtml(htmlText) {
    try {
      const re =
        /const\s+CUSTOMER_SEARCH_RESULT\s*=\s*(null|\{[\s\S]*?\}|\[[\s\S]*?\])\s*;/m;
      const m = htmlText.match(re);
      if (m && m[1]) {
        const jsonText = m[1];
        if (jsonText === "null") return null;
        return JSON.parse(jsonText);
      }
    } catch (e) {
      console.warn("parseCustomerFromHtml failed", e);
    }
    return null;
  }

  // formatMsg: set text + color cho phần tử hiển thị thông báo trong modal
  function formatMsg(el, text, color) {
    if (!el) return;
    el.style.color = color || "";
    el.textContent = text || "";
  }

  // doFetchAsJsonOrHtml: fetch POST và trả về JSON hoặc object {_html: '...'} khi server trả HTML
  // Nó thêm header `X-Requested-With` và `Accept` để server có thể nhận biết request AJAX
  async function doFetchAsJsonOrHtml(url, fd) {
    const opts = {
      method: "POST",
      body: fd,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json, text/html",
      },
    };
    const r = await fetch(url, opts);
    const ct = r.headers.get("content-type") || "";
    if (!r.ok) {
      const text = await r.text();
      throw new Error(
        `HTTP ${r.status}: ${ct.includes("text/html") ? "HTML" : text}`
      );
    }
    if (ct.includes("application/json")) return await r.json();
    // if HTML, return object with _html for caller to inspect
    if (ct.includes("text/html")) {
      const html = await r.text();
      return { _html: html };
    }
    const text = await r.text();
    try {
      return JSON.parse(text);
    } catch (e) {
      return { _text: text };
    }
  }

  // updateUiWithCustomer: cập nhật giao diện POS khi đã có dữ liệu khách
  // - cập nhật input tên, box hiển thị điểm, global `window.currentOrder` và `window.selectedCustomer`
  function updateUiWithCustomer(c) {
    if (!c) return;
    const nameInput = document.getElementById("pos-customer-name");
    const sel = document.getElementById("pos-selected-customer");
    if (nameInput)
      nameInput.value = `${c.full_name || "Khách"} — ${c.phone || ""}`;
    if (sel) sel.textContent = `${c.points || 0} điểm`;
    window.currentOrder = window.currentOrder || {};
    window.currentOrder.customer = c;
    window.currentOrder.customer_id = c.id;
    window.currentOrder.customer_points = c.points;
    // Backward-compatible global and hidden form
    window.selectedCustomer = c;
    const hiddenCustomerInput = document.getElementById("form-customer-id");
    if (hiddenCustomerInput) hiddenCustomerInput.value = c.id;
  }

  // posFindCustomer: gửi request tìm khách theo số điện thoại, cập nhật UI theo kết quả
  async function posFindCustomer() {
    const phoneEl = document.getElementById("posPhone");
    const phone = phoneEl ? phoneEl.value.trim() : "";
    const msgEl = document.getElementById("posCustomerMessage");
    if (!phone) {
      formatMsg(msgEl, "Vui lòng nhập số điện thoại", "#c00");
      return;
    }
    const findBtn = document.getElementById("posFindBtn");
    if (findBtn) {
      findBtn.disabled = true;
      findBtn.textContent = "Đang tìm...";
    }

    const fd = new FormData();
    fd.append("phone", phone);

    try {
      const data = await doFetchAsJsonOrHtml(
        "/COFFEE_PHP/Staff/searchCustomer",
        fd
      );
      if (data && data.success) {
        updateUiWithCustomer(data.customer);
        formatMsg(
          msgEl,
          `Đã tìm: ${data.customer.full_name} — ${data.customer.phone} — ${data.customer.points} điểm`,
          "#080"
        );
      } else if (data && data._html) {
        // page returned full HTML (redirect to GetData). try extract customer var
        const c = parseCustomerFromHtml(data._html);
        if (c) {
          updateUiWithCustomer(c);
          formatMsg(
            msgEl,
            `Đã tìm: ${c.full_name} — ${c.phone} — ${c.points} điểm`,
            "#080"
          );
          closePosCustomerModal();
        } else {
          formatMsg(
            msgEl,
            "Server trả HTML. Vui lòng kiểm tra phiên hoặc đăng nhập lại.",
            "#c00"
          );
        }
      } else if (data && data._text) {
        formatMsg(msgEl, "Không nhận được JSON - kiểm tra server", "#c00");
      } else {
        formatMsg(
          msgEl,
          data && data.message
            ? data.message
            : "Không tìm thấy khách với số này",
          "#c00"
        );
      }
    } catch (err) {
      console.error("posFindCustomer error:", err);
      formatMsg(msgEl, err.message || "Lỗi server", "#c00");
    } finally {
      if (findBtn) {
        findBtn.disabled = false;
        findBtn.textContent = "Tìm";
      }
    }
  }

  // posCreateOrUseCustomer: tạo khách mới hoặc lấy khách có sẵn (POS upsert)
  // Gọi endpoint `/Staff/posUpsertCustomer` và xử lý JSON hoặc HTML trả về
  async function posCreateOrUseCustomer() {
    const phoneElUp = document.getElementById("posPhoneUpsert");
    const phoneEl = phoneElUp || document.getElementById("posPhone");
    const phone = phoneEl ? phoneEl.value.trim() : "";
    const msgEl = document.getElementById("posCustomerMessage");
    if (!phone) {
      formatMsg(msgEl, "Số điện thoại bắt buộc", "#c00");
      return;
    }
    const fullnameEl = document.getElementById("posFullName");
    const emailEl = document.getElementById("posEmail");
    const fullname = (fullnameEl && fullnameEl.value.trim()) || "Khách lẻ";
    const email = (emailEl && emailEl.value.trim()) || "";

    const fd = new FormData();
    fd.append("phone", phone);
    fd.append("fullname", fullname);
    fd.append("email", email);
    fd.append("pointsToAdd", 0);

    const createBtn = document.getElementById("posCreateBtn");
    if (createBtn) {
      createBtn.disabled = true;
      createBtn.textContent = "Đang xử lý...";
    }

    try {
      const data = await doFetchAsJsonOrHtml(
        "/COFFEE_PHP/Staff/posUpsertCustomer",
        fd
      );
      if (data && data.success) {
        updateUiWithCustomer(data.customer);
        formatMsg(
          msgEl,
          data.created ? "Tạo khách thành công" : "Sử dụng khách sẵn có",
          "#080"
        );
        closePosCustomerModal();
      } else if (data && data._html) {
        const c = parseCustomerFromHtml(data._html);
        if (c) {
          updateUiWithCustomer(c);
          formatMsg(msgEl, "Tạo/Dùng khách thành công", "#080");
          closePosCustomerModal();
        } else {
          formatMsg(
            msgEl,
            "Server trả HTML. Vui lòng kiểm tra phiên hoặc đăng nhập lại.",
            "#c00"
          );
        }
      } else if (data && data._text) {
        formatMsg(msgEl, "Không nhận được JSON - kiểm tra server", "#c00");
      } else {
        formatMsg(msgEl, data && data.message ? data.message : "Lỗi", "#c00");
      }
    } catch (err) {
      console.error("posCreateOrUseCustomer error:", err);
      formatMsg(msgEl, err.message || "Lỗi kết nối", "#c00");
    } finally {
      if (createBtn) {
        createBtn.disabled = false;
        createBtn.textContent = "Tạo / Dùng";
      }
    }
  }

  // Mở modal chọn/tạo khách và reset message
  function openPosCustomerModal() {
    const m = document.getElementById("posCustomerModal");
    if (m) m.style.display = "block";
    const msgEl = document.getElementById("posCustomerMessage");
    if (msgEl) {
      msgEl.textContent = "";
      msgEl.style.color = "";
    }
  }

  // Đóng modal chọn khách
  function closePosCustomerModal() {
    const m = document.getElementById("posCustomerModal");
    if (m) m.style.display = "none";
  }

  function init() {
    // attach open modal
    const openBtn = document.getElementById("open-customer-modal");
    if (openBtn) openBtn.addEventListener("click", openPosCustomerModal);

    // attach find/create buttons if exist
    const findBtn = document.getElementById("posFindBtn");
    if (findBtn) findBtn.addEventListener("click", posFindCustomer);
    const createBtn = document.getElementById("posCreateBtn");
    if (createBtn) createBtn.addEventListener("click", posCreateOrUseCustomer);

    // close modal when clicking outside content (optional UX)
    const modal = document.getElementById("posCustomerModal");
    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) closePosCustomerModal();
      });
    }
  }

  if (document.readyState === "loading")
    document.addEventListener("DOMContentLoaded", init);
  else init();

  // expose for manual use
  window.openPosCustomerModal = openPosCustomerModal;
  window.closePosCustomerModal = closePosCustomerModal;
  window.posFindCustomer = posFindCustomer;
  window.posCreateOrUseCustomer = posCreateOrUseCustomer;
})();

// POS customer helper
document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("open-customer-modal");
  if (btn) btn.addEventListener("click", openPosCustomerModal);
});

// Helper: try extract CUSTOMER_SEARCH_RESULT from returned HTML page
function parseCustomerFromHtml(htmlText) {
  try {
    const re =
      /const\s+CUSTOMER_SEARCH_RESULT\s*=\s*(null|\{[\s\S]*?\}|\[[\s\S]*?\])\s*;/m;
    const m = htmlText.match(re);
    if (m && m[1]) {
      const jsonText = m[1];
      if (jsonText === "null") return null;
      return JSON.parse(jsonText);
    }
  } catch (e) {
    console.warn("parseCustomerFromHtml failed", e);
  }
  return null;
}

function openPosCustomerModal() {
  document.getElementById("posCustomerModal").style.display = "block";
  const msgEl = document.getElementById("posCustomerMessage");
  if (msgEl) {
    msgEl.textContent = "";
    msgEl.style.color = "";
  }
}
