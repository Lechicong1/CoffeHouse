(function () {
  function formatMsg(el, text, color) {
    if (!el) return;
    el.style.color = color || "";
    el.textContent = text || "";
    // Add background for better visibility
    if (color === "#080" || color.includes("green")) {
      el.style.background = "#e8f5e9";
      el.style.border = "1px solid #4caf50";
    } else if (color === "#c00" || color.includes("red")) {
      el.style.background = "#ffebee";
      el.style.border = "1px solid #f44336";
    } else {
      el.style.background = "";
      el.style.border = "";
    }
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
      const phoneInput = document.getElementById("posPhoneFind");
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
        formatMsg(msgEl, "Đã chọn khách hàng: " + parts[2], "#080");
        setTimeout(() => closePosCustomerModal(), 800);
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

  async function posCreateCustomer(phone, fullname, email) {
    const msgEl = document.getElementById("posCustomerMessage");
    if (!phone) {
      const p = document.getElementById("posPhoneCreate") || document.getElementById("posPhone");
      phone = p ? p.value.trim() : "";
    }
    if (!fullname) {
      const n = document.getElementById("posFullnameCreate") || document.getElementById("posFullName");
      fullname = n ? n.value.trim() : "Khách lẻ";
    }
    if (!email) {
      const e = document.getElementById("posEmailCreate") || document.getElementById("posEmail");
      email = e ? e.value.trim() : "";
    }
    if (!phone) {
      formatMsg(msgEl, "Số điện thoại bắt buộc", "#c00");
      return;
    }

    try {
      const r = await fetch("/COFFEE_PHP/Staff/createCustomerPos", {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ phone, fullname, email }),
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
        formatMsg(msgEl, "Tạo khách hàng thành công", "#080");
        closePosCustomerModal();
        return;
      }
      if (parts[0] === "ERROR") {
        formatMsg(msgEl, parts[1] || "Lỗi", "#c00");
        return;
      }
      formatMsg(msgEl, "Phản hồi không hợp lệ từ server", "#c00");
    } catch (err) {
      console.error("posCreateCustomer error:", err);
      formatMsg(msgEl, "Lỗi kết nối server", "#c00");
    }
  }

  function openPosCustomerModal() {
    const modal = document.getElementById("posCustomerModal");
    if (!modal) {
      console.error("posCustomerModal not found!");
      return;
    }

    // Bind events chỉ 1 lần
    if (!modal.dataset.bound) {
      // Event listeners
      document.getElementById("closePosCustomerBtn").addEventListener("click", closePosCustomerModal);
      document.getElementById("posApplyBtn").addEventListener("click", applySelectedCustomer);
      document.getElementById("posClearBtn").addEventListener("click", clearSelectedCustomer);
      document.getElementById("posFindBtnModal").addEventListener("click", () => {
        posFindCustomer();
      });
      document.getElementById("posCreateBtn").addEventListener("click", () => {
        posCreateCustomer();
      });

      // Enter key support
      document.getElementById("posPhoneFind").addEventListener("keypress", (e) => {
        if (e.key === "Enter") posFindCustomer();
      });
      document.getElementById("posPhoneCreate").addEventListener("keypress", (e) => {
        if (e.key === "Enter") posCreateCustomer();
      });

      // Tab switching
      document.getElementById("tabFindBtn").addEventListener("click", () => {
        document.getElementById("tabFindBtn").style.borderBottom = "3px solid #4caf50";
        document.getElementById("tabFindBtn").style.color = "#333";
        document.getElementById("tabFindBtn").style.fontWeight = "bold";
        document.getElementById("tabCreateBtn").style.borderBottom = "3px solid transparent";
        document.getElementById("tabCreateBtn").style.color = "#999";
        document.getElementById("tabCreateBtn").style.fontWeight = "normal";
        document.getElementById("tabFindContent").style.display = "block";
        document.getElementById("tabCreateContent").style.display = "none";
        clearMessage();
      });

      document.getElementById("tabCreateBtn").addEventListener("click", () => {
        document.getElementById("tabCreateBtn").style.borderBottom = "3px solid #4caf50";
        document.getElementById("tabCreateBtn").style.color = "#333";
        document.getElementById("tabCreateBtn").style.fontWeight = "bold";
        document.getElementById("tabFindBtn").style.borderBottom = "3px solid transparent";
        document.getElementById("tabFindBtn").style.color = "#999";
        document.getElementById("tabFindBtn").style.fontWeight = "normal";
        document.getElementById("tabCreateContent").style.display = "block";
        document.getElementById("tabFindContent").style.display = "none";
        clearMessage();
      });

      modal.dataset.bound = "true";
    }

    // Show modal
    modal.style.display = "flex";
    clearMessage();

    // Clear inputs
    document.getElementById("posPhoneFind").value = "";
    document.getElementById("posPhoneCreate").value = "";
    document.getElementById("posFullnameCreate").value = "";
    document.getElementById("posEmailCreate").value = "";
  }

  function clearMessage() {
    const msgEl = document.getElementById("posCustomerMessage");
    if (msgEl) {
      msgEl.textContent = "";
      msgEl.style.color = "";
      msgEl.style.background = "";
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
  window.posCreateCustomer = posCreateCustomer;
  window.applySelectedCustomer = applySelectedCustomer;
  window.clearSelectedCustomer = clearSelectedCustomer;
})();
