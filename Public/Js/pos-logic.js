// ===================================
// FILE: pos-logic.js
// DESCRIPTION: Logic for POS Interface (Adapted from staff.js)
// ===================================

// --- DATA ---
let menuItems = []; // Fetched from API

// --- STATE ---
let cart = [];
let currentOrderType = "dine-in";
let selectedPaymentMethod = null;
let currentProductForSize = null; // Store product being added

// --- INITIALIZATION ---
document.addEventListener("DOMContentLoaded", () => {
  fetchMenu(); // Fetch menu from server
  updateDate();
  setupEventListeners();
  updateCartUI(); // Render initial cart
});

// --- FUNCTIONS ---

async function fetchMenu() {
  try {
    const response = await fetch("/COFFEE_PHP/Staff/getMenu");
    if (!response.ok) throw new Error("Failed to fetch menu");
    menuItems = await response.json();
    renderMenu("coffee"); // Render default category after fetch
  } catch (error) {
    console.error("Error fetching menu:", error);
    alert("Không thể tải danh sách món ăn. Vui lòng thử lại.");
  }
}

function updateDate() {
  const dateElement = document.getElementById("current-date");
  if (dateElement) {
    const options = { weekday: "long", day: "numeric", month: "long" };
    const today = new Date();
    dateElement.textContent = today.toLocaleDateString("vi-VN", options);
  }
}

function setupEventListeners() {
  // Category filtering
  const categoryCards = document.querySelectorAll(".category-card");
  categoryCards.forEach((card) => {
    card.addEventListener("click", () => {
      // Remove active class from all
      categoryCards.forEach((c) => c.classList.remove("active"));
      // Add active class to clicked
      card.classList.add("active");
      // Render menu
      const category = card.getAttribute("data-category");
      renderMenu(category);
    });
  });

  // Search functionality
  const searchInput = document.getElementById("search-input");
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const searchTerm = e.target.value.toLowerCase();
      const filteredItems = menuItems.filter((item) =>
        item.name.toLowerCase().includes(searchTerm)
      );
      renderMenuGrid(filteredItems);
    });
  }
}

function renderMenu(category) {
  let itemsToRender = menuItems;
  if (category !== "all") {
    itemsToRender = menuItems.filter((item) => item.category === category);
  }
  renderMenuGrid(itemsToRender);
}

