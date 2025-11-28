/* ===================================
   FILE: category-products.js
   MÔ TẢ: JavaScript cho trang danh sách sản phẩm theo danh mục
   =================================== */

// ==================== PRODUCT DATA ====================
const allProductsData = {
    coffee: [
        {
            id: 1,
            name: 'CÀ PHÊ CỐT DỪA',
            description: 'Cà phê rang xay pha phin kết hợp cốt dừa béo ngậy, thơm ngon độc đáo',
            price: 45000,
            image: 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=500',
            badge: 'Mới'
        },
        {
            id: 2,
            name: 'BẠC XỈU ĐẶC BIỆT',
            description: 'Cà phê pha phin truyền thống với sữa tươi, vị ngọt dịu nhẹ, thơm ngon',
            price: 38000,
            image: 'https://images.unsplash.com/photo-1517487881594-2787fef5ebf7?w=500',
            badge: ''
        },
        {
            id: 3,
            name: 'ESPRESSO ĐẬM ĐÀ',
            description: 'Cà phê espresso ý nguyên chất, hương vị đậm đà cho người sành điệu',
            price: 35000,
            image: 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500',
            badge: ''
        },
        {
            id: 10,
            name: 'CAPPUCCINO',
            description: 'Cà phê espresso kết hợp sữa tươi và lớp foam mềm mịn',
            price: 42000,
            image: 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=500',
            badge: ''
        },
        {
            id: 11,
            name: 'LATTE',
            description: 'Sự hòa quyện hoàn hảo giữa espresso và sữa tươi béo ngậy',
            price: 45000,
            image: 'https://images.unsplash.com/photo-1561882468-9110e03e0f78?w=500',
            badge: ''
        },
        {
            id: 12,
            name: 'AMERICANO',
            description: 'Cà phê đen nguyên chất, đậm đà và mạnh mẽ',
            price: 32000,
            image: 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=500',
            badge: ''
        },
        {
            id: 19,
            name: 'MOCHA',
            description: 'Cà phê espresso pha cùng chocolate và sữa tươi béo ngậy',
            price: 48000,
            image: 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=500',
            badge: 'Hot'
        },
        {
            id: 20,
            name: 'CARAMEL MACCHIATO',
            description: 'Espresso kết hợp sữa tươi và caramel ngọt ngào hấp dẫn',
            price: 50000,
            image: 'https://images.unsplash.com/photo-1530373239216-42518e6b4063?w=500',
            badge: ''
        },
        {
            id: 21,
            name: 'CÀ PHÊ SỮA ĐÁ',
            description: 'Cà phê phin truyền thống Việt Nam với sữa đặc thơm ngon',
            price: 30000,
            image: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500',
            badge: ''
        }
    ],
    tea: [
        {
            id: 4,
            name: 'TRÀ ĐÀO CAM SẢ',
            description: 'Hương vị tươi mát từ đào, cam, sả tự nhiên - thức uống giải nhiệt hoàn hảo',
            price: 42000,
            image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500',
            badge: 'Hot'
        },
        {
            id: 5,
            name: 'MATCHA LATTE',
            description: 'Bột matcha Nhật Bản nguyên chất kết hợp sữa tươi, vị đắng nhẹ thanh mát',
            price: 48000,
            image: 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500',
            badge: ''
        },
        {
            id: 6,
            name: 'TRÀ SỮA TRÂN CHÂU',
            description: 'Trà sữa ngọt ngào với trân châu dai ngon, thức uống được yêu thích nhất',
            price: 40000,
            image: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500',
            badge: ''
        },
        {
            id: 13,
            name: 'TRÀ SỮA OOLONG',
            description: 'Hương trà oolong đặc trưng hòa quyện cùng sữa tươi mềm mịn',
            price: 38000,
            image: 'https://images.unsplash.com/photo-1578899952107-9d9d3a8d3c32?w=500',
            badge: ''
        },
        {
            id: 14,
            name: 'TRÀ XANH ĐẬU ĐỎ',
            description: 'Trà xanh thanh mát kết hợp đậu đỏ thơm ngon bổ dưỡng',
            price: 35000,
            image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500',
            badge: ''
        },
        {
            id: 15,
            name: 'FREEZE PASSION FRUIT',
            description: 'Đá xay chanh leo chua ngọt, mát lạnh sảng khoái',
            price: 45000,
            image: 'https://images.unsplash.com/photo-1546173159-315724a31696?w=500',
            badge: 'Mới'
        },
        {
            id: 22,
            name: 'TRÀ SỮA THÁI XANH',
            description: 'Trà xanh Thái Lan đậm đà kết hợp sữa đặc ngọt ngào',
            price: 42000,
            image: 'https://images.unsplash.com/photo-1525385444622-0195f89bf22b?w=500',
            badge: ''
        },
        {
            id: 23,
            name: 'FREEZE MATCHA ĐẬU ĐỎ',
            description: 'Đá xay matcha kết hợp đậu đỏ mềm mịn độc đáo',
            price: 50000,
            image: 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?w=500',
            badge: 'Mới'
        },
        {
            id: 24,
            name: 'TRÀ ATISO MẬT ONG',
            description: 'Trà atiso Đà Lạt kết hợp mật ong, bổ dưỡng và ngọt dịu',
            price: 35000,
            image: 'https://images.unsplash.com/photo-1597318133945-44946baad52b?w=500',
            badge: ''
        }
    ],
    snack: [
        {
            id: 7,
            name: 'HẠT HƯỚNG DƯƠNG',
            description: 'Hạt hướng dương rang muối vừa phải, giòn tan, bùi béo thơm ngon',
            price: 25000,
            image: 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?w=500',
            badge: 'Mới'
        },
        {
            id: 8,
            name: 'KHÔ GÀ LÁ CHANH',
            description: 'Thịt gà xé sợi khô thơm lừng mùi lá chanh, cay nhẹ hấp dẫn',
            price: 35000,
            image: 'https://images.unsplash.com/photo-1562967914-608f82629710?w=500',
            badge: ''
        },
        {
            id: 9,
            name: 'KHOAI TÂY CHIÊN',
            description: 'Khoai tây chiên giòn rụm, ăn kèm tương cà chua, phô mai',
            price: 30000,
            image: 'https://images.unsplash.com/photo-1601924582970-9238bcb495d9?w=500',
            badge: ''
        },
        {
            id: 16,
            name: 'BÁNH MOUSSE MATCHA',
            description: 'Bánh mousse matcha mềm mịn, vị đắng ngọt hài hòa',
            price: 38000,
            image: 'https://images.unsplash.com/photo-1586040140378-b5d707d5b2dc?w=500',
            badge: ''
        },
        {
            id: 17,
            name: 'BÁNH TIRAMISU',
            description: 'Bánh tiramisu Italia truyền thống với hương vị cà phê đặc trưng',
            price: 42000,
            image: 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=500',
            badge: ''
        },
        {
            id: 18,
            name: 'CROISSANT BƠ',
            description: 'Bánh croissant Pháp giòn xốp, thơm mùi bơ tươi',
            price: 28000,
            image: 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500',
            badge: ''
        },
        {
            id: 25,
            name: 'BÁNH MOUSSE CHANH DÂY',
            description: 'Bánh mousse chanh dây chua ngọt hài hòa, mát lạnh',
            price: 40000,
            image: 'https://images.unsplash.com/photo-1508737027454-e6454ef45afd?w=500',
            badge: 'Hot'
        },
        {
            id: 26,
            name: 'MOCHI TRÀ XANH',
            description: 'Bánh mochi Nhật Bản nhân trà xanh mềm mịn thơm ngon',
            price: 32000,
            image: 'https://images.unsplash.com/photo-1582716401301-b2407dc7563d?w=500',
            badge: ''
        },
        {
            id: 27,
            name: 'BÁNH FLAN CARAMEN',
            description: 'Bánh flan truyền thống với lớp caramen ngọt đắng hấp dẫn',
            price: 25000,
            image: 'https://images.unsplash.com/photo-1624353365286-3f8d62daad51?w=500',
            badge: ''
        }
    ]
};

