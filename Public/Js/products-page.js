const modal = document.getElementById('productModal');
const form = document.getElementById('productForm');
const modalTitle = document.getElementById('modalTitle');
const btnSave = document.getElementById('btnSave');

function openProductModal(mode, product = null) {
    // Sử dụng class 'active' để kích hoạt modal (display: flex) thay vì style.display = 'block'
    modal.classList.add('active');
    
    // Reset delete flag
    document.getElementById('deleteImageFlag').value = '0';
    document.getElementById('fileName').textContent = 'Chưa chọn tệp';

    if (mode === 'add') {
        modalTitle.textContent = 'Thêm sản phẩm mới';
        form.action = '?url=ProductController/store';
        form.reset();
        document.getElementById('productId').value = '';
        document.getElementById('currentImage').style.display = 'none';
        document.getElementById('productActive').checked = true;
        
        // Reset prices
        document.getElementById('priceM').value = '';
        document.getElementById('priceL').value = '';
        document.getElementById('priceXL').value = '';
    } else {
        modalTitle.textContent = 'Cập nhật sản phẩm';
        form.action = '?url=ProductController/update';
        
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productCategory').value = product.category_id;
        document.getElementById('productDescription').value = product.description;
        document.getElementById('productActive').checked = product.is_active == 1;

        // Reset prices first
        document.getElementById('priceM').value = '';
        document.getElementById('priceL').value = '';
        document.getElementById('priceXL').value = '';

        // Populate Prices based on Sizes
        if (product.sizes && product.sizes.length > 0) {
            product.sizes.forEach(size => {
                if (size.size_name === 'M') document.getElementById('priceM').value = size.price;
                if (size.size_name === 'L') document.getElementById('priceL').value = size.price;
                if (size.size_name === 'XL') document.getElementById('priceXL').value = size.price;
            });
        }

        if (product.image_url) {
            const imgContainer = document.getElementById('currentImage');
            imgContainer.style.display = 'inline-block'; // Changed to inline-block for relative positioning
            imgContainer.querySelector('img').src = product.image_url;
        } else {
            document.getElementById('currentImage').style.display = 'none';
        }
    }
}

function closeProductModal() {
    modal.classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == modal) {
        closeProductModal();
    }
    if (event.target == document.getElementById('productDetailModal')) {
        closeProductDetailModal();
    }
}

function removeImage() {
    document.getElementById('currentImage').style.display = 'none';
    document.getElementById('productImage').value = ''; // Clear file input
    document.getElementById('fileName').textContent = 'Chưa chọn tệp';
    document.getElementById('deleteImageFlag').value = '1'; // Set flag to delete image
}

// --- Logic cho Modal Chi Tiết Sản Phẩm ---
let currentProductSizes = [];

function openProductDetailModal(product) {
    const detailModal = document.getElementById('productDetailModal');
    detailModal.classList.add('active'); // Use class active for centering
    
    // Populate Data
    document.getElementById('detailName').textContent = product.name;
    document.getElementById('detailDescription').textContent = product.description || 'Chưa có mô tả';
    
    const img = document.getElementById('detailImage');
    if (product.image_url) {
        img.src = product.image_url;
        img.style.display = 'inline-block';
    } else {
        img.style.display = 'none';
    }

    // Populate Sizes
    const sizeSelect = document.getElementById('detailSizeSelect');
    sizeSelect.innerHTML = ''; // Clear old options
    currentProductSizes = product.sizes || [];

    if (currentProductSizes.length > 0) {
        currentProductSizes.forEach((size, index) => {
            const option = document.createElement('option');
            option.value = index; // Use index to easily get price later
            option.textContent = size.size_name;
            sizeSelect.appendChild(option);
        });
        // Trigger update for first item
        updateDetailPrice();
    } else {
        const option = document.createElement('option');
        option.textContent = 'Chưa có size';
        sizeSelect.appendChild(option);
        document.getElementById('detailPriceDisplay').textContent = '---';
    }
}

function closeProductDetailModal() {
    document.getElementById('productDetailModal').classList.remove('active');
}

function updateDetailPrice() {
    const sizeSelect = document.getElementById('detailSizeSelect');
    const selectedIndex = sizeSelect.value;
    
    if (currentProductSizes[selectedIndex]) {
        const price = currentProductSizes[selectedIndex].price;
        // Format currency VND
        const formattedPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
        document.getElementById('detailPriceDisplay').textContent = formattedPrice;
    }
}

function previewImage(input) {
    const fileNameSpan = document.getElementById('fileName');
    const currentImageDiv = document.getElementById('currentImage');
    const img = currentImageDiv.querySelector('img');

    if (input.files && input.files[0]) {
        fileNameSpan.textContent = input.files[0].name;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            currentImageDiv.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        fileNameSpan.textContent = 'Chưa chọn tệp';
    }
}
