/**
 * voucher-utils.js
 * Shared utilities for voucher modules (POS + Web)
 */

/**
 * Parse tiền VND từ string "53.000 ₫" -> số nguyên 53000
 * @param {string|number} text
 * @returns {number}
 */
function parseVND(text) {
  if (!text) return 0;
  const n = String(text).replace(/[^0-9\-]+/g, "");
  return Number(n) || 0;
}

/**
 * Format số thành chuỗi tiền VND "53.000đ"
 * @param {number} amount
 * @returns {string}
 */
function formatVND(amount) {
  return new Intl.NumberFormat("vi-VN").format(amount) + "đ";
}

/**
 * Format số thành chuỗi tiền VND với currency symbol "53.000 ₫"
 * @param {number} amount
 * @returns {string}
 */
function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
}

// Export for module systems (optional)
if (typeof module !== "undefined" && module.exports) {
  module.exports = { parseVND, formatVND, formatCurrency };
}