// Category information
const categoryInfo = {
    coffee: {
        title: 'CÀ PHÊ',
        description: 'Sự kết hợp hoàn hảo giữa hạt cà phê Robusta & Arabica thượng hạng',
        breadcrumb: 'Cà phê'
    },
    tea: {
        title: 'FREEZE',
        description: 'Sảng khoái với thức uống đá xay phong cách Việt',
        breadcrumb: 'Trà sữa'
    },
    snack: {
        title: 'ĐỒ ĂN VẶT',
        description: 'Những món ăn vặt thơm ngon, giòn tan, bổ dưỡng',
        breadcrumb: 'Đồ ăn vặt'
    }
};

let currentProducts = [];
let currentCategory = 'coffee';

// ==================== INIT PAGE ====================
window.addEventListener('DOMContentLoaded', () => {
    // Get category from URL
    const urlParams = new URLSearchParams(window.location.search);
    currentCategory = urlParams.get('category') || 'coffee';
    
    // Update page content
    updatePageContent();
    
    // Load products
    loadProducts();
    
    // Update cart count
    updateCartCountFromStorage();
});

// ==================== UPDATE PAGE CONTENT ====================
function updatePageContent() {
    const info = categoryInfo[currentCategory];
    
    // Update hero
    document.getElementById('category-hero').className = `category-hero ${currentCategory}`;
    document.getElementById('category-title').textContent = info.title;
    document.getElementById('category-description').textContent = info.description;
    
    // Update breadcrumb
    document.getElementById('breadcrumb-category').textContent = info.breadcrumb;
    
    // Update title
    document.getElementById('products-title').textContent = info.title;
}

