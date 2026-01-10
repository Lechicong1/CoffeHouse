// ========== INVENTORY IMPORTS - MINIMAL JS ==========
// Chỉ giữ lại confirm delete và validation cơ bản

// Form validation đơn giản
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="store"], form[action*="update"]');

    if (form) {
        form.addEventListener('submit', function(e) {
            const ingredientId = form.querySelector('select[name="ingredient_id"]').value;
            const importQuantity = form.querySelector('input[name="import_quantity"]').value;
            const totalCost = form.querySelector('input[name="total_cost"]').value;
            const importDate = form.querySelector('input[name="import_date"]').value;

            // Validation cơ bản
            if (!ingredientId) {
                alert('Vui lòng chọn nguyên liệu!');
                e.preventDefault();
                return false;
            }

            if (!importQuantity || importQuantity <= 0) {
                alert('Số lượng nhập phải lớn hơn 0!');
                e.preventDefault();
                return false;
            }

            if (!totalCost || totalCost < 0) {
                alert('Tổng tiền không hợp lệ!');
                e.preventDefault();
                return false;
            }

            if (!importDate) {
                alert('Vui lòng chọn ngày nhập!');
                e.preventDefault();
                return false;
            }

            // Thành công - cho phép submit
            return true;
        });
    }
});

// Hàm format tiền tệ khi nhập
function formatCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    input.value = value;
}