function renderMenuGrid(items) {
  const grid = document.getElementById("menu-grid");
  if (!grid) return;

  grid.innerHTML = "";

  items.forEach((item) => {
    const itemEl = document.createElement("div");
    itemEl.className = "menu-item";
    itemEl.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="menu-item-info">
                <h4>${item.name}</h4>
                <span class="price">${formatCurrency(item.price)}</span>
            </div>
            <button class="add-btn" onclick="openSizeModal(${item.id})">
                <i class="fas fa-plus"></i>
            </button>
        `;
    grid.appendChild(itemEl);
  });
}

function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
}

function openSizeModal(itemId) {
  // Use == to allow string/number comparison
  const item = menuItems.find((i) => i.id == itemId);
  if (!item) {
    console.error("Product not found:", itemId);
    return;
  }

  currentProductForSize = item;
  const modal = document.getElementById("size-modal");
  const title = document.getElementById("size-modal-title");
  const optionsContainer = document.getElementById("size-options");

  if (modal && title && optionsContainer) {
    title.textContent = `Chọn Size: ${item.name}`;
    optionsContainer.innerHTML = "";

    if (item.sizes && item.sizes.length > 0) {
      item.sizes.forEach((sizeObj) => {
        const btn = document.createElement("button");
        btn.className = "size-option-btn";
        // Inline styles for better visibility
        btn.style.padding = "15px";
        btn.style.border = "1px solid #ddd";
        btn.style.borderRadius = "8px";
        btn.style.cursor = "pointer";
        btn.style.backgroundColor = "#fff";
        btn.style.fontSize = "1rem";
        btn.style.display = "flex";
        btn.style.justifyContent = "space-between";
        btn.style.alignItems = "center";
        btn.style.transition = "background-color 0.2s";

        btn.onmouseover = () => (btn.style.backgroundColor = "#f0f9eb");
        btn.onmouseout = () => (btn.style.backgroundColor = "#fff");

        btn.innerHTML = `<span>Size ${sizeObj.size}</span> <b>${formatCurrency(
          sizeObj.price
        )}</b>`;

        btn.onclick = () => {
          addToCart(item, sizeObj.size, sizeObj.price);
          closeSizeModal();
        };
        optionsContainer.appendChild(btn);
      });
    } else {
      // Fallback if no sizes array
      addToCart(item, "M", item.price);
      return;
    }
    modal.style.display = "flex";
  }
}

function closeSizeModal() {
  const modal = document.getElementById("size-modal");
  if (modal) modal.style.display = "none";
  currentProductForSize = null;
}

function addToCart(item, size, price) {
  // Create a unique ID for cart item based on product ID and size
  const cartItemId = `${item.id}-${size}`;
  const existingItem = cart.find((i) => i.cartId === cartItemId);

  if (existingItem) {
    existingItem.qty++;
  } else {
    cart.push({
      cartId: cartItemId,
      id: item.id,
      name: item.name,
      image: item.image,
      price: parseInt(price), // Ensure price is number
      size: size,
      qty: 1,
      notes: "",
    });
  }
  updateCartUI();
}

function removeFromCart(cartItemId) {
  const index = cart.findIndex((i) => i.cartId === cartItemId);
  if (index > -1) {
    cart.splice(index, 1);
  }
  updateCartUI();
}

function updateQty(cartItemId, change) {
  const item = cart.find((i) => i.cartId === cartItemId);
  if (item) {
    item.qty += change;
    if (item.qty <= 0) {
      removeFromCart(cartItemId);
    } else {
      updateCartUI();
    }
  }
}

function updateCartUI() {
  const list = document.getElementById("order-list");
  if (!list) return;
  list.innerHTML = "";

  if (cart.length === 0) {
    list.innerHTML =
      '<div style="text-align: center; color: #999; margin-top: 50px;">Chưa có món nào</div>';
  } else {
    cart.forEach((item) => {
      const itemEl = document.createElement("div");
      itemEl.className = "order-item";
      itemEl.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div class="item-info">
                    <h4>${item.name} (${item.size})</h4>
                    <span class="price">${formatCurrency(item.price)}</span>
                    <input type="text" class="note-input" placeholder="Ghi chú..." value="${
                      item.notes
                    }" onchange="updateNote('${item.cartId}', this.value)">
                </div>
                <div class="qty-controls">
                    <button class="qty-btn" onclick="updateQty('${
                      item.cartId
                    }', -1)">-</button>
                    <span>${item.qty}</span>
                    <button class="qty-btn" onclick="updateQty('${
                      item.cartId
                    }', 1)">+</button>
                </div>
            `;
      list.appendChild(itemEl);
    });
  }

  // Calculate totals
  const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);

  // Voucher discount calculation (if any)
  let voucherDiscount = 0;
  if (window.currentOrder && window.currentOrder.voucher) {
    const v = window.currentOrder.voucher;
    if (v) {
      if (v.discount_type === "FIXED") {
        voucherDiscount = Number(v.discount_value) || 0;
      } else {
        voucherDiscount = subtotal * ((Number(v.discount_value) || 0) / 100.0);
      }
      if (v.max_discount_value) {
        voucherDiscount = Math.min(
          voucherDiscount,
          Number(v.max_discount_value)
        );
      }
      voucherDiscount = Math.min(voucherDiscount, subtotal);
      voucherDiscount = Math.round(voucherDiscount);
    }
  }

  // Update totals (NO TAX)
  const total = Math.max(0, subtotal - voucherDiscount);

  const subtotalEl = document.getElementById("subtotal-price");
  if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);

  // Hide or zero out tax if element exists
  const taxEl = document.getElementById("tax-price");
  if (taxEl) taxEl.textContent = formatCurrency(0);

  const totalEl = document.getElementById("total-price");
  if (totalEl) totalEl.textContent = formatCurrency(total);

  // Voucher discount row (create/update)
  const paymentSummary = document.querySelector(".payment-summary");
  if (paymentSummary) {
    let voucherRow = document.getElementById("voucher-discount-row");
    if (!voucherRow) {
      voucherRow = document.createElement("div");
      voucherRow.className = "summary-row voucher";
      voucherRow.id = "voucher-discount-row";
      voucherRow.innerHTML = `<span id="voucher-label"></span><span id="voucher-amount"></span>`;
      const totalRow = paymentSummary.querySelector(".summary-row.total");
      if (totalRow) paymentSummary.insertBefore(voucherRow, totalRow);
      else paymentSummary.appendChild(voucherRow);
    }

    const voucherLabel = document.getElementById("voucher-label");
    const voucherAmount = document.getElementById("voucher-amount");
    if (
      window.currentOrder &&
      window.currentOrder.voucher &&
      voucherDiscount > 0
    ) {
      const v = window.currentOrder.voucher;
      voucherLabel.textContent = `Giảm (${v.name || "Voucher"})`;
      voucherAmount.textContent = "-" + formatCurrency(voucherDiscount);
    } else {
      voucherLabel.textContent = "";
      voucherAmount.textContent = formatCurrency(0);
    }
  }

  const btnTotalEl = document.getElementById("btn-total");
  if (btnTotalEl) btnTotalEl.textContent = formatCurrency(total);

  const modalTotalEl = document.getElementById("modal-total");
  if (modalTotalEl) modalTotalEl.textContent = formatCurrency(total);
}