// ==================== LOAD PRODUCTS ====================
function loadProducts() {
    currentProducts = [...allProductsData[currentCategory]];
    renderProducts();
}

// ==================== RENDER PRODUCTS ====================
function renderProducts() {
    const productsGrid = document.getElementById('products-grid');
    productsGrid.innerHTML = '';
    
    // Update count
    document.getElementById('products-count').textContent = currentProducts.length;
    
    // Render products
    currentProducts.forEach((product, index) => {
        const productCard = createProductCard(product, index);
        productsGrid.innerHTML += productCard;
    });
}

// ==================== CREATE PRODUCT CARD ====================
function createProductCard(product, index) {
    const badgeHTML = product.badge ? `<span class="menu-badge">${product.badge}</span>` : '';
    const price = formatPrice(product.price);
    
    return `
        <div class="menu-card" data-category="${currentCategory}" data-id="${product.id}" 
             onclick="window.location.href='product-detail.html?id=${product.id}'"
             style="animation: fadeInUp 0.6s ease; animation-delay: ${index * 0.05}s; opacity: 0; animation-fill-mode: forwards;">
            <div class="menu-card-image">
                <img src="${product.image}" alt="${product.name}">
                ${badgeHTML}
            </div>
            <div class="menu-card-content">
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <div class="menu-card-footer">
                    <span class="price">${price}</span>
                    <button class="btn-add" onclick="event.stopPropagation(); addQuickToCart(${product.id})">
                        Đặt món
                    </button>
                </div>
            </div>
        </div>
    `;
}

// ==================== SORT PRODUCTS ====================
function sortProducts() {
    const sortValue = document.getElementById('sort-select').value;
    
    switch(sortValue) {
        case 'price-asc':
            currentProducts.sort((a, b) => a.price - b.price);
            break;
        case 'price-desc':
            currentProducts.sort((a, b) => b.price - a.price);
            break;
        case 'name-asc':
            currentProducts.sort((a, b) => a.name.localeCompare(b.name));
            break;
        case 'name-desc':
            currentProducts.sort((a, b) => b.name.localeCompare(a.name));
            break;
        default:
            currentProducts = [...allProductsData[currentCategory]];
    }
    
    renderProducts();
}

// ==================== FORMAT PRICE ====================
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price).replace('₫', 'đ');
}

// ==================== UPDATE CART COUNT ====================
function updateCartCountFromStorage() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = totalItems;
    }
}

console.log('📦 Category Products Page loaded!');
console.log('🏷️ Category:', currentCategory);
console.log('📊 Products:', currentProducts.length);
