# ☕ COFFEE SHOP - PHP MVC Project

> Website quản lý quán cà phê với kiến trúc MVC chuẩn, Session-based Authentication

## 📋 TÍNH NĂNG

- ✅ Authentication System (Session-based)
- ✅ Multi-role Support (Manager, Staff, Shipper, Customer)
- ✅ Clean MVC Architecture
- ✅ Repository Pattern
- ✅ Service Layer
- ✅ RESTful API
- ✅ Secure Password Hashing (BCrypt)
- ✅ Session Timeout (30 phút)
- ✅ Remember Me (Cookie-based, chưa hoàn chỉnh)

## 🛠️ TECH STACK

- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0
- **Web Server**: Apache (XAMPP)
- **Frontend**: Vanilla JavaScript, CSS3, HTML5
- **Architecture**: MVC + Service + Repository

## 📁 CẤU TRÚC THƯ MỤC

```
COFFEE_PHP/
├── Config/
│   ├── Database.php          # Kết nối database (support .env)
│   ├── Router.php            # HTTP routing
│   └── Controller.php        # Base controller
│
├── web/
│   ├── Controllers/          # HTTP request handlers
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── UserController.php
│   │
│   ├── Services/             # Business logic
│   │   ├── AuthService.php
│   │   └── UserService.php
│   │
│   ├── Repositories/         # Data access layer
│   │   └── UserRepository.php
│   │
│   ├── Models/
│   │   └── Entity/           # Data entities
│   │       └── UserEntity.php
│   │
│   └── Views/                # Frontend files
│       ├── Auth/Login/
│       ├── AdminDashBoard/
│       ├── EmployeeDashBoard/
│       ├── ShipperDashBoard/
│       └── UserDashBoard/
│
├── public/
│   ├── index.php             # Application entry point
│   ├── .htaccess             # Apache rewrite rules
│   └── views/                # Public-accessible views
│
├── .env.example              # Environment config template
├── .gitignore               # Git ignore rules
└── README.md                # This file
```

## ⚙️ CÀI ĐẶT

### 1. Requirements

- PHP 8.0+
- MySQL 8.0+
- Apache với mod_rewrite
- XAMPP hoặc LAMP/WAMP

### 2. Database Setup

```sql
CREATE DATABASE coffee_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone_number VARCHAR(20),
    address TEXT,
    role ENUM('manager', 'staff', 'shipper', 'customer') DEFAULT 'customer',
    avatar_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3. Environment Configuration

```bash
# Copy .env.example thành .env
cp .env.example .env

# Sửa thông tin database trong .env
DB_HOST=localhost
DB_NAME=coffee_php
DB_USER=root
DB_PASS=your_password
```

### 4. Deploy

```bash
# Copy project vào htdocs
cp -r COFFEE_PHP /path/to/xampp/htdocs/

# Khởi động Apache và MySQL
# Truy cập: http://localhost/COFFEE_PHP
```

## 🔐 TÀI KHOẢN MẶC ĐỊNH

| Username | Password | Role     |
| -------- | -------- | -------- |
| admin    | 123456   | manager  |
| staff    | 123456   | staff    |
| shipper  | 123456   | shipper  |
| customer | 123456   | customer |

**⚠️ LƯU Ý**: Đổi password sau khi cài đặt!

## 🚀 API ENDPOINTS

### Authentication

```
POST   /api/login          # Đăng nhập
GET    /logout             # Đăng xuất
GET    /api/check-auth     # Kiểm tra session
```

### Users (Cần authentication)

```
GET    /users              # Lấy danh sách users
GET    /users/{id}         # Lấy thông tin user
POST   /users              # Tạo user mới
PUT    /users/{id}         # Cập nhật user
DELETE /users/{id}         # Xóa user
```

## 🔒 BẢO MẬT

### Đã implement:

- ✅ Password hashing (BCrypt)
- ✅ Prepared statements (SQL Injection protection)
- ✅ Session regeneration (Session Fixation protection)
- ✅ Session timeout
- ✅ HttpOnly cookies
- ✅ Environment variables cho credentials
- ✅ Input validation

### Cần thêm:

- ⚠️ CSRF protection
- ⚠️ Rate limiting
- ⚠️ XSS protection
- ⚠️ HTTPS trong production
- ⚠️ Account lockout policy

## 🧪 TESTING

### Test Login API

```bash
curl -X POST http://localhost/COFFEE_PHP/public/index.php?url=/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"123456","remember":false}'
```

### Expected Response

```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "redirect": "/COFFEE_PHP/public/views/AdminDashBoard/admin.html"
}
```

## 📝 CODING STANDARDS

- **Naming**: camelCase cho methods, PascalCase cho classes
- **Namespace**: `Config\`, `web\Controllers\`, `web\Services\`, `web\Repositories\`, `web\Models\Entity\`
- **Indentation**: 4 spaces
- **Charset**: UTF-8
- **Line Endings**: LF (Unix style)

## 🐛 TROUBLESHOOTING

### 404 Not Found

- Kiểm tra mod_rewrite có bật không
- Kiểm tra .htaccess trong public/
- Kiểm tra path trong Router.php

### Login không hoạt động

- Kiểm tra session_start() có được gọi không
- Kiểm tra password_hash trong database
- Kiểm tra error log: `D:\xampp\apache\logs\error.log`

### Database connection failed

- Kiểm tra MySQL đã chạy chưa
- Kiểm tra credentials trong .env hoặc Database.php
- Kiểm tra database đã tạo chưa

## 📚 TÀI LIỆU THAM KHẢO

- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [MVC Pattern](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
- [Repository Pattern](https://deviq.com/design-patterns/repository-pattern)
- [OWASP Security](https://owasp.org/www-project-top-ten/)

## 👨‍💻 DEVELOPER

- **GitHub**: [Lechicong1/COFFEE_PHP](https://github.com/Lechicong1/COFFEE_PHP)
- **Branch**: phamvantan

## 📄 LICENSE

MIT License - Free to use for learning purposes

---

**Made with ☕ and 💻**
