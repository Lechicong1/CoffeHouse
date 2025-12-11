/* ===================================
   FILE: menu-script.js
   MÔ TẢ: JavaScript cho trang Menu
   =================================== */

// ==================== PRODUCT DATA ====================
const productsData = {
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
        }
    ]
};

// ==================== SHOW PRODUCTS ====================
function showProducts(category) {
    // Ẩn categories section
    const categoriesSection = document.querySelector('.categories-section');
    categoriesSection.style.display = 'none';
    
    // Hiển thị products section
    const productsSection = document.getElementById('products-section');
    productsSection.style.display = 'block';
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Update title
    const titles = {
        coffee: 'CÀ PHÊ',
        tea: 'TRÀ SỮA',
        snack: 'ĐỒ ĂN VẶT'
    };
    document.getElementById('products-title').textContent = titles[category];
    
    // Load products
    loadProducts(category);
}

// ==================== HIDE PRODUCTS ====================
function hideProducts() {
    // Hiển thị categories section
    const categoriesSection = document.querySelector('.categories-section');
    categoriesSection.style.display = 'block';
    
    // Ẩn products section
    const productsSection = document.getElementById('products-section');
    productsSection.style.display = 'none';
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==================== LOAD PRODUCTS ====================
function loadProducts(category) {
    const productsGrid = document.getElementById('products-grid');
    const products = productsData[category] || [];
    
    // Clear existing products
    productsGrid.innerHTML = '';
    
    // Add products
    products.forEach((product, index) => {
        const productCard = createProductCard(product, category, index);
        productsGrid.innerHTML += productCard;
    });
}

// ==================== CREATE PRODUCT CARD ====================
function createProductCard(product, category, index) {
    const badgeHTML = product.badge ? `<span class="menu-badge">${product.badge}</span>` : '';
    const price = formatPrice(product.price);
    
    return `
        <div class="menu-card" data-category="${category}" data-id="${product.id}" 
             onclick="window.location.href='product-detail.html?id=${product.id}'"
             style="animation-delay: ${index * 0.1}s;">
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

// ==================== FORMAT PRICE ====================
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price).replace('₫', 'đ');
}

// ==================== SCROLL ANIMATIONS ====================
const categoryShowcases = document.querySelectorAll('.category-showcase');

const observerOptions = {
    threshold: 0.2,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(50px)';
            
            setTimeout(() => {
                entry.target.style.transition = 'all 0.8s ease';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, 100);
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

categoryShowcases.forEach(showcase => {
    observer.observe(showcase);
});

// ==================== CHECK URL PARAMETERS ====================
// Nếu có category trong URL, tự động hiển thị products
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    
    if (category && productsData[category]) {
        showProducts(category);
    }
});

// ==================== CONSOLE LOG ====================
console.log('🎨 Menu Page loaded!');
console.log('📋 Categories: Coffee, Tea, Snack');
console.log('🛒 Products ready!');