function updateNote(cartItemId, note) {
  const item = cart.find((i) => i.cartId === cartItemId);
  if (item) {
    item.notes = note;
  }
}

function changeQty(id, change) {
  // Deprecated, use updateQty with cartItemId
}

function setOrderType(type) {
  currentOrderType = type;
  document
    .querySelectorAll(".toggle-btn")
    .forEach((btn) => btn.classList.remove("active"));
  if (type === "dine-in") {
    document.getElementById("btn-dine-in").classList.add("active");
  } else {
    document.getElementById("btn-take-away").classList.add("active");
  }

  // Update Inputs
  const tableGroup = document.querySelector(
    ".customer-details .input-box:nth-child(2)"
  ); // Assuming 2nd child is table
  const orderIdGroup = document.getElementById("order-id-group");
  const orderIdInput = document.getElementById("order-id");

  // Better selector for table group if it doesn't have ID
  // In staff_order.php: <div class="input-box"><label>Bàn Số</label>...</div>
  // Let's try to find it by label content if possible, or just assume structure
  // But wait, in previous read of staff_order.php, it didn't have an ID.
  // Let's use the structure:
  // .customer-details > .input-box (Name)
  // .customer-details > .input-box (Table)
  // .customer-details > .input-box (Order ID)

  const inputBoxes = document.querySelectorAll(".customer-details .input-box");
  if (inputBoxes.length >= 3) {
    const tableBox = inputBoxes[1];
    const orderIdBox = inputBoxes[2]; // This one has id="order-id-group"

    if (type === "dine-in") {
      tableBox.style.display = "block";
      orderIdBox.style.display = "none";
    } else {
      tableBox.style.display = "none";
      orderIdBox.style.display = "block";
      if (orderIdInput && !orderIdInput.value) {
        orderIdInput.value = generateOrderId();
      }
    }
  }
}

function generateOrderId() {
  return "ORD-" + Math.floor(1000 + Math.random() * 9000);
}

