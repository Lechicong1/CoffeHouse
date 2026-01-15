
document.addEventListener('DOMContentLoaded', function() {
    // Chọn size & cập nhật giá
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('selected-product-size-id').value = this.dataset.productSizeId;
            document.getElementById('product-price').textContent =
                new Intl.NumberFormat('vi-VN').format(this.dataset.price) + 'đ';
            document.getElementById('selected-price').value = this.dataset.price;
        });
    });

    // Tăng/giảm số lượng
    const qtyDisplay = document.getElementById('quantity');
    const qtyInput = document.getElementById('selected-quantity');
    document.getElementById('decrease-qty')?.addEventListener('click', function(e) {
        e.preventDefault();
        let qty = +qtyDisplay.textContent;
        if (qty > 1) qtyDisplay.textContent = qtyInput.value = --qty;
    });
    document.getElementById('increase-qty')?.addEventListener('click', function(e) {
        e.preventDefault();
        let qty = +qtyDisplay.textContent;
        if (qty < 99) qtyDisplay.textContent = qtyInput.value = ++qty;
    });

    // Chuyển tab
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(`tab-${this.dataset.tab}`)?.classList.add('active');
        });
    });
});
