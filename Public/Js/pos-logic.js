/**
 * pos-logic.js
 * Mô tả: Logic chính của giao diện POS
 * - Quản lý menu, giỏ hàng, hiển thị tổng tiền, modal chọn size và xử lý thanh toán
 * - Lưu ý: server là nguồn dữ liệu chính cho giá (backend sẽ xác thực price khi tạo đơn)
 */

// --- DATA ---
let menuItems = []; // Loaded từ SERVER_MENU_DATA

// --- STATE ---
let cart = [];
let currentOrderType = "AT_COUNTER"; // DINEIN hoặc TAKEAWAY
let selectedPaymentMethod = null;
let currentProductForSize = null; // Store product being added

// --- INITIALIZATION ---
// Khi DOM sẵn sàng, khởi tạo ứng dụng POS
document.addEventListener("DOMContentLoaded", () => {
  // Load menu từ dữ liệu PHP đã truyền vào
  if (typeof SERVER_MENU_DATA !== "undefined") {
    menuItems = SERVER_MENU_DATA;
    renderMenu("coffee"); // Render default category
  } else {
    console.error("SERVER_MENU_DATA không tồn tại");
  }

  updateDate();
  setupEventListeners();
  updateCartUI(); // Render initial cart
});

// --- FUNCTIONS ---

// updateDate: cập nhật ngày hiển thị ở header POS
function updateDate() {
  const dateElement = document.getElementById("current-date");
  if (dateElement) {
    const options = { weekday: "long", day: "numeric", month: "long" };
    const today = new Date();
    dateElement.textContent = today.toLocaleDateString("vi-VN", options);
  }
}

// setupEventListeners: gán các event cho category, search, ...
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

// renderMenu: lọc menu theo category và render lưới
function renderMenu(category) {
  let itemsToRender = menuItems;
  if (category !== "all") {
    itemsToRender = menuItems.filter((item) => item.category === category);
  }
  renderMenuGrid(itemsToRender);
}

// renderMenuGrid: tạo DOM cho mỗi món và gắn nút thêm
function renderMenuGrid(items) {
  const grid = document.getElementById("menu-grid");
  if (!grid) return;

  grid.innerHTML = "";

  // ✅ Đảm bảo voucher_id được set từ state (nguồn sự thật)
  const voucherInput = document.getElementById("form-voucher-id");
  if (voucherInput) {
    const vid = window.currentOrder?.voucher?.voucher_id || "";
    voucherInput.value = vid ? String(vid) : "";
  }
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

// formatCurrency: định dạng số sang VND
function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
}

// openSizeModal: hiện modal chọn size cho món
// Khi chọn size, truyền `product_size_id` về addToCart để backend có thể xác thực
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
          // pass product_size_id (sizeObj.id) so backend can validate price
          if (!sizeObj.id) {
            console.error('Missing product_size_id for', sizeObj);
            alert('Lỗi: Không tìm thấy ID kích thước sản phẩm');
            return;
          }
          addToCart(item, Number(sizeObj.id), sizeObj.size, sizeObj.price);
          closeSizeModal();
        };
        optionsContainer.appendChild(btn);
      });
    } else {
      // Fallback if no sizes array - LẤY SIZE ĐẦU TIÊN NẾU CÓ
      console.error('Product has no sizes array:', item);
      alert('Lỗi: Sản phẩm không có thông tin kích thước');
      closeSizeModal();
      return;
    }
    modal.style.display = "flex";
  }
}

// closeSizeModal: đóng modal chọn size
function closeSizeModal() {
  const modal = document.getElementById("size-modal");
  if (modal) modal.style.display = "none";
  currentProductForSize = null;
}

