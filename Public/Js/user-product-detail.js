/**
 * user-product-detail.js - Xử lý UI trang chi tiết sản phẩm
 */

document.addEventListener('DOMContentLoaded', function() {
    // Chọn size
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.getElementById('selected-product-size-id').value = this.dataset.productSizeId;
            document.getElementById('product-price').textContent =
                new Intl.NumberFormat('vi-VN').format(this.dataset.price) + 'đ';
        });
    });

    // Tăng/giảm số lượng
    const qtyDisplay = document.getElementById('quantity');
    const qtyInput = document.getElementById('selected-quantity');

    document.getElementById('decrease-qty')?.addEventListener('click', function(e) {
        e.preventDefault();
        let qty = parseInt(qtyDisplay.textContent);
        if (qty > 1) {
            qtyDisplay.textContent = --qty;
            qtyInput.value = qty;
        }
    });

    document.getElementById('increase-qty')?.addEventListener('click', function(e) {
        e.preventDefault();
        let qty = parseInt(qtyDisplay.textContent);
        if (qty < 99) {
            qtyDisplay.textContent = ++qty;
            qtyInput.value = qty;
        }
    });

    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            this.classList.add('active');
            document.getElementById(`tab-${this.dataset.tab}`)?.classList.add('active');
        });
    });
});
