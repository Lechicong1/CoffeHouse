function parseVND(text) {
  if (!text) return 0;
  return Number(String(text).replace(/[^0-9\-]+/g, "")) || 0;
}

function formatVND(amount) {
  return new Intl.NumberFormat("vi-VN").format(amount) + "đ";
}

function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(amount);
}

if (typeof module !== "undefined" && module.exports) {
  module.exports = { parseVND, formatVND, formatCurrency };
}