// addToCart: thêm món vào cart
// - lưu `product_size_id` để backend dùng khi tạo order
function addToCart(item, productSizeId, size, price) {
  // Use product_size_id (numeric id) for backend validation
  const cartItemId = `${item.id}-${productSizeId}`;
  const existingItem = cart.find((i) => i.cartId === cartItemId);

  if (existingItem) {
    existingItem.qty++;
  } else {
    cart.push({
      cartId: cartItemId,
      id: item.id,
      product_size_id: Number(productSizeId),
      name: item.name,
      image: item.image,
      price: parseInt(price), // client display price (DB is authoritative)
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

// updateCartUI: render danh sách món, tính subtotal, áp voucher (ưu tiên giá server preview)
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
  // Voucher discount calculation (prefer server preview if present)
  let voucherDiscount = 0;
  let totalAfter = null;
  if (window.currentOrder && window.currentOrder.voucherPreview) {
    voucherDiscount = Number(
      window.currentOrder.voucherPreview.discount_amount || 0
    );
    totalAfter = Number(window.currentOrder.voucherPreview.total_after || null);
  } else if (window.currentOrder && window.currentOrder.voucher) {
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
  const total =
    totalAfter !== null
      ? Math.max(0, totalAfter)
      : Math.max(0, subtotal - voucherDiscount);

  const subtotalEl = document.getElementById("subtotal-price");
  if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);

  // Hide or zero out tax if element exists
  const taxEl = document.getElementById("tax-price");
  if (taxEl) taxEl.textContent = formatCurrency(0);

  const totalEl = document.getElementById("total-price");
  if (totalEl) totalEl.textContent = formatCurrency(total);


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

  // Update active button
  document
    .querySelectorAll(".toggle-btn")
    .forEach((btn) => btn.classList.remove("active"));

  if (type === "AT_COUNTER") {
    document.getElementById("btn-dine-in").classList.add("active");
  } else if (type === "TAKEAWAY") {
    document.getElementById("btn-take-away").classList.add("active");
  }

  // Toggle giữa Bàn số và Mã đơn
  const tableBox = document.getElementById("table-box");
  const orderIdBox = document.getElementById("order-id-box");

  if (tableBox && orderIdBox) {
    if (type === "AT_COUNTER") {
      tableBox.style.display = "block";
      orderIdBox.style.display = "none";
    } else {
      tableBox.style.display = "none";
      orderIdBox.style.display = "block";
      const orderIdInput = document.getElementById("order-id");
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
    // Update total in modal — ưu tiên dùng #form-total-amount (nguồn sự thật),
    // nếu không có thì dùng #total-price, cuối cùng mới tính tạm từ cart
    let total = 0;
    const formTotalEl = document.getElementById("form-total-amount");
    if (formTotalEl && formTotalEl.value) {
      total = Number(formTotalEl.value) || 0;
    } else {
      const totalPriceEl = document.getElementById("total-price");
      if (totalPriceEl && totalPriceEl.textContent) {
        total =
          Number(String(totalPriceEl.textContent).replace(/[^0-9\-]+/g, "")) ||
          0;
      } else {
        total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
      }
    }

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
      selectedPaymentMethod === "CASH" ? "var(--primary-green)" : "#eee";
    cashBtn.style.backgroundColor =
      selectedPaymentMethod === "CASH" ? "#f0f9eb" : "white";
  }

  if (cardBtn) {
    cardBtn.style.borderColor =
      selectedPaymentMethod === "BANKING" ? "var(--primary-green)" : "#eee";
    cardBtn.style.backgroundColor =
      selectedPaymentMethod === "BANKING" ? "#f0f9eb" : "white";
  }
}

// processPayment: chuẩn bị form ẩn và submit sang backend (`Staff/createOrder`)
// Lưu ý: frontend chỉ hiển thị tổng; backend sẽ tính toán lại từ `product_size_id` và items
function processPayment() {
  if (!selectedPaymentMethod) {
    alert("Vui lòng chọn phương thức thanh toán.");
    return;
  }

  if (cart.length === 0) {
    alert("Giỏ hàng trống!");
    return;
  }

  // Lấy tổng tiền thực tế — ưu tiên #form-total-amount (nguồn sự thật),
  // nếu không có thì lấy từ #total-price, cuối cùng mới tính từ cart
  let totalAmount = 0;
  // đảm bảo voucherId tồn tại ở scope bên ngoài để không gây ReferenceError
  let voucherId = null;
  const formTotalEl = document.getElementById("form-total-amount");
  if (formTotalEl && formTotalEl.value) {
    totalAmount = Number(formTotalEl.value) || 0;
  } else {
    const totalPriceEl = document.getElementById("total-price");
    if (totalPriceEl && totalPriceEl.textContent) {
      totalAmount =
        Number(String(totalPriceEl.textContent).replace(/[^0-9\-]+/g, "")) || 0;
    } else {
      // Fallback: tính từ cart và voucher giống logic cũ
      const subtotal = cart.reduce(
        (sum, item) => sum + item.price * item.qty,
        0
      );
      // Tính giảm giá voucher nếu cần
      let voucherDiscount = 0;
      if (window.currentOrder && window.currentOrder.voucher) {
        const v = window.currentOrder.voucher;
        voucherId = v.id || v.voucher_id || null;
        if (v.discount_type === "FIXED") {
          voucherDiscount = Number(v.discount_value) || 0;
        } else {
          voucherDiscount =
            subtotal * ((Number(v.discount_value) || 0) / 100.0);
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
      totalAmount = Math.max(0, subtotal - voucherDiscount);
    }
  }

  // Chuẩn bị cart items để gửi (chuyển sang format OrderService cần)
  const cartItemsFormatted = cart.map((item) => {
    if (!item.product_size_id) {
      console.error('Missing product_size_id in cart item:', item);
      alert('Lỗi: Sản phẩm trong giỏ hàng thiếu thông tin kích thước. Vui lòng xóa và thêm lại.');
      throw new Error('Missing product_size_id');
    }
    if (!item.id) {
      console.error('Missing product_id in cart item:', item);
      alert('Lỗi: Sản phẩm trong giỏ hàng thiếu ID. Vui lòng xóa và thêm lại.');
      throw new Error('Missing product_id');
    }
    return {
      product_id: item.id,           // Product ID - để validate/debug
      size_id: Number(item.product_size_id), // Product Size ID - LƯU VÀO DB
      qty: item.qty,
      price: item.price,
      notes: item.notes || "",
    };
  });
  
  // Debug log để kiểm tra
  console.log('Cart items formatted:', cartItemsFormatted);

  // Lấy customer ID từ window.selectedCustomer (được set bởi pos-customer.js)
  const customerId = window.selectedCustomer ? window.selectedCustomer.id : "";

  // Điền thông tin vào form ẩn
  document.getElementById("form-order-type").value = currentOrderType;
  document.getElementById("form-payment-method").value = selectedPaymentMethod;
  // Ghi lại total vào form (nếu chưa có hoặc để đảm bảo)
  const formTotalSet = document.getElementById("form-total-amount");
  if (formTotalSet) formTotalSet.value = String(Math.round(totalAmount));
  document.getElementById("form-customer-id").value = customerId;
  document.getElementById("form-cart-items").value =
    JSON.stringify(cartItemsFormatted);
  document.getElementById("form-note").value = "";
  // ✅ đảm bảo voucher_id luôn được set từ state trước khi submit
  const voucherInput = document.getElementById("form-voucher-id");
  if (voucherInput) {
    const vid =
      window.currentOrder?.voucher?.voucher_id ||
      window.currentOrder?.voucher?.id ||
      "";
    voucherInput.value = vid ? String(vid) : "";
  }

  // Submit form
  document.getElementById("order-form").submit();
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
