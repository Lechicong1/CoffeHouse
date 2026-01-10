document.addEventListener("DOMContentLoaded", function () {
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
    modal.className = "pos-voucher-modal";
    modal.innerHTML = `
      <div class="pos-voucher-modal-content">
        <div class="pos-voucher-modal-header">
          <strong class="pos-voucher-modal-title">Chọn Voucher</strong>
          <button id="closeVoucherBtn" type="button" class="pos-voucher-modal-close">×</button>
        </div>
        <div id="voucherList" class="pos-voucher-list"></div>
        <div class="pos-voucher-actions">
          <button id="clearVoucherBtn" class="btn">Bỏ chọn</button>
          <button id="applyVoucherBtn" class="btn btn-success">Áp voucher</button>
        </div>
        <div id="voucherMsg" class="pos-voucher-msg"></div>
      </div>
    `;
    document.body.appendChild(modal);
    document.getElementById("closeVoucherBtn").addEventListener("click", closeVoucherModal);
    document.getElementById("clearVoucherBtn").addEventListener("click", clearSelectedVoucher);
    document.getElementById("applyVoucherBtn").addEventListener("click", applySelectedVoucher);
  }
  // Luôn hiển thị modal
  modal.style.display = "flex";

  const fd = new FormData();
  fd.append("customer_id", window.currentOrder.customer_id);
  
  let subtotal = 0;
  const subtotalEl = document.getElementById("subtotal-price");
  if (subtotalEl && subtotalEl.textContent) {
    subtotal = parseVND(subtotalEl.textContent);
  } else {
    subtotal = (window.cart || []).reduce(
      (s, i) => s + Number(i.price || 0) * Number(i.qty || 0),
      0
    );
  }
  fd.append("bill_total", subtotal);

  fetch("/COFFEE_PHP/Voucher/getEligibleVouchers", { method: "POST", body: fd })
    .then((r) => r.text())
    .then((html) => {
      const list = document.getElementById("voucherList");
      const msg = document.getElementById("voucherMsg");
      if (!list) return;
      list.innerHTML = html || '<div style="color:#333">Không có voucher phù hợp</div>';
      if (msg) msg.textContent = "";
      wireVoucherCardClicks();
    })
    .catch((err) => {
      console.error("Fetch error:", err);
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
      const id = card.dataset.id || card.getAttribute("data-id");
      const name = card.dataset.name || card.getAttribute("data-name") || card.textContent.trim();
      const point_cost = Number(card.dataset.pointCost || card.getAttribute("data-point-cost") || 0);
      const discount_type = card.dataset.discountType || card.getAttribute("data-discount-type") || "";
      const discount_value = card.dataset.discountValue || card.getAttribute("data-discount-value") || "";
      
      // Lưu vào DOM thay vì global variable
      const modal = document.getElementById("voucherModal");
      if (modal) {
        modal.dataset.selectedId = id;
        modal.dataset.selectedName = name;
        modal.dataset.selectedPointCost = point_cost;
        modal.dataset.selectedDiscountType = discount_type;
        modal.dataset.selectedDiscountValue = discount_value;
      }
      
      cards.forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
    });
  });
}

function closeVoucherModal() {
  const m = document.getElementById("voucherModal");
  if (m) m.style.display = "none";
}

function applySelectedVoucher() {
  const modal = document.getElementById("voucherModal");
  const msg = document.getElementById("voucherMsg");
  
  if (!modal || !modal.dataset.selectedId) {
    if (msg) msg.textContent = "Vui lòng chọn voucher";
    return;
  }
  
  const v = {
    id: modal.dataset.selectedId,
    voucher_id: modal.dataset.selectedId,
    name: modal.dataset.selectedName,
    point_cost: Number(modal.dataset.selectedPointCost || 0),
    discount_type: modal.dataset.selectedDiscountType,
    discount_value: modal.dataset.selectedDiscountValue
  };
  
  window.currentOrder = window.currentOrder || {};
  window.currentOrder.voucher = v;
  
  const voucherInput = document.getElementById("form-voucher-id");
  if (voucherInput) voucherInput.value = String(v.voucher_id || "");
  
  const sel = document.getElementById("pos-selected-voucher");
  if (sel) sel.textContent = `${v.name || ""} — ${v.point_cost || 0} điểm`;
  
  if (v.voucher_id) {
    previewVoucherServer(v.voucher_id);
  }
  
  if (typeof updateCartUI === "function") updateCartUI();
  closeVoucherModal();
}

