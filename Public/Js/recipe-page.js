/**
 * Recipe Page JavaScript
 * Sử dụng form submit truyền thống, không dùng JSON
 */

// Chọn sản phẩm
function handleProductChange(productId) {
    if (!productId) return;
    window.location.href = '/COFFEE_PHP/RecipeController/GetData?product_id=' + productId;
}

// Thêm nguyên liệu mới vào công thức (bên trái)
function submitSave() {
    var productId = document.getElementById('productSelect').value;
    if (!productId) {
        alert('⚠️ Vui lòng chọn sản phẩm trước!');
        return;
    }
    
    // Kiểm tra có chọn nguyên liệu không
    var checked = document.querySelectorAll('.ingredient-checkbox:checked');
    if (checked.length === 0) {
        alert('⚠️ Vui lòng chọn ít nhất một nguyên liệu!');
        return;
    }
    
    // Kiểm tra định lượng
    var hasError = false;
    checked.forEach(function(cb) {
        var row = cb.closest('tr');
        var input = row.querySelector('.quantity-input');
        var qty = parseFloat(input.value) || 0;
        if (qty <= 0) {
            input.classList.add('error');
            hasError = true;
        } else {
            input.classList.remove('error');
        }
    });
    
    if (hasError) {
        alert('⚠️ Vui lòng nhập định lượng cho tất cả nguyên liệu đã chọn!');
        return;
    }
    
    if (!confirm('Bạn có chắc chắn muốn THÊM các nguyên liệu này vào công thức?')) return;
    
    document.getElementById('saveForm').submit();
}

// Cập nhật định lượng (bên phải)
function submitUpdateQuantity() {
    var productId = document.getElementById('productSelect').value;
    if (!productId) {
        alert('⚠️ Vui lòng chọn sản phẩm trước!');
        return;
    }
    
    // Kiểm tra có công thức để cập nhật không
    var inputs = document.querySelectorAll('.update-qty');
    if (inputs.length === 0) {
        alert('⚠️ Chưa có công thức để cập nhật!');
        return;
    }
    
    // Validate định lượng
    var hasError = false;
    inputs.forEach(function(input) {
        var qty = parseFloat(input.value) || 0;
        if (qty <= 0) {
            input.classList.add('error');
            hasError = true;
        } else {
            input.classList.remove('error');
        }
    });
    
    if (hasError) {
        alert('⚠️ Định lượng phải lớn hơn 0!');
        return;
    }
    
    if (!confirm('Bạn có chắc chắn muốn cập nhật định lượng?')) return;
    
    document.getElementById('updateQuantityForm').action = '/COFFEE_PHP/RecipeController/UpdateQuantity';
    document.getElementById('updateQuantityForm').submit();
}

// Xóa công thức đã chọn (bên phải)
function submitDelete() {
    var productId = document.getElementById('productSelect').value;
    if (!productId) {
        alert('⚠️ Vui lòng chọn sản phẩm trước!');
        return;
    }
    
    var checked = document.querySelectorAll('.delete-checkbox:checked');
    if (checked.length === 0) {
        alert('⚠️ Vui lòng tick checkbox để chọn nguyên liệu cần xóa!');
        return;
    }
    
    if (!confirm('Bạn có chắc chắn muốn xóa ' + checked.length + ' nguyên liệu khỏi công thức?')) return;
    
    // Submit cùng form nhưng action khác
    document.getElementById('updateQuantityForm').action = '/COFFEE_PHP/RecipeController/Delete';
    document.getElementById('updateQuantityForm').submit();
}

// Làm mới trang (reset về trạng thái ban đầu)
function refreshPage() {
    window.location.href = '/COFFEE_PHP/RecipeController/GetData';
}

// Highlight row khi check
document.addEventListener('DOMContentLoaded', function() {
    // Ingredient checkboxes
    document.querySelectorAll('.ingredient-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var row = this.closest('tr');
            if (this.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
        // Init
        if (cb.checked) cb.closest('tr').classList.add('selected');
    });
    
    // Delete checkboxes
    document.querySelectorAll('.delete-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var row = this.closest('tr');
            if (this.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
    });
});
