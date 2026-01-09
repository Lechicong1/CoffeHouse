/**
 * voucher-web.js
 * Modal voucher cho trang Checkout - sử dụng voucher-utils.js
 * Requires: voucher-utils.js (phải load trước)
 */
(function () {
  function getSubtotalFromPage(fallback) {
    const subtotalEl =
      document.querySelector(".summary-total .total-row.subtotal span:last-child") ||
      document.getElementById("grandTotal");
    if (subtotalEl && subtotalEl.textContent) {
      return parseVND(subtotalEl.textContent) || fallback;
    }
    return fallback || 0;
  }

  function openVoucherList() {
    const fd = new FormData();
    if (typeof CUSTOMER_ID !== "undefined" && CUSTOMER_ID !== null) {
      fd.append("customer_id", CUSTOMER_ID);
    }
    const subtotal = getSubtotalFromPage(
      typeof TOTAL_AMOUNT !== "undefined" ? TOTAL_AMOUNT : 0
    );
    fd.append("bill_total", subtotal);

    fetch("/COFFEE_PHP/Voucher/getEligibleVouchers", {
      method: "POST",
      body: fd,
    })
      .then((r) => r.text())
      .then((html) => {
        const body = document.getElementById("voucherModalBody");
        if (!body) return;
        body.innerHTML = html || '<div style="color:#333">Không có voucher phù hợp</div>';
        
        const modal = document.getElementById("voucherModalCheckout");
        if (modal) modal.style.display = "flex";

        const cards = body.querySelectorAll(".voucher-card");
        cards.forEach((c) => {
          c.style.cursor = "pointer";
          c.addEventListener("click", () => {
            const vid = c.dataset.id || c.getAttribute("data-id");
            const vname = c.dataset.name || c.getAttribute("data-name");
            if (!vid) return;
            
            const input = document.getElementById("customerVoucher");
            const hid = document.getElementById("appliedVoucherId");
            if (input) input.value = vname || vid;
            if (hid) hid.value = vid;
            if (modal) modal.style.display = "none";
            
            previewAndApplyVoucher(vid, subtotal);
          });
        });
      })
      .catch((err) => {
        console.error("load vouchers error", err);
        const body = document.getElementById("voucherModalBody");
        if (body) body.innerHTML = '<div style="color:#a33">Lỗi khi tải voucher</div>';
        const modal = document.getElementById("voucherModalCheckout");
        if (modal) modal.style.display = "flex";
      });
  }

  function previewAndApplyVoucher(voucherId, totalAmount) {
    const fd = new FormData();
    if (typeof CUSTOMER_ID !== "undefined" && CUSTOMER_ID !== null) {
      fd.append("customer_id", CUSTOMER_ID);
    }
    fd.append("voucher_id", voucherId);
    fd.append("total_amount", totalAmount);

    fetch("/COFFEE_PHP/Voucher/previewVoucher", { method: "POST", body: fd })
      .then((r) => r.text())
      .then((html) => {
        const tmp = document.createElement("div");
        tmp.innerHTML = html;
        const pv = tmp.querySelector("#pv");
        const msgEl = document.getElementById("checkoutVoucherMsg");
        
        if (!pv) {
          if (msgEl) {
            msgEl.style.color = "#a33";
            msgEl.textContent = "Không nhận được phản hồi.";
          }
          return;
        }
        
        if (pv.dataset.ok !== "1") {
          if (msgEl) {
            msgEl.style.color = "#a33";
            msgEl.textContent = pv.dataset.msg || "Voucher không hợp lệ.";
          }
          return;
        }

        const discount = Number(pv.dataset.discount || 0);
        const totalAfter = Number(pv.dataset.totalAfter || 0);
        
        if (msgEl) {
          msgEl.style.color = "#0a6";
          msgEl.textContent = `Áp thành công — Giảm ${formatVND(discount)}. Tổng sau giảm: ${formatVND(totalAfter)}`;
        }

        const totalInput = document.querySelector('input[name="txtTotalAmount"]');
        if (totalInput) totalInput.value = totalAfter;
        
        const grand = document.getElementById("grandTotal");
        if (grand) grand.textContent = formatVND(totalAfter);
        
        const amountValue = document.querySelector(".amount-value");
        if (amountValue) amountValue.textContent = formatVND(totalAfter);
      })
      .catch((err) => {
        console.error(err);
        const msgEl = document.getElementById("checkoutVoucherMsg");
        if (msgEl) {
          msgEl.style.color = "#a33";
          msgEl.textContent = "Lỗi khi kiểm tra voucher.";
        }
      });
  }

  function init() {
    if (!document.getElementById("voucherModalCheckout")) {
      const html = `
      <div id="voucherModalCheckout" class="modal" style="display:none; position: fixed; z-index:1300; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
        <div class="modal-content" style="max-width:900px; width:92%; margin:60px auto; padding:12px; background:#fff; border-radius:10px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <strong>Voucher phù hợp</strong>
            <button id="voucherModalClose" type="button">&times;</button>
          </div>
          <div id="voucherModalBody" style="max-height:520px;overflow:auto;"></div>
        </div>
      </div>`;
      document.body.insertAdjacentHTML("beforeend", html);
    }

    const openBtn = document.getElementById("openVoucherListBtn");
    if (openBtn) openBtn.addEventListener("click", openVoucherList);
    
    const closeBtn = document.getElementById("voucherModalClose");
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        const m = document.getElementById("voucherModalCheckout");
        if (m) m.style.display = "none";
      });
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Export for manual use
  window.openVoucherList = openVoucherList;
  window.previewAndApplyVoucher = previewAndApplyVoucher;
})();