function clearSelectedVoucher() {
  if (window.currentOrder) delete window.currentOrder.voucher;
  
  const voucherInput = document.getElementById("form-voucher-id");
  if (voucherInput) voucherInput.value = "";
  
  const sel = document.getElementById("pos-selected-voucher");
  if (sel) sel.textContent = "Không có voucher";
  
  const discountRow = document.getElementById("discount-row");
  if (discountRow) discountRow.style.display = "none";
  
  const subtotalEl = document.getElementById("subtotal-price");
  const totalEl = document.getElementById("total-price");
  const btnTotalEl = document.getElementById("btn-total");
  const modalTotalEl = document.getElementById("modal-total");
  
  if (subtotalEl && totalEl) {
    const subtotal = parseVND(subtotalEl.textContent);
    totalEl.textContent = formatCurrency(subtotal);
    if (btnTotalEl) btnTotalEl.textContent = formatCurrency(subtotal);
    if (modalTotalEl) modalTotalEl.textContent = formatCurrency(subtotal);
    
    const formTotal = document.getElementById("form-total-amount");
    if (formTotal) formTotal.value = String(Math.round(subtotal));
  }
  
  if (typeof updateCartUI === "function") updateCartUI();
  closeVoucherModal();
}

function previewVoucherServer(voucherId) {
  const fd = new FormData();
  const subtotalEl = document.getElementById("subtotal-price");
  const subtotal = subtotalEl
    ? parseVND(subtotalEl.textContent)
    : (window.cart || []).reduce((s, i) => s + i.price * i.qty, 0);

  fd.append("customer_id", window.currentOrder.customer_id);
  fd.append("voucher_id", voucherId);
  fd.append("total_amount", subtotal);

  fetch("/COFFEE_PHP/Voucher/previewVoucher", { method: "POST", body: fd })
    .then((r) => r.text())
    .then((html) => {
      const tmp = document.createElement("div");
      tmp.innerHTML = html;

      const pv = tmp.querySelector("#pv");
      if (!pv) return;

      if (pv.dataset.ok !== "1") {
        console.warn(pv.dataset.msg || "Preview failed");
        return;
      }

      const discount = Number(pv.dataset.discount || 0);
      const totalAfter = Number(pv.dataset.totalAfter || 0);

      window.currentOrder.voucherPreview = {
        discount_amount: discount,
        total_after: totalAfter,
      };

      applyTotalsFromPreview(discount, totalAfter);
    });
}

function applyTotalsFromPreview(discount, totalAfter) {
  const totalEl = document.getElementById("total-price");
  const btnTotalEl = document.getElementById("btn-total");
  const discountEl = document.getElementById("discount-price");
  const discountRow = document.getElementById("discount-row");
  const discountVoucherName = document.getElementById("discount-voucher-name");

  if (discount > 0 && discountRow) {
    discountRow.style.display = "flex";
    if (discountEl) discountEl.textContent = `-${formatCurrency(discount)}`;
    
    if (discountVoucherName && window.currentOrder?.voucher?.name) {
      discountVoucherName.textContent = window.currentOrder.voucher.name;
    }
  } else if (discountRow) {
    discountRow.style.display = "none";
  }

  if (totalEl) totalEl.textContent = formatCurrency(totalAfter);
  if (btnTotalEl) btnTotalEl.textContent = formatCurrency(totalAfter);

  const payBtn = document.getElementById("btn-checkout");
  if (payBtn) payBtn.textContent = `Thanh Toán ${formatCurrency(totalAfter)}`;

  const formTotal = document.getElementById("form-total-amount");
  if (formTotal) formTotal.value = String(Math.round(totalAfter));

  const formVoucher = document.getElementById("form-voucher-id");
  if (formVoucher && window.currentOrder?.voucher?.voucher_id) {
    formVoucher.value = window.currentOrder.voucher.voucher_id;
  }
  
  const modalTotalEl = document.getElementById("modal-total");
  if (modalTotalEl) modalTotalEl.textContent = formatCurrency(totalAfter);
}
