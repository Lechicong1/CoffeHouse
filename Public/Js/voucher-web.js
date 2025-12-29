/*
 * voucher-web.js
 * Mô tả: Script xử lý modal voucher cho trang Checkout chính
 * - Tải danh sách voucher từ server, preview bằng endpoint `/Voucher/previewVoucher`
 * - Khi preview, server trả HTML chứa <div id="pv" data-ok="1" data-discount="..." data-totalAfter="...">
 * - File này dùng hàm parse và cập nhật giao diện checkout
 */
(function () {
  function formatVND(n) {
    return new Intl.NumberFormat("vi-VN").format(n) + "đ";
  }

  function getSubtotalFromPage(fallback) {
    const subtotalEl =
      document.querySelector(
        ".summary-total .total-row.subtotal span:last-child"
      ) || document.getElementById("grandTotal");
    if (subtotalEl && subtotalEl.textContent)
      return (
        Number(String(subtotalEl.textContent).replace(/[^0-9\-]+/g, "")) ||
        fallback
      );
    return fallback || 0;
  }

  // openVoucherList: mở modal voucher (dùng cho trang checkout) và load voucher_list từ server
  function openVoucherList() {
    const fd = new FormData();
    if (typeof CUSTOMER_ID !== "undefined" && CUSTOMER_ID !== null)
      fd.append("customer_id", CUSTOMER_ID);
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
        body.innerHTML =
          html || '<div style="color:#333">Không có voucher phù hợp</div>';
        const modal = document.getElementById("voucherModalCheckout");
        if (modal) modal.style.display = "flex";

        const cards = body.querySelectorAll(".voucher-card");
        cards.forEach((c) => {
          c.style.cursor = "pointer";
          c.addEventListener("click", () => {
            const vid = c.getAttribute("data-id") || c.dataset.id || null;
            const vname = c.getAttribute("data-name") || c.dataset.name || null;
            if (!vid) return;
            const input = document.getElementById("customerVoucher");
            const hid = document.getElementById("appliedVoucherId");
            // show friendly code/name in visible input; keep hidden id for backend
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
        if (body)
          body.innerHTML = '<div style="color:#a33">Lỗi khi tải voucher</div>';
        const modal = document.getElementById("voucherModalCheckout");
        if (modal) modal.style.display = "flex";
      });
  }

  // previewAndApplyVoucher: gọi server preview để lấy thông tin discount và tổng sau giảm
  // - Server trả HTML fragment, ta parse `#pv` để lấy dữ liệu (data-* attributes)
  function previewAndApplyVoucher(voucherId, totalAmount) {
    const fd = new FormData();
    if (typeof CUSTOMER_ID !== "undefined" && CUSTOMER_ID !== null)
      fd.append("customer_id", CUSTOMER_ID);
    fd.append("voucher_id", voucherId);
    fd.append("total_amount", totalAmount);

    fetch("/COFFEE_PHP/Voucher/previewVoucher", { method: "POST", body: fd })
      .then((r) => r.text())
      .then((html) => {
        console.debug("previewVoucher response HTML:", html);
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
        console.debug("pv dataset:", pv.dataset);
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
          msgEl.textContent = `Áp thành công — Giảm ${formatVND(
            discount
          )}. Tổng sau giảm: ${formatVND(totalAfter)}`;
        }

        const totalInput = document.querySelector(
          'input[name="txtTotalAmount"]'
        );
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

  // init: gán sự kiện cho nút mở modal và nút đóng
  function init() {
    // modal container (if not injected by server side)
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
    if (closeBtn)
      closeBtn.addEventListener("click", () => {
        const m = document.getElementById("voucherModalCheckout");
        if (m) m.style.display = "none";
      });
  }

  if (document.readyState === "loading")
    document.addEventListener("DOMContentLoaded", init);
  else init();

  // expose for manual use if needed
  window.openVoucherList = openVoucherList;
  window.previewAndApplyVoucher = previewAndApplyVoucher;
})();
