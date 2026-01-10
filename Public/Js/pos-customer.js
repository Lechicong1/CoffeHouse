(function () {
  function formatMsg(el, text, color) {
    if (!el) return;
    el.style.color = color || "";
    el.textContent = text || "";
  }

  function updateUiWithCustomer(c) {
    if (!c) return;
    const nameInput = document.getElementById("pos-customer-name");
    const sel = document.getElementById("pos-selected-customer");
    if (nameInput) nameInput.value = `${c.full_name || "Khách"} — ${c.phone || ""}`;
    if (sel) sel.textContent = `${c.points || 0} điểm`;
    window.currentOrder = window.currentOrder || {};
    window.currentOrder.customer = c;
    window.currentOrder.customer_id = c.id;
    window.currentOrder.customer_points = c.points;
    window.selectedCustomer = c;
    const hiddenInput = document.getElementById("form-customer-id");
    if (hiddenInput) hiddenInput.value = c.id;
  }

  function wireCustomerCardClicks() {
    const list = document.getElementById("posCustomerList");
    if (!list) return;
    const cards = list.querySelectorAll(".customer-card");
    cards.forEach((card) => {
      card.style.cursor = "pointer";
      card.addEventListener("click", () => {
        const c = {
          id: card.dataset.id || null,
          full_name: card.dataset.name || card.textContent.trim(),
          phone: card.dataset.phone || "",
          points: Number(card.dataset.points || 0)
        };
        window.__pos_selected_customer = c;
        cards.forEach((c2) => c2.classList.remove("selected"));
        card.classList.add("selected");
      });
    });
  }

  function applySelectedCustomer() {
    const c = window.__pos_selected_customer;
    const msgEl = document.getElementById("posCustomerMessage");
    if (!c) {
      formatMsg(msgEl, "Vui lòng chọn khách", "#c00");
      return;
    }
    updateUiWithCustomer(c);
    formatMsg(msgEl, `Đã chọn: ${c.full_name} — ${c.phone} — ${c.points} điểm`, "#080");
    closePosCustomerModal();
  }

  function clearSelectedCustomer() {
    delete window.__pos_selected_customer;
    if (window.currentOrder) delete window.currentOrder.customer;
    const sel = document.getElementById("pos-selected-customer");
    if (sel) sel.textContent = "Chưa có khách";
    const nameInput = document.getElementById("pos-customer-name");
    if (nameInput) nameInput.value = "";
    const hiddenInput = document.getElementById("form-customer-id");
    if (hiddenInput) hiddenInput.value = "";
  }

  async function posFindCustomer(phone) {
    const msgEl = document.getElementById("posCustomerMessage");
    if (!phone) {
      const phoneInput = document.getElementById("posPhone");
      phone = phoneInput ? phoneInput.value.trim() : "";
    }
    if (!phone) {
      formatMsg(msgEl, "Vui lòng nhập số điện thoại", "#c00");
      return;
    }

    try {
      const r = await fetch("/COFFEE_PHP/Staff/searchCustomerPos", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ phone }),
      });
      const text = (await r.text()).trim();
      const parts = text.split("|");

      if (parts[0] === "OK") {
        updateUiWithCustomer({
          id: parts[1],
          full_name: parts[2],
          phone: parts[3],
          points: Number(parts[4] || 0),
        });
        closePosCustomerModal();
        return;
      }
      if (parts[0] === "ERROR") {
        formatMsg(msgEl, parts[1], "#c00");
        return;
      }
      formatMsg(msgEl, "Phản hồi không hợp lệ từ server", "#c00");
    } catch (err) {
      console.error(err);
      formatMsg(msgEl, "Lỗi kết nối server", "#c00");
    }
  }

  async function posCreateOrUseCustomer(phone, fullname, email) {
    const msgEl = document.getElementById("posCustomerMessage");
    if (!phone) {
      const p = document.getElementById("posPhoneUpsert") || document.getElementById("posPhone");
      phone = p ? p.value.trim() : "";
    }
    if (!fullname) {
      const n = document.getElementById("posFullName") || document.getElementById("posFullnameCreate");
      fullname = n ? n.value.trim() : "Khách lẻ";
    }
    if (!email) {
      const e = document.getElementById("posEmail") || document.getElementById("posEmailCreate");
      email = e ? e.value.trim() : "";
    }
    if (!phone) {
      formatMsg(msgEl, "Số điện thoại bắt buộc", "#c00");
      return;
    }

    try {
      const r = await fetch("/COFFEE_PHP/Staff/upsertCustomerPos", {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ phone, fullname, email, pointsToAdd: 0 }),
      });
      const text = (await r.text()).trim();
      const parts = text.split("|");

      if (parts[0] === "OK") {
        updateUiWithCustomer({
          id: parts[1],
          full_name: parts[2],
          phone: parts[3],
          points: Number(parts[4] || 0),
        });
        formatMsg(msgEl, "Tạo / dùng khách thành công", "#080");
        closePosCustomerModal();
        return;
      }
      if (parts[0] === "ERROR") {
        formatMsg(msgEl, parts[1] || "Lỗi", "#c00");
        return;
      }
      formatMsg(msgEl, "Phản hồi không hợp lệ từ server", "#c00");
    } catch (err) {
      console.error("posCreateOrUseCustomer error:", err);
      formatMsg(msgEl, "Lỗi kết nối server", "#c00");
    }
  }

  function openPosCustomerModal() {
    let modal = document.getElementById("posCustomerModal");
    if (!modal) {
      modal = document.createElement("div");
      modal.id = "posCustomerModal";
      Object.assign(modal.style, {
        position: "fixed", left: 0, top: 0, width: "100%", height: "100%",
        display: "flex", alignItems: "center", justifyContent: "center", background: "rgba(0,0,0,0.4)"
      });
      modal.innerHTML = `
        <div style="background:#fff;padding:20px;border-radius:12px;min-width:560px;max-width:760px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <strong style="font-size:18px;color:#333;">Tìm / Chọn Khách</strong>
            <button id="closePosCustomerBtn" type="button" style="background:none;border:none;font-size:26px;cursor:pointer;color:#999;">×</button>
          </div>
          <div style="display:flex;gap:8px;margin-bottom:12px;align-items:center;">
            <input id="posPhone" placeholder="SĐT khách" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:8px;" />
            <button id="posFindBtnModal" onclick="posFindCustomer()" class="btn" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;background:#fff;">Tìm</button>
            <button id="posApplyBtn" class="btn btn-success" style="padding:8px 12px;border-radius:8px;border:none;background:#4caf50;color:#fff;">Chọn</button>
          </div>
          <div id="posCustomerMessage" style="min-height:18px;margin-bottom:8px;color:#c00"></div>
          <div id="posCustomerList" style="max-height:380px;overflow:auto;border-top:1px solid #f0f0f0;padding-top:8px;"></div>
          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">
            <button id="posClearBtn" class="btn" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;background:#fff;">Bỏ chọn</button>
            <button id="posCreateBtn" class="btn" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;background:#fff;">Tạo / Dùng</button>
          </div>
        </div>
      `;
      document.body.appendChild(modal);
      document.getElementById("closePosCustomerBtn").addEventListener("click", closePosCustomerModal);
      document.getElementById("posApplyBtn").addEventListener("click", applySelectedCustomer);
      document.getElementById("posClearBtn").addEventListener("click", clearSelectedCustomer);
      document.getElementById("posCreateBtn").addEventListener("click", () => {
        posCreateOrUseCustomer(document.getElementById("posPhone").value.trim());
      });
    } else {
      modal.style.display = "flex";
    }
    const msgEl = document.getElementById("posCustomerMessage");
    if (msgEl) {
      msgEl.textContent = "";
      msgEl.style.color = "";
    }
  }

  function closePosCustomerModal() {
    const m = document.getElementById("posCustomerModal");
    if (m) m.style.display = "none";
  }

  function init() {
    const openBtn = document.getElementById("open-customer-modal");
    if (openBtn) openBtn.addEventListener("click", openPosCustomerModal);
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

  window.openPosCustomerModal = openPosCustomerModal;
  window.closePosCustomerModal = closePosCustomerModal;
  window.posFindCustomer = posFindCustomer;
  window.posCreateOrUseCustomer = posCreateOrUseCustomer;
  window.applySelectedCustomer = applySelectedCustomer;
  window.clearSelectedCustomer = clearSelectedCustomer;
})();
