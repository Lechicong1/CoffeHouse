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

function openVoucherModal() {
  // build modal if not exists
  let modal = document.getElementById("voucherModal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "voucherModal";
    modal.className = "modal";
    // use flex centering so modal-content appears centered and larger
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    modal.style.padding = "24px";
    modal.style.position = "fixed";
    modal.style.zIndex = 1300;
    modal.style.left = 0;
    modal.style.top = 0;
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.background = "rgba(0,0,0,0.4)";
    modal.innerHTML = `
      <div class="modal-content" style="width: min(880px, 92%); margin: 0; background: #fff; border-radius:8px; overflow:hidden;">
        <div style="padding:12px 16px; border-bottom:1px solid #eee; display:flex; align-items:center; gap:12px;">
          <h3 style="margin:0; font-size:1.1rem; flex:0 0 auto;">Chọn Voucher / Đổi Điểm</h3>
          <input id="voucherSearch" type="text" placeholder="Tìm voucher..." style="flex:1 1 auto; padding:8px 12px; border-radius:8px; border:1px solid #e6e6e6;">
          <button style="background:none;border:none;font-size:1.4rem;cursor:pointer;" onclick="closeVoucherModal()">&times;</button>
        </div>
        <div style="padding:12px;">
          <div id="voucherList" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px;max-height:520px;overflow:auto;padding-right:6px;"></div>
          <div style="display:flex; gap:8px; margin-top:12px; justify-content:flex-end;">
            <button class="btn" onclick="clearSelectedVoucher()">Bỏ chọn</button>
            <button id="voucherApplyBtn" class="btn btn-success" onclick="applySelectedVoucher()">Áp voucher</button>
          </div>
          <div id="voucherMessage" style="margin-top:12px;color:#c00;"></div>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  } else {
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
  }

  // fetch eligible vouchers using FormData POST (match project pattern)
  const fd = new FormData();
  const custId =
    window.currentOrder && window.currentOrder.customer_id
      ? window.currentOrder.customer_id
      : "";
  const subtotal = (window.cart || []).reduce((s, i) => s + i.price * i.qty, 0);
  const applyBtn = document.getElementById("voucherApplyBtn");
  fd.append("customer_id", custId);
  fd.append("bill_total", subtotal);

  // Fetch all vouchers (we will show all and mark eligibility)
  fetch("/COFFEE_PHP/Staff/getAllVouchers", { method: "POST", body: fd })
    .then((r) => r.json())
    .then((data) => {
      // store for client-side search/filter
      window.__pos_all_vouchers = Array.isArray(data.vouchers)
        ? data.vouchers
        : [];
      renderVouchers(window.__pos_all_vouchers, true, fd);

      // wire search input to filter displayed vouchers
      const searchInput = document.getElementById("voucherSearch");
      if (searchInput) {
        searchInput.addEventListener("input", function (e) {
          const q = String(e.target.value || "")
            .toLowerCase()
            .trim();
          if (!q) return renderVouchers(window.__pos_all_vouchers, true, fd);
          const filtered = window.__pos_all_vouchers.filter(
            (v) =>
              (v.name || "").toLowerCase().includes(q) ||
              (v.discount_type || "").toLowerCase().includes(q)
          );
          renderVouchers(filtered, true, fd);
        });
      }
    })
    .catch((err) => {
      console.error(err);
      const msg = document.getElementById("voucherMessage");
      if (msg) msg.textContent = "Lỗi khi lấy voucher";
    });

  // no show-all button anymore; modal already displays all vouchers and search is provided
}

function renderVouchers(vouchers, isAll, fd) {
  const list = document.getElementById("voucherList");
  const msg = document.getElementById("voucherMessage");
  list.innerHTML = "";
  msg.textContent = "";
  if (!Array.isArray(vouchers) || vouchers.length === 0) {
    msg.style.color = "#333";
    msg.textContent = "Không có voucher phù hợp";
    return;
  }

  const customerPoints =
    window.currentOrder && window.currentOrder.customer_points
      ? Number(window.currentOrder.customer_points)
      : 0;
  // Prefer the displayed subtotal (from #subtotal-price) which reflects current UI state;
  // fallback to computing from window.cart.
  const subtotalEl = document.getElementById("subtotal-price");
  const subtotalNow = subtotalEl
    ? parseVND(subtotalEl.textContent)
    : (window.cart || []).reduce((s, i) => s + i.price * i.qty, 0);

  vouchers.forEach((v) => {
    const el = document.createElement("div");
    el.className = "voucher-card";
    el.dataset.voucher = JSON.stringify(v);

    // determine eligibility (client-side hint)
    let eligible = true;
    let reason = "";
    if (!v.is_active || Number(v.is_active) !== 1) {
      eligible = false;
      reason = "Không hoạt động";
    }
    const today = new Date().toISOString().slice(0, 10);
    if (eligible && v.end_date && v.end_date < today) {
      eligible = false;
      reason = "Hết hạn";
    }
    if (
      eligible &&
      v.quantity !== null &&
      Number(v.quantity) <= Number(v.used_count)
    ) {
      eligible = false;
      reason = "Hết lượt";
    }
    if (eligible && subtotalNow < Number(v.min_bill_total || 0)) {
      eligible = false;
      reason = "Hóa đơn chưa đạt ngưỡng";
    }
    if (eligible && Number(v.point_cost || 0) > customerPoints) {
      eligible = false;
      reason = "Không đủ điểm";
    }

    // compute a human readable discount summary
    let discountSummary = "";
    if (v.discount_type === "FIXED") {
      discountSummary = formatCurrency(Number(v.discount_value || 0));
    } else {
      discountSummary = Number(v.discount_value || 0) + "%";
    }
    const maxDisc = v.max_discount_value
      ? formatCurrency(Number(v.max_discount_value))
      : null;
    const minBill = v.min_bill_total
      ? formatCurrency(Number(v.min_bill_total))
      : null;
    // estimate discount amount for current subtotal
    let estDiscount = 0;
    if (v.discount_type === "FIXED")
      estDiscount = Number(v.discount_value || 0);
    else
      estDiscount = Math.round(
        (Number(v.discount_value || 0) / 100) * subtotalNow
      );
    if (v.max_discount_value)
      estDiscount = Math.min(estDiscount, Number(v.max_discount_value));
    estDiscount = Math.min(estDiscount, subtotalNow);

    el.innerHTML = `
      <div class="v-left">
        <div class="v-name">${v.name}</div>
        <div class="v-meta">Giảm: <strong>${discountSummary}${
      maxDisc ? " (tối đa " + maxDisc + ")" : ""
    }</strong> — Ngưỡng: ${minBill || "Không có"}</div>
        <div class="v-note">Ước tính giảm hiện tại: <strong>${formatCurrency(
          estDiscount
        )}</strong> — Điểm: ${v.point_cost}</div>
      </div>
      <div class="v-actions">
        <button class="btn" ${eligible ? "" : "disabled"}>${
      eligible ? "Chọn" : "Không thể chọn"
    }</button>
      </div>
    `;

    if (!eligible) el.classList.add("disabled");
    if (!eligible) {
      const note = document.createElement("div");
      note.style.fontSize = "0.85rem";
      note.style.color = "#a33";
      note.style.marginTop = "6px";
      note.textContent = reason;
      el.querySelector("div").appendChild(note);
    }

    const btn = el.querySelector("button");
    const doSelect = (ev) => {
      if (ev) ev.stopPropagation();
      if (!eligible) {
        if (msg) {
          msg.style.color = "#c00";
          msg.textContent = "Voucher này không thể áp dụng: " + reason;
        }
        return;
      }
      if (window.__pos_selected_voucher_el)
        window.__pos_selected_voucher_el.classList.remove("selected");
      el.classList.add("selected");
      window.__pos_selected_voucher = v;
      window.__pos_selected_voucher_el = el;
    };
    btn.addEventListener("click", doSelect);
    el.addEventListener("click", doSelect);

    list.appendChild(el);
  });
}

function closeVoucherModal() {
  const modal = document.getElementById("voucherModal");
  if (modal) modal.style.display = "none";
}

function selectVoucher(id, points, voucherObj) {
  // store provisional selection in DOM attribute
  window.__pos_selected_voucher = voucherObj;
  const list = document.querySelectorAll("#voucherList .voucher-card");
  list.forEach((n) => (n.style.background = ""));
  // highlight isn't trivial without reference; rely on apply button to commit
}

function applySelectedVoucher() {
  const v = window.__pos_selected_voucher;
  const msg = document.getElementById("voucherMessage");
  if (!v) {
    if (msg) {
      msg.style.color = "#c00";
      msg.textContent = "Vui lòng chọn voucher trước khi áp.";
    }
    return;
  }

  // set into currentOrder following project's customer flow style
  window.currentOrder = window.currentOrder || {};
  window.currentOrder.voucher = v;
  // store point_cost as numeric
  window.currentOrder.voucher.point_cost = Number(
    window.currentOrder.voucher.point_cost || 0
  );

  // update UI text
  const sel = document.getElementById("pos-selected-voucher");
  if (sel)
    sel.textContent = `${v.name} — Giảm ${
      v.discount_type === "FIXED"
        ? v.discount_value + "₫"
        : v.discount_value + "%"
    } — ${v.point_cost} điểm`;

  // re-render totals
  if (typeof updateCartUI === "function") updateCartUI();

  closeVoucherModal();
}

function clearSelectedVoucher() {
  window.currentOrder = window.currentOrder || {};
  delete window.currentOrder.voucher;
  const sel = document.getElementById("pos-selected-voucher");
  if (sel) sel.textContent = "Không có voucher";
  if (typeof updateCartUI === "function") updateCartUI();
  closeVoucherModal();
}