// --- PAYMENT MODAL ---
function openPaymentModal() {
  if (cart.length === 0) {
    alert("Vui lòng chọn món trước khi thanh toán.");
    return;
  }
  const modal = document.getElementById("payment-modal");
  if (modal) {
    modal.style.display = "flex";
    // Update total in modal
    const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    const modalTotalEl = document.getElementById("modal-total");
    if (modalTotalEl) modalTotalEl.textContent = formatCurrency(total);
  }
}

function closePaymentModal() {
  const modal = document.getElementById("payment-modal");
  if (modal) modal.style.display = "none";
  selectedPaymentMethod = null;
  updatePaymentSelection();
}

function selectPayment(method) {
  selectedPaymentMethod = method;
  updatePaymentSelection();
}

function updatePaymentSelection() {
  const cashBtn = document.getElementById("pay-cash");
  const cardBtn = document.getElementById("pay-card");

  if (cashBtn) {
    cashBtn.style.borderColor =
      selectedPaymentMethod === "cash" ? "var(--primary-green)" : "#eee";
    cashBtn.style.backgroundColor =
      selectedPaymentMethod === "cash" ? "#f0f9eb" : "white";
  }

  if (cardBtn) {
    cardBtn.style.borderColor =
      selectedPaymentMethod === "card" ? "var(--primary-green)" : "#eee";
    cardBtn.style.backgroundColor =
      selectedPaymentMethod === "card" ? "#f0f9eb" : "white";
  }
}

async function processPayment() {
  if (!selectedPaymentMethod) {
    alert("Vui lòng chọn phương thức thanh toán.");
    return;
  }

  const totalAmount = cart.reduce(
    (sum, item) => sum + item.price * item.qty,
    0
  );
  const orderCode =
    currentOrderType === "take-away"
      ? document.getElementById("order-id").value
      : "DINEIN-" + Math.floor(Date.now() / 1000);

  // Fix: Use very short codes for order_type to avoid truncation
  // Assuming DB column might be short or ENUM
  const orderData = {
    order_code: orderCode,
    order_type: currentOrderType === "take-away" ? "TAKEAWAY" : "DINEIN", // Removed underscores
    payment_method: selectedPaymentMethod === "cash" ? "CASH" : "BANKING",
    total_amount: totalAmount,
    items: cart,
    note: "",
    customer_id:
      window.currentOrder && window.currentOrder.customer_id
        ? window.currentOrder.customer_id
        : null,
  };

  // Include voucher info if applied — FE sends only voucher_id; backend computes discount/points
  if (window.currentOrder && window.currentOrder.voucher) {
    const v = window.currentOrder.voucher;
    orderData.voucher = {
      voucher_id: v.id,
    };
  }

  try {
    const response = await fetch("/COFFEE_PHP/Staff/createOrder", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(orderData),
    });

    const result = await response.json();

    if (result.success) {
      alert(`Thanh toán thành công! Mã đơn: ${result.order_id}`);
      cart = [];
      updateCartUI();
      closePaymentModal();
    } else {
      alert("Lỗi thanh toán: " + result.message);
    }
  } catch (error) {
    console.error("Error processing payment:", error);
    alert("Lỗi kết nối server.");
  }
}

// Close modal if clicked outside
window.onclick = function (event) {
  const paymentModal = document.getElementById("payment-modal");
  const sizeModal = document.getElementById("size-modal");
  if (event.target == paymentModal) {
    closePaymentModal();
  }
  if (event.target == sizeModal) {
    closeSizeModal();
  }
};

function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
}

// Expose functions to global scope for onclick events
window.addToCart = addToCart;
window.changeQty = changeQty;
window.setOrderType = setOrderType;
window.openPaymentModal = openPaymentModal;
window.closePaymentModal = closePaymentModal;
window.selectPayment = selectPayment;
window.processPayment = processPayment;
window.closeSizeModal = closeSizeModal;
window.openSizeModal = openSizeModal;
window.updateQty = updateQty;
window.updateNote = updateNote;
