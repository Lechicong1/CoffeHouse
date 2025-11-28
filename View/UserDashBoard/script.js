/* ===================================
   FILE: script.js
   MÔ TẢ: JavaScript logic cho Coffee House Website
   CHỨC NĂNG: Hero Slider, Mobile Menu, Smooth Scroll, Animations
   =================================== */

// ==================== MOBILE MENU TOGGLE ====================
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('.nav-menu');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        
        // Animate menu toggle icon
        const spans = menuToggle.querySelectorAll('span');
        spans[0].style.transform = navMenu.classList.contains('active') 
            ? 'rotate(45deg) translateY(10px)' 
            : 'rotate(0) translateY(0)';
        spans[1].style.opacity = navMenu.classList.contains('active') ? '0' : '1';
        spans[2].style.transform = navMenu.classList.contains('active') 
            ? 'rotate(-45deg) translateY(-10px)' 
            : 'rotate(0) translateY(0)';
    });
}

// Đóng menu khi click vào link
const navLinks = document.querySelectorAll('.nav-menu a');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        const spans = menuToggle.querySelectorAll('span');
        spans[0].style.transform = 'rotate(0) translateY(0)';
        spans[1].style.opacity = '1';
        spans[2].style.transform = 'rotate(0) translateY(0)';
    });
});

// ==================== HEADER SCROLL EFFECT ====================
const header = document.querySelector('header');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        header.classList.add('header-scrolled');
    } else {
        header.classList.remove('header-scrolled');
    }
    
    lastScroll = currentScroll;
});

// ==================== HERO SLIDER ====================
class HeroSlider {
    constructor() {
        this.slides = document.querySelectorAll('.hero-slide');
        this.dots = document.querySelectorAll('.slider-dot');
        this.currentSlide = 0;
        this.slideInterval = null;
        
        this.init();
    }
    
    init() {
        if (this.slides.length === 0) return;
        
        // Hiển thị slide đầu tiên
        this.showSlide(0);
        
        // Tự động chuyển slide
        this.startAutoPlay();
        
        // Thêm sự kiện cho dots
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                this.showSlide(index);
                this.resetAutoPlay();
            });
        });
    }
    
    showSlide(index) {
        // Xóa active class
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.dots.forEach(dot => dot.classList.remove('active'));
        
        // Thêm active class
        this.slides[index].classList.add('active');
        this.dots[index].classList.add('active');
        
        this.currentSlide = index;
    }
    
    nextSlide() {
        let next = this.currentSlide + 1;
        if (next >= this.slides.length) {
            next = 0;
        }
        this.showSlide(next);
    }
    
    startAutoPlay() {
        this.slideInterval = setInterval(() => {
            this.nextSlide();
        }, 5000); // Chuyển slide mỗi 5 giây
    }
    
    resetAutoPlay() {
        clearInterval(this.slideInterval);
        this.startAutoPlay();
    }
}

// Khởi tạo slider
const heroSlider = new HeroSlider();

// ==================== CATEGORY FILTER ====================
const categoryButtons = document.querySelectorAll('.category-btn');
const menuCards = document.querySelectorAll('.menu-card');

categoryButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Xóa active class từ tất cả buttons
        categoryButtons.forEach(btn => btn.classList.remove('active'));
        
        // Thêm active class cho button được click
        button.classList.add('active');
        
        // Lấy category được chọn
        const selectedCategory = button.getAttribute('data-category');
        
        // Filter menu cards
        menuCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            
            if (selectedCategory === 'all' || cardCategory === selectedCategory) {
                card.style.display = 'block';
                // Animation fade in
                card.style.animation = 'fadeInUp 0.5s ease';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// ==================== SMOOTH SCROLL ====================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        
        if (target) {
            const offsetTop = target.offsetTop - 80; // Trừ đi chiều cao header
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ==================== ACTIVE MENU HIGHLIGHT ====================
const sections = document.querySelectorAll('section[id]');

function highlightMenu() {
    const scrollY = window.pageYOffset;
    
    sections.forEach(section => {
        const sectionHeight = section.offsetHeight;
        const sectionTop = section.offsetTop - 100;
        const sectionId = section.getAttribute('id');
        
        if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
            document.querySelector(`.nav-menu a[href="#${sectionId}"]`)?.classList.add('active');
        } else {
            document.querySelector(`.nav-menu a[href="#${sectionId}"]`)?.classList.remove('active');
        }
    });
}

window.addEventListener('scroll', highlightMenu);

// ==================== SCROLL REVEAL ANIMATION ====================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Quan sát các phần tử cần animation
document.querySelectorAll('.menu-card, .about-image, .about-content, .location-item').forEach(el => {
    observer.observe(el);
});

// ==================== MENU CARD - ADD TO CART ====================
const addToCartButtons = document.querySelectorAll('.btn-add');
const cartCount = document.querySelector('.cart-count');
let cartItems = 0;

addToCartButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.stopPropagation();
        
        // Tăng số lượng giỏ hàng
        cartItems++;
        cartCount.textContent = cartItems;
        
        // Hiệu ứng animation
        cartCount.style.transform = 'scale(1.3)';
        setTimeout(() => {
            cartCount.style.transform = 'scale(1)';
        }, 300);
        
        // Hiển thị thông báo
        showNotification('Đã thêm vào giỏ hàng!');
        
        // Animation cho button
        button.textContent = '✓ Đã thêm';
        button.style.backgroundColor = '#9FC885';
        
        setTimeout(() => {
            button.textContent = 'Đặt món';
            button.style.backgroundColor = '';
        }, 1500);
    });
});

// ==================== NOTIFICATION SYSTEM ====================
function showNotification(message) {
    // Tạo phần tử notification
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: #B6DA9F;
        color: #2C2C2C;
        padding: 15px 25px;
        border-radius: 50px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        font-weight: 600;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Xóa notification sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Thêm CSS animation cho notification
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ==================== CART ICON CLICK ====================
const cartIcon = document.querySelector('.cart-icon');
if (cartIcon) {
    cartIcon.addEventListener('click', () => {
        if (cartItems > 0) {
            showNotification(`Bạn có ${cartItems} món trong giỏ hàng`);
        } else {
            showNotification('Giỏ hàng trống');
        }
    });
}

// ==================== QUICK ADD TO CART ====================
function addQuickToCart(productId) {
    // Dữ liệu sản phẩm mẫu (sau này sẽ lấy từ API)
    const products = {
        1: { name: 'Cà Phê Cốt Dừa', category: 'coffee', price: 45000 },
        2: { name: 'Bạc Xỉu Đặc Biệt', category: 'coffee', price: 38000 },
        3: { name: 'Espresso Đậm Đà', category: 'coffee', price: 35000 },
        4: { name: 'Trà Đào Cam Sả', category: 'tea', price: 42000 },
        5: { name: 'Matcha Latte', category: 'tea', price: 48000 },
        6: { name: 'Trà Sữa Trân Châu', category: 'tea', price: 40000 },
        7: { name: 'Hạt Hướng Dương', category: 'snack', price: 25000 },
        8: { name: 'Khô Gà Lá Chanh', category: 'snack', price: 35000 },
        9: { name: 'Khoai Tây Chiên', category: 'snack', price: 30000 }
    };
    
    const product = products[productId];
    
    if (product) {
        const cartItem = {
            id: productId,
            name: product.name,
            category: product.category,
            size: 'M',
            toppings: [],
            quantity: 1,
            price: product.price,
            total: product.price
        };
        
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.push(cartItem);
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Cập nhật số lượng giỏ hàng
        updateCartCountFromStorage();
        
        showNotification(`✓ Đã thêm ${product.name} vào giỏ hàng!`);
    }
}

// ==================== UPDATE CART COUNT FROM STORAGE ====================
function updateCartCountFromStorage() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.transform = 'scale(1.3)';
        setTimeout(() => {
            cartCount.style.transform = 'scale(1)';
        }, 300);
    }
}

// Load cart count khi trang load
window.addEventListener('load', () => {
    updateCartCountFromStorage();
});

// ==================== CONSOLE LOG ====================
console.log('🎉 Coffee House Website loaded successfully!');
console.log('📱 Responsive Design: Active');
console.log('🎨 Theme: Minimalist White');
console.log('☕ Enjoy your coffee!');
