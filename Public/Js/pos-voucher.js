// POS voucher helper (follows project FormData/post pattern)
document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("open-voucher-modal");
  if (btn) btn.addEventListener("click", openVoucherModal);
});

// Parse a formatted VND string like "53.000 ₫" -> 53000
function parseVND(text) {
  if (!text) return 0;
  // remove non-digits
  const n = String(text).replace(/[^0-9\-]+/g, "");
  return Number(n) || 0;
}

// POS voucher MVP (clean, single definition)
document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("open-voucher-modal");
  if (btn) btn.addEventListener("click", openVoucherModal);
});

function openVoucherModal() {
  if (!window.currentOrder || !window.currentOrder.customer_id) {
    alert("Vui lòng chọn khách hàng trước.");
    return;
  }

  let modal = document.getElementById("voucherModal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "voucherModal";
    modal.style.position = "fixed";
    modal.style.left = 0;
    modal.style.top = 0;
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    modal.style.background = "rgba(0,0,0,0.4)";
    modal.innerHTML = `
      <div style="background:#fff;padding:20px;border-radius:16px;min-width:600px;max-width:600px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <strong style="font-size:20px;color:#333;">Chọn Voucher</strong>
          <button id="closeVoucherBtn" type="button" style="background:none;border:none;font-size:28px;cursor:pointer;color:#999;line-height:1;">×</button>
        </div>
        <div id="voucherList" style="max-height:520px;overflow:auto;"></div>
        <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
          <button id="clearVoucherBtn" class="btn" style="padding:10px 20px;border-radius:8px;border:1px solid #ddd;background:#fff;cursor:pointer;">Bỏ chọn</button>
          <button id="applyVoucherBtn" class="btn btn-success" style="padding:10px 20px;border-radius:8px;border:none;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:white;cursor:pointer;font-weight:600;">Áp voucher</button>
        </div>
        <div id="voucherMsg" style="color:#c00;margin-top:8px;"></div>
      </div>
    `;
    document.body.appendChild(modal);
    document
      .getElementById("closeVoucherBtn")
      .addEventListener("click", closeVoucherModal);
    document
      .getElementById("clearVoucherBtn")
      .addEventListener("click", clearSelectedVoucher);
    document
      .getElementById("applyVoucherBtn")
      .addEventListener("click", applySelectedVoucher);
  } else {
    modal.style.display = "flex";
  }

  const fd = new FormData();
  fd.append("customer_id", window.currentOrder.customer_id);
  let subtotal = 0;
  const subtotalEl = document.getElementById("subtotal-price");
  if (subtotalEl && subtotalEl.textContent) {
    subtotal =
      Number(String(subtotalEl.textContent).replace(/[^0-9\-]+/g, "")) || 0;
  } else {
    subtotal = (window.cart || []).reduce(
      (s, i) => s + Number(i.price || 0) * Number(i.qty || 0),
      0
    );
  }
  fd.append("bill_total", subtotal);

  fetch("/COFFEE_PHP/Staff/getEligibleVouchers", { method: "POST", body: fd })
    .then((r) => r.text())
    .then((html) => {
      const list = document.getElementById("voucherList");
      const msg = document.getElementById("voucherMsg");
      if (!list) return;

      // Backend trả về HTML, không phải JSON
      // Chèn trực tiếp HTML vào list
      list.innerHTML = html || '<div style="color:#333">Không có voucher phù hợp</div>';
      
      // Clear message
      if (msg) msg.textContent = "";
      
      // Wire up click events cho các voucher cards
      wireVoucherCardClicks();
    })
    .catch((err) => {
      console.error(err);
      const msg = document.getElementById("voucherMsg");
      if (msg) msg.textContent = "Lỗi khi lấy voucher";
    });
}

function wireVoucherCardClicks() {
  const list = document.getElementById("voucherList");
  if (!list) return;
  const cards = list.querySelectorAll(".voucher-card");
  cards.forEach((card) => {
    card.style.cursor = "pointer";
    card.addEventListener("click", () => {
      const id =
        card.getAttribute("data-id") ||
        card.getAttribute("data-voucher-id") ||
        card.dataset.id ||
        null;
      const name =
        card.getAttribute("data-name") ||
        card.dataset.name ||
        (card.textContent || "").trim();
      const point_cost = Number(
        card.getAttribute("data-point-cost") || card.dataset.pointCost || 0
      );
      const discount_type =
        card.getAttribute("data-discount-type") ||
        card.dataset.discountType ||
        "";
      const discount_value =
        card.getAttribute("data-discount-value") ||
        card.dataset.discountValue ||
        "";
      const v = {
        id: id,
        voucher_id: id,
        name: name,
        point_cost: point_cost,
        discount_type: discount_type,
        discount_value: discount_value,
      };
      window.__pos_selected_voucher = v;
      cards.forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
    });
  });
}

function selectCardFromObject(v, el) {
  window.__pos_selected_voucher = v;
  const siblings = el.parentElement
    ? el.parentElement.querySelectorAll(".voucher-card")
    : [];
  siblings.forEach((s) => s.classList.remove("selected"));
  el.classList.add("selected");
}

function closeVoucherModal() {
  const m = document.getElementById("voucherModal");
  if (m) m.style.display = "none";
}

function applySelectedVoucher() {
  const v = window.__pos_selected_voucher;
  const msg = document.getElementById("voucherMsg");
  if (!v) {
    if (msg) msg.textContent = "Vui lòng chọn voucher";
    return;
  }
  window.currentOrder = window.currentOrder || {};
  window.currentOrder.voucher = v;
  const vid = v.voucher_id || v.id || v.VoucherId || v.voucherId || null;
  if (vid) window.currentOrder.voucher.voucher_id = vid;
  window.currentOrder.voucher.point_cost = Number(
    window.currentOrder.voucher.point_cost || 0
  );
  const sel = document.getElementById("pos-selected-voucher");
  if (sel)
    sel.textContent = `${v.name || ""} — ${
      v.discount_type === "FIXED"
        ? (v.discount_value || 0) + "₫"
        : (v.discount_value || 0) + "%"
    } — ${v.point_cost || 0} điểm`;
  if (typeof updateCartUI === "function") updateCartUI();
  closeVoucherModal();
}

function clearSelectedVoucher() {
  if (window.currentOrder) delete window.currentOrder.voucher;
  const sel = document.getElementById("pos-selected-voucher");
  if (sel) sel.textContent = "Không có voucher";
  if (typeof updateCartUI === "function") updateCartUI();
  closeVoucherModal();
}
